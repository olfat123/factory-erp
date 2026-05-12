<x-filament-panels::page>
    <div class="space-y-4">
        {{-- Summary card --}}
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.25rem;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:1rem;font-weight:600;color:#374151;">
                {{ __('resources.pages.total_inventory_value') }}
            </span>
            <span style="font-size:1.25rem;font-weight:700;color:#059669;">
                {{ number_format($this->getTotalValue(), 2) }}
            </span>
        </div>
        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:0.5rem;padding:1.25rem;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:1rem;font-weight:600;color:#374151;">
                {{ __('resources.fields.total_market_cost') }}
            </span>
            <span style="font-size:1.25rem;font-weight:700;color:#0369a1;">
                {{ number_format($this->getTotalMarketValue(), 2) }}
            </span>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:0.875rem;">
                <thead>
                    <tr style="background:#f3f4f6;text-align:start;">
                        <th style="padding:0.625rem 0.75rem;border-bottom:1px solid #e5e7eb;">{{ __('resources.fields.code') }}</th>
                        <th style="padding:0.625rem 0.75rem;border-bottom:1px solid #e5e7eb;">{{ __('resources.fields.name') }}</th>
                        <th style="padding:0.625rem 0.75rem;border-bottom:1px solid #e5e7eb;">{{ __('resources.fields.category') }}</th>
                        <th style="padding:0.625rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.current_stock') }}</th>
                        <th style="padding:0.625rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.average_cost') }}</th>
                        <th style="padding:0.625rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.total_value') }}</th>
                        <th style="padding:0.625rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.market_cost') }}</th>
                        <th style="padding:0.625rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.market_value') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->getItems() as $item)
                        <tr style="border-bottom:1px solid #f3f4f6;">
                            <td style="padding:0.5rem 0.75rem;">{{ $item['code'] }}</td>
                            <td style="padding:0.5rem 0.75rem;">{{ $item['name'] }}</td>
                            <td style="padding:0.5rem 0.75rem;">{{ $item['category'] }}</td>
                            <td style="padding:0.5rem 0.75rem;text-align:end;">{{ number_format($item['current_stock'], 4) }} {{ $item['unit'] }}</td>
                            <td style="padding:0.5rem 0.75rem;text-align:end;">{{ number_format($item['average_cost'], 2) }}</td>
                            <td style="padding:0.5rem 0.75rem;text-align:end;font-weight:600;">{{ number_format($item['total_value'], 2) }}</td>
                            <td style="padding:0.5rem 0.75rem;text-align:end;color:#0369a1;">{{ number_format($item['market_cost'], 2) }}</td>
                            <td style="padding:0.5rem 0.75rem;text-align:end;font-weight:600;color:#0369a1;">{{ number_format($item['market_value'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:1.5rem;text-align:center;color:#9ca3af;">
                                {{ __('resources.pages.no_data') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
