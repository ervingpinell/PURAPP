@extends('adminlte::page')

@section('title', __('customer_categories.ui.page_title_edit'))

@section('content_header')
    <h1>{{ __('customer_categories.ui.header_edit', ['name' => $category->getTranslatedName()]) }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <form action="{{ route('admin.customer_categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        @include('admin.customer_categories.partials.form', ['category' => $category, 'mode' => 'edit'])
                    </div>

                    <div class="card-footer d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('customer_categories.buttons.update') }}
                        </button>
                        <a href="{{ route('admin.customer_categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('customer_categories.buttons.back') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            @include('admin.customer_categories.partials.help')

            <div class="card card-secondary mt-3 mt-md-0">
                <div class="card-header">
                    <h3 class="card-title">{{ __('customer_categories.ui.info_card_title') }}</h3>
                </div>
                <div class="card-body">
                    <p><strong>{{ __('customer_categories.info.id') }}</strong> {{ $category->category_id }}</p>
                    <p><strong>{{ __('customer_categories.info.created') }}</strong> {{ $category->created_at->format(__('customer_categories.info.date_fmt')) }}</p>
                    <p><strong>{{ __('customer_categories.info.updated') }}</strong> {{ $category->updated_at->format(__('customer_categories.info.date_fmt')) }}</p>
                </div>
            </div>
        </div>
    </div>
@stop
