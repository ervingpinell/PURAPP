@extends('adminlte::page')

@section('title', 'Reservas')

@section('content_header')
    <h1>Gestión de Reservas</h1>
@stop

@section('content')
    <div class="p-3 table-responsive">
        <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
            <i class="fas fa-plus"></i> Añadir Reserva
        </a>
        <a href="{{ route('admin.reservas.pdf') }}" class="btn btn-danger mb-3" target="_blank">
            <i class="fas fa-file-pdf"></i> Descargar PDF
        </a>

        

        <table class="table table-striped table-bordered table-hover">
            <thead class="bg-primary text-white">
                <tr>
                    <th>ID RESERVA</th>
                    <th>Cliente</th>
                    <th>Tour</th>
                    <th>Fecha Reserva</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Idioma</th>
                    <th>Precio Adulto</th>
                    <th>Precio Niño</th>
                    <th>Estado</th>
                    <th>Codigo de Reserva</th>
                    <th>Cantidad de Adultos</th>
                    <th>Cantidad de Niños</th>
                    <th>Total a Pagar</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reservas as $reserva)
                    <tr>
                        <td>{{ $reserva->id_reserva }}</td>
                        <td>{{ $reserva->user->full_name ?? 'Sin cliente' }}</td>
                        <td>{{ $reserva->tour->nombre ?? 'Sin tour' }}</td>
                        <td>{{ $reserva->fecha_reserva }}</td>
                        <td>{{ $reserva->fecha_inicio }}</td>
                        <td>{{ $reserva->fecha_fin }}</td>
                        <td>{{ $reserva->idioma_tour }}</td>
                        <td>${{ $reserva->precio_adulto }}</td>
                        <td>${{ $reserva->precio_nino }}</td>
                        <td>{{ $reserva->estado_reserva }}</td>
                        <td>{{ $reserva->codigo_reserva }}</td>
                        <td>{{ $reserva->cantidad_adultos }}</td>
                        <td>{{ $reserva->cantidad_ninos }}</td>
                        <td>${{ $reserva->total_pago }}</td>
                        <td>
                            <!-- Editar -->
                            <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar{{ $reserva->id_reserva }}">
                                <i class="fas fa-edit"></i>
                            </a>

                            <!-- Eliminar -->
                            <form action="{{ route('admin.reservas.destroy', $reserva->id_reserva) }}" method="POST" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Deseas eliminar esta reserva?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>

                            <!-- Descargar Comprobante -->
                            <a href="{{ route('admin.reservas.comprobante', $reserva->id_reserva) }}" 
                                class="btn btn-success btn-sm" 
                                title="Descargar comprobante">
                                <i class="fas fa-file-download"></i>
                            </a>
                        </td>
                    </tr>

                    <!-- Modal Editar Reserva -->
                    <div class="modal fade" id="modalEditar{{ $reserva->id_reserva }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form action="{{ route('admin.reservas.update', $reserva->id_reserva) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Editar Reserva</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Cantidad de Adultos</label>
                                            <input type="number" name="cantidad_adultos" class="form-control cantidad-adultos" min="1" required value="{{ $reserva->cantidad_adultos }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Cantidad de Niños</label>
                                            <input type="number" name="cantidad_ninos" class="form-control cantidad-ninos" min="0" required value="{{ $reserva->cantidad_ninos }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Precio Adulto</label>
                                            <input type="number" name="precio_adulto" class="form-control precio-adulto" step="0.01" required value="{{ $reserva->precio_adulto }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Precio Niño</label>
                                            <input type="number" name="precio_nino" class="form-control precio-nino" step="0.01" required value="{{ $reserva->precio_nino }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Total a Pagar</label>
                                            <input type="number" name="total_pago" class="form-control total-pago" readonly value="{{ $reserva->total_pago }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Estado de la Reserva</label>
                                            <select name="estado_reserva" class="form-control" required>
                                                <option value="Pago Pendiente" {{ $reserva->estado_reserva == 'Pago Pendiente' ? 'selected' : '' }}>Pago Pendiente</option>
                                                <option value="Pago Cancelado" {{ $reserva->estado_reserva == 'Pago Cancelado' ? 'selected' : '' }}>Pago Cancelado</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Notas</label>
                                            <textarea name="notas" class="form-control">{{ $reserva->notas }}</textarea>
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

    <!-- Modal Registrar Reserva -->
    <div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.reservas.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Reserva</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Cliente</label>
                            <select name="id_user" class="form-control" required>
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->id_user }}">{{ $user->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tour</label>
                            <select name="id_tour" id="selectTour" class="form-control select-tour" required>
                                @foreach(\App\Models\Tour::all() as $tour)
                                    <option 
                                        value="{{ $tour->id_tour }}"
                                        data-precio-adulto="{{ $tour->precio_adulto }}"
                                        data-precio-nino="{{ $tour->precio_nino }}"
                                    >
                                        {{ $tour->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha de Reserva</label>
                            <input type="date" name="fecha_reserva" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha de Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha de Fin</label>
                            <input type="date" name="fecha_fin" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Idioma del Tour</label>
                            <select name="idioma_tour" class="form-control" required>
                                <option value="Español">Español</option>
                                <option value="Inglés">Inglés</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cantidad de Adultos</label>
                            <input type="number" name="cantidad_adultos" class="form-control" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cantidad de Niños</label>
                            <input type="number" name="cantidad_ninos" class="form-control" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Precio Adulto</label>
                            <input type="number" name="precio_adulto" class="form-control precio-adulto" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Precio Niño</label>
                            <input type="number" name="precio_nino" class="form-control precio-nino" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total a Pagar</label>
                            <input type="number" name="total_pago" class="form-control total-pago" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Estado de la Reserva</label>
                            <select name="estado_reserva" class="form-control" required>
                                <option value="Pago Pendiente">Pago Pendiente</option>
                                <option value="Pago Cancelado">Pago Cancelado</option>
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

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<!-- Script para calcular el total -->
<script>
    function calcularTotal(modal) {
        const adultos = parseInt(modal.querySelector('.cantidad-adultos')?.value || 0);
        const ninos = parseInt(modal.querySelector('.cantidad-ninos')?.value || 0);
        const precioAdulto = parseFloat(modal.querySelector('.precio-adulto')?.value || 0);
        const precioNino = parseFloat(modal.querySelector('.precio-nino')?.value || 0);

        const total = (adultos * precioAdulto) + (ninos * precioNino);
        const totalInput = modal.querySelector('.total-pago');
        if (totalInput) totalInput.value = total.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('input', () => calcularTotal(modal));
        });

        // Cargar precios automáticamente al seleccionar un tour
        const selectTour = document.getElementById('selectTour');
        if (selectTour) {
            selectTour.addEventListener('change', function () {
                const selected = this.options[this.selectedIndex];
                const precioAdulto = selected.getAttribute('data-precio-adulto');
                const precioNino = selected.getAttribute('data-precio-nino');

                const modal = this.closest('.modal');
                if (modal) {
                    modal.querySelector('.precio-adulto').value = precioAdulto;
                    modal.querySelector('.precio-nino').value = precioNino;
                    calcularTotal(modal);
                }
            });
        }
    });
</script>



@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: '{{ session('success') }}',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK'
    });
</script>
@endif
@stop

