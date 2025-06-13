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
                    <th>Categoría</th>
                    <th>Idioma</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tours as $tour)
                    <tr>
                        <td>{{ $tour->tour_id }}</td>
                        <td>{{ $tour->name }}</td>
                        <td>${{ number_format($tour->adult_price, 2) }}</td>
                        <td>${{ number_format($tour->kid_price, 2) }}</td>
                        <td>{{ $tour->length }}</td>
                        <td>{{ $tour->category->name ?? 'Sin categoría' }}</td>
                        <td>{{ $tour->language->name ?? 'Sin idioma' }}</td>
                        <td>
                            <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $tour->tour_id }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.tours.destroy', $tour->tour_id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Deseas eliminar este tour?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Editar Tour -->
                    <div class="modal fade" id="modalEditar{{ $tour->tour_id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('admin.tours.update', $tour->tour_id) }}" method="POST">
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
                                            <input type="text" name="name" class="form-control" value="{{ $tour->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Descripción</label>
                                            <textarea name="description" class="form-control" rows="2">{{ $tour->description }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Precio Adulto</label>
                                            <input type="number" step="0.01" name="adult_price" class="form-control" value="{{ $tour->adult_price }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Precio Niño</label>
                                            <input type="number" step="0.01" name="kid_price" class="form-control" value="{{ $tour->kid_price }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Duración (horas)</label>
                                            <input type="number" name="length" class="form-control" value="{{ $tour->length }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Categoría</label>
                                            <select name="category_id" class="form-control" required>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->category_id }}" {{ $tour->category_id == $category->category_id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Idioma</label>
                                            <select name="tour_language_id" class="form-control" required>
                                                @foreach ($languages as $lang)
                                                    <option value="{{ $lang->tour_language_id }}" {{ $tour->tour_language_id == $lang->tour_language_id ? 'selected' : '' }}>
                                                        {{ $lang->name }}
                                                    </option>
                                                @endforeach
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
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Precio Adulto</label>
                            <input type="number" step="0.01" name="adult_price" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Precio Niño</label>
                            <input type="number" step="0.01" name="kid_price" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Duración (horas)</label>
                            <input type="number" name="length" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Categoría</label>
                            <select name="category_id" class="form-control" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Idioma</label>
                            <select name="tour_language_id" class="form-control" required>
                                @foreach ($languages as $lang)
                                    <option value="{{ $lang->tour_language_id }}">{{ $lang->name }}</option>
                                @endforeach
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
    {{-- Tus estilos personalizados aquí si lo necesitas --}}
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
