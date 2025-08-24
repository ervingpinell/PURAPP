@php
  use App\Models\Policy;
  use Illuminate\Support\Str;

  $prefix  = $prefix ?? ('pol-' . ($tour->tour_id ?? Str::uuid()));
  $locale  = app()->getLocale();

  $cancel  = $cancel ?? Policy::byType('cancelacion');
  $refund  = $refund ?? Policy::byType('reembolso');

  // Si tu método translation() acepta locale, pásalo; si no, deja como estaba.
  $tCancel = $cancel?->translation($locale) ?? $cancel?->translation('es');
  $tRefund = $refund?->translation($locale) ?? $refund?->translation('es');
@endphp

<div class="accordion-item border-0 border-bottom">
  <h2 class="accordion-header" id="heading-{{ $prefix }}">
    <button
      class="accordion-button bg-white px-0 shadow-none collapsed"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#collapse-{{ $prefix }}"
      aria-expanded="false"
      aria-controls="collapse-{{ $prefix }}"
    >
      <span class="me-2 d-inline-flex align-items-center" aria-hidden="true">
        <i class="fas fa-plus icon-plus"></i>
        <i class="fas fa-minus icon-minus"></i>
      </span>
      {{ __('policies.page_title') }}
    </button>
  </h2>

  <div id="collapse-{{ $prefix }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $prefix }}">
    <div class="accordion-body px-0">

      <div class="accordion" id="inner-{{ $prefix }}">
        {{-- Cancellation --}}
        <div class="accordion-item border-0 border-top">
          <h2 class="accordion-header" id="heading-cancel-{{ $prefix }}">
            <button
              class="accordion-button bg-white px-0 shadow-none collapsed"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#collapse-cancel-{{ $prefix }}"
              aria-expanded="false"
              aria-controls="collapse-cancel-{{ $prefix }}"
            >
              <span class="me-2 d-inline-flex align-items-center" aria-hidden="true">
                <i class="fas fa-plus icon-plus"></i>
                <i class="fas fa-minus icon-minus"></i>
              </span>
              {{ $tCancel?->title ?? __('policies.cancellation_policy') }}
            </button>
          </h2>

          <div id="collapse-cancel-{{ $prefix }}" class="accordion-collapse collapse" aria-labelledby="heading-cancel-{{ $prefix }}" data-bs-parent="#inner-{{ $prefix }}">
            <div class="accordion-body px-0">
              @if($cancel && $tCancel && filled($tCancel->content))
                {!! nl2br(e($tCancel->content)) !!}
              @else
                <em class="text-muted">{{ __('policies.no_cancellation_policy') }}</em>
              @endif
            </div>
          </div>
        </div>

        {{-- Refund --}}
        <div class="accordion-item border-0 border-top">
          <h2 class="accordion-header" id="heading-refund-{{ $prefix }}">
            <button
              class="accordion-button bg-white px-0 shadow-none collapsed"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#collapse-refund-{{ $prefix }}"
              aria-expanded="false"
              aria-controls="collapse-refund-{{ $prefix }}"
            >
              <span class="me-2 d-inline-flex align-items-center" aria-hidden="true">
                <i class="fas fa-plus icon-plus"></i>
                <i class="fas fa-minus icon-minus"></i>
              </span>
              {{ $tRefund?->title ?? __('policies.refund_policy') }}
            </button>
          </h2>

          <div id="collapse-refund-{{ $prefix }}" class="accordion-collapse collapse" aria-labelledby="heading-refund-{{ $prefix }}" data-bs-parent="#inner-{{ $prefix }}">
            <div class="accordion-body px-0">
              @if($refund && $tRefund && filled($tRefund->content))
                {!! nl2br(e($tRefund->content)) !!}
              @else
                <em class="text-muted">{{ __('policies.no_refund_policy') }}</em>
              @endif
            </div>
          </div>
        </div>

        {{-- Fallback global --}}
        @if((!$cancel || !$tCancel || blank($tCancel->content)) && (!$refund || !$tRefund || blank($tRefund->content)))
          <div class="small text-muted mt-3">
            <em>{{ __('policies.no_policies') }}</em>
          </div>
        @endif

      </div>

    </div>
  </div>
</div>
