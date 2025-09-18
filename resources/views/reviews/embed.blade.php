@php
  use App\Models\Tour;
  use Illuminate\Support\Carbon;

  // === Parámetros ===
  $layout   = request('layout', 'hero');                         // hero | card
  $theme    = request('theme', $layout === 'card' ? 'site' : 'embed'); // site | embed
  $limit    = (int) request('limit', 1);
  $provider = $provider ?? 'viator';

  $appName  = config('app.name', 'Our Website');
  $map = [
    'local'        => $appName,
    'viator'       => 'Viator',
    'google'       => 'Google',
    'gyg'          => 'GetYourGuide',
    'getyourguide' => 'GetYourGuide',
    'tripadvisor'  => 'Tripadvisor',
    'ta'           => 'Tripadvisor',
  ];
  $origin = $map[strtolower($provider)] ?? ucfirst($provider);

  // Altura base (el padre puede sobreescribir con ?base=)
  $baseDefault = $layout === 'card' ? 500 : 460;
  $baseHeight  = max(200, (int) request('base', $baseDefault));
  $uid         = request('uid');

  // ---> Mostrar/ocultar "powered by"
  // Si viene show_powered en query, lo respetamos.
  // Si no viene, por defecto se oculta en card+site (index) y se muestra en el resto.
  $showPowered = request()->has('show_powered')
      ? request()->boolean('show_powered')
      : !($layout === 'card' && $theme === 'site');

  // Datos del padre
  $tourIdParam   = (int) request('tour_id', 0);
  $tourNameParam = trim((string) request('tname', ''));
  $tourUrlParam  = trim((string) request('turl', ''));

  /** @var \Illuminate\Support\Collection $reviews */
  $reviews = ($reviews ?? collect())->map(function ($it) use ($tourIdParam) {
      if ($tourIdParam) $it['tour_id'] = $tourIdParam;
      return $it;
  });

  // Completa tour_name desde BD cuando haya tour_id
  if ($reviews->isNotEmpty()) {
      $ids = $reviews->pluck('tour_id')->filter()->unique()->values();
      if ($ids->isNotEmpty()) {
          $loc = app()->getLocale(); $fb = config('app.fallback_locale', 'es');
          $tours = Tour::with('translations')->whereIn('tour_id', $ids)->get()->keyBy('tour_id');
          $reviews = $reviews->map(function ($it) use ($tours, $loc, $fb) {
              if (empty($it['tour_name']) && !empty($it['tour_id'])) {
                  $t = $tours->get((int) $it['tour_id']);
                  if ($t) {
                      $tr = ($t->translations ?? collect())->firstWhere('locale', $loc)
                          ?: ($t->translations ?? collect())->firstWhere('locale', $fb);
                      $it['tour_name'] = $tr->name ?? $t->name ?? '';
                  }
              }
              return $it;
          });
      }
  }

  // NTH con wrap-around
  $nth   = max(1, (int) request('nth', 1));
  $count = max(1, $reviews->count());
  $idx   = ($nth - 1) % $count;
  $r     = $reviews->slice($idx, 1)->first();

  // Fallbacks finales
  $tourId   = (int)($r['tour_id'] ?? $tourIdParam);
  $tourName = trim((string)($r['tour_name'] ?? $tourNameParam));
  $tourUrl  = $tourUrlParam ?: ($tourId ? route('tours.show', ['id'=>$tourId]) : '');

  $rating = max(0, min(5, (int) data_get($r, 'rating', 5)));
  $title  = trim((string) data_get($r, 'title', ''));
  $body   = trim((string) data_get($r, 'body', ''));
  $author = trim((string) data_get($r, 'author_name', __('reviews.anonymous_guest')));
  $dateV  = data_get($r, 'date');
  $date   = $dateV ? Carbon::parse($dateV)->isoFormat('ll') : '';

  // i18n para JS (y fallback duro si falta la key)
  $TXT_MORE = __('reviews.see_more'); if (str_starts_with($TXT_MORE,'reviews.')) $TXT_MORE = 'Ver más';
  $TXT_LESS = __('reviews.see_less'); if (str_starts_with($TXT_LESS,'reviews.')) $TXT_LESS = 'Ver menos';
@endphp

<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- CSS según theme --}}
  @if ($theme === 'site')
    @vite('resources/css/reviews.css')        {{-- el mismo CSS del index --}}
    <style>html,body{background:transparent;margin:0;padding:0}</style>
  @else
    @vite('resources/css/reviews-embed.css')  {{-- CSS del hero (home/show-tour) --}}
  @endif
</head>

<body
  data-more="{{ $TXT_MORE }}"
  data-less="{{ $TXT_LESS }}"
  data-base="{{ (int)$baseHeight }}"
  @if($uid) data-uid="{{ $uid }}" @endif
  style="margin:0;padding:0;background:transparent;"
>
@if($r)
  @if ($layout === 'card' && $theme === 'site')
    {{-- CARD con el mismo marcado que el index (usa reviews.css) --}}
    @include('partials.reviews.card', ['r' => $r, 'active' => true])

    @if($showPowered)
      <div class="powered-by" style="margin-top:.5rem;">
        {{ __('reviews.powered_by') }} {{ $origin }}
      </div>
    @endif

  @else
    {{-- HERO (home / tour-show) usando reviews-embed.css --}}
    <div class="wrap">
      <article class="hero-card">
        @if($tourName || $tourUrl)
          <h3 class="tour-title-abs">
            @if($tourUrl)
              <a href="{{ $tourUrl }}" class="open-parent-modal" data-name="{{ $tourName }}" rel="nofollow noopener">
                {{ $tourName ?: __('reviews.view_tour') }}
              </a>
            @else
              {{ $tourName }}
            @endif
          </h3>
        @endif

        <div class="review-head">
          <span class="avatar">
            <img
              src="{{ data_get($r,'avatar_url', asset('images/avatar-default.png')) }}"
              alt=""
              referrerpolicy="no-referrer"
              onerror="this.onerror=null;this.src='{{ asset('images/avatar-default.png') }}';"
            >
          </span>
          <div class="who-when">
            <div class="who">{{ $author }}</div>
            @if($date)<div class="when">{{ $date }}</div>@endif
          </div>
        </div>

        <div class="stars-row under-date">
          <span class="review-stars">{!! str_repeat('★', $rating) !!}{!! str_repeat('☆', 5 - $rating) !!}</span>
          <span class="rating-number">({{ $rating }}/5)</span>
        </div>

        @if($title)
          <div class="review-label">{{ $title }}</div>
        @endif

        @if($body !== '')
          <div class="review-textwrap">
            <div class="review-content clamp">{!! nl2br(e($body)) !!}</div>
            <button type="button" class="review-toggle">{{ $TXT_MORE }}</button>
          </div>
        @endif

        {{-- En HERO lo dejamos visible por defecto (o respeta show_powered si lo pasas) --}}
        @if($showPowered)
          <div class="powered-by">{{ __('reviews.powered_by') }} {{ $origin }}</div>
        @endif
      </article>
    </div>
  @endif
@endif

@vite('resources/js/reviews-embed.js')
</body>
</html>
