@php
    $mailLocale = str_starts_with(app()->getLocale(), 'es') ? 'es' : 'en';

    $rating   = (int) ($review->rating ?? 0);
    $title    = trim((string) ($review->title ?? ''));
    $body     = trim((string) ($review->body ?? ''));
    $created  = $review->created_at ? $review->created_at->format('d-M-Y H:i') : null;
    $provider = $review->provider ?? 'local';

    $tHeader   = $mailLocale === 'es' ? 'Nueva reseña recibida' : 'New review received';
    $tTour     = $mailLocale === 'es' ? 'Product'                  : 'Product';
    $tCustomer = $mailLocale === 'es' ? 'Cliente'               : 'Customer';
    $tRating   = $mailLocale === 'es' ? 'Calificación'          : 'Rating';
    $tTitle    = $mailLocale === 'es' ? 'Título'                : 'Title';
    $tBody     = $mailLocale === 'es' ? 'Contenido'             : 'Content';
    $tDate     = $mailLocale === 'es' ? 'Fecha de envío'       : 'Submitted at';
    $tProvider = $mailLocale === 'es' ? 'Origen'                : 'Source';
    $tGoAdmin  = $mailLocale === 'es' ? 'Ver en panel:'         : 'Open in admin:';
@endphp

{{ $tHeader }}

@if($productName)
{{ $tTour }}: {{ $productName }}
@endif

@if($customerName)
{{ $tCustomer }}: {{ $customerName }}
@endif

@if($rating > 0)
{{ $tRating }}: {{ $rating }}/5
@endif

@if($created)
{{ $tDate }}: {{ $created }}
@endif

@if($provider)
{{ $tProvider }}: {{ ucfirst($provider) }}
@endif

@if($title !== '')
{{ $tTitle }}: {{ $title }}
@endif

@if($body !== '')
{{ $tBody }}:
{{ $body }}
@endif

@if($adminUrl)
{{ $tGoAdmin }} {{ $adminUrl }}
@endif
