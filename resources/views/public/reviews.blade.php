@extends('layouts.app')

@section('title', __('adminlte::adminlte.reviews'))

@push('meta')
    <meta name="robots" content="noindex, nofollow">
@endpush

@push('styles')
    @vite('resources/css/reviews.css')
@endpush

@section('content')
<div class="container py-5">
    <h2 class="big-title text-center mb-5">{{ __('adminlte::adminlte.reviews') }}</h2>

    <div class="review-grid">
        @foreach ($tours as $tour)
            <div class="review-card expandable" id="card-{{ $tour->tour_id }}">
                {{-- Título: intenta con translated_name; si no, usa el método del modelo, y por último name --}}
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
                        {{ __('adminlte::adminlte.loading_reviews') ?: 'Loading reviews...' }}
                    </p>
                </div>

                <div class="review-footer">
                    <div class="carousel-buttons-row">
                        <button class="carousel-prev" data-tour="{{ $tour->tour_id }}">❮</button>
                        <button class="carousel-next" data-tour="{{ $tour->tour_id }}">❯</button>
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
                          Powered by Viator
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
    // Exporta tours para JS con fallback seguro
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

    // Cargar traducciones del modal desde adminlte con fallback
    $kTitle = 'adminlte::adminlte.open_tour';
    $kText  = 'adminlte::adminlte.open_tour_text_pre';
    $kOk    = 'adminlte::adminlte.open_tour_confirm';
    $kCancel= 'adminlte::adminlte.open_tour_cancel';

    $trTitle  = __($kTitle);   $title  = ($trTitle  === $kTitle)  ? 'Ir al tour?' : $trTitle;
    $trText   = __($kText);    $text   = ($trText   === $kText)   ? 'Vas a abrir la página del tour' : $trText;
    $trOk     = __($kOk);      $ok     = ($trOk     === $kOk)     ? 'Ir ahora' : $trOk;
    $trCancel = __($kCancel);  $cancel = ($trCancel === $kCancel) ? 'Cancelar' : $trCancel;
@endphp

<script>
  // Datos para el grid JS
  window.VIATOR_TOURS = @json($viatorTours, JSON_UNESCAPED_UNICODE);

  // Textos I18N expuestos al JS (incluye los del modal "abrir tour")
  window.I18N = Object.assign({}, window.I18N || {}, {
    loading_reviews: @json(__('adminlte::adminlte.loading_reviews') ?: 'Loading reviews...'),
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
    // Usa traducciones de adminlte si están definidas; si no, defaults
    const TXT = {
      title:   (window.I18N && window.I18N.open_tour_title)   || '¿Abrir tour?',
      textPre: (window.I18N && window.I18N.open_tour_text_pre)|| 'Vas a abrir la página del tour',
      confirm: (window.I18N && window.I18N.open_tour_confirm) || 'Ir ahora',
      cancel:  (window.I18N && window.I18N.open_tour_cancel)  || 'Cancelar',
    };

    // Intercepta clicks en el título del tour para confirmar
    document.addEventListener('click', function (e) {
      const a = e.target.closest('a.tour-link');
      if (!a) return;

      // Permitir abrir en nueva pestaña/ventana sin confirmación
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
        // Fallback simple si no está SweetAlert
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
