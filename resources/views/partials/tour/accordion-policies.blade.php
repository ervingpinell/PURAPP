@php
  use App\Models\Policy;
  use Illuminate\Support\Str;

  $prefix  = $prefix ?? ('pol-' . ($tour->tour_id ?? Str::uuid()));
  $locale  = app()->getLocale();

  $cancel  = $cancel ?? Policy::byType('cancelacion');
  $refund  = $refund ?? Policy::byType('reembolso');

  $tCancel = $cancel?->translation($locale) ?? $cancel?->translation('es');
  $tRefund = $refund?->translation($locale) ?? $refund?->translation('es');
@endphp

<div class="accordion-item border-0 border-bottom">
  <h2 class="accordion-header" id="{{ $prefix }}-cancel-heading">
    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $prefix }}-cancel" aria-expanded="false" aria-controls="{{ $prefix }}-cancel">
      <i class="fas fa-undo me-2"></i>
      {{ $tCancel?->name ?? __('policies.cancellation_policy') }}
    </button>
  </h2>
  <div id="{{ $prefix }}-cancel" class="accordion-collapse collapse" aria-labelledby="{{ $prefix }}-cancel-heading">
    <div class="accordion-body">
      @if($tCancel && filled($tCancel->content))
        {!! nl2br(e($tCancel->content)) !!}
      @else
        <span class="text-muted">{{ __('policies.no_content') }}</span>
      @endif
    </div>
  </div>
</div>

<div class="accordion-item border-0 border-bottom">
  <h2 class="accordion-header" id="{{ $prefix }}-refund-heading">
    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $prefix }}-refund" aria-expanded="false" aria-controls="{{ $prefix }}-refund">
      <i class="fas fa-hand-holding-usd me-2"></i>
      {{ $tRefund?->name ?? __('policies.refund_policy') }}
    </button>
  </h2>
  <div id="{{ $prefix }}-refund" class="accordion-collapse collapse" aria-labelledby="{{ $prefix }}-refund-heading">
    <div class="accordion-body">
      @if($tRefund && filled($tRefund->content))
        {!! nl2br(e($tRefund->content)) !!}
      @else
        <span class="text-muted">{{ __('policies.no_content') }}</span>
      @endif

      @if((!$cancel || !$tCancel || blank($tCancel->content)) && (!$refund || !$tRefund || blank($tRefund->content)))
        <div class="small text-muted mt-3">
          <em>{{ __('policies.no_policies') }}</em>
        </div>
      @endif
    </div>
  </div>
</div>
