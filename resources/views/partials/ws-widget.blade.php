@php
use Illuminate\Support\Str;

$variant = $variant ?? 'floating'; // 'floating' | 'inline'
$phone = preg_replace('/\D+/', '', $phone ?? config('company.phone_raw'));
$defaultMsg = $defaultMsg ?? __('adminlte::adminlte.whatsapp_placeholder');
$buttonClass = $buttonClass ?? 'btn btn-success';
$containerClass = $containerClass ?? '';
$widgetId = $widgetId ?? 'wsw_'.Str::random(6);

$startsOpen = false; // Widget inicia cerrado
@endphp

<div
  id="{{ $widgetId }}"
  x-data="{ isOpen: @json($startsOpen), message: '' }"
  x-cloak
  class="{{ $containerClass }} @if($variant === 'floating') whatsapp-widget z-50 position-fixed bottom-0 end-0 m-3 @endif">
  {{-- Panel --}}
  <div
    @if($variant==='floating' )
    x-show="isOpen"
    x-transition
    class="whatsapp-panel bg-white border rounded shadow"
    style="width:300px;"
    @else
    class="whatsapp-inline bg-white border rounded shadow p-0"
    @endif
    role="dialog" aria-modal="false" aria-label="WhatsApp">
    {{-- Header --}}
    <div class="whatsapp-header bg-success text-white d-flex justify-content-between align-items-center px-3 py-2 rounded-top">
      <div class="d-flex align-items-center gap-2">
        <div class="whatsapp-icon-circle bg-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
          <i class="fab fa-whatsapp" style="font-size:22px;color:#25D366;"></i>
        </div>
        <div>
          <strong>{{ __('adminlte::adminlte.whatsapp_title') }} WhatsApp</strong><br>
          <small>{{ __('adminlte::adminlte.whatsapp_subtitle') }}</small>
        </div>
      </div>

      @if($variant === 'floating')
      <button type="button" class="btn btn-sm text-white" @click="isOpen = false" aria-label="Cerrar">
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
        placeholder="{{ __('adminlte::adminlte.whatsapp_placeholder') }}"></textarea>

      <button
        type="button"
        class="{{ $buttonClass }} w-100"
        @click="
          const def = @js($defaultMsg);
          const text = encodeURIComponent(message || def);
          try {
            if (!navigator.onLine) throw new Error('offline');
            window.open(`https://wa.me/{{ $phone }}?text=${text}`, '_blank');
          } catch (e) {
            alert('{{ __('adminlte::adminlte.whatsapp_offline_notice') ?? 'Parece que no tienes conexión. Intenta de nuevo cuando vuelvas a estar en línea.' }}');
          } finally {
            isOpen = @json($variant === 'inline' ? true : false);
            message = '';
          }
        ">
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

  {{-- Botón flotante --}}
  @if($variant === 'floating')
  <button
    type="button"
    class="whatsapp-float-btn btn btn-success rounded-circle shadow-lg d-flex align-items-center justify-content-center"
    style="width:60px;height:60px;"
    aria-label="Abrir WhatsApp"
    @click="isOpen = !isOpen">
    <template x-if="isOpen">
      <i class="fas fa-times fa-lg"></i>
    </template>
    <template x-if="!isOpen">
      <i class="fab fa-whatsapp fa-lg"></i>
    </template>
  </button>
  @endif

  {{-- Fallback sin JS: link directo (no visible si Alpine corre por x-cloak) --}}
  <noscript>
    <a class="btn btn-success" href="https://wa.me/{{ $phone }}" target="_blank" rel="noopener">WhatsApp</a>
  </noscript>
</div>