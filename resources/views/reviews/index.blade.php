@extends('layouts.app')

@section('title', __('reviews.reviews_title'))

@push('meta')
  <meta name="robots" content="index,follow">
@endpush

@push('styles')
  @vite(entrypoints: 'resources/css/reviews.css')
@endpush

@section('content')
<div id="reviews-page"
     class="container py-5"
     data-more="{{ __('reviews.see_more') }}"
     data-less="{{ __('reviews.see_less') }}"
     data-by="{{ __('reviews.powered_by') }}"
     data-swal-title="{{ __('reviews.open_tour_title') }}"
     data-swal-text="{{ __('reviews.open_tour_text_pre') }}"
     data-swal-ok="{{ __('reviews.open_tour_confirm') }}"
     data-swal-cancel="{{ __('reviews.open_tour_cancel') }}"
>
  <h2 class="big-title text-center mb-5" style="color: var(--primary-dark);">
    {{ __('reviews.reviews_title') }}
  </h2>

  <div class="review-grid">
    @foreach ($tours as $tour)
      @php
        // Proveedor por defecto si el controlador no lo definió
        $provSlug = $tour->iframe_slug ?? config('reviews.providers.default', 'viator');
      @endphp

      <div class="review-card" id="tour-card-{{ $tour->tour_id }}">
        <h3 class="review-title">
          <a href="{{ localized_route('tours.show', $tour) }}"
             class="text-light d-inline-block tour-link"
             style="text-decoration: underline;">
            {{ $tour->display_name }}
          </a>
        </h3>

        <div class="js-carousel" data-tour="{{ $tour->tour_id }}">
          @php
            $hasMixed = isset($tour->slides) && $tour->slides instanceof \Illuminate\Support\Collection && $tour->slides->count() > 0;
          @endphp

          @if($hasMixed)
            {{-- ===== Mezcla local + remoto (hasta 6) ===== --}}
            <div class="js-slides">
              @foreach($tour->slides as $i => $slide)
                @if(($slide['type'] ?? '') === 'local')
                  @include('partials.reviews.card', ['r' => $slide['data'], 'active' => $i === 0])
                @else
                  @php
                    // 1 iframe por proveedor; nth fijo en 1
                    $slug = $provSlug ?: 'viator';
                    $uid  = 'u'.substr(bin2hex(random_bytes(6)), 0, 8);

                    // Si el controlador nos pasó pool real del proveedor, úsalo como limit
                    $poolFromProvider = (int)($slide['pool'] ?? 0);
                    $limitForIframe   = $poolFromProvider > 0
                        ? $poolFromProvider
                        : (int)($tour->pool_limit ?? 30);

                    $src  = route('reviews.embed', ['provider' => $slug]) . '?' . http_build_query([
                      'layout'        => 'card',
                      'theme'         => 'site',
                      'tour_id'       => $tour->tour_id,
                      'limit'         => $limitForIframe, // usa pool real si existe
                      'nth'           => 1,                // siempre 1
                      'base'          => 400,
                      'show_powered'  => 0,
                      'uid'           => $uid,
                    ]);
                  @endphp

                  <div class="review-item"
                       data-prov-key="{{ $slug }}"
                       data-prov-label="{{ ucfirst($slug) }}"
                       style="display: {{ $i === 0 ? '' : 'none' }};">
                    <div class="review-body-wrapper">
                      <div class="iframe-shell small" style="--h:400px">
                        <iframe
                          class="review-iframe"
                          title="Review"
                          loading="lazy"
                          referrerpolicy="no-referrer"
                          scrolling="no"
                          data-limit="{{ $limitForIframe }}"
                          data-nth="1"
                          data-src="{{ $src }}"></iframe>
                        <div class="iframe-skeleton"></div>
                      </div>
                    </div>
                  </div>
                @endif
              @endforeach
            </div>

            <div class="powered-by js-powered"></div>
            <div class="carousel-buttons-row">
              <button class="carousel-prev"  data-tour="{{ $tour->tour_id }}" aria-label="{{ __('reviews.previous_review') }}">❮</button>
              <button class="carousel-next"  data-tour="{{ $tour->tour_id }}" aria-label="{{ __('reviews.next_review') }}">❯</button>
            </div>

          @else
            {{-- ===== LEGACY ===== --}}
            @php
              /** @var \Illuminate\Support\Collection $items */
              $items = $tour->indexable_reviews ?? collect();
            @endphp

            @if($items->count() > 0)
              <div class="js-slides">
                @foreach($items as $i => $r)
                  @include('partials.reviews.card', ['r' => $r, 'active' => $i === 0])
                @endforeach
              </div>

              <div class="powered-by js-powered"></div>
              <div class="carousel-buttons-row">
                <button class="carousel-prev" data-tour="{{ $tour->tour_id }}" aria-label="{{ __('reviews.previous_review') }}">❮</button>
                <button class="carousel-next" data-tour="{{ $tour->tour_id }}" aria-label="{{ __('reviews.next_review') }}">❯</button>
              </div>

            @elseif(!empty($tour->needs_iframe))
              @php
                $slug = $provSlug ?: 'viator';
                $poolLimit = (int)($tour->pool_limit ?? $tour->iframe_limit ?? 30);
                $uid = 'u'.substr(bin2hex(random_bytes(6)), 0, 8);
                $src = route('reviews.embed', ['provider' => $slug]) . '?' . http_build_query([
                  'layout'        => 'card',
                  'theme'         => 'site',
                  'tour_id'       => $tour->tour_id,
                  'limit'         => max(8, $poolLimit),
                  'nth'           => 1,
                  'base'          => 400,
                  'show_powered'  => 0,
                  'uid'           => $uid,
                ]);
              @endphp

              <div class="js-slides">
                <div class="review-item"
                     data-prov-key="{{ $slug }}"
                     data-prov-label="{{ ucfirst($slug) }}"
                     style="display:;">
                  <div class="review-body-wrapper">
                    <div class="iframe-shell small" style="--h:400px">
                      <iframe
                        class="review-iframe"
                        title="Review"
                        loading="lazy"
                        referrerpolicy="no-referrer"
                        scrolling="no"
                        data-limit="{{ max(8, $poolLimit) }}"
                        data-nth="1"
                        data-src="{{ $src }}"></iframe>
                      <div class="iframe-skeleton"></div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="powered-by js-powered"></div>
              <div class="carousel-buttons-row">
                <button class="carousel-prev" data-tour="{{ $tour->tour_id }}" aria-label="{{ __('reviews.previous_review') }}">❮</button>
                <button class="carousel-next" data-tour="{{ $tour->tour_id }}" aria-label="{{ __('reviews.next_review') }}">❯</button>
              </div>

            @else
              <p class="text-muted text-center my-4">{{ __('reviews.no_reviews') }}</p>
            @endif
          @endif
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection

@push('scripts')
  @vite('resources/js/reviews-index.js')
@endpush
