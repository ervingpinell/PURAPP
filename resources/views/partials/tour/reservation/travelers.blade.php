{{-- Travelers inline + total --}}
<div class="mb-3 gv-travelers">
  <div class="gv-trav-rows mt-2">

    {{-- ADULTOS --}}
    <div class="gv-trav-row d-flex align-items-center justify-content-between py-2 border rounded px-2 mb-2">
      <div class="d-flex align-items-center gap-2">
        <i class="fas fa-male" aria-hidden="true"></i>
        <span class="fw-semibold">{{ __('adminlte::adminlte.adult') }}</span>
      </div>
      <div class="d-flex align-items-center gap-2">
        <button type="button"
                id="adultMinusBtn"
                class="btn btn-outline-secondary btn-sm"
                aria-label="{{ __('adminlte::adminlte.decrease_adult') ?? 'Restar adulto' }}">−</button>

        <input id="adultInput"
               class="form-control form-control-sm text-center"
               type="number" inputmode="numeric" pattern="[0-9]*"
               min="1" step="1" value="2"
               aria-label="{{ __('adminlte::adminlte.adults_quantity') ?? 'Cantidad de adultos' }}">

        <button type="button"
                id="adultPlusBtn"
                class="btn btn-outline-secondary btn-sm"
                aria-label="{{ __('adminlte::adminlte.increase_adult') ?? 'Sumar adulto' }}">+</button>
      </div>
    </div>

    {{-- NIÑOS --}}
    <div class="gv-trav-row d-flex align-items-center justify-content-between py-2 border rounded px-2">
      <div class="d-flex align-items-center gap-2">
        <i class="fas fa-child" aria-hidden="true"></i>
        <span class="fw-semibold">{{ __('adminlte::adminlte.kid') }}</span>
      </div>
      <div class="d-flex align-items-center gap-2">
        <button type="button"
                id="kidMinusBtn"
                class="btn btn-outline-secondary btn-sm"
                aria-label="{{ __('adminlte::adminlte.decrease_kid') ?? 'Restar niño' }}">−</button>

        <input id="kidInput"
               class="form-control form-control-sm text-center"
               type="number" inputmode="numeric" pattern="[0-9]*"
               min="0" step="1" value="0"
               aria-label="{{ __('adminlte::adminlte.kids_quantity') ?? 'Cantidad de niños' }}">

        <button type="button"
                id="kidPlusBtn"
                class="btn btn-outline-secondary btn-sm"
                aria-label="{{ __('adminlte::adminlte.increase_kid') ?? 'Sumar niño' }}">+</button>
      </div>
    </div>

  </div>

  {{-- Total --}}
  <div class="gv-total-inline mt-2">
    <div class="d-flex justify-content-between">
      <span>{{ __('adminlte::adminlte.total') }}:</span>
      <strong id="reservation-total-price-inline">$0.00</strong>
    </div>
  </div>
</div>
