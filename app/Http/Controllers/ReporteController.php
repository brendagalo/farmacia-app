<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\VentasExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;


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
}
