@extends('adminlte::page')

@php
$t = $i18n ?? [];
$title = $t['title'] ?? __('m_tours.image.ui.page_title_pick');
$heading = $t['heading'] ?? __('m_tours.image.ui.page_heading');
$choose = $t['choose'] ?? __('m_tours.image.ui.choose_tour');
$search_placeholder = $t['search_placeholder'] ?? __('m_tours.image.ui.search_placeholder');
$search_button = $t['search_button'] ?? __('m_tours.image.ui.search_button');
$no_results = $t['no_results'] ?? __('m_tours.image.ui.no_results');
$cover_alt = $t['cover_alt'] ?? __('m_tours.image.ui.cover_alt');
$manage_text = $t['manage'] ?? __('m_tours.image.ui.manage_images');

$idField = $idField ?? 'id';
$nameField = $nameField ?? 'name';
$coverAccessor = $coverAccessor ?? 'cover_url';
$manageRoute = $manageRoute ?? '';
@endphp

@section('title', $title)

@section('content_header')
<h1>
  {{ $heading }}
  <small class="text-muted">{{ $choose }}</small>
</h1>
@stop

@section('content')
<form method="GET" class="mb-3">
  <div class="input-group">
    <input type="text" name="q" class="form-control" placeholder="{{ $search_placeholder }}" value="{{ $q ?? '' }}">
    <button class="btn btn-primary" type="submit">
      <i class="fas fa-search"></i> {{ $search_button }}
    </button>
  </div>
</form>

@if(($items->count() ?? 0) === 0)
<div class="alert alert-info">{{ $no_results }}</div>
@else
<div class="row g-3">
  @foreach($items as $item)
  @php
  $cover = data_get($item, $coverAccessor) ?? asset('images/volcano.png');
  $id = data_get($item, $idField);
  $name = data_get($item, $nameField);
  @endphp

  <div class="col-6 col-sm-4 col-md-3 col-xl-2">
    <div class="card h-100 d-flex flex-column">
      <img src="{{ $cover }}" class="card-img-top" alt="{{ $cover_alt }}">
      <div class="card-body p-2 d-flex flex-column">
        <div class="small fw-bold mb-1">#{{ $id }} — {{ $name }}</div>

        @if(isset($requiredPermission))
        @can($requiredPermission)
        <a href="{{ route($manageRoute, $id) }}" class="btn btn-success btn-sm mt-auto">
          {{ $manage_text }}
        </a>
        @else
        <button class="btn btn-secondary btn-sm mt-auto" disabled>
          {{ $manage_text }} <i class="fas fa-lock ml-1"></i>
        </button>
        @endcan
        @else
        <a href="{{ route($manageRoute, $id) }}" class="btn btn-success btn-sm mt-auto">
          {{ $manage_text }}
        </a>
        @endif

      </div>
    </div>
  </div>
  @endforeach
</div>

<div class="mt-3">
  {{ $items->withQueryString()->links() }}
</div>
@endif
@stop