@extends('adminlte::page')

@section('title', __('m_bookings.excluded_dates.ui.blocked_page_title'))

@section('content_header')
  <h1><i class="fas fa-lock me-2"></i> {{ __('m_bookings.excluded_dates.ui.blocked_page_heading') }}</h1>
@stop

@push('css')
<style>
  :root{
    --row-bg:#454d55;
    --title-separator:rgba(255,255,255,.08);
    --pad-x:.85rem;
    --gap:.5rem;
    --sticky-offset: 64px;
    --fs-base: clamp(.82rem, 1.6vw, .95rem);
    --fs-small: clamp(.74rem, 1.4vw, .9rem);
  }

  .al-title{ color:#fff; padding:.55rem var(--pad-x); border-radius:.25rem; }
  .al-day-header{ display:flex; align-items:center; gap:var(--gap); border:1px solid var(--title-separator); }
  .al-block-title{
    display:flex; align-items:center; gap:var(--gap);
    padding:.5rem var(--pad-x); margin-top:.85rem;
    border:1px solid var(--title-separator); border-radius:.25rem;
  }
  .btn-gap{ display:flex; align-items:center; gap:var(--gap); flex-wrap: wrap; }
  .list-group-flush{ margin:0; }
  .list-group-flush .list-group-item{ background:transparent; border:0; margin-bottom:.35rem; padding:0; }
  .row-item{ display:flex; align-items:center; gap:.75rem; background:var(--row-bg); padding:.6rem var(--pad-x); border-radius:.25rem; font-size:var(--fs-base); }
  .row-item .state{ font-weight:700; }
  .row-item .form-check-input{ margin:0 .5rem 0 0 !important; width:18px; height:18px; flex:0 0 18px; position:relative; top:0; }
  .al-empty{ padding:.5rem var(--pad-x); font-size: var(--fs-small); }
  .search-input{ min-width:260px }
  .bulk-dd .dropdown-menu{ min-width: 240px; }
  .sticky-filters{ position: sticky; top: var(--sticky-offset); z-index: 1041; background:#343a40!important; border-bottom:1px solid var(--title-separator); }

  @media (max-width: 400px){
    .search-input{ min-width: 180px; }
    .row-item{ gap:.5rem; padding:.5rem .6rem; flex-wrap: wrap; }
    .row-item > .flex-grow-1{ min-width: 100%; }
  }
  @media (max-width: 360px){
    .search-input{ min-width: 150px; }
    .list-group-flush .list-group-item{ margin-bottom:.25rem; }
  }
</style>
@endpush

@section('content')

  {{-- HEADER: filtros + desbloquear + volver --}}
  <div class="card-header bg-dark text-white d-flex flex-wrap align-items-end gap-2 sticky-filters">
    <form id="filtersForm" method="GET" action="{{ route('admin.products.excluded_dates.blocked') }}" class="d-flex flex-wrap align-items-end gap-2 mb-0">
      <div>
        <label class="form-label mb-1">{{ __('m_bookings.excluded_dates.filters.date') }}</label>
        <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm" id="filterDate"
               min="{{ \Carbon\Carbon::today(config('app.timezone','America/Costa_Rica'))->toDateString() }}">
      </div>
      <div>
        <label class="form-label mb-1">{{ __('m_bookings.excluded_dates.filters.days') }}</label>
        <input type="number" min="1" max="30" name="days" value="{{ $days }}" class="form-control form-control-sm" style="width:100px" id="filterDays">
      </div>
      <div>
        <label class="form-label mb-1">{{ __('m_bookings.excluded_dates.filters.product') }}</label>
        <input type="text" name="q" value="{{ $q }}" placeholder="{{ __('m_bookings.excluded_dates.filters.search_placeholder') }}" class="form-control form-control-sm search-input" id="filterQ">
      </div>
    </form>

    <div class="ms-auto d-flex align-items-end gap-2">
      <div class="bulk-dd">
        <div class="btn-group">
          <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" data-toggle="dropdown" aria-expanded="false">
            {{ __('m_bookings.excluded_dates.filters.update_state') }}
          </button>
          <div class="dropdown-menu dropdown-menu-right dropdown-menu-end p-2">
            <button type="button" class="btn btn-success btn-sm w-100" id="bulkUnblock">
              <i class="fas fa-check me-1"></i> {{ __('m_bookings.excluded_dates.buttons.unblock_selected') }}
            </button>
          </div>
        </div>
      </div>

      <a class="btn btn-outline-light btn-sm" href="{{ route('admin.products.excluded_dates.index', ['date'=>$date,'days'=>$days,'q'=>$q]) }}">
        <i class="fas fa-arrow-left me-1"></i> {{ __('m_bookings.excluded_dates.buttons.back') }}
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
              {{ __('m_bookings.excluded_dates.ui.blocked_count', ['count' => count($buckets['am']) + count($buckets['pm'])]) }}
            </span>
          </div>
          <div class="ms-auto btn-gap">
            <button class="btn btn-primary btn-sm js-mark-day"
                    type="button"
                    data-day="{{ $day }}"
                    onclick="toggleMarkDay(this,'{{ $day }}')">
              {{ __('m_bookings.excluded_dates.buttons.mark_all') }}
            </button>
            <button class="btn btn-success btn-sm" type="button" onclick="unblockAllInDay('{{ $day }}')">
              {{ __('m_bookings.excluded_dates.buttons.unblock_all') }}
            </button>
          </div>
        </div>

        <div class="al-title bg-dark al-block-title">
          <span class="fw-bold small mb-0">{{ __('m_bookings.excluded_dates.blocks.am_blocked') }}</span>
          <div class="ms-auto btn-gap">
            <button type="button" class="btn btn-primary btn-sm js-mark-block" data-day="{{ $day }}" data-bucket="am" onclick="toggleMarkBlock(this,'{{ $day }}','am')">
              {{ __('m_bookings.excluded_dates.buttons.mark_all') }}
            </button>
            <button type="button" class="btn btn-success btn-sm" onclick="unblockAllInBlock('{{ $day }}','am')">
              {{ __('m_bookings.excluded_dates.buttons.unblock_all') }}
            </button>
          </div>
        </div>

        <div id="day-{{ $day }}-am" class="list-group list-group-flush mt-2">
          @forelse($buckets['am'] as $it)
            <div class="list-group-item">
              <div class="row-item"
                   data-day="{{ $day }}"
                   data-tid="{{ $it['product_id'] }}"
                   data-sid="{{ $it['schedule_id'] }}"
                   data-title="{{ $it['tour_name'] }} ({{ $it['time'] }})">
                <input type="checkbox" class="form-check-input me-2 select-item">
                <div class="flex-grow-1">
                  <span class="me-2">{{ $it['tour_name'] }} ({{ $it['time'] }})</span>
                  <span class="state text-danger">{{ __('m_bookings.excluded_dates.states.blocked') }}</span>
                </div>
                <div class="btn-gap">
                  <button type="button" class="btn btn-success btn-sm btn-unblock"
                          onclick="confirmToggleOne(this, '{{ $day }}', {{ $it['product_id'] }}, {{ $it['schedule_id'] }}, 'unblock')">
                    {{ __('m_bookings.excluded_dates.buttons.unblock') }}
                  </button>
                </div>
              </div>
            </div>
          @empty
            <div class="text-muted al-empty">{{ __('m_bookings.excluded_dates.blocks.empty_am') }}</div>
          @endforelse
        </div>

        <div class="al-title bg-dark al-block-title mt-3">
          <span class="fw-bold small mb-0">{{ __('m_bookings.excluded_dates.blocks.pm_blocked') }}</span>
          <div class="ms-auto btn-gap">
            <button type="button" class="btn btn-primary btn-sm js-mark-block" data-day="{{ $day }}" data-bucket="pm" onclick="toggleMarkBlock(this,'{{ $day }}','pm')">
              {{ __('m_bookings.excluded_dates.buttons.mark_all') }}
            </button>
            <button type="button" class="btn btn-success btn-sm" onclick="unblockAllInBlock('{{ $day }}','pm')">
              {{ __('m_bookings.excluded_dates.buttons.unblock_all') }}
            </button>
          </div>
        </div>

        <div id="day-{{ $day }}-pm" class="list-group list-group-flush mt-2">
          @forelse($buckets['pm'] as $it)
            <div class="list-group-item">
              <div class="row-item"
                   data-day="{{ $day }}"
                   data-tid="{{ $it['product_id'] }}"
                   data-sid="{{ $it['schedule_id'] }}"
                   data-title="{{ $it['tour_name'] }} ({{ $it['time'] }})">
                <input type="checkbox" class="form-check-input me-2 select-item">
                <div class="flex-grow-1">
                  <span class="me-2">{{ $it['tour_name'] }} ({{ $it['time'] }})</span>
                  <span class="state text-danger">{{ __('m_bookings.excluded_dates.states.blocked') }}</span>
                </div>
                <div class="btn-gap">
                  <button type="button" class="btn btn-success btn-sm btn-unblock"
                          onclick="confirmToggleOne(this, '{{ $day }}', {{ $it['product_id'] }}, {{ $it['schedule_id'] }}, 'unblock')">
                    {{ __('m_bookings.excluded_dates.buttons.unblock') }}
                  </button>
                </div>
              </div>
            </div>
          @empty
            <div class="text-muted al-empty">{{ __('m_bookings.excluded_dates.blocks.empty_pm') }}</div>
          @endforelse
        </div>

      </div>
    @empty
      <div class="text-muted al-empty">{{ __('m_bookings.excluded_dates.blocks.no_blocked') }}</div>
    @endforelse

  </div>
@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const TOGGLE_URL = @json(route('admin.products.excluded_dates.toggle'));
const BULK_URL   = @json(route('admin.products.excluded_dates.bulkToggle'));
const CSRF       = @json(csrf_token());

const I18N = {
  filters: {
    invalidTitle: @json(__('m_bookings.excluded_dates.toasts.invalid_date_title')),
    invalidText:  @json(__('m_bookings.excluded_dates.toasts.invalid_date_text')),
  },
  buttons: {
    markAll:     @json(__('m_bookings.excluded_dates.buttons.mark_all')),
    unmarkAll:   @json(__('m_bookings.excluded_dates.buttons.unmark_all')),
    unblock:     @json(__('m_bookings.excluded_dates.buttons.unblock')),
    cancel:      @json(__('m_bookings.excluded_dates.buttons.cancel')),
  },
  states: {
    available:   @json(__('m_bookings.excluded_dates.states.available')),
    blocked:     @json(__('m_bookings.excluded_dates.states.blocked')),
  },
  confirm: {
    unblockTitle: @json(__('m_bookings.excluded_dates.confirm.unblock_title')),
    unblockHtml:  @json(__('m_bookings.excluded_dates.confirm.unblock_html', ['label' => ':label', 'day' => ':day'])),
    unblockBtn:   @json(__('m_bookings.excluded_dates.confirm.unblock_btn')),
    bulkTitle:    @json(__('m_bookings.excluded_dates.confirm.bulk_title')),
    bulkItemsHtml:@json(__('m_bookings.excluded_dates.confirm.bulk_items_html', ['count' => ':count'])),
  },
  toasts: {
    noSelTitle:   @json(__('m_bookings.excluded_dates.toasts.no_selection_title')),
    noSelText:    @json(__('m_bookings.excluded_dates.toasts.no_selection_text')),
    noChangesTitle:@json(__('m_bookings.excluded_dates.toasts.no_changes_title')),
    noChangesText: @json(__('m_bookings.excluded_dates.toasts.no_changes_text')),
    errorGeneric:  @json(__('m_bookings.excluded_dates.toasts.error_generic')),
    updatedCount:  @json(__('m_bookings.excluded_dates.toasts.unblocked_count', ['count' => ':count'])),
  },
  blocks: {
    emptyAM:   @json(__('m_bookings.excluded_dates.blocks.empty_am')),
    emptyPM:   @json(__('m_bookings.excluded_dates.blocks.empty_pm')),
    noBlocked: @json(__('m_bookings.excluded_dates.blocks.no_blocked')),
  }
};

const toast = Swal.mixin({
  toast: true, position: 'top-end', showConfirmButton: false, timer: 1600, timerProgressBar: true
});

/* Sticky offset */
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
if(iDate){ const t=todayStr(); if(!iDate.min) iDate.min=t; if(iDate.value && iDate.value < t){ Swal.fire(I18N.filters.invalidTitle, I18N.filters.invalidText, 'info'); iDate.value=t; } }
function clampDays(){ let v=parseInt(iDays.value||'1',10); if(isNaN(v)||v<1)v=1; if(v>30)v=30; iDays.value=v; }
iDate.addEventListener('change', ()=>{ const t=todayStr(); if(iDate.value<t){ Swal.fire(I18N.filters.invalidTitle, I18N.filters.invalidText, 'info'); iDate.value=t; } iDays.value=1; iQ.value=''; form.submit(); });
function debounce(fn,ms){ let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a),ms); }; }
iQ.addEventListener('input', debounce(()=>{ if(iQ.value.trim().length>0){ iDays.value=Math.max(Number(iDays.value||1),30);} form.submit(); },400));
iDays.addEventListener('change', ()=>{ clampDays(); form.submit(); });
iDays.addEventListener('input', debounce(()=>{ clampDays(); form.submit(); }, 400));

