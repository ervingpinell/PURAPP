@extends('adminlte::page')

@section('title', 'Tours bloqueados')

@section('content_header')
  <h1><i class="fas fa-lock me-2"></i> Tours bloqueados</h1>
@stop

@push('css')
<style>
  :root{
    --row-bg:#454d55;
    --title-separator:rgba(255,255,255,.08);
    --pad-x:.85rem;
    --gap:.5rem;
    --sticky-offset: 64px;
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
  .list-group-flush .list-group-item{ background:transparent; border:0; margin-bottom:.35rem; padding:0; }
  .row-item{ display:flex; align-items:center; gap:.75rem; background:var(--row-bg); padding:.6rem var(--pad-x); border-radius:.25rem; }
  .row-item .state{ font-weight:700; }
  .row-item .form-check-input{ margin:0 .5rem 0 0 !important; width:18px; height:18px; flex:0 0 18px; position:relative; top:0; }
  .al-empty{ padding:.5rem var(--pad-x); }
  .search-input{ min-width:260px }
  .bulk-dd .dropdown-menu{ min-width: 240px; }
  .sticky-filters{ position: sticky; top: var(--sticky-offset); z-index: 1041; background:#343a40!important; border-bottom:1px solid var(--title-separator); }
</style>
@endpush

@section('content')

  {{-- HEADER: mismos filtros + sólo acción de DESBLOQUEAR + volver --}}
  <div class="card-header bg-dark text-white d-flex flex-wrap align-items-end gap-2 sticky-filters">
    <form id="filtersForm" method="GET" action="{{ route('admin.tours.excluded_dates.blocked') }}" class="d-flex flex-wrap align-items-end gap-2 mb-0">
      <div>
        <label class="form-label mb-1">Date</label>
        <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm" id="filterDate"
               min="{{ \Carbon\Carbon::today(config('app.timezone','America/Costa_Rica'))->toDateString() }}">
      </div>
      <div>
        <label class="form-label mb-1">Days</label>
        <input type="number" min="1" max="30" name="days" value="{{ $days }}" class="form-control form-control-sm" style="width:100px" id="filterDays">
      </div>
      <div>
        <label class="form-label mb-1">Product</label>
        <input type="text" name="q" value="{{ $q }}" placeholder="Buscar tour..." class="form-control form-control-sm search-input" id="filterQ">
      </div>
    </form>

    <div class="ms-auto d-flex align-items-end gap-2">
      <div class="bulk-dd">
        <div class="btn-group">
          <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false">
            Actualizar estado
          </button>
          <div class="dropdown-menu dropdown-menu-right dropdown-menu-end p-2">
            <button type="button" class="btn btn-success btn-sm w-100" id="bulkUnblock">
              <i class="fas fa-check me-1"></i> Desbloquear seleccionados
            </button>
          </div>
        </div>
      </div>

      <a class="btn btn-outline-light btn-sm" href="{{ route('admin.tours.excluded_dates.index', ['date'=>$date,'days'=>$days,'q'=>$q]) }}">
        <i class="fas fa-arrow-left me-1"></i> Volver
      </a>
    </div>
  </div>

  <div class="card-body p-2" id="blockedRoot">
    <div class="mt-3"></div>

    @php
      $fmt = fn($d) => \Carbon\Carbon::parse($d)->locale(app()->getLocale() ?: 'es')->isoFormat('dddd D [de] MMMM');
    @endphp

    @forelse($calendar as $day => $buckets)
      <div class="mb-4" id="wrap-{{ $day }}">

        <div class="al-title bg-dark al-day-header">
          <div class="fw-bold">
            {{ ucfirst($fmt($day)) }}
            <span class="ms-2 text-success opacity-75">
              ( <span class="js-day-count" data-day="{{ $day }}">{{ count($buckets['am']) + count($buckets['pm']) }}</span> bloqueados )
            </span>
          </div>
          <div class="ms-auto btn-gap">
            <button class="btn btn-primary btn-sm js-mark-day"
                    type="button"
                    data-day="{{ $day }}"
                    onclick="toggleMarkDay(this,'{{ $day }}')">
              Marcar todos
            </button>
            <button class="btn btn-success btn-sm" type="button" onclick="unblockAllInDay('{{ $day }}')">Desbloquear todos</button>
          </div>
        </div>

        <div class="al-title bg-dark al-block-title">
          <span class="fw-bold small mb-0">AM bloqueados</span>
          <div class="ms-auto btn-gap">
            <button type="button" class="btn btn-primary btn-sm js-mark-block" data-day="{{ $day }}" data-bucket="am" onclick="toggleMarkBlock(this,'{{ $day }}','am')">Marcar todos</button>
            <button type="button" class="btn btn-success btn-sm" onclick="unblockAllInBlock('{{ $day }}','am')">Desbloquear todos</button>
          </div>
        </div>

        <div id="day-{{ $day }}-am" class="list-group list-group-flush mt-2">
          @forelse($buckets['am'] as $it)
            <div class="list-group-item">
              <div class="row-item"
                   data-day="{{ $day }}"
                   data-tid="{{ $it['tour_id'] }}"
                   data-sid="{{ $it['schedule_id'] }}"
                   data-title="{{ $it['tour_name'] }} ({{ $it['time'] }})">
                <input type="checkbox" class="form-check-input me-2 select-item">
                <div class="flex-grow-1">
                  <span class="me-2">{{ $it['tour_name'] }} ({{ $it['time'] }})</span>
                  <span class="state text-danger">Blocked</span>
                </div>
                <div class="btn-gap">
                  <button type="button" class="btn btn-success btn-sm btn-unblock"
                          onclick="confirmToggleOne(this, '{{ $day }}', {{ $it['tour_id'] }}, {{ $it['schedule_id'] }}, 'unblock')">Unblock</button>
                </div>
              </div>
            </div>
          @empty
            <div class="text-muted al-empty">Sin bloqueados en AM.</div>
          @endforelse
        </div>

        <div class="al-title bg-dark al-block-title mt-3">
          <span class="fw-bold small mb-0">PM bloqueados</span>
          <div class="ms-auto btn-gap">
            <button type="button" class="btn btn-primary btn-sm js-mark-block" data-day="{{ $day }}" data-bucket="pm" onclick="toggleMarkBlock(this,'{{ $day }}','pm')">Marcar todos</button>
            <button type="button" class="btn btn-success btn-sm" onclick="unblockAllInBlock('{{ $day }}','pm')">Desbloquear todos</button>
          </div>
        </div>

        <div id="day-{{ $day }}-pm" class="list-group list-group-flush mt-2">
          @forelse($buckets['pm'] as $it)
            <div class="list-group-item">
              <div class="row-item"
                   data-day="{{ $day }}"
                   data-tid="{{ $it['tour_id'] }}"
                   data-sid="{{ $it['schedule_id'] }}"
                   data-title="{{ $it['tour_name'] }} ({{ $it['time'] }})">
                <input type="checkbox" class="form-check-input me-2 select-item">
                <div class="flex-grow-1">
                  <span class="me-2">{{ $it['tour_name'] }} ({{ $it['time'] }})</span>
                  <span class="state text-danger">Blocked</span>
                </div>
                <div class="btn-gap">
                  <button type="button" class="btn btn-success btn-sm btn-unblock"
                          onclick="confirmToggleOne(this, '{{ $day }}', {{ $it['tour_id'] }}, {{ $it['schedule_id'] }}, 'unblock')">Unblock</button>
                </div>
              </div>
            </div>
          @empty
            <div class="text-muted al-empty">Sin bloqueados en PM.</div>
          @endforelse
        </div>

      </div>
    @empty
      <div class="text-muted al-empty">No hay tours bloqueados en el rango seleccionado.</div>
    @endforelse

  </div>
@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const TOGGLE_URL = @json(route('admin.tours.excluded_dates.toggle'));
const BULK_URL   = @json(route('admin.tours.excluded_dates.bulkToggle'));
const CSRF       = @json(csrf_token());

/* ========== Toast base ========== */
const toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 1600,
  timerProgressBar: true
    customClass: {
    popup: 'my-toast'
  }
});

