{{-- resources/views/admin/Cart/all.blade.php --}}
@extends('adminlte::page')

@section('title', __('carts.title_all'))

@section('content_header')
    <h1><i class="fas fa-shopping-cart"></i> {{ __('carts.title_all') }}</h1>
@stop

@section('content')
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <strong><i class="fas fa-filter"></i> {{ __('carts.filters.title') }}</strong>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">{{ __('carts.filters.email') }}</label>
                <input type="text" name="email" class="form-control" placeholder="customer@email.com" value="{{ request('email') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('carts.filters.status') }}</label>
                <select name="status" class="form-control">
                    <option value="">{{ __('carts.filters.all') }}</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('carts.filters.active') }}</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('carts.filters.inactive') }}</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> {{ __('carts.filters.search') }}
                </button>
                <a href="{{ route('admin.carts.all') }}" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> {{ __('carts.filters.clear') }}
                </a>
            </div>
        </form>
    </div>
</div>

@if($carts->count())
<div class="table-responsive">
    <table class="table table-bordered table-hover shadow-sm">
        <thead class="table-dark">
            <tr class="text-center align-middle">
                <th>{{ __('carts.table.customer') }}</th>
                <th>{{ __('carts.table.email') }}</th>
                <th>{{ __('carts.table.phone') }}</th>
                <th>{{ __('carts.table.items') }}</th>
                <th>{{ __('carts.table.cart_total') }}</th>
                <th>{{ __('carts.table.status') }}</th>
                <th>{{ __('carts.table.last_update') }}</th>
                <th style="width: 240px;">{{ __('carts.table.actions') }}</th>
            </tr>
        </thead>
        <tbody>
        @foreach($carts as $cart)
            <tr class="text-center align-middle">
                <td><strong>{{ $cart->user->full_name }}</strong></td>
                <td>{{ $cart->user->email }}</td>
                <td>{{ $cart->user->full_phone ?? 'N/A' }}</td>
                <td>
                    <button class="btn btn-sm btn-info"
                        data-bs-toggle="modal"
                        data-bs-target="#modalItemsCart{{ $cart->cart_id }}"
                        title="{{ __('carts.actions.view_items') }}">
                        <i class="fas fa-list"></i> ({{ $cart->items_count }})
                    </button>
                </td>
                <td><strong>${{ number_format($cart->total_usd, 2) }}</strong></td>
                <td>
                    <span class="badge {{ $cart->is_active ? 'bg-success' : 'bg-danger' }}">
                        <i class="fas {{ $cart->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                        {{ $cart->is_active ? __('carts.status.active') : __('carts.status.inactive') }}
                    </span>
                </td>
                <td>{{ $cart->updated_at->format(__('carts.format.datetime')) }}</td>

                <td class="text-center">
                    <button class="btn btn-info btn-sm me-1"
                        data-bs-toggle="modal"
                        data-bs-target="#modalItemsCart{{ $cart->cart_id }}"
                        title="{{ __('carts.actions.view_items') }}">
                        <i class="fas fa-eye"></i>
                    </button>

                    <form action="{{ route('admin.carts.toggle', $cart->cart_id) }}" method="POST" class="d-inline-block me-1">
                        @csrf @method('PATCH')
                        <button class="btn btn-sm {{ $cart->is_active ? 'btn-toggle' : 'btn-secondary' }}"
                                title="{{ $cart->is_active ? __('carts.actions.toggle_deactivate') : __('carts.actions.toggle_activate') }}">
                            <i class="fas {{ $cart->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                        </button>
                    </form>

                    <form action="{{ route('admin.carts.destroy', $cart->cart_id) }}" method="POST" class="d-inline-block form-delete-cart">
                        @csrf @method('DELETE')
                        <button class="btn btn-delete btn-sm" title="{{ __('carts.actions.delete_cart') }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@foreach($carts as $cart)
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
            {{ __('carts.items_modal.title', ['name' => $cart->user->full_name]) }}
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('adminlte::adminlte.close') }}"></button>
      </div>

      <div class="modal-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-striped mb-0">
                <thead class="table-dark">
                    <tr class="text-center">
                        <th>{{ __('carts.items_modal.headers.tour') }}</th>
                        <th>{{ __('carts.items_modal.headers.date') }}</th>
                        <th>{{ __('carts.items_modal.headers.schedule') }}</th>
                        <th>{{ __('carts.items_modal.headers.language') }}</th>
                        <th>{{ __('carts.items_modal.headers.adults') }}</th>
                        <th>{{ __('carts.items_modal.headers.kids') }}</th>
                        <th>{{ __('carts.items_modal.headers.item_total') }}</th>
                        <th>{{ __('carts.items_modal.headers.status') }}</th>
                        <th style="width: 120px;">{{ __('carts.items_modal.headers.actions') }}</th>
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
                                <span class="text-muted">{{ __('carts.items_modal.no_schedule') }}</span>
                            @endif
                        </td>
                        <td>{{ $item->language->name ?? '—' }}</td>
                        <td>{{ $aq }}</td>
                        <td>{{ $kq }}</td>
                        <td><strong>${{ number_format($itemTotal, 2) }}</strong></td>
                        <td>
                            <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-danger' }}">
                                <i class="fas {{ $item->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                {{ $item->is_active ? __('carts.status.active') : __('carts.status.inactive') }}
                            </span>
                        </td>
                        <td class="d-flex justify-content-center gap-1">
                            <button
                                class="btn btn-sm btn-edit btn-open-edit"
                                title="{{ __('carts.actions.edit') }}"
                                data-parent="#modalItemsCart{{ $cart->cart_id }}"
                                data-child="#modalEditar{{ $item->item_id }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            <form action="{{ route('admin.carts.item.destroy', $item->item_id) }}" method="POST" class="d-inline-block form-delete-item">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-delete" title="{{ __('carts.actions.delete') }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">{{ __('carts.items_modal.no_items') }}</td></tr>
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

      <div class="modal-footer d-flex justify-content-between flex-wrap gap-2">
        <div class="text-muted">
            {{ __('carts.items_modal.customer') }}: <strong>{{ $cart->user->full_name }}</strong> ·
            {{ __('carts.items_modal.email') }}: <strong>{{ $cart->user->email }}</strong> ·
            {{ __('carts.items_modal.phone') }}: <strong>{{ $cart->user->full_phone ?? 'N/A' }}</strong> ·
            {{ __('carts.items_modal.last_update') }}: <strong>{{ $cart->updated_at->format(__('carts.format.datetime')) }}</strong>
        </div>
        <div class="fs-5">
            {{ __('carts.items_modal.cart_total') }}: <strong>${{ number_format($cartTotalFooter, 2) }}</strong>
        </div>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('carts.items_modal.close') }}</button>
      </div>

    </div>
  </div>
</div>
@endforeach

@else
<div class="alert alert-info text-center">
    <i class="fas fa-info-circle"></i> {{ __('carts.empty.no_records') }}
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
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.form-delete-item').forEach(form => {
          form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
              title: @json(__('carts.swal.delete_item.title')),
              text: @json(__('carts.swal.delete_item.text')),
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#3085d6',
              confirmButtonText: @json(__('carts.swal.delete_item.confirm')),
              cancelButtonText: @json(__('carts.swal.delete_item.cancel'))
            }).then((r) => { if (r.isConfirmed) form.submit(); });
          });
        });

        document.querySelectorAll('.form-delete-cart').forEach(form => {
          form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
              title: @json(__('carts.swal.delete_cart.title')),
              text: @json(__('carts.swal.delete_cart.text')),
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#3085d6',
              confirmButtonText: @json(__('carts.swal.delete_cart.confirm')),
              cancelButtonText: @json(__('carts.swal.delete_cart.cancel'))
            }).then((r) => { if (r.isConfirmed) form.submit(); });
          });
        });
    });
    </script>
@stop