/* ===== Selección ===== */
function getDayCheckboxes(day){ return document.querySelectorAll(`.row-item[data-day="${day}"] .select-item`); }
function getBlockCheckboxes(day,b){ return document.querySelectorAll(`#day-${day}-${b} .select-item`); }
function areAllChecked(list){ const a=[...list]; return a.length>0 && a.every(cb=>cb.checked); }
function setBtnLabel(btn, all){ btn.textContent = all ? I18N.buttons.unmarkAll : I18N.buttons.markAll; }
function refreshMarkLabelsFor(day){
  const dayCbs=getDayCheckboxes(day); document.querySelectorAll(`.js-mark-day[data-day="${day}"]`).forEach(btn=>setBtnLabel(btn,areAllChecked(dayCbs)));
  ['am','pm'].forEach(b=>{ const c=getBlockCheckboxes(day,b); document.querySelectorAll(`.js-mark-block[data-day="${day}"][data-bucket="${b}"]`).forEach(btn=>setBtnLabel(btn,areAllChecked(c))); });
}
function toggleMarkDay(btn,day){ const c=getDayCheckboxes(day); const all=areAllChecked(c); c.forEach(cb=>cb.checked=!all); refreshMarkLabelsFor(day); toast.fire({icon:'info', title: (!all?@json(__('m_bookings.excluded_dates.toasts.marked_n',['n'=>':n'])):@json(__('m_bookings.excluded_dates.toasts.unmarked_n',['n'=>':n']))).replace(':n', c.length)}); }
function toggleMarkBlock(btn,day,b){ const c=getBlockCheckboxes(day,b); const all=areAllChecked(c); c.forEach(cb=>cb.checked=!all); refreshMarkLabelsFor(day); toast.fire({icon:'info', title: (!all?@json(__('m_bookings.excluded_dates.toasts.marked_n',['n'=>':n'])):@json(__('m_bookings.excluded_dates.toasts.unmarked_n',['n'=>':n']))).replace(':n', c.length)}); }
document.addEventListener('change',e=>{ if(!e.target.classList.contains('select-item')) return; const day=e.target.closest('.row-item')?.dataset.day; if(day) refreshMarkLabelsFor(day); });
document.addEventListener('DOMContentLoaded',()=>{ document.querySelectorAll('.js-mark-day').forEach(btn=>refreshMarkLabelsFor(btn.dataset.day)); });

