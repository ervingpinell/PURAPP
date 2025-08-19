@extends('layouts.app')

@section('title', __('adminlte::adminlte.myCart'))

@section('content')
<div class="container py-5 mb-5">
  <h1 class="mb-4"><i class="fas fa-shopping-cart"></i> {{ __('adminlte::adminlte.myCart') }}</h1>

 @if (session('success') || session('error'))
  @once
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  @endonce
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      @if (session('success'))
        Swal.fire({
          icon: 'success',
          title: @json(__('adminlte::adminlte.success') ?? 'Éxito'),
          text:  @json(session('success')),
          confirmButtonColor: '#198754',
          allowOutsideClick: false
        });
      @endif
      @if (session('error'))
        Swal.fire({
          icon: 'error',
          title: @json(__('adminlte::adminlte.error') ?? 'Error'),
          text:  @json(session('error')),
          confirmButtonColor: '#dc3545',
          allowOutsideClick: false
        });
      @endif
    });
  </script>
@endif



  @if($cart && $cart->items->count())

    {{-- Versión de tabla para pantallas medianas o grandes --}}
    <div class="table-responsive d-none d-md-block mb-4">
      <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr class="text-center">
            <th>{{ __('adminlte::adminlte.tour') }}</th>
            <th>{{ __('adminlte::adminlte.date') }}</th>
            <th>{{ __('adminlte::adminlte.schedule') }}</th>
            <th>{{ __('adminlte::adminlte.language') }}</th>
            <th>{{ __('adminlte::adminlte.adults') }}</th>
            <th>{{ __('adminlte::adminlte.kids') }}</th>
            <th>{{ __('adminlte::adminlte.hotel') }}</th>
            <th>{{ __('adminlte::adminlte.status') }}</th>
            <th>{{ __('adminlte::adminlte.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($cart->items as $item)
            <tr class="text-center">
             <td>{{ $item->tour->getTranslatedName() }}</td>

              <td>{{ \Carbon\Carbon::parse($item->tour_date)->format('d/m/Y') }}</td>
              <td>
                @if($item->schedule)
                  {{ \Carbon\Carbon::parse($item->schedule->start_time)->format('g:i A') }} -
                  {{ \Carbon\Carbon::parse($item->schedule->end_time)->format('g:i A') }}
                @else
                  Sin horario
                @endif
              </td>
              <td>{{ $item->language->name }}</td>
              <td>{{ $item->adults_quantity }}</td>
              <td>{{ $item->kids_quantity }}</td>
              <td>
                @if($item->hotel)
                  {{ $item->hotel->name }}
                @elseif($item->custom_hotel_name)
                  {{ $item->custom_hotel_name }} <small class="text-muted">(personalizado)</small>
                @else
                  <span class="text-muted">No indicado</span>
                @endif
              </td>
              <td>
                <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }}">
                  {{ $item->is_active ? __('adminlte::adminlte.active') : __('adminlte::adminlte.inactive') }}
                </span>
              </td>
              <td>
                <form action="{{ route('public.cart.destroy', $item->item_id) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar este tour del carrito?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i> {{ __('adminlte::adminlte.delete') }}
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Versión móvil (tarjetas) --}}
    <div class="d-md-none">
      @foreach($cart->items as $item)
        <div class="card mb-3 shadow-sm">
          <div class="card-body">
            <h5 class="card-title">{{ $item->tour->name }}</h5>
            <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($item->tour_date)->format('d/m/Y') }}</p>
            <p><strong>Horario:</strong>
              @if($item->schedule)
                {{ \Carbon\Carbon::parse($item->schedule->start_time)->format('g:i A') }} -
                {{ \Carbon\Carbon::parse($item->schedule->end_time)->format('g:i A') }}
              @else
                Sin horario
              @endif
            </p>
            <p><strong>Idioma:</strong> {{ $item->language->name }}</p>
            <p><strong>Adultos:</strong> {{ $item->adults_quantity }}</p>
            <p><strong>Niños:</strong> {{ $item->kids_quantity }}</p>
            <p><strong>Hotel:</strong>
              @if($item->hotel)
                {{ $item->hotel->name }}
              @elseif($item->custom_hotel_name)
                {{ $item->custom_hotel_name }} <small class="text-muted">(personalizado)</small>
              @else
                <span class="text-muted">No indicado</span>
              @endif
            </p>
            <p><strong>Estado:</strong>
              <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }}">
                {{ $item->is_active ? __('adminlte::adminlte.active') : __('adminlte::adminlte.inactive') }}
              </span>
            </p>
            <form action="{{ route('public.cart.destroy', $item->item_id) }}" method="POST"
                  onsubmit="return confirm('¿Eliminar este tour del carrito?');">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm w-100">
                <i class="fas fa-trash"></i> {{ __('adminlte::adminlte.delete') }}
              </button>
            </form>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Total y Código Promocional --}}
    @php
      $total = $cart->items->sum(fn($item) =>
        ($item->tour->adult_price * $item->adults_quantity)
        + ($item->tour->kid_price * $item->kids_quantity)
      );
    @endphp

    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <h4 class="mb-3">
          <strong>{{ __('adminlte::adminlte.totalEstimated') }}:</strong>
          $<span id="cart-total">{{ number_format($total, 2) }}</span>
        </h4>

        <label for="promo-code" class="form-label fw-semibold">{{ __('adminlte::adminlte.promoCode') }}</label>
        <div class="d-flex flex-column flex-sm-row gap-2">
          <input type="text" id="promo-code" name="promo_code" class="form-control"
                placeholder="{{ __('adminlte::adminlte.promoCodePlaceholder') }}">
          <button type="button" id="apply-promo" class="btn btn-outline-primary">{{ __('adminlte::adminlte.apply') }}</button>
        </div>
        <div id="promo-message" class="mt-2 small text-success"></div>
      </div>
    </div>

    {{-- Confirmar Reserva --}}
    <form action="{{ route('public.reservas.storeFromCart') }}" method="POST"
          onsubmit="return confirm('¿Estás seguro de confirmar la reserva?');">
      @csrf
      <input type="hidden" name="promo_code" id="promo_code_hidden" value="">
      <div class="d-grid">
        <button type="submit" class="btn btn-success btn-lg">
          <i class="fas fa-check"></i> {{ __('adminlte::adminlte.confirmBooking') }}
        </button>
      </div>
    </form>

  @else
    <div class="alert alert-info">
      <i class="fas fa-info-circle"></i> {{ __('adminlte::adminlte.emptyCart') }}
    </div>
  @endif
</div>
@endsection

@push('scripts')
  @vite('resources/js/cart/promo-code.js')
@endpush
