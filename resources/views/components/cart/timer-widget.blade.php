{{-- Widget flotante de countdown del carrito --}}
@auth
@php
    $cart     = auth()->user()->cart;
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
    <div class="timer-icon">
        <i class="fas fa-shopping-cart"></i>
        <span class="timer-badge" id="widget-timer">--:--</span>
    </div>
    <div class="timer-text">
        <small>Tu carrito expira en</small>
        <strong id="widget-timer-full">--:--</strong>
    </div>
</div>

@push('styles')
<style>
    .cart-timer-widget {
        position: fixed;
        bottom: 80px;
        right: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 16px;
        border-radius: 50px;
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 9998;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .cart-timer-widget:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(76, 81, 191, 0.55);
    }

    .cart-timer-widget.warning {
        background: linear-gradient(135deg, #fbbf24 0%, #f97316 100%);
        animation: pulse-warning 1s infinite;
    }

    .cart-timer-widget.critical {
        background: linear-gradient(135deg, #ff6b6b 0%, #c92a2a 100%);
        animation: shake 0.5s infinite;
    }

    .timer-icon {
        position: relative;
        font-size: 24px;
    }

    .timer-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: rgba(255, 255, 255, 0.95);
        color: #667eea;
        font-size: 10px;
        font-weight: bold;
        padding: 2px 6px;
        border-radius: 10px;
        min-width: 30px;
        text-align: center;
    }

    .cart-timer-widget.warning .timer-badge {
        color: #f5576c;
    }

    .cart-timer-widget.critical .timer-badge {
        color: #c92a2a;
        animation: blink 0.5s infinite;
    }

    .timer-text {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .timer-text small {
        font-size: 11px;
        opacity: 0.9;
    }

    .timer-text strong {
        font-size: 16px;
        letter-spacing: 0.5px;
    }

    @keyframes pulse-warning {
        0%, 100% { transform: scale(1); }
        50%      { transform: scale(1.05); }
    }

    @keyframes blink {
        0%, 100% { opacity: 1; }
        50%      { opacity: 0.4; }
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25%      { transform: translateX(-3px); }
        75%      { transform: translateX(3px); }
    }

    @media (max-width: 575.98px) {
        .cart-timer-widget {
            bottom: 70px;
            right: 10px;
            padding: 10px 14px;
        }

        .timer-icon {
            font-size: 20px;
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
        const widget     = document.getElementById('cart-timer-widget');
        const timerBadge = document.getElementById('widget-timer');
        const timerFull  = document.getElementById('widget-timer-full');

        if (!widget || !timerBadge || !timerFull) return;

        // Click para ir al carrito
        widget.addEventListener('click', () => {
            window.location.href = '{{ route("public.carts.index") }}';
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

                timerBadge.textContent = timeStr;
                timerFull.textContent  = timeStr;

                // Cambiar estilo según tiempo restante
                widget.classList.remove('warning', 'critical');

                if (remaining <= 300) {          // <= 5 minutos
                    widget.classList.add('critical');
                } else if (remaining <= 600) {   // <= 10 minutos
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
