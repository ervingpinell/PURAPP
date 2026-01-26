@extends('adminlte::page')

@section('title', __('reviews.providers.product_mapping_title', ['name' => $provider->name]))

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>
        <i class="fas fa-map"></i>
        {{ __('reviews.providers.product_mapping_title', ['name' => $provider->name]) }}
    </h1>
    <a href="{{ route('admin.review-providers.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('reviews.providers.back') }}
    </a>
</div>
@stop

@section('content')
@if(session('ok'))
<div class="alert alert-success alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{ session('ok') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{ session('error') }}
</div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-link"></i>
            {{ __('reviews.providers.product_mappings') }}
        </h3>
    </div>
    <div class="card-body">
        @if($mappings && count($mappings) > 0)
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th style="width: 50%">{{ __('reviews.providers.tour') }}</th>
                        <th style="width: 35%">{{ __('reviews.providers.product_code') }}</th>
                        <th style="width: 15%" class="text-right">{{ __('reviews.providers.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mappings as $mapping)
                    <tr id="mapping-{{ $mapping['product_id'] }}">
                        <td>
                            <strong>{{ $mapping['tour_name'] }}</strong>
                            <br>
                            <small class="text-muted">ID: {{ $mapping['product_id'] }}</small>
                        </td>
                        <td>
                            <span class="product-code-display">
                                <code>{{ $mapping['product_code'] }}</code>
                            </span>
                            <form class="product-code-edit d-none"
                                action="{{ route('admin.review-providers.product-map.update', [$provider, $mapping['product_id']]) }}"
                                method="POST">
                                @csrf
                                @method('PUT')
                                <div class="input-group input-group-sm">
                                    <input type="text"
                                        name="product_code"
                                        class="form-control"
                                        value="{{ $mapping['product_code'] }}"
                                        required>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-cancel-edit">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </td>
                        <td class="text-right">
                            <button type="button" class="btn btn-sm btn-info btn-edit-mapping"
                                data-tour-id="{{ $mapping['product_id'] }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.review-providers.product-map.destroy', [$provider, $mapping['product_id']]) }}"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirm('{{ __('reviews.providers.confirm_delete_mapping') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            {{ __('reviews.providers.no_mappings') }}
        </div>
        @endif
    </div>
</div>

{{-- Add New Mapping Card --}}
<div class="card">
    <div class="card-header bg-success">
        <h3 class="card-title">
            <i class="fas fa-plus"></i>
            {{ __('reviews.providers.add_mapping') }}
        </h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.review-providers.product-map.store', $provider) }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="product_id">{{ __('reviews.providers.select_tour') }}</label>
                        <select name="product_id"
                            id="product_id"
                            class="form-control select2"
                            required
                            style="width: 100%">
                            <option value="">{{ __('reviews.providers.select_tour_placeholder') }}</option>
                            @foreach($allTours as $tour)
                            <option value="{{ $tour['id'] }}">{{ $tour['name'] }}</option>
                            @endforeach
                        </select>
                        @error('product_id')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="product_code">{{ __('reviews.providers.product_code') }}</label>
                        <input type="text"
                            name="product_code"
                            id="product_code"
                            class="form-control"
                            placeholder="{{ __('reviews.providers.product_code_placeholder') }}"
                            required>
                        @error('product_code')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-plus"></i>
                            {{ __('reviews.providers.add_mapping') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Help Card --}}
<div class="card card-outline card-info">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-question-circle"></i>
            {{ __('reviews.providers.help_title') }}
        </h3>
    </div>
    <div class="card-body">
        <p>{{ __('reviews.providers.help_text') }}</p>
        <ul>
            <li>{{ __('reviews.providers.help_step_1') }}</li>
            <li>{{ __('reviews.providers.help_step_2') }}</li>
            <li>{{ __('reviews.providers.help_step_3') }}</li>
        </ul>
    </div>
</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: '{{ __('
            reviews.providers.select_tour_placeholder ') }}',
            allowClear: true
        });

        // Edit mapping inline
        $('.btn-edit-mapping').on('click', function() {
            const tourId = $(this).data('tour-id');
            const row = $('#mapping-' + tourId);

            row.find('.product-code-display').addClass('d-none');
            row.find('.product-code-edit').removeClass('d-none');
            row.find('.product-code-edit input').focus();
        });

        // Cancel edit
        $('.btn-cancel-edit').on('click', function() {
            const form = $(this).closest('form');
            const row = form.closest('tr');

            row.find('.product-code-display').removeClass('d-none');
            row.find('.product-code-edit').addClass('d-none');
        });
    });
</script>
@stop