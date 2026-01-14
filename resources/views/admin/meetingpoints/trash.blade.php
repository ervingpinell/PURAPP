{{-- resources/views/admin/meetingpoints/trash.blade.php --}}
@extends('adminlte::page')

@section('title', __('pickups.meeting_point.trash.title'))

@section('content_header')
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
    <h1 class="m-0">
        <i class="fas fa-trash me-2"></i>{{ __('pickups.meeting_point.trash.title') }}
    </h1>
    <a href="{{ route('admin.meetingpoints.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> {{ __('pickups.meeting_point.buttons.back', ['default' => 'Volver']) }}
    </a>
</div>
@stop

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        @if($trashedPoints->isEmpty())
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-2"></i>{{ __('pickups.meeting_point.trash.empty') }}
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>{{ __('pickups.meeting_point.fields.name') }}</th>
                        <th>{{ __('pickups.meeting_point.trash.deleted_by') }}</th>
                        <th>{{ __('pickups.meeting_point.trash.deleted_at') }}</th>
                        <th>{{ __('pickups.meeting_point.trash.auto_delete_in') }}</th>
                        <th class="text-center">{{ __('pickups.meeting_point.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trashedPoints as $point)
                    @php
                    $daysLeft = max(0, 30 - now()->diffInDays($point->deleted_at));
                    @endphp
                    <tr>
                        <td><strong>{{ $point->name_localized }}</strong></td>
                        <td>
                            @if($point->deletedBy)
                            {{ $point->deletedBy->name }}
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $point->deleted_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="badge {{ $daysLeft <= 7 ? 'bg-danger' : 'bg-warning' }}">
                                {{ trans_choice('pickups.meeting_point.trash.days', $daysLeft, ['count' => $daysLeft]) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                @can('restore-meeting-points')
                                <form action="{{ route('admin.meetingpoints.restore', $point->id) }}" method="POST" class="d-inline restore-form">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success" title="{{ __('pickups.meeting_point.actions.restore', ['default' => 'Restaurar']) }}">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                                @endcan

                                @can('force-delete-meeting-points')
                                <form action="{{ route('admin.meetingpoints.forceDelete', $point->id) }}" method="POST" class="d-inline force-delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="{{ __('pickups.meeting_point.actions.force_delete', ['default' => 'Eliminar permanentemente']) }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@stop

@push('js')
<script>
    // Restore confirmation
    document.querySelectorAll('.restore-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '{{ __("pickups.meeting_point.confirm.restore_title", ["default" => "¿Restaurar punto?"]) }}',
                text: '{{ __("pickups.meeting_point.confirm.restore_text", ["default" => "El punto volverá a la lista activa."]) }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '{{ __("pickups.meeting_point.buttons.confirm") }}',
                cancelButtonText: '{{ __("pickups.meeting_point.buttons.cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });

    // Force delete confirmation
    document.querySelectorAll('.force-delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '{{ __("pickups.meeting_point.confirm.force_delete_title", ["default" => "¿Eliminar permanentemente?"]) }}',
                text: '{{ __("pickups.meeting_point.confirm.force_delete_text", ["default" => "Esta acción no se puede deshacer."]) }}',
                icon: 'error',
                showCancelButton: true,
                confirmButtonText: '{{ __("pickups.meeting_point.buttons.confirm") }}',
                cancelButtonText: '{{ __("pickups.meeting_point.buttons.cancel") }}',
                confirmButtonColor: '#d33'
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
        title: '{{ __("pickups.meeting_point.toasts.success_title") }}',
        text: '{{ session("success") }}',
        timer: 3000
    });
    @endif
</script>
@endpush