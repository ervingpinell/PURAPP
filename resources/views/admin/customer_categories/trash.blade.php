@extends('adminlte::page')

@section('title', __('customer_categories.ui.trash_title'))

@section('content_header')
    <h1>{{ __('customer_categories.ui.trash_header') }}</h1>
@stop

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{ session('error') }}
</div>
@endif

{{-- Tabs --}}
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.customer_categories.index') }}">
            {{ __('customer_categories.states.active') }}
        </a>
    </li>
    @can('restore-customer-categories')
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('admin.customer_categories.trash') }}">
            {{ __('customer_categories.ui.trash_title') }}
        </a>
    </li>
    @endcan
</ul>

<div class="card shadow-sm">
    <div class="card-header bg-danger text-white">
        <h3 class="card-title">
            <i class="fas fa-trash-alt"></i> {{ __('customer_categories.ui.trash_title') }}
        </h3>
    </div>
    <div class="card-body p-0 table-responsive">
        @if($categories->isEmpty())
        <div class="alert alert-light text-center m-0 p-5">
            <i class="fas fa-trash-restore fa-3x mb-3 text-muted"></i>
            <h5>{{ __('customer_categories.ui.trash_empty') }}</h5>
        </div>
        @else
        <table class="table table-hover table-striped text-nowrap">
            <thead>
                <tr>
                    <th>{{ __('customer_categories.table.name') }}</th>
                    <th>{{ __('customer_categories.table.slug') }}</th>
                    <th class="text-center">{{ __('customer_categories.table.deleted_by') }}</th>
                    <th>{{ __('customer_categories.table.date') }}</th>
                    <th class="text-center">{{ __('customer_categories.table.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr>
                    <td><strong>{{ $category->getTranslatedName() }}</strong></td>
                    <td><code>{{ $category->slug }}</code></td>
                    <td class="text-center">
                        @if($category->deletedBy)
                        <span class="badge badge-info">{{ $category->deletedBy->name }}</span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $category->deleted_at->format('d/m/Y H:i') }}</td>
                    <td class="text-center">
                        <div class="btn-group">
                            @can('restore-customer-categories')
                            <form action="{{ route('admin.customer_categories.restore', $category->category_id) }}" method="POST" class="d-inline restore-form">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success" title="{{ __('customer_categories.buttons.restore') }}">
                                    <i class="fas fa-trash-restore"></i>
                                </button>
                            </form>
                            @endcan

                            @can('hard-delete-customer-categories')
                            <form action="{{ route('admin.customer_categories.forceDelete', $category->category_id) }}" method="POST" class="d-inline force-delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="{{ __('customer_categories.buttons.force_delete') }}">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Restore confirmation
        $('.restore-form').on('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '{{ __('customer_categories.buttons.restore') }}?',
                text: "La categoría volverá a estar activa.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __('customer_categories.buttons.restore') }}',
                cancelButtonText: '{{ __('customer_categories.buttons.cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

        // Force delete confirmation
        $('.force-delete-form').on('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '{{ __('customer_categories.buttons.force_delete') }}?',
                text: "Esta acción NO se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __('customer_categories.buttons.delete') }}',
                cancelButtonText: '{{ __('customer_categories.buttons.cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@stop