/* Sticky offset (header AdminLTE + título de página) */
(function(){
  const mainHeader = document.querySelector('.main-header');
  const contentHdr = document.querySelector('.content-header');
  const off = (mainHeader?.offsetHeight || 0) + (contentHdr?.offsetHeight || 0) + 8;
  document.documentElement.style.setProperty('--sticky-offset', off + 'px');
})();

/* ===== Filtros ===== */
const form   = document.getElementById('filtersForm');
const iDate  = document.getElementById('filterDate');
const iDays  = document.getElementById('filterDays');
const iQ     = document.getElementById('filterQ');

function todayStr(){ const tz = (new Date()).getTimezoneOffset()*60000; return new Date(Date.now()-tz).toISOString().slice(0,10); }
if(iDate){ const t=todayStr(); if(!iDate.min) iDate.min=t; if(iDate.value && iDate.value < t){ iDate.value=t; } }
function clampDays(){ let v=parseInt(iDays.value||'1',10); if(isNaN(v)||v<1)v=1; if(v>30)v=30; iDays.value=v; }
iDate.addEventListener('change', ()=>{ const t=todayStr(); if(iDate.value<t){ Swal.fire('Fecha inválida','No se permiten fechas pasadas.','info'); iDate.value=t; } iDays.value=1; iQ.value=''; form.submit(); });
function debounce(fn,ms){ let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a),ms); }; }
iQ.addEventListener('input', debounce(()=>{ if(iQ.value.trim().length>0){ iDays.value=Math.max(Number(iDays.value||1),30);} form.submit(); },400));
iDays.addEventListener('change', ()=>{ clampDays(); form.submit(); });
iDays.addEventListener('input', debounce(()=>{ clampDays(); form.submit(); }, 400));

