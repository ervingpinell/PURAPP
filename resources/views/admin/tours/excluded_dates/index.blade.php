@extends('adminlte::page')

@section('title', __('m_bookings.excluded_dates.ui.page_title'))

@section('content_header')
  <h1>
    <i class="fas fa-calendar-check me-2"></i>
    {{ __('m_bookings.excluded_dates.ui.page_heading') }}
  </h1>
@stop

@push('css')
<style>
  :root{
    --row-bg:#454d55;
    --title-separator:rgba(255,255,255,.08);
    --pad-x:.85rem;
    --gap:.5rem;
    --sticky-top:64px;
    --fs-base: clamp(.82rem, 1.6vw, .95rem);
    --fs-small: clamp(.74rem, 1.4vw, .9rem);
  }

  .al-title{ color:#fff; padding:.55rem var(--pad-x); border-radius:.25rem; }
  .al-day-header{ display:flex; align-items:center; gap:var(--gap); border:1px solid var(--title-separator); }
  .al-block-title{
    display:flex; align-items:center; gap:var(--gap);
    padding:.5rem var(--pad-x); margin-top:.85rem;
    border:1px solid var(--title-separator); border-radius:.25rem;
  }
  .btn-gap{ display:flex; align-items:center; gap:var(--gap); }

  .list-group-flush{ margin:0; }
  .list-group-flush .list-group-item{
    background:transparent; border:0; margin-bottom:.35rem; padding:0;
  }

  .row-item{
    display:flex; align-items:center; gap:.75rem;
    background:var(--row-bg);
    padding:.6rem var(--pad-x);
    border-radius:.25rem;
    font-size: var(--fs-base);
  }

  .row-item .state{ font-weight:700; }

  /* BADGES DE CAPACIDAD CON NIVELES */
  .row-item .capacity-badge{
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.25rem 0.6rem;
    border-radius: 0.25rem;
    font-size: 0.85em;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    min-width: 70px;
    text-align: center;
    display: inline-block;
    white-space: nowrap;
  }
  .row-item .capacity-badge:hover{
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
  }
  .row-item .capacity-badge.blocked{ background: #dc3545 !important; }
  .row-item .capacity-badge.capacity-level-tour,
  .row-item .capacity-badge.capacity-level-none{ background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
  .row-item .capacity-badge.capacity-level-pivot{ background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
  .row-item .capacity-badge.capacity-level-day{ background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
  .row-item .capacity-badge.capacity-level-day-schedule{ background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

  .row-item .form-check-input{
    margin:0 .5rem 0 0 !important;
    width:18px; height:18px; flex:0 0 18px; position:relative; top:0;
  }
  .al-empty{ padding:.5rem var(--pad-x); }

  .search-input{ min-width:260px }
  .bulk-dd .dropdown-menu{ min-width: 240px; }

  .sticky-filters{
    position: sticky;
    top: var(--sticky-top);
    z-index: 900;
    background: #343a40 !important;
    border-bottom: 1px solid var(--title-separator);
    transition: top .15s ease;
  }
  .sticky-filters.is-stuck{ box-shadow: 0 2px 6px rgba(0,0,0,.15); }

  /* Leyenda de colores */
  .capacity-legend{
    background: #2d3238;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
  }
  .capacity-legend-item{
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-right: 1rem;
    margin-bottom: 0.5rem;
  }
  .capacity-legend-color{
    width: 24px;
    height: 24px;
    border-radius: 0.25rem;
  }

  /* ===== Desktop exacto como el ejemplo ===== */
  @media (min-width: 769px){
    .row-item{
      flex-direction: row;
      align-items: center;
    }
    .btn-gap{
      flex-wrap: nowrap;
    }
    .al-day-header .btn-gap,
    .al-block-title .btn-gap{
      gap: .5rem;
    }
  }

  /* ===== Mobile / tablets pequeñas ===== */
  @media (max-width: 768px){
    .row-item{
      flex-direction: column;
      align-items: stretch;
      gap: .5rem;
    }
    .row-item .flex-grow-1{ order: 2; }
    .row-item .capacity-badge{ order: 3; width: 100%; }
    .row-item .btn-gap{
      order: 4;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: .5rem;
    }
    .row-item .form-check-input{
      order: 1;
      width: 20px; height: 20px;
      align-self: flex-start;
    }
    .sticky-filters{
      padding-top: .5rem !important;
    }
    .sticky-filters .d-flex{ gap: .5rem; }
    .search-input{ min-width: 160px; width: 100%; }
    .btn-gap{ width: 100%; }
    .bulk-dd .dropdown-menu{ min-width: 100%; }
  }
  @media (max-width: 480px){
    .row-item .btn-gap{ grid-template-columns: 1fr; }
    .al-day-header, .al-block-title{
      flex-direction: column;
      align-items: stretch;
      gap: .5rem;
    }
  }
</style>
@endpush

@section('content')

  {{-- Leyenda --}}
  <div class="capacity-legend">
    <h6 class="text-white mb-2"><i class="fas fa-palette me-2"></i>{{ __('m_bookings.excluded_dates.legend.title') }}</h6>
    <div class="d-flex flex-wrap">
      <div class="capacity-legend-item">
        <span class="capacity-legend-color" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></span>
        <small class="text-white">{{ __('m_bookings.excluded_dates.legend.base_tour') }}</small>
      </div>
      <div class="capacity-legend-item">
        <span class="capacity-legend-color" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);"></span>
        <small class="text-white">{{ __('m_bookings.excluded_dates.legend.override_schedule') }}</small>
      </div>
      <div class="capacity-legend-item">
        <span class="capacity-legend-color" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);"></span>
        <small class="text-white">{{ __('m_bookings.excluded_dates.legend.override_day') }}</small>
      </div>
      <div class="capacity-legend-item">
        <span class="capacity-legend-color" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);"></span>
        <small class="text-white">{{ __('m_bookings.excluded_dates.legend.override_day_schedule') }}</small>
      </div>
      <div class="capacity-legend-item">
        <span class="capacity-legend-color" style="background: #dc3545;"></span>
        <small class="text-white">{{ __('m_bookings.excluded_dates.legend.blocked') }}</small>
      </div>
    </div>
  </div>

  {{-- HEADER: Filtros --}}
  <div class="card-header bg-dark text-white d-flex flex-wrap align-items-end gap-2 sticky-filters">
    <form id="filtersForm" method="GET" action="{{ route('admin.tours.excluded_dates.index') }}" class="d-flex flex-wrap align-items-end gap-2 mb-0">
      <div>
        <label class="form-label mb-1">{{ __('m_bookings.excluded_dates.filters.date') }}</label>
        <input
          type="date"
          name="date"
          value="{{ $date }}"
          class="form-control form-control-sm"
          id="filterDate"
          min="{{ \Carbon\Carbon::today(config('app.timezone','America/Costa_Rica'))->toDateString() }}"
        >
      </div>
      <div>
        <label class="form-label mb-1">{{ __('m_bookings.excluded_dates.filters.days') }}</label>
        <input type="number" min="1" max="30" name="days" value="{{ $days }}" class="form-control form-control-sm" style="width:100px" id="filterDays">
      </div>
      <div class="flex-grow-1" style="min-width:260px">
        <label class="form-label mb-1">{{ __('m_bookings.excluded_dates.filters.product') }}</label>
        <input type="text" name="q" value="{{ $q }}" placeholder="{{ __('m_bookings.excluded_dates.filters.search_placeholder') }}" class="form-control form-control-sm search-input" id="filterQ">
      </div>
    </form>

    {{-- Acciones --}}
    <div class="ms-auto d-flex align-items-end gap-2">
      <div class="bulk-dd">
        <div class="btn-group">
          <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown">
            {{ __('m_bookings.excluded_dates.filters.bulk_actions') }}
          </button>
          <div class="dropdown-menu dropdown-menu-right dropdown-menu-end p-2">
            <button type="button" class="btn btn-danger btn-sm w-100" id="bulkBlock">
              <i class="fas fa-ban me-1"></i> {{ __('m_bookings.excluded_dates.buttons.block_selected') }}
            </button>
            <button type="button" class="btn btn-success btn-sm w-100 mt-2" id="bulkUnblock">
              <i class="fas fa-check me-1"></i> {{ __('m_bookings.excluded_dates.buttons.unblock_selected') }}
            </button>
            <hr>
            <button type="button" class="btn btn-info btn-sm w-100" id="bulkSetCapacity">
              <i class="fas fa-users me-1"></i> {{ __('m_bookings.excluded_dates.buttons.set_capacity') }}
            </button>
          </div>
        </div>
      </div>

      <a class="btn btn-warning btn-sm js-view-blocked ms-3"
         href="{{ route('admin.tours.excluded_dates.blocked', ['date' => $date, 'days' => $days, 'q' => $q]) }}">
        <i class="fas fa-lock me-1"></i> {{ __('m_bookings.excluded_dates.buttons.view_blocked') }}
      </a>

      <a class="btn btn-info btn-sm ms-2"
         href="{{ route('admin.tours.capacity.index') }}">
        <i class="fas fa-cog me-1"></i> {{ __('m_bookings.excluded_dates.buttons.capacity_settings') }}
      </a>
    </div>
  </div>

  <div class="card-body p-2">
    <div class="mt-3"></div>

    @php
      $fmt = fn($d) => \Carbon\Carbon::parse($d)->locale(app()->getLocale() ?: 'es')->isoFormat('dddd D [de] MMMM');
    @endphp

    @forelse($calendar as $day => $buckets)
      <div class="mb-4">

        {{-- Día --}}
        <div class="al-title bg-dark al-day-header">
          <div class="fw-bold">
            {{ ucfirst($fmt($day)) }}
            <span class="ms-2 text-success opacity-75">
              ({{ count($buckets['am']) + count($buckets['pm']) }} {{ __('m_bookings.excluded_dates.ui.tours_count') }})
            </span>
          </div>
          <div class="ms-auto btn-gap">
            <button class="btn btn-primary btn-sm js-mark-day"
                    type="button"
                    data-day="{{ $day }}"
                    onclick="toggleMarkDay(this,'{{ $day }}')">
              {{ __('m_bookings.excluded_dates.buttons.mark_all') }}
            </button>
            <button class="btn btn-danger btn-sm" type="button" onclick="blockAllInDay('{{ $day }}')">
              {{ __('m_bookings.excluded_dates.buttons.block_all') }}
            </button>
            <button class="btn btn-info btn-sm" type="button" onclick="setCapacityForDay('{{ $day }}')">
              <i class="fas fa-users"></i> {{ __('m_bookings.excluded_dates.buttons.capacity') }}
            </button>
          </div>
        </div>

        {{-- AM --}}
        <div class="al-title bg-dark al-block-title">
          <span class="fw-bold small mb-0">{{ __('m_bookings.excluded_dates.blocks.am') }}</span>
          <div class="ms-auto btn-gap">
            <button type="button"
                    class="btn btn-primary btn-sm js-mark-block"
                    data-day="{{ $day }}" data-bucket="am"
                    onclick="toggleMarkBlock(this,'{{ $day }}','am')">
              {{ __('m_bookings.excluded_dates.buttons.mark_all') }}
            </button>
            <button type="button" class="btn btn-danger btn-sm" onclick="blockAllInBlock('{{ $day }}','am')">
              {{ __('m_bookings.excluded_dates.buttons.block_all') }}
            </button>
          </div>
        </div>

        <div id="day-{{ $day }}-am" class="list-group list-group-flush mt-2">
          @forelse($buckets['am'] as $it)
            <div class="list-group-item">
              <div class="row-item"
                   data-day="{{ $day }}"
                   data-tid="{{ $it['tour_id'] }}"
                   data-sid="{{ $it['schedule_id'] }}"
                   data-capacity="{{ $it['current_capacity'] ?? 15 }}"
                   data-title="{{ $it['tour_name'] }} ({{ $it['time'] }})">
                <input type="checkbox" class="form-check-input me-2 select-item">
                <div class="flex-grow-1">
                  <span class="me-2">{{ $it['tour_name'] }} ({{ $it['time'] }})</span>
                  <span class="state {{ $it['is_available'] ? 'text-success' : 'text-danger' }}">
                    {{ $it['is_available'] ? __('m_bookings.excluded_dates.states.available') : __('m_bookings.excluded_dates.states.blocked') }}
                  </span>
                </div>

                {{-- Badge de capacidad --}}
                <span class="capacity-badge capacity-level-{{ $it['override_level'] ?? 'none' }} {{ !$it['is_available'] ? 'blocked' : '' }}"
                      onclick="openCapacityModal('{{ $day }}', {{ $it['tour_id'] }}, {{ $it['schedule_id'] }}, '{{ $it['tour_name'] }} ({{ $it['time'] }})', {{ $it['current_capacity'] ?? 15 }})"
                      title="{{ __('m_bookings.excluded_dates.badges.tooltip_prefix') }} {{ ucfirst(str_replace(['-', '_'], ' ', $it['override_level'] ?? 'base')) }}">
                  @if(!$it['is_available'])
                    <i class="fas fa-ban me-1"></i> 0/0
                  @else
                    <i class="fas fa-users me-1"></i> {{ $it['occupied_count'] ?? 0 }}/{{ $it['current_capacity'] ?? 15 }}
                  @endif
                </span>

                <div class="btn-gap">
                  <button type="button" class="btn btn-danger btn-sm btn-block"
                          onclick="confirmToggleOne(this, '{{ $day }}', {{ $it['tour_id'] }}, {{ $it['schedule_id'] }}, 'block')"
                          {{ !$it['is_available'] ? 'disabled' : '' }}>
                    {{ __('m_bookings.excluded_dates.buttons.block') }}
                  </button>
                  <button type="button" class="btn btn-success btn-sm btn-unblock"
                          onclick="confirmToggleOne(this, '{{ $day }}', {{ $it['tour_id'] }}, {{ $it['schedule_id'] }}, 'unblock')"
                          {{ $it['is_available'] ? 'disabled' : '' }}>
                    {{ __('m_bookings.excluded_dates.buttons.unblock') }}
                  </button>
                </div>
              </div>
            </div>
          @empty
            <div class="text-muted al-empty">{{ __('m_bookings.excluded_dates.blocks.empty_am') }}</div>
          @endforelse
        </div>

        {{-- PM --}}
        <div class="al-title bg-dark al-block-title mt-3">
          <span class="fw-bold small mb-0">{{ __('m_bookings.excluded_dates.blocks.pm') }}</span>
          <div class="ms-auto btn-gap">
            <button type="button"
                    class="btn btn-primary btn-sm js-mark-block"
                    data-day="{{ $day }}" data-bucket="pm"
                    onclick="toggleMarkBlock(this,'{{ $day }}','pm')">
              {{ __('m_bookings.excluded_dates.buttons.mark_all') }}
            </button>
            <button type="button" class="btn btn-danger btn-sm" onclick="blockAllInBlock('{{ $day }}','pm')">
              {{ __('m_bookings.excluded_dates.buttons.block_all') }}
            </button>
          </div>
        </div>

        <div id="day-{{ $day }}-pm" class="list-group list-group-flush mt-2">
          @forelse($buckets['pm'] as $it)
            <div class="list-group-item">
              <div class="row-item"
                   data-day="{{ $day }}"
                   data-tid="{{ $it['tour_id'] }}"
                   data-sid="{{ $it['schedule_id'] }}"
                   data-capacity="{{ $it['current_capacity'] ?? 15 }}"
                   data-title="{{ $it['tour_name'] }} ({{ $it['time'] }})">
                <input type="checkbox" class="form-check-input me-2 select-item">
                <div class="flex-grow-1">
                  <span class="me-2">{{ $it['tour_name'] }} ({{ $it['time'] }})</span>
                  <span class="state {{ $it['is_available'] ? 'text-success' : 'text-danger' }}">
                    {{ $it['is_available'] ? __('m_bookings.excluded_dates.states.available') : __('m_bookings.excluded_dates.states.blocked') }}
                  </span>
                </div>

                <span class="capacity-badge capacity-level-{{ $it['override_level'] ?? 'none' }} {{ !$it['is_available'] ? 'blocked' : '' }}"
                      onclick="openCapacityModal('{{ $day }}', {{ $it['tour_id'] }}, {{ $it['schedule_id'] }}, '{{ $it['tour_name'] }} ({{ $it['time'] }})', {{ $it['current_capacity'] ?? 15 }})"
                      title="{{ __('m_bookings.excluded_dates.badges.tooltip_prefix') }} {{ ucfirst(str_replace(['-', '_'], ' ', $it['override_level'] ?? 'base')) }}">
                  @if(!$it['is_available'])
                    <i class="fas fa-ban me-1"></i> 0/0
                  @else
                    <i class="fas fa-users me-1"></i> {{ $it['occupied_count'] ?? 0 }}/{{ $it['current_capacity'] ?? 15 }}
                  @endif
                </span>

                <div class="btn-gap">
                  <button type="button" class="btn btn-danger btn-sm btn-block"
                          onclick="confirmToggleOne(this, '{{ $day }}', {{ $it['tour_id'] }}, {{ $it['schedule_id'] }}, 'block')"
                          {{ !$it['is_available'] ? 'disabled' : '' }}>
                    {{ __('m_bookings.excluded_dates.buttons.block') }}
                  </button>
                  <button type="button" class="btn btn-success btn-sm btn-unblock"
                          onclick="confirmToggleOne(this, '{{ $day }}', {{ $it['tour_id'] }}, {{ $it['schedule_id'] }}, 'unblock')"
                          {{ $it['is_available'] ? 'disabled' : '' }}>
                    {{ __('m_bookings.excluded_dates.buttons.unblock') }}
                  </button>
                </div>
              </div>
            </div>
          @empty
            <div class="text-muted al-empty">{{ __('m_bookings.excluded_dates.blocks.empty_pm') }}</div>
          @endforelse
        </div>

      </div>
    @empty
      <div class="text-muted al-empty">{{ __('m_bookings.excluded_dates.blocks.no_data') }}</div>
    @endforelse
  </div>

  {{-- Modal para ajustar capacidad individual --}}
  <div class="modal fade" id="capacityModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('m_bookings.excluded_dates.modals.capacity_title') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p><strong id="capacityModalLabel"></strong></p>
          <p class="text-muted small">{{ __('m_bookings.excluded_dates.modals.date') }} <span id="capacityModalDate"></span></p>

          <div class="alert alert-info small">
            <i class="fas fa-info-circle me-1"></i>
            <strong>{{ __('m_bookings.excluded_dates.modals.hierarchy_title') }}</strong><br>
            1. {{ __('m_bookings.excluded_dates.legend.override_day_schedule') }}<br>
            2. {{ __('m_bookings.excluded_dates.legend.override_day') }}<br>
            3. {{ __('m_bookings.excluded_dates.legend.override_schedule') }}<br>
            4. {{ __('m_bookings.excluded_dates.legend.base_tour') }}
          </div>

          <div class="mb-3">
            <label class="form-label">{{ __('m_bookings.excluded_dates.modals.new_capacity') }}</label>
            <input type="number" id="capacityInput" class="form-control" min="0" max="999" value="15">
            <small class="text-muted">{{ __('m_bookings.excluded_dates.modals.hint_zero_blocks') }}</small>
          </div>

          <input type="hidden" id="capacityModalTourId">
          <input type="hidden" id="capacityModalScheduleId">
          <input type="hidden" id="capacityModalDay">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_bookings.excluded_dates.buttons.cancel') }}</button>
          <button type="button" class="btn btn-primary" onclick="saveCapacity()">{{ __('m_bookings.excluded_dates.buttons.save') }}</button>
        </div>
      </div>
    </div>
  </div>

  {{-- Modal para capacidad masiva --}}
  <div class="modal fade" id="bulkCapacityModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ __('m_bookings.excluded_dates.modals.selected_capacity_title') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p>{!! __('m_bookings.excluded_dates.modals.selected_count', ['count' => '<strong id="bulkCapacityCount">0</strong>']) !!}</p>
          <div class="mb-3">
            <label class="form-label">{{ __('m_bookings.excluded_dates.modals.new_capacity') }}</label>
            <input type="number" id="bulkCapacityInput" class="form-control" min="0" max="999" value="15">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('m_bookings.excluded_dates.buttons.cancel') }}</button>
          <button type="button" class="btn btn-primary" onclick="saveBulkCapacity()">{{ __('m_bookings.excluded_dates.buttons.apply') }}</button>
        </div>
      </div>
    </div>
  </div>

@stop
@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- ===== I18N seguro contra Blade ===== --}}
@php
  $I18N = [
    'invalid_date_title'     => __('m_bookings.excluded_dates.toasts.invalid_date_title'),
    'invalid_date_text'      => __('m_bookings.excluded_dates.toasts.invalid_date_text'),
    'applying_filters'       => __('m_bookings.excluded_dates.toasts.applying_filters'),
    'searching'              => __('m_bookings.excluded_dates.toasts.searching'),
    'updating_range'         => __('m_bookings.excluded_dates.toasts.updating_range'),
    'marked_n'               => __('m_bookings.excluded_dates.toasts.marked_n'),
    'unmarked_n'             => __('m_bookings.excluded_dates.toasts.unmarked_n'),
    'no_selection_title'     => __('m_bookings.excluded_dates.toasts.no_selection_title'),
    'no_selection_text'      => __('m_bookings.excluded_dates.toasts.no_selection_text'),
    'no_changes_title'       => __('m_bookings.excluded_dates.toasts.no_changes_title'),
    'no_changes_text'        => __('m_bookings.excluded_dates.toasts.no_changes_text'),
    'updated'                => __('m_bookings.excluded_dates.toasts.updated'),
    'updated_count'          => __('m_bookings.excluded_dates.toasts.updated_count'),
    'unblocked_count'        => __('m_bookings.excluded_dates.toasts.unblocked_count'),
    'error_generic'          => __('m_bookings.excluded_dates.toasts.error_generic'),
    'confirm_block_title'    => __('m_bookings.excluded_dates.confirm.block_title'),
    'confirm_unblock_title'  => __('m_bookings.excluded_dates.confirm.unblock_title'),
    'confirm_block_html'     => __('m_bookings.excluded_dates.confirm.block_html'),
    'confirm_unblock_html'   => __('m_bookings.excluded_dates.confirm.unblock_html'),
    'confirm_block_btn'      => __('m_bookings.excluded_dates.confirm.block_btn'),
    'confirm_unblock_btn'    => __('m_bookings.excluded_dates.confirm.unblock_btn'),
    'block'                  => __('m_bookings.excluded_dates.buttons.block'),
    'unblock'                => __('m_bookings.excluded_dates.buttons.unblock'),
    'capacity_updated'       => __('m_bookings.excluded_dates.toasts.capacity_updated'),

    // Para el toggle dinámico (con fallback si aún no existen en lang)
    'block_all_btn'          => __('m_bookings.excluded_dates.buttons.block_all', [], false),
    'unblock_all_btn'        => __('m_bookings.excluded_dates.buttons.unblock_all', [], false),
    'fallback_block_all'     => 'Bloquear Todos',
    'fallback_unblock_all'   => 'Desbloquear Todos',
  ];
