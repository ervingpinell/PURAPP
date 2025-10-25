{{-- resources/views/admin/bookings/partials/scripts.blade.php --}}

<script>
document.addEventListener('DOMContentLoaded', () => {

  // =========================================================================
  // SUCCESS & ERROR ALERTS
  // =========================================================================

  @if(session('success'))
    Swal.fire({
      icon: 'success',
      title: '{{ __('m_bookings.bookings.success.created') }}',
      text: '{{ session('success') }}',
      confirmButtonColor: '#3085d6',
      confirmButtonText: 'OK'
    });
  @endif

  @if($errors->has('capacity'))
    Swal.fire({
      icon: 'error',
      title: '{{ __('m_bookings.messages.capacity_exceeded') }}',
      text: @json($errors->first('capacity')),
      confirmButtonColor: '#d33'
    });
  @endif

  @if(session('showEditModal'))
    const modalId = '{{ session('showEditModal') }}';
    const editModal = document.getElementById('modalEdit' + modalId);
    if (editModal) {
      new bootstrap.Modal(editModal).show();
    }
  @endif

  // =========================================================================
  // CLOSE FILTERS BUTTON
  // =========================================================================

  const closeFiltersBtn = document.getElementById('closeFiltersBtn');
  const advancedFilters = document.getElementById('advancedFilters');

  if (closeFiltersBtn && advancedFilters) {
    closeFiltersBtn.addEventListener('click', () => {
      new bootstrap.Collapse(advancedFilters, { toggle: true });
    });
  }

  // =========================================================================
  // ZOOM FUNCTIONALITY
  // =========================================================================

  const container = document.getElementById('bookingsTableContainer');
  let currentZoom = 1;
  const zoomStep = 0.1;
  const minZoom = 0.6;
  const maxZoom = 1.4;

  const zoomInBtn = document.getElementById('zoomIn');
  const zoomOutBtn = document.getElementById('zoomOut');
  const zoomResetBtn = document.getElementById('zoomReset');

  if (container && zoomInBtn && zoomOutBtn && zoomResetBtn) {
    zoomInBtn.addEventListener('click', () => {
      if (currentZoom < maxZoom) {
        currentZoom += zoomStep;
        container.style.transform = `scale(${currentZoom})`;
        localStorage.setItem('bookingsZoom', currentZoom);
      }
    });

    zoomOutBtn.addEventListener('click', () => {
      if (currentZoom > minZoom) {
        currentZoom -= zoomStep;
        container.style.transform = `scale(${currentZoom})`;
        localStorage.setItem('bookingsZoom', currentZoom);
      }
    });

    zoomResetBtn.addEventListener('click', () => {
      currentZoom = 1;
      container.style.transform = 'scale(1)';
      localStorage.setItem('bookingsZoom', 1);
    });

    const savedZoom = localStorage.getItem('bookingsZoom');
    if (savedZoom) {
      currentZoom = parseFloat(savedZoom);
      container.style.transform = `scale(${currentZoom})`;
    }
  }

  // =========================================================================
  // EDIT MODAL HANDLERS (Dynamic for each booking)
  // =========================================================================

  document.addEventListener('shown.bs.modal', (ev) => {
    const modalEl = ev.target;
    if (!modalEl.id || !modalEl.id.startsWith('modalEdit')) return;

    const form = modalEl.querySelector('form');
    if (!form) return;

    const tourSel = form.querySelector('select[name="tour_id"]');
    const schedSel = form.querySelector('select[name="schedule_id"]');

    if (tourSel && schedSel && tourSel.dataset.bound !== '1') {
      tourSel.dataset.bound = '1';

      tourSel.addEventListener('change', () => {
        const opt = tourSel.options[tourSel.selectedIndex];
        const schedules = JSON.parse(opt?.dataset?.schedules || '[]');

        schedSel.innerHTML = '<option value="">{{ __('m_bookings.bookings.placeholders.select_schedule') }}</option>';
        schedules.forEach(s => {
          const o = document.createElement('option');
          o.value = s.schedule_id;
          o.textContent = `${s.start_time} – ${s.end_time}`;
          schedSel.appendChild(o);
        });

        schedSel.value = '';
      });
    }

    const hotelSel = form.querySelector('select[name="hotel_id"]');
    const otherWrap = form.querySelector('[data-role="other-hotel-wrapper"]');
    const otherInput = form.querySelector('input[name="other_hotel_name"]');
    const isOtherHidden = form.querySelector('input[name="is_other_hotel"]');

    const toggleOther = () => {
      if (!hotelSel) return;
      const isOther = hotelSel.value === 'other';
      if (otherWrap) {
        otherWrap.classList.toggle('d-none', !isOther);
      }
      if (isOtherHidden) {
        isOtherHidden.value = isOther ? 1 : 0;
      }
      if (!isOther && otherInput) {
        otherInput.value = '';
      }
    };

    if (hotelSel && hotelSel.dataset.bound !== '1') {
      hotelSel.dataset.bound = '1';
      hotelSel.addEventListener('change', toggleOther);
      toggleOther();
    }

    const btnSubmit = form.querySelector('button[type="submit"]');

    if (form.dataset.submitBound !== '1') {
      form.dataset.submitBound = '1';

      form.addEventListener('submit', (e) => {
        if (form.dataset.submitted === 'true') {
          e.preventDefault();
          return;
        }

        form.dataset.submitted = 'true';

        if (btnSubmit) {
          btnSubmit.disabled = true;
          btnSubmit.dataset.originalText = btnSubmit.innerHTML;
          btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> {{ __('m_bookings.bookings.loading.updating') }}';
        }
      });
    }
  });

  // =========================================================================
  // BADGE INTERACTIVE CLICK
  // =========================================================================

  document.querySelectorAll('.badge-interactive').forEach(badge => {
    badge.addEventListener('click', function() {
      const modalTarget = this.getAttribute('data-bs-target');
      if (modalTarget) {
        const modal = document.querySelector(modalTarget);
        if (modal) {
          new bootstrap.Modal(modal).show();
        }
      }
    });
  });

  // =========================================================================
  // REGISTER MODAL LOGIC
  // =========================================================================

  const regModal = document.getElementById('modalRegister');
  if (!regModal) return;

  const form = regModal.querySelector('#createBookingForm');
  const btnSubmit = form?.querySelector('button[type="submit"]');

  // Wait for modal to be shown before initializing Select2
  regModal.addEventListener('shown.bs.modal', function() {
    // Initialize Select2 - SOLO SI EXISTE jQuery
    if (typeof jQuery !== 'undefined') {
      const userSel = jQuery('#userSelect');
      if (userSel.length && !userSel.data('select2')) {
        userSel.select2({
          theme: 'bootstrap-5',
          dropdownParent: jQuery('#modalRegister'),
          placeholder: '{{ __('m_bookings.bookings.placeholders.select_customer') }}',
          allowClear: true,
          width: '100%',
          matcher: function(params, data) {
            if (jQuery.trim(params.term) === '') return data;
            if (typeof data.text === 'undefined') return null;
            const term = params.term.toLowerCase();
            const text = data.text.toLowerCase();
            const email = (data.element?.dataset?.email || '').toLowerCase();
            return (text.includes(term) || email.includes(term)) ? data : null;
          }
        });
      }
    }
  });

  // Elements
  const userSelectEl = document.getElementById('userSelect');
  const emailInput = document.getElementById('customerEmailSearch');
  const btnValidate = document.getElementById('btnValidateEmail');
  const dateInput = form?.querySelector('#tourDate');
  const tourSel = form?.querySelector('#selectTour');
  const schedSel = form?.querySelector('#selectSchedule');
  const adultsInp = form?.querySelector('#adultsQuantity');
  const kidsInp = form?.querySelector('#kidsQuantity');
  const adultPriceDisp = form?.querySelector('#adultPriceDisplay');
  const kidPriceDisp = form?.querySelector('#kidPriceDisplay');
  const subtotalDisp = form?.querySelector('#subtotalDisplay');
  const totalDisp = form?.querySelector('#totalDisplay');
  const promoInput = form?.querySelector('#promoCodeInput');
  const btnApplyPromo = form?.querySelector('#btnApplyPromo');
  const promoMessage = form?.querySelector('#promoMessage');
  const promoDiscountWrapper = form?.querySelector('#promoDiscountWrapper');
  const promoDiscountDisplay = form?.querySelector('#promoDiscountDisplay');
  const promoDiscountLabel = form?.querySelector('#promoDiscountLabel');

  // Location elements
  const radioHotel = form?.querySelector('#locationHotel');
  const radioMeeting = form?.querySelector('#locationMeeting');
  const hotelSection = form?.querySelector('#hotelSection');
  const meetingSection = form?.querySelector('#meetingPointSection');
  const hotelSel = form?.querySelector('#selectHotel');
  const wrapOther = form?.querySelector('#otherHotelRegisterWrapper');
  const hiddenOther = form?.querySelector('#isOtherHotelRegister');
  const otherInput = form?.querySelector('#other_hotel_name');
  const mpSel = form?.querySelector('#meetingPointSelect');
  const mpHelp = form?.querySelector('#meetingPointHelp');

  let currentAdultPrice = 0;
  let currentKidPrice = 0;
  let appliedPromoCode = '';
  let promoDiscount = 0;
  let promoOperation = '';

  // Email validation
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
      userSelectEl.dispatchEvent(new Event('change',{bubbles:true}));
    }
    return true;
  }

  btnValidate?.addEventListener('click', () => {
    const email = emailInput?.value?.trim();
    if (!email) {
      Swal.fire({
        icon: 'warning',
        title: 'Campo vacío',
        text: 'Por favor ingrese un correo electrónico.'
      });
      return;
    }

    const found = selectByEmail(email);
    if (found) {
      Swal.fire({
        icon: 'success',
        title: 'Cliente encontrado',
        text: 'El cliente ha sido seleccionado automáticamente.',
        timer: 2000,
        showConfirmButton: false
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'No encontrado',
        text: 'No existe un cliente registrado con ese correo.'
      });
    }
  });

  emailInput?.addEventListener('keydown', (e)=>{
    if (e.key === 'Enter') {
      e.preventDefault();
      btnValidate?.click();
    }
  });

  userSelectEl?.addEventListener('change', ()=>{
    const opt = userSelectEl.options[userSelectEl.selectedIndex];
    const mail = opt?.dataset?.email || '';
    if (emailInput) emailInput.value = mail;
  });

  // Toggle location sections
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

  // Other hotel toggle
  const toggleOtherHotel = () => {
    if (!hotelSel || !wrapOther || !hiddenOther) return;
    const isOther = hotelSel.value === 'other';
    wrapOther.classList.toggle('d-none', !isOther);
    hiddenOther.value = isOther ? 1 : 0;
    if (!isOther && otherInput) otherInput.value = '';
  };
  hotelSel?.addEventListener('change', toggleOtherHotel);
  toggleOtherHotel();

  // Calculate total with promo code support
  const calculateTotal = () => {
    if (!adultsInp || !kidsInp) return;

    const adults = parseInt(adultsInp.value || 0);
    const kids = parseInt(kidsInp.value || 0);

    // Validate minimum booking
    if (adults === 0 && kids === 0) {
      adultsInp.value = 1;
      return calculateTotal();
    }

    const subtotal = (adults * currentAdultPrice) + (kids * currentKidPrice);
    let total = subtotal;

    // Apply promo code discount/surcharge
    if (appliedPromoCode && promoDiscount > 0) {
      if (promoOperation === 'subtract') {
        total = Math.max(0, subtotal - promoDiscount);
        if (promoDiscountDisplay) promoDiscountDisplay.value = '-$' + promoDiscount.toFixed(2);
        if (promoDiscountLabel) promoDiscountLabel.textContent = 'Descuento (' + appliedPromoCode + ')';
        if (promoDiscountDisplay) promoDiscountDisplay.style.color = 'green';
      } else if (promoOperation === 'add') {
        total = subtotal + promoDiscount;
        if (promoDiscountDisplay) promoDiscountDisplay.value = '+$' + promoDiscount.toFixed(2);
        if (promoDiscountLabel) promoDiscountLabel.textContent = 'Recargo (' + appliedPromoCode + ')';
        if (promoDiscountDisplay) promoDiscountDisplay.style.color = '#b45309';
      }
      if (promoDiscountWrapper) promoDiscountWrapper.style.display = 'block';
    } else {
      if (promoDiscountWrapper) promoDiscountWrapper.style.display = 'none';
    }

    if (adultPriceDisp) adultPriceDisp.value = '$' + currentAdultPrice.toFixed(2);
    if (kidPriceDisp) kidPriceDisp.value = '$' + currentKidPrice.toFixed(2);
    if (subtotalDisp) subtotalDisp.value = '$' + subtotal.toFixed(2);
    if (totalDisp) totalDisp.value = '$' + total.toFixed(2);
  };

  // Promo code application
  btnApplyPromo?.addEventListener('click', async () => {
    const code = promoInput?.value?.trim().toUpperCase();

    if (!code) {
      appliedPromoCode = '';
      promoDiscount = 0;
      promoOperation = '';
      if (promoMessage) promoMessage.innerHTML = '';
      if (promoInput) promoInput.value = '';
      calculateTotal();
      return;
    }

    const adults = parseInt(adultsInp?.value || 0);
    const kids = parseInt(kidsInp?.value || 0);
    const subtotal = (adults * currentAdultPrice) + (kids * currentKidPrice);

    if (subtotal === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'Error',
        text: 'Por favor seleccione un tour y cantidad de personas primero.'
      });
      return;
    }

    try {
      const url = '{{ route('admin.bookings.verify-promo') }}?code=' + encodeURIComponent(code) + '&subtotal=' + subtotal;
      const response = await fetch(url);
      const data = await response.json();

      if (data.valid) {
        appliedPromoCode = code;
        promoDiscount = data.discount_amount || 0;
        promoOperation = data.operation || 'subtract';

        const operationText = promoOperation === 'add' ? 'recargo' : 'descuento';
        const operationSymbol = promoOperation === 'add' ? '+' : '-';

        if (promoMessage) {
          promoMessage.innerHTML = `<div class="alert alert-success small mb-0">
            <i class="fas fa-check-circle me-1"></i>Código aplicado: ${operationSymbol}$${promoDiscount.toFixed(2)} ${operationText}
          </div>`;
        }

        calculateTotal();

        Swal.fire({
          icon: 'success',
          title: '¡Código Aplicado!',
          text: `Se aplicó un ${operationText} de $${promoDiscount.toFixed(2)}`,
          timer: 2000,
          showConfirmButton: false
        });
      } else {
        appliedPromoCode = '';
        promoDiscount = 0;
        promoOperation = '';

        if (promoMessage) {
          promoMessage.innerHTML = `<div class="alert alert-danger small mb-0">
            <i class="fas fa-times-circle me-1"></i>${data.message || 'Código inválido o expirado'}
          </div>`;
        }

        calculateTotal();

        Swal.fire({
          icon: 'error',
          title: 'Código Inválido',
          text: data.message || 'El código ingresado no es válido o ha expirado.'
        });
      }
    } catch (error) {
      console.error('Error verifying promo code:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'No se pudo verificar el código. Por favor intente nuevamente.'
      });
    }
  });

  // Tour change - rebuild schedules
  const rebuildSchedules = () => {
    if (!tourSel || !schedSel) return;
    const opt = tourSel.selectedOptions[0];

    currentAdultPrice = parseFloat(opt?.getAttribute('data-adult-price') || 0);
    currentKidPrice = parseFloat(opt?.getAttribute('data-kid-price') || 0);

    const json = opt ? opt.getAttribute('data-schedules') : '[]';
    let list = [];
    try { list = JSON.parse(json || '[]'); } catch(e) { console.error(e); }

    schedSel.innerHTML = '<option value="">{{ __('m_bookings.bookings.placeholders.select_schedule') }}</option>';
    list.forEach(s => {
      const o = document.createElement('option');
      o.value = s.schedule_id;
      o.textContent = `${s.start_time} – ${s.end_time}`;
      schedSel.appendChild(o);
    });

    // Reset promo code when tour changes
    appliedPromoCode = '';
    promoDiscount = 0;
    promoOperation = '';
    if (promoInput) promoInput.value = '';
    if (promoMessage) promoMessage.innerHTML = '';

    calculateTotal();
  };

  tourSel?.addEventListener('change', rebuildSchedules);
  adultsInp?.addEventListener('input', calculateTotal);
  kidsInp?.addEventListener('input', calculateTotal);

  // Prevent 0 adults and 0 kids
  const validateMinimum = () => {
    if (!adultsInp || !kidsInp) return;
    const adults = parseInt(adultsInp.value || 0);
    const kids = parseInt(kidsInp.value || 0);

    if (adults === 0 && kids === 0) {
      adultsInp.value = 1;
      calculateTotal();
    }
  };

  adultsInp?.addEventListener('blur', validateMinimum);
  kidsInp?.addEventListener('blur', validateMinimum);

  // Meeting point help
  const updateMpHelp = () => {
    if (!mpSel || !mpHelp) return;
    const opt = mpSel.options[mpSel.selectedIndex];
    if (!opt || !opt.value) {
      mpHelp.innerHTML = '';
      return;
    }
    const time = opt.getAttribute('data-time') || '';
    const addr = opt.getAttribute('data-description') || '';
    const map = opt.getAttribute('data-map') || '';
    let html = '';
    if (time) html += `<div><i class="far fa-clock me-1"></i><strong>Hora:</strong> ${time}</div>`;
    if (addr) html += `<div><i class="fas fa-map-pin me-1"></i>${addr}</div>`;
    if (map) html += `<div><a href="${map}" target="_blank" rel="noopener"><i class="fas fa-external-link-alt me-1"></i>Ver mapa</a></div>`;
    mpHelp.innerHTML = html;
  };
  mpSel?.addEventListener('change', updateMpHelp);

  // Form submission with promo code
  form?.addEventListener('submit', function(e) {
    if (appliedPromoCode && promoInput) {
      promoInput.value = appliedPromoCode;
    }
  });

  // Initialize on modal shown
  regModal.addEventListener('shown.bs.modal', function() {
    if (tourSel?.value) rebuildSchedules();
    updateMpHelp();
  });

});
</script>
