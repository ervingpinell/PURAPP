<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Procesando Pago...</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            text-align: center;
            padding: 40px;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 90%;
        }

        .icon {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .success {
            color: #28a745;
        }

        .error {
            color: #dc3545;
        }

        .cancel {
            color: #6c757d;
        }

        h2 {
            margin: 10px 0;
            font-size: 20px;
            color: #333;
        }

        p {
            color: #666;
            margin-bottom: 0;
        }

        .loader {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-left: 10px;
            vertical-align: middle;
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
    <div class="container">
        @if($type === 'success')
        <div class="icon success">✅</div>
        <h2>{{ $message ?? 'Pago Exitoso' }}</h2>
        @elseif($type === 'cancel')
        <div class="icon cancel">🚫</div>
        <h2>{{ $message ?? 'Pago Cancelado' }}</h2>
        @else
        <div class="icon error">❌</div>
        <h2>{{ $message ?? 'Error en el Pago' }}</h2>
        @endif

        <p>Redirigiendo... <span class="loader"></span></p>
    </div>

    <script>
        // Función para salir del iframe y redirigir
        function breakOut() {
            var redirectUrl = '{{ $redirectUrl }}';

            // Intentar notificar al padre primero (opcional, para UI más suave)
            if (window.parent && window.parent !== window) {
                window.parent.postMessage({
                    type: 'alignet_payment_completed',
                    status: '{{ $type }}',
                    redirectUrl: redirectUrl
                }, '*');
            }

            // Redirigir la ventana principal después de una breve pausa
            setTimeout(function() {
                window.top.location.href = redirectUrl;
            }, 800);
        }

        // Ejecutar inmediatamente
        breakOut();
    </script>
</body>

</html>