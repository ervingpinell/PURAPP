<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alignet Test Parameters</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            padding: 20px;
            background: #f5f5f5;
            max-width: 1200px;
            margin: 0 auto;
        }

        h2 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        .param {
            margin: 8px 0;
            padding: 8px;
            background: white;
            border-left: 3px solid #007bff;
        }

        .key {
            color: #0066cc;
            font-weight: bold;
            display: inline-block;
            min-width: 250px;
        }

        .value {
            color: #28a745;
            word-break: break-all;
        }

        .section {
            margin: 20px 0;
            padding: 15px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        textarea {
            width: 100%;
            height: 400px;
            font-family: 'Courier New', monospace;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .important {
            background: #fff3cd;
            border-left-color: #ffc107;
        }

        .hash {
            font-size: 11px;
            word-break: break-all;
        }
    </style>
</head>

<body>
    <h2>🧪 Alignet Test Parameters</h2>

    <div class="section">
        <h3>📋 Payment Data Parameters</h3>
        @foreach($paymentData as $key => $value)
        @if(!in_array($key, ['base_url', 'vpos2_script']))
        <div class="param {{ in_array($key, ['purchaseVerification', 'urlResponse', 'timeoutResponse']) ? 'important' : '' }}">
            <span class="key">{{ $key }}:</span>
            <span class="value {{ $key === 'purchaseVerification' ? 'hash' : '' }}">{{ $value }}</span>
        </div>
        @endif
        @endforeach
    </div>

    <div class="section">
        <h3>🔗 URLs</h3>
        <div class="param">
            <span class="key">Base URL:</span>
            <span class="value">{{ $paymentData['base_url'] ?? 'N/A' }}</span>
        </div>
        <div class="param">
            <span class="key">VPOS2 Script:</span>
            <span class="value">{{ $paymentData['vpos2_script'] ?? 'N/A' }}</span>
        </div>
    </div>

    <div class="section">
        <h3>📝 Copy-paste para testing manual (JSON)</h3>
        <textarea readonly>{{ json_encode($paymentData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</textarea>
    </div>

    <div class="section">
        <h3>✅ Verificaciones</h3>
        <div class="param">
            <span class="key">Hash Length:</span>
            <span class="value">{{ strlen($paymentData['purchaseVerification'] ?? '') }} caracteres (debe ser 128)</span>
        </div>
        <div class="param">
            <span class="key">Amount Format:</span>
            <span class="value">{{ $paymentData['purchaseAmount'] ?? 'N/A' }} centavos</span>
        </div>
        <div class="param">
            <span class="key">Currency Code:</span>
            <span class="value">{{ $paymentData['purchaseCurrencyCode'] ?? 'N/A' }} (840 = USD)</span>
        </div>
    </div>
</body>

</html>