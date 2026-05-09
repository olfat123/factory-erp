<?php

namespace App\Http\Controllers;

use App\Exports\FinancialReportExport;
use App\Exports\InventoryReportExport;
use App\Exports\ProductionReportExport;
use App\Exports\StockMovementsExport;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\ProductionOrder;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    // ---------------------------------------------------------
    // Excel exports
    // ---------------------------------------------------------

    public function inventoryExcel(Request $request): BinaryFileResponse
    {
        return Excel::download(new InventoryReportExport(), 'inventory-report.xlsx');
    }

    public function stockMovementsExcel(Request $request): BinaryFileResponse
    {
        ['from' => $from, 'to' => $to] = $this->dateRange($request);
        return Excel::download(new StockMovementsExport($from, $to), 'stock-movements.xlsx');
    }

    public function productionExcel(Request $request): BinaryFileResponse
    {
        ['from' => $from, 'to' => $to] = $this->dateRange($request);
        return Excel::download(new ProductionReportExport($from, $to), 'production-report.xlsx');
    }

    public function financialExcel(): BinaryFileResponse
    {
        return Excel::download(new FinancialReportExport(), 'financial-report.xlsx');
    }

    // ---------------------------------------------------------
    // PDF exports
    // ---------------------------------------------------------

    public function inventoryPdf(Request $request): Response
    {
        ['from' => $from, 'to' => $to] = $this->dateRange($request);
        $items = Material::with('category', 'unit')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn ($m) => [
                'code'          => $m->code,
                'name'          => $m->name,
                'category'      => $m->category?->name,
                'unit'          => $m->unit?->abbreviation,
                'current_stock' => (float) $m->current_stock,
                'minimum_stock' => (float) $m->minimum_stock,
                'average_cost'  => (float) $m->average_cost,
                'total_value'   => (float) $m->current_stock * (float) $m->average_cost,
                'is_low'        => (float) $m->current_stock <= (float) $m->minimum_stock,
            ]);

        $html = view('reports.inventory-pdf', [
            'items'       => $items,
            'total_value' => $items->sum('total_value'),
            'from'        => $from,
            'to'          => $to,
            'generated'   => now()->format('Y-m-d H:i'),
        ])->render();

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-L', 'autoScriptToLang' => true, 'autoLangToFont' => true]);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('inventory-report.pdf', 'S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="inventory-report.pdf"',
        ]);
    }

    public function productionPdf(Request $request): Response
    {
        ['from' => $from, 'to' => $to] = $this->dateRange($request);
        $query = ProductionOrder::with(['product', 'items.material'])->orderByDesc('created_at');
        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to . ' 23:59:59']);
        }
        $orders = $query->get()->map(fn ($o) => [
            'id'            => $o->id,
            'product'       => $o->product?->name,
            'status'        => $o->status instanceof \BackedEnum ? $o->status->value : $o->status,
            'quantity'      => (float) $o->quantity,
            'completed_qty' => (float) ($o->completed_quantity ?? 0),
            'material_cost' => $o->items->sum(fn ($i) => $i->consumed_quantity * ($i->material?->average_cost ?? 0)),
            'planned_date'  => $o->planned_date?->format('Y-m-d'),
            'completed_at'  => $o->completed_at?->format('Y-m-d'),
        ]);

        $html = view('reports.production-pdf', [
            'orders'     => $orders,
            'total_cost' => $orders->sum('material_cost'),
            'from'       => $from,
            'to'         => $to,
            'generated'  => now()->format('Y-m-d H:i'),
        ])->render();

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-L', 'autoScriptToLang' => true, 'autoLangToFont' => true]);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('production-report.pdf', 'S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="production-report.pdf"',
        ]);
    }

    public function financialPdf(Request $request): Response
    {
        ['from' => $from, 'to' => $to] = $this->dateRange($request);
        $items = Material::with('category', 'unit')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn ($m) => [
                'code'        => $m->code,
                'name'        => $m->name,
                'category'    => $m->category?->name,
                'unit'        => $m->unit?->abbreviation,
                'stock'       => (float) $m->current_stock,
                'avg_cost'    => (float) $m->average_cost,
                'total_value' => (float) $m->current_stock * (float) $m->average_cost,
            ]);

        $html = view('reports.financial-pdf', [
            'items'     => $items,
            'total'     => $items->sum('total_value'),
            'from'      => $from,
            'to'        => $to,
            'generated' => now()->format('Y-m-d H:i'),
        ])->render();

        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-L', 'autoScriptToLang' => true, 'autoLangToFont' => true]);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('financial-report.pdf', 'S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="financial-report.pdf"',
        ]);
    }

    // ---------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------

    private function dateRange(Request $request): array
    {
        return [
            'from' => $request->get('from', ''),
            'to'   => $request->get('to', ''),
        ];
    }
}
