@extends('layouts.app')

@section('title', __('adminlte::adminlte.faqpage'))

@section('content')
<div class="container py-5">
    <h1 class="big-title mb-4 text-center">{{ __('adminlte::adminlte.faqpage') }}</h1>

    @if($faqs->isEmpty())
        <p class="text-muted">{{ __('adminlte::adminlte.no_faqs_available') }}</p>
    @else
        <div class="accordion" id="faqAccordion">
            @foreach ($faqs as $faq)
                @php
                    // Asegura IDs únicos incluso si esta vista se incluye varias veces
                    $uid = 'faq-'.($faq->id ?? $loop->index);
                    $headingId = "heading-{$uid}";
                    $collapseId = "collapse-{$uid}";
                @endphp

                <div class="accordion-item mb-2">
                    <h2 class="accordion-header" id="{{ $headingId }}">
                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#{{ $collapseId }}"
                            aria-expanded="false"
                            aria-controls="{{ $collapseId }}"
                        >
                            <i class="fas fa-question-circle text-primary me-2"></i>
                            {{ $faq->translate()?->question ?? $faq->question }}
                        </button>
                    </h2>

                    <div
                        id="{{ $collapseId }}"
                        class="accordion-collapse collapse"
                        aria-labelledby="{{ $headingId }}"
                        data-bs-parent="#faqAccordion"
                    >
                        <div class="accordion-body">
                            <span class="text-success me-2">✓</span>
                            <span>{!! nl2br(e($faq->translate()?->answer ?? $faq->answer)) !!}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ✅ Modal de WhatsApp --}}
@include('partials.ws-widget')
@endsection
