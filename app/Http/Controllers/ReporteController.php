<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\VentasExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Producto;
use App\Models\Venta;
use App\Exports\InventarioExport;
use App\Exports\AuditoriaExport;




class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

   public function ventas(Request $request)
    {
        $query = DB::table('ventas')
            ->join('usuarios', 'ventas.id_usuario', '=', 'usuarios.id_usuario')
            ->select(
                'ventas.*',
                'usuarios.nombre_completo'
            );

        // Fecha desde
        if ($request->filled('desde')) {
            $query->whereDate('ventas.fecha_venta', '>=', $request->desde);
        }

        // Fecha hasta
        if ($request->filled('hasta')) {
            $query->whereDate('ventas.fecha_venta', '<=', $request->hasta);
        }

        // Usuario
        if ($request->filled('usuario')) {
            $query->where('ventas.id_usuario', $request->usuario);
        }

        // Método de pago
        if ($request->filled('metodo')) {
            $query->where('ventas.metodo_pago', $request->metodo);
        }

        // Estado
        if ($request->filled('estado')) {
            $query->where('ventas.estado', $request->estado);
        }

        // Cliente
        if ($request->filled('cliente')) {
            $query->where('ventas.cliente_nombre', 'like', '%' . $request->cliente . '%');
        }

        $ventas = $query
            ->orderByDesc('ventas.fecha_venta')
            ->get();

        $usuarios = DB::table('usuarios')
            ->orderBy('nombre_completo')
            ->get();

        return view('reportes.ventas', compact(
            'ventas',
            'usuarios'
        ));
    }

    public function exportarExcel(Request $request)
    {
        return Excel::download(
            new VentasExport($request),
            'Reporte_Ventas_' . now()->format('d-m-Y_H-i') . '.xlsx'
        );
    }

    public function exportarPdf(Request $request)
    {
        $ventas = DB::table('ventas')
            ->join(
                'usuarios',
                'ventas.id_usuario',
                '=',
                'usuarios.id_usuario'
            )
            ->select(
                'ventas.*',
                'usuarios.nombre_completo'
            )
            ->orderByDesc('fecha_venta')
            ->get();

        $totalVentas = $ventas->where('estado', 'COMPLETADA')->count();
        $totalMonto = $ventas->where('estado', 'COMPLETADA')->sum('total');

        $pdf = Pdf::loadView(
            'reportes.ventas_pdf',
            compact(
                'ventas',
                'totalVentas',
                'totalMonto'
            )
        );

        return $pdf->download('Reporte_Ventas.pdf');
    }

    public function imprimir(Request $request)
    {
        $ventas = DB::table('ventas')
            ->join(
                'usuarios',
                'ventas.id_usuario',
                '=',
                'usuarios.id_usuario'
            )
            ->select(
                'ventas.*',
                'usuarios.nombre_completo'
            )
            ->orderByDesc('fecha_venta')
            ->get();

        $ventasCompletadas = $ventas->where('estado', 'COMPLETADA');

        $totalVentas = $ventasCompletadas->count();

        $totalMonto = $ventasCompletadas->sum('total');

        return view(
            'reportes.ventas_imprimir',
            compact(
                'ventas',
                'totalVentas',
                'totalMonto'
            )
        );
    }

    public function detalleVenta($id)
    {
        $venta = DB::table('ventas')

            ->join(
                'usuarios',
                'ventas.id_usuario',
                '=',
                'usuarios.id_usuario'
            )

            ->select(
                'ventas.*',
                'usuarios.nombre_completo'
            )

            ->where('ventas.id_venta', $id)

            ->first();

        $detalle = DB::table('detalle_ventas')

            ->join(
                'productos',
                'detalle_ventas.id_producto',
                '=',
                'productos.id_producto'
            )

            ->select(
                'productos.codigo_barra',
                'productos.nombre',
                'detalle_ventas.cantidad',
                'detalle_ventas.precio_unitario',
                'detalle_ventas.subtotal'
            )

            ->where('detalle_ventas.id_venta', $id)

            ->get();

        return view(
            'reportes.detalle_venta',
            compact(
                'venta',
                'detalle'
            )
        );
    }

    public function detallePDF($id)
    {
        $venta = $this->obtenerVenta($id);
        $detalle = $this->obtenerDetalle($id);

        $pdf = Pdf::loadView(
            'reportes.detalle_pdf',
            compact('venta','detalle')
        );

        return $pdf->download(
            'Venta_'.$venta->numero_ticket.'.pdf'
        );
    }

    public function detalleImprimir($id)
    {
        $venta = $this->obtenerVenta($id);
        $detalle = $this->obtenerDetalle($id);

        return view(
            'reportes.detalle_imprimir',
            compact('venta','detalle')
        );
    }

    private function obtenerVenta($id)
    {
        return DB::table('ventas')
            ->join(
                'usuarios',
                'ventas.id_usuario',
                '=',
                'usuarios.id_usuario'
            )
            ->where('ventas.id_venta',$id)
            ->select(
                'ventas.*',
                'usuarios.nombre_completo'
            )
            ->first();
    }

    private function obtenerDetalle($id)
    {
        return DB::table('detalle_ventas')
            ->join(
                'productos',
                'detalle_ventas.id_producto',
                '=',
                'productos.id_producto'
            )
            ->where('detalle_ventas.id_venta',$id)
            ->select(
                'productos.nombre',
                'detalle_ventas.cantidad',
                'detalle_ventas.precio_unitario',
                'detalle_ventas.subtotal'
            )
            ->get();
    }

    public function inventario()
    {
        $productos = DB::table('productos')
            ->leftJoin('categorias','productos.id_categoria','=','categorias.id_categoria')
            ->leftJoin('proveedores','productos.id_proveedor','=','proveedores.id_proveedor')
            ->select(
                'productos.*',
                'categorias.nombre as categoria',
                'proveedores.nombre as proveedor'
            )
            ->orderBy('productos.nombre')
            ->get();

        $totalProductos = $productos->count();

        $valorInventario = $productos->sum(function($p){
            return $p->stock_actual * $p->precio_compra;
        });

        $stockBajo = $productos->filter(function($p){
            return $p->stock_actual <= $p->stock_minimo
                && $p->stock_actual > 0;
        })->count();

        $agotados = $productos->where('stock_actual',0)->count();

        return view('reportes.inventario', compact(
            'productos',
            'totalProductos',
            'valorInventario',
            'stockBajo',
            'agotados'
        ));
    }

    public function inventarioExcel()
    {
        return Excel::download(
            new InventarioExport,
            'Reporte_Inventario.xlsx'
        );
    }

    public function inventarioPdf()
    {
        $productos = DB::table('productos')
            ->leftJoin('categorias','productos.id_categoria','=','categorias.id_categoria')
            ->leftJoin('proveedores','productos.id_proveedor','=','proveedores.id_proveedor')
            ->select(
                'productos.*',
                'categorias.nombre as categoria',
                'proveedores.nombre as proveedor'
            )
            ->orderBy('productos.nombre')
            ->get();

        $totalProductos = $productos->count();

        $valorInventario = $productos->sum(function ($p) {
            return $p->stock_actual * $p->precio_compra;
        });

        $stockBajo = $productos->filter(function ($p) {
            return $p->stock_actual <= $p->stock_minimo &&
                $p->stock_actual > 0;
        })->count();

        $agotados = $productos->where('stock_actual',0)->count();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'reportes.inventario_pdf',
            compact(
                'productos',
                'totalProductos',
                'valorInventario',
                'stockBajo',
                'agotados'
            )
        );

        return $pdf->download('Reporte_Inventario.pdf');
    }

    public function inventarioImprimir()
    {
        $productos = DB::table('productos')
            ->leftJoin('categorias','productos.id_categoria','=','categorias.id_categoria')
            ->leftJoin('proveedores','productos.id_proveedor','=','proveedores.id_proveedor')
            ->select(
                'productos.*',
                'categorias.nombre as categoria',
                'proveedores.nombre as proveedor'
            )
            ->orderBy('productos.nombre')
            ->get();

        $totalProductos = $productos->count();

        $valorInventario = $productos->sum(function($p){
            return $p->stock_actual * $p->precio_compra;
        });

        $stockBajo = $productos->filter(function($p){
            return $p->stock_actual <= $p->stock_minimo
                && $p->stock_actual > 0;
        })->count();

        $agotados = $productos->where('stock_actual',0)->count();

        return view(
            'reportes.inventario_imprimir',
            compact(
                'productos',
                'totalProductos',
                'valorInventario',
                'stockBajo',
                'agotados'
            )
        );
    }

    public function caja(Request $request)
    {
        $movimientos = DB::table('movimientos_caja')
            ->join('usuarios','movimientos_caja.id_usuario','=','usuarios.id_usuario')
            ->select('movimientos_caja.*','usuarios.nombre_completo');

        $cajas = DB::table('caja');

        // Filtrar por fecha
        if($request->filled('fecha')){

            $movimientos->whereDate('movimientos_caja.creado_en',$request->fecha);

            $cajas->whereDate('fecha_apertura',$request->fecha);
        }

        // Filtrar por estado
        if($request->filled('estado')){

            $cajas->where('estado',$request->estado);

        }

        $movimientos = $movimientos
            ->orderByDesc('creado_en')
            ->get();

        $cajas = $cajas
            ->orderByDesc('fecha_apertura')
            ->get();

            $totalIngresos = $movimientos
            ->where('tipo_movimiento','INGRESO')
            ->sum('monto');

        $totalEgresos = $movimientos
            ->where('tipo_movimiento','EGRESO')
            ->sum('monto');

        $ventasEfectivo = $movimientos
            ->where('forma_pago','EFECTIVO')
            ->sum('monto');

        $ventasTarjeta = $movimientos
            ->where('forma_pago','TARJETA')
            ->sum('monto');

        $ventasTransferencia = $movimientos
            ->where('forma_pago','TRANSFERENCIA')
            ->sum('monto');

        $ventasDolares = $movimientos
            ->where('forma_pago','DOLARES')
            ->sum('monto');

        return view('reportes.caja', compact(
            'movimientos',
            'cajas',
            'totalIngresos',
            'totalEgresos',
            'ventasEfectivo',
            'ventasTarjeta',
            'ventasTransferencia',
            'ventasDolares'
        ));
    }  
    
    public function auditoria(Request $request)
    {
        $auditorias = DB::table('auditoria')
            ->join('usuarios', 'auditoria.id_usuario', '=', 'usuarios.id_usuario')
            ->select(
                'auditoria.*',
                'usuarios.nombre_completo'
            );

        // Filtro por fecha inicial
        if ($request->filled('desde')) {
            $auditorias->whereDate('auditoria.creado_en', '>=', $request->desde);
        }

        // Filtro por fecha final
        if ($request->filled('hasta')) {
            $auditorias->whereDate('auditoria.creado_en', '<=', $request->hasta);
        }

        // Filtro por usuario
        if ($request->filled('usuario')) {
            $auditorias->where('auditoria.id_usuario', $request->usuario);
        }

        // Filtro por tabla
        if ($request->filled('tabla')) {
            $auditorias->where('auditoria.tabla_afectada', $request->tabla);
        }

        // Filtro por acción
        if ($request->filled('accion')) {
            $auditorias->where('auditoria.accion', $request->accion);
        }

        $auditorias = $auditorias
            ->orderByDesc('auditoria.creado_en')
            ->get();

        $usuarios = DB::table('usuarios')
            ->orderBy('nombre_completo')
            ->get();

        // Tarjetas de resumen
        $totalRegistros = $auditorias->count();
    
        $insertados = $auditorias->where('accion', 'INSERT')->count();

        $actualizados = $auditorias->where('accion', 'UPDATE')->count();

        $eliminados = $auditorias->where('accion', 'DELETE')->count();

        return view('reportes.auditoria', compact(
            'auditorias',
            'usuarios',
            'totalRegistros',
            'insertados',
            'actualizados',
            'eliminados'
        ));
    }

    public function auditoriaExcel(Request $request)
    {
        return Excel::download(
            new AuditoriaExport($request),
            'Reporte_Auditoria.xlsx'
        );
    }

    public function auditoriaPdf(Request $request)
    {
        $q = DB::table('auditoria')
            ->leftJoin('usuarios','auditoria.id_usuario','=','usuarios.id_usuario')
            ->select(
                'auditoria.*',
                'usuarios.nombre_completo'
            );

        if($request->desde){
            $q->whereDate('auditoria.creado_en','>=',$request->desde);
        }

        if($request->hasta){
            $q->whereDate('auditoria.creado_en','<=',$request->hasta);
        }

        if($request->usuario){
            $q->where('auditoria.id_usuario',$request->usuario);
        }

        if($request->tabla){
            $q->where('auditoria.tabla_afectada','like','%'.$request->tabla.'%');
        }

        if($request->accion){
            $q->where('auditoria.accion',$request->accion);
        }

        $auditoria = $q->orderByDesc('auditoria.creado_en')->get();

        $pdf = Pdf::loadView('reportes.auditoria_pdf', compact('auditoria'));

        return $pdf->download('Reporte_Auditoria.pdf');
    }

    
    public function auditoriaDetalle($id)
{
    $auditoria = DB::table('auditoria')
        ->leftJoin('usuarios', 'auditoria.id_usuario', '=', 'usuarios.id_usuario')
        ->select('auditoria.*', 'usuarios.nombre_completo')
        ->where('id_auditoria', $id)
        ->first();

    return view('reportes.auditoria_detalle', compact('auditoria'));
}
}
