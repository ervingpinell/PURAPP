@extends('adminlte::page')

@section('title', __('m_tours.schedule.ui.trash_title'))

@section('content_header')
<h1>{{ __('m_tours.schedule.ui.trash_title') }}</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ __('m_tours.schedule.ui.trash_list_title') }}</h3>
        <div class="card-tools">
            <a href="{{ route('admin.tours.schedule.index') }}" class="btn btn-default btn-sm">
                <i class="fas fa-arrow-left"></i> {{ __('m_tours.schedule.ui.back_to_list') }}
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped projects">
                <thead>
                    <tr>
                        <th style="width: 1%">ID</th>
                        <th>{{ __('m_tours.schedule.fields.label') }}</th>
                        <th>{{ __('m_tours.schedule.ui.time_range') }}</th>
                        <th>{{ __('m_tours.schedule.ui.deleted_by') }}</th>
                        <th>{{ __('m_tours.schedule.ui.deleted_at') }}</th>
                        <th style="width: 20%">{{ __('m_tours.schedule.ui.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schedules as $schedule)
                    <tr>
                        <td>{{ $schedule->schedule_id }}</td>
                        <td>{{ $schedule->label ?: '—' }}</td>
                        <td>
                            {{ \Carbon\Carbon::createFromTimeString($schedule->start_time)->format('g:i A') }} -
                            {{ \Carbon\Carbon::createFromTimeString($schedule->end_time)->format('g:i A') }}
                        </td>
                        <td>
                            @if($schedule->deletedBy)
                            <span class="badge badge-info">{{ $schedule->deletedBy->name }}</span>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $schedule->deleted_at->format('d/m/Y H:i') }}</td>
                        <td class="project-actions">
                            @can('restore-schedules')
                            <form action="{{ route('admin.tours.schedule.restore', $schedule->schedule_id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('{{ __('m_tours.common.confirm') }}')">
                                    <i class="fas fa-trash-restore"></i> {{ __('m_tours.schedule.ui.restore') }}
                                </button>
                            </form>
                            @endcan

                            @can('force-delete-schedules')
                            <form action="{{ route('admin.tours.schedule.forceDelete', $schedule->schedule_id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('m_tours.schedule.ui.delete_confirm_html', ['label' => $schedule->schedule_id]) }}')">
                                    <i class="fas fa-trash"></i> {{ __('m_tours.schedule.ui.delete_forever') }}
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">{{ __('m_tours.schedule.ui.empty_trash') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
@stop