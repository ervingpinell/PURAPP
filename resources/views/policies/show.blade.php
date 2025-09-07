@extends('layouts.app')

@php
  $locale = app()->getLocale();
  $translatedPolicy = $policy->translation($locale) ?? $policy->translation('es');
  $pageTitle = $translatedPolicy?->name ?? $policy->name ?? __('policies.untitled');
@endphp

@section('title', $pageTitle)

@section('content')
<style>
  .accordion-button::after { content: none !important; display: none !important; }
  .accordion-item { background: transparent; }
  .accordion-button { background: transparent; }
  .accordion-button .icon-plus,
  .accordion-button .icon-minus { display: inline-block; }
  .accordion-button[aria-expanded="false"] .icon-minus { display: none !important; }
  .accordion-button[aria-expanded="true"]  .icon-plus  { display: none !important; }
</style>

<div class="container py-4">
  <h1 class="mb-3 big-title text-center">{{ $pageTitle }}</h1>

  @if(filled($translatedPolicy?->content))
    <div class="mb-4">{!! nl2br(e($translatedPolicy->content)) !!}</div>
  @endif

  @php
    $sections = method_exists($policy, 'activeSections')
                ? $policy->activeSections()->orderBy('sort_order')->get()
                : (($policy->sections ?? collect())->sortBy('sort_order'));
  @endphp

  @if($sections->isNotEmpty())
    <div class="accordion" id="policySectionsAccordion">
      @foreach($sections as $section)
        @php
          $translatedSection = $section->translation($locale) ?? $section->translation('es');
          $sectionId = "section-{$section->section_id}";
        @endphp

        <div class="accordion-item border-0 border-bottom">
          <h2 class="accordion-header" id="heading-{{ $sectionId }}">
            <button
              class="accordion-button bg-white px-0 shadow-none collapsed"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#collapse-{{ $sectionId }}"
              aria-expanded="false"
              aria-controls="collapse-{{ $sectionId }}"
            >
              <span class="me-2 d-inline-flex align-items-center" aria-hidden="true">
                <i class="fas fa-plus icon-plus"></i>
                <i class="fas fa-minus icon-minus"></i>
              </span>
              {{ $translatedSection?->name ?? $section->name ?? __('policies.section') }}
            </button>
          </h2>

          <div id="collapse-{{ $sectionId }}" class="accordion-collapse collapse" data-bs-parent="#policySectionsAccordion">
            <div class="accordion-body px-0">
              {!! nl2br(e($translatedSection?->content ?? '')) !!}
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @else
  @endif
</div>
@endsection
