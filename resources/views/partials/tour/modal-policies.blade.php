@php
  use App\Models\Policy;

  $cancel  = Policy::byType('cancelacion');
  $refund  = Policy::byType('reembolso');
  $tCancel = $cancel?->translation();
  $tRefund = $refund?->translation();
  $limit = 3000;
  $trim  = function (?string $txt) use ($limit) {
    if (!$txt) return null;
    $out = mb_strimwidth($txt, 0, $limit, '…', 'UTF-8');
    return nl2br(e($out));
  };

  $cancelTitle   = $tCancel?->title ?: __('Política de Cancelación');
  $cancelContent = $tCancel && filled($tCancel->content)
      ? $trim($tCancel->content)
      : '<em class="text-muted">'.e(__('No hay una política de cancelación configurada.')).'</em>';

  $refundTitle   = $tRefund?->title ?: __('Política de Reembolsos');
  $refundContent = $tRefund && filled($tRefund->content)
      ? $trim($tRefund->content)
      : '<em class="text-muted">'.e(__('No hay una política de reembolsos configurada.')).'</em>';
@endphp

{{-- Hidden templates just for cancelation and refund --}}
<template id="tpl-policy-cancelacion">
  <h5 class="modal-title">{{ $cancelTitle }}</h5>
  <div class="policy-body">{!! $cancelContent !!}</div>
</template>

<template id="tpl-policy-reembolso">
  <h5 class="modal-title">{{ $refundTitle }}</h5>
  <div class="policy-body">{!! $refundContent !!}</div>
</template>

{{-- MODAL --}}
<div class="modal fade" id="policyModal" tabindex="-1" aria-labelledby="policyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="policyModalLabel" class="modal-title">{{ __('Política') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Cerrar') }}"></button>
      </div>
      <div class="modal-body">
        <div id="policyModalBody" class="text-muted small"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">
          {{ __('Cerrar') }}
        </button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
(function () {
  function openPolicy(kind) {
    const tpl  = document.getElementById(`tpl-policy-${kind}`);
    const body = document.getElementById('policyModalBody');
    const titleEl = document.getElementById('policyModalLabel');
    if (!tpl || !body || !titleEl) return;

    const frag = tpl.content.cloneNode(true);
    const newTitle = frag.querySelector('.modal-title')?.textContent || '{{ __('Política') }}';
    const newBody  = frag.querySelector('.policy-body')?.innerHTML || '';

    titleEl.textContent = newTitle;
    body.innerHTML = newBody;

    const modalEl = document.getElementById('policyModal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
  }

  document.addEventListener('click', function(e) {
    const btn = e.target.closest('[data-policy]');
    if (!btn) return;

    const kind = btn.getAttribute('data-policy');
    if (kind === 'cancelacion' || kind === 'reembolso') {
      e.preventDefault();
      openPolicy(kind);
    }
  });

  window.openPolicyModal = openPolicy;
})();
</script>
@endpush
