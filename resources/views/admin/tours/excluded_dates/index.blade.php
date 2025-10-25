@extends('adminlte::page')

@section('title', __('m_bookings.availability.ui.page_title'))

@section('content_header')
  <h1><i class="fas fa-calendar-check me-2"></i> {{ __('m_bookings.availability.ui.page_heading') }}</h1>
@stop

@push('css')
<style>
  :root{
    --row-bg:#454d55;
    --title-separator:rgba(255,255,255,.08);
    --pad-x:.85rem;
    --gap:.5rem;
    --sticky-top:64px; /* lo ajusta el JS */
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
</style>
@endpush

@section('content')

  {{-- HEADER: Filtros (GET) a la izquierda, acciones masivas a la derecha --}}
  <div class="card-header bg-dark text-white d-flex flex-wrap align-items-end gap-2 sticky-filters">
    <form id="filtersForm" method="GET" action="{{ route('admin.tours.excluded_dates.index') }}" class="d-flex flex-wrap align-items-end gap-2 mb-0">
      <div>
        <label class="form-label mb-1">{{ __('m_bookings.availability.filters.date') }}</label>
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
        <label class="form-label mb-1">{{ __('m_bookings.availability.filters.days') }}</label>
        <input type="number" min="1" max="30" name="days" value="{{ $days }}" class="form-control form-control-sm" style="width:100px" id="filterDays">
      </div>
      <div>
        <label class="form-label mb-1">{{ __('m_bookings.availability.filters.product') }}</label>
        <input type="text" name="q" value="{{ $q }}" placeholder="{{ __('m_bookings.availability.filters.search_placeholder') }}" class="form-control form-control-sm search-input" id="filterQ">
      </div>
    </form>

    {{-- Acciones masivas + Ver bloqueados --}}
    <div class="ms-auto d-flex align-items-end gap-2">
      <div class="bulk-dd">
        <div class="btn-group">
          <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false">
            {{ __('m_bookings.availability.filters.update_state') }}
          </button>
          <div class="dropdown-menu dropdown-menu-right dropdown-menu-end p-2">
            <button type="button" class="btn btn-danger btn-sm w-100" id="bulkBlock">
              <i class="fas fa-ban me-1"></i> {{ __('m_bookings.availability.buttons.block_selected') }}
            </button>
            <button type="button" class="btn btn-success btn-sm w-100 mt-2" id="bulkUnblock">
              <i class="fas fa-check me-1"></i> {{ __('m_bookings.availability.buttons.unblock_selected') }}
            </button>
          </div>
        </div>
      </div>

      <a class="btn btn-warning btn-sm js-view-blocked ms-3"
         href="{{ route('admin.tours.excluded_dates.blocked', ['date' => $date, 'days' => $days, 'q' => $q]) }}">
        <i class="fas fa-lock me-1"></i> {{ __('m_bookings.availability.filters.view_blocked') }}
      </a>

      <div class="small text-muted d-none d-md-inline ms-2">
        {{ __('m_bookings.availability.filters.tip') }}
      </div>
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
              {{ __('m_bookings.availability.ui.tours_count', ['count' => count($buckets['am']) + count($buckets['pm'])]) }}
            </span>
          </div>
          <div class="ms-auto btn-gap">
            <button class="btn btn-primary btn-sm js-mark-day"
                    type="button"
                    data-day="{{ $day }}"
                    onclick="toggleMarkDay(this,'{{ $day }}')">
              {{ __('m_bookings.availability.buttons.mark_all') }}
            </button>
            <button class="btn btn-danger btn-sm" type="button" onclick="blockAllInDay('{{ $day }}')">
              {{ __('m_bookings.availability.buttons.block_all') }}
            </button>
          </div>
        </div>

        {{-- AM --}}
        <div class="al-title bg-dark al-block-title">
          <span class="fw-bold small mb-0">{{ __('m_bookings.availability.blocks.am_tours') }}</span>
          <div class="ms-auto btn-gap">
            <button type="button"
                    class="btn btn-primary btn-sm js-mark-block"
                    data-day="{{ $day }}" data-bucket="am"
                    onclick="toggleMarkBlock(this,'{{ $day }}','am')">
              {{ __('m_bookings.availability.buttons.mark_all') }}
            </button>
            <button type="button" class="btn btn-danger btn-sm" onclick="blockAllInBlock('{{ $day }}','am')">
              {{ __('m_bookings.availability.buttons.block_all') }}
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
                   data-title="{{ $it['tour_name'] }} ({{ $it['time'] }})">
                <input type="checkbox" class="form-check-input me-2 select-item">
                <div class="flex-grow-1">
                  <span class="me-2">{{ $it['tour_name'] }} ({{ $it['time'] }})</span>
                  <span class="state {{ $it['is_available'] ? 'text-success' : 'text-danger' }}">
                    {{ $it['is_available'] ? __('m_bookings.availability.states.available') : __('m_bookings.availability.states.blocked') }}
                  </span>
                </div>
                <div class="btn-gap">
                  <button type="button" class="btn btn-danger btn-sm btn-block"
                          onclick="confirmToggleOne(this, '{{ $day }}', {{ $it['tour_id'] }}, {{ $it['schedule_id'] }}, 'block')"
                          {{ !$it['is_available'] ? 'disabled' : '' }}>
                    {{ __('m_bookings.availability.buttons.block') }}
                  </button>
                  <button type="button" class="btn btn-success btn-sm btn-unblock"
                          onclick="confirmToggleOne(this, '{{ $day }}', {{ $it['tour_id'] }}, {{ $it['schedule_id'] }}, 'unblock')"
                          {{ $it['is_available'] ? 'disabled' : '' }}>
                    {{ __('m_bookings.availability.buttons.unblock') }}
                  </button>
                </div>
              </div>
            </div>
          @empty
            <div class="text-muted al-empty">{{ __('m_bookings.availability.blocks.empty_block') }}</div>
          @endforelse
        </div>

        {{-- PM --}}
        <div class="al-title bg-dark al-block-title mt-3">
          <span class="fw-bold small mb-0">{{ __('m_bookings.availability.blocks.pm_tours') }}</span>
          <div class="ms-auto btn-gap">
            <button type="button"
                    class="btn btn-primary btn-sm js-mark-block"
                    data-day="{{ $day }}" data-bucket="pm"
                    onclick="toggleMarkBlock(this,'{{ $day }}','pm')">
              {{ __('m_bookings.availability.buttons.mark_all') }}
            </button>
            <button type="button" class="btn btn-danger btn-sm" onclick="blockAllInBlock('{{ $day }}','pm')">
              {{ __('m_bookings.availability.buttons.block_all') }}
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
                   data-title="{{ $it['tour_name'] }} ({{ $it['time'] }})">
                <input type="checkbox" class="form-check-input me-2 select-item">
                <div class="flex-grow-1">
                  <span class="me-2">{{ $it['tour_name'] }} ({{ $it['time'] }})</span>
                  <span class="state {{ $it['is_available'] ? 'text-success' : 'text-danger' }}">
                    {{ $it['is_available'] ? __('m_bookings.availability.states.available') : __('m_bookings.availability.states.blocked') }}
                  </span>
                </div>
                <div class="btn-gap">
                  <button type="button" class="btn btn-danger btn-sm btn-block"
                          onclick="confirmToggleOne(this, '{{ $day }}', {{ $it['tour_id'] }}, {{ $it['schedule_id'] }}, 'block')"
                          {{ !$it['is_available'] ? 'disabled' : '' }}>
                    {{ __('m_bookings.availability.buttons.block') }}
                  </button>
                  <button type="button" class="btn btn-success btn-sm btn-unblock"
                          onclick="confirmToggleOne(this, '{{ $day }}', {{ $it['tour_id'] }}, {{ $it['schedule_id'] }}, 'unblock')"
                          {{ $it['is_available'] ? 'disabled' : '' }}>
                    {{ __('m_bookings.availability.buttons.unblock') }}
                  </button>
                </div>
              </div>
            </div>
          @empty
            <div class="text-muted al-empty">{{ __('m_bookings.availability.blocks.empty_block') }}</div>
          @endforelse
        </div>

      </div>
    @empty
      <div class="text-muted al-empty">{{ __('m_bookings.availability.blocks.no_data') }}</div>
    @endforelse

    <div id="tabla-baja" class="mt-4"></div>
  </div>
</div>

@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const TOGGLE_URL = @json(route('admin.tours.excluded_dates.toggle'));
const BULK_URL   = @json(route('admin.tours.excluded_dates.bulkToggle'));
const CSRF       = @json(csrf_token());

const I18N = {
  filters: {
    applying: @json(__('m_bookings.availability.toasts.applying_filters')),
    searching: @json(__('m_bookings.availability.toasts.searching')),
    updating: @json(__('m_bookings.availability.toasts.updating_range')),
    invalidTitle: @json(__('m_bookings.availability.toasts.invalid_date_title')),
    invalidText: @json(__('m_bookings.availability.toasts.invalid_date_text')),
  },
  buttons: {
    markAll: @json(__('m_bookings.availability.buttons.mark_all')),
    unmarkAll: @json(__('m_bookings.availability.buttons.unmark_all')),
    block: @json(__('m_bookings.availability.buttons.block')),
    unblock: @json(__('m_bookings.availability.buttons.unblock')),
  },
  states: {
    available: @json(__('m_bookings.availability.states.available')),
    blocked: @json(__('m_bookings.availability.states.blocked')),
  },
  confirm: {
    blockTitle: @json(__('m_bookings.availability.confirm.block_title')),
    blockHtml: @json(__('m_bookings.availability.confirm.block_html', ['label' => ':label', 'day' => ':day'])),
    blockBtn: @json(__('m_bookings.availability.confirm.block_btn')),
    unblockTitle: @json(__('m_bookings.availability.confirm.unblock_title')),
    unblockHtml: @json(__('m_bookings.availability.confirm.unblock_html', ['label' => ':label', 'day' => ':day'])),
    unblockBtn: @json(__('m_bookings.availability.confirm.unblock_btn')),
    bulkTitle: @json(__('m_bookings.availability.confirm.bulk_title')),
    bulkItemsHtml: @json(__('m_bookings.availability.confirm.bulk_items_html', ['count' => ':count'])),
    bulkBlockDayHtml: @json(__('m_bookings.availability.confirm.bulk_block_day_html', ['day' => ':day'])),
    bulkBlockBlockHtml: @json(__('m_bookings.availability.confirm.bulk_block_block_html', ['block' => ':block', 'day' => ':day'])),
  },
  toasts: {
    marked: @json(__('m_bookings.availability.toasts.marked_n', ['n' => ':n'])),
    unmarked: @json(__('m_bookings.availability.toasts.unmarked_n', ['n' => ':n'])),
    updated: @json(__('m_bookings.availability.toasts.updated')),
    updatedCount: @json(__('m_bookings.availability.toasts.updated_count', ['count' => ':count'])),
    noSelTitle: @json(__('m_bookings.availability.toasts.no_selection_title')),
    noSelText: @json(__('m_bookings.availability.toasts.no_selection_text')),
    noChangesTitle: @json(__('m_bookings.availability.toasts.no_changes_title')),
    noChangesText: @json(__('m_bookings.availability.toasts.no_changes_text')),
    errorGeneric: @json(__('m_bookings.availability.toasts.error_generic')),
    errorUpdate: @json(__('m_bookings.availability.toasts.error_update')),
  },
  blocks: {
    emptyBlock: @json(__('m_bookings.availability.blocks.empty_block')),
  }
};

const toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 1600,
  timerProgressBar: true,
  customClass: { popup: 'my-toast' }
});

