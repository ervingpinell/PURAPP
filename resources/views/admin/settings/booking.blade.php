@extends('adminlte::page')

@section('title', 'Booking Settings')

@push('css')
<style>
  /* ====== Paleta de la barra de pestañas (ajústala a tu gusto) ====== */
  :root{
    --tabbar-bg:        #1f2937; /* fondo de la franja (slate-800) */
    --tabbar-border:    #111827; /* borde del panel (gray-900) */
    --tab-inactive-bg:  #374151; /* pestaña inactiva (slate-700) */
    --tab-inactive-text:#e5e7eb; /* texto inactivo (gray-200) */
    --tab-active-bg:    #16a34a; /* pestaña activa (verde) */
    --tab-active-text:  #ffffff; /* texto activo */
  }

  /* Franja de pestañas */
  #settingsTabs{
    background: var(--tabbar-bg);
    padding: .5rem;
    border-radius: .85rem;
    border: 1px solid var(--tabbar-border);
    box-shadow: 0 1px 2px rgba(0,0,0,.25) inset;
  }

  /* Botones de tab */
  #settingsTabs .nav-link{
    background: var(--tab-inactive-bg);
    color: var(--tab-inactive-text);
    border: 1px solid transparent;
    margin-right: .5rem;
    border-radius: .65rem;
    transition: background-color .15s ease, color .15s ease, transform .08s ease, box-shadow .15s ease;
  }
  #settingsTabs .nav-link:hover{
    filter: brightness(1.06);
    transform: translateY(-1px);
  }
  #settingsTabs .nav-link.active{
    background: var(--tab-active-bg);
    color: var(--tab-active-text);
    border-color: rgba(0,0,0,.15);
    box-shadow: 0 2px 6px rgba(0,0,0,.25);
  }
  #settingsTabs .nav-link i{ opacity:.95; }

  /* Resto del layout original */
  .card { border-radius:.6rem; }
  .helper, .form-hint { color:#6c757d; font-size:.9rem; }
  .kbd{padding:.12rem .4rem;border:1px solid #adb5bd;border-bottom-width:2px;border-radius:.25rem;font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas,"Liberation Mono","Courier New",monospace;}
  .badge-inherit { background:#0dcaf0; }
  .badge-override { background:#ffc107; color:#000; }
  .badge-global { background:#6c757d; }
  .table thead th { white-space: nowrap; }
  .table td, .table th { vertical-align: middle; }
  .search-input { max-width: 360px; }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
@php
  use Carbon\Carbon;

  $now = Carbon::now(config('app.timezone'));

  // ===== Preparo el payload de tours + schedules para JS (sin bucles dentro de <script>)
  $toursPayload = [];
  foreach ($tours as $t) {
      $item = [
          'id'   => (int)$t->tour_id,
          'name' => $t->name,
          'cutoff' => $t->cutoff_hour,
          'lead'   => $t->lead_days,
          'schedules' => [],
      ];
      foreach ($t->schedules as $s) {
          $item['schedules'][] = [
              'id'    => (int)$s->schedule_id,
              'label' => Carbon::parse($s->start_time)->format('g:i A').' - '.Carbon::parse($s->end_time)->format('g:i A'),
              'cutoff'=> optional($s->pivot)->cutoff_hour,
              'lead'  => optional($s->pivot)->lead_days,
          ];
      }
      $toursPayload[] = $item;
  }

  // ===== Resumen de overrides (para la pestaña Resumen)
  $tourOverrides = [];       // tours con override
  $scheduleOverrides = [];   // filas pivot con override

  foreach ($tours as $t) {
      $hasTourOverride = ($t->cutoff_hour || !is_null($t->lead_days));
      if ($hasTourOverride) {
          $tourOverrides[] = [
              'tour'   => $t->name,
              'cutoff' => $t->cutoff_hour ?: '—',
              'lead'   => is_null($t->lead_days) ? '—' : $t->lead_days,
          ];
      }
      foreach ($t->schedules as $s) {
          $pCut = optional($s->pivot)->cutoff_hour;
          $pLd  = optional($s->pivot)->lead_days;
          if ($pCut || !is_null($pLd)) {
              $scheduleOverrides[] = [
                  'tour'    => $t->name,
                  'schedule'=> Carbon::parse($s->start_time)->format('g:i A').' - '.Carbon::parse($s->end_time)->format('g:i A'),
                  'cutoff'  => $pCut ?: '—',
                  'lead'    => is_null($pLd) ? '—' : $pLd,
              ];
          }
      }
  }
@endphp

<div class="container-fluid">

  <div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="mb-0">Booking Settings</h2>
    <div class="text-muted small">
      <i class="far fa-clock me-1"></i>Server time ({{ config('app.timezone') }}):
      <span class="fw-semibold">{{ $now->format('d/m/Y H:i') }}</span>
    </div>
  </div>

  {{-- TABS NAV --}}
  <ul id="settingsTabs" class="nav nav-pills mb-3" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pane-global" type="button" role="tab">
        <i class="fas fa-globe-americas me-1"></i> Global (default)
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-tour" type="button" role="tab">
        <i class="fas fa-route me-1"></i> Override por Tour
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-schedule" type="button" role="tab">
        <i class="fas fa-clock me-1"></i> Override por Horario
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-summary" type="button" role="tab">
        <i class="fas fa-list-ul me-1"></i> Resumen
      </button>
    </li>
    <li class="nav-item ms-auto" role="presentation">
      <button class="nav-link" data-bs-toggle="pill" data-bs-target="#pane-help" type="button" role="tab">
        <i class="fas fa-info-circle me-1"></i> Ayuda
      </button>
    </li>
  </ul>

  <div class="tab-content">

    {{-- ========== GLOBAL ========== --}}
    <div class="tab-pane fade show active" id="pane-global" role="tabpanel">
      <div class="card shadow-sm">
        <div class="card-body">
          <form method="POST" action="{{ route('admin.settings.booking.update') }}" id="form-global">
            @csrf
            @method('PUT')

            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Cutoff hour (24h)</label>
                <input
                  type="time"
                  class="form-control @error('cutoff_hour') is-invalid @enderror"
                  name="cutoff_hour"
                  value="{{ old('cutoff_hour', $cutoff) }}"
                  required
                  pattern="^(?:[01]\d|2[0-3]):[0-5]\d$"
                >
                @error('cutoff_hour')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <div class="form-hint mt-1">
                  Ej: <span class="kbd">18:00</span>. Después de esta hora, <em>“mañana”</em> deja de estar disponible.
                </div>
              </div>

              <div class="col-md-4">
                <label class="form-label">Lead days</label>
                <input
                  type="number"
                  class="form-control @error('lead_days') is-invalid @enderror"
                  name="lead_days"
                  value="{{ old('lead_days', $lead) }}"
                  min="0" max="30" required
                >
                @error('lead_days')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <div class="form-hint mt-1">
                  Días mínimos de antelación si aún no se pasó el cutoff (0 permite reservar “hoy”).
                </div>
              </div>

              <div class="col-md-4">
                <label class="form-label">Timezone</label>
                <input type="text" class="form-control" value="{{ $tz }}" disabled>
                <div class="form-hint mt-1">Se toma de <code>config('app.timezone')</code>.</div>
              </div>
            </div>

            <div class="mt-3">
              <button class="btn btn-success">
                <i class="fas fa-save me-1"></i> Save global
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- ========== TOUR ========== --}}
    <div class="tab-pane fade" id="pane-tour" role="tabpanel">
      <div class="card shadow-sm">
        <div class="card-body">

          {{-- Selección de tour --}}
          <div class="row g-3 align-items-end">
            <div class="col-md-6">
              <label class="form-label">Tour</label>
              <select class="form-select" id="tourSelect" aria-label="Select tour">
                <option value="">— Select tour —</option>
                @foreach($tours as $t)
                  <option
                    value="{{ $t->tour_id }}"
                    data-cutoff="{{ $t->cutoff_hour }}"
                    data-lead="{{ $t->lead_days }}"
                  >
                    {{ $t->name }}
                  </option>
                @endforeach
              </select>
              <div class="form-hint mt-1">Primero elige un tour. Luego define su override (opcional).</div>
            </div>

            <div class="col-md-6">
              <div class="alert p-3 mb-0" style="background:#f8f9fa;">
                <strong>Estado:</strong>
                <span class="badge badge-inherit" id="tourBadge">Inherits Global</span>
                <span class="ms-2 helper"><i class="far fa-lightbulb me-1"></i>Deja vacío para heredar.</span>
              </div>
            </div>
          </div>

          <hr>

          {{-- Form override por tour --}}
          <form method="POST" action="{{ route('admin.settings.booking.tour.update') }}" id="form-tour">
            @csrf
            @method('PUT')
            <input type="hidden" name="tour_id" id="tourIdHiddenForTour" value="">

            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label">Cutoff (24h)</label>
                <input type="time" class="form-control" name="cutoff_hour" id="tourCutoff" placeholder="--:--">
              </div>
              <div class="col-md-3">
                <label class="form-label">Lead days</label>
                <input type="number" class="form-control" name="lead_days" id="tourLead" min="0" max="30" placeholder="—">
              </div>
              <div class="col-md-6 d-flex gap-2 align-items-end">
                <button class="btn btn-primary">
                  <i class="fas fa-save me-1"></i> Save tour override
                </button>
                <button type="button" class="btn btn-outline-secondary" id="clearTourOverride">
                  Clear override
                </button>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>

    {{-- ========== SCHEDULE/PIVOT ========== --}}
    <div class="tab-pane fade" id="pane-schedule" role="tabpanel">
      <div class="card shadow-sm">
        <div class="card-body">

          <form method="POST" action="{{ route('admin.settings.booking.schedule.update') }}" id="form-schedule">
            @csrf
            @method('PUT')

            {{-- Tour --}}
            <div class="mb-3">
              <label class="form-label">Tour</label>
              <select class="form-select" id="tourForSchedule">
                <option value="">— Select tour —</option>
                @foreach($tours as $t)
                  <option value="{{ $t->tour_id }}">{{ $t->name }}</option>
                @endforeach
              </select>
              <div class="form-hint mt-1">1) Selecciona el tour.</div>
            </div>

            {{-- Horario --}}
            <div class="mb-3">
              <label class="form-label">Horario</label>
              <select class="form-select" id="scheduleSelect" name="schedule_id" required disabled>
                <option value="">— Select time —</option>
              </select>
              <div class="form-hint mt-1">2) Elige el horario del tour.</div>
            </div>

            {{-- Estado --}}
            <div class="mb-3">
              <div class="alert p-3 mb-0" style="background:#f8f9fa;">
                <strong>Estado:</strong>
                <span class="badge badge-global" id="schBadge">Hereda del Tour/Global</span>
                <span class="ms-2 helper"><i class="far fa-lightbulb me-1"></i>Deja vacío para heredar.</span>
              </div>
            </div>

            <hr>

            {{-- Hidden --}}
            <input type="hidden" name="tour_id" id="tourIdHidden" value="">

            {{-- Overrides --}}
            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label">Cutoff (24h)</label>
                <input type="time" class="form-control" name="pivot_cutoff_hour" id="schCutoff" placeholder="--:--">
              </div>
              <div class="col-md-3">
                <label class="form-label">Lead days</label>
                <input type="number" class="form-control" name="pivot_lead_days" id="schLead" min="0" max="30" placeholder="—">
              </div>
              <div class="col-md-6 d-flex gap-2 align-items-end">
                <button class="btn btn-primary">
                  <i class="fas fa-save me-1"></i> Save schedule override
                </button>
                <button type="button" class="btn btn-outline-secondary" id="clearScheduleOverride">
                  Clear override
                </button>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>

    {{-- ========== RESUMEN ========== --}}
    <div class="tab-pane fade" id="pane-summary" role="tabpanel">
      <div class="card shadow-sm mb-4">
        <div class="card-header">
          <strong>Overrides por Tour</strong>
        </div>
        <div class="card-body">
          @if(empty($tourOverrides))
            <div class="text-muted">No hay overrides a nivel tour.</div>
          @else
            <div class="table-responsive">
              <table class="table table-sm table-striped align-middle">
                <thead>
                  <tr>
                    <th>Tour</th>
                    <th>Cutoff</th>
                    <th>Lead days</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($tourOverrides as $row)
                    <tr>
                      <td>{{ $row['tour'] }}</td>
                      <td>{{ $row['cutoff'] }}</td>
                      <td>{{ $row['lead'] }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>

      <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between">
          <strong>Overrides por Horario (pivot)</strong>
          <input type="search" class="form-control form-control-sm search-input" id="searchPivot" placeholder="Buscar tour u horario…">
        </div>
        <div class="card-body">
          @if(empty($scheduleOverrides))
            <div class="text-muted">No hay overrides a nivel horario.</div>
          @else
            <div class="table-responsive">
              <table class="table table-sm table-striped align-middle" id="pivotTable">
                <thead>
                  <tr>
                    <th>Tour</th>
                    <th>Horario</th>
                    <th>Cutoff</th>
                    <th>Lead days</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($scheduleOverrides as $row)
                    <tr>
                      <td>{{ $row['tour'] }}</td>
                      <td>{{ $row['schedule'] }}</td>
                      <td>{{ $row['cutoff'] }}</td>
                      <td>{{ $row['lead'] }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- ========== HELP ========== --}}
    <div class="tab-pane fade" id="pane-help" role="tabpanel">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="mb-2">¿Cómo funciona?</h5>
          <ol class="mb-3">
            <li><strong>Global:</strong> valor por defecto para toda la web.</li>
            <li><strong>Tour:</strong> si un tour tiene <em>cutoff/lead</em>, sobreescribe el global.</li>
            <li><strong>Horario (pivot):</strong> si un horario del tour tiene override, sobreescribe al tour.</li>
          </ol>
          <div class="alert alert-info mb-0">
            <strong>Precedencia:</strong>
            <span class="badge badge-override me-1">Horario (pivot)</span> ➜
            <span class="badge badge-override me-1">Tour</span> ➜
            <span class="badge badge-global me-1">Global</span> ➜
            <code>config/booking.php</code>.
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@push('js')
<script>
(() => {
  // Toasts
  @if(session('success'))
    Swal.fire({ icon:'success', title:'Success', text:@json(session('success')), timer:1800, showConfirmButton:false });
  @endif
  @if ($errors->any())
    Swal.fire({ icon:'error', title:'Error', html:@json(implode('<br>', $errors->all())) });
  @endif

  // Payload tours (para selects del panel Schedule)
  const toursData = @json($toursPayload);

  /* -------------------- TOUR PANEL -------------------- */
  const tourSelect   = document.getElementById('tourSelect');
  const tourCutoff   = document.getElementById('tourCutoff');
  const tourLead     = document.getElementById('tourLead');
  const tourBadge    = document.getElementById('tourBadge');
  const tourIdHiddenForTour = document.getElementById('tourIdHiddenForTour');
  const clearTourBtn = document.getElementById('clearTourOverride');

  function setTourBadge(text, cls) {
    if (!tourBadge) return;
    tourBadge.textContent = text;
    tourBadge.className   = 'badge ' + cls;
  }

  function refreshTourPanel() {
    const opt = tourSelect?.selectedOptions[0];
    tourIdHiddenForTour.value = opt ? opt.value : '';
    if (!opt) {
      tourCutoff.value = '';
      tourLead.value   = '';
      setTourBadge('Inherits Global', 'badge-inherit');
      return;
    }
    const c = opt.dataset.cutoff || '';
    const l = opt.dataset.lead   || '';
    tourCutoff.value = c;
    tourLead.value   = l;
    if (c || l) setTourBadge('Override', 'badge-override');
    else        setTourBadge('Inherits Global', 'badge-inherit');
  }

  tourSelect?.addEventListener('change', refreshTourPanel);
  clearTourBtn?.addEventListener('click', () => { tourCutoff.value=''; tourLead.value=''; });

  /* -------------------- SCHEDULE PANEL -------------------- */
  const tourForSchedule = document.getElementById('tourForSchedule');
  const scheduleSelect  = document.getElementById('scheduleSelect');
  const schCutoff       = document.getElementById('schCutoff');
  const schLead         = document.getElementById('schLead');
  const schBadge        = document.getElementById('schBadge');
  const clearSchBtn     = document.getElementById('clearScheduleOverride');
  const tourIdHidden    = document.getElementById('tourIdHidden');

  function setSchBadge(text, cls) {
    if (!schBadge) return;
    schBadge.textContent = text;
    schBadge.className   = 'badge ' + cls;
  }

  function rebuildSchedules() {
    const tourId = tourForSchedule.value;
    tourIdHidden.value = tourId || '';
    scheduleSelect.innerHTML = '<option value="">— Select time —</option>';
    scheduleSelect.disabled = !tourId;

    schCutoff.value = '';
    schLead.value   = '';
    setSchBadge('Hereda del Tour/Global', 'badge-global');

    if (!tourId) return;
    const t = toursData.find(x => String(x.id) === String(tourId));
    if (!t) return;

    t.schedules.forEach(s => {
      const opt = document.createElement('option');
      opt.value = s.id;
      opt.textContent = s.label;
      opt.dataset.cutoff = s.cutoff || '';
      opt.dataset.lead   = (s.lead ?? '') === null ? '' : s.lead;
      scheduleSelect.appendChild(opt);
    });
  }

  function loadScheduleValues() {
    const opt = scheduleSelect?.selectedOptions[0];
    if (!opt) {
      schCutoff.value = '';
      schLead.value   = '';
      setSchBadge('Hereda del Tour/Global', 'badge-global');
      return;
    }
    const c = opt.dataset.cutoff || '';
    const l = opt.dataset.lead   || '';
    schCutoff.value = c;
    schLead.value   = l;
    if (c || l) setSchBadge('Override', 'badge-override');
    else        setSchBadge('Hereda del Tour/Global', 'badge-global');
  }

  tourForSchedule?.addEventListener('change', rebuildSchedules);
  scheduleSelect?.addEventListener('change', loadScheduleValues);
  clearSchBtn?.addEventListener('click', () => { schCutoff.value=''; schLead.value=''; });

  // Init panels
  refreshTourPanel();
  rebuildSchedules();

  /* -------------------- RESUMEN: búsqueda rápida en pivot -------------------- */
  const searchPivot = document.getElementById('searchPivot');
  const pivotTable  = document.getElementById('pivotTable');

  if (searchPivot && pivotTable) {
    searchPivot.addEventListener('input', () => {
      const q = searchPivot.value.trim().toLowerCase();
      const rows = pivotTable.querySelectorAll('tbody tr');
      rows.forEach(tr => {
        const text = tr.innerText.toLowerCase();
        tr.style.display = text.includes(q) ? '' : 'none';
      });
    });
  }
})();
</script>
@endpush
