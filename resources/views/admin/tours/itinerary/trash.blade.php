@extends('adminlte::page')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">{{ __('m_tours.itinerary.ui.trash_title') }}</h4>
        </div>
    </div>
</div>
                <div class="mt-3">
                    <a href="{{ route('admin.tours.itinerary.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('m_tours.common.back_to_list') }}
                    </a>
                </div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered dt-responsive nowrap w-100" id="datatable">
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
                            @foreach($itineraries as $itinerary)
                            <tr>
                                <td>{{ $itinerary->itinerary_id }}</td>
                                <td>
                                    <strong>{{ $itinerary->name }}</strong>
                                    <br>
                                    <small>{{ Str::limit(strip_tags($itinerary->description), 50) }}</small>
                                </td>
                                <td>{{ $itinerary->deleted_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($itinerary->deletedBy)
                                    <span class="badge badge-soft-danger">{{ $itinerary->deletedBy->name }}</span>
                                    @else
                                    <span class="badge badge-soft-secondary">System/Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    @can('restore-itineraries')
                                    <form action="{{ route('admin.tours.itinerary.restore', $itinerary->itinerary_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="{{ __('m_tours.common.restore') }}">
                                            <i class="fas fa-trash-restore"></i>
                                        </button>
                                    </form>
                                    @endcan

                                    @can('force-delete-itineraries')
                                    <form action="{{ route('admin.tours.itinerary.force-delete', $itinerary->itinerary_id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('m_tours.common.confirm_force_delete') }}')">
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

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#datatable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            }
        });
    });
</script>
@endsection