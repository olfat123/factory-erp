<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<title>{{ __('resources.reports.financial_report') }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #111; padding: 24px; }
    h1 { font-size: 18px; font-weight: 700; margin-bottom: 4px; }
    h2 { font-size: 13px; font-weight: 600; margin: 16px 0 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
    .meta { font-size: 10px; color: #555; margin-bottom: 16px; }
    .summary { display: table; width: 100%; margin-bottom: 16px; }
    .summary-inner { display: table-row; }
    .card { display: table-cell; border: 1px solid #d1d5db; border-radius: 4px; padding: 10px 16px; width: 50%; }
    .card-label { font-size: 9px; color: #6b7280; text-transform: uppercase; }
    .card-value { font-size: 16px; font-weight: 700; color: #1d4ed8; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #f3f4f6; }
    th { padding: 6px 8px; text-align: left; font-size: 10px; font-weight: 600; border-bottom: 2px solid #e5e7eb; }
    td { padding: 5px 8px; font-size: 10px; border-bottom: 1px solid #f3f4f6; }
    .text-end { text-align: right; }
    .total-row td { font-weight: 700; background: #f3f4f6; border-top: 2px solid #d1d5db; }
    .page-break { page-break-before: always; }
    @if(app()->getLocale() === 'ar')
    body { direction: rtl; text-align: right; }
    th, td { text-align: right; }
    .text-end { text-align: left; }
    @endif
</style>
</head>
<body>
    <h1>{{ __('resources.reports.financial_report') }}</h1>
    <div class="meta">{{ __('resources.reports.generated') }}: {{ $generated }} | {{ __('resources.reports.period') }}: {{ $from }} - {{ $to }}</div>

    <div class="summary"><div class="summary-inner">
        <div class="card">
            <div class="card-label">{{ __('resources.pages.total_inventory_value') }}</div>
            <div class="card-value">{{ number_format($total, 2) }}</div>
        </div>
        <div class="card">
            <div class="card-label">{{ __('resources.sections.items') }}</div>
            <div class="card-value">{{ $items->count() }}</div>
        </div>
    </div></div>

    <h2>{{ __('resources.pages.inventory_valuation') }}</h2>
    <table>
        <thead>
            <tr>
                <th>{{ __('resources.fields.code') }}</th>
                <th>{{ __('resources.fields.name') }}</th>
                <th>{{ __('resources.fields.category') }}</th>
                <th>{{ __('resources.fields.unit') }}</th>
                <th class="text-end">{{ __('resources.fields.current_stock') }}</th>
                <th class="text-end">{{ __('resources.fields.average_cost') }}</th>
                <th class="text-end">{{ __('resources.fields.total_value') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $item['code'] }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['category'] }}</td>
                    <td>{{ $item['unit'] }}</td>
                    <td class="text-end">{{ number_format($item['stock'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['avg_cost'], 2) }}</td>
                    <td class="text-end">{{ number_format($item['total_value'], 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6" class="text-end">{{ __('resources.pages.total_inventory_value') }}</td>
                <td class="text-end">{{ number_format($total, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
