{{-- resources/views/admin/Cart/cart.blade.php --}}
@extends('adminlte::page')

@section('title', __('carts.title_my'))

@section('content_header')
<h1><i class="fas fa-shopping-cart"></i> {{ __('carts.title_my') }}</h1>
@stop

@section('content')
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

<div class="table-responsive">
    <table class="table table-bordered table-hover shadow-sm">
        <thead class="table-dark text-center">
            <tr>
                <th>{{ __('carts.items_modal.headers.product') }}</th>
                <th>{{ __('carts.items_modal.headers.date') }}</th>
                <th>{{ __('carts.items_modal.headers.language') }}</th>
                <th>{{ __('adminlte::adminlte.hotel') }}</th>
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
                <td>{{ $item->product->getTranslatedName(app()->getLocale()) ?? $item->product->name }}</td>
                <td>{{ \Carbon\Carbon::parse($item->product_date)->format(__('carts.format.date')) }}</td>
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
                                    ($item->product->adult_price * $item->adults_quantity) +
                                    ($item->product->kid_price   * $item->kids_quantity),
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
                    <button class="btn btn-sm btn-edit"
                        data-toggle="modal"
                        data-target="#modalEditar{{ $item->item_id }}">
                        <i class="fas fa-edit"></i>
                    </button>
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
$__subtotal = $cart->items->sum(fn($i) =>
($i->product->adult_price * $i->adults_quantity) + ($i->product->kid_price * $i->kids_quantity)
);

$hasPromo = !empty($adminPromo ?? []);
$op = $hasPromo ? ($adminPromo['operation'] ?? 'subtract') : null;
$adjustment = $hasPromo ? (float)($adminPromo['adjustment'] ?? 0) : 0.0;

$__adminSubtotal = $__subtotal;
$__adminDiscount = $adjustment;
$__adminTotal = $hasPromo
? max(0, $op === 'add'
? ($__adminSubtotal + $__adminDiscount)
: ($__adminSubtotal - $__adminDiscount))
: $__adminSubtotal;
@endphp

<div class="card mt-4 shadow">
    <div class="card-header bg-secondary text-white">
        <i class="fas fa-tags"></i> {{ __('carts.promo.title') }}
    </div>

    <div class="card-body">
        @php
        $currentCode = $adminPromo['code'] ?? '';
        $hasPromo = !empty($currentCode);
        @endphp

        <div class="d-flex flex-wrap align-items-center gap-2">
            <form method="POST" action="{{ route('admin.carts.applyPromo') }}" class="d-flex gap-2" id="promo-form">
                @csrf
                <input type="text"
                    name="code"
                    id="promo-code"
                    class="form-control w-auto"
                    placeholder="{{ __('carts.promo.placeholder') }}"
                    value="{{ $currentCode }}"
                    autocomplete="off"
                    data-current-code="{{ $currentCode }}">
                <button type="submit"
                    id="promo-toggle-btn"
                    class="btn {{ $hasPromo ? 'btn-danger' : 'btn-info' }}">
                    <i class="fas {{ $hasPromo ? 'fa-times' : 'fa-check' }}"></i>
                    <span class="btn-text">
                        {{ $hasPromo ? (__('carts.promo.remove') ?: 'Remove') : (__('carts.promo.apply')  ?: 'Apply') }}
                    </span>
                </button>
            </form>
        </div>

        <div id="promo-message" class="small mt-2">
            @if($hasPromo)
            <span class="text-success">
                {{ __('carts.promo.applied', [
                              'code'  => $currentCode,
                              'label' => trim(
                                  ($adminPromo['amount']  ? '$'.number_format($adminPromo['amount'],2) : '')
                                . ((($adminPromo['amount'] ?? null) && ($adminPromo['percent'] ?? null)) ? ' + ' : '')
                                . ($adminPromo['percent'] ? $adminPromo['percent'].'%' : '')
                              )
                          ]) }}
                @if(($adminPromo['operation'] ?? '') === 'add') ({{ __('carts.totals.surcharge') }}) @endif
            </span>
            @else
            <span class="text-muted">
                {{ __('carts.promo.hint_toggle') ?? 'Tip: deja el campo vacío o reenvía el mismo código para quitarlo.' }}
            </span>
            @endif
        </div>

        <div class="mt-3">
            <div><strong>{{ __('carts.totals.subtotal') }}:</strong> ${{ number_format($__adminSubtotal, 2) }}</div>
            <div>
                <strong>
                    {{ ($op === 'add') ? __('carts.totals.surcharge') : __('carts.totals.discount') }}
                </strong>:
                ${{ number_format($__adminDiscount, 2) }}
            </div>
            <div class="fs-5">
                <strong>{{ __('carts.totals.estimated_total') }}:</strong> ${{ number_format($__adminTotal, 2) }}
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.bookings.storeFromCart') }}" class="mt-3">
    @csrf
    <input type="hidden" name="promo_code" id="promo_code_hidden_form" value="{{ $adminPromo['code'] ?? '' }}">
    <button type="submit" class="btn btn-success btn-lg">
        <i class="fas fa-paper-plane"></i> {{ __('carts.confirm_send.button') }}
    </button>