@endphp
<script>
const I18N = {!! json_encode($I18N, JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) !!};

// Helpers para etiquetas (usa lang o fallback)
function tBlockAll(){ return I18N.block_all_btn || I18N.fallback_block_all; }
function tUnblockAll(){ return I18N.unblock_all_btn || I18N.fallback_unblock_all; }

const TOGGLE_URL   = @json(route('admin.tours.excluded_dates.toggle'));
const BULK_URL     = @json(route('admin.tours.excluded_dates.bulkToggle'));
const CAPACITY_URL = @json(route('admin.tours.capacity.store'));
const CSRF         = @json(csrf_token());

const toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 1600,
  timerProgressBar: true
});

/* ===== Sticky header ===== */
(function(){
  const root = document.documentElement;
  const mainHeader = document.querySelector('.main-header');
  const contentHdr = document.querySelector('.content-header');
  const stickyBar = document.querySelector('.sticky-filters');

  let Hm = 0, idleTop = 0, tightTop = 0;

  function recalc() {
    Hm = mainHeader?.offsetHeight || 0;
    const Hc = contentHdr?.offsetHeight || 0;
    idleTop = Hm + Hc + 8;
    tightTop = Hm;
    apply();
  }
  function apply() {
    const chBottom = contentHdr ? contentHdr.getBoundingClientRect().bottom : 0;
    const wantTop = chBottom <= Hm ? tightTop : idleTop;
    root.style.setProperty('--sticky-top', wantTop + 'px');
    if(stickyBar){ stickyBar.classList.toggle('is-stuck', wantTop === tightTop); }
  }

  recalc();
  window.addEventListener('scroll', apply, {passive:true});
  window.addEventListener('resize', recalc, {passive:true});
  window.addEventListener('load', recalc);
})();

