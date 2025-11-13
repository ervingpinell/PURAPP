@php
  /**
   * Ahora este partial asume que SIEMPRE viene $policyBlocks
   * desde el controlador, construido desde BD con buildPolicyBlocksFromDB().
   *
   * Estructura:
   * $policyBlocks = [
   *   ['key'=>'terms',        'title'=>'...', 'version'=>'vX', 'html'=>'<p>...</p>'],
   *   ['key'=>'privacy',      'title'=>'...', 'version'=>'vX', 'html'=>'<p>...</p>'],
   *   ['key'=>'cancellation', 'title'=>'...', 'version'=>'vX', 'html'=>'<p>...</p>'],
   *   ['key'=>'refunds',      'title'=>'...', 'version'=>'vX', 'html'=>'<p>...</p>'],
   *   ['key'=>'warranty',     'title'=>'...', 'version'=>'vX', 'html'=>'<p>...</p>'],
   *   ['key'=>'payments',     'title'=>'...', 'version'=>'vX', 'html'=>'<p>...</p>'],
   *   // ...cualquier otra policy activa
   * ];
   */

  $policyBlocks = collect($policyBlocks ?? [])
      ->filter(fn ($b) => !empty($b['html']))
      ->values()
      ->all();
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

@if(empty($policyBlocks))
  {{-- Si por alguna razón no hay nada en BD, mostramos un mensajito en vez de hardcodear contenido --}}
  <p class="text-muted small mb-0">
    {{ __('m_checkout.panels.no_policies_configured') }}
  </p>
@else
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
@endif
