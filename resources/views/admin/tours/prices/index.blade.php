@extends('adminlte::page')

@section('title', 'Precios - ' . $tour->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Precios: {{ $tour->name }}</h1>
        <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Tours
        </a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            {{-- Formulario de actualización masiva --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Categorías y Precios Configurados</h3>
                </div>

                <form action="{{ route('admin.tours.prices.bulk-update', $tour) }}" method="POST">
                    @csrf

                    <div class="card-body p-0">
                        @if($tour->prices->isEmpty())
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                <p>No hay categorías configuradas para este tour.</p>
                                <p class="small">Usa el formulario de la derecha para agregar categorías.</p>
                            </div>
                        @else
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Categoría</th>
                                        <th>Rango Edad</th>
                                        <th style="width: 150px">Precio (USD)</th>
                                        <th style="width: 100px">Mín</th>
                                        <th style="width: 100px">Máx</th>
                                        <th style="width: 100px">Estado</th>
                                        <th style="width: 80px">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tour->prices as $price)
                                        <tr>
                                            <td>
                                                <strong>{{ $price->category->name }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    <code>{{ $price->category->slug }}</code>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ $price->category->age_range }}
                                                </span>
                                            </td>
                                            <td>
                                                <input type="hidden"
                                                       name="prices[{{ $loop->index }}][category_id]"
                                                       value="{{ $price->category_id }}">

                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">$</span>
                                                    </div>
                                                    <input type="number"
                                                           class="form-control"
                                                           name="prices[{{ $loop->index }}][price]"
                                                           value="{{ $price->price }}"
                                                           step="0.01"
                                                           min="0"
                                                           required>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number"
                                                       class="form-control form-control-sm"
                                                       name="prices[{{ $loop->index }}][min_quantity]"
                                                       value="{{ $price->min_quantity }}"
                                                       min="0"
                                                       max="255"
                                                       required>
                                            </td>
                                            <td>
                                                <input type="number"
                                                       class="form-control form-control-sm"
                                                       name="prices[{{ $loop->index }}][max_quantity]"
                                                       value="{{ $price->max_quantity }}"
                                                       min="0"
                                                       max="255"
                                                       required>
                                            </td>
                                            <td>
                                                <div class="custom-control custom-switch">
                                                    <input type="hidden"
                                                           name="prices[{{ $loop->index }}][is_active]"
                                                           value="0">
                                                    <input type="checkbox"
                                                           class="custom-control-input"
                                                           id="active_{{ $price->tour_price_id }}"
                                                           name="prices[{{ $loop->index }}][is_active]"
                                                           value="1"
                                                           {{ $price->is_active ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                           for="active_{{ $price->tour_price_id }}">
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button"
                                                        class="btn btn-sm btn-danger"
                                                        data-toggle="modal"
                                                        data-target="#deleteModal{{ $price->tour_price_id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                                {{-- Modal Eliminar --}}
                                                <div class="modal fade" id="deleteModal{{ $price->tour_price_id }}">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger">
                                                                <h5 class="modal-title">Eliminar Categoría</h5>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                ¿Eliminar <strong>{{ $price->category->name }}</strong> de este tour?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                                    Cancelar
                                                                </button>
                                                                <form action="{{ route('admin.tours.prices.destroy', [$tour, $price]) }}"
                                                                      method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">
                                                                        Eliminar
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                    @if($tour->prices->isNotEmpty())
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Formulario para agregar nueva categoría --}}
            @if($availableCategories->isNotEmpty())
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Agregar Categoría</h3>
                    </div>

                    <form action="{{ route('admin.tours.prices.store', $tour) }}" method="POST">
                        @csrf

                        <div class="card-body">
                            <div class="form-group">
                                <label for="category_id">Categoría</label>
                                <select name="category_id" id="category_id" class="form-control" required>
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($availableCategories as $category)
                                        <option value="{{ $category->category_id }}">
                                            {{ $category->name }} ({{ $category->age_range }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="price">Precio (USD)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number"
                                           name="price"
                                           id="price"
                                           class="form-control"
                                           step="0.01"
                                           min="0"
                                           required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="min_quantity">Mínimo</label>
                                        <input type="number"
                                               name="min_quantity"
                                               id="min_quantity"
                                               class="form-control"
                                               min="0"
                                               max="255"
                                               value="0"
                                               required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="max_quantity">Máximo</label>
                                        <input type="number"
                                               name="max_quantity"
                                               id="max_quantity"
                                               class="form-control"
                                               min="0"
                                               max="255"
                                               value="12"
                                               required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-plus"></i> Agregar
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Información --}}
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Información
                    </h3>
                </div>
                <div class="card-body">
                    <p><strong>Tour:</strong> {{ $tour->name }}</p>
                    <p><strong>Categorías configuradas:</strong> {{ $tour->prices->count() }}</p>
                    <hr>
                    <h5>Campos</h5>
                    <ul class="small">
                        <li><strong>Precio:</strong> Precio en USD para esta categoría</li>
                        <li><strong>Mín:</strong> Cantidad mínima requerida por reserva</li>
                        <li><strong>Máx:</strong> Cantidad máxima permitida por reserva</li>
                        <li><strong>Estado:</strong> Activa/Inactiva en formularios</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    // Validar que max >= min
    const minInput = document.getElementById('min_quantity');
    const maxInput = document.getElementById('max_quantity');

    function validateQuantities() {
        const min = parseInt(minInput.value) || 0;
        const max = parseInt(maxInput.value) || 0;

        if (max < min) {
            maxInput.setCustomValidity('El máximo debe ser mayor o igual al mínimo');
        } else {
            maxInput.setCustomValidity('');
        }
    }

    if (minInput && maxInput) {
        minInput.addEventListener('input', validateQuantities);
        maxInput.addEventListener('input', validateQuantities);
    }
</script>
@stop