/* ===== Filtros ===== */
const form = document.getElementById('filtersForm');
const iDate = document.getElementById('filterDate');
const iDays = document.getElementById('filterDays');
const iQ = document.getElementById('filterQ');

function todayStr(){
  const now = new Date();
  const tzOff = now.getTimezoneOffset()*60000;
  return new Date(Date.now()-tzOff).toISOString().slice(0,10);
}

iDate.addEventListener('change', () => {
  const t = todayStr();
  if(iDate.value < t){
    Swal.fire(I18N.invalid_date_title, I18N.invalid_date_text, 'info');
    iDate.value = t;
    return;
  }
  iDays.value = 1;
  iQ.value = '';
  toast.fire({icon:'info', title:I18N.applying_filters});
  form.submit();
});

function debounce(fn, ms){ let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), ms); }; }

iQ.addEventListener('input', debounce(() => {
  if(iQ.value.trim().length > 0) iDays.value = Math.max(Number(iDays.value||1), 30);
  toast.fire({icon:'info', title:I18N.searching});
  form.submit();
}, 400));

iDays.addEventListener('change', () => {
  let v = parseInt(iDays.value || '1', 10);
  if(isNaN(v) || v < 1) v = 1;
  if(v > 30) v = 30;
  iDays.value = v;
  toast.fire({icon:'info', title:I18N.updating_range});
  form.submit();
});

