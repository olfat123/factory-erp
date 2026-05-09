<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<title>{{ __('resources.reports.production_report') }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #111; padding: 24px; }
    h1 { font-size: 18px; font-weight: 700; margin-bottom: 4px; }
    .meta { font-size: 10px; color: #555; margin-bottom: 16px; }
    .summary { display: table; width: 100%; margin-bottom: 16px; }
    .summary-inner { display: table-row; }
    .card { display: table-cell; border: 1px solid #d1d5db; border-radius: 4px; padding: 10px 16px; width: 33%; }
    .card-label { font-size: 9px; color: #6b7280; text-transform: uppercase; }
    .card-value { font-size: 16px; font-weight: 700; color: #7c3aed; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    thead tr { background: #f3f4f6; }
    th { padding: 6px 8px; text-align: left; font-size: 10px; font-weight: 600; border-bottom: 2px solid #e5e7eb; }
    td { padding: 5px 8px; font-size: 10px; border-bottom: 1px solid #f3f4f6; }
    .text-end { text-align: right; }
    .total-row td { font-weight: 700; background: #f3f4f6; border-top: 2px solid #d1d5db; }
    @if(app()->getLocale() === 'ar')
    body { direction: rtl; text-align: right; }
    th, td { text-align: right; }
    .text-end { text-align: left; }
    @endif
</style>
</head>
<body>
    <h1>{{ __('resources.reports.production_report') }}</h1>
    <div class="meta">{{ __('resources.reports.generated') }}: {{ $generated }} | {{ __('resources.reports.period') }}: {{ $from }} - {{ $to }}</div>

    <div class="summary"><div class="summary-inner">
        <div class="card">
            <div class="card-label">{{ __('resources.reports.total_orders') }}</div>
            <div class="card-value">{{ $orders->count() }}</div>
        </div>
        <div class="card">
            <div class="card-label">{{ __('resources.reports.completed_orders') }}</div>
            <div class="card-value">{{ $orders->where('status', 'completed')->count() }}</div>
        </div>
        <div class="card">
            <div class="card-label">{{ __('resources.reports.total_material_cost') }}</div>
            <div class="card-value">{{ number_format($total_cost, 2) }}</div>
        </div>
    </div></div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('resources.fields.product') }}</th>
                <th>{{ __('resources.reports.status') }}</th>
                <th class="text-end">{{ __('resources.fields.planned_quantity') }}</th>
                <th class="text-end">{{ __('resources.fields.actual_quantity') }}</th>
                <th class="text-end">{{ __('resources.reports.material_cost') }}</th>
                <th>{{ __('resources.fields.planned_date') }}</th>
                <th>{{ __('resources.reports.completed_at') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $o)
                <tr>
                    <td>{{ $o['id'] }}</td>
                    <td>{{ $o['product'] }}</td>
                    <td>{{ $o['status'] }}</td>
                    <td class="text-end">{{ number_format($o['quantity'], 2) }}</td>
                    <td class="text-end">{{ number_format($o['completed_qty'], 2) }}</td>
                    <td class="text-end">{{ number_format($o['material_cost'], 2) }}</td>
                    <td>{{ $o['planned_date'] ?? '—' }}</td>
                    <td>{{ $o['completed_at'] ?? '—' }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="text-end">{{ __('resources.reports.total_material_cost') }}</td>
                <td class="text-end">{{ number_format($total_cost, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
