@php
  use App\Models\Tour;
  use Illuminate\Support\Carbon;

  // === Parámetros ===
  $layout   = request('layout', 'hero');
  $theme    = request('theme', $layout === 'card' ? 'site' : 'embed');
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

  $baseDefault = $layout === 'card' ? 500 : 460;
  $baseHeight  = max(200, (int) request('base', $baseDefault));
  $uid         = request('uid');

  $showPowered = request()->has('show_powered')
      ? request()->boolean('show_powered')
      : !($layout === 'card' && $theme === 'site');

  // Selección final
  $reviews = collect($reviews ?? []);
  $r       = $reviews->first();

  // ✅ PRIORIDAD: 1) Review, 2) Query params, 3) DB
  $tourId   = (int)($r['tour_id'] ?? request('tour_id', 0));
  $tourName = trim((string)($r['tour_name'] ?? request('tname', '')));

  // Si aún no hay nombre pero hay ID, consultar DB
  if ($tourId && !$tourName) {
      $tour = Tour::with('translations')->find($tourId);
      if ($tour) {
          $locale = app()->getLocale();
          $fallback = config('app.fallback_locale', 'es');
          $tr = ($tour->translations ?? collect())->firstWhere('locale', $locale)
              ?: ($tour->translations ?? collect())->firstWhere('locale', $fallback);
          $tourName = $tr->name ?? $tour->name ?? '';
      }
  }

  $tourUrl = request('turl') ?: ($tourId ? route('tours.show', ['id'=>$tourId]) : '');

  $rating = max(0, min(5, (int) data_get($r, 'rating', 5)));
  $title  = trim((string) data_get($r, 'title', ''));
  $body   = trim((string) data_get($r, 'body', ''));
  $author = trim((string) data_get($r, 'author_name', __('reviews.anonymous_guest')));
  $dateV  = data_get($r, 'date');
  $date   = $dateV ? Carbon::parse($dateV)->isoFormat('ll') : '';

  $TXT_MORE = __('reviews.see_more'); if (str_starts_with($TXT_MORE,'reviews.')) $TXT_MORE = 'Ver más';
  $TXT_LESS = __('reviews.see_less'); if (str_starts_with($TXT_LESS,'reviews.')) $TXT_LESS = 'Mostrar menos';
@endphp

<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  @if ($theme === 'site')
    @vite('resources/css/reviews.css')
    <style>html,body{background:transparent;margin:0;padding:0}</style>
  @else
    @vite('resources/css/reviews-embed.css')
  @endif
</head>

<body
  data-more="{{ $TXT_MORE }}"
  data-less="{{ $TXT_LESS }}"
  style="margin:0;padding:0;background:transparent;"
>
@if($r)
  @if ($layout === 'card' && $theme === 'site')
    @include('partials.reviews.card', ['r' => $r, 'active' => true])

    @if($showPowered)
      <div class="powered-by" style="margin-top:.5rem;">
        {{ __('reviews.powered_by') }} {{ $origin }}
      </div>
    @endif

  @else
    <div class="wrap">
      <article class="hero-card">
        @if($tourName || $tourUrl)
          <h3 class="tour-title-abs">
            @if($tourUrl)
              <a href="{{ $tourUrl }}" class="open-parent-modal" data-name="{{ $tourName }}" rel="nofollow noopener">
                {{ $tourName }}
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
              width="56" height="56"
              referrerpolicy="no-referrer"
              onerror="this.onerror=null;this.src='{{ asset('images/avatar-default.png') }}';"
            />
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
