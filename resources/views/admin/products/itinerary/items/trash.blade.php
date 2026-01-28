@extends('adminlte::page')

@section('content')

<div class="row mb-3">
    <div class="col-12">
        <!-- Tabs de navegación -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.products.itinerary_items.index') }}">
                    <i class="fas fa-list"></i> {{ __('m_tours.common.active') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('admin.products.itinerary_items.trash') }}">
                    <i class="fas fa-trash"></i> {{ __('m_tours.itinerary.ui.trash_title') }}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if($items->isEmpty())
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>{{ __('m_tours.itinerary_item.ui.trash_empty') }}
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>{{ __('m_tours.itinerary.table.name') }}</th>
                                <th>{{ __('m_tours.common.deleted_at') }}</th>
                                <th>{{ __('m_tours.common.deleted_by') }}</th>
                                <th>{{ __('m_tours.common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                            <tr>
                                <td>{{ $item->item_id }}</td>
                                <td>
                                    <strong>{{ $item->title }}</strong>
                                    <br>
                                    <small>{{ Str::limit(strip_tags($item->description), 50) }}</small>
                                </td>
                                <td>{{ $item->deleted_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($item->deletedBy)
                                    <span class="badge badge-soft-danger">{{ $item->deletedBy->name }}</span>
                                    @else
                                    <span class="badge badge-soft-secondary">System/Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    @can('edit-itineraries')
                                    <form action="{{ route('admin.products.itinerary_items.restore', $item->item_id) }}" 
                                          method="POST" 
                                          class="d-inline form-restore-item">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="{{ __('m_tours.common.restore') }}">
                                            <i class="fas fa-trash-restore"></i>
                                        </button>
                                    </form>
                                    @endcan

                                    @can('edit-itineraries')
                                    <form action="{{ route('admin.products.itinerary_items.force-delete', $item->item_id) }}" 
                                          method="POST" 
                                          class="d-inline form-force-delete-item">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="{{ __('m_tours.common.force_delete') }}">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Paginación --}}
                <div class="px-3 py-3">
                  {{ $items->links() }}
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Restaurar Itinerario Item
        document.querySelectorAll('.form-restore-item').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: @json(__('m_tours.common.confirm_restore')),
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: @json(__('m_tours.common.yes')),
                    cancelButtonText: @json(__('m_tours.common.cancel')),
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });

        // Force Delete Itinerario Item
        document.querySelectorAll('.form-force-delete-item').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: @json(__('m_tours.common.confirm_force_delete')),
                    text: @json(__('m_tours.image.ui.confirm_delete_text')),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: @json(__('m_tours.common.yes')),
                    cancelButtonText: @json(__('m_tours.common.cancel')),
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });

        // Flash messages
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '{{ session("success") }}',
            timer: 3000,
            showConfirmButton: false
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: '{{ session("error") }}',
            timer: 3000
        });
        @endif
    });
</script>
@endpush
