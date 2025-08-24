@extends('layouts.app')

@section('title', __('reviews.reviews_title'))

@push('meta')
  <meta name="robots" content="noindex, nofollow">
@endpush

@push('styles')
  @vite('resources/css/reviews.css')
@endpush

@section('content')
<div class="container py-5">
  <h2 class="big-title text-center mb-5">{{ __('reviews.reviews_title') }}</h2>

  <div class="review-grid">
    @foreach ($tours as $tour)
      <div class="review-card expandable" id="card-{{ $tour->tour_id }}">
        @php
          $displayName = $tour->translated_name
                        ?? $tour->getTranslatedName(app()->getLocale())
                        ?? $tour->name
                        ?? '';
        @endphp

        <h3 class="review-title">
          <a
            href="{{ route('tours.show', ['id' => $tour->tour_id]) }}"
            class="text-light d-inline-block tour-link"
            data-id="{{ $tour->tour_id }}"
            data-name="{{ $displayName }}"
            style="text-decoration: underline;">
            {{ $displayName }}
          </a>
        </h3>

        <div class="carousel" id="carousel-{{ $tour->tour_id }}">
          <p class="text-center text-muted">
            {{ __('reviews.loading') }}
          </p>
        </div>

        <div class="review-footer">
          <div class="carousel-buttons-row">
            <button class="carousel-prev" data-tour="{{ $tour->tour_id }}" aria-label="{{ __('reviews.previous_review') }}">❮</button>
            <button class="carousel-next" data-tour="{{ $tour->tour_id }}" aria-label="{{ __('reviews.next_review') }}">❯</button>
          </div>

          <div class="powered-by">
            <a href="{{ viator_product_url(
                    $tour->viator_code,
                    $tour->viator_destination_id ?? 821,
                    $tour->viator_city_slug ?? 'La-Fortuna',
                    $tour->viator_slug ?? null,
                    optional($tour->translations)->firstWhere('locale','en')->name
                      ?? $tour->getTranslatedName('en')
                      ?? $displayName
                ) }}"
               target="_blank" rel="noopener">
              {{ __('reviews.powered_by') }} Viator
            </a>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection

@push('scripts')
@php
  $viatorTours = $tours->map(function ($t) {
    $name = $t->translated_name
          ?? $t->getTranslatedName(app()->getLocale())
          ?? $t->name
          ?? '';
    return [
      'id'   => $t->tour_id,
      'code' => $t->viator_code,
      'name' => $name,
    ];
  })->values();

  // Keys de modal/JS desde reviews.*
  $titleKey   = 'reviews.open_tour_title';
  $textKey    = 'reviews.open_tour_text_pre';
  $okKey      = 'reviews.open_tour_confirm';
  $cancelKey  = 'reviews.open_tour_cancel';

  $title  = __($titleKey);
  $text   = __($textKey);
  $ok     = __($okKey);
  $cancel = __($cancelKey);
@endphp

<script>
  window.VIATOR_TOURS = @json($viatorTours, JSON_UNESCAPED_UNICODE);

  window.I18N = Object.assign({}, window.I18N || {}, {
    loading_reviews: @json(__('reviews.loading')),
    open_tour_title: @json($title),
    open_tour_text_pre: @json($text),
    open_tour_confirm: @json($ok),
    open_tour_cancel: @json($cancel),
  });
</script>

@vite('resources/js/viator/review-carousel-grid.js')
@endpush

@push('scripts')
<script>
(function () {
  const TXT = {
    title:   (window.I18N && window.I18N.open_tour_title)   || 'Open tour?',
    textPre: (window.I18N && window.I18N.open_tour_text_pre)|| 'You are about to open the tour page',
    confirm: (window.I18N && window.I18N.open_tour_confirm) || 'Open now',
    cancel:  (window.I18N && window.I18N.open_tour_cancel)  || 'Cancel',
  };

  document.addEventListener('click', function (e) {
    const a = e.target.closest('a.tour-link');
    if (!a) return;
    if (e.metaKey || e.ctrlKey || e.shiftKey || e.button === 1) return;

    e.preventDefault();
    const href = a.getAttribute('href');
    if (!href || href === '#') return;

    const name = a.dataset.name || a.textContent.trim() || '';

    if (window.Swal && typeof window.Swal.fire === 'function') {
      window.Swal.fire({
        icon: 'question',
        title: TXT.title,
        html: `${TXT.textPre} <strong>${escapeHtml(name)}</strong>.`,
        showCancelButton: true,
        confirmButtonText: TXT.confirm,
        cancelButtonText: TXT.cancel,
        focusConfirm: true,
      }).then((res) => {
        if (res.isConfirmed) window.location.assign(href);
      });
    } else {
      if (confirm(`${TXT.title}\n\n${TXT.textPre} ${name ? `"${name}"` : ''}.`)) {
        window.location.assign(href);
      }
    }
  }, false);

  function escapeHtml(str) {
    return String(str)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }
})();
</script>
@endpush

@include('partials.show-tour-modal')