/* ====== Utilidades de conteo/UI ====== */
function countInContainer(sel){ return document.querySelectorAll(sel + ' .row-item').length; }

function ensureEmptyMessage(containerId, msg){
  const cont = document.getElementById(containerId);
  if(!cont) return;
  const hasRows = cont.querySelector('.row-item');
  let empty = cont.querySelector('.al-empty');
  if(!hasRows){
    if(!empty){
      empty = document.createElement('div');
      empty.className = 'text-muted al-empty';
      empty.textContent = msg;
      cont.appendChild(empty);
    }
  }else if(empty){
    empty.remove();
  }
}

function updateDayCounts(day){
  const amId = `day-${day}-am`;
  const pmId = `day-${day}-pm`;
  const am = countInContainer('#'+amId);
  const pm = countInContainer('#'+pmId);
  const total = am + pm;

  // Actualiza ( X bloqueados )
  const badge = document.querySelector(`.js-day-count[data-day="${day}"]`);
  if(badge) badge.textContent = total;

  // Mensajes "Sin bloqueados..."
  ensureEmptyMessage(amId, 'Sin bloqueados en AM.');
  ensureEmptyMessage(pmId, 'Sin bloqueados en PM.');

  // Si no queda nada, quita todo el bloque del día
  if(total === 0){
    const wrap = document.getElementById(`wrap-${day}`);
    if(wrap){ wrap.remove(); }

    // Si ya no quedan días, muestra mensaje global
    const anyDay = document.querySelector('.mb-4[id^="wrap-"]');
    if(!anyDay){
      const root = document.getElementById('blockedRoot');
      if(root && !root.querySelector('.al-empty-global')){
        const msg = document.createElement('div');
        msg.className = 'text-muted al-empty al-empty-global';
        msg.textContent = 'No hay tours bloqueados en el rango seleccionado.';
        root.appendChild(msg);
      }
    }
  }
}

/* ====== Selección (Marcar/Desmarcar) ====== */
function getDayCheckboxes(day){ return document.querySelectorAll(`.row-item[data-day="${day}"] .select-item`); }
function getBlockCheckboxes(day,b){ return document.querySelectorAll(`#day-${day}-${b} .select-item`); }
function areAllChecked(list){ const a=[...list]; return a.length>0 && a.every(cb=>cb.checked); }
function setBtnLabel(btn, all){ btn.textContent = all ? 'Desmarcar todos' : 'Marcar todos'; }
function refreshMarkLabelsFor(day){
  const dayCbs=getDayCheckboxes(day); document.querySelectorAll(`.js-mark-day[data-day="${day}"]`).forEach(btn=>setBtnLabel(btn,areAllChecked(dayCbs)));
  ['am','pm'].forEach(b=>{ const c=getBlockCheckboxes(day,b); document.querySelectorAll(`.js-mark-block[data-day="${day}"][data-bucket="${b}"]`).forEach(btn=>setBtnLabel(btn,areAllChecked(c))); });
}
function toggleMarkDay(btn,day){ const c=getDayCheckboxes(day); const all=areAllChecked(c); c.forEach(cb=>cb.checked=!all); refreshMarkLabelsFor(day); toast.fire({icon:'info', title: (!all?'Marcados ':'Desmarcados ')+c.length}); }
function toggleMarkBlock(btn,day,b){ const c=getBlockCheckboxes(day,b); const all=areAllChecked(c); c.forEach(cb=>cb.checked=!all); refreshMarkLabelsFor(day); toast.fire({icon:'info', title: (!all?'Marcados ':'Desmarcados ')+c.length}); }
document.addEventListener('change',e=>{ if(!e.target.classList.contains('select-item')) return; const day=e.target.closest('.row-item')?.dataset.day; if(day) refreshMarkLabelsFor(day); });
document.addEventListener('DOMContentLoaded',()=>{ document.querySelectorAll('.js-mark-day').forEach(btn=>refreshMarkLabelsFor(btn.dataset.day)); });

/* ====== Acciones (unblock) ====== */
function collectSelected(){
  const sel=[]; document.querySelectorAll('.row-item').forEach(r=>{ const cb=r.querySelector('.select-item'); if(cb && cb.checked){ sel.push({ tour_id:r.dataset.tid, schedule_id:r.dataset.sid, date:r.dataset.day }); } }); return sel;
}

