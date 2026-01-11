@extends('adminlte::page')

@section('title', 'Email Previews')

@section('content_header')
<h1>
    <i class="fas fa-envelope"></i> Email Template Previews
</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Email Preview System</strong><br>
            Preview all email templates without sending them. This uses real booking data when available, or sample data for demonstration.
        </div>
    </div>
</div>

<div class="row">
    {{-- Customer Emails --}}
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user"></i> Customer Emails</h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($emailTypes['customer'] as $type => $label)
                    <li class="list-group-item">
                        <a href="{{ route('admin.email-preview.show', $type) }}" target="_blank" class="text-decoration-none">
                            <i class="fas fa-external-link-alt text-muted mr-2"></i>
                            {{ $label }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- Admin Emails --}}
    <div class="col-md-6">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-shield"></i> Admin Emails</h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($emailTypes['admin'] as $type => $label)
                    <li class="list-group-item">
                        <a href="{{ route('admin.email-preview.show', $type) }}" target="_blank" class="text-decoration-none">
                            <i class="fas fa-external-link-alt text-muted mr-2"></i>
                            {{ $label }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    {{-- Other Emails --}}
    <div class="col-md-6">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-envelope-open-text"></i> Other Emails</h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($emailTypes['other'] as $type => $label)
                    <li class="list-group-item">
                        <a href="{{ route('admin.email-preview.show', $type) }}" target="_blank" class="text-decoration-none">
                            <i class="fas fa-external-link-alt text-muted mr-2"></i>
                            {{ $label }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="col-md-6">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tools"></i> Email Tools</h3>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <a href="/telescope/mail" target="_blank" class="btn btn-info btn-block">
                        <i class="fas fa-satellite-dish"></i> View Sent Emails (Telescope)
                    </a>
                </div>
                <div class="mb-3">
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-cog"></i> Email Configuration
                    </a>
                </div>
                <div class="alert alert-warning mb-0">
                    <small>
                        <i class="fas fa-lightbulb"></i>
                        <strong>Tip:</strong> Click any email link to open it in a new tab. The preview uses the same layout and styling as actual emails.
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
                <h3 class="card-title"><i class="fas fa-info-circle"></i> Email Addresses Configuration</h3>
            </div>
            <div class="card-body">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Purpose</th>
                            <th>Address</th>
                            <th>Environment Variable</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>From (No-Reply)</strong></td>
                            <td><code>{{ config('mail.from.address') }}</code></td>
                            <td><code>MAIL_FROM_ADDRESS</code></td>
                        </tr>
                        <tr>
                            <td><strong>Reply-To (Support)</strong></td>
                            <td><code>{{ config('mail.reply_to.address') }}</code></td>
                            <td><code>MAIL_REPLY_TO_ADDRESS</code></td>
                        </tr>
                        <tr>
                            <td><strong>Admin Notifications</strong></td>
                            <td><code>{{ config('mail.notifications.address') }}</code></td>
                            <td><code>MAIL_NOTIFY_ADDRESS</code></td>
                        </tr>
                        <tr>
                            <td><strong>Booking Notifications</strong></td>
                            <td><code>{{ config('mail.booking_notify') ?: 'Not configured' }}</code></td>
                            <td><code>BOOKING_NOTIFY</code></td>
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