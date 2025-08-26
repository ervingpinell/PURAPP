@php
  use App\Models\Policy;

  $cancel  = Policy::byType('cancelacion');
  $refund  = Policy::byType('reembolso');
  $tCancel = $cancel?->translation();
  $tRefund = $refund?->translation();
  $limit   = 3000;

  $trim = function (?string $txt) use ($limit) {
    if (!$txt) return null;
    $out = mb_strimwidth($txt, 0, $limit, '…', 'UTF-8');
    return nl2br(e($out));
  };

  $cancelName    = $tCancel?->name ?: __('policies.cancellation_policy');
  $cancelContent = $tCancel && filled($tCancel->content) ? $trim($tCancel->content) : null;

  $refundName    = $tRefund?->name ?: __('policies.refund_policy');
  $refundContent = $tRefund && filled($tRefund->content) ? $trim($tRefund->content) : null;
@endphp

@if($cancelContent || $refundContent)
<div class="modal fade" id="policiesModal" tabindex="-1" aria-labelledby="policiesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="policiesModalLabel">{{ __('policies.page_title') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        @if($cancelContent)
          <div class="mb-4">
            <h6 class="mb-1">{{ $cancelName }}</h6>
            <div>{!! $cancelContent !!}</div>
          </div>
        @endif

        @if($refundContent)
          <div class="mb-2">
            <h6 class="mb-1">{{ $refundName }}</h6>
            <div>{!! $refundContent !!}</div>
          </div>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
      </div>
    </div>
  </div>
</div>
@endif

@push('scripts')
<script>
(function () {
  function openPolicy(kind) {
    var modalEl = document.getElementById('policiesModal');
    if (!modalEl) return;
    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();

    try { document.getElementById('policiesModalLabel').focus(); } catch (e) {}
  }

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-policy]');
    if (!btn) return;

    var kind = btn.getAttribute('data-policy');
    if (kind === 'cancelacion' || kind === 'reembolso') {
      e.preventDefault();
      openPolicy(kind);
    }
  });

  window.openPolicyModal = openPolicy;
})();
</script>
@endpush
