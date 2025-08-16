{{-- resources/views/admin/Cart/general.blade.php --}}
@extends('adminlte::page')

@section('title', 'Carritos de Todos los Clientes')

@section('content_header')
    <h1><i class="fas fa-shopping-cart"></i> Carritos de Todos los Clientes</h1>
@stop

@section('content')
{{-- 🔎 Filtros --}}
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <strong><i class="fas fa-filter"></i> Filtros</strong>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Correo del Cliente</label>
                <input type="text" name="correo" class="form-control" placeholder="cliente@correo.com" value="{{ request('correo') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-control">
                    <option value="">-- Todos --</option>
                    <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
                <a href="{{ route('admin.cart.general') }}" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

{{-- 🧺 Tabla: 1 fila = 1 carrito --}}
@if($carritos->count())
<div class="table-responsive">
    <table class="table table-bordered table-hover shadow-sm">
        <thead class="table-dark">
            <tr class="text-center align-middle">
                <th>Cliente</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Ítems</th>
                <th>Total carrito</th>
                <th>Estado</th>
                <th>Última modificación</th>
                <th style="width: 240px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @foreach($carritos as $cart)
            <tr class="text-center align-middle">
                <td><strong>{{ $cart->user->full_name }}</strong></td>
                <td>{{ $cart->user->email }}</td>
                <td>{{ $cart->user->full_phone ?? 'N/A' }}</td>
                <td>
                    <button class="btn btn-sm btn-info"
                        data-bs-toggle="modal"
                        data-bs-target="#modalItemsCart{{ $cart->cart_id }}"
                        title="Ver tours" data-bs-toggle="tooltip">
                        <i class="fas fa-list"></i> ({{ $cart->items_count }})
                    </button>
                </td>
                <td><strong>${{ number_format($cart->total_usd, 2) }}</strong></td>
                <td>
                    <span class="badge {{ $cart->is_active ? 'bg-success' : 'bg-danger' }}">
                        <i class="fas {{ $cart->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                        {{ $cart->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td>{{ $cart->updated_at->format('d/m/Y H:i') }}</td>

                <td class="text-center">
                    {{-- 👁️ Ver tours --}}
                    <button class="btn btn-info btn-sm me-1"
                        data-bs-toggle="modal"
                        data-bs-target="#modalItemsCart{{ $cart->cart_id }}"
                        title="Ver tours" data-bs-toggle="tooltip">
                        <i class="fas fa-eye"></i>
                    </button>

                    {{-- 🔁 Activar/Desactivar carrito --}}
                    <form action="{{ route('admin.cart.toggle', $cart->cart_id) }}" method="POST" class="d-inline-block me-1">
                        @csrf @method('PATCH')
                        <button class="btn btn-sm {{ $cart->is_active ? 'btn-toggle' : 'btn-toggle' }}"
                                title="{{ $cart->is_active ? 'Desactivar carrito' : 'Activar carrito' }}"
                                data-bs-toggle="tooltip">
                            <i class="fas {{ $cart->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                        </button>
                    </form>

                    {{-- 🗑️ Eliminar carrito completo --}}
                    <form action="{{ route('admin.cart.destroy', $cart->cart_id) }}" method="POST" class="d-inline-block form-eliminar-carrito">
                        @csrf @method('DELETE')
                        <button class="btn btn-delete btn-sm" title="Eliminar carrito" data-bs-toggle="tooltip">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

