<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AuditoriaExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $q = DB::table('auditoria')
            ->leftJoin('usuarios','auditoria.id_usuario','=','usuarios.id_usuario')
            ->select(
                'auditoria.creado_en',
                'usuarios.nombre_completo',
                'auditoria.tabla_afectada',
                'auditoria.accion',
                'auditoria.registro_id',
                'auditoria.ip_address'
            );

        if($this->request->desde){
            $q->whereDate('auditoria.creado_en','>=',$this->request->desde);
        }

        if($this->request->hasta){
            $q->whereDate('auditoria.creado_en','<=',$this->request->hasta);
        }

        if($this->request->usuario){
            $q->where('auditoria.id_usuario',$this->request->usuario);
        }

        if($this->request->tabla){
            $q->where('auditoria.tabla_afectada','like','%'.$this->request->tabla.'%');
        }

        if($this->request->accion){
            $q->where('auditoria.accion',$this->request->accion);
        }

        return $q->get();
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Usuario',
            'Tabla',
            'Acción',
            'Registro',
            'IP'
        ];
    }
}