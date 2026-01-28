{{-- resources/views/admin/notifications.blade.php --}}
@php
use Illuminate\Support\Facades\Route as RouteFacade;

/** @var \Illuminate\Support\Collection|array $capAlerts */
$serverAlerts = collect($capAlerts ?? []);
$capCount = $capCritical ?? $serverAlerts->whereIn('type', ['near_capacity', 'sold_out'])->count();

// Descubrimiento de rutas con fallback (API v1)
$incRouteName = RouteFacade::has('admin.tours.capacity.increase') ? 'admin.tours.capacity.increase' : null;
$detRouteName = RouteFacade::has('admin.tours.capacity.details') ? 'admin.tours.capacity.details' : null;
$blkRouteName = RouteFacade::has('admin.tours.capacity.block') ? 'admin.tours.capacity.block' : null;
@endphp

{{-- ===== Reescribir títulos de tours con el nombre traducido ===== --}}
@php
use App\Models\Product;

$locale = app()->getLocale();

// 1) Recolectar los product_id presentes en las alertas
$tourIds = $serverAlerts
->map(fn($a) => data_get($a, 'product_id')
?? data_get($a, 'tour.product_id')
?? data_get($a, 'tour.id'))
->filter()
->unique()
->values();

// 2) Cargar tours (productos) con traducciones y mapear por product_id
// Spatie no tiene 'translations' relationship en la definición de modelo updated pero Spatie Translatable la maneja internamente.
// Actually invalid relationship if remove? Spatie uses 'translations' table but access via trait.
// If accessors are used, translation is automatic.
// The code uses $tour->getTranslatedName($locale) which was likely custom.
// We should check if getTranslatedName exists in Product model or use standard Spatie accessor $product->name.

$toursById = Product::whereIn('product_id', $tourIds)
->get()
->keyBy('product_id');

// 3) Reescribir el campo "tour" con el nombre traducido
$serverAlerts = $serverAlerts->map(function ($a) use ($toursById, $locale) {
$tid = data_get($a, 'product_id')
?? data_get($a, 'tour.product_id')
?? data_get($a, 'tour.id');

if ($tid && $toursById->has($tid)) {
$tour = $toursById[$tid];
data_set($a, 'product_id', $tid);
// Use standard accessor or explicit getTranslation
data_set($a, 'tour', $tour->name); // $model->name is automagically translated by Spatie
}
return $a;
});

// Recalcular contador crítico por si cambió la colección
$capCount = $capCritical ?? collect($serverAlerts)->whereIn('type', ['near_capacity','sold_out'])->count();
@endphp

{{-- ===== I18N para JS (seguro contra Blade) ===== --}}
@php
$I18N = [
// Widget / cabeceras
'alerts' => __('m_notifications.widget.alerts'),
'capacity_alerts' => __('m_notifications.widget.capacity_alerts'),
'mark_all_read' => __('m_notifications.widget.mark_all_read'),
'minimize' => __('m_notifications.widget.minimize'),
'realtime' => __('m_notifications.widget.realtime'),
'no_alerts' => __('m_notifications.widget.no_alerts'),

// Tarjeta / campos
'reserved' => __('m_notifications.card.reserved'),
'available' => __('m_notifications.card.available'),
'occupancy' => __('m_notifications.card.occupancy'),
'blocked_at' => 'Bloqueado en',

// Badges
'sold_out' => __('m_notifications.badge.sold_out'),
'critical' => __('m_notifications.badge.critical'),
'alert' => __('m_notifications.badge.alert'),

// Acciones
'unlock' => __('m_notifications.actions.unlock'),
'expand' => __('m_notifications.actions.expand'),
'block' => __('m_notifications.actions.block'),
'details' => __('m_notifications.actions.details'),
'reduce' => 'Reducir',

// Modales / textos
'loading_details' => __('m_notifications.modal.loading_details'),
'operation_cancel' => __('m_notifications.modal.operation_cancel'),
'cannot_load' => __('m_notifications.modal.cannot_load'),
'close' => __('m_notifications.modal.close'),

// Errores de ruta
'missing_route' => __('m_notifications.errors.missing_route'),
'define_route_inc' => __('m_notifications.errors.define_route_inc'),
'define_route_det' => __('m_notifications.errors.define_route_det'),
'define_route_blk' => __('m_notifications.errors.define_route_blk'),

// Prompts de ampliación
'add_spaces' => __('m_notifications.prompts.add_spaces'),
'quantity' => __('m_notifications.prompts.quantity'),
'add' => __('m_notifications.prompts.add'),
'cancel' => __('m_notifications.prompts.cancel'),
'invalid_qty' => __('m_notifications.prompts.invalid_qty'),
'enter_int' => __('m_notifications.prompts.enter_int'),
'modify_capacity' => 'Modificar Capacidad',
'positive_expand' => 'Número positivo: expandir capacidad',
'negative_reduce' => 'Número negativo: reducir capacidad',

// Toasters
'ready' => __('m_notifications.toasts.ready'),
'capacity_updated' => __('m_notifications.toasts.capacity_updated'),
'error' => __('m_notifications.toasts.error'),
'couldnt_update' => __('m_notifications.toasts.couldnt_update'),

// Bloqueo
'block_date' => __('m_notifications.block.block_date'),
'block_confirm' => __('m_notifications.block.block_confirm'),
'yes_block' => __('m_notifications.block.yes_block'),
'blocked' => __('m_notifications.block.blocked'),
'date_blocked' => __('m_notifications.block.date_blocked'),
'couldnt_block' => __('m_notifications.block.couldnt_block'),
'required_date' => __('m_notifications.block.required_date'),
];
@endphp
<script>
  window.CAP_I18N = @json($I18N, JSON_UNESCAPED_UNICODE);
