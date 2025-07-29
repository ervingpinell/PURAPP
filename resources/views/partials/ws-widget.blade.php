<!-- Widget flotante de WhatsApp -->
@php
  $isContactPage = request()->routeIs('contact');
@endphp

<div x-data="{ isOpen: false, message: '' }" x-cloak class="whatsapp-widget z-50 position-fixed bottom-0 end-0 m-3">

    <!-- Panel del chat -->
    <div x-show="isOpen" x-transition class="whatsapp-panel bg-white border rounded shadow" style="width: 300px;">
        <div class="whatsapp-header bg-success text-white d-flex justify-content-between align-items-center px-3 py-2">
            <div class="d-flex align-items-center gap-2">
                <div class="whatsapp-icon-circle bg-white text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                    <i class="fas fa-comment-dots fa-sm"></i>
                </div>
                <div>
                    <strong>{{ __('adminlte::adminlte.whatsapp_title') }}</strong><br>
                    <small>{{ __('adminlte::adminlte.whatsapp_subtitle') }}</small>
                </div>
            </div>
            <button class="btn btn-sm text-white" @click="isOpen = false">
                <i class="fas fa-times"></i>
            </button>
        </div>

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
                @click="
                    const defaultMsg = @json(__('adminlte::adminlte.whatsapp_placeholder'));
                    window.open(`https://wa.me/50624791471?text=${encodeURIComponent(message || defaultMsg)}`, '_blank');
                    isOpen = false;
                    message = '';
                "
                class="btn btn-success w-100"
            >
                <i class="fas fa-paper-plane me-2"></i>{{ __('adminlte::adminlte.whatsapp_button') }}
            </button>

            <p class="text-center text-muted mt-2 mb-0 whatsapp-footer-note small">
                {{ __('adminlte::adminlte.whatsapp_footer') }}
            </p>
        </div>
    </div>

    <!-- Botón flotante -->
    <button
        @click="isOpen = !isOpen"
        class="whatsapp-float-btn btn btn-success rounded-circle shadow-lg d-flex align-items-center justify-content-center
               {{ $isContactPage ? 'animate-pulse-grow' : '' }}"
        style="width: 60px; height: 60px;"
    >
        <template x-if="isOpen">
            <i class="fas fa-times fa-lg"></i>
        </template>
        <template x-if="!isOpen">
            <i class="fab fa-whatsapp fa-lg"></i>
        </template>
    </button>
</div>
