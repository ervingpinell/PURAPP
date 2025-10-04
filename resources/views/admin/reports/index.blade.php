@extends('adminlte::page')

@section('title', __('Reportes'))

@push('css')
<style>
  :root{--card-bg:#fff;--card-shadow:0 8px 18px rgba(0,0,0,.06);--text-muted:rgba(0,0,0,.65);--border-soft:rgba(0,0,0,.08)}
  body.dark-mode{--card-bg:#1f2937;--card-shadow:0 8px 18px rgba(0,0,0,.25);--text-muted:rgba(255,255,255,.7);--border-soft:rgba(255,255,255,.08)}
  .card-elevated{background:var(--card-bg);box-shadow:var(--card-shadow);border:1px solid var(--border-soft);border-radius:12px}
  .filter-label{font-size:.85rem;color:var(--text-muted);margin-bottom:.25rem}
  .actions-bar{display:flex;gap:.5rem;flex-wrap:wrap}
  .kpi-card{display:flex;gap:.75rem;padding:1rem;background:var(--card-bg);border:1px solid var(--border-soft);border-radius:12px;height:100%}
  .kpi-icon{font-size:1.05rem;opacity:.85}
  .kpi-label{font-size:.85rem;color:var(--text-muted)}
  .kpi-value{font-size:1.45rem;font-weight:800;line-height:1.1}
  .table td,.table th{vertical-align:middle}
  .table thead th{border-bottom:1px solid var(--border-soft)}
  .table tbody tr+tr td{border-top:1px solid var(--border-soft)}
  .section-title{display:flex;align-items:center;justify-content:space-between;gap:1rem}
  .chip{display:inline-flex;align-items:center;gap:.4rem;padding:.15rem .5rem;border-radius:999px;font-size:.8rem;border:1px solid var(--border-soft);background:var(--card-bg)}
  /* Chart responsive heights */
  .chart-wrap{position:relative;width:100%}
  @media (max-width:575.98px){ .h-chart-1{height:260px} .h-chart-2{height:220px} }
  @media (min-width:576px) and (max-width:991.98px){ .h-chart-1{height:300px} .h-chart-2{height:260px} }
  @media (min-width:992px){ .h-chart-1{height:360px} .h-chart-2{height:280px} }
</style>
@endpush

@section('content_header')
  <h1 class="mb-2">{{ __('Reportes de Ventas') }}</h1>
@stop

@section('content')
<div class="container-fluid">

  {{-- ===== FILTROS ===== --}}
  <div class="card-elevated p-3 mb-3">
    <form method="GET" id="filtersForm">
      <div class="row g-2 align-items-end">
        {{-- Rango rápido (ligero, no ocupa mucho) --}}
        <div class="col-12 col-sm-6 col-lg-3">
          <label class="filter-label">Rango rápido</label>
          @php $preset = request('preset'); @endphp
          <select name="preset" id="preset" class="form-control">
            <option value="">— Selecciona —</option>
            <option value="today"      @selected($preset==='today')>Hoy</option>
            <option value="last7"      @selected($preset==='last7')>Últimos 7 días</option>
            <option value="this_week"  @selected($preset==='this_week')>Esta semana</option>
            <option value="this_month" @selected($preset==='this_month')>Este mes</option>
            <option value="last_month" @selected($preset==='last_month')>Mes pasado</option>
            <option value="this_year"  @selected($preset==='this_year')>Este año</option>
            <option value="last_year"  @selected($preset==='last_year')>Año pasado</option>
          </select>
        </div>

        {{-- Desde / Hasta --}}
        <div class="col-6 col-sm-3 col-lg-2">
          <label class="filter-label">Desde</label>
          <input type="date" name="from" id="from" class="form-control" value="{{ $from->toDateString() }}">
        </div>
        <div class="col-6 col-sm-3 col-lg-2">
          <label class="filter-label">Hasta</label>
          <input type="date" name="to" id="to" class="form-control" value="{{ $to->toDateString() }}">
        </div>

        {{-- Periodo y Agrupar --}}
        <div class="col-6 col-sm-3 col-lg-2">
          <label class="filter-label">Periodo</label>
          @php $periodSel = request('period', $period ?? 'month'); @endphp
          <select name="period" id="period" class="form-control">
            <option value="day"   @selected($periodSel==='day')>Diario</option>
            <option value="week"  @selected($periodSel==='week')>Semanal</option>
            <option value="month" @selected($periodSel==='month')>Mensual</option>
          </select>
        </div>

        <div class="col-6 col-sm-3 col-lg-2">
          <label class="filter-label">Agrupar por</label>
          @php $gb = request('group_by', $groupBy ?? 'booking_date'); @endphp
          <select name="group_by" class="form-control">
            <option value="booking_date" @selected($gb==='booking_date')>Fecha reserva</option>
            <option value="tour_date"     @selected($gb==='tour_date')>Fecha del tour</option>
          </select>
        </div>

        {{-- Acciones --}}
        <div class="col-12 col-lg-1 d-flex gap-2">
          <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary flex-fill">Reset</a>
          <button class="btn btn-primary flex-fill">Aplicar</button>
        </div>

        {{-- Toggle "Más filtros" solo visible en móvil/tablet --}}
        <div class="col-12 d-lg-none mt-2">
          <a class="text-decoration-none" data-bs-toggle="collapse" href="#moreFilters" role="button" aria-expanded="false" aria-controls="moreFilters">
            <i class="fas fa-filter me-1"></i> Más filtros
          </a>
        </div>
      </div>

      {{-- Más filtros: en desktop siempre visible; en mobile dentro de collapse --}}
      <div class="collapse d-lg-block mt-2" id="moreFilters">
        <div class="row g-2">
          <div class="col-12 col-md-6 col-lg-3">
            <label class="filter-label">Estado</label>
            <select name="status" class="form-control">
              <option value="">(Todos)</option>
              @foreach(['paid','confirmed','completed','cancelled'] as $st)
                <option value="{{ $st }}" @selected($status===$st)>{{ ucfirst($st) }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-6 col-lg-4">
            <label class="filter-label">Tours (multi)</label>
            @php $tSel = collect((array)request('tour_id', []))->map(fn($v)=>(int)$v)->all(); @endphp
            <select name="tour_id[]" class="form-control" multiple size="5">
              @foreach($toursMap as $tid => $tname)
                <option value="{{ $tid }}" @selected(in_array((int)$tid, $tSel))>{{ $tname }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-6 col-lg-5">
            <label class="filter-label">Idiomas (multi)</label>
            @php $lSel = collect((array)request('tour_language_id', []))->map(fn($v)=>(int)$v)->all(); @endphp
            <select name="tour_language_id[]" class="form-control" multiple size="5">
              @foreach($langsMap as $lid => $lname)
                <option value="{{ $lid }}" @selected(in_array((int)$lid, $lSel))>{{ $lname }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </form>
  </div>

  {{-- ===== KPIs ===== --}}
  <div class="row g-2 mb-3">
    <div class="col-12 col-sm-6 col-lg-3">
      <div class="kpi-card">
        <div class="kpi-icon mt-1"><i class="fas fa-coins"></i></div>
        <div>
          <div class="kpi-label">Ingresos (rango)</div>
          <div class="kpi-value">$ {{ number_format($kpis['revenue'], 2) }}</div>
          <div class="kpi-label">Ticket Promedio: $ {{ number_format($kpis['atv'], 2) }}</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
      <div class="kpi-card">
        <div class="kpi-icon mt-1"><i class="fas fa-receipt"></i></div>
        <div>
          <div class="kpi-label">Reservas</div>
          <div class="kpi-value">{{ $kpis['bookings'] }}</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
      <div class="kpi-card">
        <div class="kpi-icon mt-1"><i class="fas fa-users"></i></div>
        <div>
          <div class="kpi-label">PAX</div>
          <div class="kpi-value">{{ $kpis['pax'] }}</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
      <div class="kpi-card">
        <div class="kpi-icon mt-1"><i class="fas fa-check-circle"></i></div>
        <div>
          <div class="kpi-label">Reservas confirmadas</div>
          <div class="kpi-value">{{ $confirmedBookings }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- ===== Contenido ===== --}}
  <div class="row g-3">
    <div class="col-12 col-lg-8">
      <div class="card-elevated p-3">
        <div class="section-title">
          <h5 class="mb-0">Ingresos por {{ ['day'=>'día','week'=>'semana','month'=>'mes'][request('period','month')] }}</h5>
          <button id="exportMonthlyCsv" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-csv me-1"></i> CSV</button>
        </div>
        <div class="chart-wrap h-chart-1">
          <canvas id="chartMonthly" aria-label="Ingresos por periodo" role="img"></canvas>
        </div>
      </div>

      <div class="card-elevated mt-3">
        <div class="d-flex align-items-center justify-content-between px-3 pt-3">
          <h5 class="mb-0">Top Tours (por ingresos)</h5>
          <button id="exportTopCsv" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-csv me-1"></i> CSV</button>
        </div>
        <div class="p-0">
          <div class="table-responsive">
            <table class="table mb-0">
              <thead><tr>
                <th style="width:60px">#</th>
                <th>Tour</th>
                <th style="width:140px">Reservas</th>
                <th style="width:120px">PAX</th>
                <th class="text-end" style="width:180px">Ingresos</th>
              </tr></thead>
              <tbody id="topToursTbody">
                @forelse($topTours as $i => $row)
                  <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $toursMap[$row->tour_id] ?? ('#'.$row->tour_id) }}</td>
                    <td>{{ $row->bookings }}</td>
                    <td>{{ $row->pax }}</td>
                    <td class="text-end">$ {{ number_format($row->revenue, 2) }}</td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center p-4">Sin datos</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-4">
      <div class="card-elevated p-3 mb-3">
        <div class="section-title">
          <h5 class="mb-0">Ventas por idioma</h5>
          <button id="exportLangCsv" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-csv"></i></button>
        </div>
        <div class="chart-wrap h-chart-2">
          <canvas id="chartLanguage" aria-label="Ventas por idioma" role="img"></canvas>
        </div>
      </div>

      <div class="card-elevated p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="mb-0">Reservas pendientes</h5>
          <span class="chip"><i class="fas fa-clock"></i> {{ $pendingCount }}</span>
        </div>
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead>
              <tr>
                <th>Ref.</th><th>Cliente</th><th>Tour</th>
                <th>{{ ($gb==='tour_date') ? 'Fecha tour' : 'Fecha reserva' }}</th>
                <th class="text-end">Total</th>
              </tr>
            </thead>
            <tbody>
              @forelse($pendingItems as $p)
                <tr>
                  <td>{{ $p->booking_reference }}</td>
                  <td>{{ $p->customer_email ?? '—' }}</td>
                  <td>{{ $p->tour_name ?? '—' }}</td>
                  <td>
                    @if($gb==='tour_date')
                      {{ optional(\Carbon\Carbon::parse($p->tour_date))->toDateString() }}
                    @else
                      {{ optional(\Carbon\Carbon::parse($p->booking_date))->toDateString() }}
                    @endif
                  </td>
                  <td class="text-end">$ {{ number_format($p->total ?? 0, 2) }}</td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-muted py-3">No hay pendientes</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="small text-muted mt-2">* Hasta 8 según filtros.</div>
      </div>
    </div>
  </div>
</div>
@stop

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
  const $ = s=>document.querySelector(s);
  const fmtMoney = v => '$ ' + Number(v||0).toLocaleString('es-CR',{minimumFractionDigits:2,maximumFractionDigits:2});

  // Preset -> rellena fechas (cliente)
  const preset = $('#preset'), $from=$('#from'), $to=$('#to');
  const pad = n=>String(n).padStart(2,'0');
  const iso = d=>`${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
  const startOfWeek=d=>{const t=new Date(d);const day=(t.getDay()+6)%7;t.setDate(t.getDate()-day);t.setHours(0,0,0,0);return t;}
  const endOfWeek=d=>{const s=startOfWeek(d);const e=new Date(s);e.setDate(s.getDate()+6);e.setHours(23,59,59,999);return e;}
  const startOfMonth=d=>new Date(d.getFullYear(),d.getMonth(),1);
  const endOfMonth=d=>new Date(d.getFullYear(),d.getMonth()+1,0);
  preset?.addEventListener('change',()=>{
    if(!preset.value) return; const today=new Date(); let a=today, b=today;
    switch(preset.value){
      case 'today': a=new Date(today); b=new Date(today); break;
      case 'last7': a=new Date(today); a.setDate(a.getDate()-6); b=new Date(today); break;
      case 'this_week': a=startOfWeek(today); b=endOfWeek(today); break;
      case 'this_month': a=startOfMonth(today); b=endOfMonth(today); break;
      case 'last_month': const lm=new Date(today.getFullYear(),today.getMonth()-1,1); a=startOfMonth(lm); b=endOfMonth(lm); break;
      case 'this_year': a=new Date(today.getFullYear(),0,1); b=new Date(today.getFullYear(),11,31); break;
      case 'last_year': a=new Date(today.getFullYear()-1,0,1); b=new Date(today.getFullYear()-1,11,31); break;
    }
    $from.value=iso(a); $to.value=iso(b);
  });

  // Parámetros (mantengo nombres para no tocar el backend)
  const p = new URLSearchParams({
    from:'{{ $from->toDateString() }}',
    to:'{{ $to->toDateString() }}',
    status:'{{ $status }}',
    group_by:'{{ request('group_by', $groupBy ?? "booking_date") }}',
    period:'{{ request('period', $period ?? "month") }}',
  });
  @php $tSel = collect((array)request('tour_id', []))->map(fn($v)=>(int)$v)->all(); @endphp
  @php $lSel = collect((array)request('tour_language_id', []))->map(fn($v)=>(int)$v)->all(); @endphp
  ({{ json_encode($tSel) }}).forEach(v=>p.append('tour_id[]',v));
  ({{ json_encode($lSel) }}).forEach(v=>p.append('tour_language_id[]',v));

  // Chart 1
  async function loadMonthly(){
    const res = await fetch("{{ route('admin.reports.chart.monthly') }}?"+p.toString());
    const m = await res.json();
    new Chart($('#chartMonthly'),{
      type:'bar',
      data:{ labels:m.labels, datasets:[{label:'Ingresos',data:m.series.revenue,borderWidth:1}] },
      options:{
        responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{display:true}, tooltip:{callbacks:{label:(c)=>` Ingresos: ${fmtMoney(c.parsed.y)}`}} },
        scales:{ y:{ ticks:{ callback:(v)=>fmtMoney(v) } } }
      }
    });
  }

  // Chart 2
  async function loadByLanguage(){
    const res = await fetch("{{ route('admin.reports.chart.language') }}?"+p.toString());
    const l = await res.json(); const langsMap=@json($langsMap);
    new Chart($('#chartLanguage'),{
      type:'bar',
      data:{ labels:l.labels.map(id=>langsMap[id]??('#'+id)), datasets:[{label:'Ingresos',data:l.series.revenue,borderWidth:1}] },
      options:{
        responsive:true, maintainAspectRatio:false,
        plugins:{ legend:{display:true}, tooltip:{callbacks:{label:(c)=>` Ingresos: ${fmtMoney(c.parsed.y)}`}} },
        scales:{ y:{ ticks:{ callback:(v)=>fmtMoney(v) } } }
      }
    });
  }

  // CSV
  const dl=(name,rows)=>{const esc=v=>v==null?'':/[",;\n]/.test(String(v))?`"${String(v).replace(/"/g,'""')}"`:v;const csv=rows.map(r=>r.map(esc).join(';')).join('\n');const b=new Blob([csv],{type:'text/csv;charset=utf-8;'});const u=URL.createObjectURL(b);const a=document.createElement('a');a.href=u;a.download=name;document.body.appendChild(a);a.click();a.remove();URL.revokeObjectURL(u);};
  document.getElementById('exportMonthlyCsv')?.addEventListener('click', async (e)=>{
    e.preventDefault(); const r=await fetch("{{ route('admin.reports.chart.monthly') }}?"+p.toString()); const m=await r.json();
    const rows=[['Periodo','Ingresos','Reservas','PAX']]; m.labels.forEach((lbl,i)=>rows.push([lbl,m.series.revenue[i]??0,m.series.bookings[i]??0,m.series.pax[i]??0]));
    dl(`ingresos-por-periodo_${new Date().toISOString().slice(0,10)}.csv`,rows);
  });
  document.getElementById('exportTopCsv')?.addEventListener('click', (e)=>{
    e.preventDefault(); const rows=[['#','Tour','Reservas','PAX','Ingresos']];
    document.querySelectorAll('#topToursTbody tr').forEach(tr=>{const t=[...tr.querySelectorAll('td')].map(td=>td.innerText.trim()); if(t.length===5) rows.push(t);});
    dl(`top-tours_${new Date().toISOString().slice(0,10)}.csv`,rows);
  });
  document.getElementById('exportLangCsv')?.addEventListener('click', async (e)=>{
    e.preventDefault(); const r=await fetch("{{ route('admin.reports.chart.language') }}?"+p.toString()); const l=await r.json(); const map=@json($langsMap);
    const rows=[['Idioma','Ingresos','Reservas']]; l.labels.forEach((id,i)=>rows.push([map[id]??('#'+id),l.series.revenue[i]??0,l.series.bookings[i]??0]));
    dl(`ventas-por-idioma_${new Date().toISOString().slice(0,10)}.csv`,rows);
  });

  loadMonthly(); loadByLanguage();
})();
</script>
@endpush