</script>

{{-- Datos crudos para JS --}}
<script id="cap-data" type="application/json">
  {
    !!json_encode($serverAlerts->values(), JSON_UNESCAPED_UNICODE) !!
  }
</script>

{{-- ===== Widget flotante ===== --}}
<div id="cap-widget" class="cap-widget">
  <button id="cap-toggle" class="cap-toggle" type="button" aria-expanded="false">
    <i class="fas fa-bell"></i>
    <span>{{ __('m_notifications.widget.alerts') }}</span>
    <span class="cap-badge" id="cap-count">{{ $capCount }}</span>
    <i class="fas fa-chevron-up cap-chevron"></i>
  </button>

  <div id="cap-panel" class="cap-panel" hidden>
    <div class="cap-header">
      <div class="d-flex align-items-center gap-2" style="min-width:0">
        <i class="fas fa-bell"></i>
        <strong class="text-truncate" style="min-width:0">{{ __('m_notifications.widget.capacity_alerts') }}</strong>
        <span class="cap-badge" id="cap-count-top">{{ $capCount }}</span>
      </div>
      <div class="cap-actions">
        <button id="cap-markread" title="{{ __('m_notifications.widget.mark_all_read') }}"><i class="fas fa-check-double"></i></button>
        <button id="cap-minimize" title="{{ __('m_notifications.widget.minimize') }}"><i class="fas fa-chevron-down"></i></button>
      </div>
    </div>

    <div class="cap-body" id="cap-list"><!-- Render JS --></div>

    <div class="cap-footer">
      <i class="fas fa-info-circle"></i> <span>{{ __('m_notifications.widget.realtime') }}</span>
    </div>
  </div>
</div>
{{-- ===== /Widget flotante ===== --}}

{{-- ===== Modal Detalles ===== --}}
<div class="modal fade" id="capDetailsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
    <div class="modal-content">
      <div class="modal-header">
        <div class="flex-grow-1"></div>
        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('m_notifications.modal.close') }}"></button>
      </div>
      <div class="modal-body p-0">
        <div id="capDetailsBody" class="p-3">{{ __('m_notifications.modal.loading_details') }}</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('m_notifications.modal.close') }}</button>
      </div>
    </div>
  </div>
</div>

