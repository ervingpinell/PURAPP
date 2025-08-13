@php
  /** @var \App\Models\Tour $tour */
  use App\Models\Policy;

  $cancel  = Policy::byType('cancelacion');
  $refund  = Policy::byType('reembolso');

  $tCancel = $cancel?->translation(app()->getLocale()) ?? $cancel?->translation('es');
  $tRefund = $refund?->translation(app()->getLocale()) ?? $refund?->translation('es');

  $modalId = 'policiesModal-'.$tour->tour_id;

  // Limitar texto para hacerlo "pequeño"
  $limit = 600;
  $trim  = function (?string $txt) use ($limit) {
    if (!$txt) return null;
    // Corta conservando multibyte y agrega "…"
    $out = mb_strimwidth($txt, 0, $limit, '…', 'UTF-8');
    return nl2br(e($out));
  };
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="{{ $modalId }}Label">{{ __('adminlte::adminlte.policies') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
      </div>

      <div class="modal-body">
        {{-- Cancelación --}}
        <div class="mb-3">
          <h6 class="mb-1">{{ $tCancel?->title ?? __('Política de Cancelación') }}</h6>
          @if($tCancel && filled($tCancel->content))
            <div class="small text-muted">{!! $trim($tCancel->content) !!}</div>
          @else
            <div class="small text-muted"><em>{{ __('No hay una política de cancelación configurada.') }}</em></div>
          @endif
        </div>

        {{-- Reembolsos --}}
        <div>
          <h6 class="mb-1">{{ $tRefund?->title ?? __('Política de Reembolsos') }}</h6>
          @if($tRefund && filled($tRefund->content))
            <div class="small text-muted">{!! $trim($tRefund->content) !!}</div>
          @else
            <div class="small text-muted"><em>{{ __('No hay una política de reembolsos configurada.') }}</em></div>
          @endif
        </div>
      </div>

      <div class="modal-footer">
        <a href="{{ route('policies.index') }}" class="btn btn-outline-secondary btn-sm">
          {{ __('Ver todas las políticas') }}
        </a>
        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">{{ __('Cerrar') }}</button>
      </div>
    </div>
  </div>
</div>
