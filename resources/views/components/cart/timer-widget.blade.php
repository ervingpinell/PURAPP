{{-- Widget flotante de countdown del carrito - AUTOCONTENIDO (no usa @push) --}}
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
'public.checkout.*',
'checkout.show',
'checkout.payment',
'payment.process',
'payment.show',
];

$shouldShow = $hasItems && !request()->routeIs($hideOnRoutes);
@endphp

@if ($shouldShow)
<div id="cart-timer-widget" class="cart-timer-widget" style="display: none;">
  <div class="timer-content">
    <span class="timer-message">{{ __('carts.timer.will_expire') }}</span>
    <span id="widget-timer-full" class="timer-time">--:--</span>
  </div>
</div>

{{-- INLINE STYLES - No depende de @stack --}}
<style>
  .cart-timer-widget {
    position: fixed;
    bottom: 85px;
    right: 20px;
    background: linear-gradient(135deg, #2d7a4f 0%, #1e5a3a 100%);
    color: white;
    padding: 12px 18px;
    border-radius: 25px;
    box-shadow: 0 4px 12px rgba(45, 122, 79, 0.4);
    cursor: pointer;
    z-index: 998;
    transition: all 0.3s ease;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    max-width: 280px;
  }

  .cart-timer-widget .timer-content {
    display: flex;
    flex-direction: column;
    gap: 3px;
    align-items: center;
  }

  .cart-timer-widget .timer-message {
    font-size: 13px;
    font-weight: 500;
    line-height: 1.3;
    text-align: center;
    opacity: 1;
    white-space: nowrap;
  }

  .cart-timer-widget .timer-time {
    font-size: 22px;
    font-weight: 700;
    letter-spacing: 1.5px;
    line-height: 1;
  }

  .cart-timer-widget:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(45, 122, 79, 0.5);
  }

  .cart-timer-widget.warning {
    background: linear-gradient(135deg, #f57c00 0%, #e65100 100%);
    box-shadow: 0 4px 12px rgba(245, 124, 0, 0.4);
    animation: ctw-pulse 2s ease-in-out infinite;
  }

  .cart-timer-widget.critical {
    background: linear-gradient(135deg, #d32f2f 0%, #c62828 100%);
    box-shadow: 0 4px 12px rgba(211, 47, 47, 0.4);
    animation: ctw-shake 0.5s ease-in-out infinite;
  }

  @keyframes ctw-pulse {

    0%,
    100% {
      transform: scale(1);
    }

    50% {
      transform: scale(1.05);
    }
  }

  @keyframes ctw-blink {

    0%,
    100% {
      opacity: 1;
    }

    50% {
      opacity: 0.4;
    }
  }

  @keyframes ctw-shake {

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

  @media (max-width: 575.98px) {
    .cart-timer-widget {
      bottom: 80px;
      right: 15px;
      padding: 11px 16px;
      max-width: 220px;
      border-radius: 30px;
    }

    .cart-timer-widget .timer-message {
      font-size: 12px;
    }

    .cart-timer-widget .timer-time {
      font-size: 20px;
      letter-spacing: 1.2px;
    }
  }
</style>

{{-- INLINE SCRIPT - No depende de @stack, se ejecuta inmediatamente --}}
<script>
  (function() {
    console.log('🔧 [Timer Widget] Script iniciado');

    const widget = document.getElementById('cart-timer-widget');
    const timerFull = document.getElementById('widget-timer-full');

    if (!widget || !timerFull) {
      console.error('🔧 [Timer Widget] ERROR: Elementos no encontrados', {
        widget: !!widget,
        timerFull: !!timerFull
      });
      return;
    }

    console.log('🔧 [Timer Widget] Elementos DOM encontrados ✓');

    widget.addEventListener('click', () => {
      window.location.href = '{{ route("public.carts.index") }}';
    });

    const initWidget = () => {
      console.log('🔧 [Timer Widget] Intentando inicializar, cartCountdown:', !!window.cartCountdown);

      if (!window.cartCountdown || typeof window.cartCountdown.getRemainingSeconds !== 'function') {
        return false;
      }

      const remaining = window.cartCountdown.getRemainingSeconds();
      console.log('🔧 [Timer Widget] Segundos restantes:', remaining);

      const updateWidget = () => {
        const remaining = window.cartCountdown.getRemainingSeconds();

        if (isNaN(remaining)) {
          widget.style.display = 'none';
          return;
        }

        if (widget.style.display === 'none') {
          console.log('🔧 [Timer Widget] Mostrando widget');
          widget.style.display = 'block';
        }

        if (remaining <= 0) {
          timerFull.textContent = '0:00';
          widget.classList.add('critical');
          return;
        }

        const minutes = Math.floor(remaining / 60);
        const seconds = remaining % 60;
        timerFull.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

        widget.classList.remove('warning', 'critical');
        if (remaining <= 300) {
          widget.classList.add('critical');
        } else if (remaining <= 600) {
          widget.classList.add('warning');
        }
      };

      console.log('🔧 [Timer Widget] ¡Inicialización exitosa!');
      setInterval(updateWidget, 1000);
      updateWidget();
      return true;
    };

    if (!initWidget()) {
      console.log('🔧 [Timer Widget] Esperando cartCountdown:ready...');

      window.addEventListener('cartCountdown:ready', () => {
        console.log('🔧 [Timer Widget] Evento recibido');
        initWidget();
      }, {
        once: true
      });

      let attempts = 0;
      const maxAttempts = 50;
      const retryInterval = setInterval(() => {
        attempts++;
        if (initWidget() || attempts >= maxAttempts) {
          clearInterval(retryInterval);
          if (attempts >= maxAttempts && !window.cartCountdown) {
            console.error('🔧 [Timer Widget] TIMEOUT: cartCountdown nunca se inicializó');
            widget.style.display = 'none';
          }
        }
      }, 100);
    }
  })();
</script>
@endif