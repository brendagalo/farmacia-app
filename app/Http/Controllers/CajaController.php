<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caja;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{

    public function index()
    {
            $caja = DB::table('caja')
                ->where('estado', 'ABIERTA')
                ->first();

            $ventasHoy = 0;
            $ingresos = 0;
            $egresos = 0;

            if($caja){

                $ventasHoy = DB::table('ventas')
                    ->whereDate('fecha_venta', today())
                    ->where('estado','COMPLETADA')
                    ->sum('total');

                $ingresos = DB::table('movimientos_caja')
                    ->where('id_caja',$caja->id_caja)
                    ->where('tipo_movimiento','INGRESO')
                    ->sum('monto');

                $egresos = DB::table('movimientos_caja')
                    ->where('id_caja',$caja->id_caja)
                    ->where('tipo_movimiento','EGRESO')
                    ->sum('monto');
            }

            return view('caja.index', compact(
                'caja',
                'ventasHoy',
                'ingresos',
                'egresos'
            ));
    }

    public function abrir(Request $request)
    {

        Caja::create([

            'fecha_apertura' => now()->toDateString(),

            'hora_apertura' => now()->format('H:i:s'),

            'id_usuario_apertura' => auth()->user()->id_usuario,

            'saldo_inicial' => $request->saldo_inicial,

            'saldo_final' => $request->saldo_inicial,

            'tipo_cambio' => $request->tipo_cambio,

            'total_ingresos' => 0,

            'total_egresos' => 0,

            'diferencia' => 0,

            'estado' => 'ABIERTA',

            'observaciones' => $request->observaciones

        ]);

        return redirect()
                ->route('caja.index')
                ->with('success','Caja abierta correctamente');

    }

    public function ingreso()
    {
        return view('caja.ingreso');
    }


   public function guardarIngreso(Request $request)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'motivo' => 'required|max:255',
            'descripcion' => 'nullable|max:500'
        ]);

        $caja = DB::table('caja')
            ->where('estado', 'ABIERTA')
            ->first();

        if (!$caja) {
            return redirect()
                ->route('caja.index')
                ->with('error', 'No existe una caja abierta.');
        }

        $saldoAnterior = $caja->saldo_final;
        $saldoActual = $saldoAnterior + $request->monto;

        DB::table('movimientos_caja')->insert([

            'id_caja' => $caja->id_caja,

            'id_venta' => null,

            'id_compra' => null,

            'tipo_movimiento' => 'INGRESO',

            'forma_pago' => 'EFECTIVO',

            'monto' => $request->monto,

            'descripcion' => $request->descripcion,

            'motivo' => $request->motivo,

            'saldo_anterior' => $saldoAnterior,

            'saldo_actual' => $saldoActual,

            'id_usuario' => auth()->user()->id_usuario,

            'creado_en' => now()

        ]);

        DB::table('caja')
            ->where('id_caja', $caja->id_caja)
            ->update([
                'saldo_final' => $saldoActual
            ]);

        return redirect()
            ->route('caja.index')
            ->with('success','Ingreso registrado correctamente.');
    }

    public function egreso()
    {
        return view('caja.egreso');
    }

    public function guardarEgreso(Request $request)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'motivo' => 'required'
        ]);

        $caja = Caja::where('estado', 'ABIERTA')->first();

        if (!$caja) {
            return back()->with('error', 'No existe una caja abierta.');
        }

        if ($request->monto > $caja->saldo_final) {
            return back()->with('error', 'El monto supera el saldo disponible.');
        }

        $saldoAnterior = $caja->saldo_final;
        $saldoActual = $saldoAnterior - $request->monto;

        DB::table('movimientos_caja')->insert([

        'id_caja'=>$caja->id_caja,

        'tipo_movimiento'=>'EGRESO',

        'forma_pago'=>'EFECTIVO',

        'monto'=>$request->monto,

        'descripcion'=>$request->descripcion,

        'motivo'=>$request->motivo,

        'saldo_anterior'=>$saldoAnterior,

        'saldo_actual'=>$saldoActual,

        'id_usuario'=>auth()->user()->id_usuario,

        'creado_en'=>now()

       ]);
        $caja->update([

            'saldo_final' => $saldoActual

        ]);

        return redirect()
                ->route('caja.index')
                ->with('success','Egreso registrado correctamente.');
    }

    public function movimientos()
    {
        $movimientos = DB::table('movimientos_caja')
            ->leftJoin('usuarios', 'movimientos_caja.id_usuario', '=', 'usuarios.id_usuario')
            ->select(
                'movimientos_caja.*',
                'usuarios.nombre_completo'
            )
            ->orderBy('creado_en', 'desc')
            ->get();

        return view('caja.movimientos', compact('movimientos'));
    }

    public function arqueo()
    {
        $caja = Caja::where('estado','ABIERTA')->first();

        if(!$caja){

            return redirect()
                ->route('caja.index')
                ->with('error','No existe una caja abierta.');

        }

        return view('caja.arqueo',compact('caja'));
    }

    public function guardarArqueo(Request $request)
    {
        $request->validate([
            'saldo_contado'=>'required|numeric|min:0'
        ]);

        $caja = Caja::where('estado','ABIERTA')->first();

        $diferencia = $request->saldo_contado - $caja->saldo_final;

        return view('caja.resultado_arqueo',[
            'caja'=>$caja,
            'contado'=>$request->saldo_contado,
            'diferencia'=>$diferencia
        ]);
    }

    public function cerrarCaja(Request $request)
    {
        $request->validate([
            'saldo_contado' => 'required|numeric|min:0'
        ]);

        $caja = Caja::where('estado', 'ABIERTA')->first();

        if (!$caja) {

            return redirect()
                ->route('caja.index')
                ->with('error','No existe una caja abierta.');

        }

        $saldoEsperado = $caja->saldo_final;

        $diferencia = $request->saldo_contado - $saldoEsperado;

        $ingresos = DB::table('movimientos_caja')
                ->where('id_caja',$caja->id_caja)
                ->where('tipo_movimiento','INGRESO')
                ->sum('monto');

        $egresos = DB::table('movimientos_caja')
                ->where('id_caja',$caja->id_caja)
                ->where('tipo_movimiento','EGRESO')
                ->sum('monto');

        $caja->update([

            'fecha_cierre' => now()->toDateString(),

            'hora_cierre' => now()->format('H:i:s'),

            'id_usuario_cierre' => auth()->user()->id_usuario,

            'saldo_final' => $request->saldo_contado,

            'total_ingresos' => $ingresos,

            'total_egresos' => $egresos,

            'diferencia' => $diferencia,

            'estado' => 'CERRADA'

        ]);

        return redirect()
                ->route('caja.index')
                ->with('success','Caja cerrada correctamente.');

    }

}