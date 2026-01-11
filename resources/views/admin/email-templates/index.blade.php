@extends('adminlte::page')

@section('title', 'Email Templates')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>
        <i class="fas fa-envelope-open-text"></i> Email Templates
    </h1>
    <a href="{{ route('admin.email-preview.index') }}" class="btn btn-info" target="_blank">
        <i class="fas fa-eye"></i> Preview All Emails
    </a>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Email Template Editor</strong><br>
            Customize email content for all languages. Changes apply immediately to new emails sent.
        </div>
    </div>
</div>

@foreach(['customer' => 'Customer Emails', 'admin' => 'Admin Emails', 'other' => 'Other Emails'] as $category => $title)
<div class="row mt-3">
    <div class="col-12">
        <div class="card card-{{ $category === 'customer' ? 'primary' : ($category === 'admin' ? 'warning' : 'secondary') }}">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-{{ $category === 'customer' ? 'user' : ($category === 'admin' ? 'user-shield' : 'envelope') }}"></i>
                    {{ $title }}
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Template</th>
                            <th>Description</th>
                            <th width="120">Status</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates[$category] as $template)
                        <tr>
                            <td><strong>{{ $template->name }}</strong></td>
                            <td><small class="text-muted">{{ $template->description }}</small></td>
                            <td>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox"
                                        class="custom-control-input toggle-template"
                                        id="toggle-{{ $template->id }}"
                                        data-id="{{ $template->id }}"
                                        {{ $template->is_active ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="toggle-{{ $template->id }}">
                                        <span class="badge badge-{{ $template->is_active ? 'success' : 'secondary' }}">
                                            {{ $template->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                @can('edit-email-templates')
                                <a href="{{ route('admin.email-templates.edit', $template) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No templates found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endforeach

<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-lightbulb"></i> Quick Tips</h3>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Use variables like <code>@{{ customer_name }}</code> for dynamic content</li>
                    <li>Edit content for each language separately</li>
                    <li>Preview changes before saving</li>
                    <li>Inactive templates will use default Blade templates</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('.toggle-template').change(function() {
            const templateId = $(this).data('id');
            const $toggle = $(this);

            $.post(`/admin/email-templates/${templateId}/toggle`, {
                    _token: '{{ csrf_token() }}'
                })
                .done(function(response) {
                    const badge = $toggle.closest('td').find('.badge');
                    if (response.is_active) {
                        badge.removeClass('badge-secondary').addClass('badge-success').text('Active');
                    } else {
                        badge.removeClass('badge-success').addClass('badge-secondary').text('Inactive');
                    }
                    toastr.success('Template status updated');
                })
                .fail(function() {
                    $toggle.prop('checked', !$toggle.prop('checked'));
                    toastr.error('Failed to update template status');
                });
        });
    });
</script>
@stop