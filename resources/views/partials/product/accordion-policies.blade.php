@php
  use App\Models\Policy;
  use Illuminate\Support\Str;

  // Prefijo consistente con el resto
  $prefix = $prefix ?? ('pol-' . ($product->product_id ?? Str::uuid()));
  $locale = app()->getLocale();

  // Lookup robusto: primero IDs “matriculados”, luego type, luego slugs legacy
  $POLICY_IDS = ['cancellation' => 2, 'refund' => 3];
  $LEGACY = [
    'cancellation' => ['cancellation-policy', 'politicas-de-cancelacion'],
    'refund'       => ['refund-policy', 'politicas-de-reembolso'],
  ];

  $cancel = $cancelPolicy
         ?? Policy::find($POLICY_IDS['cancellation'])
         ?? (method_exists(Policy::class, 'byType') ? Policy::byType('cancelacion') : null)
         ?? Policy::whereIn('slug', $LEGACY['cancellation'])->first();

  $refund = $refundPolicy
         ?? Policy::find($POLICY_IDS['refund'])
         ?? (method_exists(Policy::class, 'byType') ? Policy::byType('reembolso') : null)
         ?? Policy::whereIn('slug', $LEGACY['refund'])->first();

  $tCancel = $cancel?->translation($locale) ?? $cancel?->translation('es');
  $tRefund = $refund?->translation($locale) ?? $refund?->translation('es');
@endphp

{{-- Acordeón "Policies" (contiene Cancellation y Refund) --}}
<div class="accordion-item border-0 border-bottom">
  <h2 class="accordion-header" id="{{ $prefix }}-policies-heading">
    <button
      class="accordion-button bg-white px-0 shadow-none collapsed"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#{{ $prefix }}-policies"
      aria-expanded="false"
      aria-controls="{{ $prefix }}-policies"
    >
      <span class="me-2 d-inline-flex align-items-center" aria-hidden="true">
        <i class="fas fa-plus icon-plus"></i>
        <i class="fas fa-minus icon-minus"></i>
      </span>
      {{ __('adminlte::adminlte.policies') }}
    </button>
  </h2>

  <div id="{{ $prefix }}-policies"
       class="accordion-collapse collapse"
       aria-labelledby="{{ $prefix }}-policies-heading"
       data-bs-parent="#tourDetailsAccordion">
    <div class="accordion-body px-0">

      {{-- Cancellation --}}
      @if($tCancel && filled($tCancel->content))
        <h6 class="fw-semibold mb-2">
          <i class="fas fa-ban me-2"></i>
          {{ $tCancel->name ?? __('policies.cancellation_policy') }}
        </h6>
        <div class="mb-3">{!! nl2br(e($tCancel->content)) !!}</div>
      @endif

      {{-- Refund --}}
      @if($tRefund && filled($tRefund->content))
        <h6 class="fw-semibold mb-2">
          <i class="fas fa-hand-holding-usd me-2"></i>
          {{ $tRefund->name ?? __('policies.refund_policy') }}
        </h6>
        <div>{!! nl2br(e($tRefund->content)) !!}</div>
      @endif

      {{-- Vacío --}}
      @if(!( $tCancel && filled($tCancel->content)) && !( $tRefund && filled($tRefund->content)))
        <span class="text-muted">{{ __('policies.no_policies') }}</span>
      @endif
    </div>
  </div>
</div>
