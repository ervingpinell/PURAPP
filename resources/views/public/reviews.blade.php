@extends('layouts.app')

@section('title', 'Reseñas de todos los productos')
@push('styles')
    @vite([
        'resources/css/review.css',
    ])
@endpush

@push('meta')
    <meta name="robots" content="noindex, nofollow">
@endpush

@section('content')
<div class="container py-5">
    <h1 class="mb-4 text-center">{{ __('adminlte::adminlte.what_visitors_say') }}</h1>

    <div id="all-reviews-container">
        <p>Cargando reseñas...</p>
    </div>
</div>

<script>
    window.VIATOR_PRODUCT_CODES = @json($productCodes);
</script>

@vite([
    'resources/js/viator/render-reviews.js',
    'resources/js/viator/all-reviews.js',
])
@endsection