</form>

@foreach($cart->items as $item)
<div class="modal fade" id="modalEditar{{ $item->item_id }}" tabindex="-1" aria-labelledby="modalLabel{{ $item->item_id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.carts.update', $item->item_id) }}" class="modal-content edit-item-form">
            @csrf
            @method('PATCH')
            <input type="hidden" name="is_active" value="0">

            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="modalLabel{{ $item->item_id }}">{{ __('carts.actions.edit') }}</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label>{{ __('carts.items_modal.headers.date') }}</label>
                    <input type="date" name="product_date" class="form-control" value="{{ $item->product_date }}" required>
                </div>
                <div class="mb-3">
                    <label>{{ __('adminlte::adminlte.hotel') }}</label>
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
                            {{ __('carts.placeholders.other_hotel_option') }}
                        </option>
                    </select>
                </div>
                <div class="mb-3 {{ $item->is_other_hotel ? '' : 'd-none' }}"
                    id="edit_other_container_{{ $item->item_id }}">
                    <label>{{ __('carts.fields.hotel_name') }}</label>
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
                        @foreach($item->product->schedules as $sched)
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('carts.actions.cancel') }}</button>
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
    (function runNowOrOnReady(cb) {
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', cb, {
            once: true
        });
        else cb();
    })(function() {
        @if($errors-> any())
        Swal.fire({
            icon: 'error',
            title: @json(__('carts.swal.validation_errors.title')),
            html: `{!! implode('<br>', $errors->all()) !!}`,
            confirmButtonText: 'OK'
        });
        @endif
        @if(session('exception'))
        Swal.fire({
            icon: 'error',
            title: @json(__('carts.swal.exception.title')),
            html: `<pre style="text-align:left;white-space:pre-wrap">{{ addslashes(session('exception')) }}</pre>`,
            width: 900
        });
        @endif

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

        @if($cart && $cart->items->count())
        @foreach($cart->items as $item)
            (function() {
                const sel = document.getElementById('edit_hotel_{{ $item->item_id }}');
                const cont = document.getElementById('edit_other_container_{{ $item->item_id }}');
                const hid = document.getElementById('edit_is_other_{{ $item->item_id }}');

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
        @endif

            (function() {
                const input = document.getElementById('promo-code');
                const btn = document.getElementById('promo-toggle-btn');
                if (!input || !btn) return;

                const current = (input.getAttribute('data-current-code') || '').trim();
                const iconEl = btn.querySelector('i');
                const textEl = btn.querySelector('.btn-text');

                const setApply = () => {
                    btn.classList.remove('btn-danger');
                    btn.classList.add('btn-info');
                    if (iconEl) iconEl.className = 'fas fa-check';
                    if (textEl) textEl.textContent = {
                        {
                            json_encode(__('carts.promo.apply') ? : 'Apply')
                        }
                    };
                };
                const setRemove = () => {
                    btn.classList.remove('btn-info');
                    btn.classList.add('btn-danger');
                    if (iconEl) iconEl.className = 'fas fa-times';
                    if (textEl) textEl.textContent = {
                        {
                            json_encode(__('carts.promo.remove') ? : 'Remove')
                        }
                    };
                };
                const refreshState = () => {
                    const val = (input.value || '').trim();
                    if (val === '' || val.toUpperCase() === current.toUpperCase()) setRemove();
                    else setApply();
                };

                refreshState();
                input.addEventListener('input', refreshState);
            })();
    });
</script>
@stop
