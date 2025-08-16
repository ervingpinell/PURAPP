@php
  /** @var \App\Models\Tour $tour */
  /** @var \App\Models\Policy|null $cancel */
  /** @var \App\Models\Policy|null $refund */

  use App\Models\Policy;
  use Illuminate\Support\Str;

  // ID único para este bloque (si no lo pasan desde el include)
  $prefix = $prefix ?? ('pol-' . ($tour->tour_id ?? Str::uuid()));

  // Si no te enviaron las políticas desde la vista/controlador, cárgalas aquí
  $cancel = $cancel ?? Policy::byType('cancelacion');
  $refund = $refund ?? Policy::byType('reembolso');

  // Traducciones (el modelo ya maneja fallback de locale)
  $tCancel = $cancel?->translation();
  $tRefund = $refund?->translation();
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
      {{ __('adminlte::adminlte.policies') }}
    </button>
  </h2>

  <div id="collapse-{{ $prefix }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $prefix }}">
    <div class="accordion-body px-0">

      {{-- Sub-acordeón interno --}}
      <div class="accordion" id="inner-{{ $prefix }}">

        {{-- Cancelación --}}
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
              {{ $tCancel?->title ?? __('Política de Cancelación') }}
            </button>
          </h2>

          <div id="collapse-cancel-{{ $prefix }}" class="accordion-collapse collapse" aria-labelledby="heading-cancel-{{ $prefix }}" data-bs-parent="#inner-{{ $prefix }}">
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
              data-bs-toggle="collapse"
              data-bs-target="#collapse-refund-{{ $prefix }}"
              aria-expanded="false"
              aria-controls="collapse-refund-{{ $prefix }}"
            >
              <span class="me-2 d-inline-flex align-items-center" aria-hidden="true">
                <i class="fas fa-plus icon-plus"></i>
                <i class="fas fa-minus icon-minus"></i>
              </span>
              {{ $tRefund?->title ?? __('Política de Reembolsos') }}
            </button>
          </h2>

          <div id="collapse-refund-{{ $prefix }}" class="accordion-collapse collapse" aria-labelledby="heading-refund-{{ $prefix }}" data-bs-parent="#inner-{{ $prefix }}">
            <div class="accordion-body px-0">
              @if($refund && $tRefund && filled($tRefund->content))
                {!! nl2br(e($tRefund->content)) !!}
              @else
                <em class="text-muted">{{ __('No hay una política de reembolsos configurada.') }}</em>
              @endif
            </div>
          </div>
        </div>

        {{-- Mensaje general si ambas faltan --}}
        @if((!$cancel || !$tCancel || blank($tCancel->content)) && (!$refund || !$tRefund || blank($tRefund->content)))
          <div class="small text-muted mt-3">
            <em>{{ __('No hay políticas configuradas por el momento.') }}</em>
          </div>
        @endif

      </div>
      {{-- /inner accordion --}}

    </div>
  </div>
</div>
