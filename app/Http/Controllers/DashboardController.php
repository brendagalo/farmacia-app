<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $ventasHoy = DB::table('ventas')
            ->whereDate('fecha_venta', now()->toDateString())
            ->sum('total');

        $totalProductos = DB::table('productos')->count();

        $stockBajo = DB::table('productos')
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->count();

        $ultimasVentas = DB::table('ventas')
            ->orderBy('fecha_venta', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'ventasHoy',
            'totalProductos',
            'stockBajo',
            'ultimasVentas'
        ));
    }
}
