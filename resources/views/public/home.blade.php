
@extends('layouts.app')

@section('content')

    <section class="hero">
       @include('partials.hero')
    </section>

    <section class="tours-section">
         @include('partials.tours')
    </section>

    <section class="compact-testimonials">
         @include('partials.testimonials')
    </section>
        <section class="ws-section">
         @include('partials.ws-widget')

@endsection

@push('styles')

@endpush

@push('scripts')

@endpush
