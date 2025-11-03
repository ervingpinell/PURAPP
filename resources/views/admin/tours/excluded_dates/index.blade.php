@extends('adminlte::page')

@section('title', 'Gestión de Disponibilidad y Capacidad')

@section('content_header')
  <h1>
    <i class="fas fa-calendar-check me-2"></i>
    Gestión de Disponibilidad y Capacidad
  </h1>
@stop

@push('css')
<style>
  :root{
    --row-bg:#454d55;
    --title-separator:rgba(255,255,255,.08);
    --pad-x:.85rem;
    --gap:.5rem;
    --sticky-top:64px;
  }

  .al-title{ color:#fff; padding:.55rem var(--pad-x); border-radius:.25rem; }
  .al-day-header{ display:flex; align-items:center; gap:var(--gap); border:1px solid var(--title-separator); }
  .al-block-title{
    display:flex; align-items:center; gap:var(--gap);
    padding:.5rem var(--pad-x); margin-top:.85rem;
    border:1px solid var(--title-separator); border-radius:.25rem;
  }
  .btn-gap{ display:flex; align-items:center; gap:var(--gap); }

  .list-group-flush{ margin:0; }
  .list-group-flush .list-group-item{
    background:transparent; border:0; margin-bottom:.35rem; padding:0;
  }

  .row-item{
    display:flex; align-items:center; gap:.75rem;
    background:var(--row-bg);
    padding:.6rem var(--pad-x);
    border-radius:.25rem;
  }

  .row-item .state{ font-weight:700; }

  /* BADGES DE CAPACIDAD CON NIVELES */
  .row-item .capacity-badge{
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.25rem 0.6rem;
    border-radius: 0.25rem;
    font-size: 0.85em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    min-width: 70px;
    text-align: center;
    display: inline-block;
  }

  .row-item .capacity-badge:hover{
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
  }

  /* Bloqueado - Rojo oscuro */
  .row-item .capacity-badge.blocked{
    background: #dc3545 !important;
  }

  /* Sin override (tour base) - Azul/Púrpura */
  .row-item .capacity-badge.capacity-level-tour,
  .row-item .capacity-badge.capacity-level-none{
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  }

  /* Override en pivote (schedule_tour.base_capacity) - Verde/Azul */
  .row-item .capacity-badge.capacity-level-pivot{
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
  }

  /* Override por día (TourAvailability sin schedule_id) - Rosa/Rojo */
  .row-item .capacity-badge.capacity-level-day{
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  }

  /* Override día + horario (TourAvailability con schedule_id) - Rosa/Amarillo */
  .row-item .capacity-badge.capacity-level-day-schedule{
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
  }

  .row-item .form-check-input{
    margin:0 .5rem 0 0 !important;
    width:18px; height:18px; flex:0 0 18px; position:relative; top:0;
  }
  .al-empty{ padding:.5rem var(--pad-x); }

  .search-input{ min-width:260px }
  .bulk-dd .dropdown-menu{ min-width: 240px; }

  .sticky-filters{
    position: sticky;
    top: var(--sticky-top);
    z-index: 900;
    background: #343a40 !important;
    border-bottom: 1px solid var(--title-separator);
    transition: top .15s ease;
  }
  .sticky-filters.is-stuck{ box-shadow: 0 2px 6px rgba(0,0,0,.15); }

  .capacity-form{
    display: flex;
    gap: 0.5rem;
    align-items: center;
  }
  .capacity-form input{
    width: 80px;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    border: 1px solid #495057;
    background: #2d3238;
    color: white;
  }
  .capacity-form button{
    padding: 0.25rem 0.5rem;
  }

  /* Leyenda de colores */
  .capacity-legend{
    background: #2d3238;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
  }
  .capacity-legend-item{
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-right: 1rem;
    margin-bottom: 0.5rem;
  }
  .capacity-legend-color{
    width: 24px;
    height: 24px;
    border-radius: 0.25rem;
  }
</style>
@endpush

@section('content')

  {{-- Leyenda de Colores --}}
  <div class="capacity-legend">
    <h6 class="text-white mb-2"><i class="fas fa-palette me-2"></i>Leyenda de Capacidades</h6>
    <div class="d-flex flex-wrap">
      <div class="capacity-legend-item">
        <span class="capacity-legend-color" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></span>
        <small class="text-white">Base Tour</small>
      </div>
      <div class="capacity-legend-item">
        <span class="capacity-legend-color" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);"></span>
        <small class="text-white">Override Horario</small>
      </div>
      <div class="capacity-legend-item">
        <span class="capacity-legend-color" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);"></span>
        <small class="text-white">Override Día</small>
      </div>
      <div class="capacity-legend-item">
        <span class="capacity-legend-color" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);"></span>
        <small class="text-white">Override Día+Horario</small>
      </div>
      <div class="capacity-legend-item">
        <span class="capacity-legend-color" style="background: #dc3545;"></span>
        <small class="text-white">Bloqueado</small>
      </div>
    </div>
  </div>

  {{-- HEADER: Filtros --}}
  <div class="card-header bg-dark text-white d-flex flex-wrap align-items-end gap-2 sticky-filters">
    <form id="filtersForm" method="GET" action="{{ route('admin.tours.excluded_dates.index') }}" class="d-flex flex-wrap align-items-end gap-2 mb-0">
      <div>
        <label class="form-label mb-1">Fecha</label>
        <input
          type="date"
          name="date"
          value="{{ $date }}"
          class="form-control form-control-sm"
          id="filterDate"
          min="{{ \Carbon\Carbon::today(config('app.timezone','America/Costa_Rica'))->toDateString() }}"
        >
      </div>
      <div>
        <label class="form-label mb-1">Días</label>
        <input type="number" min="1" max="30" name="days" value="{{ $days }}" class="form-control form-control-sm" style="width:100px" id="filterDays">
      </div>
      <div>
        <label class="form-label mb-1">Buscar Tour</label>
        <input type="text" name="q" value="{{ $q }}" placeholder="Nombre del tour..." class="form-control form-control-sm search-input" id="filterQ">
      </div>
    </form>

    {{-- Acciones --}}
    <div class="ms-auto d-flex align-items-end gap-2">
      <div class="bulk-dd">
        <div class="btn-group">
          <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown">
            Acciones Masivas
          </button>
          <div class="dropdown-menu dropdown-menu-right dropdown-menu-end p-2">
            <button type="button" class="btn btn-danger btn-sm w-100" id="bulkBlock">
              <i class="fas fa-ban me-1"></i> Bloquear Seleccionados
            </button>
            <button type="button" class="btn btn-success btn-sm w-100 mt-2" id="bulkUnblock">
              <i class="fas fa-check me-1"></i> Desbloquear Seleccionados
            </button>
            <hr>
            <button type="button" class="btn btn-info btn-sm w-100" id="bulkSetCapacity">
              <i class="fas fa-users me-1"></i> Ajustar Capacidad
            </button>
          </div>
        </div>
      </div>

      <a class="btn btn-warning btn-sm js-view-blocked ms-3"
         href="{{ route('admin.tours.excluded_dates.blocked', ['date' => $date, 'days' => $days, 'q' => $q]) }}">
        <i class="fas fa-lock me-1"></i> Ver Bloqueados
      </a>

      <a class="btn btn-info btn-sm ms-2"
         href="{{ route('admin.tours.capacity.index') }}">
        <i class="fas fa-cog me-1"></i> Configuración Capacidades
      </a>
    </div>
  </div>

  <div class="card-body p-2">
    <div class="mt-3"></div>

    @php
      $fmt = fn($d) => \Carbon\Carbon::parse($d)->locale(app()->getLocale() ?: 'es')->isoFormat('dddd D [de] MMMM');
    @endphp

    @forelse($calendar as $day => $buckets)
      <div class="mb-4">

        {{-- Día --}}
        <div class="al-title bg-dark al-day-header">
          <div class="fw-bold">
            {{ ucfirst($fmt($day)) }}
            <span class="ms-2 text-success opacity-75">
              ({{ count($buckets['am']) + count($buckets['pm']) }} tours)
            </span>
          </div>
          <div class="ms-auto btn-gap">
            <button class="btn btn-primary btn-sm js-mark-day"
                    type="button"
                    data-day="{{ $day }}"
                    onclick="toggleMarkDay(this,'{{ $day }}')">
              Marcar Todos
            </button>
            <button class="btn btn-danger btn-sm" type="button" onclick="blockAllInDay('{{ $day }}')">
              Bloquear Todos
            </button>
            <button class="btn btn-info btn-sm" type="button" onclick="setCapacityForDay('{{ $day }}')">
              <i class="fas fa-users"></i> Capacidad
            </button>
          </div>
        </div>

        {{-- AM --}}
        <div class="al-title bg-dark al-block-title">
          <span class="fw-bold small mb-0">TOURS AM</span>
          <div class="ms-auto btn-gap">
            <button type="button"
                    class="btn btn-primary btn-sm js-mark-block"
                    data-day="{{ $day }}" data-bucket="am"
                    onclick="toggleMarkBlock(this,'{{ $day }}','am')">
              Marcar Todos
            </button>
            <button type="button" class="btn btn-danger btn-sm" onclick="blockAllInBlock('{{ $day }}','am')">
              Bloquear Todos
            </button>
          </div>
        </div>

        <div id="day-{{ $day }}-am" class="list-group list-group-flush mt-2">
          @forelse($buckets['am'] as $it)
            <div class="list-group-item">
              <div class="row-item"
                   data-day="{{ $day }}"
                   data-tid="{{ $it['tour_id'] }}"
                   data-sid="{{ $it['schedule_id'] }}"
                   data-capacity="{{ $it['current_capacity'] ?? 15 }}"
                   data-title="{{ $it['tour_name'] }} ({{ $it['time'] }})">
                <input type="checkbox" class="form-check-input me-2 select-item">
                <div class="flex-grow-1">
                  <span class="me-2">{{ $it['tour_name'] }} ({{ $it['time'] }})</span>
                  <span class="state {{ $it['is_available'] ? 'text-success' : 'text-danger' }}">
                    {{ $it['is_available'] ? 'Disponible' : 'Bloqueado' }}
                  </span>
                </div>

                {{-- Badge de capacidad con ocupación y colores por nivel --}}
                <span class="capacity-badge capacity-level-{{ $it['override_level'] ?? 'none' }} {{ !$it['is_available'] ? 'blocked' : '' }}"
                      onclick="openCapacityModal('{{ $day }}', {{ $it['tour_id'] }}, {{ $it['schedule_id'] }}, '{{ $it['tour_name'] }} ({{ $it['time'] }})', {{ $it['current_capacity'] ?? 15 }})"
                      title="Ocupados/Capacidad - {{ ucfirst(str_replace(['-', '_'], ' ', $it['override_level'] ?? 'base')) }}">
                  @if(!$it['is_available'])
                    <i class="fas fa-ban me-1"></i> 0/0
                  @else
                    <i class="fas fa-users me-1"></i> {{ $it['occupied_count'] ?? 0 }}/{{ $it['current_capacity'] ?? 15 }}
                  @endif
                </span>

                <div class="btn-gap">
                  <button type="button" class="btn btn-danger btn-sm btn-block"
                          onclick="confirmToggleOne(this, '{{ $day }}', {{ $it['tour_id'] }}, {{ $it['schedule_id'] }}, 'block')"
                          {{ !$it['is_available'] ? 'disabled' : '' }}>
                    Bloquear
                  </button>
                  <button type="button" class="btn btn-success btn-sm btn-unblock"
                          onclick="confirmToggleOne(this, '{{ $day }}', {{ $it['tour_id'] }}, {{ $it['schedule_id'] }}, 'unblock')"
                          {{ $it['is_available'] ? 'disabled' : '' }}>
                    Desbloquear
                  </button>
                </div>
              </div>
            </div>
          @empty
            <div class="text-muted al-empty">No hay tours en este bloque</div>
          @endforelse
        </div>

        {{-- PM --}}
        <div class="al-title bg-dark al-block-title mt-3">
          <span class="fw-bold small mb-0">TOURS PM</span>
          <div class="ms-auto btn-gap">
            <button type="button"
                    class="btn btn-primary btn-sm js-mark-block"
                    data-day="{{ $day }}" data-bucket="pm"
                    onclick="toggleMarkBlock(this,'{{ $day }}','pm')">
              Marcar Todos
            </button>
            <button type="button" class="btn btn-danger btn-sm" onclick="blockAllInBlock('{{ $day }}','pm')">
              Bloquear Todos
            </button>
          </div>
        </div>

        <div id="day-{{ $day }}-pm" class="list-group list-group-flush mt-2">
          @forelse($buckets['pm'] as $it)
            <div class="list-group-item">
              <div class="row-item"
                   data-day="{{ $day }}"
                   data-tid="{{ $it['tour_id'] }}"
                   data-sid="{{ $it['schedule_id'] }}"
                   data-capacity="{{ $it['current_capacity'] ?? 15 }}"
                   data-title="{{ $it['tour_name'] }} ({{ $it['time'] }})">
                <input type="checkbox" class="form-check-input me-2 select-item">
                <div class="flex-grow-1">
                  <span class="me-2">{{ $it['tour_name'] }} ({{ $it['time'] }})</span>
                  <span class="state {{ $it['is_available'] ? 'text-success' : 'text-danger' }}">
                    {{ $it['is_available'] ? 'Disponible' : 'Bloqueado' }}
                  </span>
                </div>

                {{-- Badge de capacidad con ocupación y colores por nivel --}}
                <span class="capacity-badge capacity-level-{{ $it['override_level'] ?? 'none' }} {{ !$it['is_available'] ? 'blocked' : '' }}"
                      onclick="openCapacityModal('{{ $day }}', {{ $it['tour_id'] }}, {{ $it['schedule_id'] }}, '{{ $it['tour_name'] }} ({{ $it['time'] }})', {{ $it['current_capacity'] ?? 15 }})"
                      title="Ocupados/Capacidad - {{ ucfirst(str_replace(['-', '_'], ' ', $it['override_level'] ?? 'base')) }}">
                  @if(!$it['is_available'])
                    <i class="fas fa-ban me-1"></i> 0/0
                  @else
                    <i class="fas fa-users me-1"></i> {{ $it['occupied_count'] ?? 0 }}/{{ $it['current_capacity'] ?? 15 }}
                  @endif
                </span>

                <div class="btn-gap">
                  <button type="button" class="btn btn-danger btn-sm btn-block"
                          onclick="confirmToggleOne(this, '{{ $day }}', {{ $it['tour_id'] }}, {{ $it['schedule_id'] }}, 'block')"
                          {{ !$it['is_available'] ? 'disabled' : '' }}>
                    Bloquear
                  </button>
                  <button type="button" class="btn btn-success btn-sm btn-unblock"
                          onclick="confirmToggleOne(this, '{{ $day }}', {{ $it['tour_id'] }}, {{ $it['schedule_id'] }}, 'unblock')"
                          {{ $it['is_available'] ? 'disabled' : '' }}>
                    Desbloquear
                  </button>
                </div>
              </div>
            </div>
          @empty
            <div class="text-muted al-empty">No hay tours en este bloque</div>
          @endforelse
        </div>

      </div>
    @empty
      <div class="text-muted al-empty">No hay datos para mostrar</div>
    @endforelse
  </div>
