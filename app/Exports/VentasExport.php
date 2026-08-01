<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VentasExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = DB::table('ventas')
            ->join('usuarios', 'ventas.id_usuario', '=', 'usuarios.id_usuario')
            ->select(
                'ventas.numero_ticket',
                'ventas.fecha_venta',
                'ventas.cliente_nombre',
                'usuarios.nombre_completo',
                'ventas.metodo_pago',
                'ventas.total',
                'ventas.estado'
            );

        if ($this->request->filled('desde')) {
            $query->whereDate('ventas.fecha_venta', '>=', $this->request->desde);
        }

        if ($this->request->filled('hasta')) {
            $query->whereDate('ventas.fecha_venta', '<=', $this->request->hasta);
        }

        if ($this->request->filled('usuario')) {
            $query->where('ventas.id_usuario', $this->request->usuario);
        }

        if ($this->request->filled('metodo')) {
            $query->where('ventas.metodo_pago', $this->request->metodo);
        }

        if ($this->request->filled('estado')) {
            $query->where('ventas.estado', $this->request->estado);
        }

        if ($this->request->filled('cliente')) {
            $query->where('ventas.cliente_nombre', 'like', '%' . $this->request->cliente . '%');
        }

        return $query->orderByDesc('ventas.fecha_venta')->get();
    }

    public function headings(): array
    {
        return [
            'Ticket',
            'Fecha',
            'Cliente',
            'Usuario',
            'Método de Pago',
            'Total',
            'Estado'
        ];
    }
}