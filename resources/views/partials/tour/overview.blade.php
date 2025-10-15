<h1 class="fw-bold mb-1">{{ $tour->translated_name }}</h1>
<p class="text-muted mb-3 small">{{ $tour->tourType->name ?? '' }}</p>

<h2 class="section-subtitle mb-2">{{ __('adminlte::adminlte.overview') }}</h2>
<div>
  {!! nl2br(e($tour->translated_overview)) !!}
</div>
