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
        $productos = Producto::where('activo', 1)->get();
        return view('ventas.index', compact('productos'));
    }

   // dd($request->all());

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

            // ✅ Generar ticket
            $ticket = 'TICKET-' . time();

            // ✅ Calcular valores
            $subtotal = $request->total / 1.18;
            $igv = $request->total - $subtotal;

            // ✅ Insertar venta completa
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


            // ✅ Convertir JSON a array
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

                // ✅ Insertar detalle
                DB::table('detalle_ventas')->insert([
                    'id_venta' => $ventaId,
                    'id_producto' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $item['cantidad'] * $item['precio']
                ]);

                // ✅ Descontar stock
                DB::table('productos')
                    ->where('id_producto', $item['id'])
                    ->decrement('stock_actual', $item['cantidad']);
            }

            // ✅ Auditoría
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

            return back()->with('error', '❌ Error en la venta');
        }
    }
}
