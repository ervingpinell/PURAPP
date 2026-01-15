<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('payment.alignet.page_title') }} - {{ config('app.name') }}</title>
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
            <h2>{{ __('payment.alignet.page_title') }}</h2>
            <p>{{ __('payment.alignet.processed_by') }} <strong>Alignet</strong></p>
        </div>
        @if($booking ?? null)
        <div class="booking-summary">
            <h3>{{ __('payment.alignet.booking_summary') }}</h3>
            <div class="booking-detail"><span>{{ __('payment.alignet.reference') }}:</span><span><strong>{{ $booking->booking_reference }}</strong></span></div>
            <div class="booking-detail"><span>{{ __('payment.alignet.tour') }}:</span><span>{{ $booking->tour->name ?? 'N/A' }}</span></div>
            <div class="booking-detail"><span>{{ __('payment.alignet.date') }}:</span><span>{{ $booking->tour_date ? \Carbon\Carbon::parse($booking->tour_date)->format('d/m/Y') : 'N/A' }}</span></div>
            <div class="booking-detail"><span>{{ __('payment.alignet.passengers') }}:</span><span>{{ $booking->pax }}</span></div>
            <div class="booking-detail"><span>{{ __('payment.alignet.total') }}:</span><span>${{ number_format($booking->total, 2) }}</span></div>
        </div>
        @endif
        <form name="alignet_payment_form" id="alignet_payment_form" action="#" method="post" class="alignet-form-vpos2">
            @foreach($paymentData as $key => $value)
            @if(!in_array($key, ['base_url', 'vpos2_script']))
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
            @endforeach
            <button type="button" id="btn-pay" onclick="abrirModalAlignet()" class="btn-pay"><i class="fas fa-lock"></i> {{ __('payment.alignet.proceed_payment') }}</button>
            <div class="loading-message" id="loading-message"><i class="fas fa-spinner fa-spin"></i> {{ __('payment.alignet.loading_module') }}</div>
        </form>
        <div class="secure-badge"><i class="fas fa-shield-alt"></i> {{ __('payment.alignet.secure_transaction') }}</div>
        @if(config('app.debug') && !app()->isProduction())
        <div class="debug-info">
            <h4> Debug Info (solo visible en modo debug)</h4>
            <div class="debug-item"><strong>Acquirer ID:</strong> {{ $paymentData['acquirerId'] ?? '' }}</div>
            <div class="debug-item"><strong>Commerce ID:</strong> {{ $paymentData['idCommerce'] ?? '' }}</div>
            <div class="debug-item"><strong>Operation Number:</strong> {{ $paymentData['purchaseOperationNumber'] ?? '' }}</div>
            <div class="debug-item"><strong>Amount (cents):</strong> {{ $paymentData['purchaseAmount'] ?? '' }}</div>
            <div class="debug-item"><strong>Currency:</strong> {{ $paymentData['purchaseCurrencyCode'] ?? '' }}</div>
            <div class="debug-item"><strong>Verification Hash:</strong> {{ isset($paymentData['purchaseVerification']) ? substr($paymentData['purchaseVerification'], 0, 40) . '...' : '' }}</div>
            <div class="debug-item"><strong>Base URL (openModal):</strong> {{ $paymentData['base_url'] ?? '' }}</div>
            <div class="debug-item"><strong>Script URL:</strong> {{ $paymentData['vpos2_script'] ?? '' }}</div>
            <h4 style="margin-top: 20px;">📋 Todos los parámetros:</h4>
            @foreach($paymentData as $key => $value)
            @if(!in_array($key, ['base_url', 'vpos2_script']))
            <div class="debug-item"><strong>{{ $key }}:</strong> {{ $value }}</div>
            @endif
            @endforeach
        </div>
        @endif
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script type="text/javascript" src="{{ $paymentData['vpos2_script'] ?? $paymentData['base_url'] . 'VPOS2/js/modalcomercio.js' }}"></script>
    <script>
        window.addEventListener('load', function() {
            @if(config('app.debug') && !app() - > isProduction())
            console.log('Verificando Alignet VPOS2...');
            @endif
            const btnPay = document.getElementById('btn-pay');
            const loadingMsg = document.getElementById('loading-message');
            const form = document.getElementById('alignet_payment_form');

            if (typeof AlignetVPOS2 === 'undefined') {
                btnPay.disabled = true;
                btnPay.innerHTML = '<i class="fas fa-exclamation-triangle"></i> {{ __("payment.alignet.error_loading") }}';
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("payment.alignet.error_config") }}',
                    text: '{{ __("payment.alignet.error_payment_system") }}',
                    confirmButtonText: '{{ __("payment.alignet.reload") }}'
                }).then(() => {
                    window.location.reload();
                });
                return;
            }

            loadingMsg.classList.remove('active');
            btnPay.disabled = false;
            const originalOpenModal = AlignetVPOS2.openModal;
            AlignetVPOS2.openModal = function(baseUrl) {
                const formData = new FormData(form);
                const payload = {};
                formData.forEach((value, key) => {
                    payload[key] = value;
                });
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
                return originalOpenModal.call(this, baseUrl);
            };

            AlignetVPOS2.setResponseHandler(function(response) {
                const params = new URLSearchParams({
                    acquirerId: '{{ $paymentData["acquirerId"] ?? "" }}',
                    idCommerce: '{{ $paymentData["idCommerce"] ?? "" }}',
                    purchaseOperationNumber: '{{ $paymentData["purchaseOperationNumber"] ?? "" }}',
                    purchaseAmount: '{{ $paymentData["purchaseAmount"] ?? "" }}',
                    purchaseCurrencyCode: '{{ $paymentData["purchaseCurrencyCode"] ?? "" }}',
                    language: '{{ $paymentData["language"] ?? "" }}',
                    shippingFirstName: '{{ $paymentData["shippingFirstName"] ?? "" }}',
                    shippingLastName: '{{ $paymentData["shippingLastName"] ?? "" }}',
                    shippingEmail: '{{ $paymentData["shippingEmail"] ?? "" }}',
                    shippingAddress: '{{ $paymentData["shippingAddress"] ?? "" }}',
                    shippingZIP: '{{ $paymentData["shippingZIP"] ?? "" }}',
                    shippingCity: '{{ $paymentData["shippingCity"] ?? "" }}',
                    shippingState: '{{ $paymentData["shippingState"] ?? "" }}',
                    shippingCountry: '{{ $paymentData["shippingCountry"] ?? "" }}',
                    userCommerce: '{{ $paymentData["userCommerce"] ?? "" }}',
                    userCodePayme: '{{ $paymentData["userCodePayme"] ?? "" }}',
                    descriptionProducts: '{{ $paymentData["descriptionProducts"] ?? "" }}',
                    programmingLanguage: '{{ $paymentData["programmingLanguage"] ?? "" }}',
                    reserved1: '{{ $paymentData["reserved1"] ?? "" }}',
                    reserved2: '{{ $paymentData["reserved2"] ?? "" }}',
                    reserved3: '{{ $paymentData["reserved3"] ?? "" }}',
                    purchaseVerification: '{{ $paymentData["purchaseVerification"] ?? "" }}'
                });
                window.location.href = response.url + '?' + params.toString();
            });
        });

        function abrirModalAlignet() {
            const btnPay = document.getElementById('btn-pay');
            const loadingMsg = document.getElementById('loading-message');
            if (typeof AlignetVPOS2 === 'undefined') {
                alert('Error: El módulo de pago no está disponible.');
                return false;
            }
            try {
                btnPay.disabled = true;
                loadingMsg.classList.add('active');
                const baseUrl = '{{ $paymentData["base_url"] ?? "" }}';
                AlignetVPOS2.openModal(baseUrl);
                const paymentId = '{{ $paymentId ?? "" }}';
                if (paymentId) {
                    const statusInterval = setInterval(function() {
                        fetch('/api/payment/check-status/' + paymentId).then(res => res.json()).then(data => {
                            if (data.status === 'paid' && data.redirect_url) {
                                clearInterval(statusInterval);
                                LoadingMsg.innerHTML = '<i class="fas fa-check-circle"></i> ¡Pago Exitoso!';
                                try {
                                    closeAlignetModal();
                                } catch (e) {}
                                window.location.href = data.redirect_url;
                            } else if (['failed', 'rejected', 'cancelled'].includes(data.status)) {
                                clearInterval(statusInterval);
                                try {
                                    closeAlignetModal();
                                } catch (e) {}
                                window.location.href = "{{ route('public.carts.index') }}?error=El proceso de pago fue cancelado.";
                            }
                        }).catch(err => console.error('Status check error:', err));
                    }, 3000);
                }
                setTimeout(() => {
                    btnPay.disabled = false;
                    loadingMsg.classList.remove('active');
                }, 2000);
            } catch (error) {
                console.error('ERROR Error al abrir modal:', error);
                alert('Error al abrir el formulario: ' + error.message);
                btnPay.disabled = false;
                loadingMsg.classList.remove('active');
            }
        }
        const PAYMENT_TIMEOUT_MS = 300000;
        setTimeout(() => {
            const loadingMsg = document.getElementById('loading-message');
            if (loadingMsg) {
                loadingMsg.innerHTML = '<i class="fas fa-history"></i> Tiempo agotado.';
                loadingMsg.classList.add('active');
            }
            alert('El tiempo de espera ha expirado.');
        }, PAYMENT_TIMEOUT_MS);

        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('storage', function(e) {
                if (e.key === 'alignet_payment_complete' && e.newValue === 'true') {
                    closeAlignetModal();
                    localStorage.removeItem('alignet_payment_complete');
                }
            });
            if (localStorage.getItem('alignet_payment_complete') === 'true') {
                closeAlignetModal();
                localStorage.removeItem('alignet_payment_complete');
            }
        });

        function closeAlignetModal() {
            try {
                document.querySelectorAll('iframe[src*="alignet"], iframe[src*="VPOS2"]').forEach(iframe => iframe.remove());
                document.querySelectorAll('[id*="alignet"], [class*="alignet"], [id*="vpos2"], [class*="vpos2"], [class*="modal"]').forEach(overlay => {
                    if (overlay && overlay.style && !overlay.matches('form, body, html')) {
                        overlay.style.display = 'none';
                        overlay.remove();
                    }
                });
                document.querySelectorAll('.modal-backdrop, .overlay').forEach(el => el.remove());
            } catch (error) {}
        }
    </script>
</body>

</html>
