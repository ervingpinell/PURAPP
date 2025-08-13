@php
    /** @var \App\Models\Tour $tour */
    $prefix   = 'policies-'.$tour->tour_id;                 // IDs únicos
    $cancel   = \App\Models\Policy::byType('cancelacion');
    $refund   = \App\Models\Policy::byType('reembolso');
    $tCancel  = $cancel?->translation();                    // usa fallback del modelo
    $tRefund  = $refund?->translation();
@endphp

<div class="accordion-item border-0 border-bottom">
  <h2 class="accordion-header" id="heading-{{ $prefix }}">
    <button class="accordion-button bg-white px-0 shadow-none collapsed" type="button"
            data-bs-toggle="collapse" data-bs-target="#collapse-{{ $prefix }}">
      <i class="fas fa-plus me-2 toggle-icon"></i> {{ __('adminlte::adminlte.policies') }}
    </button>
  </h2>

  <div id="collapse-{{ $prefix }}" class="accordion-collapse collapse"
       data-bs-parent="#tourDetailsAccordion">
    <div class="accordion-body px-0">

      {{-- Sub-acordeón interno para las dos políticas --}}
      <div class="accordion" id="inner-{{ $prefix }}">

        {{-- Política de Cancelación --}}
        <div class="accordion-item border-0 border-top">
          <h2 class="accordion-header" id="heading-cancel-{{ $prefix }}">
            <button class="accordion-button bg-white px-0 shadow-none collapsed" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapse-cancel-{{ $prefix }}">
              <i class="fas fa-plus me-2 toggle-icon"></i>
              {{ $tCancel?->title ?? __('Política de Cancelación') }}
            </button>
          </h2>
          <div id="collapse-cancel-{{ $prefix }}" class="accordion-collapse collapse"
               data-bs-parent="#inner-{{ $prefix }}">
            <div class="accordion-body px-0">
              @if($cancel && $tCancel && filled($tCancel->content))
                {!! nl2br(e($tCancel->content)) !!}
                <div class="mt-2">
                  <a href="{{ route('policies.show', $cancel) }}" class="btn btn-outline-primary btn-sm">
                    {{ __('Leer completa') }}
                  </a>
                </div>
              @else
                <em class="text-muted">{{ __('No hay una política de cancelación configurada.') }}</em>
              @endif
            </div>
          </div>
        </div>

        {{-- Política de Reembolsos --}}
        <div class="accordion-item border-0 border-top">
          <h2 class="accordion-header" id="heading-refund-{{ $prefix }}">
            <button class="accordion-button bg-white px-0 shadow-none collapsed" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapse-refund-{{ $prefix }}">
              <i class="fas fa-plus me-2 toggle-icon"></i>
              {{ $tRefund?->title ?? __('Política de Reembolsos') }}
            </button>
          </h2>
          <div id="collapse-refund-{{ $prefix }}" class="accordion-collapse collapse"
               data-bs-parent="#inner-{{ $prefix }}">
            <div class="accordion-body px-0">
              @if($refund && $tRefund && filled($tRefund->content))
                {!! nl2br(e($tRefund->content)) !!}
                <div class="mt-2">
                  <a href="{{ route('policies.show', $refund) }}" class="btn btn-outline-primary btn-sm">
                    {{ __('Leer completa') }}
                  </a>
                </div>
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

@push('js')
<script>
  // Opcional: alterna el ícono + / - en todos los botones de este bloque
  document.addEventListener('DOMContentLoaded', function () {
    const rootId = 'collapse-{{ $prefix }}';
    const root   = document.getElementById(rootId)?.closest('.accordion-item');
    if (!root) return;

    const toggleIcon = (btn, open) => {
      const icon = btn.querySelector('.toggle-icon');
      if (!icon) return;
      icon.classList.toggle('fa-plus', !open);
      icon.classList.toggle('fa-minus', !!open);
    };

    // Para el header principal
    const mainBtn = root.querySelector('h2 > .accordion-button');
    const mainCollapse = document.getElementById(rootId);
    if (mainBtn && mainCollapse) {
      mainCollapse.addEventListener('show.bs.collapse', () => toggleIcon(mainBtn, true));
      mainCollapse.addEventListener('hide.bs.collapse', () => toggleIcon(mainBtn, false));
    }

    // Para los dos sub-acordeones
    root.querySelectorAll('#inner-{{ $prefix }} .accordion-button').forEach(btn => {
      const target = btn.getAttribute('data-bs-target');
      if (!target) return;
      const collapseEl = document.querySelector(target);
      if (!collapseEl) return;
      collapseEl.addEventListener('show.bs.collapse', () => toggleIcon(btn, true));
      collapseEl.addEventListener('hide.bs.collapse', () => toggleIcon(btn, false));
    });
  });
</script>
@endpush
