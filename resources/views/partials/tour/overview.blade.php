<h2 class="fw-bold mb-1" style="color: var(--primary-dark);">{{ $tour->translated_name }}</h2>
<p class="text-muted mb-2" style="font-size: 0.95rem;">{{ $tour->tourType->name ?? '' }}</p>

<h4 class="fw-semibold mb-2">{{ __('adminlte::adminlte.overview') }}</h4>
<div style="font-size: 0.92rem; color: #333; line-height: 1.6;">
  {!! nl2br(e($tour->translated_overview)) !!}
</div>
