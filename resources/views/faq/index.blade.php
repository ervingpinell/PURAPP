@extends('layouts.app')

@section('title', __('adminlte::adminlte.faqpage'))

@push('styles')
  @vite(['resources/css/faqs.css'])
@endpush

@section('content')
<section class="faq-section">
  <div class="container">
    <h1 class="big-title text-center">{{ __('adminlte::adminlte.faqpage') }}</h1>

    @if($faqs->isEmpty())
        <p class="text-muted">{{ __('adminlte::adminlte.no_faqs_available') }}</p>
    @else
        <div class="accordion" id="faqAccordion">
            @foreach ($faqs as $faq)
                @php
                    $uid = 'faq-'.($faq->id ?? $loop->index);
                @endphp

                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-{{ $uid }}">
                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse-{{ $uid }}"
                            aria-expanded="false"
                            aria-controls="collapse-{{ $uid }}"
                        >
                            <i class="fas fa-question-circle"></i>
                            {{ $faq->translate()?->question ?? $faq->question }}
                        </button>
                    </h2>

                    <div
                        id="collapse-{{ $uid }}"
                        class="accordion-collapse collapse"
                        aria-labelledby="heading-{{ $uid }}"
                        data-bs-parent="#faqAccordion"
                    >
                        <div class="accordion-body">
                            <span class="text-success">✓</span>
                            <span>{!! nl2br(e($faq->translate()?->answer ?? $faq->answer)) !!}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
  </div>
</section>

@include('partials.ws-widget')
@endsection
