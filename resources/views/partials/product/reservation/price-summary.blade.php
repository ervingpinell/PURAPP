{{-- resources/views/partials/product/reservation/price-summary.blade.php --}}
{{-- Resumen simple de precio mínimo --}}

@php
// Obtener precio mínimo de todas las categorías activas
$minPrice = null;
$categoryWithMinPrice = null;

if (isset($categoriesData) && !empty($categoriesData)) {
    foreach ($categoriesData as $cat) {
        // Buscar el precio más bajo entre todas las reglas de esta categoría
        if (isset($cat['rules']) && !empty($cat['rules'])) {
            foreach ($cat['rules'] as $rule) {
                $price = (float) $rule['price'];
                if ($price > 0 && (is_null($minPrice) || $price < $minPrice)) {
                    $minPrice = $price;
                    $categoryWithMinPrice = $cat['name'];
                }
            }
        }
    }
}
@endphp

@if($minPrice)
<div class="price-summary mb-3">
    <h5 class="mb-2">{{ __('adminlte::adminlte.price') }}</h5>
    <div class="d-flex align-items-baseline gap-2">
        <span class="h4 text-success mb-0">${{ number_format($minPrice, 2) }}</span>
        <small class="text-muted">{{ __('adminlte::adminlte.per_person_from') }}</small>
    </div>
    @if($categoryWithMinPrice)
        <small class="text-muted">{{ $categoryWithMinPrice }}</small>
    @endif
</div>
@endif