/* ===== Utilidades de estado por scope ===== */
function isRowBlocked(row){
  // bloqueado si badge tiene .blocked o el texto está en "Bloqueado"
  const state = row.querySelector('.state');
  const badge = row.querySelector('.capacity-badge');
  const byBadge = badge?.classList.contains('blocked');
  const byState = state?.classList.contains('text-danger');
  return !!(byBadge || byState);
}

function scopeCounts(rows){
  let total = 0, blocked = 0;
  rows.forEach(r => { total++; if(isRowBlocked(r)) blocked++; });
  return { total, blocked };
}

// === NUEVO: texto + color dinámico (rojo para bloquear, verde para desbloquear)
function setScopeBtnAppearance(btn, allBlocked){
  // allBlocked=true => el siguiente paso es "unblock" (verde)
  const want = allBlocked ? 'unblock' : 'block';
  btn.dataset.want = want;

  btn.classList.remove('btn-danger', 'btn-success');
  if (want === 'block') btn.classList.add('btn-danger');
  else btn.classList.add('btn-success');

  btn.textContent = allBlocked ? tUnblockAll() : tBlockAll();
}

// Compat con funciones existentes
function setScopeBtnLabel(btn, allBlocked){
  setScopeBtnAppearance(btn, allBlocked);
}

