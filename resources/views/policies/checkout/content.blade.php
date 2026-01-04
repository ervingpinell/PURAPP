@php
/**
* Ahora este partial asume que SIEMPRE viene $policyBlocks
* desde el controlador, construido desde BD con buildPolicyBlocksFromDB().
*/

$policyBlocks = collect($policyBlocks ?? [])
->filter(fn ($b) => !empty($b['html']))
->values()
->all();

/**
* Helper para procesar contenido: si ya tiene HTML lo deja, si no aplica nl2br
*/
$processContent = function($html) {
// Si ya tiene etiquetas HTML significativas, dejarlo como está
if (preg_match('/<(p|div|ul|ol|li|h[1-6]|br)\s*[^>]*>/i', $html)) {
  return $html;
  }
  // Si no, escapar y convertir saltos de línea
  return nl2br(e($html));
  };
  @endphp

  <style>
    .policy-block {
      margin-bottom: 1.25rem;
      border: 1px solid var(--g200);
      border-radius: .75rem;
      overflow: hidden;
      background: #fff
    }

    .policy-subheader {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: .75rem;
      padding: .75rem 1rem;
      background: var(--g50);
      border-bottom: 1px solid var(--g200)
    }

    .policy-subheader .title {
      font-weight: 700;
      color: var(--g800)
    }

    .policy-subheader .version {
      font-size: .75rem;
      color: var(--g600);
      background: #fff;
      padding: .15rem .6rem;
      border: 1px solid var(--g300);
      border-radius: .5rem;
      white-space: nowrap
    }

    .policy-body {
      padding: 1rem 1.25rem;
      line-height: 1.75;
      color: var(--g700)
    }

    .policy-body ul {
      margin: 0 0 1rem 1.25rem
    }

    .policy-body li {
      margin: .35rem 0
    }

    .policy-body h4 {
      font-size: 1.1rem;
      font-weight: 600;
      margin: 1.5rem 0 0.75rem;
      color: var(--g900)
    }

    .policy-body p {
      margin: 0.5rem 0
    }
  </style>

  @if(empty($policyBlocks))
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
      {{-- Procesar contenido inteligentemente --}}
      {!! $processContent($block['html'] ?? '') !!}
    </div>
  </div>
  @endforeach
  @endif