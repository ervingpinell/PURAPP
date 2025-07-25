@extends('layouts.app')

@section('title', __('adminlte::adminlte.faqpage'))

@section('content')
<div class="container py-5">
    <h1 class="mb-4 text-center">{{ __('adminlte::adminlte.faqpage') }}</h1>

    @if($faqs->isEmpty())
        <p class="text-muted">{{ __('adminlte::adminlte.no_faqs_available') }}</p>
    @else
        <div class="accordion" id="faqAccordion">
            @foreach ($faqs as $faq)
                <div class="accordion-item mb-2">
                    <h2 class="accordion-header" id="heading{{ $faq->id }}">
                        <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapse{{ $faq->id }}"
                                aria-expanded="false"
                                aria-controls="collapse{{ $faq->id }}">
                            <i class="fas fa-question-circle text-primary me-2"></i>
                            {{ $faq->translate()?->question ?? $faq->question }}
                        </button>
                    </h2>
                    <div id="collapse{{ $faq->id }}"
                         class="accordion-collapse collapse"
                         aria-labelledby="heading{{ $faq->id }}"
                         data-bs-parent="#faqAccordion">
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
@endsection