/* ===== Acciones (unblock) ===== */
function collectSelected(){
  const sel=[]; document.querySelectorAll('.row-item').forEach(r=>{ const cb=r.querySelector('.select-item'); if(cb && cb.checked){ sel.push({ product_id:r.dataset.tid, schedule_id:r.dataset.sid, date:r.dataset.day }); } }); return sel;
}

async function confirmToggleOne(el, day, productId, scheduleId, want){
  const label = el.closest('.row-item')?.dataset.title || '—';
  const res = await Swal.fire({
    icon: 'warning',
    title: I18N.confirm.unblockTitle,
    html: I18N.confirm.unblockHtml.replace(':label', label).replace(':day', day),
    showCancelButton: true,
    confirmButtonText: I18N.confirm.unblockBtn,
    cancelButtonText:  I18N.buttons.cancel
  });
  if(res.isConfirmed){ await toggleOne(el, day, productId, scheduleId, want); }
}

async function toggleOne(el, day, productId, scheduleId, want){
  const row = el.closest('.row-item');
  try{
    const res=await fetch(TOGGLE_URL,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({product_id:productId,schedule_id:scheduleId,date:day,want})});
    const data=await res.json(); if(!data.ok) throw new Error();
    if(data.is_available){
      row.remove();
      updateDayCounts(day);
      toast.fire({icon:'success', title: I18N.states.available});
    }else{
      row.querySelector('.state').textContent = I18N.states.blocked;
      toast.fire({icon:'info', title: I18N.states.blocked});
    }
  }catch(e){
    console.error(e); Swal.fire('Error', I18N.toasts.errorGeneric, 'error');
  }
}

