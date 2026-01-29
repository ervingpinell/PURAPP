{{-- resources/views/admin/tours/wizard/steps/amenities.blade.php --}}

@extends('adminlte::page')

@section('title', __('m_tours.product.wizard.steps.amenities'))

@push('css')
<style>
    /* Header mejorado */
    .amenities-header {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        color: white;
        padding: 2rem;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
    }

    .amenities-header h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 600;
    }

    .amenities-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
    }

    /* Botones de acción superior */
    .action-buttons {
        background: #2d3748;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    /* Tarjetas de amenidades */
    .amenity-card {
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        border-radius: 0.5rem;
        overflow: hidden;
        height: 100%;
    }

    .amenity-card.card-success .card-header {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        border: none;
        padding: 1rem;
    }

    .amenity-card.card-danger .card-header {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        color: white;
        border: none;
        padding: 1rem;
    }

    .amenity-card .card-header h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .amenity-card .card-body {
        background: #2d3748;
        color: #cbd5e0;
        padding: 1.25rem;
    }

    /* Hints y textos */
    .amenity-card .text-muted {
        color: #a0aec0 !important;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    /* Checkboxes personalizados */
    .amenity-row-included,
    .amenity-row-excluded {
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        transition: background-color 0.2s;
        margin-bottom: 0.5rem !important;
        position: relative;
    }

    .amenity-row-included:hover,
    .amenity-row-excluded:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    /* Fix para que los checkboxes se vean completos */
    .custom-control {
        padding-left: 1.75rem;
        min-height: 1.5rem;
        display: block;
    }

    .custom-control-label {
        color: #e2e8f0;
        cursor: pointer;
        padding-left: 0.5rem;
        display: inline-block;
        position: relative;
        margin-bottom: 0;
        vertical-align: top;
        line-height: 1.5;
    }

    .custom-control-label::before {
        position: absolute;
        top: 0.125rem;
        left: -1.75rem;
        display: block;
        width: 1.25rem;
        height: 1.25rem;
        pointer-events: none;
        content: "";
        background-color: #3a4556;
        border: 1px solid #4a5568;
        border-radius: 0.25rem;
    }

    .custom-control-label::after {
        position: absolute;
        top: 0.125rem;
        left: -1.75rem;
        display: block;
        width: 1.25rem;
        height: 1.25rem;
        content: "";
        background: no-repeat 50% / 50% 50%;
    }

    .custom-control-input {
        position: absolute;
        left: 0;
        z-index: -1;
        width: 1.25rem;
        height: 1.5rem;
        opacity: 0;
    }

    .custom-control-input:checked~.custom-control-label::before {
        background-color: #48bb78;
        border-color: #48bb78;
    }

    .custom-control-input:checked~.custom-control-label::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%23fff' d='M6.564.75l-3.59 3.612-1.538-1.55L0 4.26l2.974 2.99L8 2.193z'/%3e%3c/svg%3e");
    }

    .amenity-card.card-danger .custom-control-input:checked~.custom-control-label::before {
        background-color: #f56565;
        border-color: #f56565;
    }

    .custom-control-input:checked~.custom-control-label {
        font-weight: 600;
        color: #90cdf4;
    }

    .amenity-card.card-danger .custom-control-input:checked~.custom-control-label {
        color: #fc8181;
    }

    .custom-control-input:focus~.custom-control-label::before {
        box-shadow: 0 0 0 0.2rem rgba(66, 153, 225, 0.25);
    }

    .custom-control-input:not(:disabled):active~.custom-control-label::before {
        background-color: #4a5568;
        border-color: #667eea;
    }

    /* Alert info */
    .alert-info {
        background: rgba(66, 153, 225, 0.15);
        border: 1px solid rgba(66, 153, 225, 0.3);
        color: #90cdf4;
        border-radius: 0.375rem;
    }

    .alert-info i {
        color: #63b3ed;
    }

    .alert-info strong {
        color: #bee3f8;
    }

    /* Alert danger para errores */
    .alert-danger {
        background: rgba(245, 101, 101, 0.15);
        border: 1px solid rgba(245, 101, 101, 0.3);
        color: #fc8181;
        border-radius: 0.375rem;
    }

    /* Footer de navegación */
    .navigation-footer {
        background: #2d3748;
        border: none;
        box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.15);
        padding: 1.25rem;
        border-radius: 0.5rem;
        margin-top: 2rem;
    }

    /* Modal mejorado */
    .modal-content {
        background: #2d3748;
        border: 1px solid #4a5568;
    }

    .modal-header {
        background: #3a4556;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        color: #e2e8f0;
    }

    .modal-header .close {
        color: #e2e8f0;
        opacity: 0.8;
    }

    .modal-header .close:hover {
        opacity: 1;
    }

    .modal-body {
        background: #2d3748;
        color: #cbd5e0;
    }

    .modal-body label {
        color: #e2e8f0;
        font-weight: 600;
    }

    .modal-body input {
        background: #3a4556;
        border: 1px solid #4a5568;
        color: #e2e8f0;
    }

    .modal-body input:focus {
        background: #3a4556;
        border-color: #4299e1;
        color: #e2e8f0;
        box-shadow: 0 0 0 0.2rem rgba(66, 153, 225, 0.25);
    }

    .modal-footer {
        background: #3a4556;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Lista de amenidades scrollable si es muy larga */
    #amenities-included-list,
    #amenities-excluded-list {
        max-height: 500px;
        overflow-y: auto;
        padding-right: 0.5rem;
    }

    #amenities-included-list::-webkit-scrollbar,
    #amenities-excluded-list::-webkit-scrollbar {
        width: 8px;
    }

    #amenities-included-list::-webkit-scrollbar-track,
    #amenities-excluded-list::-webkit-scrollbar-track {
        background: #1a202c;
        border-radius: 4px;
    }

    #amenities-included-list::-webkit-scrollbar-thumb,
    #amenities-excluded-list::-webkit-scrollbar-thumb {
        background: #4a5568;
        border-radius: 4px;
    }

    #amenities-included-list::-webkit-scrollbar-thumb:hover,
    #amenities-excluded-list::-webkit-scrollbar-thumb:hover {
        background: #667eea;
    }

    /* Estilos de botones */
    .btn-primary {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        border: none;
        color: white;
        font-weight: 600;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #3182ce 0%, #2c5282 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }

    .btn-success {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        border: none;
        color: white;
        font-weight: 600;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }

    .btn-secondary {
        background: #4a5568;
        border: none;
        color: #e2e8f0;
        font-weight: 600;
    }

    .btn-secondary:hover {
        background: #5a6778;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }

    .btn-danger,
    .btn-outline-danger {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        border: none;
        color: white;
        font-weight: 600;
    }

    .btn-danger:hover,
    .btn-outline-danger:hover {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }

    /* Responsive */
    @media (max-width: 991.98px) {
        .amenities-header {
            padding: 1.5rem;
        }

        .amenities-header h1 {
            font-size: 1.5rem;
        }

        .amenity-card {
            margin-bottom: 1.5rem;
        }
    }

    @media (max-width: 767.98px) {
        .action-buttons {
            padding: 0.75rem;
        }

        .action-buttons .btn {
            flex: 1;
        }

        #amenities-included-list,
        #amenities-excluded-list {
            max-height: 400px;
        }

        .navigation-footer .d-flex {
            flex-direction: column;
            gap: 0.75rem;
        }

        .navigation-footer .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    @include('admin.products.wizard.partials.stepper')

    {{-- Header mejorado --}}
    <div class="amenities-header">
        <h1>
            <i class="fas fa-check-circle"></i>
            {{ __('m_tours.product.wizard.steps.amenities') }}
        </h1>
        <p>{{ $product->name }}</p>
    </div>

    @php
    $oldIncluded = old(
    'included_amenities',
    $product->amenities->pluck('amenity_id')->toArray()
    );

    $oldExcludedRaw = old(
    'excluded_amenities',
    $product->excludedAmenities->pluck('amenity_id')->toArray()
    );

    if (empty($oldExcludedRaw)) {
    $oldExcluded = $amenities
    ->pluck('amenity_id')
    ->reject(fn ($id) => in_array($id, $oldIncluded))
    ->values()
    ->all();
    $autoExcluded = true;
    } else {
    $oldExcluded = $oldExcludedRaw;
    $autoExcluded = false;
    }
    @endphp

    <form id="amenities-form" method="POST" action="{{ route('admin.products.wizard.store.amenities', $product) }}">
        @csrf

        {{-- Botones de acción --}}
        <div class="action-buttons">
            <a href="{{ route('admin.products.amenities.index') }}"
                class="btn btn-primary btn-sm"
                title="{{ __('m_tours.common.crud_go_to_index', [
                    'element' => __('m_tours.amenity.plural'),
               ]) }}">
                <i class="fas fa-list"></i>
                <span class="d-none d-md-inline">
                    {{ __('m_tours.common.crud_go_to_index', [
                        'element' => __('m_tours.amenity.plural'),
                  ]) }}
                </span>
            </a>

            <button type="button"
                class="btn btn-success btn-sm"
                data-toggle="modal"
                data-target="#modalQuickAmenity">
                <i class="fas fa-plus"></i>
                <span class="d-none d-md-inline">
                    {{ __('m_tours.amenity.quick_create.button') }}
                </span>
            </button>
        </div>

        <div class="row">
            {{-- Columna: Amenidades incluidas --}}
            <div class="col-md-6 mb-3">
                <div class="card amenity-card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-check"></i>
                            {{ __('m_tours.product.ui.amenities_included') }}
                        </h3>
                    </div>

                    <div class="card-body">
                        <p class="text-muted">
                            {{ __('m_tours.product.ui.amenities_included_hint') }}
                        </p>

                        <div id="amenities-included-list">
                            @foreach($amenities ?? [] as $amenity)
                            @php
                            $id = $amenity->amenity_id;
                            $isIncluded = in_array($id, $oldIncluded);
                            @endphp

                            <div class="custom-control custom-checkbox amenity-row-included"
                                data-amenity-id="{{ $id }}">
                                <input
                                    type="checkbox"
                                    class="custom-control-input amenity-included"
                                    id="included_{{ $id }}"
                                    name="included_amenities[]"
                                    value="{{ $id }}"
                                    data-amenity-id="{{ $id }}"
                                    {{ $isIncluded ? 'checked' : '' }}>
                                <label class="custom-control-label" for="included_{{ $id }}">
                                    {{ $amenity->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>

                        @error('included_amenities')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                        @error('included_amenities.*')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Columna: Amenidades NO incluidas --}}
            <div class="col-md-6 mb-3">
                <div class="card amenity-card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-times"></i>
                            {{ __('m_tours.product.ui.amenities_excluded') }}
                        </h3>
                    </div>

                    <div class="card-body">
                        <p class="text-muted">
                            {{ __('m_tours.product.ui.amenities_excluded_hint') }}
                        </p>

                        <p class="text-muted small mb-2" style="font-size: 0.85rem; opacity: 0.8;">
                            {{ __('m_tours.product.ui.amenities_excluded_auto_hint') }}
                        </p>

                        <div id="amenities-excluded-list">
                            @foreach($amenities ?? [] as $amenity)
                            @php
                            $id = $amenity->amenity_id;
                            $isIncluded = in_array($id, $oldIncluded);
                            $isExcluded = in_array($id, $oldExcluded) && ! $isIncluded;
                            $autoCheck = $autoExcluded && ! $isIncluded ? '1' : '0';
                            @endphp

                            <div class="custom-control custom-checkbox amenity-row-excluded"
                                data-amenity-id="{{ $id }}"
                                data-autocheck="{{ $autoCheck }}"
                                style="{{ $isIncluded ? 'display:none;' : '' }}">
                                <input
                                    type="checkbox"
                                    class="custom-control-input amenity-excluded"
                                    id="excluded_{{ $id }}"
                                    name="excluded_amenities[]"
                                    value="{{ $id }}"
                                    data-amenity-id="{{ $id }}"
                                    {{ $isExcluded ? 'checked' : '' }}>
                                <label class="custom-control-label" for="excluded_{{ $id }}">
                                    {{ $amenity->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>

                        @error('excluded_amenities')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                        @error('excluded_amenities.*')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Navigation Footer --}}
    <div class="card-footer navigation-footer">
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.products.wizard.step', ['product' => $product, 'step' => 1]) }}"
                class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                {{ __('m_tours.common.previous') }}
            </a>

            <div class="d-flex">
                @if($product->is_draft)
                <form action="{{ route('admin.products.wizard.delete-draft', $product) }}"
                    method="POST"
                    class="d-inline"
                    onsubmit="return confirm('{{ __('m_tours.product.wizard.confirm_cancel') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i>
                        <span class="d-none d-md-inline">{{ __('m_tours.common.cancel') }}</span>
                    </button>
                </form>
                @endif

                <button type="submit" form="amenities-form" class="btn btn-primary ml-2">
                    {{ __('m_tours.product.wizard.save_and_continue') }}
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Quick Create Amenity --}}
<div class="modal fade" id="modalQuickAmenity" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="quickAmenityForm"
            action="{{ route('admin.products.wizard.quick.amenity') }}"
            method="POST"
            class="modal-content"
            autocomplete="off">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle"></i>
                    {{ __('m_tours.amenity.quick_create.title') }}
                </h5>
                <button type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="{{ __('m_tours.common.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label for="quick_amenity_name">
                        {{ __('m_tours.amenity.quick_create.name_label') }}
                    </label>
                    <input type="text"
                        id="quick_amenity_name"
                        name="name"
                        class="form-control"
                        maxlength="255"
                        required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i>
                    {{ __('m_tours.amenity.quick_create.save') }}
                </button>
                <button type="button"
                    class="btn btn-secondary"
                    data-dismiss="modal">
                    {{ __('m_tours.amenity.quick_create.cancel') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const i18n = {
            saving: @json(__('m_tours.amenity.quick_create.saving')),
            successTitle: @json(__('m_tours.amenity.quick_create.success_title')),
            successText: @json(__('m_tours.amenity.quick_create.success_text')),
            errorTitle: @json(__('m_tours.amenity.quick_create.error_title')),
            errorGeneric: @json(__('m_tours.amenity.quick_create.error_generic')),
        };

        function showError(message) {
            const text = message || i18n.errorGeneric;
            if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: i18n.errorTitle,
                    text: text,
                });
            } else {
                alert(text);
            }
        }

        function showSuccess(message) {
            const text = message || i18n.successText;
            if (window.Swal) {
                Swal.fire({
                    icon: 'success',
                    title: i18n.successTitle,
                    text: text,
                    timer: 1600,
                    showConfirmButton: false
                });
            }
        }

        function closeModal() {
            const modalEl = document.getElementById('modalQuickAmenity');

            // Método 1: jQuery/Bootstrap (preferido si está disponible)
            if (window.$ && typeof window.$.fn.modal === 'function') {
                $('#modalQuickAmenity').modal('hide');
            } else if (window.jQuery && typeof window.jQuery.fn.modal === 'function') {
                jQuery('#modalQuickAmenity').modal('hide');
            } else {
                // Método 2: Manual (fallback)
                if (modalEl) {
                    modalEl.classList.remove('show');
                    modalEl.style.display = 'none';
                    modalEl.setAttribute('aria-hidden', 'true');
                    modalEl.removeAttribute('aria-modal');
                }

                // Limpiar body
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
                document.body.style.removeProperty('overflow');

                // Eliminar todos los backdrops
                document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
                    backdrop.remove();
                });
            }
        }

        function syncAmenity(id) {
            const included = document.querySelector('.amenity-included[data-amenity-id="' + id + '"]');
            const excludedRow = document.querySelector('.amenity-row-excluded[data-amenity-id="' + id + '"]');
            const excluded = document.querySelector('.amenity-excluded[data-amenity-id="' + id + '"]');

            if (!excludedRow || !excluded) return;

            if (included && included.checked) {
                excluded.checked = false;
                excludedRow.style.display = 'none';
            } else {
                excludedRow.style.display = '';
                if (excludedRow.dataset.autocheck === '1' && !excluded.dataset.touched) {
                    excluded.checked = true;
                }
            }
        }

        document.querySelectorAll('.amenity-included').forEach(function(input) {
            input.addEventListener('change', function() {
                const id = this.dataset.amenityId;
                syncAmenity(id);
            });
            syncAmenity(input.dataset.amenityId);
        });

        document.querySelectorAll('.amenity-excluded').forEach(function(input) {
            input.addEventListener('change', function() {
                this.dataset.touched = '1';
            });
        });

        const quickForm = document.getElementById('quickAmenityForm');
        const includedBox = document.getElementById('amenities-included-list');
        const excludedBox = document.getElementById('amenities-excluded-list');

        if (quickForm && includedBox && excludedBox) {
            quickForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const submitBtn = quickForm.querySelector('button[type="submit"]');
                const originalHtml = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>' + i18n.saving;

                try {
                    const formData = new FormData(quickForm);
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    const res = await fetch(quickForm.action, {
                        method: 'POST',
                        headers: token ? {
                            'X-CSRF-TOKEN': token
                        } : {},
                        body: formData
                    });

                    const raw = await res.text();
                    let payload = null;
                    try {
                        payload = raw ? JSON.parse(raw) : null;
                    } catch (e) {}

                    if (!res.ok) {
                        if (res.status === 422 && payload && payload.errors) {
                            const firstFieldErrors = Object.values(payload.errors)[0] || [];
                            const firstMessage = firstFieldErrors[0] || i18n.errorGeneric;
                            showError(firstMessage);
                        } else if (payload && payload.message) {
                            showError(payload.message);
                        } else {
                            showError();
                        }
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalHtml;
                        return;
                    }

                    const id = payload.id;
                    const name = payload.name;
                    const msg = payload.message || null;

                    const includedHtml = `
                    <div class="custom-control custom-checkbox amenity-row-included"
                         data-amenity-id="${id}">
                        <input
                            type="checkbox"
                            class="custom-control-input amenity-included"
                            id="included_${id}"
                            name="included_amenities[]"
                            value="${id}"
                            data-amenity-id="${id}"
                            checked>
                        <label class="custom-control-label" for="included_${id}">
                            ${name}
                        </label>
                    </div>
                `;
                    includedBox.insertAdjacentHTML('beforeend', includedHtml);

                    const excludedHtml = `
                    <div class="custom-control custom-checkbox amenity-row-excluded"
                         data-amenity-id="${id}"
                         data-autocheck="0"
                         style="display:none;">
                        <input
                            type="checkbox"
                            class="custom-control-input amenity-excluded"
                            id="excluded_${id}"
                            name="excluded_amenities[]"
                            value="${id}"
                            data-amenity-id="${id}">
                        <label class="custom-control-label" for="excluded_${id}">
                            ${name}
                        </label>
                    </div>
                `;
                    excludedBox.insertAdjacentHTML('beforeend', excludedHtml);

                    const newIncluded = includedBox.querySelector('.amenity-included[data-amenity-id="' + id + '"]');
                    const newExcluded = excludedBox.querySelector('.amenity-excluded[data-amenity-id="' + id + '"]');

                    if (newIncluded) {
                        newIncluded.addEventListener('change', function() {
                            syncAmenity(id);
                        });
                        syncAmenity(id);
                    }

                    if (newExcluded) {
                        newExcluded.addEventListener('change', function() {
                            this.dataset.touched = '1';
                        });
                    }

                    // Resetear form y cerrar modal
                    quickForm.reset();
                    closeModal();

                    // Mostrar success después de cerrar el modal
                    setTimeout(() => {
                        showSuccess(msg);
                    }, 300);

                } catch (error) {
                    console.error(error);
                    showError();
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            });
        }
    });
</script>
@endpush
