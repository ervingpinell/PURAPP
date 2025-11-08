@extends('adminlte::page')

@section('title', __('customer_categories.ui.page_title_create'))

@section('content_header')
    <h1>{{ __('customer_categories.ui.header_create') }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <form action="{{ route('admin.customer_categories.store') }}" method="POST">
                    @csrf

                    <div class="card-body">
                        @include('admin.customer_categories.partials.form', ['mode' => 'create'])
                    </div>

                    <div class="card-footer d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('customer_categories.buttons.save') }}
                        </button>
                        <a href="{{ route('admin.customer_categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> {{ __('customer_categories.buttons.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            @include('admin.customer_categories.partials.help')
        </div>
    </div>
@stop
