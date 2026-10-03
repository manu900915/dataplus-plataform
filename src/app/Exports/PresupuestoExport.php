<?php

namespace App\Exports;

use App\Models\Proyecto;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PresupuestoExport implements FromArray, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $proyecto;

    public function __construct(Proyecto $proyecto)
    {
        $this->proyecto = $proyecto->load('lineasPresupuesto.item', 'cliente', 'ubicacion');
    }

    public function array(): array
    {
        $data = [];
        $row = 1;

        // Header
        $data[] = ['dataplus S S soluciones de datos y seguridad', '', '', '', ''];
        $row++;
        $data[] = ['Cliente: ' . ($this->proyecto->cliente->nombre ?? 'N/A'), '', '', 'Fecha: ' . now()->format('d-m-Y'), ''];
        $row++;
        $data[] = ['Objeto de Obra: ' . $this->proyecto->nombre, '', '', '', ''];
        $row++;
        $data[] = ['', '', '', '', ''];
        $row++;

        // Resumen de totales
        $equipamientoTotal = $this->proyecto->getSubtotalByTipo('equipamiento');
        $manoObraTotal = $this->proyecto->getSubtotalByTipo('mano_obra');
        $materialesTotal = $this->proyecto->getSubtotalByTipo('material');
        $transporteTotal = $this->proyecto->getSubtotalByTipo('transporte');
        $alimentacionTotal = $this->proyecto->getSubtotalByTipo('alimentacion');
        $subtotal = $equipamientoTotal + $manoObraTotal + $materialesTotal;

        $data[] = ['', 'Importe TOTAL', '', '', ''];
        $data[] = ['Equipo CCTV', '$ ' . number_format($equipamientoTotal, 2), '', '', ''];
        $data[] = ['Mano Obra', '$ ' . number_format($manoObraTotal, 2), '', '', ''];
        $data[] = ['Materiales', '$ ' . number_format($materialesTotal, 2), '', '', ''];
        $data[] = ['SubTotal', '$ ' . number_format($subtotal, 2), '', '', ''];
        $row += 5;

        // Equipamiento
        $data[] = ['', '', '', '', ''];
        $data[] = ['EQUIPO', 'Cant', 'U/M', 'Precio (USD)', 'Total (USD)'];
        $lineasEquipamiento = $this->proyecto->lineasPresupuesto->where('tipo_linea', 'equipamiento');
        foreach ($lineasEquipamiento as $linea) {
            $data[] = [
                $linea->descripcion,
                number_format($linea->cantidad, 2),
                $linea->item->unidad_medida ?? 'u',
                number_format($linea->costo_unitario, 2),
                number_format($linea->subtotal, 2)
            ];
        }
        $data[] = ['Subtotal', '', '', '', '$ ' . number_format($equipamientoTotal, 2)];

        // Mano de Obra
        $data[] = ['', '', '', '', ''];
        $data[] = ['MANO DE OBRA CCTV', '', '', '', ''];
        $lineasManoObra = $this->proyecto->lineasPresupuesto->where('tipo_linea', 'mano_obra');
        foreach ($lineasManoObra as $linea) {
            $data[] = [$linea->descripcion, '', '', '', number_format($linea->subtotal, 2)];
        }
        $data[] = ['Subtotal', '', '', '', '$ ' . number_format($manoObraTotal, 2)];

        // Materiales
        $data[] = ['', '', '', '', ''];
        $data[] = ['MATERIALES', '', '', '', ''];
        $lineasMateriales = $this->proyecto->lineasPresupuesto->where('tipo_linea', 'material');
        foreach ($lineasMateriales as $linea) {
            $data[] = [
                $linea->descripcion,
                number_format($linea->cantidad, 2),
                $linea->item->unidad_medida ?? 'u',
                number_format($linea->costo_unitario, 2),
                number_format($linea->subtotal, 2)
            ];
        }

        // Alimentación
        $lineasAlimentacion = $this->proyecto->lineasPresupuesto->where('tipo_linea', 'alimentacion');
        foreach ($lineasAlimentacion as $linea) {
            $data[] = [
                $linea->descripcion,
                number_format($linea->cantidad, 2),
                'dias',
                number_format($linea->costo_unitario, 2),
                number_format($linea->subtotal, 2)
            ];
        }

        // Transporte
        $lineasTransporte = $this->proyecto->lineasPresupuesto->where('tipo_linea', 'transporte');
        foreach ($lineasTransporte as $linea) {
            $data[] = [$linea->descripcion, '', '', '', number_format($linea->subtotal, 2)];
        }

        $data[] = ['Subtotal', '', 'Estimado', '', '$ ' . number_format($materialesTotal + $alimentacionTotal + $transporteTotal, 2)];

        // Total General
        $data[] = ['', '', '', '', ''];
        $data[] = ['GESTION REMOTA DE CCTV', '', '', '', ''];
        $data[] = ['', '', '', '', ''];
        $data[] = ['Total', '', '', '', '$' . number_format($this->proyecto->presupuesto_total, 2)];

        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function map($row): array
    {
        return $row;
    }

    // ✅ CORREGIDO: Agregar return type ?array
    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Presupuesto ' . $this->proyecto->codigo;
    }
}