function refreshScopeButtonsFor(day){
  // Día completo
  const dayRows = document.querySelectorAll(`.row-item[data-day="${day}"]`);
  const dayBtn  = document.querySelector(`.al-day-header .js-toggle-day[data-day="${day}"]`);
  if(dayBtn){
    const c = scopeCounts(dayRows);
    setScopeBtnAppearance(dayBtn, c.total > 0 && c.blocked === c.total);
  }
  // AM / PM
  ['am','pm'].forEach(bucket => {
    const blockRows = document.querySelectorAll(`#day-${day}-${bucket} .row-item`);
    const headerBtn = document.querySelector(`.al-block-title .js-toggle-block[data-day="${day}"][data-bucket="${bucket}"]`);
    if(headerBtn){
      const c = scopeCounts(blockRows);
      setScopeBtnAppearance(headerBtn, c.total > 0 && c.blocked === c.total);
    }
  });
}

/* ===== Toggle individual ===== */
async function toggleOne(el, day, tourId, scheduleId, want){
  const row = el.closest('.row-item');
  const btnBlock = row.querySelector('.btn-block');
  const btnUnblock = row.querySelector('.btn-unblock');

  btnBlock.disabled = true;
  btnUnblock.disabled = true;

  try{
    const res = await fetch(TOGGLE_URL, {
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
      body: JSON.stringify({ tour_id:tourId, schedule_id:scheduleId, date:day, want })
    });
    const data = await res.json();
    if(!data.ok) throw new Error('bad response');

    const state = row.querySelector('.state');
    const available = !!data.is_available;
    state.textContent = available ? '{{ __('m_bookings.excluded_dates.states.available') }}' : '{{ __('m_bookings.excluded_dates.states.blocked') }}';
    state.classList.toggle('text-success', available);
    state.classList.toggle('text-danger', !available);

    btnBlock.disabled = !available;
    btnUnblock.disabled = available;

    const capacityBadge = row.querySelector('.capacity-badge');
    if(!available){
      capacityBadge.classList.add('blocked');
      capacityBadge.innerHTML = '<i class="fas fa-ban me-1"></i> 0/0';
    } else {
      capacityBadge.classList.remove('blocked');
      const cap = row.dataset.capacity || 15;
      capacityBadge.innerHTML = `<i class="fas fa-users me-1"></i> 0/${cap}`;
    }

    // Actualiza botones de scope del día y sus bloques:
    refreshScopeButtonsFor(day);

    toast.fire({icon:'success', title: available ? I18N.unblock : I18N.block });
  }catch(e){
    console.error(e);
    Swal.fire('Error', I18N.error_generic, 'error');
  } finally {
    btnBlock.disabled = row.querySelector('.state')?.classList.contains('text-danger');
    btnUnblock.disabled = row.querySelector('.state')?.classList.contains('text-success');
  }
}

