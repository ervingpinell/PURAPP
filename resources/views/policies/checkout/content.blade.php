{{-- resources/views/policies/checkout/content.blade.php --}}
@php
  $T = fn($k) => __('policies.checkout.titles.' . $k);
  $V = fn($k) => __('policies.checkout.version.' . $k);
  $B = fn($k) => __('policies.checkout.bodies.' . $k . '_html');
@endphp

<style>
  /* separadores visuales por sección */
  .policy-block{margin-bottom:1.25rem;border:1px solid var(--g200);border-radius:.75rem;overflow:hidden;background:#fff}
  .policy-subheader{display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.75rem 1rem;background:var(--g50);border-bottom:1px solid var(--g200)}
  .policy-subheader .title{font-weight:700;color:var(--g800)}
  .policy-subheader .version{font-size:.75rem;color:var(--g600);background:#fff;padding:.15rem .6rem;border:1px solid var(--g300);border-radius:.5rem;white-space:nowrap}
  .policy-body{padding:1rem 1.25rem;line-height:1.75;color:var(--g700)}
  .policy-body ul{margin:0 0 1rem 1.25rem}
  .policy-body li{margin:.35rem 0}
</style>

{{-- Términos y Condiciones --}}
<div class="policy-block">
  <div class="policy-subheader">
    <span class="title">{{ $T('terms') }}</span>
    <span class="version">{{ $V('terms') }}</span>
  </div>
  <div class="policy-body">{!! $B('terms') !!}</div>
</div>

{{-- Privacidad --}}
<div class="policy-block">
  <div class="policy-subheader">
    <span class="title">{{ $T('privacy') }}</span>
    <span class="version">{{ $V('privacy') }}</span>
  </div>
  <div class="policy-body">{!! $B('privacy') !!}</div>
</div>

{{-- Cancelación --}}
<div class="policy-block">
  <div class="policy-subheader">
    <span class="title">{{ $T('cancellation') }}</span>
    <span class="version">v1</span>
  </div>
  <div class="policy-body">{!! $B('cancellation') !!}</div>
</div>

{{-- Devoluciones --}}
<div class="policy-block">
  <div class="policy-subheader">
    <span class="title">{{ $T('refunds') }}</span>
    <span class="version">v1</span>
  </div>
  <div class="policy-body">{!! $B('refunds') !!}</div>
</div>

{{-- Garantía --}}
<div class="policy-block">
  <div class="policy-subheader">
    <span class="title">{{ $T('warranty') }}</span>
    <span class="version">v1</span>
  </div>
  <div class="policy-body">{!! $B('warranty') !!}</div>
</div>

{{-- Métodos de pago --}}
<div class="policy-block">
  <div class="policy-subheader">
    <span class="title">{{ $T('payments') }}</span>
    <span class="version">v1</span>
  </div>
  <div class="policy-body">{!! $B('payments') !!}</div>
</div>
