<x-filament-panels::page>
    <div class="space-y-4">
        {{-- Summary card --}}
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:0.5rem;padding:1.25rem;display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:1rem;font-weight:600;color:#374151;">
                {{ __('resources.pages.total_production_cost') }}
            </span>
            <span style="font-size:1.25rem;font-weight:700;color:#d97706;">
                {{ number_format($this->getTotalCost(), 2) }}
            </span>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:0.875rem;">
                <thead>
                    <tr style="background:#f3f4f6;text-align:start;">
                        <th style="padding:0.625rem 0.75rem;border-bottom:1px solid #e5e7eb;">#</th>
                        <th style="padding:0.625rem 0.75rem;border-bottom:1px solid #e5e7eb;">{{ __('resources.fields.product') }}</th>
                        <th style="padding:0.625rem 0.75rem;border-bottom:1px solid #e5e7eb;">{{ __('resources.fields.status') }}</th>
                        <th style="padding:0.625rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.quantity') }}</th>
                        <th style="padding:0.625rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.material_cost') }}</th>
                        <th style="padding:0.625rem 0.75rem;border-bottom:1px solid #e5e7eb;text-align:end;">{{ __('resources.fields.unit_cost') }}</th>
                        <th style="padding:0.625rem 0.75rem;border-bottom:1px solid #e5e7eb;">{{ __('resources.fields.date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->getOrders() as $order)
                        <tr style="border-bottom:1px solid #f3f4f6;">
                            <td style="padding:0.5rem 0.75rem;color:#6b7280;">{{ $order['id'] }}</td>
                            <td style="padding:0.5rem 0.75rem;font-weight:500;">{{ $order['product'] }}</td>
                            <td style="padding:0.5rem 0.75rem;">{{ $order['status'] }}</td>
                            <td style="padding:0.5rem 0.75rem;text-align:end;">{{ number_format($order['quantity'], 2) }}</td>
                            <td style="padding:0.5rem 0.75rem;text-align:end;font-weight:600;">{{ number_format($order['material_cost'], 2) }}</td>
                            <td style="padding:0.5rem 0.75rem;text-align:end;">{{ number_format($order['unit_cost'], 2) }}</td>
                            <td style="padding:0.5rem 0.75rem;color:#6b7280;">{{ $order['created_at']?->format('Y-m-d') }}</td>
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
