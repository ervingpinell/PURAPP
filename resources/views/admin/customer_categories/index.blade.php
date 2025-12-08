@extends('adminlte::page')

@section('title', __('customer_categories.ui.page_title_index'))

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>{{ __('customer_categories.ui.header_index') }}</h1>
    @can('create-customer-categories')
    <a href="{{ route('admin.customer_categories.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ __('customer_categories.buttons.new_category') }}
    </a>
    @endcan
</div>
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

<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ __('customer_categories.ui.list_title') }}</h3>
        <div class="card-tools">
            <span class="badge badge-primary">
                {{ $categories->total() }}
            </span>
        </div>
    </div>

    {{-- Desktop Table View --}}
    <div class="card-body p-0 d-none d-md-block">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: 60px">#</th>
                    <th>{{ __('customer_categories.table.name') }} <small class="text-muted">({{ strtoupper(app()->getLocale()) }})</small></th>
                    <th>Slug</th>
                    <th>{{ __('customer_categories.table.range') }}</th>
                    <th style="width: 80px">{{ __('customer_categories.table.order') }}</th>
                    <th style="width: 100px">{{ __('customer_categories.table.active') }}</th>
                    <th style="width: 150px" class="text-center">{{ __('customer_categories.table.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td>{{ $category->category_id }}</td>
                    <td><strong>{{ $category->getTranslatedName() }}</strong></td>
                    <td><code>{{ $category->slug }}</code></td>
                    <td>
                        <span class="badge badge-info">
                            {{ $category->age_range }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-secondary">{{ $category->order }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $category->is_active ? 'badge-success' : 'badge-secondary' }}">
                            {{ $category->is_active ? __('customer_categories.states.active') : __('customer_categories.states.inactive') }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            @can('edit-customer-categories')
                            <a href="{{ route('admin.customer_categories.edit', $category) }}"
                                class="btn btn-sm btn-edit"
                                title="{{ __('customer_categories.buttons.edit') }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan

                            @can('delete-customer-categories')
                            @can('publish-customer-categories')
                            <form action="{{ route('admin.customer_categories.toggle', $category) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit"
                                    class="btn btn-sm {{ $category->is_active ? 'btn-warning' : 'btn-secondary' }}"
                                    title="{{ $category->is_active ? 'Desactivar' : 'Activar' }}">
                                    <i class="fas fa-toggle-{{ $category->is_active ? 'on' : 'off' }}"></i>
                                </button>
                            </form>
                            @endcan
                            <button type="button"
                                class="btn btn-sm btn-danger"
                                data-toggle="modal"
                                data-target="#deleteModal{{ $category->category_id }}"
                                title="{{ __('customer_categories.buttons.delete') }}">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endcan
                        </div>

                        {{-- Modal de Confirmación --}}
                        <div class="modal fade" id="deleteModal{{ $category->category_id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title">{{ __('customer_categories.dialogs.delete.title') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>{!! __('customer_categories.dialogs.delete.text', [
                                            'name' => '<strong>'.e($category->getTranslatedName()).'</strong>'
                                            ]) !!}</p>
                                        <p class="text-muted small">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            {{ __('customer_categories.dialogs.delete.caution') }}
                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                            {{ __('customer_categories.buttons.cancel') }}
                                        </button>
                                        <form action="{{ route('admin.customer_categories.destroy', $category) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i> {{ __('customer_categories.buttons.delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                        {{ __('customer_categories.ui.empty_list') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Card View --}}
    <div class="card-body d-md-none">
        @forelse($categories as $category)
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-{{ $category->is_active ? 'info' : 'secondary' }} text-white d-flex justify-content-between align-items-center">
                <strong>{{ $category->getTranslatedName() }}</strong>
                <span class="badge bg-light text-dark">#{{ $category->category_id }}</span>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Slug:</strong> <code>{{ $category->slug }}</code></p>
                <p class="mb-2"><strong>{{ __('customer_categories.table.range') }}:</strong>
                    <span class="badge badge-info">{{ $category->age_range }}</span>
                </p>
                <p class="mb-2"><strong>{{ __('customer_categories.table.order') }}:</strong>
                    <span class="badge badge-secondary">{{ $category->order }}</span>
                </p>
                <p class="mb-3">
                    <strong>{{ __('customer_categories.table.active') }}:</strong>
                    <span class="badge {{ $category->is_active ? 'badge-success' : 'badge-secondary' }}">
                        <i class="fas fa-{{ $category->is_active ? 'check' : 'times' }}"></i>
                        {{ $category->is_active ? __('customer_categories.states.active') : __('customer_categories.states.inactive') }}
                    </span>
                </p>

                <div class="d-grid gap-2">
                    @can('edit-customer-categories')
                    <a href="{{ route('admin.customer_categories.edit', $category) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-edit me-1"></i> {{ __('customer_categories.buttons.edit') }}
                    </a>

                    <form action="{{ route('admin.customer_categories.toggle', $category) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $category->is_active ? 'btn-warning' : 'btn-secondary' }} w-100">
                            <i class="fas fa-toggle-{{ $category->is_active ? 'on' : 'off' }} me-1"></i>
                            {{ $category->is_active ? __('customer_categories.states.deactivate') : __('customer_categories.states.activate') }}
                        </button>
                    </form>
                    @endcan

                    @can('delete-customer-categories')
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal{{ $category->category_id }}">
                        <i class="fas fa-trash me-1"></i> {{ __('customer_categories.buttons.delete') }}
                    </button>
                    @endcan
                </div>
            </div>
        </div>
        @empty
        <div class="alert alert-info text-center">
            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
            {{ __('customer_categories.ui.empty_list') }}
        </div>
        @endforelse
    </div>

    @if($categories->hasPages())
    <div class="card-footer">
        {{ $categories->links() }}
    </div>
    @endif
</div>

{{-- Información sobre rangos --}}
<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-info-circle"></i> {{ __('customer_categories.ui.info_card_title') }}
        </h3>
    </div>
    <div class="card-body">
        <ul class="mb-0">
            <li>{{ __('customer_categories.rules.no_overlap') }}</li>
            <li>{{ __('customer_categories.rules.order_affects_display') }}</li>
            <li>{{ __('customer_categories.help.notes.use_null_age_to') }}</li>
            <li>{{ __('customer_categories.help.notes.inactive_hidden') }}</li>
        </ul>
    </div>
</div>
@stop

@section('css')
<style>
    .table td {
        vertical-align: middle;
    }

    /* Mobile responsiveness */
    @media (max-width: 767px) {
        .d-grid {
            display: grid;
        }

        .gap-2 {
            gap: 0.5rem;
        }
    }
</style>
@stop
