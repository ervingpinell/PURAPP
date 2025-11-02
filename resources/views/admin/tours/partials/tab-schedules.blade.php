<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Horarios Disponibles</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Selecciona los horarios para este tour</label>

                    @php
                        $existingSchedules = $tour ? $tour->schedules->pluck('schedule_id')->toArray() : [];
                    @endphp

                    @forelse($schedules ?? [] as $schedule)
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="schedule_{{ $schedule->schedule_id }}"
                                   name="schedules[]"
                                   value="{{ $schedule->schedule_id }}"
                                   {{ in_array($schedule->schedule_id, old('schedules', $existingSchedules)) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="schedule_{{ $schedule->schedule_id }}">
                                <strong>{{ $schedule->start_time }}</strong> - {{ $schedule->end_time }}
                                @if($schedule->label)
                                    <span class="badge badge-info">{{ $schedule->label }}</span>
                                @endif
                                <small class="text-muted">(Capacidad: {{ $schedule->max_capacity }})</small>
                            </label>
                        </div>
                    @empty
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No hay horarios disponibles.
                            <a href="{{ route('admin.tours.schedule.index') }}" target="_blank">
                                Crear horarios
                            </a>
                        </div>
                    @endforelse
                </div>

                @error('schedules')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Opción para crear horario nuevo (opcional) --}}
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-plus"></i> Crear Horario Nuevo
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" style="display: none;">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="new_schedule_start">Hora Inicio</label>
                            <input type="time"
                                   name="new_schedule[start_time]"
                                   id="new_schedule_start"
                                   class="form-control"
                                   value="{{ old('new_schedule.start_time') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="new_schedule_end">Hora Fin</label>
                            <input type="time"
                                   name="new_schedule[end_time]"
                                   id="new_schedule_end"
                                   class="form-control"
                                   value="{{ old('new_schedule.end_time') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="new_schedule_capacity">Capacidad</label>
                            <input type="number"
                                   name="new_schedule[max_capacity]"
                                   id="new_schedule_capacity"
                                   class="form-control"
                                   value="{{ old('new_schedule.max_capacity', 12) }}"
                                   min="1">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="new_schedule_label">Etiqueta (opcional)</label>
                    <input type="text"
                           name="new_schedule[label]"
                           id="new_schedule_label"
                           class="form-control"
                           placeholder="Ej: Mañana, Tarde"
                           value="{{ old('new_schedule.label') }}">
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox"
                           class="custom-control-input"
                           id="new_schedule_create"
                           name="new_schedule[create]"
                           value="1"
                           {{ old('new_schedule.create') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="new_schedule_create">
                        Crear este horario y asignarlo al tour
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i> Información
                </h3>
            </div>
            <div class="card-body">
                <h5>Horarios</h5>
                <p class="small">
                    Selecciona uno o más horarios en los que este tour estará disponible.
                </p>
                <hr>
                <h5>Crear Nuevo</h5>
                <p class="small mb-0">
                    Si necesitas un horario que no existe, puedes crearlo desde aquí
                    y se asignará automáticamente a este tour.
                </p>
            </div>
        </div>

        @if($tour ?? false)
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Horarios Actuales</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($tour->schedules as $schedule)
                            <li class="list-group-item">
                                <strong>{{ $schedule->start_time }}</strong> - {{ $schedule->end_time }}
                                @if($schedule->label)
                                    <br><small class="text-muted">{{ $schedule->label }}</small>
                                @endif
                            </li>
                        @empty
                            <li class="list-group-item text-muted">Sin horarios asignados</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>
