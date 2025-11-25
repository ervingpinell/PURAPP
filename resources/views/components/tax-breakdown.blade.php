{{-- Tax Breakdown Component --}}
@props([
'breakdown' => null,
'showTitle' => true,
'compact' => false,
])

@if($breakdown && isset($breakdown['subtotal']))
<div {{ $attributes->merge(['class' => 'tax-breakdown']) }}>
    @if($showTitle)
    <h6 class="mb-2">{{ __('taxes.breakdown.title') }}</h6>
    @endif

    <div class="tax-breakdown-content {{ $compact ? 'small' : '' }}">
        <div class="d-flex justify-content-between mb-1">
            <span>{{ __('taxes.breakdown.subtotal') }}:</span>
            <span class="font-weight-bold">${{ number_format($breakdown['subtotal'], 2) }}</span>
        </div>

        @if($breakdown['tax_amount'] > 0)
        @if(isset($breakdown['taxes']) && count($breakdown['taxes']) > 0)
        @foreach($breakdown['taxes'] as $tax)
        <div class="d-flex justify-content-between mb-1 text-muted {{ $compact ? 'small' : '' }}">
            <span class="ml-2">{{ $tax['name'] }} ({{ $tax['rate'] }}):</span>
            <span>${{ number_format($tax['amount'], 2) }}</span>
        </div>
        @endforeach
        @else
        <div class="d-flex justify-content-between mb-1">
            <span>{{ __('taxes.breakdown.tax') }}:</span>
            <span>${{ number_format($breakdown['tax_amount'], 2) }}</span>
        </div>
        @endif
        @endif

        <hr class="my-2">

        <div class="d-flex justify-content-between">
            <span class="font-weight-bold">{{ __('taxes.breakdown.total') }}:</span>
            <span class="font-weight-bold text-primary">${{ number_format($breakdown['total'], 2) }}</span>
        </div>
    </div>
</div>
@endif