{{-- resources/views/admin/dashboard.blade.php --}}
@extends('adminlte::page')

@section('title', __('adminlte::adminlte.dashboard.title'))

@section('content_header')
  <div class="mb-4">
    <div class="card shadow-sm border-0">
      <div class="card-body text-center">
        <h3 class="mb-2">
          {{ __('adminlte::adminlte.dashboard.greeting', ['name' => Auth::user()->full_name]) }}
        </h3>
        <p class="mb-0">
          {{ __('adminlte::adminlte.dashboard.welcome_to', ['app' => 'Green Vacations CR']) }}
          {{ __('adminlte::adminlte.dashboard.hint') }}
        </p>
      </div>
    </div>
  </div>
@stop

@section('content')

  {{-- ======= Tarjetas KPI ======= --}}
  <div class="row">
    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="{{ __('adminlte::adminlte.entities.users') }}" text="{{ $totalUsers ?? 0 }}" icon="fas fa-users" theme="info" />
      <a href="{{ route('admin.users.index') }}" class="btn btn-info btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} {{ __('adminlte::adminlte.entities.users') }}
      </a>
    </div>

    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="{{ __('adminlte::adminlte.entities.tours') }}" text="{{ $totalTours ?? 0 }}" icon="fas fa-map" theme="warning" />
      <a href="{{ route('admin.tours.index') }}" class="btn btn-warning btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} {{ __('adminlte::adminlte.entities.tours') }}
      </a>
    </div>

    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="{{ __('adminlte::adminlte.entities.tour_types') }}" text="{{ $totalTourTypes ?? ($tourTypes ?? 0) }}" icon="fas fa-tags" theme="success" />
      <a href="{{ route('admin.tourtypes.index') }}" class="btn btn-success btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} {{ __('adminlte::adminlte.entities.tour_types') }}
      </a>
    </div>

    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="{{ __('adminlte::adminlte.entities.languages') }}" text="{{ $totalLanguages ?? 0 }}" icon="fas fa-globe" theme="primary" />
      <a href="{{ route('admin.languages.index') }}" class="btn btn-primary btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} {{ __('adminlte::adminlte.entities.languages') }}
      </a>
    </div>

    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="{{ __('adminlte::adminlte.entities.schedules') }}" text="{{ $totalSchedules ?? 0 }}" icon="fas fa-clock" theme="dark" />
      <a href="{{ route('admin.tours.schedule.index') }}" class="btn btn-dark btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} {{ __('adminlte::adminlte.entities.schedules') }}
      </a>
    </div>

    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="Amenidades" text="{{ $totalAmenities ?? 0 }}" icon="fas fa-concierge-bell" theme="secondary" />
      <a href="{{ route('admin.tours.amenities.index') }}" class="btn btn-secondary btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} Amenidades
      </a>
    </div>

    <div class="col-md-4 mb-3">
      <x-adminlte-info-box title="{{ __('adminlte::adminlte.entities.total_bookings') }}" text="{{ $totalBookings ?? 0 }}" icon="fas fa-calendar-check" theme="success" />
      <a href="{{ route('admin.bookings.index') }}" class="btn btn-success btn-block mt-2">
        {{ __('adminlte::adminlte.buttons.view') }} {{ __('adminlte::adminlte.entities.bookings') }}
      </a>
    </div>
  </div>

  {{-- ======= Tours Disponibles (colapsables simples) ======= --}}
  <div class="row">
    <div class="col-md-12 mb-3">
      <div class="card">
        <div class="card-header bg-danger text-white">
          <h4 class="mb-0">Tours Disponibles</h4>
        </div>
        <div class="card-body">
          @forelse (($itineraries ?? collect()) as $itinerary)
            @php $cid = 'collapseItin_' . $itinerary->itinerary_id; @endphp
            <div class="mb-2">
              <button type="button" class="btn btn-outline-danger btn-block text-center js-simple-toggle"
                      data-target="#{{ $cid }}" aria-expanded="false" aria-controls="{{ $cid }}">
                {{ $itinerary->name }}<i class="fas fa-chevron-down ml-2 itin-chevron" aria-hidden="true"></i>
              </button>
              <div id="{{ $cid }}" class="mt-2 simple-collapse">
                @php $items = $itinerary->items ?? collect(); @endphp
                @if ($items->isEmpty())
                  <p class="text-muted text-center mb-0">{{ __('adminlte::adminlte.empty.itinerary_items') }}</p>
                @else
                  <ul class="list-group">
                    @foreach ($items->sortBy('order') as $item)
                      <li class="list-group-item">
                        <strong>{{ $item->title }}</strong><br>
                        <span class="text-muted">{{ $item->description }}</span>
                      </li>
                    @endforeach
                  </ul>
                @endif
              </div>
            </div>
          @empty
            <p class="text-muted text-center">{{ __('adminlte::adminlte.empty.itineraries') }}</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  {{-- ======= Próximas reservas ======= --}}
  <div class="col-md-12 mb-3">
    <div class="card shadow">
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
          {{ __('adminlte::adminlte.sections.upcoming_bookings') }}
          @isset($tomorrowC)
            <small class="d-block fw-normal">({{ __('adminlte::adminlte.labels.date') }}: {{ $tomorrowC->format('d/m/Y') }})</small>
          @endisset
        </h5>
      </div>
      <div class="card-body">
        @forelse (($upcomingBookings ?? collect()) as $booking)
          <div class="mb-2">
            <strong>{{ $booking->user->full_name ?? '—' }}</strong>
            – {{ optional(optional($booking->detail)->tour)->name ?? '—' }}<br>
            <small class="text-muted">
              {{ __('adminlte::adminlte.labels.reference') }}: {{ $booking->booking_reference ?? '—' }}
            </small><br>
            <span class="text-muted">
              {{ __('adminlte::adminlte.labels.date') }}:
              {{ optional(optional($booking->detail)->tour_date)->format('d/m/Y') ?? '—' }}
            </span>
          </div>
          <hr>
        @empty
          <p class="text-muted">{{ __('adminlte::adminlte.empty.upcoming_bookings') }}</p>
        @endforelse
      </div>
    </div>
  </div>

  {{-- ================= Sistema de Alertas Flotante ================= --}}
  @php
    use Illuminate\Support\Facades\Route as RouteFacade;
    $serverAlerts = collect($capAlerts ?? []);
    $capCount  = $capCritical ?? $serverAlerts->whereIn('type',['near_capacity','sold_out'])->count();

    $incRouteName = RouteFacade::has('admin.capacity.increase') ? 'admin.capacity.increase' : (RouteFacade::has('capacity.increase') ? 'capacity.increase' : null);
    $detRouteName = RouteFacade::has('admin.capacity.details')  ? 'admin.capacity.details'  : (RouteFacade::has('capacity.details')  ? 'capacity.details'  : null);
    $blkRouteName = RouteFacade::has('admin.capacity.block')    ? 'admin.capacity.block'    : (RouteFacade::has('capacity.block')    ? 'capacity.block'    : null);
  @endphp

  <script id="cap-data" type="application/json">{!! json_encode($serverAlerts->values(), JSON_UNESCAPED_UNICODE) !!}</script>

  <div id="cap-widget" class="cap-widget">
    <button id="cap-toggle" class="cap-toggle" type="button" aria-expanded="false">
      <i class="fas fa-bell"></i>
      <span>Alertas</span>
      <span class="cap-badge" id="cap-count">{{ $capCount }}</span>
      <i class="fas fa-chevron-up cap-chevron"></i>
    </button>

    <div id="cap-panel" class="cap-panel" hidden>
      <div class="cap-header">
        <div class="d-flex align-items-center gap-2" style="min-width:0">
          <i class="fas fa-bell"></i>
          <strong class="text-truncate" style="min-width:0">Alertas de Capacidad</strong>
          <span class="cap-badge" id="cap-count-top">{{ $capCount }}</span>
        </div>
        <div class="cap-actions">
          <button id="cap-markread" title="Marcar todas como leídas"><i class="fas fa-check-double"></i></button>
          <button id="cap-minimize" title="Minimizar"><i class="fas fa-chevron-down"></i></button>
        </div>
      </div>

      <div class="cap-body" id="cap-list"><!-- JS pinta aquí --></div>

      <div class="cap-footer">
        <i class="fas fa-info-circle"></i> <span>Actualizaciones en tiempo real</span>
      </div>
    </div>
  </div>
  {{-- ================= /Sistema Alertas ================= --}}
