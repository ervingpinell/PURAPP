@extends('adminlte::page')

@section('title', 'Mi Carrito')

@section('content_header')
    <h1><i class="fas fa-shopping-cart"></i> Carrito de Reservas</h1>
@stop

@section('content')
    {{-- Filtros --}}
    <form method="GET" class="mb-4 d-flex justify-content-center align-items-center gap-2">
        <label class="mb-0"><i class="fas fa-filter"></i> Estado:</label>
        <select name="estado" class="form-control w-auto">
            <option value="">-- Todos --</option>
            <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Pendientes</option>
            <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Canceladas</option>
        </select>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Filtrar
        </button>
    </form>

    @if($cart && $cart->items->count())
        {{-- Información del cliente --}}
        <div class="card mb-4 shadow">
            <div class="card-header bg-info text-white">
                <i class="fas fa-user"></i> Información del Cliente
            </div>
            <div class="card-body">
                <p><i class="fas fa-id-card"></i> <strong>Nombre:</strong> {{ $cart->user->full_name ?? 'N/A' }}</p>
                <p><i class="fas fa-envelope"></i> <strong>Email:</strong> {{ $cart->user->email ?? 'N/A' }}</p>
                <p><i class="fas fa-phone"></i> <strong>Teléfono:</strong> {{ $cart->user->full_phone ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="table-responsive">
            <table class="table table-bordered table-hover shadow-sm">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Tour</th>
                        <th>Fecha</th>
                        <th>Idioma</th>
                        <th>Hotel</th>
                        <th>Horario</th>
                        <th>Adultos</th>
                        <th>Niños</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-center align-middle">
                    @foreach($cart->items as $item)
                        <tr>
                            <td>{{ $item->tour->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tour_date)->format('d/m/Y') }}</td>
                            <td>{{ $item->language->name }}</td>
                            <td>
                                @if($item->is_other_hotel)
                                    {{ $item->other_hotel_name }}
                                @else
                                    {{ optional($item->hotel)->name ?? '—' }}
                                @endif
                            </td>
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
                            <td>{{ $item->adults_quantity }}</td>
                            <td>{{ $item->kids_quantity }}</td>
                            <td>
                                ${{ number_format(
                                    $item->tour->adult_price * $item->adults_quantity +
                                    $item->tour->kid_price   * $item->kids_quantity,
                                    2
                                ) }}
                            </td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge bg-success"><i class="fas fa-check-circle"></i> Activo</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fas fa-times-circle"></i> Inactivo</span>
                                @endif
                            </td>
                            <td>
                                {{-- Editar --}}
                                <button class="btn btn-sm btn-edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditar{{ $item->item_id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                {{-- Eliminar --}}
                                <form method="POST"
                                      action="{{ route('admin.cart.item.destroy', $item->item_id) }}"
                                      class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @php
            // Fallback de totales (por si el controlador no envió adminSubtotal/adminDiscount/adminTotal)
            $__subtotal = $cart->items->sum(fn($i) =>
                ($i->tour->adult_price * $i->adults_quantity) + ($i->tour->kid_price * $i->kids_quantity)
            );
            $__adminSubtotal = isset($adminSubtotal) ? $adminSubtotal : $__subtotal;
            $__adminDiscount = isset($adminDiscount) ? $adminDiscount : 0;
            $__adminTotal    = isset($adminTotal)    ? $adminTotal    : max($__adminSubtotal - $__adminDiscount, 0);
        @endphp

        {{-- Código Promocional (ADMIN) --}}
        <div class="card mt-4 shadow">
            <div class="card-header bg-secondary text-white">
                <i class="fas fa-tags"></i> Código Promocional
            </div>

            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <input type="text" id="promo-code" class="form-control w-auto" placeholder="Ingresa código"
                        value="{{ $adminPromo['code'] ?? '' }}" />
                    <button type="button" id="apply-promo" class="btn btn-primary">Aplicar</button>
                    @if(!empty($adminPromo))
                        <button type="button" id="remove-promo" class="btn btn-outline-danger">Quitar</button>
                    @endif

                    {{-- Hidden para UI (se sincroniza con el del form de abajo) --}}
                    <input type="hidden" id="promo_code_hidden_ui" value="{{ $adminPromo['code'] ?? '' }}">
                </div>

                {{-- Mensaje (único, sin duplicados) --}}
                <div id="promo-message" class="small mt-2">
                    @if(!empty($adminPromo))
                        <span class="text-success">
                            {{ $adminPromo['code'] }} aplicado
                            ({{ $adminPromo['amount'] ? '$'.number_format($adminPromo['amount'],2) : '' }}
                            {{ $adminPromo['amount'] && $adminPromo['percent'] ? ' + ' : '' }}
                            {{ $adminPromo['percent'] ? $adminPromo['percent'].'%' : '' }})
                        </span>
                    @endif
                </div>

                {{-- Totales dinámicos --}}
                <div class="mt-3">
                    <div><strong>Subtotal:</strong> $<span id="cart-subtotal">{{ number_format($__adminSubtotal, 2) }}</span></div>
                    <div><strong>Descuento:</strong> $<span id="cart-discount">{{ number_format($__adminDiscount, 2) }}</span></div>
                    <div class="fs-5">
                        <strong>Total estimado:</strong> $<span id="cart-total">{{ number_format($__adminTotal, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Confirmar y Enviar (el hidden promo_code VA DENTRO DEL FORM) --}}
        <form method="POST" action="{{ route('admin.reservas.storeFromCart') }}" class="mt-3">
            @csrf
            {{-- Este es el que viaja al controlador --}}
            <input type="hidden" name="promo_code" id="promo_code_hidden_form" value="{{ $adminPromo['code'] ?? '' }}">
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-paper-plane"></i> Confirmar y Enviar Solicitud de Reserva
            </button>
        </form>

        {{-- Modales de edición --}}
        @foreach($cart->items as $item)
            <div class="modal fade" id="modalEditar{{ $item->item_id }}" tabindex="-1" aria-labelledby="modalLabel{{ $item->item_id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('admin.cart.update', $item->item_id) }}" class="modal-content">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="is_active" value="0">

                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title" id="modalLabel{{ $item->item_id }}">Editar Ítem del Carrito</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Fecha del Tour</label>
                                <input type="date" name="tour_date" class="form-control" value="{{ $item->tour_date }}" required>
                            </div>
                            <div class="mb-3">
                                <label>Hotel</label>
                                <select name="hotel_id"
                                        id="edit_hotel_{{ $item->item_id }}"
                                        class="form-control">
                                    <option value="">Seleccione un hotel</option>
                                    @foreach($hotels as $hotel)
                                        <option value="{{ $hotel->hotel_id }}"
                                            {{ !$item->is_other_hotel && $item->hotel_id == $hotel->hotel_id ? 'selected':'' }}>
                                            {{ $hotel->name }}
                                        </option>
                                    @endforeach
                                    <option value="other" {{ $item->is_other_hotel ? 'selected':'' }}>
                                        Otro…
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3 {{ $item->is_other_hotel ? '' : 'd-none' }}"
                                 id="edit_other_container_{{ $item->item_id }}">
                                <label>Nombre de hotel</label>
                                <input type="text"
                                       name="other_hotel_name"
                                       class="form-control"
                                       value="{{ $item->other_hotel_name }}">
                            </div>
                            <input type="hidden"
                                   name="is_other_hotel"
                                   id="edit_is_other_{{ $item->item_id }}"
                                   value="{{ $item->is_other_hotel ? 1 : 0 }}">
                            <div class="mb-3">
                                <label>Horario</label>
                                <select name="schedule_id" class="form-control">
                                    <option value="">Seleccione un horario</option>
                                    @foreach($item->tour->schedules as $sched)
                                        <option value="{{ $sched->schedule_id }}"
                                            {{ $item->schedule && $item->schedule->schedule_id == $sched->schedule_id ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::parse($sched->start_time)->format('g:i A') }} –
                                            {{ \Carbon\Carbon::parse($sched->end_time)->format('g:i A') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Cantidad de Adultos</label>
                                <input type="number" name="adults_quantity" class="form-control" value="{{ $item->adults_quantity }}" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label>Cantidad de Niños</label>
                                <input type="number" name="kids_quantity" class="form-control" value="{{ $item->kids_quantity }}" min="0" max="2">
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="check{{ $item->item_id }}" {{ $item->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="check{{ $item->item_id }}">
                                    Reserva activa
                                </label>
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
    @else
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle"></i> Tu carrito está vacío.
        </div>
    @endif
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Confirmación al eliminar ítem
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', e => {
                e.preventDefault();
                Swal.fire({
                    title: '¿Eliminar este ítem?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d'
                }).then(result => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });

        // Toasts de sesión
        @if(session('success'))
            Swal.fire({ icon: 'success', title: '{{ session("success") }}', timer:2000, showConfirmButton:false });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: '¡Ups!', text: @js(session('error')), confirmButtonText:'Entendido' });
        @endif

        // Control Hotel “Otro…”
        document.addEventListener('DOMContentLoaded', () => {
            // En cada modal de edición
            @foreach($cart->items as $item)
                (function(){
                    const sel    = document.getElementById('edit_hotel_{{ $item->item_id }}'),
                          cont   = document.getElementById('edit_other_container_{{ $item->item_id }}'),
                          hid    = document.getElementById('edit_is_other_{{ $item->item_id }}');

                    sel.addEventListener('change', () => {
                        if (sel.value === 'other') {
                            cont.classList.remove('d-none');
                            hid.value = 1;
                        } else {
                            cont.classList.add('d-none');
                            cont.querySelector('input').value = '';
                            hid.value = 0;
                        }
                    });
                })();
            @endforeach
        });
    </script>

    {{-- Cupón (ADMIN) --}}
    <script>
    (() => {
      const csrf        = '{{ csrf_token() }}';
      const routeApply  = '{{ route("admin.cart.applyPromo") }}';
      const routeRemove = '{{ route("admin.cart.removePromo") }}';

      const $code    = document.getElementById('promo-code');
      const $apply   = document.getElementById('apply-promo');
      const $remove  = document.getElementById('remove-promo');
      const $msg     = document.getElementById('promo-message');

      const $sub     = document.getElementById('cart-subtotal');
      const $disc    = document.getElementById('cart-discount');
      const $total   = document.getElementById('cart-total');

      // Hiddens (UI + FORM REAL)
      const $hiddenUI   = document.getElementById('promo_code_hidden_ui');
      const $hiddenForm = document.getElementById('promo_code_hidden_form');

      // Helper: sincroniza ambos hiddens
      function syncHidden(value) {
        if ($hiddenUI)   $hiddenUI.value   = value || '';
        if ($hiddenForm) $hiddenForm.value = value || '';
      }

      $apply?.addEventListener('click', async () => {
        const code = ($code?.value || '').trim();
        if (!code) {
          return Swal.fire({ icon:'info', title:'Ingresa un código' });
        }
        try {
          const resp = await fetch(routeApply, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrf,
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ code })
          });
          const data = await resp.json();
          if (!data.ok) {
            syncHidden('');
            $msg.innerHTML = `<span class="text-danger">${data.message ?? 'No se pudo aplicar.'}</span>`;
            return;
          }

          syncHidden(data.code);
          $msg.innerHTML   = `<span class="text-success">${data.code} aplicado (${data.label})</span>`;
          $sub.textContent = data.subtotal;
          $disc.textContent= data.discount;
          $total.textContent = data.new_total;

          // Si no existía botón "Quitar", créalo dinámicamente
          if (!$remove) {
            const btn = document.createElement('button');
            btn.id = 'remove-promo';
            btn.type = 'button';
            btn.className = 'btn btn-outline-danger ms-2';
            btn.textContent = 'Quitar';
            $apply.parentElement.appendChild(btn);
            attachRemove(btn);
          }
        } catch (e) {
          Swal.fire({ icon:'error', title:'Error', text:'No se pudo validar el cupón.' });
        }
      });

      function attachRemove(btn){
        btn.addEventListener('click', async () => {
          try {
            const resp = await fetch(routeRemove, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest'
              }
            });
            const data = await resp.json();
            if (data.ok) {
              syncHidden('');
              if ($code) $code.value = '';
              $msg.innerHTML = `<span class="text-muted">Cupón eliminado.</span>`;
              // Al quitar, descuento 0 y total = subtotal mostrado
              $disc.textContent  = '0.00';
              $total.textContent = $sub.textContent;
              btn.remove();
            }
          } catch (e) {
            Swal.fire({ icon:'error', title:'Error', text:'No se pudo quitar el cupón.' });
          }
        });
      }
      if ($remove) attachRemove($remove);
    })();
    </script>
@stop
