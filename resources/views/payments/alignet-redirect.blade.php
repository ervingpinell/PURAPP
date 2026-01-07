<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Redirigiendo...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: #f5f5f5;
        }

        .redirect-container {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #2c5282;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="redirect-container">
        <div class="spinner"></div>
        <h2>{{ $isSuccess ? 'Pago Procesado' : 'Redirigiendo...' }}</h2>
        <p>{{ $errorMessage ?? 'Por favor espere...' }}</p>
    </div>

    <script>
        // 🔥 CRITICAL: Force redirect in top window to preserve session
        // This solves the issue where Alignet modal (iframe) loses cookies
        (function() {
            const redirectUrl = @json($redirectUrl);
            const errorMessage = @json($errorMessage ?? '');

            console.log('🔄 Alignet redirect handler');
            console.log('Target URL:', redirectUrl);
            console.log('In iframe:', window.self !== window.top);

            // If we're in an iframe (Alignet modal), redirect the top window
            if (window.self !== window.top) {
                console.log('📤 Redirecting top window from iframe');
                window.top.location.href = redirectUrl;
            } else {
                // We're already in the top window, just redirect normally
                console.log('📤 Redirecting current window');
                window.location.href = redirectUrl;
            }

            // Signal to close any open Alignet modals
            try {
                localStorage.setItem('alignet_payment_complete', 'true');
            } catch (e) {
                console.warn('Could not set localStorage:', e);
            }
        })();
    </script>
</body>

</html>