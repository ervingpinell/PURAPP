@once
  {{-- Flatpickr --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  {{-- Choices.js --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
@endonce

@push('scripts')
<script>
(function(){
  const RULES = @json($rulesPayload);

  const T = {
    selectFromList: @json(__('adminlte::adminlte.selectFromList')),
    fillThisField:  @json(__('adminlte::adminlte.fillThisField')),
    pickupRequiredTitle: @json(__('adminlte::adminlte.pickupRequiredTitle')),
    pickupRequiredBody:  @json(__('adminlte::adminlte.pickupRequiredBody')),
    ok: @json(__('adminlte::adminlte.ok')),
    selectOption: @json(__('adminlte::adminlte.select_option')),
    meeting_time: @json(__('adminlte::adminlte.meeting_time') ?: 'Meeting time'),
    view_location: @json(__('adminlte::adminlte.open_map') ?: 'View location'),
  };

  const formEl = document.querySelector('form.reservation-box');
  if (!formEl || formEl.dataset.bound === '1') return;
  formEl.dataset.bound = '1';

  /* ========= PRECIOS, CANTIDADES Y TOTAL ========= */
  const adultPrice = Number(formEl?.dataset?.adultPrice || 0);
  const kidPrice   = Number(formEl?.dataset?.kidPrice   || 0);

  const inAdults = document.getElementById('adults_quantity');
  const inKids   = document.getElementById('kids_quantity');
  const totalInlineEl = document.getElementById('reservation-total-price-inline');

  const fmt = (n) => {
    try { return (new Intl.NumberFormat(undefined, { style: 'currency', currency: 'USD' })).format(n); }
    catch { return `$${Number(n).toFixed(2)}`; }
  };

  function updateTotals(){
    const a = Number(inAdults?.value || 0);
    const k = Number(inKids?.value   || 0);
    const total = (a * adultPrice) + (k * kidPrice);
    if (totalInlineEl) totalInlineEl.textContent = fmt(total);
  }
  updateTotals();
  ['change','input'].forEach(evt => {
    inAdults?.addEventListener(evt, updateTotals);
    inKids  ?.addEventListener(evt, updateTotals);
  });
  window.addEventListener('traveler:updated', updateTotals);

  /* ========= BLOQUE DE MEETING POINT ========= */
  const meetingSel = document.getElementById('meetingPointSelect');
  const mpWrap = document.getElementById('meetingPointInfo');
  const mpDesc = document.getElementById('mpDesc');
  const mpTime = document.getElementById('mpTime');
  const mpLink = document.getElementById('mpLink');

  function getSelectedMeetingValue(){
    try {
      if (window.meetingChoices && typeof window.meetingChoices.getValue === 'function') {
        return window.meetingChoices.getValue(true);
      }
    } catch(_){}
    return meetingSel?.value || '';
  }

  function getOptionByValue(sel, val){
    return Array.from(sel?.options || []).find(o => String(o.value) === String(val));
  }

  function refreshMeetingInfo(){
    const val = getSelectedMeetingValue();
    if (!val) {
      mpWrap?.classList.add('d-none');
      if (mpDesc) mpDesc.textContent = '';
      if (mpTime) mpTime.textContent = '';
      if (mpLink) {
        mpLink.removeAttribute('href');
        mpLink.classList.add('d-none');
        mpLink.textContent = T.view_location;
      }
      return;
    }

    const opt  = getOptionByValue(meetingSel, val);
    if (!opt) { mpWrap?.classList.add('d-none'); return; }

    const desc = opt.dataset.desc || '';
    const time = opt.dataset.time || '';
    const url  = opt.dataset.url  || '';

    if (mpDesc) {
      mpDesc.textContent = desc || '';
      mpDesc.classList.toggle('d-none', !desc);
    }
    if (mpTime) {
      mpTime.textContent = time ? `${T.meeting_time}: ${time}` : '';
      mpTime.classList.toggle('d-none', !time);
    }

    if (mpLink) {
      if (url) {
        mpLink.href = url;
        mpLink.textContent = T.view_location;
        mpLink.classList.remove('d-none');
      } else {
        mpLink.removeAttribute('href');
        mpLink.classList.add('d-none');
      }
    }

    mpWrap?.classList.remove('d-none');
  }
  meetingSel?.addEventListener('change', refreshMeetingInfo);
  refreshMeetingInfo();

  /* ========= BLOQUEO DE SUBMIT ========= */
  formEl.addEventListener('submit', (e) => e.preventDefault());

  window.isAuthenticated = @json(Auth::check());
  window.CART_COUNT_URL  = @json(route('cart.count.public'));

  const fullyBlockedDates = Array.isArray(window.fullyBlockedDates) ? window.fullyBlockedDates : [];
  const blockedGeneral    = Array.isArray(window.blockedGeneral) ? window.blockedGeneral : [];
  const blockedBySchedule = (window.blockedBySchedule && typeof window.blockedBySchedule === 'object') ? window.blockedBySchedule : {};

  const dateInput   = document.getElementById('tourDateInput');
  const scheduleSel = document.getElementById('scheduleSelect');
  const helpMsg     = document.getElementById('noSlotsHelp');
  const langSelect  = document.getElementById('languageSelect');
  const hotelSelect = document.getElementById('hotelSelect');

  const isoFromDate = (d) => (
    `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`
  );
  const ruleForSchedule = (sid) => RULES.schedules[String(sid)] || RULES.tour;
  const minAcrossAll = () => [RULES.tour.min, ...Object.values(RULES.schedules).map(r => r.min)].sort()[0];

  // Invitados: deshabilita campos (pero total/meeting visibles)
  if (!window.isAuthenticated) {
    if (dateInput) { dateInput.setAttribute('disabled','disabled'); dateInput.setAttribute('readonly','readonly'); }
    scheduleSel && scheduleSel.setAttribute('disabled','disabled');
    langSelect  && langSelect.setAttribute('disabled','disabled');
    hotelSelect && hotelSelect.setAttribute('disabled','disabled');
    meetingSel  && meetingSel.setAttribute('disabled','disabled');
    return;
  }

  const canUseScheduleOnDate = (iso, sid) => {
    if (!iso) return false;
    if (fullyBlockedDates.includes(iso)) return false;
    if (blockedGeneral.includes(iso))    return false;
    if ((blockedBySchedule[sid] || []).includes(iso)) return false;
    const r = ruleForSchedule(sid);
    return !(iso < r.min);
  };

  /* ========= Choices ========= */
  const scheduleChoices = new Choices(scheduleSel, { searchEnabled:false, shouldSort:false, itemSelectText:'', placeholder:true, placeholderValue:'-- ' + T.selectOption + ' --' });
  const langChoices     = new Choices(langSelect,  { searchEnabled:false, shouldSort:false, itemSelectText:'' });
  const hotelChoices    = new Choices(hotelSelect, { searchEnabled:true,  shouldSort:false, itemSelectText:'' });
  const meetingChoices  = new Choices(meetingSel,  { searchEnabled:true,  shouldSort:false, itemSelectText:'', placeholder:true, placeholderValue:'-- ' + T.selectOption + ' --' });
  window.meetingChoices = meetingChoices;

  const BASE_CHOICES = scheduleChoices._store.choices.filter(c => c.value !== '').map(c => ({ value:String(c.value), label:c.label }));
  const BASE_IDS = BASE_CHOICES.map(o => o.value);

  const anyScheduleAvailable = (iso) => BASE_IDS.some((sid) => canUseScheduleOnDate(iso, sid));
  const isDayFullyBlocked = (iso) => {
    if (!iso) return true;
    if (fullyBlockedDates.includes(iso)) return true;
    if (blockedGeneral.includes(iso))    return true;
    return !anyScheduleAvailable(iso);
  };

  function rebuildScheduleChoices(iso){
    const ph = [{ value:'', label:'-- ' + T.selectOption + ' --', disabled:true, selected:true }];
    scheduleChoices.clearStore();

    if (!iso || isDayFullyBlocked(iso)) {
      scheduleChoices.setChoices(ph, 'value', 'label', true);
      scheduleChoices.disable();
      helpMsg.textContent = iso ? 'No hay horarios disponibles para esa fecha.' : '';
      helpMsg.style.display = iso ? '' : 'none';
      return;
    }

    const allowed = BASE_CHOICES.map(o => ({ ...o, disabled: !canUseScheduleOnDate(iso, o.value) }));
    scheduleChoices.setChoices(ph.concat(allowed), 'value', 'label', true);

    const hasEnabled = allowed.some(c => !c.disabled);
    if (hasEnabled) { scheduleChoices.enable(); helpMsg.style.display = 'none'; }
    else { scheduleChoices.disable(); helpMsg.textContent = 'No hay horarios disponibles para esa fecha.'; helpMsg.style.display = ''; }
  }

  /* ========= Flatpickr ========= */
  let fp;
  const setupFlatpickr = () => {
    const initialMin = RULES.initialMin || minAcrossAll();

    if (window.flatpickr && dateInput) {
      fp = flatpickr(dateInput, {
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd/m/Y',
        minDate: initialMin,
        disable: [ (date) => isDayFullyBlocked(isoFromDate(date)) ],
        onChange: (_sel, dateStr /* iso */) => {
          rebuildScheduleChoices(dateStr);
        },
        onReady: (_sel, dateStr, instance) => {
          if (!dateStr) {
            scheduleChoices.disable();
            helpMsg.style.display = 'none';
            instance.setDate(initialMin, false);
            rebuildScheduleChoices(initialMin);
          } else {
            rebuildScheduleChoices(dateStr);
          }
        }
      });
    } else if (dateInput) {
      dateInput.type = 'date';
      dateInput.min  = initialMin;
      dateInput.addEventListener('change', e => rebuildScheduleChoices(e.target.value));
      scheduleChoices.disable();
    }
  };
  setupFlatpickr();

  /* Cambio de horario -> minDate (protegido) */
  scheduleSel.addEventListener('change', () => {
    const sid  = scheduleSel.value;
    const rule = sid ? ruleForSchedule(sid) : RULES.tour;

    if (fp && typeof fp.set === 'function') {
      fp.set('minDate', sid ? rule.min : minAcrossAll());
    }
    const current = dateInput.value;
    if (fp && typeof fp.setDate === 'function' && current && sid && current < rule.min) {
      fp.setDate(rule.min, true);
    }
    const iso = (fp && fp.input ? fp.input.value : dateInput.value) || null;
    rebuildScheduleChoices(iso);
  });

  /* Hotel "otro" */
  const otherWrap = document.getElementById('otherHotelWrapper');
  const isOtherH  = document.getElementById('isOtherHotel');
  const otherInp  = document.getElementById('otherHotelInput');
  const warnMsg   = document.getElementById('outsideAreaMessage');

  const toggleOther = () => {
    const isOther = (hotelChoices.getValue(true) === 'other');
    otherWrap.classList.toggle('d-none', !isOther);
    if (isOtherH) isOtherH.value = isOther ? 1 : 0;
    if (isOther) { warnMsg && (warnMsg.style.display = ''); otherInp && otherInp.setAttribute('required','required'); }
    else { warnMsg && (warnMsg.style.display = 'none'); if (otherInp) { otherInp.removeAttribute('required'); otherInp.value=''; } }
  };
  hotelSelect.addEventListener('change', toggleOther);
  toggleOther();

  /* Meeting point -> hidden + info */
  const hiddenMP = document.getElementById('selectedMeetingPoint');
  meetingSel.addEventListener('change', () => {
    if (hiddenMP) hiddenMP.value = meetingChoices.getValue(true) || '';
    refreshMeetingInfo();
  });

  /* Mutua exclusión hotel/meeting */
  function validateHotelMeetingPoint() {
    const hotelValue = hotelChoices.getValue(true);
    const meetingValue = meetingChoices.getValue(true);

    if (hotelValue && hotelValue !== '') {
      meetingChoices.disable(); meetingSel.value = ''; if (hiddenMP) hiddenMP.value = '';
      refreshMeetingInfo();
    } else { meetingChoices.enable(); }

    if (meetingValue && meetingValue !== '') {
      hotelChoices.disable(); hotelSelect.value = ''; toggleOther(); if (isOtherH) isOtherH.value = 0;
    } else { hotelChoices.enable(); }
  }
  hotelSelect.addEventListener('change', validateHotelMeetingPoint);
  meetingSel.addEventListener('change', validateHotelMeetingPoint);
  validateHotelMeetingPoint();

  /* ========= Validaciones nativas traducidas ========= */
  const setSelectValidity = (el) => el.setCustomValidity(T.selectFromList || '');
  const setInputValidity  = (el) => el.setCustomValidity(T.fillThisField || '');
  [scheduleSel, langSelect, meetingSel, hotelSelect].forEach(el => {
    if (!el) return;
    el.addEventListener('invalid', () => setSelectValidity(el));
    el.addEventListener('change',  () => el.setCustomValidity(''));
    el.addEventListener('input',   () => el.setCustomValidity(''));
  });
  if (dateInput) {
    dateInput.addEventListener('invalid', () => setInputValidity(dateInput));
    dateInput.addEventListener('input',   () => dateInput.setCustomValidity(''));
    dateInput.addEventListener('change',  () => dateInput.setCustomValidity(''));
  }

  /* =======================
   *   TRAVELER INLINE — mínimo total = 2
   *   + Máximo niños = 2 (botón + bloquea al llegar)
   * ======================= */
  {
    const adultMinusBtn = document.getElementById('adultMinusBtn');
    const adultPlusBtn  = document.getElementById('adultPlusBtn');
    const kidMinusBtn   = document.getElementById('kidMinusBtn');
    const kidPlusBtn    = document.getElementById('kidPlusBtn');
    const adultInput    = document.getElementById('adultInput');
    const kidInput      = document.getElementById('kidInput');

    const hiddenAdults  = document.getElementById('adults_quantity');
    const hiddenKids    = document.getElementById('kids_quantity');

    const MIN_ADULTS    = Number(formEl?.dataset?.minAdults ?? 1);
    const MIN_TOTAL     = Number(formEl?.dataset?.minTotal  ?? 2);
    const MAX_TRAVELERS = Number(formEl?.dataset?.maxTravelers ?? 12);
    const MAX_KIDS = Math.min(2, Number(formEl?.dataset?.maxKids ?? 2));

    const MIN_KIDS      = 0;
    const clamp = (v, min, max) => Math.max(min, Math.min(max, v));

    function capByRules(a, k){
      a = clamp(a, MIN_ADULTS, MAX_TRAVELERS);
      k = clamp(k, MIN_KIDS,   Math.min(MAX_KIDS, MAX_TRAVELERS - a));

      if (a + k > MAX_TRAVELERS) {
        const overflow = (a + k) - MAX_TRAVELERS;
        const cutKids = Math.min(Math.max(0, k - MIN_KIDS), overflow);
        k -= cutKids;
        const remaining = overflow - cutKids;
        if (remaining > 0) a -= Math.min(Math.max(0, a - MIN_ADULTS), remaining);
      }

      if (a + k < MIN_TOTAL) {
        let need = MIN_TOTAL - (a + k);
        const addAdults = Math.min(need, MAX_TRAVELERS - (a + k));
        a = clamp(a + addAdults, MIN_ADULTS, MAX_TRAVELERS);
        need = MIN_TOTAL - (a + k);
        if (need > 0) {
          k = clamp(k + need, MIN_KIDS, Math.min(MAX_KIDS, MAX_TRAVELERS - a));
        }
      }
      return [a, k];
    }

    function syncAll(fromVisible = true){
      let a = Number(fromVisible ? (adultInput?.value || 0) : (hiddenAdults?.value || 0));
      let k = Number(fromVisible ? (kidInput?.value   || 0) : (hiddenKids?.value   || 0));
      [a, k] = capByRules(a, k);

      if (adultInput)   adultInput.value   = String(a);
      if (kidInput)     kidInput.value     = String(k);
      if (hiddenAdults) hiddenAdults.value = String(a);
      if (hiddenKids)   hiddenKids.value   = String(k);

      updateTotals();
      window.dispatchEvent(new CustomEvent('traveler:updated'));

      // bloquear “–” si rompería el mínimo total
      if (adultMinusBtn) adultMinusBtn.disabled = ((a - 1) < MIN_ADULTS) || ((a - 1) + k < MIN_TOTAL);
      if (kidMinusBtn)   kidMinusBtn.disabled   = ((k - 1) < MIN_KIDS)   || (a + (k - 1) < MIN_TOTAL);

      // bloquear “+” niños al alcanzar tope (2 por reserva)
      if (kidPlusBtn)    kidPlusBtn.disabled    = (k >= MAX_KIDS) || (a + k >= MAX_TRAVELERS);
    }

    // Inicial desde hidden y normaliza
    syncAll(false);

    // Botones +/−
    adultMinusBtn?.addEventListener('click', () => { if (!adultInput) return; adultInput.value = String(Number(adultInput.value || 0) - 1); syncAll(true); });
    adultPlusBtn ?.addEventListener('click', () => { if (!adultInput) return; adultInput.value = String(Number(adultInput.value || 0) + 1); syncAll(true); });
    kidMinusBtn  ?.addEventListener('click', () => { if (!kidInput)   return; kidInput.value   = String(Number(kidInput.value   || 0) - 1); syncAll(true); });
    kidPlusBtn   ?.addEventListener('click', () => { if (!kidInput)   return; kidInput.value   = String(Number(kidInput.value   || 0) + 1); syncAll(true); });

    // Edición manual
    ['input','change','blur'].forEach(evt => {
      adultInput?.addEventListener(evt, () => syncAll(true));
      kidInput  ?.addEventListener(evt, () => syncAll(true));
    });

    // Evitar scroll accidental
    [adultInput, kidInput].forEach(inp => {
      inp?.addEventListener('wheel', e => { e.preventDefault(); inp.blur(); }, { passive:false });
    });

    if (adultInput) { adultInput.min = '1'; adultInput.step = '1'; }
    if (kidInput)   { kidInput.min   = '0'; kidInput.step   = '1'; }
  }

  /* ========= AJAX Add to cart ========= */
  const addBtn = document.getElementById('addToCartBtn');
  if (!addBtn) return;

  let submitting = false;
  addBtn.addEventListener('click', async () => {
    if (submitting) return;

    if (!formEl.checkValidity()) { formEl.reportValidity(); return; }

    const meetingValue = window.meetingChoices ? window.meetingChoices.getValue(true) : '';

    const isOtherH = document.getElementById('isOtherHotel');
    const otherInp = document.getElementById('otherHotelInput');

    const hotelSelect = document.getElementById('hotelSelect');
    const selectedHotel = hotelSelect ? hotelSelect.value : '';
    const isOtherHotel = isOtherH && isOtherH.value === '1';
    const otherHotelName = otherInp && otherInp.value.trim();

    const hasHotel = (selectedHotel && selectedHotel !== '' && selectedHotel !== 'other') || (isOtherHotel && otherHotelName);
    const hasMeeting = (meetingValue && meetingValue !== '');

    if (!hasHotel && !hasMeeting) {
      await Swal.fire({ icon: 'warning', title: T.pickupRequiredTitle, text: T.pickupRequiredBody, confirmButtonColor: '#198754', confirmButtonText: T.ok });
      return;
    }

    submitting = true;
    const prevHTML = addBtn.innerHTML;
    addBtn.disabled = true;
    addBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __('adminlte::adminlte.add_to_cart') }}';

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const fd = new FormData(formEl);

    try {
      const res = await fetch(formEl.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: fd
      });

      let data = {}; try { data = await res.json(); } catch(_) {}

      if (!res.ok) {
        const msg = (data && data.message) ? data.message : 'No se pudo agregar el tour. Intenta de nuevo.';
        await Swal.fire({ icon:'error', title:'Error', text:msg }); return;
      }

      const okMsg = (data && data.message) ? data.message : 'Tour añadido al carrito.';
      await Swal.fire({ icon:'success', title:'Success', text:okMsg, confirmButtonColor:'#198754', confirmButtonText:T.ok });

      if (typeof data.count !== 'undefined' && window.setCartCount) {
        window.setCartCount(data.count);
      } else if (window.CART_COUNT_URL && window.setCartCount) {
        try {
          const cRes = await fetch(window.CART_COUNT_URL, { headers: { 'Accept': 'application/json' }});
          const cData = await cRes.json();
          window.setCartCount(cData.count || 0);
        } catch(_) {}
      }

      window.location.reload();

    } catch (err) {
      await Swal.fire({ icon:'error', title:'Error', text:'Error de red. Intenta nuevamente.', confirmButtonText:T.ok });
    } finally {
      addBtn.disabled = false;
      addBtn.innerHTML = prevHTML;
      submitting = false;
    }
  });
})();
</script>
@endpush
