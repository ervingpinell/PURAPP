@extends('layouts.app')

@section('title', __('policies.page_title'))
@section('content')
@php $locale = app()->getLocale(); @endphp

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
  <h1 class="mb-3 big-title text-center">{{ __('policies.page_title') }}</h1>

  @if($policies->isEmpty())
    <p class="text-muted">{{ __('policies.no_policies') }}</p>
  @else
    <div class="accordion" id="allPoliciesAccordion">
      @foreach($policies as $policy)
        @php
          $translatedPolicy  = $policy->translation($locale) ?? $policy->translation('es');
          $accordionId       = "policy-{$policy->policy_id}";
          $sections = method_exists($policy, 'activeSections')
                        ? $policy->activeSections()->orderBy('sort_order')->get()
                        : (($policy->sections ?? collect())->sortBy('sort_order'));
        @endphp

        <div class="accordion-item border-0 border-bottom">
          <h2 class="accordion-header" id="heading-{{ $accordionId }}">
            <button
              class="accordion-button bg-white px-0 shadow-none collapsed"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#collapse-{{ $accordionId }}"
              aria-expanded="false"
              aria-controls="collapse-{{ $accordionId }}"
            >
              <span class="me-2 d-inline-flex align-items-center" aria-hidden="true">
                <i class="fas fa-plus icon-plus"></i>
                <i class="fas fa-minus icon-minus"></i>
              </span>
              {{ $translatedPolicy?->name ?? $policy->name ?? __('policies.untitled') }}
            </button>
          </h2>

          <div id="collapse-{{ $accordionId }}" class="accordion-collapse collapse" data-bs-parent="#allPoliciesAccordion">
            <div class="accordion-body px-0">

              @if(filled($translatedPolicy?->content))
                <div class="mb-3">{!! nl2br(e($translatedPolicy->content)) !!}</div>
              @endif

              @if($sections->isNotEmpty())
                <div class="accordion" id="inner-{{ $accordionId }}">
                  @foreach($sections as $section)
                    @php
                      $translatedSection = $section->translation($locale) ?? $section->translation('es');
                      $sectionId         = "sec-{$policy->policy_id}-{$section->section_id}";
                    @endphp

                    <div class="accordion-item border-0 border-top">
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

                      <div id="collapse-{{ $sectionId }}" class="accordion-collapse collapse" data-bs-parent="#inner-{{ $accordionId }}">
                        <div class="accordion-body px-0">
                          {!! nl2br(e($translatedSection?->content ?? '')) !!}
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              @endif

            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
