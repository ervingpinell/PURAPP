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
                                    <th style="width: 150px">Precio Final</th>
                                    <th style="width: 100px">{{ __('m_tours.prices.table.min') }}</th>
                                    <th style="width: 100px">{{ __('m_tours.prices.table.max') }}</th>
                                    <th style="width: 120px" class="text-center">{{ __('m_tours.prices.table.status') }}</th>
                                    <th style="width: 80px" class="text-center">{{ __('m_tours.prices.table.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tour->prices as $index => $price)
                                @php
                                $taxIncluded = (bool) config('settings.taxes.included', false);
                                $breakdown = $price->calculateTaxBreakdown(1, $taxIncluded);
                                @endphp
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
                                        <input type="hidden"
                                            name="prices[{{ $index }}][tour_price_id]"
                                            value="{{ $price->tour_price_id }}">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">$</span>
                                            <input type="number"
                                                class="form-control form-control-sm price-input"
                                                name="prices[{{ $index }}][price]"
                                                value="{{ number_format($price->price, 2, '.', '') }}"
                                                step="0.01"
                                                min="0"
                                                data-index="{{ $index }}"
                                                required>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong class="text-success">${{ number_format($breakdown['total'], 2) }}</strong>
                                            @if($tour->taxes->isNotEmpty())
                                            <small class="text-muted">
                                                @php
                                                $hasInclusive = collect($breakdown['taxes'])->contains('included', true);
                                                $hasExclusive = collect($breakdown['taxes'])->contains('included', false);
                                                @endphp

                                                @if($hasInclusive && !$hasExclusive)
                                                <span class="badge badge-success badge-sm">{{ __('taxes.included') }}</span>
                                                @elseif($hasExclusive && !$hasInclusive)
                                                <span class="badge badge-warning badge-sm">+ Tax</span>
                                                @elseif($hasInclusive && $hasExclusive)
                                                <span class="badge badge-info badge-sm">{{ __('taxes.mixed') }}</span>
                                                @endif
                                            </small>
                                            @endif
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
        {{-- Quick Actions --}}
        <div class="card card-outline card-primary mb-3">
            <div class="card-header">
                <h3 class="card-title">{{ __('m_general.quick_actions') }}</h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#manageTaxesModal">
                        <i class="fas fa-percentage"></i> {{ __('taxes.title') }}
                        @if($tour->taxes->isNotEmpty())
                        <span class="badge bg-light text-dark ms-2">{{ $tour->taxes->count() }}</span>
                        @endif
                    </button>

                    @if($availableCategories->isNotEmpty())
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="fas fa-plus"></i> {{ __('m_tours.prices.ui.add_category') }}
                    </button>
                    @endif
                </div>
            </div>
        </div>

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

{{-- Modal: Manage Taxes --}}
<div class="modal fade" id="manageTaxesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.tours.prices.update-taxes', $tour) }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-percentage"></i> {{ __('taxes.title') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted">{{ __('taxes.messages.select_taxes') }}</p>

                    @if($taxes->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> {{ __('m_general.no_records') }}
                    </div>
                    @else
                    <div class="list-group">
                        @foreach($taxes as $tax)
                        <label class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                            <div class="form-check">
                                <input class="form-check-input tax-checkbox me-2" type="checkbox" name="taxes[]"
                                    value="{{ $tax->tax_id }}"
                                    id="modal_tax_{{ $tax->tax_id }}"
                                    data-type="{{ $tax->type }}"
                                    data-rate="{{ $tax->rate }}"
                                    {{ $tour->taxes->contains($tax->tax_id) ? 'checked' : '' }}>
                                <div>
                                    <div class="fw-bold">{{ $tax->name }}</div>
                                    <small class="text-muted">
                                        <code>{{ $tax->code }}</code> -
                                        {{ $tax->type == 'percentage' ? number_format($tax->rate, 2) . '%' : '$' . number_format($tax->rate, 2) }}
                                    </small>
                                </div>
                            </div>
                            <span class="badge {{ $tax->is_inclusive ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $tax->is_inclusive ? __('taxes.included') : __('taxes.not_included') }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('m_general.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __('m_general.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Add Category --}}
@if($availableCategories->isNotEmpty())
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.tours.prices.store', $tour) }}" method="POST" id="addCategoryFormModal">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-plus"></i> {{ __('m_tours.prices.ui.add_category') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="modal_category_id">{{ __('m_tours.prices.forms.category') }} <span class="text-danger">*</span></label>
                        <select name="category_id" id="modal_category_id" class="form-control" required>
                            <option value="">{{ __('m_tours.prices.forms.select_placeholder') }}</option>
                            @foreach($availableCategories as $category)
                            <option value="{{ $category->category_id }}">
                                {{ $category->name }} ({{ $category->age_range }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="modal_price">{{ __('m_tours.prices.forms.price_usd') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="price" id="modal_price" class="form-control" step="0.01" min="0" value="0" required>
                        </div>
                        <small class="form-text text-muted">{{ __('m_tours.prices.forms.create_disabled_hint') }}</small>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="modal_min_quantity">{{ __('m_tours.prices.forms.min') }} <span class="text-danger">*</span></label>
                                <input type="number" name="min_quantity" id="modal_min_quantity" class="form-control" min="0" max="255" value="0" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="modal_max_quantity">{{ __('m_tours.prices.forms.max') }} <span class="text-danger">*</span></label>
                                <input type="number" name="max_quantity" id="modal_max_quantity" class="form-control" min="0" max="255" value="12" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('m_general.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus"></i> {{ __('m_tours.prices.forms.add') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

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
    // Tax Breakdown Preview Calculator
    // ============================
    function calculateTaxBreakdown() {
        const priceInput = document.getElementById('preview-price');
        const taxIncludedCheckbox = document.getElementById('tax-included-preview');
        const breakdownSubtotal = document.getElementById('breakdown-subtotal');
        const breakdownTaxes = document.getElementById('breakdown-taxes');
        const breakdownTotal = document.getElementById('breakdown-total');

        if (!priceInput || !taxIncludedCheckbox) return;

        const basePrice = parseFloat(priceInput.value) || 0;
        const taxIncluded = taxIncludedCheckbox.checked;

        // Get selected taxes
        const selectedTaxes = [];
        document.querySelectorAll('.tax-checkbox:checked').forEach(checkbox => {
            selectedTaxes.push({
                type: checkbox.dataset.type,
                rate: parseFloat(checkbox.dataset.rate) || 0,
                name: checkbox.parentElement.querySelector('label').textContent.trim().split('(')[0].trim()
            });
        });

        let subtotal, taxAmountTotal, total;
        const taxDetails = [];

        if (taxIncluded) {
            // Tax is INCLUDED in the base price
            total = basePrice;
            subtotal = basePrice;
            taxAmountTotal = 0;

            selectedTaxes.forEach(tax => {
                let taxAmount = 0;
                if (tax.type === 'percentage') {
                    const divisor = 1 + (tax.rate / 100);
                    const currentSubtotal = subtotal / divisor;
                    taxAmount = subtotal - currentSubtotal;
                    subtotal = currentSubtotal;
                } else {
                    taxAmount = tax.rate;
                    subtotal -= taxAmount;
                }
                taxAmountTotal += taxAmount;
                taxDetails.push({
                    name: tax.name,
                    amount: taxAmount,
                    rate: tax.rate,
                    type: tax.type
                });
            });
        } else {
            // Tax is NOT INCLUDED - add on top
            subtotal = basePrice;
            taxAmountTotal = 0;

            selectedTaxes.forEach(tax => {
                let taxAmount = 0;
                if (tax.type === 'percentage') {
                    taxAmount = subtotal * (tax.rate / 100);
                } else {
                    taxAmount = tax.rate;
                }
                taxAmountTotal += taxAmount;
                taxDetails.push({
                    name: tax.name,
                    amount: taxAmount,
                    rate: tax.rate,
                    type: tax.type
                });
            });

            total = subtotal + taxAmountTotal;
        }

        // Update display
        if (breakdownSubtotal) breakdownSubtotal.textContent = '$' + subtotal.toFixed(2);
        if (breakdownTotal) breakdownTotal.textContent = '$' + total.toFixed(2);

        if (breakdownTaxes) {
            breakdownTaxes.innerHTML = '';
            taxDetails.forEach(tax => {
                const rateDisplay = tax.type === 'percentage' ? tax.rate.toFixed(2) + '%' : '$' + tax.rate.toFixed(2);
                breakdownTaxes.innerHTML += `
                    <div class="d-flex justify-content-between mb-1 text-muted small">
                        <span class="ml-2">${tax.name} (${rateDisplay}):</span>
                        <span>$${tax.amount.toFixed(2)}</span>
                    </div>
                `;
            });
        }
    }

    // Attach event listeners for tax breakdown
    document.getElementById('preview-price')?.addEventListener('input', calculateTaxBreakdown);
    document.getElementById('tax-included-preview')?.addEventListener('change', calculateTaxBreakdown);
    document.querySelectorAll('.tax-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', calculateTaxBreakdown);
    });

    // Initial calculation
    calculateTaxBreakdown();

    // ============================
    // Modal delete: setear action dinámico (Bootstrap 5)
    // ============================
    document.getElementById('confirmDeleteModal')?.addEventListener('show.bs.modal', function(e) {
        const trigger = e.relatedTarget;
        if (!trigger) return;
        const action = trigger.getAttribute('data-action');
        document.getElementById('confirmDeleteForm')?.setAttribute('action', action || '');
    });
</script>
@stop