/* ===== Header sticky ===== */
(function(){
  const root        = document.documentElement;
  const mainHeader  = document.querySelector('.main-header');
  const contentHdr  = document.querySelector('.content-header');
  const stickyBar   = document.querySelector('.sticky-filters');

  let Hm = 0, idleTop = 0, tightTop = 0;

  function recalc() {
    Hm       = mainHeader?.offsetHeight || 0;
    const Hc = contentHdr?.offsetHeight || 0;
    idleTop  = Hm + Hc + 8;
    tightTop = Hm;
    apply();
  }
  function apply() {
    const chBottom = contentHdr ? contentHdr.getBoundingClientRect().bottom : 0;
    const wantTop  = chBottom <= Hm ? tightTop : idleTop;
    root.style.setProperty('--sticky-top', wantTop + 'px');
    if (stickyBar){ stickyBar.classList.toggle('is-stuck', wantTop === tightTop); }
  }

  recalc();
  window.addEventListener('scroll', apply,  { passive: true });
  window.addEventListener('resize', recalc, { passive: true });
  window.addEventListener('load',   recalc);
})();

/* ================== FILTROS ================== */
const form   = document.getElementById('filtersForm');
const iDate  = document.getElementById('filterDate');
const iDays  = document.getElementById('filterDays');
const iQ     = document.getElementById('filterQ');

