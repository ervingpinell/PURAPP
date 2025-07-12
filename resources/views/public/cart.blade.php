@extends('layouts.app')

@section('title', 'Mi Carrito')

@section('content')
<div class="container py-5 mb-5">
  <h1 class="mb-4"><i class="fas fa-shopping-cart"></i> Mi Carrito</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  @if($cart && $cart->items->count())
    <div class="table-responsive mb-4">
      <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr class="text-center">
            <th>Tour</th>
            <th>Fecha</th>
            <th>Horario</th>
            <th>Idioma</th>
            <th>Adultos</th>
            <th>Niños</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach($cart->items as $item)
            <tr class="text-center">
              <td>{{ $item->tour->name }}</td>
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
                <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }}">
                  {{ $item->is_active ? 'Activo' : 'Inactivo' }}
                </span>
              </td>
              <td>
                <form action="{{ route('public.cart.destroy', $item->item_id) }}"
                      method="POST"
                      onsubmit="return confirm('¿Eliminar este tour del carrito?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i> Eliminar
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @php
      $total = $cart->items->sum(fn($item) =>
        ($item->tour->adult_price * $item->adults_quantity)
        + ($item->tour->kid_price * $item->kids_quantity)
      );
    @endphp

    <h4 class="mb-4">
      <strong>Total Estimado:</strong> ${{ number_format($total, 2) }}
    </h4>

    <form action="{{ route('public.reservas.storeFromCart') }}"
          method="POST"
          onsubmit="return confirm('¿Estás seguro de confirmar la reserva?');">
      @csrf
      <button type="submit" class="btn btn-success btn-lg">
        <i class="fas fa-check"></i> Confirmar Reserva
      </button>
    </form>
  @else
    <div class="alert alert-info">
      <i class="fas fa-info-circle"></i> No tienes tours en tu carrito.
    </div>
  @endif
</div>
@endsection