@push('css')
<style>
  /* ========== Z-INDEX / STACKING ========== */
  .swal2-container {
    z-index: 2005 !important;
  }

  .modal {
    z-index: 2000;
  }

  .modal-backdrop {
    z-index: 1995;
  }

  /* ========== Widget flotante ========== */
  .cap-widget {
    position: fixed;
    right: clamp(12px, 2vw, 20px);
    bottom: clamp(12px, 2vw, 20px);
    z-index: 1080;
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding-bottom: env(safe-area-inset-bottom, 0);
  }

  .cap-toggle {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: #fff;
    border: none;
    border-radius: 999px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    box-shadow: 0 8px 24px rgba(99, 102, 241, .35);
    min-height: 44px;
  }

  .cap-toggle .cap-badge {
    background: rgba(255, 255, 255, .22);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px
  }

  .cap-chevron {
    transition: transform .25s
  }

  .cap-toggle[aria-expanded="true"] .cap-chevron {
    transform: rotate(180deg)
  }

  .cap-panel {
    width: min(90vw, 420px);
    max-height: 70vh;
    background: #1f2937;
    color: #e5e7eb;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 24px 60px rgba(0, 0, 0, .5);
    transform: translateY(8px);
    opacity: 0;
    pointer-events: none;
    transition: all .25s;
  }

  .cap-panel:not([hidden]) {
    opacity: 1;
    pointer-events: auto;
    transform: translateY(0)
  }

  .cap-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    padding: 12px 14px
  }

  .cap-header .cap-badge {
    background: rgba(255, 255, 255, .25);
    padding: 2px 8px;
    border-radius: 12px;
    font-weight: 700
  }

  .cap-actions {
    display: flex;
    gap: 8px
  }

  .cap-actions button {
    background: rgba(255, 255, 255, .2);
    border: 0;
    color: #fff;
    width: 34px;
    height: 34px;
    border-radius: 10px
  }

  /* Cuerpo */
  .cap-body {
    padding: 10px 10px calc(20px + env(safe-area-inset-bottom, 0));
    overflow: auto;
    max-height: calc(70vh - 92px);
    background: #111827;
    -webkit-overflow-scrolling: touch;
  }

  .cap-body::-webkit-scrollbar {
    width: 6px
  }

  .cap-body::-webkit-scrollbar-thumb {
    background: #374151;
    border-radius: 10px
  }

  .cap-empty {
    padding: 40px 10px;
    text-align: center;
    color: #9ca3af
  }

  .cap-empty i {
    font-size: 48px;
    margin-bottom: 8px;
    color: #10b981
  }

  /* Tarjeta */
  .cap-card {
    position: relative;
    background: #1f2937;
    border-radius: 12px;
    padding: 12px;
    margin-bottom: 12px;
    border-left: 4px solid #6b7280;
    box-shadow: 0 1px 6px rgba(0, 0, 0, .25);
    overflow: hidden;
    word-break: break-word;
    hyphens: auto;
  }

  .cap-card--blocked {
    border-left-color: #ef4444
  }

  .cap-card__top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
    min-width: 0
  }

  .cap-chip {
    font-size: 11px;
    font-weight: 700;
    border-radius: 999px;
    padding: 4px 10px;
    background: #374151;
    color: #e5e7eb;
    text-transform: uppercase
  }

  .cap-chip--danger {
    background: rgba(239, 68, 68, .2);
    color: #fca5a5
  }

  .cap-chip--near_capacity {
    background: rgba(245, 158, 11, .2);
    color: #fcd34d
  }

  .cap-chip--info {
    background: rgba(59, 130, 246, .2);
    color: #93c5fd
  }

  .cap-card__title {
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.25;
    min-width: 0;
  }

  .cap-card__date {
    font-size: 13px;
    color: #9ca3af;
    margin-bottom: 8px;
    display: flex;
    gap: 6px;
    align-items: center;
    min-width: 0
  }

  /* Stats */
  .cap-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-bottom: 8px
  }

  @media (max-width: 420px) {
    .cap-stats {
      grid-template-columns: repeat(2, 1fr)
    }
  }

  .cap-stat__lbl {
    font-size: 11px;
    text-transform: uppercase;
    color: #9ca3af
  }

  .cap-stat__val {
    font-size: 16px;
    font-weight: 800;
    color: #f9fafb
  }

  /* Progreso */
  .cap-bar {
    height: 6px;
    background: #374151;
    border-radius: 8px;
    overflow: hidden
  }

  .cap-bar__fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    transition: all 0.3s
  }

  .cap-bar__fill--blocked {
    background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%)
  }

  /* Acciones */
  .cap-actions-row {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 10px
  }

  .cap-btn {
    border: 0;
    border-radius: 10px;
    padding: 8px 12px;
    font-weight: 700;
    font-size: 12px;
    display: flex;
    gap: 6px;
    align-items: center;
    min-height: 36px;
    flex: 1 1 auto;
  }

  @media (max-width: 480px) {
    .cap-actions-row .cap-btn {
      flex: 1 0 48%;
    }

    .cap-actions-row .cap-btn.ms-auto {
      margin-left: 0 !important;
      flex-basis: 100%;
    }
  }

  .cap-btn--primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: #fff
  }

  .cap-btn--danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: #fff
  }

  .cap-btn--success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #fff
  }

  .cap-btn--ghost {
    background: #374151;
    color: #e5e7eb
  }

  .cap-dismiss {
    background: #374151;
    border: 0;
    color: #9ca3af;
    width: 28px;
    height: 28px;
    border-radius: 8px
  }

  .cap-footer {
    padding: 10px 14px;
    border-top: 1px solid #374151;
    background: #1f2937;
    color: #9ca3af;
    font-size: 13px
  }

  /* Modal */
  #capDetailsModal .modal-body {
    max-height: 70vh;
    overflow: auto;
  }

  @media (max-width: 576px) {
    #capDetailsModal .modal-body {
      max-height: 82vh
    }

    .cap-panel {
      width: 96vw;
      max-height: min(82vh, 620px);
      right: calc(env(safe-area-inset-right, 0) + 8px);
    }

    .cap-toggle {
      padding: 14px 18px;
    }
  }

  @supports (padding: max(0px)) {
    .cap-widget {
      padding-bottom: max(12px, env(safe-area-inset-bottom));
    }
  }

  /* Tablas dentro del modal */
  #capDetailsBody table {
    margin-bottom: 0
  }

  #capDetailsBody .table-responsive {
    border-top: 1px solid rgba(0, 0, 0, .05)
  }
</style>
@endpush

