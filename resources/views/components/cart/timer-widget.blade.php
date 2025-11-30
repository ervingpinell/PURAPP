{{-- Widget flotante de countdown del carrito --}}
@auth
@php
  $cart = auth()->user()->cart;
  $hasItems = $cart && $cart->items()->count() > 0;

  // Ocultar en páginas donde ya hay timer visible
  $hideOnRoutes = [
    'public.carts.index',
    // checkout + payment público (cubre show, payment, confirmation, etc.)
    'public.checkout.*',
    // nombres legacy por si acaso
    'checkout.show',
    'checkout.payment',
    'payment.process',
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
        0%, 100% {
          transform: scale(1);
        }
        50% {
          transform: scale(1.05);
        }
      }

      @keyframes blink {
        0%, 100% {
          opacity: 1;
        }
        50% {
          opacity: 0.4;
        }
      }

      @keyframes shake {
        0%, 100% {
          transform: translateX(0);
        }
        25% {
          transform: translateX(-3px);
        }
        75% {
          transform: translateX(3px);
        }
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
      (function () {
        const widget = document.getElementById('cart-timer-widget');
        const timerFull = document.getElementById('widget-timer-full');

        if (!widget || !timerFull) return;

        // Click para ir al carrito
        widget.addEventListener('click', () => {
          window.location.href = '{{ route('public.carts.index') }}';
        });

        // Conectar con el sistema de countdown existente
        if (window.cartCountdown) {
          const updateWidget = () => {
            const remaining = window.cartCountdown.getRemainingSeconds();

            if (remaining <= 0) {
              widget.style.display = 'none';
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
        } else {
          // Fallback si no existe el countdown
          console.warn('Cart countdown system not found');
          widget.style.display = 'none';
        }
      })();
    </script>
  @endpush
@endif
@endauth
