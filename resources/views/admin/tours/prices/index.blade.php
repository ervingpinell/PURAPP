{{-- resources/views/admin/tours/prices/index.blade.php --}}
@extends('adminlte::page')

@section('title', __('m_tours.prices.ui.page_title', ['name' => $tour->name]))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>{{ __('m_tours.prices.ui.header_title', ['name' => $tour->name]) }}</h1>
        <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('m_tours.prices.ui.back_to_tours') }}
        </a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('m_tours.common.cancel') }}"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('m_tours.common.cancel') }}"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('m_tours.common.cancel') }}"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            {{-- Formulario de actualización masiva --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('m_tours.prices.ui.configured_title') }}</h3>
                </div>

                <form action="{{ route('admin.tours.prices.bulk-update', $tour) }}" method="POST" id="bulkUpdateForm">
                    @csrf

                    <div class="card-body p-0">
                        @if($tour->prices->isEmpty())
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                <p>{{ __('m_tours.prices.ui.empty_title') }}</p>
                                <p class="small">{{ __('m_tours.prices.ui.empty_hint') }}</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('m_tours.prices.table.category') }}</th>
                                            <th>{{ __('m_tours.prices.table.age_range') }}</th>
                                            <th style="width: 150px">{{ __('m_tours.prices.table.price_usd') }}</th>
                                            <th style="width: 100px">{{ __('m_tours.prices.table.min') }}</th>
                                            <th style="width: 100px">{{ __('m_tours.prices.table.max') }}</th>
                                            <th style="width: 120px" class="text-center">{{ __('m_tours.prices.table.status') }}</th>
                                            <th style="width: 80px" class="text-center">{{ __('m_tours.prices.table.action') }}</th>
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
                                                    <span class="badge bg-info">
                                                        {{ $price->category->age_range }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <input type="hidden"
                                                           name="prices[{{ $index }}][category_id]"
                                                           value="{{ $price->category_id }}">
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">$</span>
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
                                                    <input type="hidden" name="prices[{{ $index }}][is_active]" value="0">
                                                    <div class="form-check form-switch d-inline-flex justify-content-center">
                                                        <input
                                                            class="form-check-input is-active-toggle"
                                                            type="checkbox"
                                                            role="switch"
                                                            id="active_{{ $price->tour_price_id }}"
                                                            name="prices[{{ $index }}][is_active]"
                                                            value="1"
                                                            data-index="{{ $index }}"
                                                            {{ $price->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label ms-1" for="active_{{ $price->tour_price_id }}"></label>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                            class="btn btn-sm btn-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#confirmDeleteModal"
                                                            data-action="{{ route('admin.tours.prices.destroy', ['tour' => $tour->tour_id, 'price' => $price->getKey()]) }}"
                                                            title="{{ __('m_tours.prices.modal.delete_tooltip') }}">
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
                        <div class="card-footer d-flex align-items-center gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('m_tours.prices.ui.save_changes') }}
                            </button>
                            <span class="ms-2 text-muted small">
                                <i class="fas fa-info-circle"></i>
                                {{ __('m_tours.prices.ui.auto_disable_note') }}
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
                        <h3 class="card-title">{{ __('m_tours.prices.ui.add_category') }}</h3>
                    </div>

                    <form action="{{ route('admin.tours.prices.store', $tour) }}" method="POST" id="addCategoryForm">
                        @csrf

                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="category_id">{{ __('m_tours.prices.forms.category') }}</label>
                                <select name="category_id" id="category_id" class="form-control" required>
                                    <option value="">{{ __('m_tours.prices.forms.select_placeholder') }}</option>
                                    @foreach($availableCategories as $category)
                                        <option value="{{ $category->category_id }}">
                                            {{ $category->name }} ({{ $category->age_range }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="price">{{ __('m_tours.prices.forms.price_usd') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
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
                                    {{ __('m_tours.prices.forms.create_disabled_hint') }}
                                </small>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="min_quantity">{{ __('m_tours.prices.forms.min') }}</label>
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
                                        <label for="max_quantity">{{ __('m_tours.prices.forms.max') }}</label>
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
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-plus"></i> {{ __('m_tours.prices.forms.add') }}
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="card card-info">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <p><strong>{{ __('m_tours.prices.ui.all_assigned_title') }}</strong></p>
                        <p class="small text-muted">{{ __('m_tours.prices.ui.all_assigned_text') }}</p>
                    </div>
                </div>
            @endif

            {{-- Información --}}
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> {{ __('m_tours.prices.ui.info_title') }}
                    </h3>
                </div>
                <div class="card-body">
                    <p><strong>{{ __('m_tours.prices.ui.tour_label') }}:</strong> {{ $tour->name }}</p>
                    <p><strong>{{ __('m_tours.prices.ui.configured_count') }}:</strong> {{ $tour->prices->count() }}</p>
                    <p><strong>{{ __('m_tours.prices.ui.active_count') }}:</strong> {{ $tour->prices->where('is_active', true)->count() }}</p>
                    <hr>
                    <h5>{{ __('m_tours.prices.ui.fields_title') }}</h5>
                    <ul class="small">
                        <li><strong>{{ __('m_tours.prices.ui.field_price') }}:</strong> {{ __('m_tours.prices.forms.price_usd') }}</li>
                        <li><strong>{{ __('m_tours.prices.ui.field_min') }}:</strong> {{ __('m_tours.prices.forms.min') }}</li>
                        <li><strong>{{ __('m_tours.prices.ui.field_max') }}:</strong> {{ __('m_tours.prices.forms.max') }}</li>
                        <li><strong>{{ __('m_tours.prices.ui.field_status') }}:</strong> {{ __('m_tours.prices.table.active') }}/{{ __('m_tours.prices.table.inactive') }}</li>
                    </ul>
                    <hr>
                    <h5>{{ __('m_tours.prices.ui.rules_title') }}</h5>
                    <ul class="small">
                        <li>{{ __('m_tours.prices.ui.rule_min_le_max') }}</li>
                        <li>{{ __('m_tours.prices.ui.rule_zero_disable') }}</li>
                        <li>{{ __('m_tours.prices.ui.rule_only_active') }}</li>
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
                        <h5 class="modal-title">{{ __('m_tours.prices.modal.delete_title') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('m_tours.common.cancel') }}"></button>
                    </div>
                    <div class="modal-body">
                        {{ __('m_tours.prices.modal.delete_text') }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_tours.prices.modal.cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('m_tours.prices.modal.delete') }}</button>
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
        maxInput.setCustomValidity(@json(__('m_tours.prices.js.max_ge_min')));
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
            // Tooltip/ayuda (opcional)
            checkbox.title = @json(__('m_tours.prices.js.auto_disabled_tooltip'));
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
        alert(@json(__('m_tours.prices.js.fix_errors')));
        return false;
    }
});

// ============================
// Modal delete: setear action dinámico (Bootstrap 5)
// ============================
document.getElementById('confirmDeleteModal')?.addEventListener('show.bs.modal', function (e) {
    const trigger = e.relatedTarget;
    if (!trigger) return;
    const action = trigger.getAttribute('data-action');
    document.getElementById('confirmDeleteForm')?.setAttribute('action', action || '');
});
</script>
@stop
