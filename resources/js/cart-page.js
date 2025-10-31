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
  /* ===== Confirm booking ===== */
  const reservaForm = document.getElementById('confirm-reserva-form');
  if(reservaForm){
    reservaForm.addEventListener('submit', function(e){
      e.preventDefault();
      Swal.fire({
        title: @json(__('adminlte::adminlte.confirmReservationTitle')),
        text: @json(__('adminlte::adminlte.confirmReservationText')),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#d33',
        confirmButtonText: @json(__('adminlte::adminlte.confirmReservationConfirm')),
        cancelButtonText: @json(__('adminlte::adminlte.confirmReservationCancel'))
      }).then((r) => { if(r.isConfirmed){ reservaForm.submit(); } });
    });
  }

  /* ===== Delete item ===== */
  document.querySelectorAll('.delete-item-form').forEach(form => {
    form.addEventListener('submit', function(e){
      e.preventDefault();
      Swal.fire({
        title: @json(__('adminlte::adminlte.deleteItemTitle')),
        text: @json(__('adminlte::adminlte.deleteItemText')),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: @json(__('adminlte::adminlte.deleteItemConfirm')),
        cancelButtonText: @json(__('adminlte::adminlte.deleteItemCancel'))
      }).then((r) => { if(r.isConfirmed){ form.submit(); } });
    });
  });

  /* ===== Meeting Point preview (usa mpListJson ya traducido) ===== */
  const pickupLabel = (document.getElementById('mp-config')?.dataset?.pickupLabel) || 'Pick-up';
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

  /* ===== Pickup tabs ===== */
  const activatePickupTab = (group, tab) => {
    const itemId = group.getAttribute('data-item');
    const panes = document.getElementById('pickup-panes-' + itemId);
    if (!panes) return;

    group.querySelectorAll('[data-pickup-tab]').forEach(btn => {
      btn.classList.toggle('btn-secondary', btn.getAttribute('data-pickup-tab') === tab);
      btn.classList.toggle('btn-outline-secondary', btn.getAttribute('data-pickup-tab') !== tab);
    });

    panes.querySelectorAll('.pickup-pane').forEach(p => p.style.display = 'none');
    const showPane = document.getElementById('pane-' + tab + '-' + itemId);
    if (showPane) showPane.style.display = 'block';

    const isOtherHidden = document.getElementById('is-other-hidden-' + itemId);
    const hotelSelect   = document.getElementById('hotel-select-' + itemId);
    const customInput   = document.getElementById('custom-hotel-input-' + itemId);
    const mpSelect      = document.getElementById('meetingpoint-select-' + itemId);

    if (hotelSelect) hotelSelect.value = hotelSelect.value;
    if (customInput) { /* keep text */ }
    if (mpSelect) mpSelect.value = mpSelect.value;

    if (tab === 'hotel') {
      if (isOtherHidden) isOtherHidden.value = 0;
      if (mpSelect) mpSelect.value = '';
      updateMpInfo(mpSelect);
    } else if (tab === 'custom') {
      if (isOtherHidden) isOtherHidden.value = 1;
      if (hotelSelect) hotelSelect.value = '';
      if (mpSelect) mpSelect.value = '';
      updateMpInfo(mpSelect);
    } else if (tab === 'mp') {
      if (isOtherHidden) isOtherHidden.value = 0;
      if (hotelSelect) hotelSelect.value = '';
    }
  };

  document.querySelectorAll('.pickup-tabs').forEach(group => {
    const init = group.getAttribute('data-init') || 'hotel';
    activatePickupTab(group, init);
    group.querySelectorAll('[data-pickup-tab]').forEach(btn => {
      btn.addEventListener('click', () => activatePickupTab(group, btn.getAttribute('data-pickup-tab')));
    });
  });

  /* ===== Prevent double submit in modals ===== */
  document.querySelectorAll('.edit-item-form').forEach(f => {
    f.addEventListener('submit', () => {
      const btn = f.querySelector('button[type="submit"]');
      if (btn) {
        btn.disabled = true;
        btn.innerHTML =
          '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' +
          (@json(__('adminlte::adminlte.saving')));
      }
    });
  });

  /* ===== Promo code toggle ===== */
  {
    const toggleBtn   = document.getElementById('toggle-promo');
    const codeInput   = document.getElementById('promo-code');
    const msgBox      = document.getElementById('promo-message');
    const totalEl     = document.getElementById('cart-total');
    const hiddenCode  = document.getElementById('promo_code_hidden');

    const setMsg = (ok, text) => {
      msgBox.classList.remove('text-success','text-danger');
      msgBox.classList.add(ok ? 'text-success' : 'text-danger');
      msgBox.innerHTML = text;
    };

    const baseTotal = () => {
      const rows = Array.from(document.querySelectorAll('.cart-item-row, .cart-item-card'));
      const seen = new Set(); let sum = 0;
      rows.forEach(el => {
        const id = el.dataset.itemId || '';
        if (!id || seen.has(id)) return; seen.add(id);
        const v = parseFloat(el.dataset.subtotal || '0'); if (!isNaN(v)) sum += v;
      });
      return Math.round(sum * 100) / 100;
    };

    const setState = (applied, code, newTotal) => {
      toggleBtn.dataset.state = applied ? 'applied' : 'idle';
      toggleBtn.textContent = applied ? (@json(__('adminlte::adminlte.remove'))) : (@json(__('adminlte::adminlte.apply')));
      toggleBtn.classList.toggle('btn-outline-danger', applied);
      toggleBtn.classList.toggle('btn-outline-primary', !applied);
      hiddenCode.value = applied ? (code || '') : '';
      if (typeof newTotal === 'number') totalEl.textContent = newTotal.toFixed(2);
    };

    const applyCode = async (code) => {
      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const res = await fetch(@json(route('public.carts.applyPromo')), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
        body: JSON.stringify({ code })
      });
      return res.json();
    };

    const removeCode = async () => {
      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const res = await fetch(@json(route('public.carts.removePromo')), {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
      });
      return res.json();
    };

    toggleBtn?.addEventListener('click', async () => {
      const state = toggleBtn.dataset.state || 'idle';

      if (state === 'applied') {
        try {
          const data = await removeCode();
          setMsg(true, `<i class="fas fa-check-circle me-1"></i>${data?.message || @json(__('carts.messages.code_removed'))}`);
          setState(false, '', baseTotal());
        } catch {
          setMsg(false, '<i class="fas fa-times-circle me-1"></i>' + @json(__('carts.messages.code_remove_failed')));
        }
        return;
      }

      const code = (codeInput?.value || '').trim();
      if (!code) {
        setMsg(false, '<i class="fas fa-times-circle me-1"></i>' + @json(__('carts.messages.enter_code')));
        return;
      }

      try {
        const data = await applyCode(code);
        if (!data?.ok) {
          setMsg(false, `<i class="fas fa-times-circle me-1"></i>${data?.message || @json(__('carts.messages.invalid_code'))}`);
          setState(false, '', baseTotal());
        } else {
          setMsg(true, `<i class="fas fa-check-circle me-1"></i>${data?.message || @json(__('carts.messages.code_applied'))}`);
          const newTotal = Number(data?.new_total ?? baseTotal());
          setState(true, data?.code || code, newTotal);
        }
      } catch {
        setMsg(false, '<i class="fas fa-times-circle me-1"></i>' + @json(__('carts.messages.code_apply_failed')));
      }
    });
  }

  /* ===== Timer countdown (con control de extensión única y estado server-driven) ===== */
  (function(){
    const box = document.getElementById('cart-timer');
    if (!box) return;

    const csrf            = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const remainingEl     = document.getElementById('cart-timer-remaining');
    const barEl           = document.getElementById('cart-timer-bar');
    const btnRefresh      = document.getElementById('cart-timer-refresh');
    const expireEndpoint  = box.getAttribute('data-expire-endpoint');
    const refreshEndpoint = box.getAttribute('data-refresh-endpoint');

    const totalSecondsCfg = Number(box.getAttribute('data-total-minutes') || '15') * 60;
    let serverExpires     = new Date(box.getAttribute('data-expires-at')).getTime();

    // Estado desde backend
    const maxExt   = Number(box.dataset.extendMax || '1');
    let usedExt    = Number(box.dataset.extendUsed || '0');

    const lblDefault  = btnRefresh?.getAttribute('data-label-default')  || 'Extender';
    const lblDisabled = btnRefresh?.getAttribute('data-label-disabled') || 'Ya extendido';

    const disableExtend = (permanent = false) => {
      if (!btnRefresh) return;
      btnRefresh.disabled = true;
      btnRefresh.classList.add('disabled','btn-secondary');
      btnRefresh.classList.remove('btn-dark');
      btnRefresh.textContent = lblDisabled;
      if (permanent) btnRefresh.setAttribute('aria-disabled', 'true');
    };

    const enableExtend = () => {
      if (!btnRefresh) return;
      btnRefresh.disabled = false;
      btnRefresh.classList.remove('disabled','btn-secondary');
      btnRefresh.classList.add('btn-dark');
      btnRefresh.textContent = lblDefault;
      btnRefresh.removeAttribute('aria-disabled');
    };

    // Arranque: respeta el estado del backend
    if (usedExt >= maxExt) disableExtend(true); else enableExtend();

    let rafId = null;
    const fmt = (sec) => {
      const s = Math.max(0, sec|0);
      const m = Math.floor(s / 60);
      const r = s % 60;
      return String(m).padStart(2,'0') + ':' + String(r).padStart(2,'0');
    };
    const setBar = (remainingSec) => {
      const frac = Math.max(0, Math.min(1, remainingSec / totalSecondsCfg));
      if (barEl) barEl.style.width = (frac * 100).toFixed(2) + '%';
    };

    const tick = () => {
      const now = Date.now();
      const remainingSec = Math.ceil((serverExpires - now) / 1000);
      if (remainingEl) remainingEl.textContent = fmt(remainingSec);
      setBar(remainingSec);
      if (remainingSec <= 0) { cancelAnimationFrame(rafId); return handleExpire(); }
      rafId = requestAnimationFrame(tick);
    };

    const handleExpire = async () => {
      try {
        await fetch(expireEndpoint, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
      } catch {}
      location.reload();
    };

    const handleRefresh = async (e) => {
      e?.preventDefault?.();

      // Bloqueo UI si ya no hay extensiones disponibles
      if (usedExt >= maxExt) {
        Swal.fire({
          icon: 'info',
          title: @json(__('adminlte::adminlte.info')),
          text: @json(__('carts.timer.extend_limit_reached')),
          confirmButtonColor: '#6c757d'
        });
        disableExtend(true);
        return;
      }

      try {
        const res = await fetch(refreshEndpoint, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
        });

        if (!res.ok) {
          if ([400,403,429].includes(res.status)) {
            Swal.fire({
              icon: 'info',
              title: @json(__('adminlte::adminlte.info')),
              text: @json(__('carts.timer.extend_limit_reached')),
              confirmButtonColor: '#6c757d'
            });
            usedExt = maxExt;
            disableExtend(true);
            return;
          }
          return location.reload();
        }

        const data = await res.json();

        // Convención:
        // { ok:true, expires_at:"...", extended:true, used:1, max:1 }
        // { ok:false, reason:"limit_reached", message:"..." }
        if (data?.ok && data?.expires_at) {
          serverExpires = new Date(data.expires_at).getTime();
          if (data.extended === true) {
            usedExt = Number(data.used ?? (usedExt + 1));
          } else {
            usedExt += 1;
          }
          if (usedExt >= Number(data.max ?? maxExt)) {
            disableExtend(true);
          } else {
            enableExtend();
          }
          if (rafId) cancelAnimationFrame(rafId);
          tick();
        } else if (data?.reason === 'limit_reached') {
          Swal.fire({
            icon: 'info',
            title: @json(__('adminlte::adminlte.info')),
            text: data?.message || @json(__('carts.timer.extend_limit_reached')),
            confirmButtonColor: '#6c757d'
          });
          usedExt = maxExt;
          disableExtend(true);
        } else {
          location.reload();
        }
      } catch {
        location.reload();
      }
    };

    btnRefresh?.addEventListener('click', handleRefresh);
    tick();
  })();

}); // DOMContentLoaded
}); // ensureSwal
</script>
@endpush
