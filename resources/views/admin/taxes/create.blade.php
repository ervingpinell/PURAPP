@extends('adminlte::page')

@section('title', __('taxes.create'))

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>{{ __('taxes.create') }}</h1>
    <a href="{{ route('admin.taxes.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('m_general.back') }}
    </a>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <form action="{{ route('admin.taxes.store') }}" method="POST">
                @csrf

                <div class="card-body">
                    <div class="form-group">
                        <label for="name">{{ __('taxes.fields.name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="code">{{ __('taxes.fields.code') }} <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" required>
                        <small class="form-text text-muted">Código único en mayúsculas (ej: IVA, SALES_TAX)</small>
                        @error('code')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">{{ __('taxes.fields.type') }} <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-control @error('type') is-invalid @enderror">
                                    <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>{{ __('taxes.types.percentage') }}</option>
                                    <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>{{ __('taxes.types.fixed') }}</option>
                                </select>
                                @error('type')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rate">{{ __('taxes.fields.rate') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="rate" id="rate" class="form-control @error('rate') is-invalid @enderror" value="{{ old('rate', '0.00') }}" min="0" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="rate-suffix">%</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted" id="rate-help">Para porcentaje: ej 13 = 13%</small>
                                @error('rate')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="apply_to">{{ __('taxes.fields.apply_to') }} <span class="text-danger">*</span></label>
                        <select name="apply_to" id="apply_to" class="form-control @error('apply_to') is-invalid @enderror">
                            <option value="subtotal" {{ old('apply_to') == 'subtotal' ? 'selected' : '' }}>{{ __('taxes.apply_to_options.subtotal') }}</option>
                            <option value="total" {{ old('apply_to') == 'total' ? 'selected' : '' }}>{{ __('taxes.apply_to_options.total') }}</option>
                            <option value="per_person" {{ old('apply_to') == 'per_person' ? 'selected' : '' }}>{{ __('taxes.apply_to_options.per_person') }}</option>
                        </select>
                        @error('apply_to')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="description">{{ __('m_general.description') }}</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                        @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="sort_order">{{ __('taxes.fields.sort_order') }}</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', 0) }}" min="0">
                        @error('sort_order')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_inclusive" name="is_inclusive" value="1" {{ old('is_inclusive') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_inclusive">{{ __('taxes.fields.is_inclusive') }}</label>
                        </div>
                        <small class="form-text text-muted">Si está marcado, este impuesto se considera incluido en el precio base.</small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">{{ __('taxes.fields.is_active') }}</label>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __('m_general.save') }}
                    </button>
                    <a href="{{ route('admin.taxes.index') }}" class="btn btn-secondary">{{ __('m_general.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    // Update rate suffix based on type
    document.getElementById('type').addEventListener('change', function() {
        const suffix = document.getElementById('rate-suffix');
        const help = document.getElementById('rate-help');
        if (this.value === 'percentage') {
            suffix.textContent = '%';
            help.textContent = 'Para porcentaje: ej 13 = 13%';
        } else {
            suffix.textContent = '$';
            help.textContent = 'Monto fijo en dólares';
        }
    });

    // Auto-uppercase code
    document.getElementById('code').addEventListener('input', function() {
        this.value = this.value.toUpperCase().replace(/\s+/g, '_');
    });
</script>
@stop