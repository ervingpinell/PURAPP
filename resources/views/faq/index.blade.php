@extends('layouts.app')

@section('title', __('adminlte::adminlte.faqpage'))
@section('meta_description', __('adminlte::adminlte.meta.faq_description'))

@push('styles')
@vite(entrypoints: 'resources/css/faqs.css')
@endpush

@section('content')
<section class="faq-section">
    <div class="container">
        <h1 class="big-title text-center">{{ __('adminlte::adminlte.faqpage') }}</h1>

        @if($faqs->isEmpty())
        <p class="text-muted text-center">{{ __('adminlte::adminlte.no_faqs_available') }}</p>
        @else
        <div class="accordion" id="faqAccordion">
            @foreach ($faqs as $index => $faq)
            @php
                // Use Spatie Translatable methods
                $locale = app()->getLocale();
                $fallback = config('app.fallback_locale', 'es');
                
                $question = $faq->getTranslation('question', $locale, false) 
                         ?: $faq->getTranslation('question', $fallback)
                         ?: $faq->question;
                         
                $answer = $faq->getTranslation('answer', $locale, false)
                       ?: $faq->getTranslation('answer', $fallback)
                       ?: $faq->answer;
                
                $uid = 'faq-'.($faq->faq_id ?? $loop->index); 
            @endphp

            <div class="accordion-item">
                <h2 class="accordion-header" id="heading-{{ $uid }}">
                    <button
                        class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapse-{{ $uid }}"
                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                        aria-controls="collapse-{{ $uid }}">
                        <i class="fas fa-question-circle me-2"></i>
                        {!! nl2br(e($question)) !!}
                    </button>
                </h2>

                <div
                    id="collapse-{{ $uid }}"
                    class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                    aria-labelledby="heading-{{ $uid }}"
                    data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <span>{!! nl2br(e($answer)) !!}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <div class="text-center mt-5">
            <p>{{ __('adminlte::adminlte.faq_more_questions') ?? '¿Tiene más preguntas?' }}</p>
            <a href="{{ route(app()->getLocale() . '.contact') }}" class="btn btn-product-cta">{{ __('adminlte::adminlte.contact_us') ?? 'Contáctenos' }}</a>
        </div>
    </div>
</section>

@include('partials.ws-widget')
@endsection