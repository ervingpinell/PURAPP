<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pago Seguro - {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .payment-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .booking-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .booking-summary h3 {
            margin-top: 0;
            color: #333;
        }

        .booking-detail {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .booking-detail:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 1.2em;
            color: #2c5282;
        }

        .btn-pay {
            width: 100%;
            padding: 15px;
            background: #2c5282;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-pay:hover {
            background: #1a365d;
        }

        .btn-pay:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .alignet-logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .secure-badge {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }

        .debug-info {
            margin-top: 30px;
            padding: 20px;
            background: #f0f0f0;
            border-radius: 8px;
            font-size: 12px;
            font-family: monospace;
        }

        .debug-info h4 {
            margin-top: 0;
            color: #666;
        }

        .debug-item {
            margin: 5px 0;
            word-break: break-all;
        }

        .loading-message {
            text-align: center;
            color: #666;
            margin-top: 10px;
            display: none;
        }

        .loading-message.active {
            display: block;
        }

        /* 🔧 FIX: Force visibility of Alignet Iframe if truncated */
        iframe,
        #ADS-IFRAME-CONTAINER-IFRAME {
            background: white !important;
            width: 100% !important;
            min-height: 600px !important;
        }
    </style>
</head>

<body>
    <div class="payment-container">
        <div class="alignet-logo">
            <h2>Pago Seguro</h2>
            <p>Procesado por <strong>Alignet</strong></p>
        </div>

        @if($booking ?? null)
        <div class="booking-summary">
            <h3>Resumen de Reserva</h3>

            <div class="booking-detail">
                <span>Referencia:</span>
                <span><strong>{{ $booking->booking_reference }}</strong></span>
            </div>

            <div class="booking-detail">
                <span>Tour:</span>
                <span>{{ $booking->tour->name ?? 'N/A' }}</span>
            </div>

            <div class="booking-detail">
                <span>Fecha:</span>
                <span>{{ $booking->tour_date ? \Carbon\Carbon::parse($booking->tour_date)->format('d/m/Y') : 'N/A' }}</span>
            </div>

            <div class="booking-detail">
                <span>Pasajeros:</span>
                <span>{{ $booking->pax }}</span>
            </div>

            <div class="booking-detail">
                <span>Total:</span>
                <span>${{ number_format($booking->total, 2) }}</span>
            </div>
        </div>
        @endif

        <!-- Hidden form with all payment data -->
        <form name="alignet_payment_form"
            id="alignet_payment_form"
            action="#"
            method="post"
            class="alignet-form-vpos2">

            @foreach($paymentData as $key => $value)
            @if(!in_array($key, ['base_url', 'vpos2_script']))
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
            @endforeach

            <button type="button"
                id="btn-pay"
                onclick="abrirModalAlignet()"
                class="btn-pay">
                <i class="fas fa-lock"></i> Proceder al Pago
            </button>

            <div class="loading-message" id="loading-message">
                <i class="fas fa-spinner fa-spin"></i> Cargando módulo de pago...
            </div>
        </form>

        <div class="secure-badge">
            <i class="fas fa-shield-alt"></i> Transacción segura y encriptada
        </div>

        @if(config('app.debug'))
        <div class="debug-info">
            <h4>🔍 Debug Info (solo visible en modo debug)</h4>
            <div class="debug-item"><strong>Acquirer ID:</strong> {{ $paymentData['acquirerId'] }}</div>
            <div class="debug-item"><strong>Commerce ID:</strong> {{ $paymentData['idCommerce'] }}</div>
            <div class="debug-item"><strong>Operation Number:</strong> {{ $paymentData['purchaseOperationNumber'] }}</div>
            <div class="debug-item"><strong>Amount (cents):</strong> {{ $paymentData['purchaseAmount'] }}</div>
            <div class="debug-item"><strong>Currency:</strong> {{ $paymentData['purchaseCurrencyCode'] }}</div>
            <div class="debug-item"><strong>Verification Hash:</strong> {{ substr($paymentData['purchaseVerification'], 0, 40) }}...</div>
            <div class="debug-item"><strong>Base URL (openModal):</strong> {{ $paymentData['base_url'] ?? 'N/A' }}</div>
            <div class="debug-item"><strong>Script URL:</strong> {{ $paymentData['vpos2_script'] ?? 'N/A' }}</div>

            <h4 style="margin-top: 20px;">📋 Todos los parámetros:</h4>
            @foreach($paymentData as $key => $value)
            @if(!in_array($key, ['base_url', 'vpos2_script']))
            <div class="debug-item"><strong>{{ $key }}:</strong> {{ $value }}</div>
            @endif
            @endforeach
        </div>
        @endif
    </div>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- 🔥 CRÍTICO: Cargar script de Alignet AL FINAL, después del formulario -->
    <script type="text/javascript" src="{{ $paymentData['vpos2_script'] ?? $paymentData['base_url'] . 'VPOS2/js/modalcomercio.js' }}"></script>

    <script>
        // 🔍 INTERCEPTOR DE DEBUG - Debe ejecutarse AL CARGAR LA PÁGINA
        window.addEventListener('load', function() {
            console.log('🔍 Verificando Alignet VPOS2...');
            console.log('AlignetVPOS2 disponible:', typeof AlignetVPOS2);

            const btnPay = document.getElementById('btn-pay');
            const loadingMsg = document.getElementById('loading-message');
            const form = document.getElementById('alignet_payment_form');

            if (typeof AlignetVPOS2 === 'undefined') {
                console.error('❌ ERROR: El script de Alignet no se cargó correctamente');
                console.error('URL intentada: {{ $paymentData["vpos2_script"] ?? "N/A" }}');

                btnPay.disabled = true;
                btnPay.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error al cargar módulo de pago';

                alert('Error: No se pudo cargar el módulo de pago de Alignet. Por favor recargue la página.');
            } else {
                console.log('✅ Alignet VPOS2 cargado correctamente');
                console.log('📦 Propiedades de AlignetVPOS2:', Object.keys(AlignetVPOS2));

                loadingMsg.classList.remove('active');
                btnPay.disabled = false;

                // Guardar la función original
                const originalOpenModal = AlignetVPOS2.openModal;

                // Sobrescribir para capturar los datos
                AlignetVPOS2.openModal = function(baseUrl) {
                    console.log('🔍 DEBUGGING - Datos enviados a Alignet:');
                    console.log('Base URL:', baseUrl);

                    const formData = new FormData(form);
                    const payload = {};

                    console.log('Parámetros que se enviarán:');
                    formData.forEach((value, key) => {
                        payload[key] = value;
                        console.log(`  ${key}: ${value}`);
                    });

                    // Enviar a backend para logging persistente
                    fetch('/api/debug/alignet-request', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            payload: payload,
                            baseUrl: baseUrl,
                            timestamp: new Date().toISOString()
                        })
                    }).catch(err => console.error('Debug log error:', err));

                    // Llamar a la función original
                    return originalOpenModal.call(this, baseUrl);
                };

                console.log('✅ Interceptor de Alignet activado');
            }
        });

        // Función para abrir el modal con validación
        function abrirModalAlignet() {
            console.log('🚀 Intentando abrir modal de Alignet...');

            const btnPay = document.getElementById('btn-pay');
            const loadingMsg = document.getElementById('loading-message');

            if (typeof AlignetVPOS2 === 'undefined') {
                alert('Error: El módulo de pago no está disponible. Por favor recargue la página.');
                return false;
            }

            try {
                // Deshabilitar botón mientras se procesa
                btnPay.disabled = true;
                loadingMsg.classList.add('active');

                const baseUrl = '{{ $paymentData["base_url"] ?? "" }}';
                console.log('🔓 Abriendo modal con URL base:', baseUrl);

                AlignetVPOS2.openModal(baseUrl);

                console.log('✅ Modal invocado exitosamente');

                // 🔄 BREAKOUT: Polling de estado para evitar congelamiento
                // Si el navegador bloquea la redirección del iframe (X-Frame-Options/Sandbox),
                // nosotros redirigimos manualmente desde aquí apenas detectamos el pago.
                const paymentId = '{{ $paymentId ?? "" }}';
                if (paymentId) {
                    console.log('🔄 Iniciando monitor de estado para pago:', paymentId);
                    const statusInterval = setInterval(function() {
                        fetch('/api/payment/check-status/' + paymentId)
                            .then(res => res.json())
                            .then(data => {
                                console.log('Estado de pago:', data.status);
                                if (data.status === 'paid' && data.redirect_url) {
                                    clearInterval(statusInterval);
                                    console.log('✅ Pago confirmado! Redirigiendo forzosamente...');
                                    loadingMsg.innerHTML = '<i class="fas fa-check-circle"></i> ¡Pago Exitoso! Redirigiendo...';
                                    loadingMsg.classList.add('active');

                                    // Cerrar modal sucio para evitar bloqueos visuales
                                    try {
                                        closeAlignetModal();
                                    } catch (e) {}

                                    window.location.href = data.redirect_url;
                                }
                            })
                            .catch(err => console.error('Status check error:', err));
                    }, 3000); // Verificar cada 3 segundos
                }

                // Re-habilitar botón después de 2 segundos
                setTimeout(() => {
                    btnPay.disabled = false;
                    loadingMsg.classList.remove('active');
                }, 2000);

            } catch (error) {
                console.error('❌ Error al abrir modal:', error);
                alert('Error al abrir el formulario de pago: ' + error.message);

                btnPay.disabled = false;
                loadingMsg.classList.remove('active');
            }
        }

        // Auto-close modal if returning from Alignet (after webhook redirect)
        // This uses localStorage to communicate between the payment page and the redirected page
        document.addEventListener('DOMContentLoaded', function() {
            // Listen for storage events (when another tab/window signals payment completion)
            window.addEventListener('storage', function(e) {
                if (e.key === 'alignet_payment_complete' && e.newValue === 'true') {
                    console.log('🔄 Recibida señal de pago completado, cerrando modal...');
                    closeAlignetModal();
                    // Clear the flag
                    localStorage.removeItem('alignet_payment_complete');
                }
            });

            // Also check on page load (in case we're on the same tab)
            if (localStorage.getItem('alignet_payment_complete') === 'true') {
                console.log('🔄 Detectado pago completado al cargar, cerrando modal...');
                closeAlignetModal();
                localStorage.removeItem('alignet_payment_complete');
            }
        });

        function closeAlignetModal() {
            try {
                // Remove Alignet overlays and iframes
                const alignetIframes = document.querySelectorAll('iframe[src*="alignet"], iframe[src*="VPOS2"]');
                alignetIframes.forEach(iframe => {
                    console.log('Removiendo iframe:', iframe.src);
                    iframe.remove();
                });

                const alignetOverlays = document.querySelectorAll('[id*="alignet"], [class*="alignet"], [id*="vpos2"], [class*="vpos2"], [id*="VPOS"], [class*="modal"]');
                alignetOverlays.forEach(overlay => {
                    if (overlay && overlay.style && overlay.style.display !== 'none') {
                        console.log('Ocultando overlay:', overlay.id || overlay.className);
                        overlay.style.display = 'none';
                        // Don't remove the form itself
                        if (!overlay.matches('form, body, html')) {
                            overlay.remove();
                        }
                    }
                });

                // Remove any backdrop/overlay elements
                document.querySelectorAll('.modal-backdrop, .overlay, [style*="position: fixed"]').forEach(el => {
                    if (el && !el.matches('form, body, html')) {
                        el.remove();
                    }
                });

                console.log('✅ Modal de Alignet cerrado');
            } catch (error) {
                console.warn('⚠️ Error al cerrar modal:', error);
            }
        }
    </script>
</body>

</html>