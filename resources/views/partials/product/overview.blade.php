<h1 class="fw-bold mb-1">{{ $product->translated_name }}</h1>
<p class="text-muted mb-3 small">{{ $product->productType->name ?? '' }}</p>

<h2 class="section-subtitle mb-2">{{ __('adminlte::adminlte.overview') }}</h2>
<div>
  {!! nl2br(e($product->translated_overview)) !!}
</div>