</div>

{{-- Modal para ajustar capacidad individual --}}
<div class="modal fade" id="capacityModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ajustar Capacidad</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p><strong id="capacityModalLabel"></strong></p>
        <p class="text-muted small">Fecha: <span id="capacityModalDate"></span></p>

        <div class="alert alert-info small">
          <i class="fas fa-info-circle me-1"></i>
          <strong>Jerarquía de capacidades:</strong><br>
          1. Override Día+Horario (máxima prioridad)<br>
          2. Override Día completo<br>
          3. Capacidad del Horario (pivote)<br>
          4. Capacidad del Tour (base)
        </div>

        <div class="mb-3">
          <label class="form-label">Nueva Capacidad</label>
          <input type="number" id="capacityInput" class="form-control" min="0" max="999" value="15">
          <small class="text-muted">Dejar en 0 para bloquear completamente</small>
        </div>

        <input type="hidden" id="capacityModalTourId">
        <input type="hidden" id="capacityModalScheduleId">
        <input type="hidden" id="capacityModalDay">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="saveCapacity()">Guardar</button>
      </div>
    </div>
  </div>
</div>

{{-- Modal para capacidad masiva --}}
<div class="modal fade" id="bulkCapacityModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ajustar Capacidad de Seleccionados</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Se actualizará la capacidad de <strong id="bulkCapacityCount">0</strong> items seleccionados.</p>

        <div class="mb-3">
          <label class="form-label">Nueva Capacidad</label>
          <input type="number" id="bulkCapacityInput" class="form-control" min="0" max="999" value="15">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="saveBulkCapacity()">Aplicar</button>
      </div>
    </div>
  </div>
