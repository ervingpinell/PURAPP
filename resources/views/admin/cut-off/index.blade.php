@extends('adminlte::page')

@section('title', __('m_config.cut-off.title'))

@push('css')
<style>
  :root{
    --tabbar-bg:#1f2937; --tabbar-border:#111827;
    --tab-inactive-bg:#374151; --tab-inactive-text:#e5e7eb;
    --tab-active-bg:#16a34a; --tab-active-text:#fff;
  }
  #settingsTabs{background:var(--tabbar-bg);padding:.5rem;border-radius:.85rem;border:1px solid var(--tabbar-border);box-shadow:0 1px 2px rgba(0,0,0,.25) inset;}
  #settingsTabs .nav-link{background:var(--tab-inactive-bg);color:var(--tab-inactive-text);border:1px solid transparent;margin-right:.5rem;border-radius:.65rem;transition:.15s;}
  #settingsTabs .nav-link:hover{filter:brightness(1.06);transform:translateY(-1px);}
  #settingsTabs .nav-link.active{background:var(--tab-active-bg);color:var(--tab-active-text);border-color:rgba(0,0,0,.15);box-shadow:0 2px 6px rgba(0,0,0,.25);}
  .card{border-radius:.6rem;}
  .helper,.form-hint{color:#6c757d;font-size:.9rem;}
  .kbd{padding:.12rem .4rem;border:1px solid #adb5bd;border-bottom-width:2px;border-radius:.25rem;font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas,"Courier New",monospace;}
  .badge-inherit{background:#0dcaf0;}
  .badge-override{background:#ffc107;color:#000;}
  .badge-global{background:#6c757d;}
  .table thead th{white-space:nowrap;}
  .table td,.table th{vertical-align:middle;}
  .search-input{max-width:360px;}
  .status-pill{background:#0b1320;border:1px solid #0f172a;border-radius:.65rem;padding:.5rem .75rem;display:inline-flex;align-items:center;gap:.5rem}
  .status-pill .label{color:#cbd5e1;font-weight:600}
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
@php
  use Carbon\Carbon;

  // build payload para JS
  $toursPayload = [];
  foreach ($tours as $t) {
      $item = [
          'id'   => (int)$t->product_id,
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

  // resumen con IDs (para summary editable)
  $tourOverrides = [];
  $scheduleOverrides = [];
  foreach ($tours as $t) {
      $hasTourOverride = ($t->cutoff_hour || !is_null($t->lead_days));
      if ($hasTourOverride) {
          $tourOverrides[] = [
            'product_id' => (int)$t->product_id,
            'tour'    => $t->name,
            'cutoff'  => $t->cutoff_hour ?: '—',
            'lead'    => is_null($t->lead_days) ? '—' : $t->lead_days,
          ];
      }
      foreach ($t->schedules as $s) {
          $pCut = optional($s->pivot)->cutoff_hour;
          $pLd  = optional($s->pivot)->lead_days;
          if ($pCut || !is_null($pLd)) {
              $scheduleOverrides[] = [
                'product_id'     => (int)$t->product_id,
                'schedule_id' => (int)$s->schedule_id,
                'tour'        => $t->name,
                'schedule'    => Carbon::parse($s->start_time)->format('g:i A').' - '.Carbon::parse($s->end_time)->format('g:i A'),
                'cutoff'      => $pCut ?: '—',
                'lead'        => is_null($pLd) ? '—' : $pLd,
              ];
          }
      }
  }
@endphp

<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="mb-0">{{ __('m_config.cut-off.header') }}</h2>
    <div class="text-muted small">
      <i class="far fa-clock me-1"></i>{{ __('m_config.cut-off.server_time', ['tz'=>$tz]) }}:
      <span class="fw-semibold">{{ $now->format('d/m/Y H:i') }}</span>
    </div>
  </div>

  {{-- Tabs --}}
  <ul id="settingsTabs" class="nav nav-pills mb-3" role="tablist">
    <li class="nav-item">
      <button class="nav-link active" data-toggle="pill" data-target="#pane-global" type="button">
        <i class="fas fa-globe-americas me-1"></i> {{ __('m_config.cut-off.tabs.global') }}
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link" data-toggle="pill" data-target="#pane-tour" type="button">
        <i class="fas fa-route me-1"></i> {{ __('m_config.cut-off.tabs.tour') }}
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link" data-toggle="pill" data-target="#pane-schedule" type="button">
        <i class="fas fa-clock me-1"></i> {{ __('m_config.cut-off.tabs.schedule') }}
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link" data-toggle="pill" data-target="#pane-summary" type="button">
        <i class="fas fa-list-ul me-1"></i> {{ __('m_config.cut-off.tabs.summary') }}
      </button>
    </li>
    <li class="nav-item ms-auto">
      <button class="nav-link" data-toggle="pill" data-target="#pane-help" type="button">
        <i class="fas fa-info-circle me-1"></i> {{ __('m_config.cut-off.tabs.help') }}
      </button>
    </li>
  </ul>

  <div class="tab-content">
    <div id="pane-global" class="tab-pane fade show active">
      @include('admin.cut-off.partials.global', ['cutoff'=>$cutoff,'lead'=>$lead,'tz'=>$tz])
    </div>
    <div id="pane-tour" class="tab-pane fade">
      @include('admin.cut-off.partials.tour', ['tours'=>$tours])
    </div>
    <div id="pane-schedule" class="tab-pane fade">
      @include('admin.cut-off.partials.schedule', ['tours'=>$tours,'toursPayload'=>$toursPayload])
    </div>
    <div id="pane-summary" class="tab-pane fade">
      {{-- Usa la versión editable que ya hicimos; importante que los forms lleven name="from_summary" --}}
      @include('admin.cut-off.partials.summary', ['tourOverrides'=>$tourOverrides,'scheduleOverrides'=>$scheduleOverrides])
    </div>
    <div id="pane-help" class="tab-pane fade">
      @include('admin.cut-off.partials.help')
    </div>
  </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // SweetAlert sin toast
  @if (session('success'))
    Swal.fire({ icon:'success', title:@json(__('m_config.cut-off.flash.success_title')), text:@json(session('success')) });
  @endif
  @if (session('error'))
    Swal.fire({ icon:'error', title:@json(__('m_config.cut-off.flash.error_title')), text:@json(session('error')) });
  @endif

  // === Persistencia de pestañas (query param + hash + localStorage) ===
  const KEY = 'cutoffActiveTab';
  const url = new URL(window.location.href);
  const qsTab = url.searchParams.get('tab'); // 'global'|'tour'|'schedule'|'summary'|'help'
  const hash  = url.hash;                    // '#pane-...'
  const list  = document.querySelectorAll('#settingsTabs [data-toggle="pill"]');

  function showTabByTarget(targetSel) {
    const trigger = document.querySelector(`#settingsTabs [data-target="${targetSel}"]`);
    if (trigger) {
      new bootstrap.Tab(trigger).show();
      const short = targetSel.replace('#pane-', '');
      history.replaceState({}, '', `${window.location.pathname}?tab=${short}${targetSel}`);
      localStorage.setItem(KEY, targetSel);
    }
  }

  // Decide prioridad: query ?tab -> hash -> localStorage -> #pane-global
  let initialTarget = '#pane-global';
  if (qsTab) {
    initialTarget = `#pane-${qsTab}`;
  } else if (hash) {
    initialTarget = hash;
  } else {
    initialTarget = localStorage.getItem(KEY) || '#pane-global';
  }
  showTabByTarget(initialTarget);

  // Cuando el usuario cambia de pestaña, persiste en URL + localStorage
  list.forEach(el => {
    el.addEventListener('shown.bs.tab', (ev) => {
      const sel = ev.target.getAttribute('data-target'); // '#pane-...'
      const short = sel.replace('#pane-','');
      history.replaceState({}, '', `${window.location.pathname}?tab=${short}${sel}`);
      localStorage.setItem(KEY, sel);
    });
  });
});
</script>
@endpush
