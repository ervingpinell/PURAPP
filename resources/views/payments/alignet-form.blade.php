<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('payment.alignet.page_title') }} - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .payment-container {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
            padding: 50px 40px;
            text-align: center;
        }

        .alignet-logo {
            margin-bottom: 40px;
        }

        .alignet-logo h2 {
            margin: 0 0 12px;
            color: #1f2937;
            font-size: 2rem;
            font-weight: 700;
        }

        .alignet-logo p {
            color: #6b7280;
            margin: 0;
            font-size: 1rem;
        }

        .btn-pay {
            width: 100%;
            max-width: 350px;
            padding: 18px;
            background: #2c5282;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 0 auto;
        }

        .btn-pay:hover {
            background: #1a365d;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(44, 82, 130, 0.3);
        }

        .btn-pay:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
        }

        .secure-badge {
            margin-top: 20px;
            color: #059669;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .loading-message {
            color: #6b7280;
            margin-top: 16px;
            display: none;
        }

        .loading-message.active {
            display: block;
        }

        .debug-info {
            margin-top: 30px;
            padding: 16px;
            background: #f3f4f6;
            border-radius: 8px;
            font-size: 11px;
            font-family: monospace;
            border: 1px solid #e5e7eb;
            text-align: left;
        }

        iframe,
        #ADS-IFRAME-CONTAINER-IFRAME {
            background: white !important;
            width: 100% !important;
            min-height: 600px !important;
        }

        @media (max-width: 768px) {
            .payment-container {
                padding: 40px 24px;
            }

            .alignet-logo h2 {
                font-size: 1.5rem;
            }

            .btn-pay {
                font-size: 1rem;
                padding: 16px;
            }
        }
    </style>
</head>

<body>
    <div class="payment-container">
        <div class="alignet-logo">
            <h2>{{ __('payment.alignet.page_title') }}</h2>
            <p>{{ __('payment.alignet.processed_by') }} <strong>Alignet</strong></p>
        </div>

        <form name="alignet_payment_form" id="alignet_payment_form" action="#" method="post" class="alignet-form-vpos2">
            @foreach($paymentData as $key => $value)
            @if(!in_array($key, ['base_url', 'vpos2_script']))
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
            @endforeach
            <button type="button" id="btn-pay" onclick="abrirModalAlignet()" class="btn-pay">
                <i class="fas fa-lock"></i> {{ __('payment.alignet.proceed_payment') }}
            </button>
            <div class="loading-message" id="loading-message">
                <i class="fas fa-spinner fa-spin"></i> {{ __('payment.alignet.loading_module') }}
            </div>
        </form>

        <div class="secure-badge">
            <i class="fas fa-shield-alt"></i>
            {{ __('payment.alignet.secure_transaction') }}
        </div>

        @if(config('app.debug') && !app()->isProduction())
        <div class="debug-info">
            <strong>🐛 Debug Info</strong><br>
            Operation: {{ $paymentData['purchaseOperationNumber'] ?? '' }}<br>
            Amount: ${{ number_format(($paymentData['purchaseAmount'] ?? 0) / 100, 2) }}
        </div>
        @endif
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script type="text/javascript" src="{{ $paymentData['vpos2_script'] ?? $paymentData['base_url'] . 'VPOS2/js/modalcomercio.js' }}"></script>
    <script>
        window.addEventListener('load', function() {
            @if(config('app.debug') && !app() -> isProduction())
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
