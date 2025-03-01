<?php

namespace App\Exports;

use App\Models\archivo\InventarioDocumental;
use App\Models\archivo\InventarioDocumentalDetalle;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class InventarioDocumentalExport implements FromView
{
    use Exportable;

    private InventarioDocumental $inventario_documental;
    private $inventario_detalle;


    public function inventario(InventarioDocumental $inventarioDocumental)
    {

        $this->inventario_documental = $inventarioDocumental;
        $this->inventario_detalle = $inventarioDocumental->inventario_documental_detalles;
        return $this;
    }


    public function view(): View
    {
        return view('archivo.inventario_documental_export', [
            'inventario_documental' => $this->inventario_documental,
            'inventario_detalle' => $this->inventario_detalle,
        ]);
    }
}
