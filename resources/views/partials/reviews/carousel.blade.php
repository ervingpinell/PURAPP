{{-- resources/views/partials/reviews/carousel.blade.php --}}
@php
  $items = collect($items ?? []);

  // Dedupe SOLO indexables (SEO)
  $indexables = $items->where('indexable', true)
    ->unique(fn($r) => md5(
      mb_strtolower(trim((string)($r['body'] ?? ''))) . '|' .
      mb_strtolower(trim((string)($r['author_name'] ?? ''))) . '|' .
      trim((string)($r['date'] ?? ''))
    ))->values();

  $embeds = $items->where('indexable', false)->values();
  $items  = $indexables->merge($embeds)->take(25);

  $providerHeight = (int)($providerHeight ?? 320); // compacto en página de tour
  $carouselId     = 'tourReviewsCarousel_'.uniqid();

  $TXT_MORE = __('reviews.read_more');  if (str_starts_with($TXT_MORE,'reviews.')) $TXT_MORE = 'Leer más';
  $TXT_LESS = __('reviews.read_less');  if (str_starts_with($TXT_LESS,'reviews.')) $TXT_LESS = 'Mostrar menos';

  $providerMap = [
    'local'        => config('app.name', 'Our Website'),
    'viator'       => 'Viator',
    'tripadvisor'  => 'Tripadvisor',
    'ta'           => 'Tripadvisor',
    'google'       => 'Google',
    'gyg'          => 'GetYourGuide',
    'getyourguide' => 'GetYourGuide',
  ];

  $initialLoad = 2;
@endphp

@once
  @push('styles')
    @vite('resources/css/reviews-carousel.css')
  @endpush
  @push('scripts')
    @vite('resources/js/reviews-carousel.js')
  @endpush
@endonce

@if($items->isNotEmpty())
  <div id="{{ $carouselId }}"
       class="carousel slide reviews-block"
       data-bs-ride="carousel"
       data-bs-interval="9000"
       data-more="{{ $TXT_MORE }}"
       data-less="{{ $TXT_LESS }}"
       data-base="{{ (int)$providerHeight }}"
       data-carousel-id="{{ $carouselId }}"
       style="--provider-base: {{ (int)$providerHeight }}px;">
    <div class="carousel-inner">
      @foreach($items as $i => $r)
        @php
          $isActive = $i === 0 ? 'active' : '';
          $provKey  = strtolower((string)($r['provider'] ?? 'local'));
          $origin   = $providerMap[$provKey] ?? ucfirst($provKey ?: 'Local');
          $rating   = max(0, min(5, (int)($r['rating'] ?? 5)));
          $author   = $r['author_name'] ?? __('reviews.anonymous_guest');
          $date     = !empty($r['date']) ? \Illuminate\Support\Carbon::parse($r['date'])->isoFormat('ll') : '';
          $title    = trim((string)($r['title'] ?? ''));
          $body     = trim((string)($r['body'] ?? ''));
          $tourId   = $r['tour_id'] ?? null;
          $tourUrl  = $tourId ? route('tours.show', ['id' => $tourId]) : '#';
          $tourName = trim((string)($r['tour_name'] ?? ''));
          $avatarUrl= $r['avatar_url'] ?? null;

          $uid = 'r_'.substr(sha1(($r['provider'] ?? 'p').'|'.($r['tour_id'] ?? '0').'|'.($r['nth'] ?? $i).'|'.uniqid()),0,10);
        @endphp

        <div class="carousel-item {{ $isActive }}">
          <div class="slide-wrap">
            @if(!empty($r['indexable']))
              <article class="hero-card">
                @if($tourName !== '')
                  <h3 class="tour-title-abs">
                    <a href="{{ $tourUrl }}" class="tour-link" data-id="{{ $tourId ?? '' }}" data-name="{{ $tourName }}">{{ $tourName }}</a>
                  </h3>
                @endif

                <div class="review-head">
                  <span class="avatar">
<img
  src="{{ $avatarUrl ?: asset('images/avatar-default.png') }}"
  alt=""
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

                @if($title !== '')
                  <div class="review-label">{{ $title }}</div>
                @endif

                @if($body !== '')
                  <div class="review-textwrap">
                    <div class="review-content clamp-8">{!! nl2br(e($body)) !!}</div>
                    <button type="button" class="review-toggle">{{ $TXT_MORE }}</button>
                  </div>
                @endif

                <div class="powered-by">{{ __('reviews.powered_by') }} {{ $origin }}</div>
              </article>

            @else
              @php
                $limit   = max(1, (int)($r['iframe_limit'] ?? 1));
                $nth     = (int)($r['nth'] ?? 1);
                $src     = route('reviews.embed', $provKey)
                          . '?layout=hero'
                          . '&limit='  . urlencode($limit)
                          . '&nth='    . urlencode($nth)
                          . ($tourId ? ('&tour_id=' . urlencode($tourId)) : '')
                          . '&base='   . urlencode($providerHeight)
                          . '&uid='    . urlencode($uid);
                $eager   = $i < $initialLoad;
              @endphp

              <article class="hero-card p-0" style="box-shadow:none;background:transparent">
                <div class="iframe-shell">
                  <div class="iframe-skeleton" aria-hidden="true"></div>
                  <iframe
                    class="review-embed"
                    title="Review {{ $origin }}"
                    data-uid="{{ $uid }}"
                    @if($eager) src="{{ $src }}" @else data-src="{{ $src }}" @endif
                    loading="lazy"
                    referrerpolicy="no-referrer"
                    sandbox="allow-scripts allow-same-origin">
                  </iframe>
                </div>
              </article>
            @endif
          </div>
        </div>
      @endforeach
    </div>

    @if($items->count() > 1)
      <button class="carousel-control-prev" type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide="prev" aria-label="{{ __('reviews.previous') }}">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">{{ __('reviews.previous') }}</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide="next" aria-label="{{ __('reviews.next') }}">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">{{ __('reviews.next') }}</span>
      </button>
    @endif
  </div>
@endif

@push('scripts')
<script>
  window.REVIEWS_I18N = {
    more:       @json(__('reviews.read_more')),
    less:       @json(__('reviews.read_less')),
    by:         @json(__('reviews.powered_by')),
    swalTitle:  @json(__('reviews.open_tour_title')),
    swalText:   @json(__('reviews.open_tour_text_pre')),
    swalOK:     @json(__('reviews.open_tour_confirm')),
    swalCancel: @json(__('reviews.open_tour_cancel')),
  };
</script>
@endpush

