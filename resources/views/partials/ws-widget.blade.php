@php
  // Params (defaults)
  $variant        = $variant        ?? 'floating'; // 'floating' | 'inline'
  $phone          = preg_replace('/\D+/', '', $phone ?? '50624791471');
  $defaultMsg     = $defaultMsg     ?? __('adminlte::adminlte.whatsapp_placeholder');
  $buttonClass    = $buttonClass    ?? 'btn btn-success';
  $containerClass = $containerClass ?? '';
  $widgetId       = $widgetId       ?? 'wsw_'.Str::random(6);
@endphp

<div
  x-data="{ isOpen: {{ $variant === 'inline' ? 'true' : 'false' }}, message: '' }"
  x-cloak
  id="{{ $widgetId }}"
  class="{{ $containerClass }}
         @if($variant === 'floating') whatsapp-widget z-50 position-fixed bottom-0 end-0 m-3 @endif">

  {{-- Panel --}}
  <div
    @if($variant === 'floating')
      x-show="isOpen" x-transition
      class="whatsapp-panel bg-white border rounded shadow"
      style="width: 300px;"
    @else
      class="whatsapp-inline bg-white border rounded shadow p-0"
    @endif
  >
    {{-- Header --}}
    <div class="whatsapp-header bg-success text-white d-flex justify-content-between align-items-center px-3 py-2 rounded-top">
      <div class="d-flex align-items-center gap-2">
<div class="whatsapp-icon-circle bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
  <i class="fab fa-whatsapp" style="font-size: 22px; color: #25D366;"></i>
</div>

        <div>
          <strong>{{ __('adminlte::adminlte.whatsapp_title') }} WhatsApp</strong><br>
          <small>{{ __('adminlte::adminlte.whatsapp_subtitle') }}</small>
        </div>
      </div>

      {{-- Close button (floating --}}
      @if($variant === 'floating')
        <button type="button" class="btn btn-sm text-white" @click="isOpen = false">
          <i class="fas fa-times"></i>
        </button>
      @endif
    </div>

    {{-- Body --}}
    <div class="p-3">
      <div class="whatsapp-greeting mb-2">
        <small>{{ __('adminlte::adminlte.whatsapp_greeting') }}</small>
      </div>

      <textarea
        x-model="message"
        class="form-control mb-2"
        rows="3"
        placeholder="{{ __('adminlte::adminlte.whatsapp_placeholder') }}"
      ></textarea>

      <button
        type="button"
        @click="
          const def = @js($defaultMsg);
          window.open(`https://wa.me/{{ $phone }}?text=${encodeURIComponent(message || def)}`, '_blank');
          isOpen = {{ $variant === 'floating' ? 'false' : 'true' }};
          message = '';
        "
        class="{{ $buttonClass }} w-100"
      >
        <i class="fas fa-paper-plane me-2"></i>{{ __('adminlte::adminlte.whatsapp_button') }}
      </button>

      <div class="whatsapp-attention-schedule mb-2 text-center">
        <small><i class="fas fa-clock"></i> {{ __('adminlte::adminlte.whatsapp_attention_schedule') }}</small>
      </div>

      <div class="whatsapp-attention-language text-success mb-2 text-center">
        <small>{{ __('adminlte::adminlte.whatsapp_attention_language') }}</small>
      </div>

      <div class="whatsapp-footer-note text-muted mb-2 text-center">
        <small>{{ __('adminlte::adminlte.whatsapp_footer') }}</small>
      </div>
    </div>
  </div>

  {{-- Floating button --}}
  @if($variant === 'floating')
    <button
      type="button"
      @click="isOpen = !isOpen"
      class="whatsapp-float-btn btn btn-success rounded-circle shadow-lg d-flex align-items-center justify-content-center"
      style="width: 60px; height: 60px;"
      aria-label="Abrir WhatsApp"
    >
      <template x-if="isOpen">
        <i class="fas fa-times fa-lg"></i>
      </template>
      <template x-if="!isOpen">
        <i class="fab fa-whatsapp fa-lg"></i>
      </template>
    </button>
  @endif
</div>
