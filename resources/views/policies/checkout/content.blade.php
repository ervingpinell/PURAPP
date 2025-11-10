@php
  /**
   * Estructura esperada (DB-first):
   * $policyBlocks = [
   *   ['key'=>'terms',        'title'=>'...', 'version'=>'vX', 'html'=>'<p>...</p>'],
   *   ['key'=>'privacy',      'title'=>'...', 'version'=>'vX', 'html'=>'<p>...</p>'],
   *   ['key'=>'cancellation', 'title'=>'...', 'version'=>'vX', 'html'=>'<p>...</p>'],
   *   ['key'=>'refunds',      'title'=>'...', 'version'=>'vX', 'html'=>'<p>...</p>'],
   *   ['key'=>'warranty',     'title'=>'...', 'version'=>'vX', 'html'=>'<p>...</p>'],
   *   ['key'=>'payments',     'title'=>'...', 'version'=>'vX', 'html'=>'<p>...</p>'],
   * ];
   */

  // Helpers de fallback a archivos de traducción
  $T = fn($k) => __('policies.checkout.titles.' . $k);
  $V = fn($k) => __('policies.checkout.version.' . $k);
  $B = fn($k) => __('policies.checkout.bodies.' . $k . '_html');

  // Si NO llega $policyBlocks, construimos con las claves actuales (archivos)
  if (empty($policyBlocks) || !is_array($policyBlocks)) {
    $policyBlocks = [
      ['key'=>'terms',        'title'=> $T('terms'),        'version'=> $V('terms'),        'html'=> $B('terms')],
      ['key'=>'privacy',      'title'=> $T('privacy'),      'version'=> $V('privacy'),      'html'=> $B('privacy')],
      ['key'=>'cancellation', 'title'=> $T('cancellation'), 'version'=> 'v1',               'html'=> $B('cancellation')],
      ['key'=>'refunds',      'title'=> $T('refunds'),      'version'=> 'v1',               'html'=> $B('refunds')],
      ['key'=>'warranty',     'title'=> $T('warranty'),     'version'=> 'v1',               'html'=> $B('warranty')],
      ['key'=>'payments',     'title'=> $T('payments'),     'version'=> 'v1',               'html'=> $B('payments')],
    ];
  } else {
    // Si sí llega $policyBlocks desde BD, permite override de versions para términos/privacidad
    if (!empty($termsVersion)) {
      foreach ($policyBlocks as &$blk) if (($blk['key'] ?? null) === 'terms') $blk['version'] = $termsVersion;
      unset($blk);
    }
    if (!empty($privacyVersion)) {
      foreach ($policyBlocks as &$blk) if (($blk['key'] ?? null) === 'privacy') $blk['version'] = $privacyVersion;
      unset($blk);
    }
  }
@endphp

<style>
  .policy-block{margin-bottom:1.25rem;border:1px solid var(--g200);border-radius:.75rem;overflow:hidden;background:#fff}
  .policy-subheader{display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.75rem 1rem;background:var(--g50);border-bottom:1px solid var(--g200)}
  .policy-subheader .title{font-weight:700;color:var(--g800)}
  .policy-subheader .version{font-size:.75rem;color:var(--g600);background:#fff;padding:.15rem .6rem;border:1px solid var(--g300);border-radius:.5rem;white-space:nowrap}
  .policy-body{padding:1rem 1.25rem;line-height:1.75;color:var(--g700)}
  .policy-body ul{margin:0 0 1rem 1.25rem}
  .policy-body li{margin:.35rem 0}
</style>

@foreach($policyBlocks as $block)
  <div class="policy-block">
    <div class="policy-subheader">
      <span class="title">{{ $block['title'] ?? '' }}</span>
      <span class="version">{{ $block['version'] ?? 'v1' }}</span>
    </div>
    <div class="policy-body">
      {!! $block['html'] ?? '' !!}
    </div>
  </div>
@endforeach