/* ===== Masivo: desbloquear ===== */
document.getElementById('bulkUnblock').addEventListener('click', async ()=>{
  const items=collectSelected(); if(!items.length){ Swal.fire(I18N.toasts.noSelTitle, I18N.toasts.noSelText, 'info'); return; }
  const res = await Swal.fire({
    icon: 'warning',
    title: I18N.confirm.bulkTitle,
    html: I18N.confirm.bulkItemsHtml.replace(':count', items.length),
    showCancelButton: true,
    confirmButtonText: @json(__('m_bookings.excluded_dates.buttons.unblock')),
    cancelButtonText:  @json(__('m_bookings.excluded_dates.buttons.cancel'))
  });
  if(res.isConfirmed) await bulkToggle(items,'unblock');
});

async function bulkToggle(items, want){
  try{
    const res=await fetch(BULK_URL,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({items,want})});
    const data=await res.json(); if(!data.ok) throw new Error();

    const affectedDays = new Set();
    items.forEach(it=>{
      const sel = `.row-item[data-day="${it.date}"][data-tid="${it.product_id}"][data-sid="${it.schedule_id}"]`;
      const row = document.querySelector(sel);
      if(row){ row.remove(); affectedDays.add(it.date); }
    });

    affectedDays.forEach(day => updateDayCounts(day));

    Swal.fire({icon:'success',title:@json(__('m_bookings.excluded_dates.toasts.updated')),html:I18N.toasts.updatedCount.replace(':count', data.changed),timer:1300,showConfirmButton:false});
  }catch(e){
    console.error(e); Swal.fire('Error', I18N.toasts.errorGeneric, 'error');
  }
}

/* ===== Helpers ===== */
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
  }else if(empty){ empty.remove(); }
}
function updateDayCounts(day){
  const amId = `day-${day}-am`, pmId = `day-${day}-pm`;
  const am = countInContainer('#'+amId), pm = countInContainer('#'+pmId);
  ensureEmptyMessage(amId, @json(__('m_bookings.excluded_dates.blocks.empty_am')));
  ensureEmptyMessage(pmId, @json(__('m_bookings.excluded_dates.blocks.empty_pm')));

  if(am+pm === 0){
    const wrap = document.getElementById(`wrap-${day}`);
    if(wrap){ wrap.remove(); }
    const anyDay = document.querySelector('.mb-4[id^="wrap-"]');
    if(!anyDay){
      const root = document.getElementById('blockedRoot');
      if(root && !root.querySelector('.al-empty-global')){
        const msg = document.createElement('div');
        msg.className = 'text-muted al-empty al-empty-global';
        msg.textContent = @json(__('m_bookings.excluded_dates.blocks.no_blocked'));
        root.appendChild(msg);
      }
    }
  }
}
</script>
@endpush
