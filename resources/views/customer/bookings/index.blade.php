{{-- resources/views/public/customer.blade.php --}}
@extends('layouts.app')

@push('styles')
<style>
    main {
        flex: 1;
    }

    .card-img-top-custom {
        height: 200px;
        object-fit: cover;
        width: 100%;
        border-top-left-radius: .5rem;
        border-top-right-radius: .5rem;
    }

    .card {
        border: none;
        border-radius: .5rem;
        transition: transform .2s, box-shadow .2s;
        box-shadow: 0 4px 15px rgba(0, 0, 0, .08);
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, .15);
    }

    .card-body {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: .5rem;
        color: var(--primary-header, #0d4b1a);
    }

    .card-text i {
        width: 20px;
        text-align: center;
        margin-right: 8px;
        color: var(--primary-header, #0d4b1a);
    }

    .badge-status {
        padding: .5em .75em;
        border-radius: .35rem;
        font-weight: 700;
        font-size: .85em;
    }

    .badge-pending {
        background-color: #ffc107;
        color: #1f2937;
    }

    .badge-confirmed {
        background-color: #28a745;
        color: #fff;
    }

    .badge-cancelled {
        background-color: #dc3545;
        color: #fff;
    }

    .alert-info {
        background-color: #e2f3e8;
        border: 1px solid #b0e0c8;
        color: #0d4b1a;
        padding: 2rem;
        border-radius: .75rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
    }

    .alert-heading {
        color: #0d4b1a;
        font-size: 1.5rem;
        font-weight: 700;
    }

    .cat-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        font-size: .95rem
    }

    .cat-left {
        display: flex;
        align-items: center;
        gap: .5rem
    }

    .cat-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.6rem;
        height: 1.35rem;
        padding: 0 .4rem;
        font-size: .75rem;
        border-radius: .25rem;
        background: #eef3ee;
        color: #0d4b1a
    }

    .cat-name {
        font-weight: 600
    }

    .cat-price {
        white-space: nowrap;
        font-weight: 600;
    }

    .cat-sub {
        white-space: nowrap;
        color: var(--primary-red, #dc3545);
        font-weight: 700
    }

    .cat-total {
        color: var(--primary-dark, #0d4b1a);
        font-weight: 700
    }
</style>
@endpush

@section('content')
@php
use App\Models\CustomerCategory;

$fmt2 = fn($n) => number_format((float)$n, 2, '.', '');
$locale = app()->getLocale();

/**
* ===== 1) Preload diccionarios por ID y por SLUG para TODO el set de bookings =====
* Evita N+1 al resolver nombres traducidos cuando el payload no trae 'translated'
*/
$allCatsRaw = collect($bookings ?? [])
->map(fn($b) => optional($b->detail)->categories ?? [])
->flatten(1);

$ids = $allCatsRaw->pluck('category_id')->merge($allCatsRaw->pluck('id'))->filter()->unique()->values()->all();
$slugs = $allCatsRaw->pluck('slug')->filter()->unique()->values()->all();

$dict = CustomerCategory::preloadDictionaries($ids, $slugs, $locale);
$byId = $dict['byId'] ?? [];
$bySlug = $dict['bySlug'] ?? [];

/**
* ===== 2) Mapa para códigos “antiguos” si llegan solo como code =====
*/
$codeMap = [
'adult' => __('adminlte::adminlte.adult'),
'adults' => __('adminlte::adminlte.adults'),
'kid' => __('adminlte::adminlte.kid'),
'kids' => __('adminlte::adminlte.kids'),
'child' => __('adminlte::adminlte.kid'),
'children' => __('adminlte::adminlte.kids'),
'senior' => __('adminlte::adminlte.senior') ?: 'Senior',
'student' => __('adminlte::adminlte.student') ?: 'Student',
];

/**
* ===== 3) Resolución de nombre modular (usa clave estándar 'translated') =====
*/
$resolveCategoryName = function(array $c) use ($byId, $bySlug, $codeMap): string {
// 3.1) Clave estándar pedida: 'translated' (o compat 'translation.name')
$inlineTranslated = data_get($c, 'translated')
?? data_get($c, 'translation.name');
if ($inlineTranslated) return (string) $inlineTranslated;

// 3.2) Aliases comunes si el payload trae texto
$inline = data_get($c, 'name')
?? data_get($c, 'label')
?? data_get($c, 'category_name')
?? data_get($c, 'category.name');
if ($inline) return (string) $inline;

// 3.3) Diccionarios precargados por ID o SLUG
$cid = data_get($c, 'category_id') ?? data_get($c, 'id');
if ($cid && isset($byId[$cid])) return (string) $byId[$cid];

$slug = data_get($c, 'slug');
if ($slug && isset($bySlug[$slug])) return (string) $bySlug[$slug];

// 3.4) Por code (adult/kid/etc.) → traducción de llave o texto del code
$code = data_get($c, 'code');
if ($code) {
if (isset($codeMap[$code])) return (string) $codeMap[$code];
$tr = __($code);
if ($tr !== $code) return (string) $tr;
return (string) ucfirst(str_replace('_',' ', $code));
}

// 3.5) Fallback traducido para "category"
return __('adminlte::adminlte.category');
};

/**
* ===== 4) Construcción de líneas por detalle =====
* Si no hay estructura de categorías en el detalle, cae al fallback adults/kids
*/
$catLinesFromDetail = function($detail) use ($resolveCategoryName) {
$raw = collect($detail->categories ?? []);

if ($raw->isEmpty()) {
$fallback = [
[
'name' => __('adminlte::adminlte.adults'),
'quantity' => (int)($detail->adults_quantity ?? 0),
'price' => (float)($detail->adult_price ?? 0),
],
[
'name' => __('adminlte::adminlte.kids'),
'quantity' => (int)($detail->kids_quantity ?? 0),
'price' => (float)($detail->kid_price ?? 0),
],
];

return collect($fallback)->map(function($c){
$c['subtotal'] = (float)$c['price'] * (int)$c['quantity'];
return $c;
})->filter(fn($c)=>$c['quantity']>0)->values();
}

return $raw->map(function($c) use ($resolveCategoryName) {
$cArr = is_array($c) ? $c : (array)$c;
$q = (int) data_get($cArr, 'quantity', 0);
$p = (float) data_get($cArr, 'price', 0.0);
$name = $resolveCategoryName($cArr);

return [
'name' => (string)$name,
'quantity' => $q,
'price' => $p,
'subtotal' => $p * $q,
];
})->filter(fn($c)=>$c['quantity']>0)->values();
};

$detailSubtotal = fn($detail) => (float) collect($catLinesFromDetail($detail))->sum('subtotal');
$detailTotalPax = fn($detail) => (int) collect($catLinesFromDetail($detail))->sum('quantity');
@endphp

<main class="container my-5">
    <h2 class="mb-4 text-center big-title">{{ __('adminlte::adminlte.my_reservations') }}</h2>

    @if($bookings->isEmpty())
    <div class="alert alert-info text-center" role="alert">
        <h4 class="alert-heading">{{ __('adminlte::adminlte.no_reservations_yet') }}</h4>
        <p>{{ __('adminlte::adminlte.no_reservations_message') }}</p>
        <hr>
        <a href="{{ url('/') }}" class="btn btn-primary">
            <i class="fas fa-compass me-2"></i>{{ __('adminlte::adminlte.view_available_tours') }}
        </a>
    </div>
    @else
    @php
    $groupedBookings = collect($bookings)->groupBy('status');
    $statusOrder = ['pending', 'confirmed', 'cancelled'];
    @endphp

    @foreach($statusOrder as $statusKey)
    @if($groupedBookings->has($statusKey) && $groupedBookings[$statusKey]->count() > 0)
    <section class="mb-5">
        <h3 class="mb-4 text-center">
            @switch($statusKey)
            @case('pending')
            <i class="fas fa-clock text-warning me-2"></i> {{ __('adminlte::adminlte.pending_reservations') }}
            @break
            @case('confirmed')
            <i class="fas fa-check-circle text-success me-2"></i> {{ __('adminlte::adminlte.confirmed_reservations') }}
            @break
            @case('cancelled')
            <i class="fas fa-times-circle text-danger me-2"></i> {{ __('adminlte::adminlte.cancelled_reservations') }}
            @break
            @default
            {{ __('adminlte::adminlte.reservations_generic') }}
            @endswitch
        </h3>

        <div class="row justify-content-center row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($groupedBookings[$statusKey] as $booking)
            @php
            $detail = $booking->detail;
            $lines = $detail ? $catLinesFromDetail($detail) : collect();
            $pax = $detail ? $detailTotalPax($detail) : 0;
            @endphp
            <div class="col mx-auto">
                <div class="card h-100">
                    @if(optional($booking->product)->image_path)
                    <img src="{{ asset('storage/' . $booking->product->image_path) }}" class="card-img-top card-img-top-custom" alt="{{ optional($booking->product)->name }}">
                    @else
                    <img src="{{ asset('images/volcano.png') }}" class="card-img-top card-img-top-custom" alt="{{ __('adminlte::adminlte.generic_tour') }}">
                    @endif

                    <div class="card-body">
                        <h5 class="card-title">
                            {{ method_exists($booking->product, 'getTranslatedName') ? $booking->product->getTranslatedName() : (optional($booking->product)->name ?? __('adminlte::adminlte.unknown_tour')) }}
                        </h5>

                        <p class="card-text text-muted small mb-1">
                            <i class="fas fa-calendar-alt"></i>
                            <strong>{{ __('adminlte::adminlte.booking_date') }}:</strong>
                            {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}
                        </p>

                        <p class="card-text text-muted small mb-1">
                            <i class="fas fa-calendar-check"></i>
                            <strong>{{ __('adminlte::adminlte.product_date') }}:</strong>
                            {{ \Carbon\Carbon::parse(optional($detail)->product_date)->format('M d, Y') }}
                        </p>

                        @if(optional($detail)->schedule)
                        <p class="card-text text-muted small mb-1">
                            <i class="fas fa-clock"></i>
                            <strong>{{ __('adminlte::adminlte.schedule') }}:</strong>
                            {{ \Carbon\Carbon::parse($detail->schedule->start_time)->format('g:i A') }}
                            – {{ \Carbon\Carbon::parse($detail->schedule->end_time)->format('g:i A') }}
                        </p>
                        @endif
                        @php
                        // Prioridad: idioma del detalle → idioma del booking
                        $langName = optional($detail?->productLanguage)->name
                        ?? optional($booking->productLanguage)->name;
                        @endphp

                        @if(!empty($langName))
                        <p class="card-text text-muted small mb-1">
                            <i class="fas fa-language"></i>
                            <strong>{{ __('adminlte::adminlte.language') }}:</strong>
                            {{ $langName }}
                        </p>
                        @endif


                        <div class="card-text text-muted small mb-2">
                            <i class="fas fa-users"></i>
                            <strong>{{ __('adminlte::adminlte.participants') }}:</strong>
                            <span class="ms-1">{{ $pax }}</span>
                            @if($lines->count())
                            <div class="mt-2">
                                @foreach($lines as $L)
                                <div class="cat-line">
                                    <div class="cat-left">
                                        <span class="cat-badge">{{ $L['quantity'] }}x</span>
                                        <span class="cat-name">{{ $L['name'] }}</span>
                                        <span class="cat-price">${{ $fmt2($L['price']) }}</span>
                                    </div>
                                    <div class="cat-sub">${{ $fmt2($L['subtotal']) }}</div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        <p class="card-text text-muted small mb-2">
                            <i class="fas fa-hotel"></i>
                            <strong>{{ __('adminlte::adminlte.hotel') }}:</strong>
                            @if(optional($detail)->is_other_hotel)
                            {{ $detail->other_hotel_name }}
                            @else
                            {{ optional(optional($detail)->hotel)->name ?? __('adminlte::adminlte.not_specified') }}
                            @endif
                        </p>

                        <div class="d-flex justify-content-between align-items-center mb-3 mt-auto pt-2">
                            <div>
                                <strong>{{ __('adminlte::adminlte.status') }}:</strong>
                                <span class="badge badge-status
                                                    @switch($booking->status)
                                                        @case('pending') badge-pending @break
                                                        @case('confirmed') badge-confirmed @break
                                                        @case('cancelled') badge-cancelled @break
                                                        @default bg-secondary @break
                                                    @endswitch">
                                    @switch($booking->status)
                                    @case('pending') {{ __('adminlte::adminlte.status_pending') }} @break
                                    @case('confirmed') {{ __('adminlte::adminlte.status_confirmed') }} @break
                                    @case('cancelled') {{ __('adminlte::adminlte.status_cancelled') }} @break
                                    @default {{ __('adminlte::adminlte.status_unknown') }} @break
                                    @endswitch
                                </span>

                                @php
                                // Determinar estado de pago
                                $paymentBadgeClass = 'secondary';
                                $paymentText = __('Pending');

                                if ($booking->relationLoaded('payments') && $booking->payments->isNotEmpty()) {
                                $latestPayment = $booking->payments->sortByDesc('created_at')->first();
                                if ($latestPayment && $latestPayment->status === 'completed') {
                                $paymentBadgeClass = 'success';
                                $paymentText = __('Paid');
                                }
                                }
                                @endphp

                                <br>
                                <small class="badge bg-{{ $paymentBadgeClass }} mt-1">
                                    <i class="fas fa-credit-card me-1"></i>{{ $paymentText }}
                                </small>
                            </div>
                            <div class="cat-total fs-5">
                                ${{ number_format($booking->total, 2) }}
                            </div>
                        </div>

                        <div class="d-grid">
                            <a href="{{ route('bookings.receipt.download', $booking->booking_id) }}" class="btn btn-primary btn-lg mb-2">
                                <i class="fas fa-file-invoice me-2"></i> {{ __('adminlte::adminlte.view_receipt') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif
    @endforeach
    @endif
</main>
@endsection