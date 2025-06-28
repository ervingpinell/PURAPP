<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Comprobante de Reserva</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Lora:wght@400;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --green-dark: #1A5229;
      --green-base: #2E8B57;
      --gray-light: #f0f2f5;
      --text-color: #333;
      --font-heading: 'Montserrat', sans-serif;
      --font-body: 'Lora', serif;
    }
    body {
      font-family: var(--font-body);
      font-size: 14px; /* Reducimos el tamaño de la fuente base */
      background: var(--gray-light);
      margin: 0;
      padding: 30px; /* Reducimos el padding del cuerpo */
      color: var(--text-color);
      line-height: 1.5; /* Ajustamos la altura de línea */
    }
    .comprobante-container {
      max-width: 680px; /* Reducimos el ancho máximo */
      margin: auto;
      background: #fff;
      border: none;
      border-radius: 10px; /* Bordes ligeramente más pequeños */
      padding: 30px; /* Reducimos el padding interno */
      box-shadow: 0 8px 20px rgba(0,0,0,0.08); /* Sombra más sutil */
    }
    h2 {
      margin: 0 0 25px; /* Menos espacio debajo del título */
      color: var(--green-dark);
      text-transform: uppercase;
      letter-spacing: 1.5px; /* Menos espacio entre letras */
      font-size: 28px; /* Título más pequeño */
      text-align: center;
      font-family: var(--font-heading);
      font-weight: 700;
    }
    .header-info {
        text-align: center;
        margin-bottom: 25px;
        font-size: 16px;
        color: var(--green-base);
        font-family: var(--font-heading);
    }
    .datos-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px 30px; /* Reducimos el espacio entre elementos del grid */
    }
    .dato {
      display: flex;
      flex-direction: column;
      padding: 8px 0; /* Padding más pequeño */
      border-bottom: 1px dashed #ddd;
    }
    .dato:last-of-type, .datos-grid > div:nth-last-child(2):nth-child(odd) {
        border-bottom: none;
    }
    .dato strong {
      color: var(--green-base);
      margin-bottom: 6px; /* Menos espacio entre etiqueta y valor */
      font-size: 13px; /* Etiquetas más pequeñas */
      font-family: var(--font-heading);
      font-weight: 600;
    }
    .dato span {
        font-size: 15px; /* Valores ligeramente más pequeños */
        color: var(--text-color);
    }
    .dato small {
        font-size: 12px; /* Email más pequeño */
        color: #777;
    }
    .line-separator {
      border-top: 1.5px solid var(--green-base); /* Línea más fina */
      margin: 30px 0; /* Menos espacio alrededor del separador */
    }
    .total-section {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding-top: 10px; /* Padding más pequeño */
    }
    .total {
      font-size: 20px; /* Total más pequeño */
      color: var(--green-dark);
      font-weight: 700;
      font-family: var(--font-heading);
    }
    .qr-container {
      text-align: center;
      margin-top: 35px; /* Menos espacio encima del QR */
      padding: 15px; /* Padding más pequeño */
      background-color: #f9f9f9;
      border-radius: 6px;
    }
    .qr-label {
      margin-top: 10px; /* Menos espacio debajo del QR */
      font-size: 13px; /* Etiqueta del QR más pequeña */
      color: #666;
      font-family: var(--font-body);
    }

    /* Media Queries para responsividad (ajustadas para el nuevo tamaño base) */
    @media (max-width: 600px) {
      body {
        padding: 15px;
      }
      .comprobante-container {
        padding: 20px;
      }
      .datos-grid {
        grid-template-columns: 1fr;
        gap: 10px;
      }
      h2 {
        font-size: 24px;
      }
      .total {
        font-size: 18px;
      }
    }
    /* Estilos específicos para impresión */
    @media print {
        body {
            background: none; /* Elimina el fondo en impresión */
            padding: 0; /* Elimina el padding en impresión */
            margin: 0; /* Elimina los márgenes en impresión */
        }
        .comprobante-container {
            box-shadow: none; /* Elimina la sombra en impresión */
            border: 1px solid #ccc; /* Borde sutil para definir el área */
            border-radius: 0; /* Sin bordes redondeados en impresión */
            margin: 1cm; /* Margen para asegurar que no se corte al imprimir */
            max-width: initial; /* Permite que el contenedor use todo el ancho disponible */
        }
    }
  </style>
</head>
<body>
  <div class="comprobante-container">
    <h2>COMPROBANTE DE RESERVA</h2>

    @php
      $tour   = $reserva->tour;
      $detail = $reserva->detail;
      $aQty   = $detail->adults_quantity;
      $kQty   = $detail->kids_quantity;
      $aPrice = $tour->adult_price   ?? 0;
      $kPrice = $tour->kid_price     ?? 0;
      $hotel  = $detail->is_other_hotel
                  ? $detail->other_hotel_name
                  : optional($detail->hotel)->name ?? '—';
    @endphp

    <div class="datos-grid">
      <div class="dato">
        <strong>Código</strong>
        <span>{{ $reserva->booking_reference }}</span>
      </div>
      <div class="dato">
        <strong>Cliente</strong>
        <span>{{ optional($reserva->user)->full_name }}</span>
        <br><small>({{ optional($reserva->user)->email }})</small>
      </div>
      <div class="dato">
        <strong>Tour</strong>
        <span>{{ $tour->name }}</span>
      </div>
      <div class="dato">
        <strong>Fecha de reserva</strong>
        <span>{{ \Carbon\Carbon::parse($reserva->booking_date)->format('d/m/Y') }}</span>
      </div>
      <div class="dato">
        <strong>Hotel</strong>
        <span>{{ $hotel }}</span>
      </div>
      <div class="dato">
        <strong>Estado</strong>
        <span>{{ ucfirst($reserva->status) }}</span>
      </div>
    </div>

    <div class="line-separator"></div>

    <div class="datos-grid">
      <div class="dato">
        <strong>Adultos (x{{ $aQty }})</strong>
        <span>${{ number_format($aPrice * $aQty, 2) }}</span>
      </div>
      <div class="dato">
        <strong>Niños (x{{ $kQty }})</strong>
        <span>${{ number_format($kPrice * $kQty, 2) }}</span>
      </div>
      <div class="dato">
        <strong>Personas</strong>
        <span>{{ $aQty + $kQty }}</span>
      </div>
    </div>
    <div class="total-section">
        <span class="total">TOTAL: ${{ number_format($reserva->total, 2) }}</span>
    </div>

    <div class="qr-container">
    @php
        // URL al servicio externo (qrserver.com)
        $data = urlencode($reserva->booking_reference);
        $urlQr = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={$data}"; /* Reducimos el QR a 120x120 */

        // Descarga el PNG y conviértelo a base64
        $png    = file_get_contents($urlQr);
        $base64 = base64_encode($png);
    @endphp

    <img
        src="data:image/png;base64,{{ $base64 }}"
        alt="QR Código de Reserva"
        style="width:120px; height:120px;" /* Ajustamos el tamaño del QR en el HTML también */
    >
    <p class="qr-label">Escanea para verificar tu reserva</p>
    </div>
  </div>
</body>
</html>