async function confirmToggleOne(el, day, tourId, scheduleId, want){
  const row = el.closest('.row-item');
  const label = row?.dataset.title || '—';
  const isBlock = want === 'block';

  const res = await Swal.fire({
    icon: 'warning',
    title: isBlock ? I18N.confirm_block_title : I18N.confirm_unblock_title,
    html: (isBlock ? I18N.confirm_block_html : I18N.confirm_unblock_html)
            .replace(':label', label).replace(':day', day),
    showCancelButton: true,
    confirmButtonText: isBlock ? I18N.confirm_block_btn : I18N.confirm_unblock_btn,
    cancelButtonText: '{{ __('m_bookings.excluded_dates.buttons.cancel') }}'
  });

  if(res.isConfirmed){
    await toggleOne(el, day, tourId, scheduleId, want);
  }
}

/* ===== Gestión de capacidad ===== */
let capacityModalInstance;

function openCapacityModal(day, tourId, scheduleId, label, currentCapacity){
  document.getElementById('capacityModalLabel').textContent = label;
  document.getElementById('capacityModalDate').textContent = day;
  document.getElementById('capacityInput').value = currentCapacity;
  document.getElementById('capacityModalTourId').value = tourId;
  document.getElementById('capacityModalScheduleId').value = scheduleId;
  document.getElementById('capacityModalDay').value = day;

  if(!capacityModalInstance){
    capacityModalInstance = new bootstrap.Modal(document.getElementById('capacityModal'));
  }
  capacityModalInstance.show();
}

async function saveCapacity(){
  const tourId = document.getElementById('capacityModalTourId').value;
  const scheduleId = document.getElementById('capacityModalScheduleId').value;
  const day = document.getElementById('capacityModalDay').value;
  const capacity = parseInt(document.getElementById('capacityInput').value);

  try{
    const res = await fetch(CAPACITY_URL, {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
      body: JSON.stringify({
        tour_id: tourId,
        schedule_id: scheduleId,
        date: day,
        max_capacity: capacity > 0 ? capacity : null,
        is_blocked: capacity === 0
      })
    });

    if(!res.ok) throw new Error('Failed to save capacity');

    const row = document.querySelector(`.row-item[data-day="${day}"][data-tid="${tourId}"][data-sid="${scheduleId}"]`);
    if(row){
      row.dataset.capacity = capacity;
      const badge = row.querySelector('.capacity-badge');

      if(capacity === 0){
        badge.classList.add('blocked');
        badge.innerHTML = '<i class="fas fa-ban me-1"></i> 0/0';

        const state = row.querySelector('.state');
        state.textContent = '{{ __('m_bookings.excluded_dates.states.blocked') }}';
        state.classList.remove('text-success');
        state.classList.add('text-danger');

        row.querySelector('.btn-block').disabled = true;
        row.querySelector('.btn-unblock').disabled = false;
      } else {
        badge.classList.remove('blocked');
        badge.classList.remove('capacity-level-tour', 'capacity-level-pivot', 'capacity-level-day', 'capacity-level-none');
        badge.classList.add('capacity-level-day-schedule');
        badge.innerHTML = `<i class="fas fa-users me-1"></i> 0/${capacity}`;
      }
    }

    // Refresca etiquetas/colores de botones de scope
    refreshScopeButtonsFor(day);

    capacityModalInstance.hide();
    toast.fire({icon:'success', title:I18N.capacity_updated});
    setTimeout(() => window.location.reload(), 1000);
  }catch(e){
    console.error(e);
    Swal.fire('Error', I18N.error_generic, 'error');
  }
}

