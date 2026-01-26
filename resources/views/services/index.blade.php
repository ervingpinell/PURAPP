@extends('layouts.app')

@section('meta_title'){{ branding('seo_services_title_' . app()->getLocale(), __('adminlte::adminlte.our_services')) }}@endsection
@section('meta_description'){{ branding('seo_services_description_' . app()->getLocale(), 'Discover all our services and experiences in Costa Rica') }}@endsection
@section('title', __('adminlte::adminlte.our_services'))

@push('styles')
@vite(entrypoints: [
'resources/css/breadcrumbs.css',
])
@endpush

@section('content')
<div class="container py-4" id="services-page">
  {{-- Breadcrumbs --}}
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">
        <a href="{{ localized_route('home') }}">{{ __('adminlte::adminlte.home') }}</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">
        {{ __('adminlte::adminlte.services') }}
      </li>
    </ol>
  </nav>

  @php
  use App\Helpers\SchemaHelper;
  $breadcrumbItems = [
    ['name' => __('adminlte::adminlte.home'), 'url' => localized_route('home')],
    ['name' => __('adminlte::adminlte.services')],
  ];
  $breadcrumbSchema = SchemaHelper::generateBreadcrumbSchema($breadcrumbItems);
  @endphp
  <script type="application/ld+json">
    {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
  </script>

  {{-- Hero Section --}}
  <div class="text-center mb-4">
    <h1 class="big-title">{{ __('adminlte::adminlte.our_services') }}</h1>
  </div>

  {{-- Category Counter --}}
  <div class="tours-index-counter">
    <span class="counter-badge">
      <i class="fas fa-leaf"></i>
      <span>
        {{ trans_choice('adminlte::adminlte.categories_count', count($categoriesWithProducts), ['count' => count($categoriesWithProducts)]) }}
      </span>
    </span>
  </div>

  {{-- FILTER CONTROLS (filter toggle) --}}
  <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
    {{-- Button to toggle filters (collapse) --}}
    <button
      class="btn-tour-cta btn-sm filters-toggle d-inline-flex align-items-center gap-1"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#services-filters"
      aria-expanded="false"
      aria-controls="services-filters">
      <i class="fas fa-filter"></i>
      <span>{{ __('adminlte::adminlte.filters_btn') }}</span>
    </button>
  </div>

  {{-- FILTERS (COLLAPSE) --}}
  <div id="services-filters" class="collapse mb-2">
    <div class="card tours-index-filters">
      <div class="card-body">
        <div class="tours-index-filters-header mb-2">
          <div class="filters-title-block">
            <div class="filters-icon-circle">
              <i class="fas fa-filter"></i>
            </div>
            <div>
              <div class="filters-title">
                {{ __('adminlte::adminlte.filters_title') }}
              </div>
              <div class="filters-subtitle">
                {{ __('adminlte::adminlte.filters_subtitle') }}
              </div>
            </div>
          </div>

          @if(request('q') || request('category'))
          <div class="filters-active-chip">
            <i class="fas fa-check-circle me-1"></i>
            {{ __('adminlte::adminlte.filters_active') }}
          </div>
          @endif
        </div>

        <form
          method="GET"
          action="{{ localized_route('services.index') }}"
          class="tours-index-filters-form row g-2 align-items-end">
          {{-- Search --}}
          <div class="col-12 col-md-6">
            <label class="filters-label mb-1" for="q">
              {{ __('adminlte::adminlte.search_services_placeholder', ['default' => 'Search services...']) }}
            </label>
            <div class="input-group filters-input-group">
              <span class="input-group-text">
                <i class="fas fa-search"></i>
              </span>
              <input
                type="text"
                name="q"
                id="q"
                class="form-control filters-control"
                placeholder="{{ __('adminlte::adminlte.search_services_placeholder', ['default' => 'Search services...']) }}"
                value="{{ request('q') }}">
            </div>
          </div>

          {{-- Category (ProductType) --}}
          <div class="col-12 col-md-4">
            <label class="filters-label mb-1" for="category">
              {{ __('adminlte::adminlte.category_label') }}
            </label>
            <div class="filters-select-wrapper">
              <span class="filters-select-icon">
                <i class="fas fa-tags"></i>
              </span>
              <select
                name="category"
                id="category"
                class="form-select filters-control filters-select">
                <option value="">
                  {{ __('adminlte::adminlte.all_categories') }}
                </option>
                @foreach($categoriesWithProducts as $key => $data)
                <option value="{{ $key }}" @selected(request('category')==$key)>
                  {{ $data['category']->translated_name ?? $data['category']->name }}
                </option>
                @endforeach
              </select>
            </div>
          </div>

          {{-- Filter / Clear buttons --}}
          <div class="col-12 col-md-2 d-flex gap-2 justify-content-md-end mt-2 mt-md-0">
            <button type="submit" class="btn btn-success w-100">
              <i class="fas fa-filter me-1"></i>
              {{ __('adminlte::adminlte.filters_btn') }}
            </button>

            @if(request('q') || request('category'))
            <a
              href="{{ localized_route('services.index') }}"
              class="btn btn-outline-secondary"
              title="{{ __('adminlte::adminlte.clear_filters') }}">
              <i class="fas fa-times"></i>
            </a>
            @endif
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Categories Grid --}}
  @foreach($categoriesWithProducts as $key => $data)
    <section class="service-category mb-5" id="category-{{ $key }}">
      
      {{-- Category Header --}}
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 style="color: var(--color-page-title);">
          {{ $data['config']['plural'] }}
        </h2>
        <a href="{{ \App\Helpers\ProductCategoryHelper::categoryUrl($key) }}" 
           class="btn btn-primary">
          {{ __('adminlte::adminlte.view_all_count', ['count' => $data['total'], 'type' => $data['config']['plural']]) }}
        </a>
      </div>

      {{-- Category Description --}}
      @if(!empty($data['config']['description']))
      <p class="text-muted mb-4">{{ $data['config']['description'] }}</p>
      @endif

      {{-- Featured Products (3 per category) --}}
      @if($data['products']->isNotEmpty())
        <div class="row">
          @foreach($data['products'] as $product)
            @php
            $productUrl = \App\Helpers\ProductCategoryHelper::productUrl($product);
            $coverImage = optional($product->coverImage)->url ?? asset('images/volcano.png');
            @endphp
            <div class="col-md-4 mb-3">
              <div class="card h-100 shadow-sm">
                {{-- Product Image --}}
                @if($coverImage)
                <div class="position-relative">
                  <img src="{{ $coverImage }}" 
                       class="card-img-top" 
                       alt="{{ $product->translated_name }}"
                       style="height: 200px; object-fit: cover;"
                       loading="lazy">
                  
                  {{-- Duration Badge --}}
                  @if($product->length)
                  <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge" style="background-color: var(--color-surface-dark); font-size: 0.85rem;">
                      {{ $product->length }} hrs
                    </span>
                  </div>
                  @endif
                </div>
                @endif

                <div class="card-body d-flex flex-column">
                  <h5 class="card-title" style="color: var(--color-page-title);">
                    {{ $product->translated_name }}
                  </h5>
                  
                  @if($product->length)
                  <p class="text-muted mb-2 small">
                    <i class="far fa-clock me-1"></i>
                    {{ $product->length }} {{ __('adminlte::adminlte.hours') }}
                  </p>
                  @endif

                  <div class="mt-auto">
                    <a href="{{ $productUrl }}" 
                       class="btn btn-primary btn-sm w-100">
                      {{ __('adminlte::adminlte.view_details') }}
                    </a>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="alert alert-info">
          {{ __('adminlte::adminlte.no_products_available', ['type' => $data['config']['plural']]) }}
        </div>
      @endif
      
    </section>
    
    {{-- Divider between categories --}}
    @if(!$loop->last)
      <hr class="my-5" style="border-color: var(--border-color, #d9d9d9);">
    @endif
  @endforeach

  {{-- Call to Action --}}
  <div class="text-center mt-5">
    <div class="card text-white" style="background-color: var(--color-button-primary);">
      <div class="card-body py-5">
        <h3>{{ __('adminlte::adminlte.need_help_choosing') }}</h3>
        <p class="mb-4">{{ __('adminlte::adminlte.contact_cta_text') }}</p>
        <a href="{{ localized_route('contact') }}" class="btn btn-light btn-lg">
          {{ __('adminlte::adminlte.contact_us') }}
        </a>
      </div>
    </div>
  </div>

</div>
@endsection
