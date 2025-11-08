@push('scripts')
<script>
(function ensureSwal(cb){
  if (window.Swal) return cb();
  const s = document.createElement('script');
  s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
  s.onload = cb;
  document.head.appendChild(s);
})(function(){

document.addEventListener('DOMContentLoaded', () => {
  /* =========================
     Utilidades
     ========================= */
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

  const swalInfo = (title, text) =>
    Swal.fire({ icon:'info', title, text, confirmButtonColor:'#6c757d' });

  const swalSuccess = (text) =>
    Swal.fire({ icon:'success', title:@json(__('adminlte::adminlte.success')), text, confirmButtonColor:'#198754', timer:1800, showConfirmButton:false });

  const clamp = (v, min, max) => Math.max(min, Math.min(max, v));

  /* =========================
     I18N helpers (categorías y validaciones)
     ========================= */
  const $cfg = document.getElementById('mp-config');

  // Mapas inyectables desde Blade en data-attrs (opcionales)
  let CAT_BY_ID = {};
  let CAT_BY_CODE = {};
  try { CAT_BY_ID = JSON.parse($cfg?.getAttribute('data-catmap-byid')  || '{}') || {}; } catch(_){}
  try { CAT_BY_CODE = JSON.parse($cfg?.getAttribute('data-catmap-bycode')|| '{}') || {}; } catch(_){}

  // Fallbacks por código con traducciones de Laravel
  const DEFAULT_CODE_MAP = {
    adult:   @json(__('adminlte::adminlte.adult')),
    adults:  @json(__('adminlte::adminlte.adults')),
    kid:     @json(__('adminlte::adminlte.kid')),
    kids:    @json(__('adminlte::adminlte.kids')),
    child:   @json(__('adminlte::adminlte.kid')),
    children:@json(__('adminlte::adminlte.kids')),
    senior:  @json(__('adminlte::adminlte.senior') ?? 'Senior'),
    student: @json(__('adminlte::adminlte.student') ?? 'Student'),
  };
  CAT_BY_CODE = Object.assign({}, DEFAULT_CODE_MAP, CAT_BY_CODE);

  // Resolver nombre de categoría (para cualquier objeto-like de categoría)
  function resolveCategoryName(catLike) {
    const get = (p, d=null) => p.split('.').reduce((o,k)=> (o && o[k]!==undefined) ? o[k] : d, catLike);

    // 1) i18n embebido
    let name =
       get('i18n_name') ||
       get('name')      ||
       get('label')     ||
       get('category_name') ||
       get('category.name');

    if (name) return String(name);

    // 2) por ID
    const id = Number(get('category_id', get('id', 0))) || 0;
    if (id && CAT_BY_ID && CAT_BY_ID[id]) return String(CAT_BY_ID[id]);

    // 3) por code
    const code = get('code', null);
    if (code) {
      if (CAT_BY_CODE && CAT_BY_CODE[code]) return String(CAT_BY_CODE[code]);
      // fallback: prettify del code
      return String(code).replace(/[_-]+/g,' ').replace(/\b\w/g, m => m.toUpperCase());
    }

    // 4) prettify del slug
    const slug = String(get('category_slug', get('slug','')) || '');
    if (slug) {
      return slug.replace(/[_-]+/g,' ').replace(/\b\w/g, m => m.toUpperCase());
    }

    return 'Category';
  }

  // I18N de validaciones (sin hardcode)
  const I18N = {
    info: @json(__('adminlte::adminlte.info')),
    minAdults: @json(__('carts.validation.min_adults', ['min' => 2])),
    maxKids:   @json(__('carts.validation.max_kids',   ['max' => 2])),
    maxTotal:  @json(__('carts.validation.max_total',  ['max' => 12])),
  };

  /* =========================
     1) Confirmar reserva
     ========================= */
  const reservaForm = document.getElementById('confirm-reserva-form');
  if(reservaForm){
    reservaForm.addEventListener('submit', function(e){
      e.preventDefault();
      Swal.fire({
        title: @json(__('adminlte::adminlte.confirmReservationTitle')),
        text:  @json(__('adminlte::adminlte.confirmReservationText')),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#d33',
        confirmButtonText: @json(__('adminlte::adminlte.confirmReservationConfirm')),
        cancelButtonText: @json(__('adminlte::adminlte.confirmReservationCancel'))
      }).then((r) => { if(r.isConfirmed){ reservaForm.submit(); } });
    });
  }

  /* =========================
     2) Eliminar ítem (confirmación)
     ========================= */
  document.querySelectorAll('.delete-item-form').forEach(form => {
    form.addEventListener('submit', function(e){
      e.preventDefault();
      Swal.fire({
        title: @json(__('adminlte::adminlte.deleteItemTitle')),
        text:  @json(__('adminlte::adminlte.deleteItemText')),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: @json(__('adminlte::adminlte.deleteItemConfirm')),
        cancelButtonText: @json(__('adminlte::adminlte.deleteItemCancel'))
      }).then((r) => { if(r.isConfirmed){ form.submit(); } });
    });
  });

  /* =========================
     3) Previsualización Meeting Point
     ========================= */
  const pickupLabel = ($cfg?.dataset?.pickupLabel) || 'Pick-up';
  const updateMpInfo = (selectEl) => {
    if (!selectEl) return;
    let mplist = [];
    try { mplist = JSON.parse(selectEl.getAttribute('data-mplist') || '[]'); } catch (_) { mplist = []; }
    const box = document.querySelector(selectEl.getAttribute('data-target'));
    if (!box) return;

    const id = selectEl.value ? Number(selectEl.value) : null;
    const found = id ? mplist.find(m => Number(m.id) === id) : null;

    const nameEl = box.querySelector('.mp-name');
    const timeEl = box.querySelector('.mp-time');
    const addrEl = box.querySelector('.mp-addr');
    const linkEl = box.querySelector('.mp-link');

    if (found) {
      box.style.display = 'block';
      if (nameEl) nameEl.textContent = found.name || '';
      if (timeEl) timeEl.textContent = found.pickup_time ? (pickupLabel + ': ' + found.pickup_time) : '';
      if (addrEl) addrEl.innerHTML = found.description ? ('<i class="fas fa-map-marker-alt me-1"></i>' + found.description) : '';
      if (linkEl) {
        if (found.map_url) { linkEl.href = found.map_url; linkEl.style.display = 'inline-block'; }
        else { linkEl.style.display = 'none'; }
      }
    } else {
      box.style.display = 'none';
    }
  };
  document.querySelectorAll('.meetingpoint-select').forEach(sel => {
    updateMpInfo(sel);
    sel.addEventListener('change', () => updateMpInfo(sel));
  });

  /* =========================
     4) Tabs de Pickup (Hotel / Otro / Punto)
     ========================= */
  document.querySelectorAll('.pickup-tabs').forEach(group => {
    const itemId = group.dataset.item;
    const init = group.dataset.init || 'hotel';

    const setActive = (target) => {
      group.querySelectorAll('button').forEach(btn => btn.classList.remove('active','btn-secondary'));
      const activeBtn = group.querySelector(`[data-pickup-tab="${target}"]`);
      if (activeBtn) activeBtn.classList.add('active','btn-secondary');

      document
        .querySelectorAll(`#pickup-panes-${itemId} .pickup-pane`)
        .forEach(pane => pane.style.display = pane.id.includes(`-${target}-`) ? 'block' : 'none');

      // hidden flag para backend (solo se marca 1 cuando "custom")
      const isOtherHidden = document.getElementById(`is-other-hidden-${itemId}`);
      if (isOtherHidden) isOtherHidden.value = (target === 'custom') ? 1 : 0;

      // limpieza básica de selects cuando cambia a otro modo
      const hotelSelect = document.getElementById(`hotel-select-${itemId}`);
      const mpSelect    = document.getElementById(`meetingpoint-select-${itemId}`);
      if (target === 'hotel') {
        if (mpSelect) { mpSelect.value = ''; updateMpInfo(mpSelect); }
      } else if (target === 'custom') {
        if (hotelSelect) hotelSelect.value = '';
        if (mpSelect)   { mpSelect.value = ''; updateMpInfo(mpSelect); }
      } else if (target === 'mp') {
        if (hotelSelect) hotelSelect.value = '';
      }
    };

    group.querySelectorAll('button').forEach(btn => {
      btn.addEventListener('click', () => setActive(btn.dataset.pickupTab));
    });

    setActive(init);
  });

  /* =========================
     5) Evitar doble submit en formularios de edición
     ========================= */
  document.querySelectorAll('.edit-item-form').forEach(f => {
    f.addEventListener('submit', (e) => {
      // validación frontend (ver sección 6)
      if (!validateEditForm(f)) {
        e.preventDefault();
        return;
      }
      const btn = f.querySelector('button[type="submit"]');
      if (btn) {
        btn.disabled = true;
        btn.innerHTML =
          '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' +
          (@json(__('adminlte::adminlte.saving')));
      }
    });
  });

  /* =========================
     6) Validación Frontend (PAX)
        - Min 2 adultos
        - Máx 2 niños
        - Total <= 12
        - Ajuste live mientras el usuario digita
     ========================= */
  const PAX_MAX_TOTAL = 12;
  const PAX_MIN_ADULTS = 2;
  const PAX_MAX_KIDS = 2;

  function parseIntSafe(v){ const n = parseInt(v,10); return isNaN(n)?0:n; }

  function validateAndClampAdultsKids(adultsInput, kidsInput) {
    let a = clamp(parseIntSafe(adultsInput.value), 0, PAX_MAX_TOTAL);
    let k = clamp(parseIntSafe(kidsInput.value), 0, PAX_MAX_KIDS);

    // mínimo 2 adultos
    if (a < PAX_MIN_ADULTS) a = PAX_MIN_ADULTS;

    // no superar total
    if ((a + k) > PAX_MAX_TOTAL) {
      // reducir niños primero
      const extra = (a + k) - PAX_MAX_TOTAL;
      const newKids = clamp(k - extra, 0, PAX_MAX_KIDS);
      if (newKids < k) k = newKids;
      // Si aún supera, recortar adultos pero nunca < 2
      if ((a + k) > PAX_MAX_TOTAL) {
        a = clamp(PAX_MAX_TOTAL - k, PAX_MIN_ADULTS, PAX_MAX_TOTAL);
      }
    }

    adultsInput.value = a;
    kidsInput.value   = k;
  }

  function attachPaxListeners(scope){
    const adults = scope.querySelector('input[name="adults_quantity"]');
    const kids   = scope.querySelector('input[name="kids_quantity"]');
    if (!adults || !kids) return;

    // set min/max HTML también
    adults.setAttribute('min', String(PAX_MIN_ADULTS));
    adults.setAttribute('max', String(PAX_MAX_TOTAL));
    kids.setAttribute('min', '0');
    kids.setAttribute('max', String(PAX_MAX_KIDS));

    const revalidate = () => validateAndClampAdultsKids(adults, kids);
    ['input','change','blur'].forEach(ev => {
      adults.addEventListener(ev, revalidate);
      kids.addEventListener(ev, revalidate);
    });

    // validación inicial
    revalidate();
  }

  // aplicar a cada modal de edición
  document.querySelectorAll('.modal form.edit-item-form').forEach(form => attachPaxListeners(form));

  // validar antes de enviar (mensaje amable) — con I18N
  function validateEditForm(form){
    const adults = form.querySelector('input[name="adults_quantity"]');
    const kids   = form.querySelector('input[name="kids_quantity"]');
    if (!adults || !kids) return true;

    const a = parseIntSafe(adults.value);
    const k = parseIntSafe(kids.value);
    const total = a + k;

    if (a < PAX_MIN_ADULTS) {
      swalInfo(I18N.info, I18N.minAdults);
      adults.focus();
      return false;
    }
    if (k > PAX_MAX_KIDS) {
      swalInfo(I18N.info, I18N.maxKids);
      kids.focus();
      return false;
    }
    if (total > PAX_MAX_TOTAL) {
      swalInfo(I18N.info, I18N.maxTotal);
      adults.focus();
      return false;
    }
    return true;
  }

  /* =========================
     7) Código Promocional (Apply / Remove)
     ========================= */
  (function(){
    const promoBtn  = document.getElementById('toggle-promo');
    const promoIn   = document.getElementById('promo-code');
    const promoMsg  = document.getElementById('promo-message');
    const totalEl   = document.getElementById('cart-total');

    const baseError = @json(__('carts.messages.invalid_code'));

    if (!promoBtn || !promoIn || !totalEl) return;

    promoBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      const state = promoBtn.dataset.state || 'idle';

      if (state === 'applied') {
        try{
          const res  = await fetch(@json(route('public.carts.removePromo')), {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
          });
          const data = await res.json();
          if (res.ok && data.ok) {
            promoBtn.className   = 'btn btn-outline-primary';
            promoBtn.dataset.state = 'idle';
            promoBtn.textContent = @json(__('adminlte::adminlte.apply'));
            if (promoMsg) promoMsg.textContent = '';
            totalEl.textContent  = parseFloat(data.new_total).toFixed(2);
          } else {
            Swal.fire('Oops!', data?.message || baseError, 'error');
          }
        }catch(_){ Swal.fire('Oops!', baseError, 'error'); }
        return;
      }

      // apply
      const code = (promoIn.value || '').trim();
      if (!code) {
        return Swal.fire('Oops!', @json(__('carts.messages.enter_code')), 'info');
      }

      try{
        const res  = await fetch(@json(route('public.carts.applyPromo')), {
          method: 'POST',
          headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' },
          body: JSON.stringify({ code })
        });
        const data = await res.json();

        if (res.ok && data.ok) {
          promoBtn.className   = 'btn btn-outline-danger';
          promoBtn.dataset.state = 'applied';
          promoBtn.textContent = @json(__('adminlte::adminlte.remove'));
          if (promoMsg) promoMsg.innerHTML = '<i class="fas fa-check-circle me-1"></i>' + @json(__('carts.messages.code_applied'));
          totalEl.textContent  = parseFloat(data.new_total).toFixed(2);
        } else {
          Swal.fire('Oops!', data?.message || baseError, 'error');
        }
      }catch(_){ Swal.fire('Oops!', baseError, 'error'); }
    });
  })();

  /* =========================
     8) Timer (sincronizado con backend, con tope de 1 extensión)
     ========================= */
  (function(){
    const box = document.getElementById('cart-timer');
    if (!box) return;

    const remainingEl     = document.getElementById('cart-timer-remaining');
    const barEl           = document.getElementById('cart-timer-bar');
    const btnRefresh      = document.getElementById('cart-timer-refresh');
    const expireEndpoint  = box.getAttribute('data-expire-endpoint');
    const refreshEndpoint = box.getAttribute('data-refresh-endpoint');

    const totalSecondsCfg = Number(box.getAttribute('data-total-minutes') || '15') * 60;
    let serverExpires     = new Date(box.getAttribute('data-expires-at')).getTime();

    let maxExt = Number(box.dataset.extendMax || '1');
    let usedExt = Number(box.dataset.extendUsed || '0');

    const lblDefault  = btnRefresh?.getAttribute('data-label-default')  || 'Extender';
    const lblDisabled = btnRefresh?.getAttribute('data-label-disabled') || 'Ya extendido';

    const disableExtend = (perm=false) => {
      if (!btnRefresh) return;
      btnRefresh.disabled = true;
      btnRefresh.classList.add('disabled','btn-secondary');
      btnRefresh.classList.remove('btn-dark');
      btnRefresh.textContent = lblDisabled;
      if (perm) btnRefresh.setAttribute('aria-disabled','true');
    };
    const enableExtend = () => {
      if (!btnRefresh) return;
      btnRefresh.disabled = false;
      btnRefresh.classList.remove('disabled','btn-secondary');
      btnRefresh.classList.add('btn-dark');
      btnRefresh.textContent = lblDefault;
      btnRefresh.removeAttribute('aria-disabled');
    };
    if (usedExt >= maxExt) disableExtend(true); else enableExtend();

    const fmt = (sec) => {
      const s = Math.max(0, sec|0);
      const m = Math.floor(s / 60), r = s % 60;
      return String(m).padStart(2,'0') + ':' + String(r).padStart(2,'0');
    };
    const setBar = (remainingSec) => {
      const frac = Math.max(0, Math.min(1, remainingSec / totalSecondsCfg));
      if (barEl) barEl.style.width = (frac * 100).toFixed(2) + '%';
    };

    let rafId = null;
    const tick = () => {
      const now = Date.now();
      const remainingSec = Math.ceil((serverExpires - now) / 1000);
      if (remainingEl) remainingEl.textContent = fmt(remainingSec);
      setBar(remainingSec);
      if (remainingSec <= 0) { cancelAnimationFrame(rafId); return handleExpire(); }
      rafId = requestAnimationFrame(tick);
    };

    const handleExpire = async () => {
      try { await fetch(expireEndpoint, { method:'POST', headers:{ 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' } }); }
      catch(_){}
      location.reload();
    };

    const handleRefresh = async (e) => {
      e?.preventDefault?.();
      if (usedExt >= maxExt) {
        swalInfo(@json(__('adminlte::adminlte.info')), @json(__('carts.timer.extend_limit_reached')));
        disableExtend(true);
        return;
      }

      try {
        const res  = await fetch(refreshEndpoint, { method:'POST', headers:{ 'X-CSRF-TOKEN': csrf, 'Accept':'application/json' } });
        const data = await res.json();

        if (res.ok && data.ok) {
          serverExpires = new Date(data.expires_at).getTime();
          usedExt = Number(data.used ?? data.extended_count ?? (usedExt + 1));
          maxExt  = Number(data.max ?? maxExt);
          if (usedExt >= maxExt) disableExtend(true); else enableExtend();
          swalSuccess(data.message || @json(__('carts.messages.extend_success')));
          cancelAnimationFrame(rafId);
          tick();
        } else if (data.reason === 'limit_reached' || !data.ok) {
          swalInfo(@json(__('adminlte::adminlte.info')), data.message || @json(__('carts.timer.extend_limit_reached')));
          disableExtend(true);
        } else {
          location.reload();
        }
      } catch(_){ location.reload(); }
    };

    btnRefresh?.addEventListener('click', handleRefresh);
    tick();
  })();

}); // DOMContentLoaded
}); // ensureSwal
</script>
@endpush