@push('js')
@once
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endonce
<script>
  (function() {
    const T = (k) => (window.CAP_I18N && window.CAP_I18N[k]) || k;

    /* ===================== Helpers ===================== */
    const $ = (s, root = document) => root.querySelector(s);
    const $$ = (s, root = document) => Array.from(root.querySelectorAll(s));
    const token = '{{ csrf_token() }}';

    const KEY_OPEN = 'capWidgetOpen';
    const KEY_CACHE = 'capCacheV1';
    const TTL_MS = 24 * 60 * 60 * 1000;

    // Rutas con placeholder __SID__
    const incRoute = sid => `{{ $incRouteName ? route($incRouteName, ['schedule' => '__SID__']) : '' }}`.replace('__SID__', sid);
    const detRoute = sid => `{{ $detRouteName ? route($detRouteName, ['schedule' => '__SID__']) : '' }}`.replace('__SID__', sid);
    const blkRoute = sid => `{{ $blkRouteName ? route($blkRouteName, ['schedule' => '__SID__']) : '' }}`.replace('__SID__', sid);

    const widget = $('#cap-widget');
    const toggle = $('#cap-toggle');
    const panel = $('#cap-panel');
    const minimize = $('#cap-minimize');
    const markAll = $('#cap-markread');
    const list = $('#cap-list');

    // ===== Datos crudos del servidor y mapa schedule->tour =====
    const serverRaw = JSON.parse($('#cap-data')?.textContent || '[]');
    const scheduleTourMap = Object.fromEntries(
      serverRaw.map(a => {
        const sid = String(a.schedule_id ?? a.scheduleId ?? a.scheduleID ?? '');
        const tid = a.product_id ?? a.tourId ?? a.tourID ?? a?.tour?.product_id ?? a?.tour?.id ?? null;
        return [sid, tid];
      })
    );

    /* ===================== Widget open/close ===================== */
    const open = () => {
      toggle.setAttribute('aria-expanded', 'true');
      panel.hidden = false;
    };
    const close = () => {
      toggle.setAttribute('aria-expanded', 'false');
      panel.hidden = true;
    };
    try {
      localStorage.getItem(KEY_OPEN) === '1' ? open() : close();
    } catch {
      close();
    }

    toggle?.addEventListener('click', () => {
      const isOpen = toggle.getAttribute('aria-expanded') === 'true';
      isOpen ? close() : open();
      try {
        localStorage.setItem(KEY_OPEN, isOpen ? '0' : '1');
      } catch {}
    });
    minimize?.addEventListener('click', () => {
      close();
      try {
        localStorage.setItem(KEY_OPEN, '0');
      } catch {}
    });
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
        e.preventDefault();
        close();
      }
    });
    document.addEventListener('click', e => {
      if (toggle.getAttribute('aria-expanded') === 'true' && !widget.contains(e.target)) close();
    });

    /* ===================== Cache ===================== */
    const loadCache = () => {
      try {
        const raw = JSON.parse(localStorage.getItem(KEY_CACHE) || '{}'),
          now = Date.now();
        Object.keys(raw).forEach(k => {
          if (!raw[k] || (raw[k].exp || 0) < now) delete raw[k];
        });
        return raw;
      } catch {
        return {};
      }
    };
    const saveCache = c => {
      try {
        localStorage.setItem(KEY_CACHE, JSON.stringify(c));
      } catch {}
    };
    const putInCache = a => {
      const c = loadCache();
      c[a.key] = {
        data: a,
        exp: Date.now() + TTL_MS
      };
      saveCache(c);
    };
    const removeFromCache = key => {
      const c = loadCache();
      delete c[key];
      saveCache(c);
    };

    /* ===================== Render ===================== */
    const formatDateDMY = iso => !iso ? '—' : iso.split('T')[0].split('-').reverse().join('/');

    const cardHTML = a => {
      const isBlocked = a.max === 0 || a.type === 'sold_out';
      const used = parseInt(a.used || 0, 10);
      const max = isBlocked ? used : parseInt(a.max || 0, 10);
      const displayMax = isBlocked ? used : max;

      const critical = !isBlocked && (a.remaining <= 3 || a.pct >= 90);
      const chipCls = isBlocked ? 'cap-chip--danger' : (critical ? 'cap-chip--near_capacity' : 'cap-chip--info');
      const badge = isBlocked ? T('sold_out') : (critical ? T('critical') : T('alert'));
      const pct = isBlocked ? 100 : Math.max(0, Math.min(100, parseInt(a.pct || 0, 10)));

      const incLbl = isBlocked ? `<i class="fas fa-unlock"></i> ${T('unlock')}` : `<i class="fas fa-edit"></i> ${T('expand')}`;
      const incCls = isBlocked ? 'cap-btn--success' : 'cap-btn--primary';
      const blkDis = isBlocked ? 'disabled' : '';
      const cardCls = isBlocked ? 'cap-card--blocked' : '';
      const barCls = isBlocked ? 'cap-bar__fill--blocked' : '';

      return `
      <div class="cap-card ${cardCls}" data-key="${a.key}" data-id="${a.schedule_id}" data-tour="${a.product_id||''}" data-date="${a.date}">
        <div class="cap-card__top">
          <div class="cap-chip ${chipCls}">${badge}</div>
          <button class="cap-dismiss" title="${T('alerts')}"><i class="fas fa-times"></i></button>
        </div>
        <div class="cap-card__title" title="${(a.tour||'').replace(/"/g,'&quot;')}">${a.tour||'—'}</div>
        <div class="cap-card__date"><i class="far fa-calendar-alt"></i> ${formatDateDMY(a.date||'')}</div>
        <div class="cap-stats">
          <div><div class="cap-stat__lbl">${T('reserved')}</div><div class="cap-stat__val js-used">${used}/${displayMax}</div></div>
          <div><div class="cap-stat__lbl js-avail-lbl">${isBlocked ? T('blocked_at') : T('available')}</div><div class="cap-stat__val js-rem">${isBlocked ? used : (a.remaining||0)}</div></div>
          <div><div class="cap-stat__lbl">${T('occupancy')}</div><div class="cap-stat__val js-pct">${pct}%</div></div>
        </div>
        <div class="cap-bar"><div class="cap-bar__fill ${barCls} js-bar" style="width:${pct}%"></div></div>
        <div class="cap-actions-row">
          <button class="cap-btn ${incCls} js-inc" data-url-inc="${incRoute(a.schedule_id)}">${incLbl}</button>
          <button class="cap-btn cap-btn--danger js-block" data-url-block="${blkRoute(a.schedule_id)}" ${blkDis}><i class="fas fa-ban"></i> ${T('block')}</button>
          <button class="cap-btn cap-btn--ghost ms-auto js-det" data-url-det="${detRoute(a.schedule_id)}"><i class="fas fa-eye"></i> ${T('details')}</button>
        </div>
      </div>`;
    };

    const updateCountersFromDOM = () => {
      const leftCritical = $$('.cap-card').filter(c => {
        const [u, max] = (c.querySelector('.js-used')?.textContent || '0/0').split('/').map(x => parseInt(x, 10));
        const rem = Math.max(0, (max || 0) - (u || 0));
        const pct = (max || 0) > 0 ? Math.floor((u * 100) / max) : 0;
        return rem === 0 || rem <= 3 || pct >= 80;
      }).length;
      const c1 = $('#cap-count'),
        c2 = $('#cap-count-top');
      if (c1) c1.textContent = leftCritical;
      if (c2) c2.textContent = leftCritical;
    };

    // OPCIÓN 2: Solo usar alertas del servidor, ignorar cache mezclado
    const mountCards = () => {
      // Filtrar alertas inválidas (sin product_id válido)
      const validAlerts = serverRaw.filter(a => {
        const tid = a.product_id;
        return tid && tid !== 0 && tid !== null && tid !== undefined;
      });

      // Ordenar por fecha y nombre de tour
      const arr = validAlerts.sort((a, b) =>
        (a.date || '').localeCompare(b.date || '') || (a.tour || '').localeCompare(b.tour || '')
      );

      // Renderizar
      if (list) {
        list.innerHTML = arr.length ? arr.map(cardHTML).join('') :
          `<div class="cap-empty"><i class="fas fa-check-circle"></i><p>${T('no_alerts')}</p></div>`;
      }

      // Hidratación: asegurar data-tour desde el mapa
      $$('.cap-card').forEach(c => {
        if (!c.dataset.tour || c.dataset.tour === '0' || c.dataset.tour === '') {
          const sid = String(c.dataset.id || '');
          const tid = scheduleTourMap[sid] ?? null;
          if (tid) {
            c.dataset.tour = String(tid);
          }
        }
      });

      // Actualizar cache solo con alertas válidas del servidor
      const freshCache = {};
      validAlerts.forEach(a => {
        freshCache[a.key] = {
          data: a,
          exp: Date.now() + TTL_MS
        };
      });
      saveCache(freshCache);

      updateCountersFromDOM();
    };

    mountCards();

    $('#cap-markread')?.addEventListener('click', () => {
      if (list) list.innerHTML = `<div class="cap-empty"><i class="fas fa-check-circle"></i><p>${T('no_alerts')}</p></div>`;
      saveCache({});
      updateCountersFromDOM();
    });

    /* ===================== Modal Manager ===================== */
    const detailsEl = $('#capDetailsModal');
    const bodyEl = $('#capDetailsBody');
    let detailsModal = null,
      detailsAbort = null,
      detailsBusy = false,
      detailsTimer = null,
      lastClickTs = 0;

    const getModal = () => {
      if (!window.bootstrap) return null;
      if (!detailsModal) {
        detailsModal = bootstrap.Modal.getOrCreateInstance(detailsEl, {
          backdrop: true,
          keyboard: true,
          focus: true
        });
        detailsEl.addEventListener('hidden.bs.modal', () => {
          try {
            detailsAbort?.abort();
          } catch {}
          detailsAbort = null;
          detailsBusy = false;
          if (detailsTimer) {
            clearTimeout(detailsTimer);
            detailsTimer = null;
          }
          if (bodyEl) bodyEl.innerHTML = '';
          document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
          document.body.classList.remove('modal-open');
          document.body.style.removeProperty('padding-right');
        });
        detailsEl.addEventListener('shown.bs.modal', () => {
          document.body.classList.add('modal-open');
        });
      }
      return detailsModal;
    };

    const canTrigger = () => {
      const now = Date.now();
      if (now - lastClickTs < 350) return false;
      lastClickTs = now;
      return true;
    };

    const hideModalIfOpen = () => new Promise(resolve => {
      const m = getModal();
      if (!m) {
        resolve();
        return;
      }
      const el = detailsEl;
      if (el.classList.contains('show')) {
        const onHidden = () => {
          el.removeEventListener('hidden.bs.modal', onHidden);
          resolve();
        };
        el.addEventListener('hidden.bs.modal', onHidden);
        m.hide();
      } else {
        resolve();
      }
    });

    const renderDetails = (container, res) => {
      const rows = res?.data || [];
      if (!rows.length) {
        container.innerHTML = `<p class="text-muted mb-0 p-3">${T('operation_cancel')}</p>`;
        return;
      }
      let html = `<div class="table-responsive"><table class="table table-sm align-middle mb-0">
      <thead><tr><th>{{ __('m_notifications.table.date') }}</th><th>{{ __('m_notifications.table.tour') }}</th>
      <th class="text-end">${T('reserved')}</th><th class="text-end">{{ __('m_notifications.table.capacity') }}</th>
      <th class="text-end">${T('available')}</th><th class="text-end">${T('occupancy')}</th></tr></thead><tbody>`;
      rows.forEach(r => {
        const used = parseInt(r.used || 0, 10),
          max = parseInt(r.max || 0, 10);
        const rem = Math.max(0, max - used),
          pct = max > 0 ? Math.floor((used * 100) / max) : 0;
        html += `<tr><td>${(r.date||'').slice(0,10)}</td><td>${r.tour||'—'}</td>
        <td class="text-end">${used}</td><td class="text-end">${max}</td>
        <td class="text-end">${rem}</td><td class="text-end">${pct}%</td></tr>`;
      });
      html += `</tbody></table></div>`;
      container.innerHTML = html;
    };

    const applyResponse = (card, res) => {
      const used = parseInt(res.used || 0, 10);
      const max = parseInt(res.max_capacity || 0, 10);
      const remaining = parseInt(res.remaining || 0, 10);
      const pct = parseInt(res.pct || 0, 10);

      // Determinar estado basado en la respuesta del servidor
      const isBlocked = max === 0;
      const displayMax = isBlocked ? used : max;

      // Actualizar valores en el DOM
      const usedEl = card.querySelector('.js-used');
      const remEl = card.querySelector('.js-rem');
      const pctEl = card.querySelector('.js-pct');
      const barEl = card.querySelector('.js-bar');
      const availLblEl = card.querySelector('.js-avail-lbl');

      if (usedEl) usedEl.textContent = `${used}/${displayMax}`;
      if (remEl) remEl.textContent = isBlocked ? used : remaining;
      if (pctEl) pctEl.textContent = `${pct}%`;
      if (barEl) barEl.style.width = pct + '%';

      const chip = card.querySelector('.cap-chip');
      const incBtn = card.querySelector('.js-inc');
      const blkBtn = card.querySelector('.js-block');
      const barFillEl = card.querySelector('.cap-bar__fill');

      // Resetear todas las clases primero
      if (chip) chip.className = 'cap-chip';
      if (barFillEl) barFillEl.classList.remove('cap-bar__fill--blocked');
      card.classList.remove('cap-card--blocked');

      // Aplicar el estado correcto según respuesta del servidor
      if (isBlocked) {
        // Estado: BLOQUEADO (max = 0)
        if (chip) {
          chip.classList.add('cap-chip--danger');
          chip.textContent = T('sold_out');
        }
        if (incBtn) {
          incBtn.classList.remove('cap-btn--primary');
          incBtn.classList.add('cap-btn--success');
          incBtn.innerHTML = `<i class="fas fa-unlock"></i> ${T('unlock')}`;
        }
        if (blkBtn) blkBtn.disabled = true;
        if (barFillEl) barFillEl.classList.add('cap-bar__fill--blocked');
        card.classList.add('cap-card--blocked');
        if (availLblEl) availLblEl.textContent = T('blocked_at');
      } else if (remaining <= 3 || pct >= 80) {
        // Estado: CRÍTICO (casi lleno)
        if (chip) {
          chip.classList.add('cap-chip--near_capacity');
          chip.textContent = T('critical');
        }
        if (incBtn) {
          incBtn.classList.remove('cap-btn--success');
          incBtn.classList.add('cap-btn--primary');
          incBtn.innerHTML = `<i class="fas fa-edit"></i> ${T('expand')}`;
        }
        if (blkBtn) blkBtn.disabled = false;
        if (availLblEl) availLblEl.textContent = T('available');
      } else {
        // Estado: NORMAL (disponible)
        if (chip) {
          chip.classList.add('cap-chip--info');
          chip.textContent = T('alert');
        }
        if (incBtn) {
          incBtn.classList.remove('cap-btn--success');
          incBtn.classList.add('cap-btn--primary');
          incBtn.innerHTML = `<i class="fas fa-edit"></i> ${T('expand')}`;
        }
        if (blkBtn) blkBtn.disabled = false;
        if (availLblEl) availLblEl.textContent = T('available');
      }

      console.log('[CAPACITY] Card updated:', {
        used,
        max,
        remaining,
        pct,
        isBlocked,
        chip: chip?.className,
        incBtn: incBtn?.className
      });
    };

    const cardToAlert = (card) => {
      const [u, max] = (card.querySelector('.js-used')?.textContent || '0/0').split('/').map(x => parseInt(x, 10));
      const remaining = Math.max(0, (max || 0) - (u || 0));
      const pct = (max || 0) > 0 ? Math.floor((u * 100) / max) : 0;
      const type = remaining === 0 ? 'sold_out' : (remaining <= 3 || pct >= 80) ? 'near_capacity' : 'info';

      // Obtener product_id de la tarjeta
      const tourId = getTourIdForCard(card);

      return {
        key: card.dataset.key,
        product_id: tourId, // Incluir product_id
        schedule_id: parseInt(card.dataset.id, 10),
        date: card.dataset.date,
        tour: card.querySelector('.cap-card__title')?.textContent || '—',
        used: u,
        max: max,
        remaining,
        pct,
        type
      };
    };

    // Helper para obtener product_id siempre
    const getTourIdForCard = (card) => {
      const sid = String(card.dataset.id || '');
      const fromAttr = card.dataset.tour && card.dataset.tour !== '0' ? parseInt(card.dataset.tour, 10) : null;
      const fromMap = scheduleTourMap[sid] ? parseInt(scheduleTourMap[sid], 10) : null;
      return fromAttr || fromMap || null;
    };

    /* ===================== Delegación en tarjetas ===================== */
    list?.addEventListener('click', async (e) => {
      const card = e.target.closest('.cap-card');
      if (!card) return;

      if (e.target.closest('.cap-dismiss')) {
        removeFromCache(card.dataset.key);
        card.remove();
        updateCountersFromDOM();
        return;
      }

      if (e.target.closest('.js-det')) {
        if (!canTrigger()) return;
        try {
          if (window.Swal) Swal.close();
        } catch {}
        const m = getModal();
        if (!m) return;
        if (detailsBusy) {
          m.show();
          return;
        }

        let url = e.target.closest('.js-det').dataset.urlDet;
        if (!url) return Swal.fire(T('missing_route'), T('define_route_det'), 'warning');

        const tourId = getTourIdForCard(card);
        if (!tourId) {
          return Swal.fire(T('error'), 'No se pudo determinar el product_id para esta alerta.', 'error');
        }
        url += (url.includes('?') ? '&' : '?') + 'product_id=' + encodeURIComponent(tourId);

        try {
          detailsAbort?.abort();
        } catch {}
        detailsAbort = new AbortController();
        detailsBusy = true;

        if (bodyEl) {
          bodyEl.innerHTML = `<div class="p-3">
          <div class="d-flex align-items-center gap-2 mb-2">
            <div class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></div>
            <strong>${T('loading_details')}</strong>
          </div>
          <div class="progress" style="height:6px;"><div class="progress-bar progress-bar-striped progress-bar-animated" style="width:55%"></div></div>
        </div>`;
        }
        m.show();

        detailsTimer = setTimeout(() => {
          try {
            detailsAbort?.abort();
          } catch {}
        }, 10000);

        try {
          const res = await fetch(url, {
            headers: {
              'Accept': 'application/json'
            },
            signal: detailsAbort.signal
          });
          if (res.status === 422) {
            const j = await res.json().catch(() => null);
            const errs = j?.errors ? Object.values(j.errors).flat() : ['Unprocessable Entity'];
            bodyEl.innerHTML = `<p class="text-danger mb-0 p-3">${errs.join('<br>')}</p>`;
            return;
          }
          const ct = res.headers.get('content-type') || '';
          const data = ct.includes('application/json') ? await res.json() : {};
          if (!res.ok || (ct.includes('application/json') && data?.ok !== true)) throw new Error('bad_response');
          renderDetails(bodyEl, data);
        } catch (err) {
          if (err?.name === 'AbortError') {
            bodyEl.innerHTML = `<p class="text-muted mb-0 p-3">${T('operation_cancel')}</p>`;
          } else {
            bodyEl.innerHTML = `<p class="text-danger mb-0 p-3">${T('cannot_load')}</p>`;
          }
        } finally {
          detailsBusy = false;
          if (detailsTimer) {
            clearTimeout(detailsTimer);
            detailsTimer = null;
          }
        }
        return;
      }

      if (e.target.closest('.js-inc')) {
        await hideModalIfOpen();
        const url = e.target.closest('.js-inc').dataset.urlInc;
        if (!url) return Swal.fire(T('missing_route'), T('define_route_inc'), 'warning');

        const [used, currentMax] = (card.querySelector('.js-used')?.textContent || '0/0').split('/').map(x => parseInt(x, 10));
        const isSoldOrBlocked = currentMax === 0 || currentMax === used || card.querySelector('.cap-chip')?.classList.contains('cap-chip--danger');

        // SIEMPRE preguntar cuántos espacios agregar
        let suggestedAmount;
        let promptTitle;
        let promptLabel;

        if (isSoldOrBlocked) {
          // Si está bloqueado, sugerir +2 desde lo que ya está usado
          suggestedAmount = 2;
          promptTitle = `${T('unlock')} - ${T('modify_capacity')}`;
          promptLabel = `Actualmente: ${used} reservados (bloqueado). ¿Cuántos espacios liberar?`;
        } else {
          // Si está activo, sugerir +5 desde la capacidad actual
          suggestedAmount = 5;
          promptTitle = T('modify_capacity');
          promptLabel = `${T('quantity')} (${used}/${currentMax})`;
        }

        const {
          value
        } = await Swal.fire({
          title: promptTitle,
          html: `
          <p style="font-size:14px;color:#6b7280;margin-bottom:12px;">
            ${isSoldOrBlocked
              ? `<strong>OK Desbloquear y liberar espacios</strong><br>Número positivo: cuántos espacios totales tendrá disponible`
              : `${T('positive_expand')}<br>${T('negative_reduce')}`
            }
          </p>
        `,
          input: 'number',
          inputLabel: promptLabel,
          inputValue: suggestedAmount,
          inputAttributes: {
            step: 1,
            min: isSoldOrBlocked ? 1 : -999
          },
          showCancelButton: true,
          confirmButtonText: isSoldOrBlocked ? 'Desbloquear' : T('add'),
          cancelButtonText: T('cancel'),
          customClass: {
            confirmButton: isSoldOrBlocked ? 'btn btn-success' : 'btn btn-primary'
          }
        });

        if (value === undefined) return;
        const amount = parseInt(value, 10);
        if (isNaN(amount) || amount === 0) return Swal.fire(T('invalid_qty'), T('enter_int'), 'error');

        try {
          const tourId = getTourIdForCard(card);
          if (!tourId) {
            return Swal.fire(T('error'), 'No se pudo determinar el product_id para esta alerta.', 'error');
          }

          const res = await fetch(url, {
            method: 'PATCH',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': token,
              'Accept': 'application/json'
            },
            body: JSON.stringify({
              amount,
              date: card.dataset.date || null,
              product_id: tourId
            })
          });

          if (res.status === 422) {
            const j = await res.json().catch(() => null);
            const errs = j?.errors ? Object.values(j.errors).flat() : ['Unprocessable Entity'];
            return Swal.fire(T('error'), errs.join('<br>'), 'error');
          }

          if (!res.ok) {
            throw new Error(`HTTP ${res.status}`);
          }

          let data;
          try {
            data = await res.json();
          } catch (parseErr) {
            console.error('JSON parse error:', parseErr);
            throw new Error('Invalid JSON response');
          }

          if (data?.ok !== true) {
            console.error('Response data:', data);
            throw new Error(data?.message || 'Operation failed');
          }

          applyResponse(card, data);
          putInCache(cardToAlert(card));
          updateCountersFromDOM();

          await Swal.fire({
            icon: 'success',
            title: T('ready'),
            text: isSoldOrBlocked ? `Desbloqueado con ${data.max_capacity} espacios disponibles` : T('capacity_updated'),
            timer: 2500,
            showConfirmButton: false
          });

        } catch (err) {
          console.error('Increase error:', err);
          Swal.fire(T('error'), err.message || T('couldnt_update'), 'error');
        }
        return;
      }

      if (e.target.closest('.js-block')) {
        await hideModalIfOpen();
        const url = e.target.closest('.js-block').dataset.urlBlock;
        if (!url) return Swal.fire(T('missing_route'), T('define_route_blk'), 'warning');
        const dateStr = card.dataset.date || null;
        if (!dateStr) return Swal.fire(T('required_date'), T('required_date'), 'warning');

        const {
          isConfirmed
        } = await Swal.fire({
          title: T('block_date'),
          text: T('block_confirm'),
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: T('yes_block'),
          cancelButtonText: T('cancel')
        });
        if (!isConfirmed) return;

        try {
          const tourId = getTourIdForCard(card);
          if (!tourId) {
            return Swal.fire(T('error'), 'No se pudo determinar el product_id para esta alerta.', 'error');
          }

          const res = await fetch(url, {
            method: 'PATCH',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': token,
              'Accept': 'application/json'
            },
            body: JSON.stringify({
              date: dateStr,
              product_id: tourId
            })
          });

          if (res.status === 422) {
            const j = await res.json().catch(() => null);
            const errs = j?.errors ? Object.values(j.errors).flat() : ['Unprocessable Entity'];
            return Swal.fire(T('error'), errs.join('<br>'), 'error');
          }

          if (!res.ok) {
            throw new Error(`HTTP ${res.status}`);
          }

          let data;
          try {
            data = await res.json();
          } catch (parseErr) {
            console.error('JSON parse error:', parseErr);
            throw new Error('Invalid JSON response');
          }

          if (data?.ok !== true) {
            console.error('Response data:', data);
            throw new Error(data?.message || 'Operation failed');
          }

          applyResponse(card, data);
          putInCache(cardToAlert(card));
          updateCountersFromDOM();

          await Swal.fire({
            icon: 'success',
            title: T('blocked'),
            text: T('date_blocked'),
            timer: 2000,
            showConfirmButton: false
          });

        } catch (err) {
          console.error('Block error:', err);
          Swal.fire(T('error'), err.message || T('couldnt_block'), 'error');
        }
        return;
      }
    });
  })();
</script>
@endpush