</div>

@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const TOGGLE_URL = @json(route('admin.tours.excluded_dates.toggle'));
const BULK_URL   = @json(route('admin.tours.excluded_dates.bulkToggle'));
const CAPACITY_URL = @json(route('admin.tours.capacity.store'));
const CSRF       = @json(csrf_token());

const toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 1600,
  timerProgressBar: true
});

/* ===== Sticky header ===== */
(function(){
  const root = document.documentElement;
  const mainHeader = document.querySelector('.main-header');
  const contentHdr = document.querySelector('.content-header');
  const stickyBar = document.querySelector('.sticky-filters');

  let Hm = 0, idleTop = 0, tightTop = 0;

  function recalc() {
    Hm = mainHeader?.offsetHeight || 0;
    const Hc = contentHdr?.offsetHeight || 0;
    idleTop = Hm + Hc + 8;
    tightTop = Hm;
    apply();
  }
  function apply() {
    const chBottom = contentHdr ? contentHdr.getBoundingClientRect().bottom : 0;
    const wantTop = chBottom <= Hm ? tightTop : idleTop;
    root.style.setProperty('--sticky-top', wantTop + 'px');
    if(stickyBar){ stickyBar.classList.toggle('is-stuck', wantTop === tightTop); }
  }

  recalc();
  window.addEventListener('scroll', apply, {passive:true});
  window.addEventListener('resize', recalc, {passive:true});
  window.addEventListener('load', recalc);
})();

