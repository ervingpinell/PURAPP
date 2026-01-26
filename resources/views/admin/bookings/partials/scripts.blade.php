{{-- resources/views/admin/bookings/partials/scripts.blade.php --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  // =========================================================================
  // CONFIG (desde Blade)
  // =========================================================================
  const LOCALE        = @json(app()->getLocale());
  const CURRENCY_SIGN = @json(config('app.currency_symbol', '$'));
  const CURRENCY_CODE = @json(config('app.currency_code', 'USD')); // si no existe en tu app, quedará 'USD'
  const NF = (() => {
    try { return new Intl.NumberFormat(LOCALE, { style:'currency', currency: CURRENCY_CODE }); }
    catch(e){ return { format:(n)=> `${CURRENCY_SIGN}${Number(n||0).toFixed(2)}` }; }
  })();
  const fmt = (n) => {
    try { return NF.format(Number(n||0)); }
    catch(e){ return `${CURRENCY_SIGN}${Number(n||0).toFixed(2)}`; }
  };

  // Helper SweetAlert (fallback básico)
  const ensureSwal = (cb) => {
    if (window.Swal) return cb();
    const s = document.createElement('script');
    s.src = "https://cdn.jsdelivr.net/npm/sweetalert2@11";
    s.async = true;
    s.onload = cb;
    document.head.appendChild(s);
  };
  const alertSuccess = (title, text, ms=2000) => ensureSwal(()=> Swal.fire({icon:'success', title, text, timer:ms, showConfirmButton:false}));
  const alertError   = (title, text, ms)      => ensureSwal(()=> Swal.fire({icon:'error',   title, text, ...(ms?{timer:ms, showConfirmButton:false}:{})}));
  const alertWarn    = (title, text, ms)      => ensureSwal(()=> Swal.fire({icon:'warning', title, text, ...(ms?{timer:ms, showConfirmButton:false}:{})}));

  // =========================================================================
  // SUCCESS & ERROR ALERTS
  // =========================================================================
  @if(session('success'))
    alertSuccess(@json(__('m_bookings.bookings.success.created')), @json(session('success')));
  @endif

  @if($errors->has('capacity'))
    alertError(@json(__('m_bookings.messages.capacity_exceeded')), @json($errors->first('capacity')));
  @endif

  @if(session('showEditModal'))
    const modalId = @json(session('showEditModal'));
    const editModal = document.getElementById('modalEdit' + modalId);
    if (editModal) new bootstrap.Modal(editModal).show();
  @endif

  // =========================================================================
  // CLOSE FILTERS BUTTON
  // =========================================================================
  const closeFiltersBtn   = document.getElementById('closeFiltersBtn');
  const advancedFiltersEl = document.getElementById('advancedFilters');
  closeFiltersBtn?.addEventListener('click', () => {
    if (!advancedFiltersEl) return;
    new bootstrap.Collapse(advancedFiltersEl, { toggle: true });
  });

  // =========================================================================
  // ZOOM FUNCTIONALITY (persistente)
  // =========================================================================
  const container   = document.getElementById('bookingsTableContainer');
  const zoomInBtn   = document.getElementById('zoomIn');
  const zoomOutBtn  = document.getElementById('zoomOut');
  const zoomResetBtn= document.getElementById('zoomReset');
  const ZOOM_KEY    = 'bookingsZoom';
  let currentZoom   = 1;
  const zoomStep    = 0.1, minZoom = 0.6, maxZoom = 1.4;

  const applyZoom = () => {
    if (!container) return;
    container.style.transformOrigin = '0 0';
    container.style.transform = `scale(${currentZoom})`;
  };

  if (container && zoomInBtn && zoomOutBtn && zoomResetBtn) {
    const savedZoom = parseFloat(localStorage.getItem(ZOOM_KEY) || '1');
    if (!Number.isNaN(savedZoom)) { currentZoom = Math.min(maxZoom, Math.max(minZoom, savedZoom)); }
    applyZoom();

    zoomInBtn.addEventListener('click', () => {
      if (currentZoom < maxZoom) {
        currentZoom = Math.min(maxZoom, currentZoom + zoomStep);
        localStorage.setItem(ZOOM_KEY, String(currentZoom));
        applyZoom();
      }
    });
    zoomOutBtn.addEventListener('click', () => {
      if (currentZoom > minZoom) {
        currentZoom = Math.max(minZoom, currentZoom - zoomStep);
        localStorage.setItem(ZOOM_KEY, String(currentZoom));
        applyZoom();
      }
    });
    zoomResetBtn.addEventListener('click', () => {
      currentZoom = 1;
      localStorage.setItem(ZOOM_KEY, '1');
      applyZoom();
    });
  }

  // =========================================================================
  // EDIT MODAL HANDLERS (dinámico por booking)
  // =========================================================================
  document.addEventListener('shown.bs.modal', (ev) => {
    const modalEl = ev.target;
    if (!modalEl.id || !/^modalEdit\d+$/.test(modalEl.id)) return;

    const form    = modalEl.querySelector('form');
    if (!form) return;

    const tourSel  = form.querySelector('select[name="product_id"]');
    const schedSel = form.querySelector('select[name="schedule_id"]');

    if (tourSel && schedSel && tourSel.dataset.bound !== '1') {
      tourSel.dataset.bound = '1';
      tourSel.addEventListener('change', () => {
        const opt = tourSel.options[tourSel.selectedIndex];
        let schedules = [];
        try { schedules = JSON.parse(opt?.dataset?.schedules || '[]') || []; } catch(e){ schedules = []; }
        schedSel.innerHTML = `<option value="">${@json(__('m_bookings.bookings.placeholders.select_schedule'))}</option>`;
        schedules.forEach(s => {
          const o = document.createElement('option');
          o.value = s.schedule_id;
          o.textContent = `${s.start_time} – ${s.end_time}`;
          schedSel.appendChild(o);
        });
        schedSel.value = '';
      });
    }

    // Toggle "otro hotel"
    const hotelSel   = form.querySelector('select[name="hotel_id"]');
    const otherWrap  = form.querySelector('[data-role="other-hotel-wrapper"]');
    const otherInput = form.querySelector('input[name="other_hotel_name"]');
    const isOtherHdn = form.querySelector('input[name="is_other_hotel"]');

    const toggleOther = () => {
      if (!hotelSel) return;
      const isOther = hotelSel.value === 'other';
      otherWrap?.classList.toggle('d-none', !isOther);
      if (isOtherHdn) isOtherHdn.value = isOther ? 1 : 0;
      if (!isOther && otherInput) otherInput.value = '';
    };

    if (hotelSel && hotelSel.dataset.bound !== '1') {
      hotelSel.dataset.bound = '1';
      hotelSel.addEventListener('change', toggleOther);
      toggleOther();
    }

    // Evitar doble submit
    const btnSubmit = form.querySelector('button[type="submit"]');
    if (form.dataset.submitBound !== '1') {
      form.dataset.submitBound = '1';
      form.addEventListener('submit', (e) => {
        if (form.dataset.submitted === 'true') { e.preventDefault(); return; }
        form.dataset.submitted = 'true';
        if (btnSubmit) {
          btnSubmit.disabled = true;
          btnSubmit.dataset.originalText = btnSubmit.innerHTML;
          btnSubmit.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i> ${@json(__('m_bookings.bookings.loading.updating'))}`;
        }
      });
    }
  });

  // =========================================================================
  // BADGE INTERACTIVE CLICK (delegado)
  // =========================================================================
  document.addEventListener('click', (ev) => {
    const badge = ev.target.closest('.badge-interactive');
    if (!badge) return;
    const modalTarget = badge.getAttribute('data-bs-target');
    if (!modalTarget) return;
    const modal = document.querySelector(modalTarget);
    if (modal) new bootstrap.Modal(modal).show();
  });

  // =========================================================================
  // REGISTER MODAL LOGIC
  // =========================================================================
  const regModal = document.getElementById('modalRegister');
  if (!regModal) return;

  const form = regModal.querySelector('#createBookingForm');
  const btnSubmit = form?.querySelector('button[type="submit"]');

  // Inicializar Select2 al abrir (si hay jQuery)
  regModal.addEventListener('shown.bs.modal', function() {
    if (typeof jQuery !== 'undefined') {
      const $userSel = jQuery('#userSelect');
      if ($userSel.length && !$userSel.data('select2')) {
        $userSel.select2({
          theme: 'bootstrap-5',
          dropdownParent: jQuery('#modalRegister'),
          placeholder: @json(__('m_bookings.bookings.placeholders.select_customer')),
          allowClear: true,
          width: '100%',
          matcher: function(params, data) {
            if (jQuery.trim(params.term) === '') return data;
            if (typeof data.text === 'undefined') return null;
            const term  = params.term.toLowerCase();
            const text  = (data.text||'').toLowerCase();
            const email = (data.element?.dataset?.email || '').toLowerCase();
            return (text.includes(term) || email.includes(term)) ? data : null;
          }
        });
      }
    }

    // Rebuild al mostrar
    if (tourSel?.value) rebuildSchedules();
    updateMpHelp();
  });

  // ===== Elementos del form de registro =====
  const userSelectEl   = document.getElementById('userSelect');
  const emailInput     = document.getElementById('customerEmailSearch');
  const btnValidate    = document.getElementById('btnValidateEmail');

  const dateInput      = form?.querySelector('#tourDate');
  const tourSel        = form?.querySelector('#selectTour');
  const schedSel       = form?.querySelector('#selectSchedule');

  const adultsInp      = form?.querySelector('#adultsQuantity');
  const kidsInp        = form?.querySelector('#kidsQuantity');

  const adultPriceDisp = form?.querySelector('#adultPriceDisplay');
  const kidPriceDisp   = form?.querySelector('#kidPriceDisplay');
  const subtotalDisp   = form?.querySelector('#subtotalDisplay');
  const totalDisp      = form?.querySelector('#totalDisplay');

  const promoInput     = form?.querySelector('#promoCodeInput');
  const btnApplyPromo  = form?.querySelector('#btnApplyPromo');
  const promoMessage   = form?.querySelector('#promoMessage');
  const promoDiscountWrapper = form?.querySelector('#promoDiscountWrapper');
  const promoDiscountDisplay = form?.querySelector('#promoDiscountDisplay');
  const promoDiscountLabel   = form?.querySelector('#promoDiscountLabel');

  // Ubicación
  const radioHotel     = form?.querySelector('#locationHotel');
  const radioMeeting   = form?.querySelector('#locationMeeting');
  const hotelSection   = form?.querySelector('#hotelSection');
  const meetingSection = form?.querySelector('#meetingPointSection');
  const hotelSel       = form?.querySelector('#selectHotel');
  const wrapOther      = form?.querySelector('#otherHotelRegisterWrapper');
  const hiddenOther    = form?.querySelector('#isOtherHotelRegister');
  const otherInput     = form?.querySelector('#other_hotel_name');
  const mpSel          = form?.querySelector('#meetingPointSelect');
  const mpHelp         = form?.querySelector('#meetingPointHelp');

  let currentAdultPrice = 0;
  let currentKidPrice   = 0;
  let appliedPromoCode  = '';
  let promoDiscount     = 0;
  let promoOperation    = '';

  // ===== Email: index para autoseleccionar =====
  const normalizeEmail = (x) => String(x||'').trim().toLowerCase();
  const EMAIL_INDEX = (() => {
    if (!userSelectEl) return {};
    const idx = {};
    [...userSelectEl.options].forEach(opt => {
      const email = normalizeEmail(opt.dataset.email);
      if (email) idx[email] = opt.value;
    });
    return idx;
  })();

  function selectByEmail(email){
    const id = EMAIL_INDEX[normalizeEmail(email)];
    if (!id) return false;
    if (typeof jQuery !== 'undefined') {
      jQuery(userSelectEl).val(id).trigger('change');
    } else {
      userSelectEl.value = id;
      userSelectEl.dispatchEvent(new Event('change', { bubbles:true }));
    }
    return true;
  }

  btnValidate?.addEventListener('click', () => {
    const email = emailInput?.value?.trim();
    if (!email) return alertWarn(@json(__('m_bookings.alerts.empty_field_title')), @json(__('m_bookings.alerts.empty_email')));
    const found = selectByEmail(email);
    if (found) {
      alertSuccess(@json(__('m_bookings.alerts.customer_found_title')), @json(__('m_bookings.alerts.customer_found_text')));
    } else {
      alertError(@json(__('m_bookings.alerts.not_found_title')), @json(__('m_bookings.alerts.not_found_email')));
    }
  });

  emailInput?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') { e.preventDefault(); btnValidate?.click(); }
  });

  userSelectEl?.addEventListener('change', ()=>{
    const opt  = userSelectEl.options[userSelectEl.selectedIndex];
    const mail = opt?.dataset?.email || '';
    if (emailInput) emailInput.value = mail;
  });

  // ===== Toggle secciones de ubicación =====
  const toggleLocation = () => {
    if (!radioHotel || !radioMeeting) return;
    const isHotel = radioHotel.checked;

    hotelSection?.classList.toggle('d-none', !isHotel);
    meetingSection?.classList.toggle('d-none', isHotel);

    if (isHotel) {
      if (mpSel) mpSel.value = '';
      if (mpHelp) mpHelp.innerHTML = '';
    } else {
      if (hotelSel) hotelSel.value = '';
      if (otherInput) otherInput.value = '';
      if (hiddenOther) hiddenOther.value = 0;
      wrapOther?.classList.add('d-none');
    }
  };
  radioHotel?.addEventListener('change', toggleLocation);
  radioMeeting?.addEventListener('change', toggleLocation);
  toggleLocation();

  // ===== "Otro hotel" =====
  const toggleOtherHotel = () => {
    if (!hotelSel || !wrapOther || !hiddenOther) return;
    const isOther = hotelSel.value === 'other';
    wrapOther.classList.toggle('d-none', !isOther);
    hiddenOther.value = isOther ? 1 : 0;
    if (!isOther && otherInput) otherInput.value = '';
  };
  hotelSel?.addEventListener('change', toggleOtherHotel);
  toggleOtherHotel();

  // =========================================================================
  // CÁLCULO DE TOTALES (con promo)
  // =========================================================================
  const calculateTotal = () => {
    if (!adultsInp || !kidsInp) return;

    const adults = parseInt(adultsInp.value || 0, 10);
    const kids   = parseInt(kidsInp.value || 0, 10);

    // Mínimo 1 pax
    if (adults === 0 && kids === 0) {
      adultsInp.value = 1;
      return calculateTotal();
    }

    const subtotal = (adults * currentAdultPrice) + (kids * currentKidPrice);
    let total = subtotal;

    // Promo
    if (appliedPromoCode && promoDiscount > 0) {
      if (promoOperation === 'subtract') {
        total = Math.max(0, subtotal - promoDiscount);
        if (promoDiscountDisplay) promoDiscountDisplay.value = `-${fmt(promoDiscount)}`;
        if (promoDiscountLabel)   promoDiscountLabel.textContent = @json(__('m_config.promocode.operations.discount')) + ` (${appliedPromoCode})`;
        if (promoDiscountDisplay) promoDiscountDisplay.style.color = 'green';
      } else if (promoOperation === 'add') {
        total = subtotal + promoDiscount;
        if (promoDiscountDisplay) promoDiscountDisplay.value = `+${fmt(promoDiscount)}`;
        if (promoDiscountLabel)   promoDiscountLabel.textContent = @json(__('m_config.promocode.operations.surcharge')) + ` (${appliedPromoCode})`;
        if (promoDiscountDisplay) promoDiscountDisplay.style.color = '#b45309';
      }
      if (promoDiscountWrapper) promoDiscountWrapper.style.display = 'block';
    } else {
      if (promoDiscountWrapper) promoDiscountWrapper.style.display = 'none';
    }

    if (adultPriceDisp) adultPriceDisp.value = fmt(currentAdultPrice);
    if (kidPriceDisp)   kidPriceDisp.value   = fmt(currentKidPrice);
    if (subtotalDisp)   subtotalDisp.value   = fmt(subtotal);
    if (totalDisp)      totalDisp.value      = fmt(total);
  };

  // =========================================================================
  // PROMO CODE: aplicar/verificar
  // =========================================================================
  const resetPromoUI = () => {
    appliedPromoCode = '';
    promoDiscount    = 0;
    promoOperation   = '';
    if (promoMessage)          promoMessage.innerHTML = '';
    if (promoDiscountWrapper)  promoDiscountWrapper.style.display = 'none';
    if (promoInput)            promoInput.value = '';
  };

  (btnApplyPromo)||{}; // hint

  btnApplyPromo?.addEventListener('click', async () => {
    const code = (promoInput?.value || '').trim().toUpperCase();

    if (!code) { resetPromoUI(); calculateTotal(); return; }

    const adults = parseInt(adultsInp?.value || 0, 10);
    const kids   = parseInt(kidsInp?.value || 0, 10);
    const subtotal = (adults * currentAdultPrice) + (kids * currentKidPrice);

    if (subtotal === 0) {
      return alertWarn(@json(__('m_bookings.alerts.select_tour_title')), @json(__('m_bookings.alerts.select_tour_text')));
    }

    try {
      const url = @json(route('admin.bookings.verifyPromoCode')) + '?code=' + encodeURIComponent(code) + '&subtotal=' + encodeURIComponent(subtotal);
      const response = await fetch(url, { headers: { 'Accept': 'application/json' }});
      const data = await response.json();

      if (data?.valid) {
        appliedPromoCode = code;
        promoDiscount    = Number(data.discount_amount || 0);
        promoOperation   = data.operation || 'subtract';

        const opText = promoOperation === 'add' ? @json(__('m_config.promocode.operations.surcharge')) : @json(__('m_config.promocode.operations.discount'));
        const opSign = promoOperation === 'add' ? '+' : '−';

        if (promoMessage) {
          promoMessage.innerHTML = `<div class="alert alert-success small mb-0">
            <i class="fas fa-check-circle me-1"></i>{{ __('m_bookings.promocode.applied') }}: ${opSign}${fmt(promoDiscount)} (${opText})
          </div>`;
        }

        calculateTotal();
        alertSuccess(@json(__('m_bookings.promocode.title_applied')), @json(__('m_bookings.promocode.text_applied')));
      } else {
        resetPromoUI();
        if (promoMessage) {
          promoMessage.innerHTML = `<div class="alert alert-danger small mb-0">
            <i class="fas fa-times-circle me-1"></i>${data?.message || @json(__('m_bookings.promocode.invalid_or_expired'))}
          </div>`;
        }
        calculateTotal();
        alertError(@json(__('m_bookings.promocode.title_invalid')), data?.message || @json(__('m_bookings.promocode.invalid_or_expired')));
      }
    } catch (error) {
      console.error('Error verifying promo code:', error);
      alertError(@json(__('m_bookings.promocode.title_error')), @json(__('m_bookings.promocode.check_failed')));
    }
  });

  // =========================================================================
  // Build schedules al cambiar tour
  // =========================================================================
  const rebuildSchedules = () => {
    if (!tourSel || !schedSel) return;
    const opt = tourSel.selectedOptions[0];

    currentAdultPrice = parseFloat(opt?.getAttribute('data-adult-price') || '0') || 0;
    currentKidPrice   = parseFloat(opt?.getAttribute('data-kid-price')   || '0') || 0;

    let list = [];
    try { list = JSON.parse(opt?.getAttribute('data-schedules') || '[]') || []; } catch(e){ list = []; }

    schedSel.innerHTML = `<option value="">${@json(__('m_bookings.bookings.placeholders.select_schedule'))}</option>`;
    list.forEach(s => {
      const o = document.createElement('option');
      o.value = s.schedule_id;
      o.textContent = `${s.start_time} – ${s.end_time}`;
      schedSel.appendChild(o);
    });

    resetPromoUI();
    calculateTotal();
  };

  tourSel?.addEventListener('change', rebuildSchedules);
  adultsInp?.addEventListener('input', calculateTotal);
  kidsInp?.addEventListener('input', calculateTotal);

  // Evitar 0 y 0
  const validateMinimum = () => {
    if (!adultsInp || !kidsInp) return;
    const adults = parseInt(adultsInp.value || 0, 10);
    const kids   = parseInt(kidsInp.value || 0, 10);
    if (adults === 0 && kids === 0) {
      adultsInp.value = 1;
      calculateTotal();
    }
  };
  adultsInp?.addEventListener('blur', validateMinimum);
  kidsInp?.addEventListener('blur', validateMinimum);

  // =========================================================================
  // Meeting point help
  // =========================================================================
  const updateMpHelp = () => {
    if (!mpSel || !mpHelp) return;
    const opt = mpSel.options[mpSel.selectedIndex];
    if (!opt || !opt.value) { mpHelp.innerHTML = ''; return; }
    const time = opt.getAttribute('data-time') || '';
    const addr = opt.getAttribute('data-description') || '';
    const map  = opt.getAttribute('data-map') || '';
    let html = '';
    if (time) html += `<div><i class="far fa-clock me-1"></i><strong>{{ __('m_bookings.meeting.time') }}:</strong> ${time}</div>`;
    if (addr) html += `<div><i class="fas fa-map-pin me-1"></i>${addr}</div>`;
    if (map)  html += `<div><a href="${map}" target="_blank" rel="noopener"><i class="fas fa-external-link-alt me-1"></i>{{ __('m_bookings.meeting.view_map') }}</a></div>`;
    mpHelp.innerHTML = html;
  };
  mpSel?.addEventListener('change', updateMpHelp);

  // =========================================================================
  // Submit: inyectar promo code aplicado al backend
  // =========================================================================
  form?.addEventListener('submit', function() {
    if (appliedPromoCode && promoInput) {
      promoInput.value = appliedPromoCode;
    }
  });
});
</script>
