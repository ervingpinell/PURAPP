<div class="card shadow-sm">
  <div class="card-body">
    {{-- === Status arriba (primera fila) === --}}
    <div class="row g-2 mb-3">
      <div class="col-12">
        <div class="status-panel p-3">
          <span class="label">{{ __('m_config.cut-off.labels.status') }}:</span>
          <span class="badge badge-chip badge-inherit" id="tourBadge">
            <i class="fas fa-arrow-turn-down-left me-1"></i>{{ __('m_config.cut-off.badges.inherits') }}
          </span>
          <span class="helper ms-1">
            <i class="far fa-lightbulb me-1"></i>{{ __('m_config.cut-off.hints.leave_empty_inherit') }}
          </span>
        </div>
      </div>
    </div>

    {{-- Selección de tour --}}
    <div class="row g-3 align-items-end">
      <div class="col-12 col-lg-6">
        <label class="form-label">{{ __('m_config.cut-off.fields.tour') }}</label>
        <select class="form-select" id="tourSelect">
          <option value="">{{ __('m_config.cut-off.selects.tour') }}</option>
          @foreach($products as $t)
          <option value="{{ $t->product_id }}" data-cutoff="{{ $t->cutoff_hour }}" data-lead="{{ $t->lead_days }}">{{ $t->name }}</option>
          @endforeach
        </select>
        <div class="form-hint mt-1">{{ __('m_config.cut-off.hints.pick_tour') }}</div>
        <div class="form-hint">{{ __('m_config.cut-off.hints.tour_override_explain') }}</div>
      </div>
    </div>

    <hr>

    {{-- Form override por tour --}}
    <form method="POST" action="{{ route('admin.products.cutoff.tour.update') }}" id="form-tour">
      @csrf
      @method('PUT')
      <input type="hidden" name="product_id" id="productIdHiddenForProduct" value="">
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">{{ __('m_config.cut-off.fields.cutoff_hour_short') }}</label>
          <input type="time" class="form-control" name="cutoff_hour" id="tourCutoff" placeholder="--:--">
          <div class="form-hint mt-1">{{ __('m_config.cut-off.hints.pattern_24h') }}</div>
        </div>
        <div class="col-md-3">
          <label class="form-label">{{ __('m_config.cut-off.fields.lead_days') }}</label>
          <input type="number" class="form-control" name="lead_days" id="tourLead" min="0" max="30" placeholder="—">
          <div class="form-hint mt-1">{{ __('m_config.cut-off.hints.lead_days_detail') }}</div>
        </div>
        <div class="col-md-6 d-flex flex-column gap-2 align-items-start align-items-md-end">
          <div>
            @can('edit-tour-availability')
            <button class="btn btn-primary">
              <i class="fas fa-save me-1"></i> {{ __('m_config.cut-off.actions.save_tour') }}
            </button>
            <button type="button" class="btn btn-outline-secondary" id="clearProductOverride">
              {{ __('m_config.cut-off.actions.clear') }}
            </button>
            @endcan
          </div>
          <div class="form-hint">{{ __('m_config.cut-off.hints.clear_button_hint') }}</div>
        </div>
      </div>
    </form>
  </div>
</div>

@push('js')
<script>
  (() => {
    const tourSelect = document.getElementById('tourSelect');
    const tourCutoff = document.getElementById('tourCutoff');
    const tourLead = document.getElementById('tourLead');
    const tourBadge = document.getElementById('tourBadge');
    const productIdHidden = document.getElementById('productIdHiddenForProduct');
    const clearBtn = document.getElementById('clearProductOverride');

    const setBadge = (text, cls) => {
      if (!tourBadge) return;
      tourBadge.className = 'badge badge-chip ' + cls;
      const icon = (cls === 'badge-override') ? 'fa-bolt' : 'fa-arrow-turn-down-left';
      tourBadge.innerHTML = `<i class="fas ${icon} me-1"></i>${text}`;
    };

    function refresh() {
      const opt = tourSelect?.selectedOptions[0];
      productIdHidden.value = opt ? opt.value : '';
      if (!opt) {
        tourCutoff.value = '';
        tourLead.value = '';
        setBadge(@json(__('m_config.cut-off.badges.inherits')), 'badge-inherit');
        return;
      }
      const c = opt.dataset.cutoff || '';
      const l = opt.dataset.lead || '';
      tourCutoff.value = c;
      tourLead.value = l;
      (c || l) ? setBadge(@json(__('m_config.cut-off.badges.override')), 'badge-override'): setBadge(@json(__('m_config.cut-off.badges.inherits')), 'badge-inherit');
    }

    tourSelect?.addEventListener('change', refresh);
    clearBtn?.addEventListener('click', () => {
      tourCutoff.value = '';
      tourLead.value = '';
    });
    refresh();

    // Confirm al enviar
    document.getElementById('form-tour')?.addEventListener('submit', function(e) {
      e.preventDefault();
      Swal.fire({
        icon: 'question',
        title: @json(__('m_config.cut-off.confirm.tour.title')),
        text: @json(__('m_config.cut-off.confirm.tour.text')),
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