/* ===== Filtros ===== */
const form = document.getElementById('filtersForm');
const iDate = document.getElementById('filterDate');
const iDays = document.getElementById('filterDays');
const iQ = document.getElementById('filterQ');

function todayStr(){
  const now = new Date();
  const tzOff = now.getTimezoneOffset()*60000;
  return new Date(Date.now()-tzOff).toISOString().slice(0,10);
}

iDate.addEventListener('change', () => {
  const t = todayStr();
  if(iDate.value < t){
    Swal.fire('Fecha inválida', 'No puedes seleccionar fechas pasadas', 'info');
    iDate.value = t;
    return;
  }
  iDays.value = 1;
  iQ.value = '';
  toast.fire({icon:'info', title:'Aplicando filtros...'});
  form.submit();
});

function debounce(fn, ms){ let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), ms); }; }

iQ.addEventListener('input', debounce(() => {
  if(iQ.value.trim().length > 0) iDays.value = Math.max(Number(iDays.value||1), 30);
  toast.fire({icon:'info', title:'Buscando...'});
  form.submit();
}, 400));

iDays.addEventListener('change', () => {
  let v = parseInt(iDays.value || '1', 10);
  if(isNaN(v) || v < 1) v = 1;
  if(v > 30) v = 30;
  iDays.value = v;
  toast.fire({icon:'info', title:'Actualizando rango...'});
  form.submit();
});

