@extends('adminlte::page')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">{{ __('m_tours.schedule.ui.trash_title') }}</h4>
        </div>
    </div>
</div>
<div class="mt-3">
    <a href="{{ route('admin.products.schedule.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> {{ __('m_tours.schedule.ui.back_to_list') }}
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
                                <th>{{ __('m_tours.schedule.fields.label') }}</th>
                                <th>{{ __('m_tours.schedule.ui.time_range') }}</th>
                                <th>{{ __('m_tours.schedule.ui.deleted_by') }}</th>
                                <th>{{ __('m_tours.schedule.ui.deleted_at') }}</th>
                                <th>{{ __('m_tours.schedule.ui.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($schedules as $schedule)
                            <tr>
                                <td>{{ $schedule->schedule_id }}</td>
                                <td>{{ $schedule->label ?: '—' }}</td>
                                <td>
                                    {{ \Carbon\Carbon::createFromTimeString($schedule->start_time)->format('g:i A') }} -
                                    {{ \Carbon\Carbon::createFromTimeString($schedule->end_time)->format('g:i A') }}
                                </td>
                                <td>
                                    @if($schedule->deletedBy)
                                    <span class="badge badge-soft-danger">{{ $schedule->deletedBy->name }}</span>
                                    @else
                                    <span class="badge badge-soft-secondary">System/Unknown</span>
                                    @endif
                                </td>
                                <td>{{ $schedule->deleted_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @can('restore-schedules')
                                    <form action="{{ route('admin.products.schedule.restore', $schedule->schedule_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success" title="{{ __('m_tours.common.restore') }}">
                                            <i class="fas fa-trash-restore"></i>
                                        </button>
                                    </form>
                                    @endcan

                                    @can('force-delete-schedules')
                                    <form action="{{ route('admin.products.schedule.forceDelete', $schedule->schedule_id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('m_tours.common.confirm_force_delete') }}')">
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