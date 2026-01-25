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
                // Determine active locale translation or fallback
                $locale = app()->getLocale();
                $translation = $faq->translations->firstWhere('locale', $locale);
                // Fallback mechanisms: specific translation -> ES translation -> base columns
                $question = $translation ? $translation->question : $faq->question;
                $answer   = $translation ? $translation->answer : $faq->answer;
                
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
            <a href="{{ route(app()->getLocale() . '.contact') }}" class="btn btn-tour-cta">{{ __('adminlte::adminlte.contact_us') ?? 'Contáctenos' }}</a>
        </div>
    </div>
</section>

@include('partials.ws-widget')
@endsection