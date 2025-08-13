@php
    /** @var \App\Models\Tour $tour */
    $prefix   = 'policies-'.$tour->tour_id;
    $cancel   = \App\Models\Policy::byType('cancelacion');
    $refund   = \App\Models\Policy::byType('reembolso');
    $tCancel  = $cancel?->translation();
    $tRefund  = $refund?->translation();
@endphp

<div class="accordion-item border-0 border-bottom">
  <h2 class="accordion-header" id="heading-{{ $prefix }}">
    <button
      class="accordion-button bg-white px-0 shadow-none collapsed"
      type="button"
      data-bs-toggle="collapse" data-toggle="collapse"
      data-bs-target="#collapse-{{ $prefix }}" data-target="#collapse-{{ $prefix }}"
      aria-expanded="false" aria-controls="collapse-{{ $prefix }}"
    >
      <span class="me-2 d-inline-flex align-items-center" aria-hidden="true">
        <i class="fas fa-plus icon-plus"></i>
        <i class="fas fa-minus icon-minus"></i>
      </span>
      {{ __('adminlte::adminlte.policies') }}
    </button>
  </h2>

  <div id="collapse-{{ $prefix }}" class="accordion-collapse collapse"
       data-bs-parent="#tourDetailsAccordion" data-parent="#tourDetailsAccordion">
    <div class="accordion-body px-0">

      {{-- Sub-acordeón interno --}}
      <div class="accordion" id="inner-{{ $prefix }}">
        {{-- Cancelación --}}
        <div class="accordion-item border-0 border-top">
          <h2 class="accordion-header" id="heading-cancel-{{ $prefix }}">
            <button
              class="accordion-button bg-white px-0 shadow-none collapsed"
              type="button"
              data-bs-toggle="collapse" data-toggle="collapse"
              data-bs-target="#collapse-cancel-{{ $prefix }}" data-target="#collapse-cancel-{{ $prefix }}"
              aria-expanded="false" aria-controls="collapse-cancel-{{ $prefix }}"
            >
              <span class="me-2 d-inline-flex align-items-center" aria-hidden="true">
                <i class="fas fa-plus icon-plus"></i>
                <i class="fas fa-minus icon-minus"></i>
              </span>
              {{ $tCancel?->title ?? __('Política de Cancelación') }}
            </button>
          </h2>
          <div id="collapse-cancel-{{ $prefix }}" class="accordion-collapse collapse"
               data-bs-parent="#inner-{{ $prefix }}" data-parent="#inner-{{ $prefix }}">
            <div class="accordion-body px-0">
              @if($cancel && $tCancel && filled($tCancel->content))
                {!! nl2br(e($tCancel->content)) !!}
              @else
                <em class="text-muted">{{ __('No hay una política de cancelación configurada.') }}</em>
              @endif
            </div>
          </div>
        </div>

        {{-- Reembolsos --}}
        <div class="accordion-item border-0 border-top">
          <h2 class="accordion-header" id="heading-refund-{{ $prefix }}">
            <button
              class="accordion-button bg-white px-0 shadow-none collapsed"
              type="button"
              data-bs-toggle="collapse" data-toggle="collapse"
              data-bs-target="#collapse-refund-{{ $prefix }}" data-target="#collapse-refund-{{ $prefix }}"
              aria-expanded="false" aria-controls="collapse-refund-{{ $prefix }}"
            >
              <span class="me-2 d-inline-flex align-items-center" aria-hidden="true">
                <i class="fas fa-plus icon-plus"></i>
                <i class="fas fa-minus icon-minus"></i>
              </span>
              {{ $tRefund?->title ?? __('Política de Reembolsos') }}
            </button>
          </h2>
          <div id="collapse-refund-{{ $prefix }}" class="accordion-collapse collapse"
               data-bs-parent="#inner-{{ $prefix }}" data-parent="#inner-{{ $prefix }}">
            <div class="accordion-body px-0">
              @if($refund && $tRefund && filled($tRefund->content))
                {!! nl2br(e($tRefund->content)) !!}
              @else
                <em class="text-muted">{{ __('No hay una política de reembolsos configurada.') }}</em>
              @endif
            </div>
          </div>
        </div>
      </div>
      {{-- /inner accordion --}}

    </div>
  </div>
</div>
