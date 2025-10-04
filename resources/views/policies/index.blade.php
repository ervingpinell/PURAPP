@extends('layouts.app')

@section('title', __('policies.page_title'))

@push('styles')
  @vite('resources/css/policies.css')
@endpush

@section('content')
@php $locale = app()->getLocale(); @endphp

<section class="policies-page">
  <div class="container policies-wrap">
    <h1 class="policies-title big-title">{{ __('policies.page_title') }}</h1>

    @if($policies->isEmpty())
      <p class="policy-meta">{{ __('policies.no_policies') }}</p>
    @else
      <div class="accordion policies-accordion" id="allPoliciesAccordion">
        @foreach($policies as $policy)
          @php
            $translatedPolicy = $policy->translation($locale) ?? $policy->translation('es');
            $accordionId      = "policy-{$policy->policy_id}";
            $sections = method_exists($policy, 'activeSections')
                          ? $policy->activeSections()->orderBy('sort_order')->get()
                          : (($policy->sections ?? collect())->sortBy('sort_order'));
          @endphp

          <div class="accordion-item">
            <h2 class="accordion-header" id="heading-{{ $accordionId }}">
              <button
                class="accordion-button collapsed"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapse-{{ $accordionId }}"
                aria-expanded="false"
                aria-controls="collapse-{{ $accordionId }}"
              >
                <span class="icon-wrap" aria-hidden="true">
                  <i class="fas fa-plus"></i>
                  <i class="fas fa-minus"></i>
                </span>
                <a href="{{ localized_route('policies.show', ['policy' => $policy->slug]) }}"
                   class="policy-link ms-2">
                  {{ $translatedPolicy?->name ?? $policy->name ?? __('policies.untitled') }}
                </a>
              </button>
            </h2>

            <div id="collapse-{{ $accordionId }}" class="accordion-collapse collapse" data-bs-parent="#allPoliciesAccordion">
              <div class="accordion-body">
                @if(filled($translatedPolicy?->content))
                  {!! nl2br(e($translatedPolicy->content)) !!}
                @endif

                @if($sections->isNotEmpty())
                  <div class="accordion policy-sections" id="inner-{{ $accordionId }}">
                    @foreach($sections as $section)
                      @php
                        $translatedSection = $section->translation($locale) ?? $section->translation('es');
                        $sectionId = "sec-{$policy->policy_id}-{$section->section_id}";
                      @endphp

                      <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-{{ $sectionId }}">
                          <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse-{{ $sectionId }}"
                            aria-expanded="false"
                            aria-controls="collapse-{{ $sectionId }}"
                          >
                            <span class="icon-wrap" aria-hidden="true">
                              <i class="fas fa-plus"></i>
                              <i class="fas fa-minus"></i>
                            </span>
                            <span class="ms-2">
                              {{ $translatedSection?->name ?? $section->name ?? __('policies.section') }}
                            </span>
                          </button>
                        </h2>

                        <div id="collapse-{{ $sectionId }}" class="accordion-collapse collapse" data-bs-parent="#inner-{{ $accordionId }}">
                          <div class="accordion-body">
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
</section>
@endsection
