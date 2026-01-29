{{-- resources/views/admin/tours/prices/index.blade.php --}}
{{-- NUEVA VERSIÓN: Precios agrupados por periodos de fechas --}}

@extends('adminlte::page')

@section('title', __('m_tours.prices.ui.page_title', ['name' => $product->name]))

@push('css')
<style>
    /* Dark theme compatible styles */
    .product-info-header {
        color: white;
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .pricing-period-card {
        margin-bottom: 1.5rem;
        border: 1px solid #454d55;
        border-radius: 0.5rem;
        overflow: hidden;
        background: #343a40;
    }

    .period-header {
        color: white;
        padding: 1rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .period-header.default {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    }

    .period-dates {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .period-dates>div {
        display: flex;
        flex-direction: column;
        min-width: 180px;
    }

    .period-dates input[type="date"] {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }

    .period-dates input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }

    .period-actions {
        display: flex;
        gap: 0.5rem;
    }

    .categories-table {
        margin: 0;
        background: #343a40;
    }

    .categories-table thead {
        background: #454d55;
    }

    .categories-table th {
        font-size: 0.8125rem;
        font-weight: 600;
        padding: 0.5rem;
        border-bottom: 2px solid #454d55;
        color: #c2c7d0;
    }

    .categories-table td {
        padding: 0.5rem;
        vertical-align: middle;
        border-top: 1px solid #454d55;
        color: #c2c7d0;
    }

    .categories-table tbody tr:hover {
        background: #3a4047;
    }

    .categories-table input[type="number"],
    .categories-table input[type="text"] {
        font-size: 0.875rem;
        padding: 0.375rem 0.5rem;
        background: #3d444b;
        border: 1px solid #6c757d;
        color: #fff;
    }

    .categories-table input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .add-category-section {
        padding: 1rem 1.25rem;
        background: #3d444b;
        border-top: 1px solid #454d55;
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .add-category-section select {
        background: #343a40;
        border: 1px solid #6c757d;
        color: #c2c7d0;
        flex: 1;
    }

    .btn-add-period {
        color: white !important;
        border: none;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        border-radius: 0.5rem;
        transition: all 0.3s;
    }

    .btn-add-period:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        color: white !important;
    }

    .input-group-text {
        background: #3d444b;
        border: 1px solid #6c757d;
        color: #c2c7d0;
    }

    .price-changed {
        background: #00bc8c !important;
        border-color: #00bc8c !important;
    }

    .save-indicator {
        display: none;
        color: #28a745;
        font-size: 0.875rem;
    }

    .save-indicator.show {
        display: inline-block;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Product Info Header --}}
    <div class="product-info-header bg-secondary">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-2" style="font-size: 1.75rem; font-weight: 600;">
                    <i class="fas fa-dollar-sign mr-2"></i>
                    {{ $product->name }} - {{ __('m_tours.prices.ui.configured_title') }}
                </h1>
                <p class="mb-0" style="opacity: 0.9;">
                    {{ __('m_tours.prices.ui.configured_count') }}: {{ $product->prices->count() }} |
                    {{ __('m_tours.prices.ui.active_count') }}: {{ $product->prices->where('is_active', true)->count() }}
                </p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-light">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('m_tours.prices.ui.back_to_tours') }}
            </a>
        </div>
    </div>

    {{-- Alerts flash --}}
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

    {{-- Add Period Button + Bulk Save + Taxes --}}
    <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-add-period bg-success" id="add-period-btn">
                <i class="fas fa-plus-circle mr-2"></i>
                {{ __('m_tours.product.pricing.add_period') ?? 'Agregar Periodo de Precios' }}
            </button>
            <button type="button" class="btn btn-primary" id="bulk-save-all-btn" style="display: none;">
                <i class="fas fa-save mr-2"></i>
                {{ __('m_tours.prices.alerts.save_all_changes') ?? 'Guardar Todos los Cambios' }}
                <span class="badge badge-light ml-2" id="changes-count">0</span>
            </button>
        </div>
        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#manageTaxesModal">
            <i class="fas fa-percentage mr-2"></i>
            {{ __('taxes.title') }}
            @if($product->taxes->isNotEmpty())
            <span class="badge badge-light ml-2">{{ $product->taxes->count() }}</span>
            @endif
        </button>
    </div>

    {{-- Pricing Periods Container --}}
    <div id="pricing-periods-container">
        @forelse($pricingPeriods as $periodIndex => $period)
        <div class="card pricing-period-card" data-period-index="{{ $periodIndex }}">
            <div class="period-header bg-secondary {{ $period['is_default'] ? 'default' : '' }}">
                <div class="period-dates">
                    @if(!$period['is_default'])
                    <div>
                        <label class="mb-1" style="font-size: 0.75rem; opacity: 0.9;">
                            {{ __('m_tours.product.pricing.valid_from') }}
                        </label>
                        <input
                            type="date"
                            class="form-control form-control-sm period-date-input"
                            data-field="valid_from"
                            value="{{ $period['valid_from'] }}">
                    </div>
                    <div>
                        <label class="mb-1" style="font-size: 0.75rem; opacity: 0.9;">
                            {{ __('m_tours.product.pricing.valid_until') }}
                        </label>
                        <input
                            type="date"
                            class="form-control form-control-sm period-date-input"
                            data-field="valid_until"
                            value="{{ $period['valid_until'] }}">
                    </div>
                    <div style="min-width: 200px;">
                        <label class="mb-1" style="font-size: 0.75rem; opacity: 0.9;">
                            {{ __('m_tours.product.pricing.period_name') ?? 'Nombre (Opcional)' }}
                        </label>
                        <input
                            type="text"
                            class="form-control form-control-sm period-date-input"
                            data-field="label"
                            value="{{ $period['label'] !== \App\Models\TourPrice::getPeriodLabel($period['valid_from'], $period['valid_until']) ? $period['label'] : '' }}"
                            placeholder="{{ __('m_tours.product.pricing.period_name_placeholder') ?? 'Ej. Temporada Alta' }}">
                    </div>
                    @else
                    <h5 class="mb-0">
                        <i class="fas fa-infinity mr-2"></i>
                        {{ $period['label'] }}
                    </h5>
                    @endif
                </div>
                <div class="period-actions">
                    @if(!$period['is_default'])
                    <button type="button" class="btn btn-sm btn-success save-period-dates-btn">
                        <i class="fas fa-save"></i> {{ __('m_general.save') }}
                    </button>
                    @endif
                    <button type="button" class="btn btn-sm btn-danger remove-period-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <div class="card-body p-0">
                <table class="table table-sm table-hover categories-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 20%;">{{ __('m_tours.product.pricing.category') }}</th>
                            <th style="width: 12%;">{{ __('m_tours.product.pricing.age_range') }}</th>
                            <th style="width: 15%;">{{ __('m_tours.product.pricing.price_usd') }}</th>
                            <th style="width: 12%;">{{ __('m_tours.product.pricing.min_quantity') }}</th>
                            <th style="width: 12%;">{{ __('m_tours.product.pricing.max_quantity') }}</th>
                            <th style="width: 10%;" class="text-center">{{ __('m_tours.product.pricing.active') }}</th>
                            <th style="width: 19%;" class="text-center">{{ __('m_tours.prices.table.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="categories-tbody">
                        @foreach($period['categories'] as $cat)
                        <tr
                            data-price-id="{{ $cat['price_id'] }}"
                            data-category-id="{{ $cat['id'] }}">
                            <td><strong>{{ $cat['name'] }}</strong></td>
                            <td><small class="text-muted">{{ $cat['age_range'] }}</small></td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input
                                        type="number"
                                        class="form-control price-input"
                                        value="{{ $cat['price'] }}"
                                        data-field="price"
                                        step="0.01"
                                        min="0">
                                </div>
                            </td>
                            <td>
                                <input
                                    type="number"
                                    class="form-control form-control-sm quantity-input"
                                    value="{{ $cat['min_quantity'] }}"
                                    data-field="min_quantity"
                                    min="0">
                            </td>
                            <td>
                                <input
                                    type="number"
                                    class="form-control form-control-sm quantity-input"
                                    value="{{ $cat['max_quantity'] }}"
                                    data-field="max_quantity"
                                    min="0">
                            </td>
                            <td class="text-center">
                                <input
                                    type="checkbox"
                                    class="active-toggle"
                                    data-field="is_active"
                                    {{ $cat['is_active'] ? 'checked' : '' }}>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-success save-price-btn" style="display: none;">
                                    <i class="fas fa-save"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger remove-category-btn">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <span class="save-indicator ml-2">
                                    <i class="fas fa-check-circle"></i> {{ __('m_tours.prices.alerts.saved') ?? 'Guardado' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="add-category-section">
                <select class="form-control form-control-sm add-category-select">
                    <option value="">
                        {{ __('m_tours.product.pricing.choose_category_placeholder') }}
                    </option>
                    @foreach($availableCategories as $category)
                    <option
                        value="{{ $category->category_id }}"
                        data-name="{{ $category->getTranslatedName() ?? $category->name }}"
                        data-age-range="{{ $category->age_range ?? ($category->age_from . '-' . $category->age_to) }}">
                        {{ $category->getTranslatedName() ?? $category->name }}
                        ({{ $category->age_range ?? ($category->age_from . '-' . $category->age_to) }})
                    </option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-sm btn-primary add-category-btn">
                    <i class="fas fa-plus mr-1"></i> {{ __('m_tours.product.pricing.add_button') }}
                </button>
            </div>
        </div>
        @empty
        <div class="card" style="background: #343a40; border: 1px solid #454d55;">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times fa-3x mb-3" style="opacity: 0.5; color: #6c757d;"></i>
                <h5 style="color: #6c757d;">
                    {{ __('m_tours.product.pricing.no_periods') ?? 'No hay periodos de precios definidos' }}
                </h5>
                <p class="text-muted">
                    {{ __('m_tours.product.pricing.click_add_period') ?? 'Haz clic en "Agregar Periodo de Precios" para comenzar' }}
                </p>
            </div>
        </div>
        @endforelse
    </div>
</div>

{{-- Modal: Manage Taxes --}}
<div class="modal fade" id="manageTaxesModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.products.prices.update-taxes', $product) }}" method="POST">
                @csrf
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-percentage"></i> {{ __('taxes.title') }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    @if($taxes->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> {{ __('m_general.no_records') }}
                    </div>
                    @else
                    <div class="list-group">
                        @foreach($taxes as $tax)
                        <label class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="custom-control custom-checkbox">
                                <input
                                    class="custom-control-input"
                                    type="checkbox"
                                    name="taxes[]"
                                    id="tax_{{ $tax->tax_id }}"
                                    value="{{ $tax->tax_id }}"
                                    {{ $product->taxes->contains($tax->tax_id) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="tax_{{ $tax->tax_id }}">
                                    <strong>{{ $tax->name }}</strong><br>
                                    <small class="text-muted">
                                        {{ $tax->type == 'percentage'
                                                    ? number_format($tax->rate, 2) . '%'
                                                    : '$' . number_format($tax->rate, 2) }}
                                    </small>
                                </label>
                            </div>
                            <span class="badge {{ $tax->is_inclusive ? 'badge-success' : 'badge-warning' }}">
                                {{ $tax->is_inclusive ? __('taxes.included') : __('taxes.not_included') }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        {{ __('m_general.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save"></i> {{ __('m_general.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function() {
        const productId = {
            {
                $product-> product_id
            }
        };
        const csrfToken = '{{ csrf_token() }}';

        // Toast genérico
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });

        // ==========
        // TRACK CAMBIOS EN INPUTS + BULK SAVE
        // ==========
        let changedRows = new Set();

        function updateBulkSaveButton() {
            const count = changedRows.size;
            const $bulkBtn = $('#bulk-save-all-btn');
            const $countBadge = $('#changes-count');

            if (count > 0) {
                $bulkBtn.show();
                $countBadge.text(count);
            } else {
                $bulkBtn.hide();
            }
        }

        $(document).on('input change', '.price-input, .quantity-input', function() {
            const $row = $(this).closest('tr');
            const priceId = $row.data('price-id');
            const $saveBtn = $row.find('.save-price-btn');

            $(this).addClass('price-changed');
            $saveBtn.show();
            changedRows.add(priceId);
            updateBulkSaveButton();
        });

        // ==========
        // GUARDAR PRECIO INDIVIDUAL
        // ==========
        $(document).on('click', '.save-price-btn', function() {
            const $btn = $(this);
            const $row = $btn.closest('tr');
            const priceId = $row.data('price-id');

            const data = {
                price: $row.find('[data-field="price"]').val(),
                min_quantity: $row.find('[data-field="min_quantity"]').val(),
                max_quantity: $row.find('[data-field="max_quantity"]').val(),
                is_active: $row.find('[data-field="is_active"]').is(':checked') ? 1 : 0,
                _token: csrfToken,
                _method: 'PUT'
            };

            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: `/admin/tours/${productId}/prices/${priceId}`,
                method: 'POST',
                data: data,
                success: function() {
                    $row.find('.price-input, .quantity-input').removeClass('price-changed');
                    $btn.hide();
                    $row.find('.save-indicator').addClass('show');
                    changedRows.delete(priceId);
                    updateBulkSaveButton();
                    Toast.fire({
                        icon: 'success',
                        title: @json(__('m_tours.prices.alerts.price_updated'))
                    });
                    setTimeout(() => $row.find('.save-indicator').removeClass('show'), 2000);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: @json(__('m_tours.prices.alerts.error_title')),
                        text: xhr.responseJSON?.message || @json(__('m_tours.prices.alerts.error_unexpected'))
                    });
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="fas fa-save"></i>');
                }
            });
        });

        // ==========
        // BULK SAVE ALL CHANGES
        // ==========
        $('#bulk-save-all-btn').on('click', function() {
            const $btn = $(this);
            const priceIds = Array.from(changedRows);

            if (priceIds.length === 0) return;

            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Guardando...');

            let completed = 0;
            let errors = 0;

            priceIds.forEach(priceId => {
                const $row = $(`tr[data-price-id="${priceId}"]`);
                const data = {
                    price: $row.find('[data-field="price"]').val(),
                    min_quantity: $row.find('[data-field="min_quantity"]').val(),
                    max_quantity: $row.find('[data-field="max_quantity"]').val(),
                    is_active: $row.find('[data-field="is_active"]').is(':checked') ? 1 : 0,
                    _token: csrfToken,
                    _method: 'PUT'
                };

                $.ajax({
                    url: `/admin/tours/${productId}/prices/${priceId}`,
                    method: 'POST',
                    data: data,
                    success: function() {
                        $row.find('.price-input, .quantity-input').removeClass('price-changed');
                        $row.find('.save-price-btn').hide();
                        changedRows.delete(priceId);
                    },
                    error: function() {
                        errors++;
                    },
                    complete: function() {
                        completed++;
                        if (completed === priceIds.length) {
                            updateBulkSaveButton();
                            $btn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i>{{ __('
                                m_tours.prices.alerts.save_all_changes ') ?? '
                                Guardar Todos los Cambios ' }}');

                            if (errors === 0) {
                                Toast.fire({
                                    icon: 'success',
                                    title: `${priceIds.length} precios actualizados`
                                });
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Guardado parcial',
                                    text: `${priceIds.length - errors} de ${priceIds.length} precios guardados correctamente`
                                });
                            }
                        }
                    }
                });
            });
        });

        // ==========
        // TOGGLE ACTIVO / INACTIVO
        // ==========
        $(document).on('change', '.active-toggle', function() {
            const $checkbox = $(this);
            const $row = $checkbox.closest('tr');
            const priceId = $row.data('price-id');

            $.ajax({
                url: `/admin/tours/${productId}/prices/${priceId}/toggle`,
                method: 'POST',
                data: {
                    _token: csrfToken
                },
                success: function() {
                    $row.find('.save-indicator').addClass('show');
                    Toast.fire({
                        icon: 'success',
                        title: @json(__('m_tours.prices.alerts.status_updated'))
                    });
                    setTimeout(() => $row.find('.save-indicator').removeClass('show'), 2000);
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: @json(__('m_tours.prices.alerts.error_title')),
                        text: @json(__('m_tours.prices.alerts.error_unexpected'))
                    });
                    // revert checkbox
                    $checkbox.prop('checked', !$checkbox.is(':checked'));
                }
            });
        });

        // ==========
        // ELIMINAR CATEGORÍA (PRECIO)
        // ==========
        $(document).on('click', '.remove-category-btn', function() {
            const $row = $(this).closest('tr');
            const priceId = $row.data('price-id');

            Swal.fire({
                title: @json(__('m_tours.prices.alerts.confirm_delete_price_title')),
                text: @json(__('m_tours.prices.alerts.confirm_delete_price_text')),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: @json(__('m_tours.prices.alerts.confirm_yes_delete')),
                cancelButtonText: @json(__('m_tours.prices.alerts.confirm_cancel'))
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: `/admin/tours/${productId}/prices/${priceId}`,
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        _method: 'DELETE'
                    },
                    success: function() {
                        $row.fadeOut(300, function() {
                            $(this).remove();
                            changedRows.delete(priceId);
                            updateBulkSaveButton();
                        });
                        Toast.fire({
                            icon: 'success',
                            title: @json(__('m_tours.prices.alerts.price_deleted'))
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: @json(__('m_tours.prices.alerts.error_title')),
                            text: @json(__('m_tours.prices.alerts.error_delete_price'))
                        });
                    }
                });
            });
        });

        // ==========
        // AGREGAR CATEGORÍA A UN PERIODO
        // ==========
        $(document).on('click', '.add-category-btn', function() {
            const $period = $(this).closest('.pricing-period-card');
            const $select = $period.find('.add-category-select');
            const categoryId = $select.val();

            if (!categoryId) {
                Swal.fire({
                    icon: 'warning',
                    title: @json(__('m_tours.prices.alerts.attention')),
                    text: @json(__('m_tours.prices.alerts.select_category_first'))
                });
                return;
            }

            // Check if already exists
            if ($period.find(`tr[data-category-id="${categoryId}"]`).length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: @json(__('m_tours.prices.alerts.duplicate_category_title')),
                    text: @json(__('m_tours.prices.alerts.duplicate_category_text'))
                });
                return;
            }

            const $option = $select.find('option:selected');
            const validFrom = $period.find('[data-field="valid_from"]').val() || null;
            const validUntil = $period.find('[data-field="valid_until"]').val() || null;
            const label = $period.find('[data-field="label"]').val() || null;

            $.ajax({
                url: `/admin/tours/${productId}/prices`,
                method: 'POST',
                data: {
                    category_id: categoryId,
                    price: 0,
                    min_quantity: 0,
                    max_quantity: 12,
                    is_active: 0,
                    valid_from: validFrom,
                    valid_until: validUntil,
                    label: label,
                    _token: csrfToken
                },
                success: function() {
                    Swal.fire({
                        icon: 'success',
                        title: @json(__('m_tours.prices.alerts.price_created'))
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: @json(__('m_tours.prices.alerts.error_title')),
                        text: @json(__('m_tours.prices.alerts.error_add_category'))
                    });
                }
            });
        });

        // ==========
        // GUARDAR FECHAS DEL PERIODO (BULK UPDATE)
        // ==========
        $(document).on('click', '.save-period-dates-btn', function() {
            const $period = $(this).closest('.pricing-period-card');
            const validFrom = $period.find('[data-field="valid_from"]').val();
            const validUntil = $period.find('[data-field="valid_until"]').val();
            const label = $period.find('[data-field="label"]').val();
            const priceIds = [];

            $period.find('tr[data-price-id]').each(function() {
                priceIds.push($(this).data('price-id'));
            });

            if (priceIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: @json(__('m_tours.prices.alerts.attention')),
                    text: @json(__('m_tours.prices.alerts.no_categories'))
                });
                return;
            }

            $.ajax({
                url: `/admin/tours/${productId}/prices/bulk-update`,
                method: 'POST',
                data: {
                    price_ids: priceIds,
                    valid_from: validFrom,
                    valid_until: validUntil,
                    label: label,
                    _token: csrfToken
                },
                success: function() {
                    Toast.fire({
                        icon: 'success',
                        title: @json(__('m_tours.prices.alerts.period_updated'))
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: @json(__('m_tours.prices.alerts.error_title')),
                        text: @json(__('m_tours.prices.alerts.error_update_period'))
                    });
                }
            });
        });

        // ==========
        // ELIMINAR PERIODO COMPLETO
        // ==========
        $(document).on('click', '.remove-period-btn', function() {
            const $period = $(this).closest('.pricing-period-card');
            const priceIds = [];

            $period.find('tr[data-price-id]').each(function() {
                priceIds.push($(this).data('price-id'));
            });

            Swal.fire({
                title: @json(__('m_tours.prices.alerts.confirm_delete_period_title')),
                text: @json(__('m_tours.prices.alerts.confirm_delete_period_text')),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: @json(__('m_tours.prices.alerts.confirm_yes_delete')),
                cancelButtonText: @json(__('m_tours.prices.alerts.confirm_cancel'))
            }).then((result) => {
                if (!result.isConfirmed) return;

                if (priceIds.length === 0) {
                    $period.fadeOut(300, function() {
                        $(this).remove();
                    });
                    Toast.fire({
                        icon: 'success',
                        title: @json(__('m_tours.prices.alerts.period_deleted'))
                    });
                    return;
                }

                let completed = 0;
                priceIds.forEach(priceId => {
                    $.ajax({
                        url: `/admin/tours/${productId}/prices/${priceId}`,
                        method: 'POST',
                        data: {
                            _token: csrfToken,
                            _method: 'DELETE'
                        },
                        complete: function() {
                            completed++;
                            if (completed === priceIds.length) {
                                $period.fadeOut(300, function() {
                                    $(this).remove();
                                });
                                Toast.fire({
                                    icon: 'success',
                                    title: @json(__('m_tours.prices.alerts.period_deleted'))
                                });
                            }
                        }
                    });
                });
            });
        });

        // ==========
        // AGREGAR NUEVO PERIODO
        // ==========
        $('#add-period-btn').on('click', function() {
            Swal.fire({
                title: 'Crear Nuevo Periodo',
                html: `
                    <div class="form-group text-left">
                        <label>Válido desde</label>
                        <input type="date" id="new-period-from" class="form-control">
                    </div>
                    <div class="form-group text-left">
                        <label>Válido hasta</label>
                        <input type="date" id="new-period-until" class="form-control">
                    </div>
                    <div class="form-group text-left">
                        <label>Nombre del periodo (opcional)</label>
                        <input type="text" id="new-period-label" class="form-control" placeholder="Ej. Temporada Alta">
                    </div>
                    <div class="form-group text-left">
                        <label>Selecciona una categoría inicial</label>
                        <select id="new-period-category" class="form-control">
                            <option value="">-- Selecciona --</option>
                            @foreach($availableCategories as $category)
                            <option value="{{ $category->category_id }}">
                                {{ $category->getTranslatedName() ?? $category->name }}
                                ({{ $category->age_range ?? ($category->age_from . '-' . $category->age_to) }})
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Puedes agregar más categorías después</small>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Crear Periodo',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const from = document.getElementById('new-period-from').value;
                    const until = document.getElementById('new-period-until').value;
                    const label = document.getElementById('new-period-label').value;
                    const categoryId = document.getElementById('new-period-category').value;

                    if (!from || !until) {
                        Swal.showValidationMessage('Debes especificar ambas fechas');
                        return false;
                    }

                    if (from > until) {
                        Swal.showValidationMessage('La fecha de inicio debe ser anterior a la fecha de fin');
                        return false;
                    }

                    if (!categoryId) {
                        Swal.showValidationMessage('Debes seleccionar al menos una categoría');
                        return false;
                    }

                    return {
                        from,
                        until,
                        label,
                        categoryId
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const {
                        from,
                        until,
                        label,
                        categoryId
                    } = result.value;

                    // Crear el primer precio del periodo
                    $.ajax({
                        url: `/admin/tours/${productId}/prices`,
                        method: 'POST',
                        data: {
                            category_id: categoryId,
                            price: 0,
                            min_quantity: 0,
                            max_quantity: 12,
                            is_active: 0,
                            valid_from: from,
                            valid_until: until,
                            label: label || null,
                            _token: csrfToken
                        },
                        success: function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Periodo creado',
                                text: 'El periodo ha sido creado. Ahora puedes configurar los precios.',
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'No se pudo crear el periodo'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
