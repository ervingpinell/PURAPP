@extends('adminlte::page')

@section('title', __('carts.title_my'))

@section('content_header')
    <h1><i class="fas fa-shopping-cart"></i> {{ __('carts.title_my') }}</h1>
@stop

{{-- SweetAlert for validation errors and exceptions --}}
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'error',
                title: @json(__('carts.swal.validation_errors.title')),
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonText: 'OK'
            });
        });
    </script>
@endif

@if (session('exception'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'error',
                title: @json(__('carts.swal.exception.title')),
                html: `<pre style="text-align:left;white-space:pre-wrap">{{ addslashes(session('exception')) }}</pre>`,
                width: 900
            });
        });
    </script>
@endif

@section('content')
    {{-- Filters --}}
    <form method="GET" class="mb-4 d-flex justify-content-center align-items-center gap-2">
        <label class="mb-0"><i class="fas fa-filter"></i> {{ __('carts.filters.status') }}:</label>
        <select name="status" class="form-control w-auto">
            <option value="">{{ __('carts.filters.all') }}</option>
            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('carts.filters.active') }}</option>
            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('carts.filters.inactive') }}</option>
        </select>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> {{ __('carts.actions.filter') }}
        </button>
    </form>

    @if($cart && $cart->items->count())
        {{-- Customer info --}}
        <div class="card mb-4 shadow">
            <div class="card-header bg-info text-white">
                <i class="fas fa-user"></i> {{ __('carts.my.customer_info.title') }}
            </div>
            <div class="card-body">
                <p><i class="fas fa-id-card"></i> <strong>{{ __('carts.my.customer_info.name') }}:</strong> {{ $cart->user->full_name ?? 'N/A' }}</p>
                <p><i class="fas fa-envelope"></i> <strong>{{ __('carts.my.customer_info.email') }}:</strong> {{ $cart->user->email ?? 'N/A' }}</p>
                <p><i class="fas fa-phone"></i> <strong>{{ __('carts.my.customer_info.phone') }}:</strong> {{ $cart->user->full_phone ?? 'N/A' }}</p>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-bordered table-hover shadow-sm">
                <thead class="table-dark text-center">
                    <tr>
                        <th>{{ __('carts.items_modal.headers.tour') }}</th>
                        <th>{{ __('carts.items_modal.headers.date') }}</th>
                        <th>{{ __('carts.items_modal.headers.language') }}</th>
                        <th>Hotel</th>
                        <th>{{ __('carts.items_modal.headers.schedule') }}</th>
                        <th>{{ __('carts.items_modal.headers.adults') }}</th>
                        <th>{{ __('carts.items_modal.headers.kids') }}</th>
                        <th>{{ __('carts.items_modal.headers.item_total') }}</th>
                        <th>{{ __('carts.items_modal.headers.status') }}</th>
                        <th>{{ __('carts.items_modal.headers.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="text-center align-middle">
                    @foreach($cart->items as $item)
                        <tr>
                            <td>{{ $item->tour->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tour_date)->format(__('carts.format.date')) }}</td>
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
                                    <span class="text-muted">{{ __('carts.items_modal.no_schedule') }}</span>
                                @endif
                            </td>
                            <td>{{ $item->adults_quantity }}</td>
                            <td>{{ $item->kids_quantity }}</td>
                            <td>
                                ${{ number_format(
                                    ($item->tour->adult_price * $item->adults_quantity) +
                                    ($item->tour->kid_price   * $item->kids_quantity),
                                    2
                                ) }}
                            </td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge bg-success"><i class="fas fa-check-circle"></i> {{ __('carts.status.active') }}</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fas fa-times-circle"></i> {{ __('carts.status.inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                {{-- Edit --}}
                                <button class="btn btn-sm btn-edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditar{{ $item->item_id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                {{-- Delete --}}
                                <form method="POST"
                                      action="{{ route('admin.carts.item.destroy', $item->item_id) }}"
                                      class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-delete" title="{{ __('carts.actions.delete') }}">
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
            // Totals fallback
            $__subtotal = $cart->items->sum(fn($i) =>
                ($i->tour->adult_price * $i->adults_quantity) + ($i->tour->kid_price * $i->kids_quantity)
            );
            $__adminSubtotal = isset($adminSubtotal) ? $adminSubtotal : $__subtotal;
            $__adminDiscount = isset($adminDiscount) ? $adminDiscount : 0;
            $__adminTotal    = isset($adminTotal)    ? $adminTotal    : max($__adminSubtotal - $__adminDiscount, 0);
        @endphp

        {{-- Promo Code (ADMIN) --}}
        <div class="card mt-4 shadow">
            <div class="card-header bg-secondary text-white">
                <i class="fas fa-tags"></i> {{ __('carts.promo.title') }}
            </div>

            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <input type="text" id="promo-code" class="form-control w-auto" placeholder="{{ __('carts.promo.placeholder') }}"
                        value="{{ $adminPromo['code'] ?? '' }}" />
                    <button type="button" id="apply-promo" class="btn btn-primary">{{ __('carts.promo.apply') }}</button>
                    @if(!empty($adminPromo))
                        <button type="button" id="remove-promo" class="btn btn-outline-danger">{{ __('carts.promo.remove') }}</button>
                    @endif

                    {{-- Hidden for UI (synced with form below) --}}
                    <input type="hidden" id="promo_code_hidden_ui" value="{{ $adminPromo['code'] ?? '' }}">
                </div>

                {{-- Message (single, no duplicates) --}}
                <div id="promo-message" class="small mt-2">
                    @if(!empty($adminPromo))
                        <span class="text-success">
                            {{ __('carts.promo.applied', [
                                'code'  => $adminPromo['code'],
                                'label' => trim(($adminPromo['amount'] ? '$'.number_format($adminPromo['amount'],2) : '')
                                    . ($adminPromo['amount'] && $adminPromo['percent'] ? ' + ' : '')
                                    . ($adminPromo['percent'] ? $adminPromo['percent'].'%' : '')
                                )
                            ]) }}
                        </span>
                    @endif
                </div>

                {{-- Dynamic totals --}}
                <div class="mt-3">
                    <div><strong>{{ __('carts.totals.subtotal') }}:</strong> $<span id="cart-subtotal">{{ number_format($__adminSubtotal, 2) }}</span></div>
                    <div><strong>{{ __('carts.totals.discount') }}:</strong> $<span id="cart-discount">{{ number_format($__adminDiscount, 2) }}</span></div>
                    <div class="fs-5">
                        <strong>{{ __('carts.totals.estimated_total') }}:</strong> $<span id="cart-total">{{ number_format($__adminTotal, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Confirm & Send --}}
        <form method="POST" action="{{ route('admin.bookings.storeFromCart') }}" class="mt-3">
            @csrf
            <input type="hidden" name="promo_code" id="promo_code_hidden_form" value="{{ $adminPromo['code'] ?? '' }}">
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-paper-plane"></i> {{ __('carts.confirm_send.button') }}
            </button>
        </form>

        {{-- Edit modals --}}
        @foreach($cart->items as $item)
            <div class="modal fade" id="modalEditar{{ $item->item_id }}" tabindex="-1" aria-labelledby="modalLabel{{ $item->item_id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('admin.carts.update', $item->item_id) }}" class="modal-content">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="is_active" value="0">

                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title" id="modalLabel{{ $item->item_id }}">{{ __('carts.actions.edit') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label>{{ __('carts.items_modal.headers.date') }}</label>
                                <input type="date" name="tour_date" class="form-control" value="{{ $item->tour_date }}" required>
                            </div>
                            <div class="mb-3">
                                <label>Hotel</label>
                                <select name="hotel_id"
                                        id="edit_hotel_{{ $item->item_id }}"
                                        class="form-control">
                                    <option value="">{{ __('carts.actions.select') ?? 'Select a hotel' }}</option>
                                    @foreach($hotels as $hotel)
                                        <option value="{{ $hotel->hotel_id }}"
                                            {{ !$item->is_other_hotel && $item->hotel_id == $hotel->hotel_id ? 'selected':'' }}>
                                            {{ $hotel->name }}
                                        </option>
                                    @endforeach
                                    <option value="other" {{ $item->is_other_hotel ? 'selected':'' }}>
                                        Other…
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3 {{ $item->is_other_hotel ? '' : 'd-none' }}"
                                 id="edit_other_container_{{ $item->item_id }}">
                                <label>Hotel name</label>
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
                                <label>{{ __('carts.items_modal.headers.schedule') }}</label>
                                <select name="schedule_id" class="form-control">
                                    <option value="">{{ __('carts.actions.select') ?? 'Select a schedule' }}</option>
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
                                <label>{{ __('carts.items_modal.headers.adults') }}</label>
                                <input type="number" name="adults_quantity" class="form-control" value="{{ $item->adults_quantity }}" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label>{{ __('carts.items_modal.headers.kids') }}</label>
                                <input type="number" name="kids_quantity" class="form-control" value="{{ $item->kids_quantity }}" min="0" max="2">
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="check{{ $item->item_id }}" {{ $item->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="check{{ $item->item_id }}">
                                    {{ __('carts.status.active') }}
                                </label>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ __('carts.actions.save_changes') }}</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('carts.actions.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle"></i> {{ __('carts.empty.cart') }}
        </div>
    @endif
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Confirm delete item
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', e => {
                e.preventDefault();
                Swal.fire({
                    title: @json(__('carts.swal.delete_item.title')),
                    text: @json(__('carts.swal.delete_item.text')),
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: @json(__('carts.swal.delete_item.confirm')),
                    cancelButtonText: @json(__('carts.swal.delete_item.cancel')),
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d'
                }).then(result => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });

        // Session toasts
        @if(session('success'))
            Swal.fire({ icon: 'success', title: '{{ session("success") }}', timer:2000, showConfirmButton:false });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Oops!', text: @js(session('error')), confirmButtonText:'Got it' });
        @endif

        // Hotel "Other…" toggle in edit modals
        document.addEventListener('DOMContentLoaded', () => {
            @foreach($cart->items as $item)
                (function(){
                    const sel    = document.getElementById('edit_hotel_{{ $item->item_id }}'),
                          cont   = document.getElementById('edit_other_container_{{ $item->item_id }}'),
                          hid    = document.getElementById('edit_is_other_{{ $item->item_id }}');

                    sel?.addEventListener('change', () => {
                        if (sel.value === 'other') {
                            cont.classList.remove('d-none');
                            hid.value = 1;
                        } else {
                            cont.classList.add('d-none');
                            const input = cont.querySelector('input');
                            if (input) input.value = '';
                            hid.value = 0;
                        }
                    });
                })();
            @endforeach
        });
    </script>

    {{-- Promo (ADMIN) --}}
    <script>
    (() => {
      const csrf        = '{{ csrf_token() }}';
      const routeApply  = '{{ route("admin.carts.applyPromo") }}';
      const routeRemove = '{{ route("admin.carts.removePromo") }}';

      const $code    = document.getElementById('promo-code');
      const $apply   = document.getElementById('apply-promo');
      const $remove  = document.getElementById('remove-promo');
      const $msg     = document.getElementById('promo-message');

      const $sub     = document.getElementById('cart-subtotal');
      const $disc    = document.getElementById('cart-discount');
      const $total   = document.getElementById('cart-total');

      const $hiddenUI   = document.getElementById('promo_code_hidden_ui');
      const $hiddenForm = document.getElementById('promo_code_hidden_form');

      function syncHidden(value) {
        if ($hiddenUI)   $hiddenUI.value   = value || '';
        if ($hiddenForm) $hiddenForm.value = value || '';
      }

      $apply?.addEventListener('click', async () => {
        const code = ($code?.value || '').trim();
        if (!code) return Swal.fire({ icon:'info', title: @json(__('carts.promo.enter_code')) });

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
            $msg.innerHTML = `<span class="text-danger">${data.message ?? @json(__('carts.promo.apply_error'))}</span>`;
            return;
          }

          syncHidden(data.code);
          $msg.innerHTML     = `<span class="text-success">${@json(__('carts.promo.applied', ['code' => '::code', 'label' => '::label']))}`
                                .replace('::code', data.code)
                                .replace('::label', data.label)
                                + `</span>`;
          $sub.textContent   = data.subtotal;
          $disc.textContent  = data.adjustment ?? data.discount ?? '0.00';
          $total.textContent = data.new_total;

          if (!$remove) {
            const btn = document.createElement('button');
            btn.id = 'remove-promo';
            btn.type = 'button';
            btn.className = 'btn btn-outline-danger ms-2';
            btn.textContent = @json(__('carts.promo.remove'));
            $apply.parentElement.appendChild(btn);
            attachRemove(btn);
          }
        } catch (e) {
          Swal.fire({ icon:'error', title:'Error', text:@json(__('carts.promo.apply_error')) });
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
              $msg.innerHTML = `<span class="text-muted">${@json(__('carts.promo.removed'))}</span>`;
              $disc.textContent  = '0.00';
              $total.textContent = $sub.textContent;
              btn.remove();
            }
          } catch (e) {
            Swal.fire({ icon:'error', title:'Error', text:@json(__('carts.promo.remove_error')) });
          }
        });
      }
      if ($remove) attachRemove($remove);
    })();
    </script>
@stop
