<div class="card shadow-sm">
  <div class="card-body">
    {{-- === Status arriba (primera fila) === --}}
    <div class="row g-2 mb-3">
      <div class="col-12">
        <div class="status-panel p-3">
          <span class="label">{{ __('m_config.cut-off.labels.status') }}:</span>
          <span class="badge badge-chip badge-global" id="schBadge">
            <i class="fas fa-arrow-turn-down-left me-1"></i>{{ __('m_config.cut-off.badges.inherit_tour_global') }}
          </span>
          <span class="helper ms-1">
            <i class="far fa-lightbulb me-1"></i>{{ __('m_config.cut-off.hints.leave_empty_inherit') }}
          </span>
        </div>
      </div>
    </div>

    <form method="POST" action="{{ route('admin.tours.cutoff.schedule.update') }}" id="form-schedule">
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label class="form-label">{{ __('m_config.cut-off.fields.tour') }}</label>
        <select class="form-select" id="tourForSchedule">
          <option value="">{{ __('m_config.cut-off.selects.tour') }}</option>
          @foreach($tours as $t)
          <option value="{{ $t->tour_id }}">{{ $t->name }}</option>
          @endforeach
        </select>
        <div class="form-hint mt-1">1) {{ __('m_config.cut-off.hints.pick_tour') }}</div>
      </div>

      <div class="mb-3">
        <label class="form-label">{{ __('m_config.cut-off.fields.schedule') }}</label>
        <select class="form-select" id="scheduleSelect" name="schedule_id" required disabled>
          <option value="">{{ __('m_config.cut-off.selects.time') }}</option>
        </select>
        <div class="form-hint mt-1">2) {{ __('m_config.cut-off.hints.pick_schedule') }}</div>
      </div>

      <hr>

      <input type="hidden" name="tour_id" id="tourIdHidden" value="">

      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">{{ __('m_config.cut-off.fields.cutoff_hour_short') }}</label>
          <input type="time" class="form-control" name="pivot_cutoff_hour" id="schCutoff" placeholder="--:--">
          <div class="form-hint mt-1">{{ __('m_config.cut-off.hints.pattern_24h') }}</div>
        </div>
        <div class="col-md-3">
          <label class="form-label">{{ __('m_config.cut-off.fields.lead_days') }}</label>
          <input type="number" class="form-control" name="pivot_lead_days" id="schLead" min="0" max="30" placeholder="—">
          <div class="form-hint mt-1">{{ __('m_config.cut-off.hints.lead_days_detail') }}</div>
        </div>
        <div class="col-md-6 d-flex gap-2 align-items-end">
          @can('edit-tour-availability')
          <button class="btn btn-primary"><i class="fas fa-save me-1"></i> {{ __('m_config.cut-off.actions.save_schedule') }}</button>
          <button type="button" class="btn btn-outline-secondary" id="clearScheduleOverride">{{ __('m_config.cut-off.actions.clear') }}</button>
          @endcan
        </div>
      </div>
    </form>
  </div>
</div>

@push('js')
<script>
  (() => {
    const toursData = @json($toursPayload);
    const tourForSchedule = document.getElementById('tourForSchedule');
    const scheduleSelect = document.getElementById('scheduleSelect');
    const schCutoff = document.getElementById('schCutoff');
    const schLead = document.getElementById('schLead');
    const schBadge = document.getElementById('schBadge');
    const clearSchBtn = document.getElementById('clearScheduleOverride');
    const tourIdHidden = document.getElementById('tourIdHidden');

    const setSch = (text, cls) => {
      if (!schBadge) return;
      schBadge.className = 'badge badge-chip ' + cls;
      const icon = (cls === 'badge-override') ? 'fa-bolt' : 'fa-arrow-turn-down-left';
      schBadge.innerHTML = `<i class="fas ${icon} me-1"></i>${text}`;
    };

    function rebuild() {
      const tourId = tourForSchedule.value;
      tourIdHidden.value = tourId || '';
      scheduleSelect.innerHTML = `<option value="">${@json(__('m_config.cut-off.selects.time'))}</option>`;
      scheduleSelect.disabled = !tourId;
      schCutoff.value = '';
      schLead.value = '';
      setSch(@json(__('m_config.cut-off.badges.inherit_tour_global')), 'badge-global');

      if (!tourId) return;
      const t = toursData.find(x => String(x.id) === String(tourId));
      if (!t) return;
      t.schedules.forEach(s => {
        const opt = document.createElement('option');
        opt.value = s.id;
        opt.textContent = s.label;
        opt.dataset.cutoff = s.cutoff || '';
        opt.dataset.lead = (s.lead ?? '') === null ? '' : s.lead;
        scheduleSelect.appendChild(opt);
      });
    }

    function loadValues() {
      const opt = scheduleSelect?.selectedOptions[0];
      if (!opt) {
        schCutoff.value = '';
        schLead.value = '';
        setSch(@json(__('m_config.cut-off.badges.inherit_tour_global')), 'badge-global');
        return;
      }
      const c = opt.dataset.cutoff || '';
      const l = opt.dataset.lead || '';
      schCutoff.value = c;
      schLead.value = l;
      (c || l) ? setSch(@json(__('m_config.cut-off.badges.override')), 'badge-override'): setSch(@json(__('m_config.cut-off.badges.inherit_tour_global')), 'badge-global');
    }

    tourForSchedule?.addEventListener('change', rebuild);
    scheduleSelect?.addEventListener('change', loadValues);
    clearSchBtn?.addEventListener('click', () => {
      schCutoff.value = '';
      schLead.value = '';
    });

    rebuild();

    // Confirm al enviar
    document.getElementById('form-schedule')?.addEventListener('submit', function(e) {
      e.preventDefault();
      Swal.fire({
        icon: 'question',
        title: @json(__('m_config.cut-off.confirm.schedule.title')),
        text: @json(__('m_config.cut-off.confirm.schedule.text')),
        showCancelButton: true,
        confirmButtonText: @json(__('m_config.cut-off.actions.confirm')),
        cancelButtonText: @json(__('m_config.cut-off.actions.cancel')),
      }).then(r => {
        if (r.isConfirmed) this.submit();
      });
    });
  })();
</script>
@endpush