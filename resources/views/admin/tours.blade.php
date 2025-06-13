@extends('adminlte::page')

@section('title', 'Tours')

@section('content_header')
    <h1>Gestión de Tours</h1>
@stop

@section('content')
    <div class="p-3 table-responsive">
        <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
            <i class="fas fa-plus"></i> Añadir Tour
        </a>

        <table class="table table-striped table-bordered table-hover">
            <thead class="bg-primary text-white">
                <tr>
                    <th>ID TOUR</th>
                    <th>Nombre</th>
                    <th>Precio Adulto</th>
                    <th>Precio Niño</th>
                    <th>Duración (horas)</th>
                    <th>Ubicación</th>
                    <th>Categoria</th>
                    <th>Idioma</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tours as $tour)
                    <tr>
                        <td>{{ $tour->id_tour }}</td>
                        <td>{{ $tour->nombre }}</td>
                        <td>${{ number_format($tour->precio_adulto, 2) }}</td>
                        <td>${{ number_format($tour->precio_nino, 2) }}</td>
                        <td>{{ $tour->duracion_horas }}</td>
                        <td>{{ $tour->ubicacion }}</td>
                        <td>{{ $tour->tipo_tour }}</td>
                        <td>{{ $tour->idioma_disponible }}</td>
                        <td>
                            <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $tour->id_tour }}">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('admin.tours.destroy', $tour->id_tour) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Deseas eliminar este tour?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Editar Tour -->
                    <div class="modal fade" id="modalEditar{{ $tour->id_tour }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('admin.tours.update', $tour->id_tour) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Editar Tour</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Nombre</label>
                                            <input type="text" name="nombre" class="form-control" value="{{ $tour->nombre }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="descripcion" class="form-control" rows="2">{{ $tour->descripcion }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Precio Adulto</label>
                                            <input type="number" step="0.01" name="precio_adulto" class="form-control" value="{{ $tour->precio_adulto }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Precio Niño</label>
                                            <input type="number" step="0.01" name="precio_nino" class="form-control" value="{{ $tour->precio_nino }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Duración (horas)</label>
                                            <input type="number" name="duracion_horas" class="form-control" value="{{ $tour->duracion_horas }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Ubicación</label>
                                            <input type="text" name="ubicacion" class="form-control" value="{{ $tour->ubicacion }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Tour</label>
                                            <select name="tipo_tour" class="form-control" required>
                                                <option value="Half Day" {{ $tour->tipo_tour == 'Half Day' ? 'selected' : '' }}>Half Day</option>
                                                <option value="Full Day" {{ $tour->tipo_tour == 'Full Day' ? 'selected' : '' }}>Full Day</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Idioma Disponible</label>
                                            <select name="idioma_disponible" class="form-control" required>
                                                <option value="Español" {{ $tour->idioma_disponible == 'Español' ? 'selected' : '' }}>Español</option>
                                                <option value="Inglés" {{ $tour->idioma_disponible == 'Inglés' ? 'selected' : '' }}>Inglés</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-warning">Actualizar</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal Registrar Tour -->
    <div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.tours.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Tour</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Precio Adulto</label>
                            <input type="number" step="0.01" name="precio_adulto" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Precio Niño</label>
                            <input type="number" step="0.01" name="precio_nino" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Duración (horas)</label>
                            <input type="number" name="duracion_horas" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ubicación</label>
                            <input type="text" name="ubicacion" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Tour</label>
                            <select name="tipo_tour" class="form-control" required>
                                <option value="Half Day">Half Day</option>
                                <option value="Full Day">Full Day</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Idioma Disponible</label>
                            <select name="idioma_disponible" class="form-control" required>
                                <option value="Español">Español</option>
                                <option value="Inglés">Inglés</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    {{-- Agrega aquí tus estilos si los necesitas --}}
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: "{{ session('success') }}",
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
@stop