/* ===== Toggle individual ===== */
async function toggleOne(el, day, tourId, scheduleId, want){
  const row = el.closest('.row-item');
  const btnBlock = row.querySelector('.btn-block');
  const btnUnblock = row.querySelector('.btn-unblock');

  btnBlock.disabled = true;
  btnUnblock.disabled = true;

  try{
    const res = await fetch(TOGGLE_URL, {
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
      body: JSON.stringify({ tour_id:tourId, schedule_id:scheduleId, date:day, want })
    });
    const data = await res.json();
    if(!data.ok) throw new Error('bad response');

    const state = row.querySelector('.state');
    const available = !!data.is_available;
    state.textContent = available ? 'Disponible' : 'Bloqueado';
    state.classList.toggle('text-success', available);
    state.classList.toggle('text-danger', !available);

    btnBlock.disabled = !available;
    btnUnblock.disabled = available;

    // Actualizar badge de capacidad
    const capacityBadge = row.querySelector('.capacity-badge');
    if(!available){
      capacityBadge.classList.add('blocked');
      capacityBadge.innerHTML = '<i class="fas fa-ban me-1"></i> 0/0';
    } else {
      capacityBadge.classList.remove('blocked');
      const cap = row.dataset.capacity || 15;
      capacityBadge.innerHTML = `<i class="fas fa-users me-1"></i> 0/${cap}`;
    }

    toast.fire({icon:'success', title: available ? 'Desbloqueado' : 'Bloqueado' });
  }catch(e){
    console.error(e);
    Swal.fire('Error', 'No se pudo actualizar el estado', 'error');
  }
}

