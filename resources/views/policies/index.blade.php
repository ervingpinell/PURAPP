@extends('layouts.app')

@section('title', __('policies.page_title')) {{-- o __('policies.categories_title') si prefieres --}}
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
          $t        = $policy->translation($locale) ?? $policy->translation('es');
          $pid      = "policy-{$policy->policy_id}";
          $sections = $policy->sections ?? collect();
        @endphp

        <div class="accordion-item border-0 border-bottom">
          <h2 class="accordion-header" id="heading-{{ $pid }}">
            <button
              class="accordion-button bg-white px-0 shadow-none collapsed"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#collapse-{{ $pid }}"
              aria-expanded="false"
              aria-controls="collapse-{{ $pid }}"
            >
              <span class="me-2 d-inline-flex align-items-center" aria-hidden="true">
                <i class="fas fa-plus icon-plus"></i>
                <i class="fas fa-minus icon-minus"></i>
              </span>
              {{ $t?->title ?? $policy->name }}
            </button>
          </h2>

          <div id="collapse-{{ $pid }}" class="accordion-collapse collapse" data-bs-parent="#allPoliciesAccordion">
            <div class="accordion-body px-0">
              @if(filled($t?->content))
                <div class="mb-3">{!! nl2br(e($t->content)) !!}</div>
              @endif

              @if($sections->isNotEmpty())
                <div class="accordion" id="inner-{{ $pid }}">
                  @foreach($sections as $section)
                    @php
                      $st  = $section->translation($locale) ?? $section->translation('es');
                      $sid = "sec-{$policy->policy_id}-{$section->section_id}";
                    @endphp

                    <div class="accordion-item border-0 border-top">
                      <h2 class="accordion-header" id="heading-{{ $sid }}">
                        <button
                          class="accordion-button bg-white px-0 shadow-none collapsed"
                          type="button"
                          data-bs-toggle="collapse"
                          data-bs-target="#collapse-{{ $sid }}"
                          aria-expanded="false"
                          aria-controls="collapse-{{ $sid }}"
                        >
                          <span class="me-2 d-inline-flex align-items-center" aria-hidden="true">
                            <i class="fas fa-plus icon-plus"></i>
                            <i class="fas fa-minus icon-minus"></i>
                          </span>
                          {{ $st?->title ?? __('policies.section') }}
                        </button>
                      </h2>

                      <div id="collapse-{{ $sid }}" class="accordion-collapse collapse" data-bs-parent="#inner-{{ $pid }}">
                        <div class="accordion-body px-0">
                          {!! nl2br(e($st?->content ?? '')) !!}
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
