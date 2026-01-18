@extends('adminlte::page')

@section('title', __('adminlte::adminlte.email_preview.page_title'))

@section('content_header')
<h1>
    <i class="fas fa-envelope"></i> {{ __('adminlte::adminlte.email_preview.page_title') }}
</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>{{ __('adminlte::adminlte.email_preview.title') }}</strong><br>
            {{ __('adminlte::adminlte.email_preview.description') }}
        </div>
    </div>
</div>

<div class="row">
    @foreach($emailTypes as $key => $category)
    <div class="col-md-6">
        <div class="card card-outline card-{{ $key === 'auth' ? 'danger' : ($key === 'admin' ? 'warning' : ($key === 'reviews' ? 'success' : 'primary')) }}">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="{{ $category['icon'] ?? 'fas fa-envelope' }}"></i> {{ $category['label'] }}
                </h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($category['items'] as $type => $label)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.email-preview.show', $type) }}" target="_blank" class="text-decoration-none">
                            <i class="fas fa-external-link-alt text-muted mr-2"></i>
                            {{ $label }}
                        </a>
                        <div class="d-flex align-items-center">
                            <a href="{{ route('admin.email-preview.show', $type) }}?locale=es" target="_blank" class="badge badge-warning mr-1" title="Español">ES</a>
                            <a href="{{ route('admin.email-preview.show', $type) }}?locale=en" target="_blank" class="badge badge-primary mr-1" title="English">EN</a>
                            {{-- 
                            <a href="{{ route('admin.email-preview.show', $type) }}?locale=fr" target="_blank" class="badge badge-info mr-1" title="Français">FR</a>
                            <a href="{{ route('admin.email-preview.show', $type) }}?locale=de" target="_blank" class="badge badge-secondary mr-1" title="Deutsch">DE</a>
                            <a href="{{ route('admin.email-preview.show', $type) }}?locale=pt" target="_blank" class="badge badge-success" title="Português">PT</a>
                            --}}
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row mt-3">
    {{-- Quick Links --}}
    <div class="col-md-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tools"></i> {{ __('adminlte::adminlte.email_preview.tools_title') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <a href="/telescope/mail" target="_blank" class="btn btn-info btn-block">
                            <i class="fas fa-satellite-dish"></i> {{ __('adminlte::adminlte.email_preview.view_telescope') }}
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-cog"></i> {{ __('adminlte::adminlte.email_preview.config_button') }}
                        </a>
                    </div>
                </div>
                <div class="alert alert-warning mb-0">
                    <small>
                        <i class="fas fa-lightbulb"></i>
                        <strong>{{ __('adminlte::adminlte.email_preview.tip_title') }}</strong> {{ __('adminlte::adminlte.email_preview.tip_text') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle"></i> {{ __('adminlte::adminlte.email_preview.config_title') }}</h3>
            </div>
            <div class="card-body">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>{{ __('adminlte::adminlte.email_preview.table.purpose') }}</th>
                            <th>{{ __('adminlte::adminlte.email_preview.table.address') }}</th>
                            <th>{{ __('adminlte::adminlte.email_preview.table.env_var') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>{{ __('adminlte::adminlte.email_preview.table.from') }}</strong></td>
                            <td><code>{{ config('mail.from.address') }}</code></td>
                            <td><code>MAIL_FROM_ADDRESS</code></td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('adminlte::adminlte.email_preview.table.reply_to') }}</strong></td>
                            <td><code>{{ config('mail.reply_to.address') }}</code></td>
                            <td><code>MAIL_REPLY_TO_ADDRESS</code></td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('adminlte::adminlte.email_preview.table.admin_notify') }}</strong></td>
                            <td><code>{{ config('mail.notifications.address') }}</code></td>
                            <td><code>MAIL_NOTIFY_ADDRESS</code></td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('adminlte::adminlte.email_preview.table.booking_notify') }}</strong></td>
                            <td><code>{{ setting('email.booking_notifications') ?: 'Not configured' }}</code></td>
                            <td><code>setting: email.booking_notifications</code></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .list-group-item {
        border-left: none;
        border-right: none;
        padding: 15px 20px;
        background-color: transparent !important;
    }

    .list-group-item a {
        display: flex;
        align-items: center;
        color: #ffffff !important;
        text-decoration: none;
        transition: all 0.2s ease;
        padding: 10px 15px;
        font-size: 15px;
        font-weight: 500;
        border-radius: 5px;
    }

    .list-group-item a:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: #4fc3f7 !important;
        transform: translateX(5px);
    }

    .list-group-item a i {
        font-size: 14px;
        margin-right: 12px;
        min-width: 20px;
    }

    .card-header h3 {
        font-size: 16px;
        font-weight: 600;
    }
</style>
@stop