async function confirmToggleOne(el, day, tourId, scheduleId, want){
  const row = el.closest('.row-item');
  const label = row?.dataset.title || '—';
  const isBlock = want === 'block';

  const res = await Swal.fire({
    icon: 'warning',
    title: isBlock ? '¿Bloquear?' : '¿Desbloquear?',
    html: `<strong>${label}</strong><br>Fecha: ${day}`,
    showCancelButton: true,
    confirmButtonText: isBlock ? 'Bloquear' : 'Desbloquear',
    cancelButtonText: 'Cancelar'
  });

  if(res.isConfirmed){
    await toggleOne(el, day, tourId, scheduleId, want);
  }
}

/* ===== Gestión de capacidad ===== */
let capacityModalInstance;

function openCapacityModal(day, tourId, scheduleId, label, currentCapacity){
  document.getElementById('capacityModalLabel').textContent = label;
  document.getElementById('capacityModalDate').textContent = day;
  document.getElementById('capacityInput').value = currentCapacity;
  document.getElementById('capacityModalTourId').value = tourId;
  document.getElementById('capacityModalScheduleId').value = scheduleId;
  document.getElementById('capacityModalDay').value = day;

  if(!capacityModalInstance){
    capacityModalInstance = new bootstrap.Modal(document.getElementById('capacityModal'));
  }
  capacityModalInstance.show();
}

async function saveCapacity(){
  const tourId = document.getElementById('capacityModalTourId').value;
  const scheduleId = document.getElementById('capacityModalScheduleId').value;
  const day = document.getElementById('capacityModalDay').value;
  const capacity = parseInt(document.getElementById('capacityInput').value);

  try{
    const res = await fetch(CAPACITY_URL, {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
      body: JSON.stringify({
        tour_id: tourId,
        schedule_id: scheduleId,
        date: day,
        max_capacity: capacity > 0 ? capacity : null,
        is_blocked: capacity === 0
      })
    });

    if(!res.ok) throw new Error('Failed to save capacity');

    // Actualizar UI
    const row = document.querySelector(`.row-item[data-day="${day}"][data-tid="${tourId}"][data-sid="${scheduleId}"]`);
    if(row){
      row.dataset.capacity = capacity;
      const badge = row.querySelector('.capacity-badge');

      if(capacity === 0){
        badge.classList.add('blocked');
        badge.innerHTML = '<i class="fas fa-ban me-1"></i> 0/0';

        // Actualizar estado a bloqueado
        const state = row.querySelector('.state');
        state.textContent = 'Bloqueado';
        state.classList.remove('text-success');
        state.classList.add('text-danger');

        row.querySelector('.btn-block').disabled = true;
        row.querySelector('.btn-unblock').disabled = false;
      } else {
        badge.classList.remove('blocked');
        badge.classList.remove('capacity-level-tour', 'capacity-level-pivot', 'capacity-level-day', 'capacity-level-none');
        badge.classList.add('capacity-level-day-schedule');
        badge.innerHTML = `<i class="fas fa-users me-1"></i> 0/${capacity}`;
      }
    }

    capacityModalInstance.hide();
    toast.fire({icon:'success', title:'Capacidad actualizada'});

    // Recargar página después de 1 segundo para actualizar ocupación real
    setTimeout(() => window.location.reload(), 1000);
  }catch(e){
    console.error(e);
    Swal.fire('Error', 'No se pudo actualizar la capacidad', 'error');
  }
}

