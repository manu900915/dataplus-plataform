<?php

namespace App\Http\Controllers;

use App\Exports\PresupuestoExport;
use App\Models\Proyecto;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class PresupuestoController extends Controller
{
    public function exportarExcel(Proyecto $proyecto)
    {
        return Excel::download(
            new PresupuestoExport($proyecto),
            "Presupuesto_{$proyecto->codigo}.xlsx"
        );
    }

    public function exportarPDF(Proyecto $proyecto)
    {
        $proyecto->load('lineasPresupuesto.item', 'cliente', 'ubicacion');
        
        $pdf = Pdf::loadView('presupuestos.pdf', compact('proyecto'))
            ->setPaper('letter')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->download("Presupuesto_{$proyecto->codigo}.pdf");
    }
}