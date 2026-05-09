<?php

namespace App\Exports;

use App\Models\StockMovement;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockMovementsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    public function __construct(
        private readonly string $from = '',
        private readonly string $to = '',
    ) {}

    public function title(): string
    {
        return __('resources.reports.stock_movements');
    }

    public function collection(): Collection
    {
        $query = StockMovement::with('creator')->orderByDesc('created_at');
        if ($this->from && $this->to) {
            $query->whereBetween('created_at', [$this->from, $this->to . ' 23:59:59']);
        }
        return $query->limit(2000)->get();
    }

    public function headings(): array
    {
        return [
            __('resources.fields.date'),
            __('resources.fields.movement_type'),
            __('resources.fields.item_type'),
            __('resources.fields.quantity'),
            __('resources.fields.unit_cost'),
            __('resources.fields.total_value'),
            __('resources.fields.created_by'),
            __('resources.fields.notes'),
        ];
    }

    public function map($row): array
    {
        return [
            $row->created_at?->format('Y-m-d H:i'),
            $row->movement_type?->label() ?? $row->movement_type,
            $row->item_type,
            number_format((float) $row->quantity, 4),
            number_format((float) $row->unit_cost, 4),
            number_format((float) $row->quantity * (float) $row->unit_cost, 2),
            $row->creator?->name ?? '—',
            $row->notes ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