function setCapacityForDay(day){
  Swal.fire({
    title: 'Ajustar capacidad para el día',
    html: `<strong>${day}</strong><br>Todos los horarios del día`,
    input: 'number',
    inputAttributes: {
      min: 0,
      max: 999,
      step: 1
    },
    inputValue: 15,
    showCancelButton: true,
    confirmButtonText: 'Aplicar',
    cancelButtonText: 'Cancelar'
  }).then(async (result) => {
    if(result.isConfirmed){
      const capacity = parseInt(result.value);
      const rows = document.querySelectorAll(`.row-item[data-day="${day}"]`);

      let updated = 0;
      for(const row of rows){
        const tourId = row.dataset.tid;
        const scheduleId = row.dataset.sid;

        try{
          await fetch(CAPACITY_URL, {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
            body: JSON.stringify({
              tour_id: tourId,
              schedule_id: scheduleId,
              date: day,
              max_capacity: capacity > 0 ? capacity : null,
              is_blocked: capacity === 0
            })
          });
          updated++;
        }catch(e){
          console.error(e);
        }
      }

      toast.fire({icon:'success', title:`${updated} capacidades actualizadas`});
      setTimeout(() => window.location.reload(), 1000);
    }
  });
}

/* ===== Acciones masivas ===== */
function collectSelected(){
  const sel = [];
  document.querySelectorAll('.row-item').forEach(r => {
    const cb = r.querySelector('.select-item');
    if(cb && cb.checked){
      sel.push({
        tour_id: r.dataset.tid,
        schedule_id: r.dataset.sid,
        date: r.dataset.day,
        _label: r.dataset.title
      });
    }
  });
  return sel;
}

document.getElementById('bulkBlock').addEventListener('click', async () => {
  const items = collectSelected();
  if(!items.length){
    Swal.fire('Sin selección', 'Debes seleccionar al menos un item', 'info');
    return;
  }

  const res = await Swal.fire({
    icon: 'warning',
    title: 'Bloquear seleccionados',
    html: `Se bloquearán ${items.length} items`,
    showCancelButton: true,
    confirmButtonText: 'Bloquear',
    cancelButtonText: 'Cancelar'
  });

  if(res.isConfirmed) await bulkToggle(items, 'block');
});

document.getElementById('bulkUnblock').addEventListener('click', async () => {
  const items = collectSelected();
  if(!items.length){
    Swal.fire('Sin selección', 'Debes seleccionar al menos un item', 'info');
    return;
  }

  const res = await Swal.fire({
    icon: 'warning',
    title: 'Desbloquear seleccionados',
    html: `Se desbloquearán ${items.length} items`,
    showCancelButton: true,
    confirmButtonText: 'Desbloquear',
    cancelButtonText: 'Cancelar'
  });

  if(res.isConfirmed) await bulkToggle(items, 'unblock');
});

document.getElementById('bulkSetCapacity').addEventListener('click', () => {
  const items = collectSelected();
  if(!items.length){
    Swal.fire('Sin selección', 'Debes seleccionar al menos un item', 'info');
    return;
  }

  document.getElementById('bulkCapacityCount').textContent = items.length;
  const bulkModal = new bootstrap.Modal(document.getElementById('bulkCapacityModal'));
  bulkModal.show();
});

async function saveBulkCapacity(){
  const items = collectSelected();
  const capacity = parseInt(document.getElementById('bulkCapacityInput').value);

  let updated = 0;
  for(const item of items){
    try{
      await fetch(CAPACITY_URL, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({
          tour_id: item.tour_id,
          schedule_id: item.schedule_id,
          date: item.date,
          max_capacity: capacity > 0 ? capacity : null,
          is_blocked: capacity === 0
        })
      });
      updated++;
    }catch(e){
      console.error(e);
    }
  }

  bootstrap.Modal.getInstance(document.getElementById('bulkCapacityModal')).hide();

  // Desmarcar selección
  document.querySelectorAll('.select-item:checked').forEach(cb => cb.checked = false);

  toast.fire({icon:'success', title:`${updated} capacidades actualizadas`});
  setTimeout(() => window.location.reload(), 1000);
}