function todayStr(){
  const now = new Date();
  const tzOff = now.getTimezoneOffset()*60000;
  return new Date(Date.now()-tzOff).toISOString().slice(0,10);
}
if(iDate){
  const t = todayStr();
  if(!iDate.min) iDate.min = t;
  if(iDate.value && iDate.value < t){
    Swal.fire(I18N.filters.invalidTitle, I18N.filters.invalidText, 'info');
    iDate.value = t;
  }
}
function clampDays(){
  let v = parseInt(iDays.value || '1', 10);
  if (isNaN(v) || v < 1) v = 1;
  if (v > 30) v = 30;
  iDays.value = v;
}

iDate.addEventListener('change', () => {
  const t = todayStr();
  if(iDate.value < t){
    Swal.fire(I18N.filters.invalidTitle, I18N.filters.invalidText, 'info');
    iDate.value = t;
    return;
  }
  iDays.value = 1;
  iQ.value = '';
  toast.fire({icon:'info', title:I18N.filters.applying});
  form.submit();
});

function debounce(fn, ms){ let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), ms); }; }
iQ.addEventListener('input', debounce(() => {
  if(iQ.value.trim().length > 0){
    iDays.value = Math.max(Number(iDays.value||1), 30);
  }
  toast.fire({icon:'info', title:I18N.filters.searching});
  form.submit();
}, 400));

