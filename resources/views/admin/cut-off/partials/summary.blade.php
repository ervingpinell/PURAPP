<div class="card shadow-sm mb-4">
  <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
    <strong>{{ __('m_config.cut-off.summary.product_title') }}</strong>
    <span class="text-muted small">{{ __('m_config.cut-off.hints.dash_means_inherit') }}</span>
  </div>
  <div class="card-body">
    @if(empty($productOverrides))
    <div class="text-muted">{{ __('m_config.cut-off.summary.no_tour_overrides') }}</div>
    @else
    <div class="table-responsive">
      <table class="table table-sm table-striped align-middle mb-0">
        <thead>
          <tr>
            <th style="min-width:220px">{{ __('m_config.cut-off.fields.product') }}</th>
            <th style="min-width:160px">{{ __('m_config.cut-off.fields.cutoff_hour_short') }}</th>
            <th style="min-width:160px">{{ __('m_config.cut-off.fields.lead_days') }}</th>
            <th class="text-end" style="width:220px">{{ __('m_config.cut-off.fields.actions') ?? 'Acciones' }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($productOverrides as $row)
          @php
          $cutVal = $row['cutoff'] === '—' ? '' : $row['cutoff'];
          $leadVal = $row['lead'] === '—' ? '' : $row['lead'];
          $rid = 'tour_'.$row['product_id'];
          @endphp
          <tr>
            <td class="fw-medium">{{ $row['product'] }}</td>

            <td>
              <form id="form-{{ $rid }}-update" class="d-flex gap-2 align-items-center flex-wrap" method="POST"
                action="{{ route('admin.products.cutoff.product.update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="from_summary" value="1">
                <input type="hidden" name="product_id" value="{{ $row['product_id'] }}">
                <input type="time" name="cutoff_hour" value="{{ $cutVal }}" class="form-control form-control-sm" placeholder="--:--"
                  pattern="^(?:[01]\d|2[0-3]):[0-5]\d$" style="max-width:130px">
            </td>

            <td>
              <input type="number" name="lead_days" value="{{ $leadVal === '' ? '' : (int)$leadVal }}"
                class="form-control form-control-sm" min="0" max="30" placeholder="—" style="max-width:130px">
            </td>

            <td class="text-end">
              @can('edit-product-availability')
              <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-save me-1"></i>{{ __('m_config.cut-off.actions.save_tour') }}
              </button>
              </form>

              {{-- DELETE: manda PUT al mismo endpoint con valores vacíos (se guardan como NULL) --}}
              <form id="form-{{ $rid }}-delete" class="d-inline" method="POST"
                action="{{ route('admin.products.cutoff.product.update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="from_summary" value="1">
                <input type="hidden" name="product_id" value="{{ $row['product_id'] }}">
                <input type="hidden" name="cutoff_hour" value="">
                <input type="hidden" name="lead_days" value="">
                <button type="button" class="btn btn-danger btn-sm js-confirm-delete"
                  data-target="#form-{{ $rid }}-delete">
                  <i class="fas fa-trash-alt me-1"></i>{{ __('m_config.cut-off.actions.clear') }}
                </button>
              </form>
              @endcan
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
    <strong>{{ __('m_config.cut-off.summary.schedule_title') }}</strong>
    <div class="d-flex align-items-center gap-2">
      <input type="search" class="form-control form-control-sm search-input" id="searchPivot"
        placeholder="{{ __('m_config.cut-off.summary.search_placeholder') }}">
    </div>
  </div>
  <div class="card-body">
    @if(empty($scheduleOverrides))
    <div class="text-muted">{{ __('m_config.cut-off.summary.no_schedule_overrides') }}</div>
    @else
    <div class="table-responsive">
      <table class="table table-sm table-striped align-middle mb-0" id="pivotTable">
        <thead>
          <tr>
            <th style="min-width:220px">{{ __('m_config.cut-off.fields.product') }}</th>
            <th style="min-width:220px">{{ __('m_config.cut-off.fields.schedule') }}</th>
            <th style="min-width:160px">{{ __('m_config.cut-off.fields.cutoff_hour_short') }}</th>
            <th style="min-width:160px">{{ __('m_config.cut-off.fields.lead_days') }}</th>
            <th class="text-end" style="width:220px">{{ __('m_config.cut-off.fields.actions') ?? 'Acciones' }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($scheduleOverrides as $row)
          @php
          $cutVal = $row['cutoff'] === '—' ? '' : $row['cutoff'];
          $leadVal = $row['lead'] === '—' ? '' : $row['lead'];
          $rid = 'sch_'.$row['product_id'].'_'.$row['schedule_id'];
          @endphp
          <tr>
            <td class="fw-medium">{{ $row['product'] }}</td>
            <td class="text-nowrap">{{ $row['schedule'] }}</td>

            <td>
              <form id="form-{{ $rid }}-update" class="d-flex gap-2 align-items-center flex-wrap" method="POST"
                action="{{ route('admin.products.cutoff.schedule.update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="from_summary" value="1">
                <input type="hidden" name="product_id" value="{{ $row['product_id'] }}">
                <input type="hidden" name="schedule_id" value="{{ $row['schedule_id'] }}">
                <input type="time" name="pivot_cutoff_hour" value="{{ $cutVal }}" class="form-control form-control-sm" placeholder="--:--"
                  pattern="^(?:[01]\d|2[0-3]):[0-5]\d$" style="max-width:130px">
            </td>

            <td>
              <input type="number" name="pivot_lead_days" value="{{ $leadVal === '' ? '' : (int)$leadVal }}"
                class="form-control form-control-sm" min="0" max="30" placeholder="—" style="max-width:130px">
            </td>

            <td class="text-end">
              @can('edit-product-availability')
              <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-save me-1"></i>{{ __('m_config.cut-off.actions.save_schedule') }}
              </button>
              </form>

              {{-- DELETE: manda PUT al mismo endpoint con valores vacíos (se guardan como NULL) --}}
              <form id="form-{{ $rid }}-delete" class="d-inline" method="POST"
                action="{{ route('admin.products.cutoff.schedule.update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="from_summary" value="1">
                <input type="hidden" name="product_id" value="{{ $row['product_id'] }}">
                <input type="hidden" name="schedule_id" value="{{ $row['schedule_id'] }}">
                <input type="hidden" name="pivot_cutoff_hour" value="">
                <input type="hidden" name="pivot_lead_days" value="">
                <button type="button" class="btn btn-danger btn-sm js-confirm-delete"
                  data-target="#form-{{ $rid }}-delete">
                  <i class="fas fa-trash-alt me-1"></i>{{ __('m_config.cut-off.actions.clear') }}
                </button>
              </form>
              @endcan
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>
</div>

@push('js')
<script>
  (() => {
    // Buscar en tabla de schedules
    const searchPivot = document.getElementById('searchPivot');
    const pivotTable = document.getElementById('pivotTable');
    if (searchPivot && pivotTable) {
      searchPivot.addEventListener('input', () => {
        const q = searchPivot.value.trim().toLowerCase();
        pivotTable.querySelectorAll('tbody tr').forEach(tr => {
          const text = tr.innerText.toLowerCase();
          tr.style.display = text.includes(q) ? '' : 'none';
        });
      });
    }

    // Confirmación de "Eliminar bloqueo" (envía el form oculto con campos vacíos)
    document.querySelectorAll('.js-confirm-delete').forEach(btn => {
      btn.addEventListener('click', () => {
        const sel = btn.getAttribute('data-target');
        const form = document.querySelector(sel);
        if (!form) return;

        Swal.fire({
          icon: 'warning',
          title: @json(__('m_config.cut-off.confirm.schedule.title')),
          text: @json(__('m_config.cut-off.hints.leave_empty_inherit')), // mensaje corto y claro
          showCancelButton: true,
          confirmButtonText: @json(__('m_config.cut-off.actions.confirm')),
          cancelButtonText: @json(__('m_config.cut-off.actions.cancel')),
        }).then(r => {
          if (r.isConfirmed) form.submit();
        });
      });
    });
  })();
</script>
@endpush