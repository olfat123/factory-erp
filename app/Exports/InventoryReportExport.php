<?php

namespace App\Exports;

use App\Models\Material;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    public function title(): string
    {
        return __('resources.reports.inventory_report');
    }

    public function collection(): Collection
    {
        return Material::with('category', 'unit')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function headings(): array
    {
        return [
            __('resources.fields.code'),
            __('resources.fields.name'),
            __('resources.fields.category'),
            __('resources.fields.unit'),
            __('resources.fields.current_stock'),
            __('resources.fields.minimum_stock'),
            __('resources.fields.average_cost'),
            __('resources.fields.total_value'),
            __('resources.reports.status'),
        ];
    }

    public function map($row): array
    {
        $totalValue = (float) $row->current_stock * (float) $row->average_cost;
        $isLow = (float) $row->current_stock <= (float) $row->minimum_stock;

        return [
            $row->code,
            $row->name,
            $row->category?->name ?? '—',
            $row->unit?->abbreviation ?? '—',
            number_format((float) $row->current_stock, 4),
            number_format((float) $row->minimum_stock, 4),
            number_format((float) $row->average_cost, 4),
            number_format($totalValue, 2),
            $isLow ? __('resources.reports.low_stock') : __('resources.reports.normal'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