iDays.addEventListener('change', () => { clampDays(); toast.fire({icon:'info', title:I18N.filters.updating}); form.submit(); });
iDays.addEventListener('input', debounce(() => { clampDays(); toast.fire({icon:'info', title:I18N.filters.updating}); form.submit(); }, 400));

/* Abrir “Ver bloqueados” con confirmación */
document.querySelector('.js-view-blocked')?.addEventListener('click', async (e) => {
  e.preventDefault();
  const url = e.currentTarget.href;
  const res = await Swal.fire({
    icon: 'warning',
    title: @json(__('m_bookings.availability.confirm.view_blocked_title')),
    text:  @json(__('m_bookings.availability.confirm.view_blocked_text')),
    showCancelButton: true,
    confirmButtonText: @json(__('m_bookings.availability.buttons.open')),
    cancelButtonText:  @json(__('m_bookings.availability.buttons.cancel'))
  });
  if(res.isConfirmed){ window.location.href = url; }
});

/* ================== ACCIONES POR FILA ================== */
async function toggleOne(el, day, tourId, scheduleId, want){
  const row = el.closest('.row-item');
  const btnBlock  = row.querySelector('.btn-block');
  const btnUnblock= row.querySelector('.btn-unblock');

  btnBlock.disabled = true; btnUnblock.disabled = true;

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
    state.textContent = available ? I18N.states.available : I18N.states.blocked;
    state.classList.toggle('text-success', available);
    state.classList.toggle('text-danger', !available);

    btnBlock.disabled   = !available ? true : false;
    btnUnblock.disabled = available ? true : false;

    toast.fire({icon:'success', title: available ? I18N.states.available : I18N.states.blocked });
  }catch(e){
    console.error(e);
    Swal.fire('Error', I18N.toasts.errorUpdate, 'error');
  }finally{
    const state = row.querySelector('.state');
    btnBlock.disabled   = state.classList.contains('text-danger');
    btnUnblock.disabled = state.classList.contains('text-success');
  }
}

