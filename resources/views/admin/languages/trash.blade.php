@extends('adminlte::page')

@section('title', __('m_tours.language.ui.trash_title'))

@section('content_header')
<h1>{{ __('m_tours.language.ui.trash_title') }}</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ __('m_tours.language.ui.trash_list_title') }}</h3>
        <div class="card-tools">
            <a href="{{ route('admin.languages.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> {{ __('m_tours.language.ui.back_to_list') }}
            </a>
        </div>
    </div>
    <div class="card-body p-0 table-responsive">
        @if($trashedLanguages->isEmpty())
        <div class="p-4 text-center text-muted">
            <i class="fas fa-trash-alt fa-3x mb-3"></i>
            <p>{{ __('m_tours.language.ui.empty_trash') }}</p>
        </div>
        @else
        <table class="table table-striped table-hover align-middle">
            <thead class="bg-secondary text-white">
                <tr>
                    <th>{{ __('m_tours.language.ui.table.id') }}</th>
                    <th>{{ __('m_tours.language.ui.table.name') }}</th>
                    <th>{{ __('m_tours.language.ui.deleted_at') }}</th>
                    <th>{{ __('m_tours.language.ui.deleted_by') }}</th>
                    <th class="text-center" style="width: 180px;">{{ __('m_tours.language.ui.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trashedLanguages as $item)
                <tr>
                    <td>{{ $item->product_language_id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>
                        <small>
                            <div><i class="far fa-calendar-alt me-1"></i> {{ $item->deleted_at->format('Y-m-d') }}</div>
                            <div class="text-muted"><i class="far fa-clock me-1"></i> {{ $item->deleted_at->format('H:i') }}</div>
                        </small>
                    </td>
                    <td>
                        @if($item->deletedBy)
                        <span class="badge bg-secondary">
                            <i class="fas fa-user-times me-1"></i> {{ $item->deletedBy->name }}
                        </span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            @can('restore-product-languages')
                            <form action="{{ route('admin.languages.restore', $item->getKey()) }}" method="POST"
                                class="d-inline restore-form">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success"
                                    title="{{ __('m_tours.language.ui.restore') }}">
                                    <i class="fas fa-trash-restore"></i>
                                </button>
                            </form>
                            @endcan

                            @can('force-delete-product-languages')
                            <form action="{{ route('admin.languages.forceDelete', $item->getKey()) }}" method="POST"
                                class="d-inline force-delete-form ms-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    title="{{ __('m_tours.language.ui.delete_forever') }}">
                                    <i class="fas fa-ban"></i>
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
    // Confirm Restore
    document.querySelectorAll('.restore-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: @json(__('m_tours.language.ui.restore')) + '?',
                text: @json(__('m_tours.language.ui.restore_confirm')),
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: @json(__('m_tours.language.ui.yes_continue'))
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Confirm Force Delete
    document.querySelectorAll('.force-delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: @json(__('m_tours.language.ui.delete_forever')) + '?',
                html: @json(__('m_tours.language.ui.delete_confirm_html', ['label' => ''])),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: @json(__('m_tours.language.ui.yes_delete'))
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Success Flash
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: @json(__('m_tours.common.success_title')),
        text: @json(session('success')),
        timer: 3000,
        showConfirmButton: false
    });
    @endif
</script>
@stop