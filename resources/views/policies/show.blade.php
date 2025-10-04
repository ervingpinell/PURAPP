@extends('layouts.app')

@php
  $locale = app()->getLocale();
  $translatedPolicy = $policy->translation($locale) ?? $policy->translation('es');
  $pageTitle = $translatedPolicy?->name ?? $policy->name ?? __('policies.untitled');
@endphp

@section('title', $pageTitle)

@push('styles')
  @vite('resources/css/policies.css')
@endpush

@section('content')
<section class="policies-page">
  <div class="container policies-wrap">
    <h1 class="policies-title big-title text-center">{{ $pageTitle }}</h1>

    @if(filled($translatedPolicy?->content))
      <div class="policy-content">
        {!! nl2br(e($translatedPolicy->content)) !!}
      </div>
    @endif

    @php
      $sections = method_exists($policy, 'activeSections')
        ? $policy->activeSections()->orderBy('sort_order')->get()
        : (($policy->sections ?? collect())->sortBy('sort_order'));
    @endphp

    @if($sections->isNotEmpty())
      <div class="accordion policy-sections" id="policySectionsAccordion">
        @foreach($sections as $section)
          @php
            $translatedSection = $section->translation($locale) ?? $section->translation('es');
            $sectionId = "section-{$section->section_id}";
          @endphp

          <div class="accordion-item">
            <h2 class="accordion-header" id="heading-{{ $sectionId }}">
              <button class="accordion-button collapsed"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#collapse-{{ $sectionId }}"
                      aria-expanded="false"
                      aria-controls="collapse-{{ $sectionId }}">
                <span class="icon-wrap" aria-hidden="true">
                  <i class="fas fa-plus"></i>
                  <i class="fas fa-minus"></i>
                </span>
                <span class="ms-2">
                  {{ $translatedSection?->name ?? $section->name ?? __('policies.section') }}
                </span>
              </button>
            </h2>

            <div id="collapse-{{ $sectionId }}" class="accordion-collapse collapse"
                 data-bs-parent="#policySectionsAccordion">
              <div class="accordion-body">
                {!! nl2br(e($translatedSection?->content ?? '')) !!}
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</section>
@endsection