/* Confirmación para Block/Unblock individual */
async function confirmToggleOne(el, day, tourId, scheduleId, want){
  const row = el.closest('.row-item');
  const label = row?.dataset.title || '—';
  const isBlock = want === 'block';
  const res = await Swal.fire({
    icon: 'warning',
    title: isBlock ? I18N.confirm.blockTitle : I18N.confirm.unblockTitle,
    html: (isBlock ? I18N.confirm.blockHtml : I18N.confirm.unblockHtml)
      .replace(':label', label)
      .replace(':day', day),
    showCancelButton: true,
    confirmButtonText: isBlock ? I18N.confirm.blockBtn : I18N.confirm.unblockBtn,
    cancelButtonText:  @json(__('m_bookings.availability.buttons.cancel'))
  });
  if(res.isConfirmed){
    await toggleOne(el, day, tourId, scheduleId, want);
  }
}

/* ================== ACCIONES MASIVAS ================== */
function collectSelected(){
  const sel = [];
  document.querySelectorAll('.row-item').forEach(r => {
    const cb = r.querySelector('.select-item');
    if(cb && cb.checked){
      sel.push({ tour_id:r.dataset.tid, schedule_id:r.dataset.sid, date:r.dataset.day, _label:r.dataset.title });
    }
  });
  return sel;
}

document.getElementById('bulkBlock').addEventListener('click', async () => {
  const items = collectSelected();
  if(!items.length){ Swal.fire(I18N.toasts.noSelTitle, I18N.toasts.noSelText, 'info'); return; }
  const res = await Swal.fire({
    icon: 'warning',
    title: I18N.confirm.bulkTitle,
    html: I18N.confirm.bulkItemsHtml.replace(':count', items.length),
    showCancelButton: true,
    confirmButtonText: @json(__('m_bookings.availability.buttons.block')),
    cancelButtonText:  @json(__('m_bookings.availability.buttons.cancel'))
  });
  if(res.isConfirmed) await bulkToggle(items, 'block');
});

document.getElementById('bulkUnblock').addEventListener('click', async () => {
  const items = collectSelected();
  if(!items.length){ Swal.fire(I18N.toasts.noSelTitle, I18N.toasts.noSelText, 'info'); return; }
  const res = await Swal.fire({
    icon: 'warning',
    title: I18N.confirm.bulkTitle,
    html: I18N.confirm.bulkItemsHtml.replace(':count', items.length),
    showCancelButton: true,
    confirmButtonText: @json(__('m_bookings.availability.buttons.unblock')),
    cancelButtonText:  @json(__('m_bookings.availability.buttons.cancel'))
  });
  if(res.isConfirmed) await bulkToggle(items, 'unblock');
});

function itemsFromRows(rows, want){
  const items = [];
  rows.forEach(r => {
    const isAvailable = r.querySelector('.state').classList.contains('text-success');
    if(want === 'block'   && isAvailable)   items.push({ tour_id:r.dataset.tid, schedule_id:r.dataset.sid, date:r.dataset.day, _label:r.dataset.title });
    if(want === 'unblock' && !isAvailable)  items.push({ tour_id:r.dataset.tid, schedule_id:r.dataset.sid, date:r.dataset.day, _label:r.dataset.title });
  });
  return items;
}
async function blockAllInDay(day){
  const rows = document.querySelectorAll(`.row-item[data-day="${day}"]`);
  await confirmBulkFromRows(rows, 'block', I18N.confirm.bulkBlockDayHtml.replace(':day', day));
}
async function blockAllInBlock(day, bucket){
  const rows = document.querySelectorAll(`#day-${day}-${bucket} .row-item`);
  const blockName = bucket.toUpperCase();
  await confirmBulkFromRows(rows, 'block', I18N.confirm.bulkBlockBlockHtml.replace(':block', blockName).replace(':day', day));
}
async function confirmBulkFromRows(rows, want, titleHtml){
  const items = itemsFromRows(rows, want);
  if(!items.length){ Swal.fire(I18N.toasts.noChangesTitle, I18N.toasts.noChangesText, 'info'); return; }
  const res = await Swal.fire({
    icon: 'warning',
    title: I18N.confirm.bulkTitle,
    html: `${titleHtml}<br>${I18N.confirm.bulkItemsHtml.replace(':count', items.length)}`,
    showCancelButton: true,
    confirmButtonText: want === 'block' ? @json(__('m_bookings.availability.buttons.block')) : @json(__('m_bookings.availability.buttons.unblock')),
    cancelButtonText:  @json(__('m_bookings.availability.buttons.cancel'))
  });
  if(res.isConfirmed){ await bulkToggle(items, want); }
}

