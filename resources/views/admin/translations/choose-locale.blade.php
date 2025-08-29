@extends('adminlte::page')

@section('title', __('m_config.translations.choose_locale_title'))

@section('content_header')
    <h1><i class="fas fa-language"></i> {{ __('m_config.translations.choose_locale_title') }}</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <p class="mb-4">{{ __('m_config.translations.choose_locale_hint') }}</p>

        <div class="row">
            @foreach (['en','pt','fr','de','es'] as $code)
                <div class="col-md-3 mb-3">
                    <a href="{{ route('admin.translations.edit', ['type' => $type, 'id' => $item->getKey(), 'locale' => $code]) }}"
                       class="btn btn-outline-success w-100">
                        <i class="fas fa-flag me-1"></i> {{ __('m_config.translations.languages.' . $code) }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
@stop
