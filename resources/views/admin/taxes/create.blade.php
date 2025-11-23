@extends('adminlte::page')

@section('title', __('taxes.create'))

@section('content_header')
<h1>{{ __('taxes.create') }}</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.taxes.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">{{ __('taxes.fields.name') }}</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="code">{{ __('taxes.fields.code') }}</label>
                <input type="text" name="code" id="code" class="form-control" value="{{ old('code') }}" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="rate">{{ __('taxes.fields.rate') }}</label>
                        <input type="number" step="0.01" name="rate" id="rate" class="form-control" value="{{ old('rate') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type">{{ __('taxes.fields.type') }}</label>
                        <select name="type" id="type" class="form-control">
                            <option value="percentage">{{ __('taxes.types.percentage') }}</option>
                            <option value="fixed">{{ __('taxes.types.fixed') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="apply_to">{{ __('taxes.fields.apply_to') }}</label>
                <select name="apply_to" id="apply_to" class="form-control">
                    <option value="subtotal">{{ __('taxes.apply_to_options.subtotal') }}</option>
                    <option value="total">{{ __('taxes.apply_to_options.total') }}</option>
                    <option value="per_person">{{ __('taxes.apply_to_options.per_person') }}</option>
                </select>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="is_inclusive" name="is_inclusive" value="1" {{ old('is_inclusive') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_inclusive">{{ __('taxes.fields.is_inclusive') }}</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                    <label class="custom-control-label" for="is_active">{{ __('taxes.fields.is_active') }}</label>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">{{ __('m_general.save') }}</button>
                <a href="{{ route('admin.taxes.index') }}" class="btn btn-secondary">{{ __('m_general.cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@stop