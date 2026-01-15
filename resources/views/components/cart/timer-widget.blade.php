{{-- Widget flotante de countdown del carrito --}}
@php
// Check both authenticated and guest users
$hasItems = false;
$isGuest = !Auth::check();

if ($isGuest) {
// Guest user - check session cart
$sessionCartItems = session('guest_cart_items', []);
$createdAt = session('guest_cart_created_at');
$hasItems = !empty($sessionCartItems) && $createdAt;
} else {
// User->cart() es hasMany. Obtenemos el último activo:
$cart = auth()->user()->cart()->where('is_active', true)->latest('cart_id')->first();
$hasItems = $cart && $cart->items()->count() > 0;
}

// Ocultar en páginas donde ya hay timer visible
$hideOnRoutes = [
'public.carts.index',
// checkout + payment público (cubre show, payment, confirmation, etc.)
'public.checkout.*',
// nombres legacy por si acaso
'checkout.show',
'checkout.payment',
'payment.process',
'payment.show', // Fix: Hide on payment page to prevent double alert
];

$shouldShow = $hasItems && !request()->routeIs($hideOnRoutes);
@endphp

@if ($shouldShow)
<div id="cart-timer-widget" class="cart-timer-widget">
  <div class="timer-text">
    <small>{{ __('carts.timer.will_expire') }}</small>
    <strong id="widget-timer-full">--:--</strong>
  </div>
</div>

@push('styles')
<style>
  .timer-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
    text-align: center;
  }

  .timer-text small {
    font-size: 11px;
    opacity: 0.9;
    line-height: 1.2;
  }

  .timer-text strong {
    font-size: 16px;
    letter-spacing: 0.5px;
    line-height: 1.2;
  }

  /* Animaciones existentes */
  @keyframes pulse-warning {

    0%,
    100% {
      transform: scale(1);
    }

    50% {
      transform: scale(1.05);
    }
  }

  @keyframes blink {

    0%,
    100% {
      opacity: 1;
    }

    50% {
      opacity: 0.4;
    }
  }

  @keyframes shake {

    0%,
    100% {
      transform: translateX(0);
    }

    25% {
      transform: translateX(-3px);
    }

    75% {
      transform: translateX(3px);
    }
  }

  /* Base styles for cart timer widget */
  .cart-timer-widget {
    position: fixed;
    bottom: 90px;
    left: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.75rem 1.25rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    cursor: pointer;
    z-index: 999;
    transition: all 0.3s ease;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  }

  .cart-timer-widget:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
  }

  .cart-timer-widget.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    animation: pulse-warning 2s ease-in-out infinite;
  }

  .cart-timer-widget.critical {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    animation: shake 0.5s ease-in-out infinite, blink 1s ease-in-out infinite;
  }

  /* Versión responsive (móviles) */
  @media (max-width: 575.98px) {
    .cart-timer-widget {
      left: 50%;
      right: auto;
      transform: translateX(-50%);
      bottom: 70px;
      width: calc(100% - 2.5rem);
      padding: 0.65rem 0.9rem;
      font-size: 0.8rem;
      border-radius: 999px;
    }

    .timer-text strong {
      font-size: 14px;
    }
  }
</style>
@endpush

@push('scripts')
<script>
  (function() {
    const widget = document.getElementById('cart-timer-widget');
    const timerFull = document.getElementById('widget-timer-full');

    if (!widget || !timerFull) return;

    // Click para ir al carrito
    widget.addEventListener('click', () => {
      window.location.href = '{{ route("public.carts.index") }}';
    });

    // Función para inicializar el widget
    const initWidget = () => {
      if (!window.cartCountdown) {
        return false;
      }

      const updateWidget = () => {
        const remaining = window.cartCountdown.getRemainingSeconds();

        if (remaining <= 0) {
          // Don't hide immediately, show expired state
          timerFull.textContent = '0:00';
          widget.classList.add('critical');
          return;
        }

        const minutes = Math.floor(remaining / 60);
        const seconds = remaining % 60;
        const timeStr = `${minutes}:${seconds.toString().padStart(2, '0')}`;

        // Solo mostramos el tiempo grande
        timerFull.textContent = timeStr;

        // Cambiar estilo según tiempo restante
        widget.classList.remove('warning', 'critical');

        if (remaining <= 300) { // <= 5 minutos
          widget.classList.add('critical');
        } else if (remaining <= 600) { // <= 10 minutos
          widget.classList.add('warning');
        }
      };

      // Actualizar cada segundo
      setInterval(updateWidget, 1000);
      updateWidget();
      return true;
    };

    // Intentar inicializar inmediatamente
    if (!initWidget()) {
      // Listen for cartCountdown:ready event
      window.addEventListener('cartCountdown:ready', () => {
        initWidget();
      }, {
        once: true
      });

      // Fallback: reintentar cada 100ms hasta 5 segundos (por si el evento ya pasó)
      let attempts = 0;
      const maxAttempts = 50; // 5 segundos
      const retryInterval = setInterval(() => {
        attempts++;
        if (initWidget() || attempts >= maxAttempts) {
          clearInterval(retryInterval);
          if (attempts >= maxAttempts && !window.cartCountdown) {
            console.warn('Cart countdown system not found after 5 seconds');
            widget.style.display = 'none';
          }
        }
      }, 100);
    }
  })();
</script>
@endpush
@endif