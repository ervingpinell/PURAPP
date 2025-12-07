{{-- resources/views/emails/partials/booking-line-items.blade.php --}}
@php
/**
* Espera:
* - $booking
* - $details? // opcional, si no se pasa usa $booking->details
* - $mailLocale? // 'es'|'en'
* - $showLineTotals? // bool: si true, muestra personas y total por detalle
*/

use App\Models\CustomerCategory;

$locale = isset($mailLocale)
? (str_starts_with($mailLocale, 'es') ? 'es' : 'en')
: (str_starts_with(app()->getLocale(), 'es') ? 'es' : 'en');

// Locale preferido: booking > mailLocale > app
$preferredLoc = strtolower(
($booking->locale ?? $booking->language_code ?? $mailLocale ?? app()->getLocale())
);
$preferredLoc = \Illuminate\Support\Str::of($preferredLoc)->before('-')->lower()->value();

$rows = collect($details ?? $booking->details ?? []);
$withTotals = (bool)($showLineTotals ?? false);

$money = fn($n) => '$' . number_format((float)$n, 2);

$labelBreakdown = $locale === 'es' ? 'Desglose por cliente' : 'Customer breakdown';
$labelCustomer = $locale === 'es' ? 'Cliente' : 'Customer';
$labelQty = $locale === 'es' ? 'Cantidad' : 'Qty';
$labelPrice = $locale === 'es' ? 'Precio' : 'Price';
$labelSubtotal = $locale === 'es' ? 'Subtotal' : 'Subtotal';
$labelPersons = $locale === 'es' ? 'Personas' : 'Persons';

/* ===== Prefetch de traducciones por category_id ===== */
$allCatIds = $rows
->flatMap(fn($d) => collect($d->categories ?? [])->pluck('category_id'))
->filter()
->map(fn($id) => (int)$id)
->unique()
->values();

$catMap = $allCatIds->isNotEmpty()
? CustomerCategory::with('translations')
->whereIn('category_id', $allCatIds)
->get()
->keyBy('category_id')
: collect();
@endphp

@if ($rows->isEmpty())
<div class="section-card">
    <p style="margin:0;color:#6b7280">
        {{ $locale === 'es'
                ? 'No se encontraron detalles de la reserva.'
                : 'No booking details found.' }}
    </p>
</div>
@else
@foreach ($rows as $d)
@php
$cats = collect($d->categories ?? []);
$lineSum = $cats->reduce(fn($c,$x)=>$c+((float)($x['quantity']??0)*(float)($x['price']??0)), 0.0);
$pax = $cats->sum(fn($x) => (int)($x['quantity'] ?? 0));
@endphp

<div class="section-card" style="font-size:14px;">
    <div class="section-title" style="font-weight:700; margin-bottom:10px;">
        {{ $labelBreakdown }}
    </div>

    @if ($cats->isEmpty())
    <p style="margin:0;color:#6b7280">
        {{ $locale === 'es'
                        ? 'No hay categorías registradas.'
                        : 'No customers recorded.' }}
    </p>
    @else
    <table class="data-table" role="presentation" cellpadding="0" cellspacing="0" style="width:100%; font-size:14px;">
        <thead>
            <tr>
                <th style="text-align:left">{{ $labelCustomer }}</th>
                <th>{{ $labelQty }}</th>
                <th>{{ $labelPrice }}</th>
                <th>{{ $labelSubtotal }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cats as $c)
            @php
            // Traducción priorizando category_id
            $translated = null;
            $catId = isset($c['category_id']) ? (int)$c['category_id'] : null;

            if ($catId && $catMap->has($catId)) {
            $translated = $catMap[$catId]->getTranslatedName($preferredLoc);
            }

            $fallback = $c['category_name'] ?? $c['category_slug'] ?? '—';
            if ($fallback && !$translated) {
            // “pretty slug” si viene slug
            $fallback = $c['category_name']
            ?? \Illuminate\Support\Str::of($fallback)
            ->replace(['_','-'],' ')
            ->title();
            }

            $name = $translated ?: $fallback;
            $qty = (int) ($c['quantity'] ?? 0);
            $price = (float) ($c['price'] ?? 0);
            $sub = $qty * $price;
            @endphp
            <tr>
                <td style="text-align:left">{{ $name }}</td>
                <td>{{ $qty }}</td>
                <td>{{ $money($price) }}</td>
                <td>{{ $money($sub) }}</td>
            </tr>
            @endforeach
        </tbody>

        @if($withTotals)
        <tfoot>
            <tr>
                <td style="text-align:left;font-weight:600">{{ $labelPersons }}</td>
                <td style="font-weight:600">{{ $pax }}</td>
                <td></td>
                <td style="font-weight:600">{{ $money($lineSum) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
    @endif
</div>
@endforeach
@endif