async function confirmToggleOne(el, day, tourId, scheduleId, want){
  const label = el.closest('.row-item')?.dataset.title || 'este tour';
  const res = await Swal.fire({
    icon: 'warning',
    title: '¿Desbloquear tour?',
    html: `Se desbloqueará <b>${label}</b> para la fecha <b>${day}</b>.`,
    showCancelButton: true,
    confirmButtonText: 'Sí, desbloquear',
    cancelButtonText: 'Cancelar'
  });
  if(res.isConfirmed){
    await toggleOne(el, day, tourId, scheduleId, want);
  }
}

async function toggleOne(el, day, tourId, scheduleId, want){
  const row = el.closest('.row-item');
  try{
    const res=await fetch(TOGGLE_URL,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({tour_id:tourId,schedule_id:scheduleId,date:day,want})});
    const data=await res.json(); if(!data.ok) throw new Error();

    // Si quedó disponible, sacamos la fila y actualizamos conteos
    if(data.is_available){
      row.remove();
      updateDayCounts(day);
      toast.fire({icon:'success', title:'Desbloqueado'});
    }else{
      // En teoría aquí no cae (en bloqueados sólo desbloqueamos), pero por si acaso:
      row.querySelector('.state').textContent = data.label;
      toast.fire({icon:'info', title:data.label});
    }
  }catch(e){
    console.error(e); Swal.fire('Error','No se pudo actualizar.','error');
  }
}

/* ====== Masivo: desbloquear seleccionados ====== */
document.getElementById('bulkUnblock').addEventListener('click', async ()=>{
  const items=collectSelected(); if(!items.length){ Swal.fire('Sin selección','Marca al menos un tour.','info'); return; }
  const res = await Swal.fire({
    icon: 'warning',
    title: 'Desbloquear seleccionados',
    html: `Se desbloquearán <b>${items.length}</b> ítems seleccionados.`,
    showCancelButton: true,
    confirmButtonText: 'Desbloquear',
    cancelButtonText: 'Cancelar'
  });
  if(res.isConfirmed) await bulkToggle(items,'unblock');
});

async function bulkToggle(items, want){
  try{
    const res=await fetch(BULK_URL,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({items,want})});
    const data=await res.json(); if(!data.ok) throw new Error();

    // Quitar filas afectadas
    const affectedDays = new Set();
    items.forEach(it=>{
      const sel = `.row-item[data-day="${it.date}"][data-tid="${it.tour_id}"][data-sid="${it.schedule_id}"]`;
      const row = document.querySelector(sel);
      if(row){ row.remove(); affectedDays.add(it.date); }
    });

    // Actualizar conteos por día
    affectedDays.forEach(day => updateDayCounts(day));

    Swal.fire({icon:'success',title:`Desbloqueados: ${data.changed}`,timer:1300,showConfirmButton:false});
  }catch(e){
    console.error(e); Swal.fire('Error','No se pudo completar la actualización.','error');
  }
}

/* ====== Helpers: desbloquear por día/bloque (con confirmación) ====== */
function gatherRows(rows){
  const items=[]; rows.forEach(r=>{ items.push({ tour_id:r.dataset.tid, schedule_id:r.dataset.sid, date:r.dataset.day }); });
  return items;
}
async function unblockAllInDay(day){
  const rows = document.querySelectorAll(`.row-item[data-day="${day}"]`);
  const items = gatherRows(rows);
  if(!items.length){ Swal.fire('Sin cambios','No hay ítems para desbloquear.','info'); return; }
  const res = await Swal.fire({
    icon:'warning', title:'Desbloquear todo el día',
    html:`Se desbloquearán <b>${items.length}</b> ítems del <b>${day}</b>.`,
    showCancelButton:true, confirmButtonText:'Desbloquear', cancelButtonText:'Cancelar'
  });
  if(res.isConfirmed) await bulkToggle(items,'unblock');
}
async function unblockAllInBlock(day,bucket){
  const rows = document.querySelectorAll(`#day-${day}-${bucket} .row-item`);
  const items = gatherRows(rows);
  if(!items.length){ Swal.fire('Sin cambios','No hay ítems para desbloquear.','info'); return; }
  const name = bucket.toUpperCase();
  const res = await Swal.fire({
    icon:'warning', title:'Desbloquear bloque',
    html:`Se desbloquearán <b>${items.length}</b> ítems del bloque <b>${name}</b> del <b>${day}</b>.`,
    showCancelButton:true, confirmButtonText:'Desbloquear', cancelButtonText:'Cancelar'
  });
  if(res.isConfirmed) await bulkToggle(items,'unblock');
}
</script>
@endpush
