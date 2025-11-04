{{-- resources/views/admin/tours/prices/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Precios - ' . $tour->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Precios: {{ $tour->name }}</h1>
        <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Tours
        </a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            {{-- Formulario de actualización masiva --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Categorías y Precios Configurados</h3>
                </div>

                <form action="{{ route('admin.tours.prices.bulk-update', $tour) }}" method="POST" id="bulkUpdateForm">
                    @csrf

                    <div class="card-body p-0">
                        @if($tour->prices->isEmpty())
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                <p>No hay categorías configuradas para este tour.</p>
                                <p class="small">Usa el formulario de la derecha para agregar categorías.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Categoría</th>
                                            <th>Rango Edad</th>
                                            <th style="width: 150px">Precio (USD)</th>
                                            <th style="width: 100px">Mín</th>
                                            <th style="width: 100px">Máx</th>
                                            <th style="width: 100px" class="text-center">Estado</th>
                                            <th style="width: 80px" class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tour->prices as $index => $price)
                                            <tr>
                                                <td>
                                                    <strong>{{ $price->category->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <code>{{ $price->category->slug }}</code>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        {{ $price->category->age_range }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <input type="hidden"
                                                           name="prices[{{ $index }}][category_id]"
                                                           value="{{ $price->category_id }}">

                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="number"
                                                               class="form-control price-input"
                                                               name="prices[{{ $index }}][price]"
                                                               value="{{ number_format($price->price, 2, '.', '') }}"
                                                               step="0.01"
                                                               min="0"
                                                               data-index="{{ $index }}"
                                                               required>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number"
                                                           class="form-control form-control-sm min-quantity"
                                                           name="prices[{{ $index }}][min_quantity]"
                                                           value="{{ $price->min_quantity }}"
                                                           min="0"
                                                           max="255"
                                                           data-index="{{ $index }}"
                                                           required>
                                                </td>
                                                <td>
                                                    <input type="number"
                                                           class="form-control form-control-sm max-quantity"
                                                           name="prices[{{ $index }}][max_quantity]"
                                                           value="{{ $price->max_quantity }}"
                                                           min="0"
                                                           max="255"
                                                           data-index="{{ $index }}"
                                                           required>
                                                </td>
                                                <td class="text-center">
                                                    <div class="custom-control custom-switch">
                                                        <input type="hidden"
                                                               name="prices[{{ $index }}][is_active]"
                                                               value="0">
                                                        <input type="checkbox"
                                                               class="custom-control-input is-active-toggle"
                                                               id="active_{{ $price->tour_price_id }}"
                                                               name="prices[{{ $index }}][is_active]"
                                                               value="1"
                                                               data-index="{{ $index }}"
                                                               {{ $price->is_active ? 'checked' : '' }}>
                                                        <label class="custom-control-label"
                                                               for="active_{{ $price->tour_price_id }}">
                                                        </label>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                            class="btn btn-sm btn-danger"
                                                            data-toggle="modal"
                                                            data-target="#confirmDeleteModal"
                                                            data-action="{{ route('admin.tours.prices.destroy', ['tour' => $tour->tour_id, 'price' => $price->getKey()]) }}"
                                                            title="Eliminar categoría">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    @if($tour->prices->isNotEmpty())
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <span class="ml-2 text-muted small">
                                <i class="fas fa-info-circle"></i>
                                Los precios en $0 se desactivan automáticamente
                            </span>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Formulario para agregar nueva categoría --}}
            @if($availableCategories->isNotEmpty())
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Agregar Categoría</h3>
                    </div>

                    <form action="{{ route('admin.tours.prices.store', $tour) }}" method="POST" id="addCategoryForm">
                        @csrf

                        <div class="card-body">
                            <div class="form-group">
                                <label for="category_id">Categoría</label>
                                <select name="category_id" id="category_id" class="form-control" required>
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($availableCategories as $category)
                                        <option value="{{ $category->category_id }}">
                                            {{ $category->name }} ({{ $category->age_range }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="price">Precio (USD)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number"
                                           name="price"
                                           id="price"
                                           class="form-control"
                                           step="0.01"
                                           min="0"
                                           value="0"
                                           required>
                                </div>
                                <small class="form-text text-muted">
                                    Si el precio es $0, la categoría se creará desactivada
                                </small>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="min_quantity">Mínimo</label>
                                        <input type="number"
                                               name="min_quantity"
                                               id="min_quantity"
                                               class="form-control"
                                               min="0"
                                               max="255"
                                               value="0"
                                               required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="max_quantity">Máximo</label>
                                        <input type="number"
                                               name="max_quantity"
                                               id="max_quantity"
                                               class="form-control"
                                               min="0"
                                               max="255"
                                               value="12"
                                               required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-plus"></i> Agregar
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="card card-info">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p><strong>Todas las categorías están asignadas</strong></p>
                        <p class="small text-muted">No hay más categorías disponibles para agregar a este tour.</p>
                    </div>
                </div>
            @endif

            {{-- Información --}}
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Información
                    </h3>
                </div>
                <div class="card-body">
                    <p><strong>Tour:</strong> {{ $tour->name }}</p>
                    <p><strong>Categorías configuradas:</strong> {{ $tour->prices->count() }}</p>
                    <p><strong>Categorías activas:</strong> {{ $tour->prices->where('is_active', true)->count() }}</p>
                    <hr>
                    <h5>Campos</h5>
                    <ul class="small">
                        <li><strong>Precio:</strong> Precio en USD para esta categoría</li>
                        <li><strong>Mín:</strong> Cantidad mínima requerida por reserva</li>
                        <li><strong>Máx:</strong> Cantidad máxima permitida por reserva</li>
                        <li><strong>Estado:</strong> Activa/Inactiva en formularios públicos</li>
                    </ul>
                    <hr>
                    <h5>Reglas</h5>
                    <ul class="small">
                        <li>El mínimo debe ser menor o igual al máximo</li>
                        <li>Precios en $0 se desactivan automáticamente</li>
                        <li>Solo categorías activas aparecen en el frontend</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de confirmación global (fuera de cualquier form) --}}
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="confirmDeleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Eliminar Categoría</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        ¿Eliminar esta categoría de este tour?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
// ============================
// Validación de cantidades min/max
// ============================
function validateQuantities(minInput, maxInput) {
    const min = parseInt(minInput.value) || 0;
    const max = parseInt(maxInput.value) || 0;

    if (max < min) {
        maxInput.setCustomValidity('El máximo debe ser mayor o igual al mínimo');
        return false;
    } else {
        maxInput.setCustomValidity('');
        return true;
    }
}

// Formulario de agregar categoría
const minInput = document.getElementById('min_quantity');
const maxInput = document.getElementById('max_quantity');

if (minInput && maxInput) {
    minInput.addEventListener('input', () => validateQuantities(minInput, maxInput));
    maxInput.addEventListener('input', () => validateQuantities(minInput, maxInput));
}

// Validación en formulario bulk
document.querySelectorAll('.min-quantity').forEach(minEl => {
    const index = minEl.getAttribute('data-index');
    const maxEl = document.querySelector(`.max-quantity[data-index="${index}"]`);

    if (maxEl) {
        minEl.addEventListener('input', () => validateQuantities(minEl, maxEl));
        maxEl.addEventListener('input', () => validateQuantities(minEl, maxEl));
    }
});

// Auto-desactivar si precio es 0
document.querySelectorAll('.price-input').forEach(priceInput => {
    priceInput.addEventListener('change', function() {
        const index = this.getAttribute('data-index');
        const checkbox = document.querySelector(`.is-active-toggle[data-index="${index}"]`);

        if (parseFloat(this.value) === 0 && checkbox) {
            checkbox.checked = false;
            $(checkbox).closest('.custom-switch').find('.custom-control-label')
                .attr('title', 'Precio en $0 - Desactivado automáticamente');
        }
    });
});

// Validación antes de submit del formulario bulk
document.getElementById('bulkUpdateForm')?.addEventListener('submit', function(e) {
    let hasErrors = false;

    document.querySelectorAll('.min-quantity').forEach(minEl => {
        const index = minEl.getAttribute('data-index');
        const maxEl = document.querySelector(`.max-quantity[data-index="${index}"]`);

        if (maxEl && !validateQuantities(minEl, maxEl)) {
            hasErrors = true;
        }
    });

    if (hasErrors) {
        e.preventDefault();
        alert('Por favor corrige los errores en las cantidades mínimas y máximas');
        return false;
    }
});

// ============================
// Modal delete: setear action dinámico
// ============================
$('#confirmDeleteModal').on('show.bs.modal', function (e) {
    const button = $(e.relatedTarget);
    const action = button.data('action');
    $('#confirmDeleteForm').attr('action', action);
});
</script>
@stop
