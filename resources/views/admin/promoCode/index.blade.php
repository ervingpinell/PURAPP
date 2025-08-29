@extends('adminlte::page')

@section('title', __('m_config.promocode.title'))

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
@if(session('success'))
Swal.fire({
  icon:'success',
  title:'Éxito',
  text:'{{ session('success') }}',
  confirmButtonColor:'#3085d6',
  timer:3000,
  timerProgressBar:true
});
@endif
@if(session('error'))
Swal.fire({
  icon:'error',
  title:'Error',
  text:'{{ session('error') }}',
  confirmButtonColor:'#d33'
});
@endif
</script>
@endpush

@section('content')
<div class="container-fluid">

  {{-- 🧾 FORMULARIO PARA CREAR CÓDIGO --}}
  <h2 class="mb-4">Generar nuevo código promocional</h2>

  <form method="POST" action="{{ route('admin.promoCode.store') }}">
    @csrf

    <div class="row g-3 mb-2">
      <div class="col-md-4">
        <label for="code" class="form-label">Código</label>
        <input type="text" name="code" id="code" class="form-control" value="{{ old('code') }}" required>
        @error('code') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="col-md-3">
        <label for="discount" class="form-label">Descuento</label>
        <input type="number" step="0.01" name="discount" id="discount" class="form-control" value="{{ old('discount') }}" required>
        @error('discount') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="col-md-3">
        <label for="type" class="form-label">Tipo</label>
        <select name="type" id="type" class="form-control" required>
          <option value="percent" @selected(old('type')==='percent')>% (porcentaje)</option>
          <option value="amount"  @selected(old('type')==='amount')>$ (monto fijo)</option>
        </select>
        @error('type') <small class="text-danger">{{ $message }}</small> @enderror
      </div>
    </div>

    <div class="row g-3 mb-2">
      <div class="col-md-3">
        <label for="valid_from" class="form-label">Válido desde</label>
        <input type="date" name="valid_from" id="valid_from" class="form-control" value="{{ old('valid_from') }}">
        @error('valid_from') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="col-md-3">
        <label for="valid_until" class="form-label">Válido hasta</label>
        <input type="date" name="valid_until" id="valid_until" class="form-control" value="{{ old('valid_until') }}">
        @error('valid_until') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="col-md-3">
        <label for="usage_limit" class="form-label">Límite de usos</label>
        <input
          type="number"
          min="1"
          name="usage_limit"
          id="usage_limit"
          class="form-control"
          value="{{ old('usage_limit') }}"
          placeholder="Vacío = ilimitado">
        @error('usage_limit') <small class="text-danger">{{ $message }}</small> @enderror
        <small class="text-muted">Déjalo vacío para usos ilimitados. Pon 1 para un solo uso.</small>
      </div>

      <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-success w-100">Generar</button>
      </div>
    </div>
  </form>

  {{-- 📋 LISTADO DE CÓDIGOS --}}
  <h3 class="mt-5">Códigos promocionales existentes</h3>

  <table class="table table-dark table-bordered align-middle">
    <thead>
      <tr>
        <th style="width: 14rem;">Código</th>
        <th style="width: 12rem;">Descuento</th>
        <th style="width: 16rem;">Vigencia</th>
        <th style="width: 10rem;">Estado (fecha)</th>
        <th style="width: 14rem;">Usos</th>
        <th style="width: 10rem;">Estado (uso)</th>
        <th style="width: 10rem;">Acciones</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($promoCodes as $promo)
        @php
          $tz    = config('app.timezone', 'America/Costa_Rica');
          $today = \Carbon\Carbon::today($tz);

          $vf = $promo->valid_from ? \Carbon\Carbon::parse($promo->valid_from, $tz) : null;
          $vu = $promo->valid_until ? \Carbon\Carbon::parse($promo->valid_until, $tz) : null;

          // Estado por fechas
          $dateStatus = 'Vigente';
          $dateClass  = 'bg-success';
          if ($vf && $today->lt($vf)) { $dateStatus = 'Programado'; $dateClass = 'bg-info'; }
          if ($vu && $today->gt($vu)) { $dateStatus = 'Expirado';   $dateClass = 'bg-secondary'; }

          // Estado por uso (nuevo esquema: usage_limit / usage_count)
          // Si usage_limit es null => ilimitado; usamos is_used solo como compat.
          $isExhausted = false;
          if (!is_null($promo->usage_limit)) {
              $isExhausted = (int)$promo->usage_count >= (int)$promo->usage_limit;
          } elseif ($promo->is_used) { // compat legada
              $isExhausted = true;
          }

          $usageLabel = is_null($promo->usage_limit)
                        ? ($promo->usage_count . ' / ∞')
                        : ($promo->usage_count . ' / ' . $promo->usage_limit);

          $usageClass  = $isExhausted ? 'bg-danger' : 'bg-success';
          $usageStatus = $isExhausted ? 'Agotado' : 'Disponible';

        @endphp

        <tr>
          <td class="fw-bold">{{ $promo->code }}</td>

          <td>
            @if (!is_null($promo->discount_percent))
              {{ number_format($promo->discount_percent, 2) }}%
            @elseif (!is_null($promo->discount_amount))
              ${{ number_format($promo->discount_amount, 2) }}
            @else
              —
            @endif
          </td>

          <td>
            @if($promo->valid_from || $promo->valid_until)
              {{ $promo->valid_from ? \Carbon\Carbon::parse($promo->valid_from)->format('Y-m-d') : '—' }}
              —
              {{ $promo->valid_until ? \Carbon\Carbon::parse($promo->valid_until)->format('Y-m-d') : '—' }}
            @else
              — (sin límite)
            @endif
          </td>

          <td>
            <span class="badge {{ $dateClass }}">{{ $dateStatus }}</span>
          </td>

          <td>
            <div class="d-flex align-items-center gap-2">
              <span>{{ $usageLabel }}</span>
              @if(property_exists($promo, 'remaining_uses'))
                <span class="badge bg-dark">
                  restantes:
                  {{ is_null($promo->remaining_uses) ? '∞' : $promo->remaining_uses }}
                </span>
              @endif
            </div>
          </td>

          <td>
            <span class="badge {{ $usageClass }}">{{ $usageStatus }}</span>
          </td>

          <td>
            <form action="{{ route('admin.promoCode.destroy', $promo) }}"
                  method="POST"
                  onsubmit="return confirm('¿Eliminar este código?')">
              @csrf
              @method('DELETE')
              <button class="btn btn-outline-danger btn-sm">Eliminar</button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="text-center">No hay códigos promocionales.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