{{-- 📦 Modal de tours por carrito --}}
@foreach($carritos as $cart)
<div class="modal fade"
     id="modalItemsCart{{ $cart->cart_id }}"
     tabindex="-1"
     aria-labelledby="modalLabelCart{{ $cart->cart_id }}"
     aria-hidden="true"
     data-bs-backdrop="static"
     data-bs-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalLabelCart{{ $cart->cart_id }}">
            Tours del carrito de {{ $cart->user->full_name }}
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-striped mb-0">
                <thead class="table-dark">
                    <tr class="text-center">
                        <th>Tour</th>
                        <th>Fecha</th>
                        <th>Horario</th>
                        <th>Idioma</th>
                        <th>Adultos</th>
                        <th>Niños</th>
                        <th>Total ítem</th>
                        <th>Estado</th>
                        <th style="width: 120px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($cart->items as $item)
                    @php
                        $ap = (float)($item->tour->adult_price ?? 0);
                        $kp = (float)($item->tour->kid_price   ?? 0);
                        $aq = (int)($item->adults_quantity ?? 0);
                        $kq = (int)($item->kids_quantity   ?? 0);
                        $itemTotal = ($ap * $aq) + ($kp * $kq);
                    @endphp
                    <tr class="text-center align-middle">
                        <td>{{ $item->tour->name ?? '—' }}</td>
                        <td>{{ $item->tour_date ?? '—' }}</td>
                        <td>
                            @if($item->schedule)
                                <span class="badge bg-success">
                                    {{ \Carbon\Carbon::parse($item->schedule->start_time)->format('g:i A') }}
                                    –
                                    {{ \Carbon\Carbon::parse($item->schedule->end_time)->format('g:i A') }}
                                </span>
                            @else
                                <span class="text-muted">Sin horario</span>
                            @endif
                        </td>
                        <td>{{ $item->language->name ?? '—' }}</td>
                        <td>{{ $aq }}</td>
                        <td>{{ $kq }}</td>
                        <td><strong>${{ number_format($itemTotal, 2) }}</strong></td>
                        <td>
                            <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-danger' }}">
                                <i class="fas {{ $item->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                {{ $item->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="d-flex justify-content-center gap-1">
                            {{-- ✏️ Editar: cerrar modal del carrito, abrir modal de edición --}}
                            <button
                                class="btn btn-sm btn-edit btn-open-edit"
                                title="Editar" data-bs-toggle="tooltip"
                                data-parent="#modalItemsCart{{ $cart->cart_id }}"
                                data-child="#modalEditar{{ $item->item_id }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            {{-- 🗑️ Eliminar ítem --}}
                            <form action="{{ route('admin.cart.item.destroy', $item->item_id) }}" method="POST" class="d-inline-block form-eliminar">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-delete" title="Eliminar" data-bs-toggle="tooltip">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No hay tours en este carrito.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
      </div>

      @php
        $cartTotalFooter = $cart->items->sum(function($it){
            $ap=(float)($it->tour->adult_price ?? 0);
            $kp=(float)($it->tour->kid_price   ?? 0);
            $aq=(int)($it->adults_quantity ?? 0);
            $kq=(int)($it->kids_quantity   ?? 0);
            return ($ap*$aq)+($kp*$kq);
        });
      @endphp

      <div class="modal-footer d-flex justify-content-between">
        <div class="text-muted">
            Cliente: <strong>{{ $cart->user->full_name }}</strong> ·
            Email: <strong>{{ $cart->user->email }}</strong> ·
            Tel: <strong>{{ $cart->user->full_phone ?? 'N/A' }}</strong> ·
            Última modificación: <strong>{{ $cart->updated_at->format('d/m/Y H:i') }}</strong>
        </div>
        <div class="fs-5">
            Total del carrito: <strong>${{ number_format($cartTotalFooter, 2) }}</strong>
        </div>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
      </div>

    </div>
  </div>
</div>

