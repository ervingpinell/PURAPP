{{-- resources/views/admin/tours/wizard/steps/schedules.blade.php --}}

@extends('adminlte::page')

@section('title', __('m_tours.tour.wizard.steps.schedules'))

@push('css')
    <style>
        /* Botones de acción superior */
        .action-buttons {
            background: #2d3748;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        /* Header mejorado */
        .schedules-header {
            background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%);
            color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }

        .schedules-header h1 {
            margin: 0;
            font-size: 1.75rem;
            font-weight: 600;
        }

        .schedules-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }

        .schedules-header .small {
            font-size: 0.9rem;
            opacity: 0.85;
        }

        /* Tarjetas principales */
        .schedules-card {
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            border-radius: 0.5rem;
            overflow: hidden;
            background: #2d3748;
            margin-bottom: 1.5rem;
        }

        .schedules-card .card-header {
            background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%);
            color: white;
            border: none;
            padding: 1rem;
        }

        .schedules-card .card-header h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .schedules-card .card-body {
            background: #2d3748;
            color: #cbd5e0;
            padding: 1.25rem;
        }

        /* Card info lateral */
        .card-info {
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .card-info .card-header {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
            color: white;
            border: none;
            padding: 1rem;
        }

        .card-info .card-header h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
        }

        .card-info .card-body {
            background: #2d3748;
            color: #cbd5e0;
            padding: 1.25rem;
        }

        .card-info hr {
            border-color: rgba(255, 255, 255, 0.1);
        }

        /* Schedule items individuales */
        .schedule-item {
            background: #3a4556;
            border: 1px solid #4a5568;
            border-radius: 0.375rem;
            padding: 0.75rem;
            margin-bottom: 0.75rem;
            transition: all 0.2s;
        }

        .schedule-item:hover {
            background: #434d5f;
            border-color: #667eea;
        }

        /* Checkboxes mejorados */
        .custom-control {
            padding-left: 1.75rem;
            min-height: 1.5rem;
            display: block;
        }

        .custom-control-label {
            color: #e2e8f0;
            cursor: pointer;
            padding-left: 0.5rem;
            display: inline-block;
            position: relative;
            margin-bottom: 0;
            vertical-align: top;
            line-height: 1.5;
        }

        .custom-control-label::before {
            position: absolute;
            top: 0.125rem;
            left: -1.75rem;
            display: block;
            width: 1.25rem;
            height: 1.25rem;
            pointer-events: none;
            content: "";
            background-color: #3a4556;
            border: 1px solid #4a5568;
            border-radius: 0.25rem;
        }

        .custom-control-label::after {
            position: absolute;
            top: 0.125rem;
            left: -1.75rem;
            display: block;
            width: 1.25rem;
            height: 1.25rem;
            content: "";
            background: no-repeat 50% / 50% 50%;
        }

        .custom-control-input {
            position: absolute;
            left: 0;
            z-index: -1;
            width: 1.25rem;
            height: 1.5rem;
            opacity: 0;
        }

        .custom-control-input:checked~.custom-control-label::before {
            background-color: #f6ad55;
            border-color: #f6ad55;
        }

        .custom-control-input:checked~.custom-control-label::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%23fff' d='M6.564.75l-3.59 3.612-1.538-1.55L0 4.26l2.974 2.99L8 2.193z'/%3e%3c/svg%3e");
        }

        .custom-control-input:checked~.custom-control-label {
            font-weight: 600;
            color: #fbd38d;
        }

        .custom-control-input:focus~.custom-control-label::before {
            box-shadow: 0 0 0 0.2rem rgba(246, 173, 85, 0.25);
        }

        /* Badge para labels */
        .badge-info {
            background-color: #4299e1;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            margin-left: 0.5rem;
        }

        /* Inputs de formulario */
        .schedule-item input[type="number"],
        .schedule-item input[type="text"],
        .schedule-item input[type="time"] {
            background: #2d3748;
            border: 1px solid #4a5568;
            color: #e2e8f0;
        }

        .schedule-item input:focus {
            background: #2d3748;
            border-color: #f6ad55;
            color: #e2e8f0;
            box-shadow: 0 0 0 0.2rem rgba(246, 173, 85, 0.25);
        }

        .schedule-item label {
            color: #a0aec0;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .schedule-item .text-muted {
            color: #718096 !important;
            font-size: 0.8rem;
        }

        /* Alert info */
        .alert-info {
            background: rgba(66, 153, 225, 0.15);
            border: 1px solid rgba(66, 153, 225, 0.3);
            color: #90cdf4;
            border-radius: 0.375rem;
        }

        .alert-info i {
            color: #63b3ed;
        }

        /* Alert danger */
        .alert-danger {
            background: rgba(245, 101, 101, 0.15);
            border: 1px solid rgba(245, 101, 101, 0.3);
            color: #fc8181;
            border-radius: 0.375rem;
        }

        /* Footer de navegación */
        .navigation-footer {
            background: #2d3748;
            border: none;
            box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.15);
            padding: 1.25rem;
            border-radius: 0.5rem;
            margin-top: 2rem;
        }

        /* Modal mejorado */
        .modal-content {
            background: #2d3748;
            border: 1px solid #4a5568;
        }

        .modal-header {
            background: #3a4556;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
        }

        .modal-header .close,
        .modal-header .btn-close {
            color: #e2e8f0;
            opacity: 0.8;
        }

        .modal-header .close:hover,
        .modal-header .btn-close:hover {
            opacity: 1;
        }

        .modal-body {
            background: #2d3748;
            color: #cbd5e0;
        }

        .modal-body label {
            color: #e2e8f0;
            font-weight: 600;
        }

        .modal-body input[type="time"],
        .modal-body input[type="text"],
        .modal-body input[type="number"] {
            background: #3a4556;
            border: 1px solid #4a5568;
            color: #e2e8f0;
        }

        .modal-body input:focus {
            background: #3a4556;
            border-color: #f6ad55;
            color: #e2e8f0;
            box-shadow: 0 0 0 0.2rem rgba(246, 173, 85, 0.25);
        }

        .modal-body .text-muted {
            color: #a0aec0 !important;
        }

        .modal-body .alert-info {
            font-size: 0.9rem;
        }

        .modal-footer {
            background: #3a4556;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Form check para is_active */
        .form-check-input {
            background-color: #3a4556;
            border-color: #4a5568;
        }

        .form-check-input:checked {
            background-color: #f6ad55;
            border-color: #f6ad55;
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(246, 173, 85, 0.25);
        }

        .form-check-label {
            color: #e2e8f0;
        }

        /* Textos muted generales */
        .text-muted {
            color: #a0aec0 !important;
        }

        /* Estilos de botones */
        .btn-primary {
            background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%);
            border: none;
            color: white;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .btn-success {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            border: none;
            color: white;
            font-weight: 600;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary {
            background: #4a5568;
            border: none;
            color: #e2e8f0;
            font-weight: 600;
        }

        .btn-secondary:hover {
            background: #5a6778;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .btn-danger,
        .btn-outline-danger {
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
            border: none;
            color: white;
            font-weight: 600;
        }

        .btn-danger:hover,
        .btn-outline-danger:hover {
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .schedules-header {
                padding: 1.5rem;
            }

            .schedules-header h1 {
                font-size: 1.5rem;
            }

            .card-info {
                margin-top: 1.5rem;
            }
        }

        @media (max-width: 767.98px) {
            .schedule-item {
                padding: 1rem;
            }

            .schedule-item .d-flex {
                flex-direction: column;
                gap: 0.75rem;
            }

            .schedule-item .mt-md-0 {
                margin-top: 0.75rem !important;
                width: 100%;
            }

            .navigation-footer .d-flex {
                flex-direction: column;
                gap: 0.75rem;
            }

            .navigation-footer .btn {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        @include('admin.tours.wizard.partials.stepper')

        {{-- Header mejorado --}}
        <div class="schedules-header">
            <h1>
                <i class="fas fa-clock"></i>
                {{ __('m_tours.tour.wizard.steps.schedules') }}
            </h1>
            <p>{{ $tour->name }}</p>
            <p class="small mb-0">
                {{ __('m_tours.schedule.ui.base_capacity_tour') }}
                <strong>
                    {{ $tour->max_capacity ?? __('m_tours.schedule.ui.capacity_not_defined') }}
                </strong>
            </p>
        </div>

        <form method="POST" action="{{ route('admin.tours.wizard.store.schedules', $tour) }}">
            @csrf

            {{-- Botones de acción --}}
            <div class="action-buttons">
                <a href="{{ route('admin.tours.schedule.index') }}" class="btn btn-primary btn-sm" title="                    {{ __('m_tours.common.crud_go_to_index', [
        'element' => __('m_tours.schedule.plural'),
    ]) }}">
                    <i class="fas fa-list"></i>
                    <span class="d-none d-md-inline">
                        {{ __('m_tours.common.crud_go_to_index', [
        'element' => __('m_tours.schedule.plural'),
    ]) }}
                    </span>
                </a>

                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                    data-bs-target="#modalCreateScheduleForTour">
                    <i class="fas fa-plus"></i>
                    {{ __('m_tours.schedule.ui.new_schedule_for_tour') }}
                </button>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card schedules-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-clock"></i>
                                {{ __('m_tours.tour.schedules_form.available_title') }}
                            </h3>
                        </div>

                        <div class="card-body">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                                <p class="text-muted mb-2 mb-md-0">
                                    {{ __('m_tours.tour.schedules_form.select_hint') }}
                                </p>
                            </div>

                            @foreach($schedules ?? [] as $schedule)
                                                @php
                                                    $isChecked = in_array(
                                                        $schedule->schedule_id,
                                                        old(
                                                            'schedules',
                                                            $tour->schedules->pluck('schedule_id')->toArray()
                                                        )
                                                    );

                                                    $pivot = $tour->schedules
                                                        ->firstWhere('schedule_id', $schedule->schedule_id);

                                                    $existingCapacity = $pivot?->pivot?->base_capacity;
                                                    $oldCapacity = old('base_capacity.' . $schedule->schedule_id, $existingCapacity);
                                                @endphp

                                                <div class="schedule-item">
                                                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input"
                                                                id="schedule_{{ $schedule->schedule_id }}" name="schedules[]"
                                                                value="{{ $schedule->schedule_id }}" {{ $isChecked ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="schedule_{{ $schedule->schedule_id }}">
                                                                <strong>
                                                                    {{ date('g:i A', strtotime($schedule->start_time)) }}
                                                                    -
                                                                    {{ date('g:i A', strtotime($schedule->end_time)) }}
                                                                </strong>
                                                                @if($schedule->label)
                                                                    <span class="badge badge-info">
                                                                        {{ $schedule->label }}
                                                                    </span>
                                                                @endif
                                                            </label>
                                                        </div>

                                                        <div class="mt-2 mt-md-0" style="min-width: 180px;">
                                                            <label class="mb-0 small">
                                                                {{ __('m_tours.schedule.ui.capacity_optional') }}
                                                            </label>
                                                            <input type="number" name="base_capacity[{{ $schedule->schedule_id }}]"
                                                                class="form-control form-control-sm" min="1" max="999"
                                                                value="{{ $oldCapacity }}" placeholder="{{ $tour->max_capacity
                                ? __('m_tours.schedule.ui.capacity_placeholder_with_value', ['capacity' => $tour->max_capacity])
                                : __('m_tours.schedule.ui.capacity_placeholder_generic') }}">
                                                            <small class="text-muted d-block">
                                                                @if($tour->max_capacity)
                                                                    {{ __('m_tours.schedule.ui.capacity_hint_with_value', ['capacity' => $tour->max_capacity]) }}
                                                                @else
                                                                    {{ __('m_tours.schedule.ui.capacity_hint_generic') }}
                                                                @endif
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                            @endforeach

                            @error('schedules')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                            @error('schedules.*')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                            @error('base_capacity.*')
                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Columna lateral de info --}}
                <div class="col-md-4">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle"></i>
                                {{ __('m_tours.tour.schedules_form.info_title') }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <p class="small">
                                {{ __('m_tours.tour.schedules_form.schedules_text') }}
                            </p>
                            <hr>
                            <p class="small mb-0">
                                <strong>{{ __('m_tours.schedule.ui.tip_label') }}</strong>
                                {{ __('m_tours.schedule.ui.capacity_tip', [
        'capacity' => $tour->max_capacity
            ?? __('m_tours.schedule.ui.capacity_not_defined')
    ]) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer navegación --}}
            <div class="card">
                <div class="card-footer navigation-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.tours.wizard.step', ['tour' => $tour, 'step' => 2]) }}"
                            class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            {{ __('m_tours.common.previous') }}
                        </a>

                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.tours.wizard.cancel', $tour) }}" class="btn btn-danger"
                                onclick="return confirm('{{ __('m_tours.tour.wizard.confirm_cancel') }}')">
                                <i class="fas fa-trash"></i>
                                <span class="d-none d-md-inline">{{ __('m_tours.common.cancel') }}</span>
                            </a>

                            <button type="submit" class="btn btn-primary ml-2">
                                {{ __('m_tours.tour.wizard.save_and_continue') }}
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Modal crear horario --}}
    <div class="modal fade" id="modalCreateScheduleForTour" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.tours.wizard.quick.schedule', $tour) }}" method="POST" class="modal-content"
                autocomplete="off">
                @csrf
                <input type="hidden" name="tour_id" value="{{ $tour->tour_id }}">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus"></i>
                        {{ __('m_tours.schedule.ui.modal_new_for_tour_title', ['tour' => $tour->name]) }}
                    </h5>
                    <button type="button" class="close btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('m_tours.common.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_schedule_start">
                                    {{ __('m_tours.schedule.fields.start_time') }}
                                </label>
                                <input type="time" name="start_time" id="modal_schedule_start" class="form-control"
                                    required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_schedule_end">
                                    {{ __('m_tours.schedule.fields.end_time') }}
                                </label>
                                <input type="time" name="end_time" id="modal_schedule_end" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2">
                        <label for="modal_schedule_label">
                            {{ __('m_tours.schedule.fields.label_optional') }}
                        </label>
                        <input type="text" name="label" id="modal_schedule_label" class="form-control" maxlength="255">
                    </div>

                    <div class="alert alert-info small mt-3">
                        <i class="fas fa-info-circle me-1"></i>
                        @if($tour->max_capacity)
                            {{ __('m_tours.schedule.ui.capacity_modal_info_with_value', ['capacity' => $tour->max_capacity]) }}
                        @else
                            {{ __('m_tours.schedule.ui.capacity_modal_info_generic') }}
                        @endif
                    </div>

                    <div class="mt-2">
                        <label for="modal_schedule_capacity">
                            {{ __('m_tours.schedule.ui.capacity_optional') }}
                        </label>
                        <input type="number" name="base_capacity" id="modal_schedule_capacity" class="form-control" min="1"
                            max="999" placeholder="{{ $tour->max_capacity
        ? __('m_tours.schedule.ui.capacity_placeholder_with_value', ['capacity' => $tour->max_capacity])
        : __('m_tours.schedule.ui.capacity_placeholder_generic') }}">
                        <small class="text-muted">
                            @if($tour->max_capacity)
                                {{ __('m_tours.schedule.ui.capacity_hint_with_value', ['capacity' => $tour->max_capacity]) }}
                            @else
                                {{ __('m_tours.schedule.ui.capacity_hint_generic') }}
                            @endif
                        </small>
                    </div>

                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="modal_schedule_active" name="is_active"
                            value="1" checked>
                        <label class="form-check-label" for="modal_schedule_active">
                            {{ __('m_tours.schedule.fields.active') }}
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        {{ __('m_tours.schedule.ui.modal_save') }}
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('m_tours.schedule.ui.modal_cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
