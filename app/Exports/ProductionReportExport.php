<?php

namespace App\Exports;

use App\Models\ProductionOrder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductionReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    public function __construct(
        private readonly string $from = '',
        private readonly string $to = '',
    ) {}

    public function title(): string
    {
        return __('resources.reports.production_report');
    }

    public function collection(): Collection
    {
        $query = ProductionOrder::with(['product', 'items.material'])->orderByDesc('created_at');
        if ($this->from && $this->to) {
            $query->whereBetween('created_at', [$this->from, $this->to . ' 23:59:59']);
        }
        return $query->get();
    }

    public function headings(): array
    {
        return [
            '#',
            __('resources.fields.product'),
            __('resources.fields.status'),
            __('resources.fields.planned_quantity'),
            __('resources.fields.actual_quantity'),
            __('resources.reports.material_cost'),
            __('resources.reports.unit_cost'),
            __('resources.fields.planned_date'),
            __('resources.reports.completed_at'),
        ];
    }

    public function map($row): array
    {
        $materialCost = $row->items->sum(
            fn ($i) => $i->consumed_quantity * ($i->material?->average_cost ?? 0)
        );
        $qty = (float) ($row->completed_quantity ?: $row->quantity);

        return [
            $row->id,
            $row->product?->name ?? '—',
            $row->status instanceof \BackedEnum ? $row->status->value : $row->status,
            number_format((float) $row->quantity, 2),
            number_format((float) ($row->completed_quantity ?? 0), 2),
            number_format($materialCost, 2),
            number_format($qty > 0 ? $materialCost / $qty : 0, 2),
            $row->planned_date?->format('Y-m-d') ?? '—',
            $row->completed_at?->format('Y-m-d') ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