async function bulkToggle(items, want){
  try{
    const res = await fetch(BULK_URL, {
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
      body: JSON.stringify({ items, want })
    });
    const data = await res.json();
    if(!data.ok) throw new Error();

    document.querySelectorAll('.select-item:checked').forEach(cb => cb.checked = false);

    Swal.fire({
      icon:'success',
      title:'Actualizado',
      html:`${data.changed} items actualizados`,
      timer:1300,
      showConfirmButton:false
    });

    setTimeout(() => window.location.reload(), 1300);
  }catch(e){
    console.error(e);
    Swal.fire('Error', 'No se pudo completar la operación', 'error');
  }
}

function blockAllInDay(day){
  const rows = document.querySelectorAll(`.row-item[data-day="${day}"]`);
  confirmBulkFromRows(rows, 'block', `Bloquear todo el día ${day}`);
}

function blockAllInBlock(day, bucket){
  const rows = document.querySelectorAll(`#day-${day}-${bucket} .row-item`);
  confirmBulkFromRows(rows, 'block', `Bloquear bloque ${bucket.toUpperCase()} del ${day}`);
}

async function confirmBulkFromRows(rows, want, titleHtml){
  const items = [];
  rows.forEach(r => {
    const isAvailable = r.querySelector('.state').classList.contains('text-success');
    if((want === 'block' && isAvailable) || (want === 'unblock' && !isAvailable)){
      items.push({
        tour_id: r.dataset.tid,
        schedule_id: r.dataset.sid,
        date: r.dataset.day,
        _label: r.dataset.title
      });
    }
  });

  if(!items.length){
    Swal.fire('Sin cambios', 'No hay items para actualizar', 'info');
    return;
  }

  const res = await Swal.fire({
    icon: 'warning',
    title: 'Confirmar',
    html: `${titleHtml}<br>${items.length} items afectados`,
    showCancelButton: true,
    confirmButtonText: want === 'block' ? 'Bloquear' : 'Desbloquear',
    cancelButtonText: 'Cancelar'
  });

  if(res.isConfirmed){
    await bulkToggle(items, want);
  }
}

/* ===== Marcar/Desmarcar ===== */
function getDayCheckboxes(day){ return document.querySelectorAll(`.row-item[data-day="${day}"] .select-item`); }
function getBlockCheckboxes(day, bucket){ return document.querySelectorAll(`#day-${day}-${bucket} .select-item`); }
function areAllChecked(list){ const arr = Array.from(list); return arr.length > 0 && arr.every(cb => cb.checked); }
function setBtnLabel(btn, allChecked){ btn.textContent = allChecked ? 'Desmarcar Todos' : 'Marcar Todos'; }

function refreshMarkLabelsFor(day){
  const dayCbs = getDayCheckboxes(day);
  document.querySelectorAll(`.js-mark-day[data-day="${day}"]`).forEach(btn => setBtnLabel(btn, areAllChecked(dayCbs)));

  ['am','pm'].forEach(bucket => {
    const blockCbs = getBlockCheckboxes(day, bucket);
    document.querySelectorAll(`.js-mark-block[data-day="${day}"][data-bucket="${bucket}"]`).forEach(btn => setBtnLabel(btn, areAllChecked(blockCbs)));
  });
}

function toggleMarkDay(btn, day){
  const cbs = getDayCheckboxes(day);
  const all = areAllChecked(cbs);
  cbs.forEach(cb => cb.checked = !all);
  refreshMarkLabelsFor(day);
  toast.fire({icon:'info', title: (!all ? 'Marcados' : 'Desmarcados') + ` ${cbs.length} items`});
}

function toggleMarkBlock(btn, day, bucket){
  const cbs = getBlockCheckboxes(day, bucket);
  const all = areAllChecked(cbs);
  cbs.forEach(cb => cb.checked = !all);
  refreshMarkLabelsFor(day);
  toast.fire({icon:'info', title: (!all ? 'Marcados' : 'Desmarcados') + ` ${cbs.length} items`});
}

document.addEventListener('change', (e) => {
  if(!e.target.classList.contains('select-item')) return;
  const day = e.target.closest('.row-item')?.dataset.day;
  if(day) refreshMarkLabelsFor(day);
});

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.js-mark-day').forEach(btn => refreshMarkLabelsFor(btn.dataset.day));
});
</script>
@endpush