function setCapacityForDay(day){
  Swal.fire({
    title: '{{ __('m_bookings.excluded_dates.modals.capacity_day_title') }}',
    html: `<strong>${day}</strong><br>{{ __('m_bookings.excluded_dates.modals.capacity_day_subtitle') }}`,
    input: 'number',
    inputAttributes: { min: 0, max: 999, step: 1 },
    inputValue: 15,
    showCancelButton: true,
    confirmButtonText: '{{ __('m_bookings.excluded_dates.buttons.apply') }}',
    cancelButtonText: '{{ __('m_bookings.excluded_dates.buttons.cancel') }}'
  }).then(async (result) => {
    if(result.isConfirmed){
      const capacity = parseInt(result.value);
      const rows = document.querySelectorAll(`.row-item[data-day="${day}"]`);

      let updated = 0;
      for(const row of rows){
        const tourId = row.dataset.tid;
        const scheduleId = row.dataset.sid;

        try{
          await fetch(CAPACITY_URL, {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
            body: JSON.stringify({
              tour_id: tourId,
              schedule_id: scheduleId,
              date: day,
              max_capacity: capacity > 0 ? capacity : null,
              is_blocked: capacity === 0
            })
          });
          updated++;
        }catch(e){
          console.error(e);
        }
      }

      // Refresca etiquetas/colores
      refreshScopeButtonsFor(day);

      toast.fire({icon:'success', title: I18N.updated_count.replace(':count', updated) });
      setTimeout(() => window.location.reload(), 1000);
    }
  });
}

/* ===== Acciones masivas ===== */
function collectSelected(){
  const sel = [];
  document.querySelectorAll('.row-item').forEach(r => {
    const cb = r.querySelector('.select-item');
    if(cb && cb.checked){
      sel.push({
        tour_id: r.dataset.tid,
        schedule_id: r.dataset.sid,
        date: r.dataset.day,
        _label: r.dataset.title
      });
    }
  });
  return sel;
}

document.getElementById('bulkBlock').addEventListener('click', async () => {
  const items = collectSelected();
  if(!items.length){
    Swal.fire(I18N.no_selection_title, I18N.no_selection_text, 'info');
    return;
  }

  const res = await Swal.fire({
    icon: 'warning',
    title: '{{ __('m_bookings.excluded_dates.confirm.bulk_title') }}',
    html: '{{ __('m_bookings.excluded_dates.confirm.bulk_items_html', ['count' => ':count']) }}'.replace(':count', items.length),
    showCancelButton: true,
    confirmButtonText: '{{ __('m_bookings.excluded_dates.buttons.block') }}',
    cancelButtonText: '{{ __('m_bookings.excluded_dates.buttons.cancel') }}'
  });

  if(res.isConfirmed) await bulkToggle(items, 'block');
});

document.getElementById('bulkUnblock').addEventListener('click', async () => {
  const items = collectSelected();
  if(!items.length){
    Swal.fire(I18N.no_selection_title, I18N.no_selection_text, 'info');
    return;
  }

  const res = await Swal.fire({
    icon: 'warning',
    title: '{{ __('m_bookings.excluded_dates.confirm.bulk_title') }}',
    html: '{{ __('m_bookings.excluded_dates.confirm.bulk_items_html', ['count' => ':count']) }}'.replace(':count', items.length),
    showCancelButton: true,
    confirmButtonText: '{{ __('m_bookings.excluded_dates.buttons.unblock') }}',
    cancelButtonText: '{{ __('m_bookings.excluded_dates.buttons.cancel') }}'
  });

  if(res.isConfirmed) await bulkToggle(items, 'unblock');
});

document.getElementById('bulkSetCapacity').addEventListener('click', () => {
  const items = collectSelected();
  if(!items.length){
    Swal.fire(I18N.no_selection_title, I18N.no_selection_text, 'info');
    return;
  }

  document.getElementById('bulkCapacityCount').textContent = items.length;
  const bulkModal = new bootstrap.Modal(document.getElementById('bulkCapacityModal'));
  bulkModal.show();
});

async function saveBulkCapacity(){
  const items = collectSelected();
  const capacity = parseInt(document.getElementById('bulkCapacityInput').value);

  let updated = 0;
  for(const item of items){
    try{
      await fetch(CAPACITY_URL, {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({
          tour_id: item.tour_id,
          schedule_id: item.schedule_id,
          date: item.date,
          max_capacity: capacity > 0 ? capacity : null,
          is_blocked: capacity === 0
        })
      });
      updated++;
    }catch(e){
      console.error(e);
    }
  }

  bootstrap.Modal.getInstance(document.getElementById('bulkCapacityModal')).hide();
  document.querySelectorAll('.select-item:checked').forEach(cb => cb.checked = false);

  toast.fire({icon:'success', title: I18N.updated_count.replace(':count', updated) });
  setTimeout(() => window.location.reload(), 1000);
}

async function bulkToggle(items, want){
  try{
    const res = await fetch(BULK_URL, {
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
      body: JSON.stringify({ items, want })
    });
    const data = await res.json();
    if(!data.ok) throw new Error();

    document.querySelectorAll('.select-item:checked').forEach(cb => cb.checked = false);

    // Después de bulk, refresca estado de botones por día
    const affectedDays = [...new Set(items.map(i => i.date))];
    affectedDays.forEach(d => refreshScopeButtonsFor(d));

    Swal.fire({
      icon:'success',
      title:I18N.updated,
      html:I18N.updated_count.replace(':count', data.changed || 0),
      timer:1300,
      showConfirmButton:false
    });

    setTimeout(() => window.location.reload(), 1300);
  }catch(e){
    console.error(e);
    Swal.fire('Error', I18N.error_generic, 'error');
  }
}

/* ===== Toggle de scope (Día / Bloque) con detección de estado ===== */
function blockAllInDay(day){
  const rows = document.querySelectorAll(`.row-item[data-day="${day}"]`);
  const { total, blocked } = scopeCounts(rows);
  const want = (total > 0 && blocked === total) ? 'unblock' : 'block';
  confirmBulkFromRows(rows, want, day, 'day');
}