@stop

{{-- ===== Modal Detalles (sin título) ===== --}}
@section('footer')
<div class="modal fade" id="capDetailsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
    <div class="modal-content">
      <div class="modal-header">
        <div class="flex-grow-1"></div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div id="capDetailsBody" class="p-3">Cargando…</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('css')
<style>
/* ========== Collapses tours ========== */
.simple-collapse{display:none}.simple-collapse.show{display:block}
.itin-chevron{transition:transform .2s}.js-simple-toggle[aria-expanded="true"] .itin-chevron{transform:rotate(180deg)}

/* ========== Z-INDEX / STACKING ========== */
.swal2-container{ z-index: 2005 !important; }
.modal{ z-index: 2000; }
.modal-backdrop{ z-index: 1995; }

/* ========== Widget flotante ========== */
.cap-widget{
  position:fixed;right:clamp(12px,2vw,20px);bottom:clamp(12px,2vw,20px);
  z-index:1080;display:flex;flex-direction:column;gap:10px;
  padding-bottom: env(safe-area-inset-bottom,0);
}
.cap-toggle{
  background:linear-gradient(135deg,#6366f1 0%,#8b5cf6 100%); color:#fff;border:none;border-radius:999px;
  padding:12px 16px;display:flex;align-items:center;gap:8px;font-weight:700;
  box-shadow:0 8px 24px rgba(99,102,241,.35); min-height:44px;
}
.cap-toggle .cap-badge{background:rgba(255,255,255,.22);padding:2px 8px;border-radius:12px;font-size:12px}
.cap-chevron{transition:transform .25s}
.cap-toggle[aria-expanded="true"] .cap-chevron{transform:rotate(180deg)}

.cap-panel{
  width:min(90vw,420px);max-height:70vh;background:#1f2937;color:#e5e7eb;border-radius:16px;overflow:hidden;
  box-shadow:0 24px 60px rgba(0,0,0,.5);transform:translateY(8px);opacity:0;pointer-events:none;transition:all .25s;
}
.cap-panel:not([hidden]){opacity:1;pointer-events:auto;transform:translateY(0)}
.cap-header{display:flex;align-items:center;justify-content:space-between;background:linear-gradient(135deg,#6366f1 0%,#8b5cf6 100%);padding:12px 14px}
.cap-header .cap-badge{background:rgba(255,255,255,.25);padding:2px 8px;border-radius:12px;font-weight:700}
.cap-actions{display:flex;gap:8px}
.cap-actions button{background:rgba(255,255,255,.2);border:0;color:#fff;width:34px;height:34px;border-radius:10px}

/* Cuerpo con padding inferior extra para que la última tarjeta no quede cortada */
.cap-body{
  padding:10px 10px calc(20px + env(safe-area-inset-bottom,0));
  overflow:auto;max-height:calc(70vh - 92px);background:#111827;
  -webkit-overflow-scrolling: touch;
}
.cap-body::-webkit-scrollbar{width:6px}
.cap-body::-webkit-scrollbar-thumb{background:#374151;border-radius:10px}

.cap-empty{padding:40px 10px;text-align:center;color:#9ca3af}
.cap-empty i{font-size:48px;margin-bottom:8px;color:#10b981}

/* ====== TARJETA ====== */
.cap-card{
  position:relative;background:#1f2937;border-radius:12px;padding:12px;margin-bottom:12px;
  border-left:4px solid #6b7280;box-shadow:0 1px 6px rgba(0,0,0,.25);
  overflow:hidden; word-break:break-word; hyphens:auto;
}
.cap-card__top{display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;min-width:0}
.cap-chip{font-size:11px;font-weight:700;border-radius:999px;padding:4px 10px;background:#374151;color:#e5e7eb;text-transform:uppercase}
.cap-chip--danger{background:rgba(239,68,68,.2);color:#fca5a5}
.cap-chip--near_capacity{background:rgba(245,158,11,.2);color:#fcd34d}
.cap-chip--info{background:rgba(59,130,246,.2);color:#93c5fd}

/* Títulos largos: 2 líneas máximo en móvil */
.cap-card__title{
  font-size:15px;font-weight:700;margin-bottom:4px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
  line-height:1.25;min-width:0;
}
.cap-card__date{font-size:13px;color:#9ca3af;margin-bottom:8px;display:flex;gap:6px;align-items:center;min-width:0}

/* Stats */
.cap-stats{
  display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:8px
}
@media (max-width: 420px){
  .cap-stats{grid-template-columns:repeat(2,1fr)}
}
.cap-stat__lbl{font-size:11px;text-transform:uppercase;color:#9ca3af}
.cap-stat__val{font-size:16px;font-weight:800;color:#f9fafb}

/* Progreso */
.cap-bar{height:6px;background:#374151;border-radius:8px;overflow:hidden}
.cap-bar__fill{height:100%;background:linear-gradient(90deg,#10b981 0%,#059669 100%)}

/* Acciones responsivas */
.cap-actions-row{
  display:flex;gap:8px;flex-wrap:wrap;margin-top:10px
}
.cap-btn{
  border:0;border-radius:10px;padding:8px 12px;font-weight:700;font-size:12px;display:flex;gap:6px;align-items:center;min-height:36px;
  flex:1 1 auto; /* toma el ancho disponible y evita overflow */
}
@media (max-width: 480px){
  .cap-actions-row .cap-btn{ flex:1 0 48%; } /* 2 por fila en XS */
  .cap-actions-row .cap-btn.ms-auto{ margin-left:0 !important; flex-basis:100%; } /* "Detalles" abajo si no cabe */
}
.cap-btn--primary{background:linear-gradient(135deg,#3b82f6 0%,#2563eb 100%);color:#fff}
.cap-btn--danger{background:linear-gradient(135deg,#ef4444 0%,#dc2626 100%);color:#fff}
.cap-btn--success{background:linear-gradient(135deg,#10b981 0%,#059669 100%);color:#fff}
.cap-btn--ghost{background:#374151;color:#e5e7eb}

.cap-dismiss{background:#374151;border:0;color:#9ca3af;width:28px;height:28px;border-radius:8px}
.cap-footer{padding:10px 14px;border-top:1px solid #374151;background:#1f2937;color:#9ca3af;font-size:13px}

/* ===== Modal Detalles: responsive ===== */
#capDetailsModal .modal-body{max-height:70vh; overflow:auto;}
@media (max-width: 576px){
  #capDetailsModal .modal-body{max-height:82vh}
  .cap-panel{ width:96vw; max-height:min(82vh, 620px); right:calc(env(safe-area-inset-right,0) + 8px); }
  .cap-toggle{ padding:14px 18px; }
}
@supports (padding: max(0px)) {
  .cap-widget{ padding-bottom: max(12px, env(safe-area-inset-bottom)); }
}

/* Tablas dentro del modal */
#capDetailsBody table{margin-bottom:0}
#capDetailsBody .table-responsive{border-top:1px solid rgba(0,0,0,.05)}
</style>
@endpush

@push('js')
@once
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endonce
<script>
(function(){
  /* ===================== Helpers ===================== */
  const $ = (s,root=document)=>root.querySelector(s);
  const $$ = (s,root=document)=>Array.from(root.querySelectorAll(s));
  const token = '{{ csrf_token() }}';

  const KEY_OPEN  = 'capWidgetOpen';
  const KEY_CACHE = 'capCacheV1';
  const TTL_MS    = 24*60*60*1000;

  // Rutas con placeholder __SID__
  const incRoute = sid => `{{ $incRouteName ? route($incRouteName, ['schedule' => '__SID__']) : '' }}`.replace('__SID__',sid);
  const detRoute = sid => `{{ $detRouteName ? route($detRouteName, ['schedule' => '__SID__']) : '' }}`.replace('__SID__',sid);
  const blkRoute = sid => `{{ $blkRouteName ? route($blkRouteName, ['schedule' => '__SID__']) : '' }}`.replace('__SID__',sid);

  const widget  = $('#cap-widget');
  const toggle  = $('#cap-toggle');
  const panel   = $('#cap-panel');
  const minimize= $('#cap-minimize');
  const markAll = $('#cap-markread');
  const list    = $('#cap-list');

  /* ===================== Collapses tours ===================== */
  $$('.js-simple-toggle').forEach(b=>{
    const p = $(b.getAttribute('data-target')); if(!p) return;
    b.addEventListener('click', ()=>{ const o=p.classList.toggle('show'); b.setAttribute('aria-expanded', o?'true':'false'); });
  });

  /* ===================== Widget open/close ===================== */
  const open  = ()=>{ toggle.setAttribute('aria-expanded','true'); panel.hidden=false; };
  const close = ()=>{ toggle.setAttribute('aria-expanded','false'); panel.hidden=true; };
  try { localStorage.getItem(KEY_OPEN)==='1' ? open() : close(); } catch{ close(); }

  toggle?.addEventListener('click',()=>{
    const isOpen = toggle.getAttribute('aria-expanded')==='true';
    isOpen ? close() : open();
    try{ localStorage.setItem(KEY_OPEN, isOpen?'0':'1'); }catch{}
  });
  minimize?.addEventListener('click',()=>{ close(); try{ localStorage.setItem(KEY_OPEN,'0'); }catch{} });
  document.addEventListener('keydown',e=>{ if(e.key==='Escape' && toggle.getAttribute('aria-expanded')==='true'){ e.preventDefault(); close(); }});
  document.addEventListener('click',e=>{ if(toggle.getAttribute('aria-expanded')==='true' && !widget.contains(e.target)) close(); });

  /* ===================== Cache ===================== */
  const loadCache = ()=>{
    try{
      const raw = JSON.parse(localStorage.getItem(KEY_CACHE)||'{}'), now=Date.now();
      Object.keys(raw).forEach(k=>{ if(!raw[k] || (raw[k].exp||0)<now) delete raw[k]; });
      return raw;
    }catch{ return {}; }
  };
  const saveCache = c=>{ try{ localStorage.setItem(KEY_CACHE, JSON.stringify(c)); }catch{} };
  const putInCache = a=>{ const c=loadCache(); c[a.key]={data:a,exp:Date.now()+TTL_MS}; saveCache(c); };
  const removeFromCache = key=>{ const c=loadCache(); delete c[key]; saveCache(c); };

  /* ===================== Render ===================== */
  const formatDateDMY = iso => !iso ? '—' : iso.split('T')[0].split('-').reverse().join('/');
  const cardHTML = a=>{
    const critical = (a.remaining<=3 || a.pct>=90);
    const chipCls  = a.type==='sold_out' ? 'cap-chip--danger' : (critical ? 'cap-chip--near_capacity' : 'cap-chip--info');
    const badge    = a.type==='sold_out' ? 'SOLD OUT' : (critical ? 'CRÍTICO' : 'ALERTA');
    const pct      = Math.max(0, Math.min(100, parseInt(a.pct||0,10)));
    const incLbl   = a.type==='sold_out' ? '<i class="fas fa-unlock"></i> Desbloquear' : '<i class="fas fa-plus-circle"></i> Ampliar';
    const incCls   = a.type==='sold_out' ? 'cap-btn--success' : 'cap-btn--primary';
    const blkDis   = a.type==='sold_out' ? 'disabled' : '';
    return `
      <div class="cap-card" data-key="${a.key}" data-id="${a.schedule_id}" data-date="${a.date}">
        <div class="cap-card__top">
          <div class="cap-chip ${chipCls}">${badge}</div>
          <button class="cap-dismiss" title="Ocultar"><i class="fas fa-times"></i></button>
        </div>
        <div class="cap-card__title" title="${(a.tour||'').replace(/"/g,'&quot;')}">${a.tour||'—'}</div>
        <div class="cap-card__date"><i class="far fa-calendar-alt"></i> ${formatDateDMY(a.date||'')}</div>
        <div class="cap-stats">
          <div><div class="cap-stat__lbl">Reservados</div><div class="cap-stat__val js-used">${a.used}/${a.max}</div></div>
          <div><div class="cap-stat__lbl">Disponibles</div><div class="cap-stat__val js-rem">${a.remaining}</div></div>
          <div><div class="cap-stat__lbl">Ocupación</div><div class="cap-stat__val js-pct">${a.pct}%</div></div>
        </div>
        <div class="cap-bar"><div class="cap-bar__fill js-bar" style="width:${pct}%"></div></div>
        <div class="cap-actions-row">
          <button class="cap-btn ${incCls} js-inc" data-url-inc="${incRoute(a.schedule_id)}">${incLbl}</button>
          <button class="cap-btn cap-btn--danger js-block" data-url-block="${blkRoute(a.schedule_id)}" ${blkDis}><i class="fas fa-ban"></i> Bloquear</button>
          <button class="cap-btn cap-btn--ghost ms-auto js-det" data-url-det="${detRoute(a.schedule_id)}"><i class="fas fa-eye"></i> Detalles</button>
        </div>
      </div>`;
  };

  const updateCountersFromDOM = ()=>{
    const leftCritical = $$('.cap-card').filter(c=>{
      const [u,max] = (c.querySelector('.js-used')?.textContent || '0/0').split('/').map(x=>parseInt(x,10));
      const rem = Math.max(0, (max||0)-(u||0));
      const pct = (max||0)>0 ? Math.floor((u*100)/max) : 0;
      return rem===0 || rem<=3 || pct>=80;
    }).length;
    const c1=$('#cap-count'), c2=$('#cap-count-top');
    if(c1) c1.textContent=leftCritical;
    if(c2) c2.textContent=leftCritical;
  };

  const mountCards = ()=>{
    const server = JSON.parse($('#cap-data')?.textContent || '[]');
    const sMap = Object.fromEntries(server.map(a=>[a.key,a]));
    const cache = loadCache();
    const merged = {...cache};
    Object.keys(sMap).forEach(k=>{ merged[k]={data:sMap[k],exp:Date.now()+TTL_MS}; });
    const arr = Object.values(merged).map(x=>x.data)
      .sort((a,b)=>(a.date||'').localeCompare(b.date||'') || (a.tour||'').localeCompare(b.tour||''));
    list.innerHTML = arr.length ? arr.map(cardHTML).join('') :
      '<div class="cap-empty"><i class="fas fa-check-circle"></i><p>No hay alertas pendientes</p></div>';
    saveCache(merged);
    updateCountersFromDOM();
  };

  mountCards();
  markAll?.addEventListener('click',()=>{ list.innerHTML='<div class="cap-empty"><i class="fas fa-check-circle"></i><p>No hay alertas pendientes</p></div>'; saveCache({}); updateCountersFromDOM(); });

  /* ===================== Modal Manager (singleton) ===================== */
  const detailsEl = $('#capDetailsModal');
  const bodyEl    = $('#capDetailsBody');
  let detailsModal=null, detailsAbort=null, detailsBusy=false, detailsTimer=null, lastClickTs=0;

  const getModal = ()=>{
    if(!window.bootstrap) return null;
    if(!detailsModal){
      detailsModal = bootstrap.Modal.getOrCreateInstance(detailsEl, {backdrop:true,keyboard:true,focus:true});
      detailsEl.addEventListener('hidden.bs.modal', ()=>{
        try{ detailsAbort?.abort(); }catch{} detailsAbort=null; detailsBusy=false;
        if(detailsTimer){ clearTimeout(detailsTimer); detailsTimer=null; }
        if(bodyEl) bodyEl.innerHTML='';
        document.querySelectorAll('.modal-backdrop').forEach(b=>b.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('padding-right');
      });
      detailsEl.addEventListener('shown.bs.modal', ()=>{ document.body.classList.add('modal-open'); });
    }
    return detailsModal;
  };

  const canTrigger = ()=>{
    const now=Date.now(); if(now-lastClickTs<350) return false; lastClickTs=now; return true;
  };

  const hideModalIfOpen = () => new Promise(resolve=>{
    const m = getModal(); if(!m){ resolve(); return; }
    const el = detailsEl;
    if (el.classList.contains('show')){
      const onHidden = ()=>{ el.removeEventListener('hidden.bs.modal', onHidden); resolve(); };
      el.addEventListener('hidden.bs.modal', onHidden);
      m.hide();
    } else { resolve(); }
  });

  const renderDetails = (container, res)=>{
    const rows = res?.data || [];
    if(!rows.length){ container.innerHTML='<p class="text-muted mb-0 p-3">Sin reservas en los próximos 30 días para este horario.</p>'; return; }
    let html = `<div class="table-responsive"><table class="table table-sm align-middle mb-0">
      <thead><tr><th>Fecha</th><th>Tour</th>
      <th class="text-end">Reservados</th><th class="text-end">Capacidad</th>
      <th class="text-end">Disponibles</th><th class="text-end">Ocupación</th></tr></thead><tbody>`;
    rows.forEach(r=>{
      const used=parseInt(r.used||0,10), max=parseInt(r.max||0,10);
      const rem=Math.max(0,max-used), pct=max>0?Math.floor((used*100)/max):0;
      html+=`<tr><td>${(r.date||'').slice(0,10)}</td><td>${r.tour||'—'}</td>
        <td class="text-end">${used}</td><td class="text-end">${max}</td>
        <td class="text-end">${rem}</td><td class="text-end">${pct}%</td></tr>`;
    });
    html+=`</tbody></table></div>`;
    container.innerHTML=html;
  };

  const applyResponse = (card,res)=>{
    card.querySelector('.js-used').textContent = `${res.used}/${res.max_capacity}`;
    card.querySelector('.js-rem').textContent  = `${res.remaining}`;
    card.querySelector('.js-pct').textContent  = `${res.pct}%`;
    card.querySelector('.js-bar').style.width  = (res.pct||0)+'%';

    const chip=card.querySelector('.cap-chip'), incBtn=card.querySelector('.js-inc'), blkBtn=card.querySelector('.js-block');
    chip.className='cap-chip';
    if(res.max_capacity===0 || (res.remaining===0 && res.pct>=100)){
      chip.classList.add('cap-chip--danger'); chip.textContent='SOLD OUT';
      incBtn.classList.remove('cap-btn--primary'); incBtn.classList.add('cap-btn--success'); incBtn.innerHTML='<i class="fas fa-unlock"></i> Desbloquear';
      blkBtn.disabled=true;
    }else if(res.remaining<=3 || res.pct>=80){
      chip.classList.add('cap-chip--near_capacity'); chip.textContent='CRÍTICO';
      incBtn.classList.remove('cap-btn--success'); incBtn.classList.add('cap-btn--primary'); incBtn.innerHTML='<i class="fas fa-plus-circle"></i> Ampliar';
      blkBtn.disabled=false;
    }else{
      chip.classList.add('cap-chip--info'); chip.textContent='ALERTA';
      incBtn.classList.remove('cap-btn--success'); incBtn.classList.add('cap-btn--primary'); incBtn.innerHTML='<i class="fas fa-plus-circle"></i> Ampliar';
      blkBtn.disabled=false;
    }
  };

  const cardToAlert = (card)=>{
    const [u,max]=(card.querySelector('.js-used')?.textContent||'0/0').split('/').map(x=>parseInt(x,10));
    const remaining=Math.max(0,(max||0)-(u||0));
    const pct=(max||0)>0?Math.floor((u*100)/max):0;
    const type=remaining===0?'sold_out':(remaining<=3||pct>=80)?'near_capacity':'info';
    return { key:card.dataset.key, schedule_id:parseInt(card.dataset.id,10),
             date:card.dataset.date, tour:card.querySelector('.cap-card__title')?.textContent||'—',
             used:u, max:max, remaining, pct, type };
  };

  /* ===================== Delegación en tarjetas ===================== */
  list?.addEventListener('click', async (e)=>{
    const card = e.target.closest('.cap-card'); if(!card) return;

    if(e.target.closest('.cap-dismiss')){ removeFromCache(card.dataset.key); card.remove(); updateCountersFromDOM(); return; }

    if(e.target.closest('.js-det')){
      if(!canTrigger()) return;
      try{ if(window.Swal) Swal.close(); }catch{}
      const m = getModal(); if(!m) return;
      if(detailsBusy) { m.show(); return; }

      const url = e.target.closest('.js-det').dataset.urlDet;
      if(!url) return Swal.fire('Ruta faltante','Define la ruta: admin.capacity.details','warning');

      try{ detailsAbort?.abort(); }catch{}
      detailsAbort = new AbortController();
      detailsBusy  = true;

      if(bodyEl){
        bodyEl.innerHTML = `<div class="p-3">
          <div class="d-flex align-items-center gap-2 mb-2">
            <div class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></div>
            <strong>Cargando detalles…</strong>
          </div>
          <div class="progress" style="height:6px;"><div class="progress-bar progress-bar-striped progress-bar-animated" style="width:55%"></div></div>
        </div>`;
      }
      m.show();

      detailsTimer = setTimeout(()=>{ try{ detailsAbort?.abort(); }catch{} }, 10000);

      try{
        const res  = await fetch(url, {headers:{'Accept':'application/json'}, signal: detailsAbort.signal});
        const data = await res.json();
        if(!res.ok || !data?.ok) throw new Error('bad_response');
        renderDetails(bodyEl, data);
      }catch(err){
        if(err?.name==='AbortError'){ bodyEl.innerHTML='<p class="text-muted mb-0 p-3">Operación cancelada.</p>'; }
        else { bodyEl.innerHTML='<p class="text-danger mb-0 p-3">No se pudieron cargar los detalles.</p>'; }
      }finally{
        detailsBusy=false; if(detailsTimer){ clearTimeout(detailsTimer); detailsTimer=null; }
      }
      return;
    }

    if(e.target.closest('.js-inc')){
      await hideModalIfOpen();
      const url = e.target.closest('.js-inc').dataset.urlInc;
      if(!url) return Swal.fire('Ruta faltante','Define la ruta: admin.capacity.increase','warning');

      const isSoldOrBlocked =
        card.querySelector('.cap-chip')?.classList.contains('cap-chip--danger') ||
        parseInt(card.querySelector('.js-rem')?.textContent || '0', 10) === 0 ||
        /\/0$/.test(card.querySelector('.js-used')?.textContent || '');

      let amount;
      if(isSoldOrBlocked){
        const used = parseInt((card.querySelector('.js-used')?.textContent || '0/0').split('/')[0],10) || 0;
        amount = used + 2;  // desbloquear = usados + 2
      }else{
        const { value } = await Swal.fire({
          title: 'Añadir espacios',
          input: 'number',
          inputLabel: 'Cantidad',
          inputValue: 5,
          inputAttributes: { min:1, step:1 },
          showCancelButton: true,
          confirmButtonText: 'Añadir',
          cancelButtonText: 'Cancelar'
        });
        if(value===undefined) return;
        amount = parseInt(value,10);
        if(isNaN(amount)||amount<1) return Swal.fire('Cantidad inválida','Ingresa un entero ≥ 1','error');
      }

      try{
        const res  = await fetch(url,{
          method:'PATCH',
          headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token,'Accept':'application/json'},
          body:JSON.stringify({amount, date:card.dataset.date||null})
        });
        const data = await res.json();
        if(!res.ok || !data?.ok) throw new Error();
        applyResponse(card,data);
        putInCache(cardToAlert(card));
        Swal.fire('Listo','Capacidad actualizada','success');
        updateCountersFromDOM();
      }catch{ Swal.fire('Error','No se pudo actualizar','error'); }
      return;
    }

    if(e.target.closest('.js-block')){
      await hideModalIfOpen();
      const url = e.target.closest('.js-block').dataset.urlBlock;
      if(!url) return Swal.fire('Ruta faltante','Define la ruta: capacity.block','warning');
      const dateStr = card.dataset.date||null;
      if(!dateStr) return Swal.fire('Fecha requerida','Falta la fecha a bloquear','warning');

      const { isConfirmed } = await Swal.fire({
        title:'Bloquear fecha',
        text:'¿Bloquear por completo este horario para la fecha seleccionada?',
        icon:'warning', showCancelButton:true,
        confirmButtonText:'Sí, bloquear', cancelButtonText:'Cancelar'
      });
      if(!isConfirmed) return;

      try{
        const res  = await fetch(url,{method:'PATCH',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token,'Accept':'application/json'},body:JSON.stringify({date:dateStr})});
        const data = await res.json();
        if(!res.ok || !data?.ok) throw new Error();
        applyResponse(card,data);
        putInCache(cardToAlert(card));
        Swal.fire('Bloqueado','La fecha fue bloqueada','success');
        updateCountersFromDOM();
      }catch{ Swal.fire('Error','No se pudo bloquear','error'); }
      return;
    }
  });
})();
</script>
@endpush
