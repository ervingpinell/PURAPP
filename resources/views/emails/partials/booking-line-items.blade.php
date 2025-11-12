@php
    /**
     * Espera:
     * - $booking
     * - $details?
     * - $mailLocale? ('es'|'en')
     * - $suppressHeader? (bool)  // si true, NO muestra fecha/idioma/meeting/hotel/notas
     * - $showLineTotals? (bool)
     */

    use App\Models\CustomerCategory;

    $locale = isset($mailLocale) ? (str_starts_with($mailLocale, 'es') ? 'es' : 'en')
                                 : (str_starts_with(app()->getLocale(), 'es') ? 'es' : 'en');

    // Locale preferido: booking > mailLocale > app
    $preferredLoc = strtolower(
        ($booking->locale ?? $booking->language_code ?? $mailLocale ?? app()->getLocale())
    );
    $preferredLoc = \Illuminate\Support\Str::of($preferredLoc)->before('-')->lower()->value();

    $rows        = collect($details ?? $booking->details ?? []);
    $withTotals  = (bool)($showLineTotals ?? false);
    $suppress    = (bool)($suppressHeader ?? false);
    $showContext = !$suppress;

    $money = fn($n) => '$' . number_format((float)$n, 2);

    $labelBreakdown = $locale === 'es' ? 'Desglose por cliente' : 'Customer breakdown';
    $labelCustomer  = $locale === 'es' ? 'Cliente'  : 'Customer';
    $labelQty       = $locale === 'es' ? 'Cantidad' : 'Qty';
    $labelPrice     = $locale === 'es' ? 'Precio'   : 'Price';
    $labelSubtotal  = $locale === 'es' ? 'Subtotal' : 'Subtotal';
    $labelDate      = $locale === 'es' ? 'Fecha'    : 'Date';
    $labelLang      = $locale === 'es' ? 'Idioma'   : 'Language';
    $labelHotel     = $locale === 'es' ? 'Hotel pickup' : 'Hotel pickup';
    $labelMeeting   = $locale === 'es' ? 'Punto de encuentro' : 'Meeting point';
    $labelNotes     = $locale === 'es' ? 'Notas' : 'Notes';
    $labelPersons   = $locale === 'es' ? 'Personas' : 'Persons';

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

@forelse ($rows as $d)
    @php
        $tourDate = $d->tour_date ? \Illuminate\Support\Carbon::parse($d->tour_date)->format('Y-m-d') : null;
        $cats     = collect($d->categories ?? []);
        $lineSum  = $cats->reduce(fn($c,$x)=>$c+((float)($x['quantity']??0)*(float)($x['price']??0)), 0.0);
        $pax      = $cats->sum(fn($x) => (int)($x['quantity'] ?? 0));

        $tourLang = optional($d->tourLanguage)->language_name
            ?? optional($d->tourLanguage)->name
            ?? optional($booking->tourLanguage)->language_name
            ?? optional($booking->tourLanguage)->language
            ?? null;

        $meetingName = $d->meeting_point_name;
        $meetingUrl  = $d->meeting_point_map_url;

        if (!$meetingName && $d->meetingPoint) {
            $mp = $d->meetingPoint;
            if (method_exists($mp, 'getTranslated')) {
                $meetingName = $mp->getTranslated('name', app()->getLocale()) ?? $mp->name;
            } else {
                $loc = \Illuminate\Support\Str::of(app()->getLocale())->before('-')->lower()->value();
                $tr  = $mp->relationLoaded('translations')
                    ? $mp->translations->firstWhere('locale', $loc)
                    : $mp->translations()->where('locale', $loc)->first();
                $meetingName = $tr->name ?? $mp->name;
            }
            $meetingUrl = $meetingUrl ?: ($mp->map_url ?? null);
        }

        if (($d->is_other_hotel ?? false) && filled($d->other_hotel_name)) {
            $hotelName = $d->other_hotel_name;
        } else {
            $hotelName = optional($d->hotel)->name
                ?? optional($booking->hotel)->name
                ?? null;
        }

        $notes = trim((string)($booking->notes ?? ''));
    @endphp

    <div class="section-card">
        <div class="section-title">
            {{ $labelBreakdown }}

            {{-- Contexto opcional sólo si NO se suprime --}}
            @if($showContext)
                @if($tourDate)
                    <span style="display:block;font-size:12px;color:#6b7280;font-weight:400;margin-top:.25rem">
                        {{ $labelDate }}: {{ $tourDate }}
                    </span>
                @endif
                @if($tourLang)
                    <span style="display:block;font-size:12px;color:#6b7280;font-weight:400;margin-top:.25rem">
                        {{ $labelLang }}: {{ $tourLang }}
                    </span>
                @endif
                @if($meetingName)
                    <span style="display:block;font-size:12px;color:#6b7280;font-weight:400;margin-top:.25rem">
                        {{ $labelMeeting }}:
                        @if($meetingUrl)
                            <a href="{{ $meetingUrl }}" target="_blank" rel="noopener" style="color:#0ea5e9;text-decoration:none;">
                                {{ $meetingName }}
                            </a>
                        @else
                            {{ $meetingName }}
                        @endif
                    </span>
                @elseif($hotelName)
                    <span style="display:block;font-size:12px;color:#6b7280;font-weight:400;margin-top:.25rem">
                        {{ $labelHotel }}: {{ $hotelName }}
                    </span>
                @endif
                @if($notes !== '')
                    <span style="display:block;font-size:12px;color:#6b7280;font-weight:400;margin-top:.25rem">
                        {{ $labelNotes }}: {{ $notes }}
                    </span>
                @endif
            @endif
        </div>

        @if ($cats->isEmpty())
            <p style="margin:0;color:#6b7280">{{ $locale === 'es' ? 'No hay categorías registradas.' : 'No customers recorded.' }}</p>
        @else
            <table class="data-table" role="presentation" cellpadding="0" cellspacing="0">
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
                            $fallback = $c['category_name'] ?? \Illuminate\Support\Str::of($fallback)->replace(['_','-'],' ')->title();
                        }

                        $name  = $translated ?: $fallback;
                        $qty   = (int) ($c['quantity'] ?? 0);
                        $price = (float) ($c['price'] ?? 0);
                        $sub   = $qty * $price;
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
@empty
    <div class="section-card">
        <p style="margin:0;color:#6b7280">{{ $locale === 'es' ? 'No se encontraron detalles de la reserva.' : 'No booking details found.' }}</p>
    </div>
@endforelse
