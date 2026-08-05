<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InventarioExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DB::table('productos')
            ->leftJoin('categorias','productos.id_categoria','=','categorias.id_categoria')
            ->leftJoin('proveedores','productos.id_proveedor','=','proveedores.id_proveedor')
            ->select(
                'productos.codigo_barra',
                'productos.nombre',
                'categorias.nombre as categoria',
                'proveedores.nombre as proveedor',
                'productos.stock_actual',
                'productos.stock_minimo',
                'productos.precio_compra',
                'productos.precio_venta'
            )
            ->orderBy('productos.nombre')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Código',
            'Producto',
            'Categoría',
            'Proveedor',
            'Stock',
            'Stock Mínimo',
            'Precio Compra',
            'Precio Venta'
        ];
    }
}