{{-- 🛠️ Modal de edición de ÍTEM (separado, no anidado) --}}
@foreach($cart->items as $item)
<div class="modal fade"
     id="modalEditar{{ $item->item_id }}"
     tabindex="-1"
     aria-labelledby="modalLabel{{ $item->item_id }}"
     aria-hidden="true"
     data-bs-backdrop="static"
     data-bs-keyboard="false">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('admin.cart.updateFromPost', $item->item_id) }}" class="modal-content">
      @csrf
      <div class="modal-header bg-edit text-white">
        <h5 class="modal-title" id="modalLabel{{ $item->item_id }}">Editar Ítem del Carrito</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
            <label>Fecha del Tour</label>
            <input type="date" name="tour_date" class="form-control" value="{{ $item->tour_date }}" required>
        </div>

        <div class="mb-3">
            <label>Horario</label>
            <select name="schedule_id" class="form-select">
                @foreach(($item->tour->schedules ?? []) as $sch)
                    @php
                        $label = \Carbon\Carbon::parse($sch->start_time)->format('g:i A').' – '.\Carbon\Carbon::parse($sch->end_time)->format('g:i A');
                    @endphp
                    <option value="{{ $sch->schedule_id }}" {{ (int)$item->schedule_id === (int)$sch->schedule_id ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Cantidad de Adultos</label>
            <input type="number" name="adults_quantity" class="form-control" value={{ $item->adults_quantity }} min="1" required>
        </div>
        <div class="mb-3">
            <label>Cantidad de Niños</label>
            <input type="number" name="kids_quantity" class="form-control" value={{ $item->kids_quantity }} min="0" max="2">
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="check{{ $item->item_id }}" {{ $item->is_active ? 'checked' : '' }}>
            <label class="form-check-label" for="check{{ $item->item_id }}">Reserva activa</label>
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Cambios</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>
@endforeach
@endforeach

@else
<div class="alert alert-info text-center">
    <i class="fas fa-info-circle"></i> No hay registros que coincidan con los filtros aplicados.
</div>
@endif
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
        <script>
            Swal.fire({ icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 2000 });
        </script>
    @endif
    @if(session('error'))
        <script>
            Swal.fire({ icon: 'error', title: 'Error', text: @json(session('error')) });
        </script>
    @endif

    <script>
    // Habilita tooltips
    document.addEventListener('DOMContentLoaded', () => {
        const list = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        list.map(el => new bootstrap.Tooltip(el));
    });

    // Evitar "modales anidados"
    document.addEventListener('click', function(e){
      const btn = e.target.closest('.btn-open-edit');
      if (!btn) return;
      e.preventDefault();
      const parentEl = document.querySelector(btn.getAttribute('data-parent'));
      const childEl  = document.querySelector(btn.getAttribute('data-child'));
      if (!parentEl || !childEl) return;

      const parent = bootstrap.Modal.getOrCreateInstance(parentEl);
      parent.hide();
      parentEl.addEventListener('hidden.bs.modal', function onHidden(){
        parentEl.removeEventListener('hidden.bs.modal', onHidden);
        const child = bootstrap.Modal.getOrCreateInstance(childEl);
        child.show();
        childEl.addEventListener('hidden.bs.modal', function onChildHidden(){
          childEl.removeEventListener('hidden.bs.modal', onChildHidden);
          parent.show();
        });
      });
    });

    // Limpieza de backdrops sobrantes
    document.addEventListener('hidden.bs.modal', function(){
      const backs = document.querySelectorAll('.modal-backdrop');
      if (backs.length > 1) {
        backs.forEach((b, i) => { if (i < backs.length - 1) b.remove(); });
      }
    });

    // Confirmación eliminar ÍTEM
    document.querySelectorAll('.form-eliminar').forEach(form => {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
          title: '¿Eliminar este ítem del carrito?',
          text: 'Esta acción no se puede deshacer.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Sí, eliminar',
          cancelButtonText: 'Cancelar'
        }).then((r) => { if (r.isConfirmed) form.submit(); });
      });
    });

    // Confirmación eliminar CARRITO completo
    document.querySelectorAll('.form-eliminar-carrito').forEach(form => {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
          title: '¿Eliminar este carrito completo?',
          text: 'Se eliminarán todos los tours contenidos.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Sí, eliminar',
          cancelButtonText: 'Cancelar'
        }).then((r) => { if (r.isConfirmed) form.submit(); });
      });
    });
    </script>
@stop
