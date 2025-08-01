<h1 class="fw-bold">{{ $tour->translated_name }}</h1>
<p class="text-muted">{{ $tour->tourType->name ?? '' }}</p>
<h2>{{ __('adminlte::adminlte.overview') }}</h2>
<p>{!! nl2br(e($tour->translated_overview)) !!}</p>