/* === Toggle “Marcar / Desmarcar” === */
function getDayCheckboxes(day){ return document.querySelectorAll(`.row-item[data-day="${day}"] .select-item`); }
function getBlockCheckboxes(day, bucket){ return document.querySelectorAll(`#day-${day}-${bucket} .select-item`); }
function areAllChecked(list){ const arr = Array.from(list); return arr.length > 0 && arr.every(cb => cb.checked); }
function setBtnLabel(btn, allChecked){ btn.textContent = allChecked ? I18N.buttons.unmarkAll : I18N.buttons.markAll; }
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
  toast.fire({ icon:'info', title: (!all ? I18N.toasts.marked : I18N.toasts.unmarked).replace(':n', cbs.length) });
}
function toggleMarkBlock(btn, day, bucket){
  const cbs = getBlockCheckboxes(day, bucket);
  const all = areAllChecked(cbs);
  cbs.forEach(cb => cb.checked = !all);
  refreshMarkLabelsFor(day);
  toast.fire({ icon:'info', title: (!all ? I18N.toasts.marked : I18N.toasts.unmarked).replace(':n', cbs.length) });
}
document.addEventListener('change', (e) => {
  if(!e.target.classList.contains('select-item')) return;
  const day = e.target.closest('.row-item')?.dataset.day;
  if(day) refreshMarkLabelsFor(day);
});
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.js-mark-day').forEach(btn => refreshMarkLabelsFor(btn.dataset.day));
});

/* ============ Bulk toggle (desmarca todo al terminar) ============ */
async function bulkToggle(items, want){
  try{
    const res = await fetch(BULK_URL, {
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
      body: JSON.stringify({ items, want })
    });
    const data = await res.json();
    if(!data.ok) throw new Error();

    // Actualizar UI local
    for(const it of items){
      const row = document.querySelector(`.row-item[data-day="${it.date}"][data-tid="${it.tour_id}"][data-sid="${it.schedule_id}"]`);
      if(!row) continue;
      const state = row.querySelector('.state');
      const btnBlock   = row.querySelector('.btn-block');
      const btnUnblock = row.querySelector('.btn-unblock');

      const available = (want === 'unblock');
      state.textContent = available ? I18N.states.available : I18N.states.blocked;
      state.classList.toggle('text-success', available);
      state.classList.toggle('text-danger', !available);
      btnBlock.disabled   = !available ? true : false;
      btnUnblock.disabled = available ? true : false;
    }

    // Desmarcar selección y refrescar etiquetas
    document.querySelectorAll('.select-item:checked').forEach(cb => cb.checked = false);
    const affectedDays = [...new Set(items.map(it => it.date))];
    affectedDays.forEach(day => refreshMarkLabelsFor(day));

    Swal.fire({
      icon:'success',
      title:I18N.toasts.updated,
      html:I18N.toasts.updatedCount.replace(':count', data.changed),
      timer:1300,
      showConfirmButton:false
    });
  }catch(e){
    console.error(e);
    Swal.fire('Error', I18N.toasts.errorGeneric, 'error');
  }
}
</script>
@endpush