function blockAllInBlock(day, bucket){
  const rows = document.querySelectorAll(`#day-${day}-${bucket} .row-item`);
  const { total, blocked } = scopeCounts(rows);
  const want = (total > 0 && blocked === total) ? 'unblock' : 'block';
  const label = `${day} — ${bucket.toUpperCase()}`;
  confirmBulkFromRows(rows, want, label, 'block');
}

async function confirmBulkFromRows(rows, want, contextLabel = '', scope = 'day'){
  const items = [];
  rows.forEach(r => {
    const currentlyBlocked = isRowBlocked(r);
    if ((want === 'block' && !currentlyBlocked) || (want === 'unblock' && currentlyBlocked)) {
      items.push({
        tour_id: r.dataset.tid,
        schedule_id: r.dataset.sid,
        date: r.dataset.day,
        _label: r.dataset.title
      });
    }
  });

  if (!items.length) {
    Swal.fire(I18N.no_changes_title, I18N.no_changes_text, 'info');
    return;
  }

  const countHtml = '{{ __('m_bookings.excluded_dates.confirm.bulk_items_html', ['count' => ':count']) }}'
                      .replace(':count', items.length);
  const ctxLine   = contextLabel ? `<div class="mb-1"><strong>${contextLabel}</strong></div>` : '';

  const res = await Swal.fire({
    icon: 'warning',
    title: want === 'block' ? I18N.confirm_block_title : I18N.confirm_unblock_title,
    html: ctxLine + countHtml,
    showCancelButton: true,
    confirmButtonText: want === 'block'
      ? '{{ __('m_bookings.excluded_dates.buttons.block') }}'
      : '{{ __('m_bookings.excluded_dates.buttons.unblock') }}',
    cancelButtonText: '{{ __('m_bookings.excluded_dates.buttons.cancel') }}'
  });

  if (res.isConfirmed) {
    await bulkToggle(items, want);
  }
}

/* ===== Marcar/Desmarcar selección ===== */
function getDayCheckboxes(day){ return document.querySelectorAll(`.row-item[data-day="${day}"] .select-item`); }
function getBlockCheckboxes(day, bucket){ return document.querySelectorAll(`#day-${day}-${bucket} .select-item`); }
function areAllChecked(list){ const arr = Array.from(list); return arr.length > 0 && arr.every(cb => cb.checked); }
function setBtnLabel(btn, allChecked){
  btn.textContent = allChecked
    ? '{{ __('m_bookings.excluded_dates.buttons.unmark_all') }}'
    : '{{ __('m_bookings.excluded_dates.buttons.mark_all') }}';
}

function refreshMarkLabelsFor(day){
  const dayCbs = getDayCheckboxes(day);
  document.querySelectorAll(`.js-mark-day[data-day="${day}"]`).forEach(btn => setBtnLabel(btn, areAllChecked(dayCbs)));

  ['am','pm'].forEach(bucket => {
    const blockCbs = getBlockCheckboxes(day, bucket);
    document.querySelectorAll(`.js-mark-block[data-day="${day}"][data-bucket="${bucket}"]`).forEach(btn => setBtnLabel(btn, areAllChecked(blockCbs)));
  });
}

function toggleMarkDay(btn, day){
  const cbs = getDayCheckboxes(day);
  const all = areAllChecked(cbs);
  cbs.forEach(cb => cb.checked = !all);
  refreshMarkLabelsFor(day);
  const txt = (!all ? I18N.marked_n : I18N.unmarked_n).replace(':n', cbs.length);
  toast.fire({icon:'info', title: txt});
}

function toggleMarkBlock(btn, day, bucket){
  const cbs = getBlockCheckboxes(day, bucket);
  const all = areAllChecked(cbs);
  cbs.forEach(cb => cb.checked = !all);
  refreshMarkLabelsFor(day);
  const txt = (!all ? I18N.marked_n : I18N.unmarked_n).replace(':n', cbs.length);
  toast.fire({icon:'info', title: txt});
}

/* ===== Inicio ===== */
document.addEventListener('change', (e) => {
  if(!e.target.classList.contains('select-item')) return;
  const day = e.target.closest('.row-item')?.dataset.day;
  if(day){
    refreshMarkLabelsFor(day);
    refreshScopeButtonsFor(day);
  }
});

document.addEventListener('DOMContentLoaded', () => {
  // Detecta y marca el botón de scope del DÍA (bloquear/desbloquear todos del header del día)
  document.querySelectorAll('.al-day-header').forEach(dayHdr => {
    const day = dayHdr.querySelector('.js-mark-day')?.dataset.day;
    if(!day) return;

    const btns = dayHdr.querySelectorAll('button');
    btns.forEach(b => {
      if (b.getAttribute('onclick')?.startsWith('blockAllInDay(')) {
        b.classList.add('js-toggle-day');
        b.dataset.day = day;
        // Color inicial acorde al estado actual
      }
    });
    refreshScopeButtonsFor(day);
    refreshMarkLabelsFor(day);
  });

  // Detecta y marca los botones de scope por BLOQUE (AM/PM)
  document.querySelectorAll('.al-block-title').forEach(blockHdr => {
    const markBtn = blockHdr.querySelector('.js-mark-block');
    if(!markBtn) return;
    const day    = markBtn.dataset.day;
    const bucket = markBtn.dataset.bucket;

    const btns = blockHdr.querySelectorAll('button');
    btns.forEach(b => {
      if (b.getAttribute('onclick')?.startsWith(`blockAllInBlock('${day}','${bucket}')`)) {
        b.classList.add('js-toggle-block');
        b.dataset.day = day;
        b.dataset.bucket = bucket;
      }
    });

    refreshScopeButtonsFor(day);
  });
});
</script>
@endpush
