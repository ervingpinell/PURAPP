@extends('adminlte::page')

@section('title', __('m_config.promocode.title'))

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
@if(session('success'))
Swal.fire({
  icon:'success',
  title:@json(__('m_config.promocode.success_title')),
  text:@json(__(session('success'))),
  confirmButtonColor:'#3085d6',
  timer:3000,
  timerProgressBar:true
});
@endif
@if(session('error'))
Swal.fire({
  icon:'error',
  title:@json(__('m_config.promocode.error_title')),
  text:@json(__(session('error'))),
  confirmButtonColor:'#d33'
});
@endif
</script>
@endpush

@section('content')
<div class="container-fluid">

  {{-- 🧾 FORMULARIO PARA CREAR CÓDIGO --}}
  <h2 class="mb-4">{{ __('m_config.promocode.create_title') }}</h2>

  <form method="POST" action="{{ route('admin.promoCode.store') }}">
    @csrf

    <div class="row g-3 mb-2">
      <div class="col-md-4">
        <label for="code" class="form-label">{{ __('m_config.promocode.fields.code') }}</label>
        <input type="text" name="code" id="code" class="form-control" value="{{ old('code') }}" required>
        @error('code') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="col-md-3">
        <label for="discount" class="form-label">{{ __('m_config.promocode.fields.discount') }}</label>
        <input type="number" step="0.01" name="discount" id="discount" class="form-control" value="{{ old('discount') }}" required>
        @error('discount') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="col-md-3">
        <label for="type" class="form-label">{{ __('m_config.promocode.fields.type') }}</label>
        <select name="type" id="type" class="form-control" required>
          <option value="percent" @selected(old('type')==='percent')>{{ __('m_config.promocode.types.percent') }}</option>
          <option value="amount"  @selected(old('type')==='amount')>{{ __('m_config.promocode.types.amount') }}</option>
        </select>
        @error('type') <small class="text-danger">{{ $message }}</small> @enderror
      </div>
    </div>

    <div class="row g-3 mb-2">
      <div class="col-md-3">
        <label for="valid_from" class="form-label">{{ __('m_config.promocode.fields.valid_from') }}</label>
        <input type="date" name="valid_from" id="valid_from" class="form-control" value="{{ old('valid_from') }}">
        @error('valid_from') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="col-md-3">
        <label for="valid_until" class="form-label">{{ __('m_config.promocode.fields.valid_until') }}</label>
        <input type="date" name="valid_until" id="valid_until" class="form-control" value="{{ old('valid_until') }}">
        @error('valid_until') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="col-md-3">
        <label for="usage_limit" class="form-label">{{ __('m_config.promocode.fields.usage_limit') }}</label>
        <input
          type="number"
          min="1"
          name="usage_limit"
          id="usage_limit"
          class="form-control"
          value="{{ old('usage_limit') }}"
          placeholder="{{ __('m_config.promocode.labels.unlimited_placeholder') }}">
        @error('usage_limit') <small class="text-danger">{{ $message }}</small> @enderror
        <small class="text-muted">{{ __('m_config.promocode.labels.unlimited_hint') }}</small>
      </div>

      <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-success w-100">
          {{ __('m_config.promocode.actions.generate') }}
        </button>
      </div>
    </div>
  </form>

  {{-- 📋 LISTADO DE CÓDIGOS --}}
  <h3 class="mt-5">{{ __('m_config.promocode.list_title') }}</h3>

  <table class="table table-dark table-bordered align-middle">
    <thead>
      <tr>
        <th style="width: 14rem;">{{ __('m_config.promocode.table.code') }}</th>
        <th style="width: 12rem;">{{ __('m_config.promocode.table.discount') }}</th>
        <th style="width: 16rem;">{{ __('m_config.promocode.table.validity') }}</th>
        <th style="width: 10rem;">{{ __('m_config.promocode.table.date_status') }}</th>
        <th style="width: 14rem;">{{ __('m_config.promocode.table.usage') }}</th>
        <th style="width: 10rem;">{{ __('m_config.promocode.table.usage_status') }}</th>
        <th style="width: 10rem;">{{ __('m_config.promocode.table.actions') }}</th>
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
          $dateStatus = __('m_config.promocode.date_status.active');
          $dateClass  = 'bg-success';
          if ($vf && $today->lt($vf)) { $dateStatus = __('m_config.promocode.date_status.scheduled'); $dateClass = 'bg-info'; }
          if ($vu && $today->gt($vu)) { $dateStatus = __('m_config.promocode.date_status.expired');   $dateClass = 'bg-secondary'; }

          // Estado por uso
          $isExhausted = false;
          if (!is_null($promo->usage_limit)) {
              $isExhausted = (int)$promo->usage_count >= (int)$promo->usage_limit;
          } elseif ($promo->is_used) { // compat legado
              $isExhausted = true;
          }

          $usageLabel = is_null($promo->usage_limit)
                        ? ($promo->usage_count . ' / ∞')
                        : ($promo->usage_count . ' / ' . $promo->usage_limit);

          $usageClass  = $isExhausted ? 'bg-danger' : 'bg-success';
          $usageStatus = $isExhausted ? __('m_config.promocode.status.used') : __('m_config.promocode.status.available');
        @endphp

        <tr>
          <td class="fw-bold">{{ $promo->code }}</td>

          <td>
            @if (!is_null($promo->discount_percent))
              {{ number_format($promo->discount_percent, 2) }}{{ __('m_config.promocode.symbols.percent') }}
            @elseif (!is_null($promo->discount_amount))
              {{ __('m_config.promocode.symbols.currency') }}{{ number_format($promo->discount_amount, 2) }}
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
              — {{ __('m_config.promocode.labels.no_limit') }}
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
                  {{ __('m_config.promocode.labels.remaining') }}:
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
                  onsubmit="return confirm(@json(__('m_config.promocode.confirm_delete')))">
              @csrf
              @method('DELETE')
              <button class="btn btn-outline-danger btn-sm">{{ __('m_config.promocode.actions.delete') }}</button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="text-center">{{ __('m_config.promocode.empty') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
