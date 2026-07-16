<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Producto;


class VentaController extends Controller
{
    // ✅ Mostrar POS
    public function index()
    {

        $caja = DB::table('caja')
        ->where('estado', 'ABIERTA')
        ->first();

        if (!$caja) {
            return redirect()
                ->route('caja.index')
                ->with('error', 'Debe abrir una caja antes de realizar ventas.');
        }

        $productos = Producto::where('activo', 1)->get();
        return view('ventas.index', compact('productos','caja' ));


    }

   // dd($request->all());

    public function historial(Request $request)
    {
        $query = DB::table('ventas');

        // Buscar por ticket
        if ($request->filled('ticket')) {
            $query->where('numero_ticket', 'like', '%' . $request->ticket . '%');
        }

        // Buscar por cliente
        if ($request->filled('cliente')) {
            $query->where('cliente_nombre', 'like', '%' . $request->cliente . '%');
        }

        // Buscar por fecha
        if ($request->filled('fecha')) {
            $query->whereDate('fecha_venta', $request->fecha);
        }

        $ventas = $query
            ->orderBy('fecha_venta', 'desc')
            ->get();

        return view('ventas.historial', compact('ventas'));
    }

    public function show($id)
    {
        $venta = DB::table('ventas')
        ->where('id_venta', $id)
        ->first();

        $detalle = DB::table('detalle_ventas')
        ->join(
            'productos',
            'detalle_ventas.id_producto',
            '=',
            'productos.id_producto'
        )
        ->where('detalle_ventas.id_venta', $id)
        ->select(
            'productos.nombre',
            'detalle_ventas.cantidad',
            'detalle_ventas.precio_unitario',
            'detalle_ventas.subtotal'
        )
        ->get();

        return view('ventas.show', compact(
            'venta',
            'detalle'
        ));
    }

    public function anular($id)
    {
        DB::beginTransaction();

        try {

            $detalle = DB::table('detalle_ventas')
                ->where('id_venta', $id)
                ->get();

            foreach ($detalle as $item) {

                DB::table('productos')
                    ->where('id_producto', $item->id_producto)
                    ->increment(
                        'stock_actual',
                        $item->cantidad
                    );

            }

            DB::table('ventas')
                ->where('id_venta', $id)
                ->update([
                    'estado' => 'ANULADA'
                ]);

            DB::commit();

            return redirect()
                ->route('ventas.historial')
                ->with(
                    'success',
                    'Venta anulada correctamente.'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with(
                'error',
                'No fue posible anular la venta.'
            );

        }
    }


    // ✅ Procesar venta
    public function procesar(Request $request)
    {
        if ($request->monto_pagado <= 0) {
            return back()->with('error', 'Debe ingresar monto pagado');
            }

            if ($request->monto_pagado < $request->total) {
                return back()->with('error', 'El monto pagado es insuficiente');
            }
        DB::beginTransaction();

        try {

            //  Generar ticket
            $ticket = 'TICKET-' . time();

            //  Calcular valores
            $subtotal = $request->total / 1.18;
            $igv = $request->total - $subtotal;

            //  Insertar venta completa
            $ventaId = DB::table('ventas')->insertGetId([
                'numero_ticket' => $ticket,
                'id_usuario' => auth()->user()->id_usuario,
                'fecha_venta' => now(),

                'subtotal' => $subtotal,
                'igv' => $igv,
                'total' => $request->total,

                'metodo_pago' => $request->metodo_pago ?? 'EFECTIVO',
                'monto_pagado' => $request->monto_pagado ?? 0,
                'cambio' => $request->cambio ?? 0,

                'cliente_nombre' => $request->cliente_nombre,
                'cliente_dni' => $request->cliente_dni,
                'observaciones' => $request->observaciones,

                'estado' => 'COMPLETADA'
            ]);


            //  Convertir JSON a array
            $productos = json_decode($request->productos, true);

            foreach ($productos as $item) {

                $producto = DB::table('productos')
                    ->where('id_producto', $item['id'])
                    ->first();

                if ($producto->stock_actual < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para {$producto->nombre}");
                }
            }

            foreach ($productos as $item) {

                //  Insertar detalle
                DB::table('detalle_ventas')->insert([
                    'id_venta' => $ventaId,
                    'id_producto' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $item['cantidad'] * $item['precio']
                ]);

                //  Descontar stock
                DB::table('productos')
                    ->where('id_producto', $item['id'])
                    ->decrement('stock_actual', $item['cantidad']);
            }

            //  Auditoría
            DB::table('auditoria')->insert([
                'id_usuario' => auth()->user()->id_usuario,
                'tabla_afectada' => 'ventas',
                'accion' => 'INSERT',
                'registro_id' => $ventaId,
                'datos_nuevos' => json_encode([
                    'total' => $request->total
                ]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();

            return redirect()->route('ventas.index')
                ->with('success', '✅ Venta realizada correctamente');

        } catch (\Exception $e) {

            DB::rollback();

            dd($e->getMessage());

        }
    }
}