<div class="accordion-item border-0 border-bottom">
    <h2 class="accordion-header" id="headingHotels">
        <button class="accordion-button bg-white px-0 shadow-none collapsed"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#collapseHotels"
            aria-expanded="false"
            aria-controls="collapseHotels">
            <span class="me-2 d-inline-flex align-items-center" aria-hidden="true">
                <i class="fas fa-plus icon-plus"></i>
                <i class="fas fa-minus icon-minus"></i>
            </span>
            {{ __('adminlte::adminlte.hotels_meeting_points') }}
        </button>
    </h2>

    <div id="collapseHotels" class="accordion-collapse collapse"
        data-bs-parent="#tourDetailsAccordion">
        <div class="accordion-body px-0">

            {{-- 📝 Nota general --}}
            <div class="mt-3">
                <strong>{{ __('adminlte::adminlte.pickup_details') }}</strong>
                <p>{{ __('adminlte::adminlte.pickup_note') }}</p>
            </div>

            <div class="row">
                {{-- 📍 SECCIÓN HOTEL --}}
                <div class="col-md-6 mb-4">
                    <h6 class="mb-1">
                        <i class="fas fa-shuttle-van me-1"></i>
                        {{ __('adminlte::adminlte.pickup_points') }}
                    </h6>
                    <p class="text-muted small mb-2">{{ __('adminlte::adminlte.select_pickup') }}</p>

                    <div class="position-relative">
                        <input type="text"
                            class="form-control border rounded px-3 py-2 ps-4"
                            id="pickupInput"
                            name="pickup_name"
                            placeholder="{{ __('adminlte::adminlte.type_to_search') }}"
                            autocomplete="off"
                            style="padding-left: 2rem;">
                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted"></i>
                    </div>

                    <ul class="list-group mt-2 d-none" id="pickupList" style="max-height: 220px; overflow-y: auto;">
                        @foreach($hotels as $hotel)
                        <li class="list-group-item list-group-item-action pickup-option"
                            data-id="{{ $hotel->hotel_id }}"
                            data-name="{{ $hotel->name }}">
                            <i class="fas fa-hotel me-2 text-success"></i>
                            {{ $hotel->name }}
                        </li>
                        @endforeach
                        {{-- Opción "Otro" manual --}}
                        <li class="list-group-item list-group-item-action pickup-option text-warning fw-bold"
                            data-id="other"
                            data-name="{{ __('adminlte::adminlte.hotel_other') }}">
                            <i class="fas fa-question-circle me-2"></i>
                            {{ __('adminlte::adminlte.other_hotel_option') }}
                        </li>
                    </ul>

                    <div id="pickupValidMsg" class="text-success small mt-2 d-none">
                        ✔️ {{ __('adminlte::adminlte.pickup_valid') }}
                    </div>
                    {{-- Mensaje de advertencia para "Otro" o fuera de área --}}
                    <div id="pickupWarningMsg" class="alert alert-warning mt-2 d-none small">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        {{ __('adminlte::adminlte.custom_pickup_notice') }}
                    </div>

                    <input type="hidden" name="selected_pickup_point" id="selectedPickupPoint" value="">
                </div>

                {{-- 📍 SECCIÓN MEETING POINT --}}
                @if($meetingPoints->count() > 0)
                <div class="col-md-6 mb-4">
                    <h6 class="mb-1">
                        <i class="fas fa-map-marked-alt me-1"></i>
                        {{ __('adminlte::adminlte.meeting_points') }}
                    </h6>
                    <p class="text-muted small mb-2">{{ __('adminlte::adminlte.select_meeting') }}</p>

                    <div class="position-relative">
                        <input type="text"
                            class="form-control border rounded px-3 py-2 ps-4"
                            id="meetingInput"
                            placeholder="{{ __('adminlte::adminlte.type_to_search') }}"
                            autocomplete="off"
                            style="padding-left: 2rem;">
                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted"></i>
                    </div>

                    <ul class="list-group mt-2 d-none" id="meetingList" style="max-height: 220px; overflow-y: auto;">
                        @foreach($meetingPoints as $mp)
                        <li class="list-group-item list-group-item-action meeting-option"
                            data-id="{{ $mp->id }}"
                            data-name="{{ method_exists($mp,'getTranslated') ? $mp->getTranslated('name') : ($mp->name ?? '') }}"
                            data-description="{{ method_exists($mp,'getTranslated') ? $mp->getTranslated('description') : ($mp->description ?? '') }}"
                            data-url="{{ $mp->map_url }}">
                            <i class="fas fa-location-dot me-2 text-success"></i>
                            <div class="d-inline">
                                <strong>{{ method_exists($mp,'getTranslated') ? $mp->getTranslated('name') : ($mp->name ?? '') }}</strong>
                                @if(!empty($mp->pickup_time))
                                <small class="text-muted ms-2">{{ $mp->pickup_time }}</small>
                                @endif
                            </div>
                        </li>
                        @endforeach
                    </ul>

                    {{-- Detalles del punto seleccionado --}}
                    <div id="meetingDetails" class="meeting-details-card d-none mt-3">
                        <div class="d-flex align-items-start gap-2">
                            <div class="icon bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <p class="fw-bold mb-1">{{ __('adminlte::adminlte.meeting_point_details') }}</p>
                                <p class="small text-muted mb-2" id="meetingDesc"></p>
                                <a href="#" id="meetingMapLink" target="_blank" class="btn btn-sm btn-outline-success d-none">
                                    <i class="fas fa-map me-1"></i> {{ __('adminlte::adminlte.open_map') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Mensajes de validación --}}
                    <div id="meetingValidMsg" class="text-success small mt-2 d-none">
                        ✔️ {{ __('adminlte::adminlte.meeting_valid') }}
                    </div>

                    <input type="hidden" name="selected_meeting_point" id="selectedMeetingPoint" value="">
                </div>
                @endif
            </div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- CLEANED UP JS (No Radio Logic) ---


        /* ---------- PICKUP (Hoteles) ---------- */
        const pickupInput = document.getElementById('pickupInput');
        const pickupList = document.getElementById('pickupList');
        const pickupItems = pickupList ? pickupList.querySelectorAll('.pickup-option') : [];
        const pickupOK = document.getElementById('pickupValidMsg');
        const pickupWarn = document.getElementById('pickupWarningMsg');
        const pickupHidden = document.getElementById('selectedPickupPoint');

        const showPickupList = () => pickupList?.classList.remove('d-none');
        const hidePickupList = () => pickupList?.classList.add('d-none');

        const filterPickup = (q) => {
            const term = (q || '').toLowerCase();
            pickupItems.forEach(li => {
                const name = (li.dataset.name || '').toLowerCase();
                // Always show 'other' option even if filtering, or maybe only if no matches?
                // User wants it visible. Let's keep it visible always if searching or empty?
                // Actually good UX: always show 'Other' at bottom.
                if (li.dataset.id === 'other') {
                    li.classList.remove('d-none');
                } else {
                    li.classList.toggle('d-none', term && !name.includes(term));
                }
            });
        };

        const selectPickup = (id, name) => {
            pickupHidden.value = id || '';
            pickupInput.value = name || '';

            if (id === 'other') {
                pickupOK.classList.add('d-none');
                pickupWarn.classList.remove('d-none');
            } else if (id) {
                pickupOK.classList.remove('d-none');
                pickupWarn.classList.add('d-none');
            } else {
                pickupOK.classList.add('d-none');
                pickupWarn.classList.add('d-none');
            }
        };

        pickupInput?.addEventListener('focus', () => {
            showPickupList();
            filterPickup(pickupInput.value);
        });
        pickupInput?.addEventListener('input', () => {
            showPickupList();
            filterPickup(pickupInput.value);
        });
        pickupItems.forEach(li => li.addEventListener('click', () => {
            selectPickup(li.dataset.id, li.dataset.name);
            hidePickupList();
        }));
        document.addEventListener('click', e => {
            if (pickupList && !pickupList.contains(e.target) && e.target !== pickupInput) hidePickupList();
        });

        /* ---------- MEETING POINT ---------- */
        const meetingInput = document.getElementById('meetingInput');
        const meetingList = document.getElementById('meetingList');
        const meetingItems = meetingList ? meetingList.querySelectorAll('.meeting-option') : [];
        const meetingOK = document.getElementById('meetingValidMsg');
        const meetingHidden = document.getElementById('selectedMeetingPoint');
        const detailsBox = document.getElementById('meetingDetails');
        const descEl = document.getElementById('meetingDesc');
        const mapLink = document.getElementById('meetingMapLink');

        const showMeetingList = () => meetingList?.classList.remove('d-none');
        const hideMeetingList = () => meetingList?.classList.add('d-none');

        const filterMeeting = (q) => {
            const term = (q || '').toLowerCase();
            meetingItems.forEach(li => {
                const name = (li.dataset.name || '').toLowerCase();
                li.classList.toggle('d-none', term && !name.includes(term));
            });
        };

        const selectMeeting = (id, name, desc = '', url = '') => {
            meetingHidden.value = id || '';
            meetingInput.value = name || '';

            // Mostrar detalles si existen
            if (desc) {
                descEl.textContent = desc;
                detailsBox.classList.remove('d-none');
                if (url) {
                    mapLink.href = url;
                    mapLink.classList.remove('d-none');
                } else {
                    mapLink.classList.add('d-none');
                }
            } else {
                detailsBox.classList.add('d-none');
            }

            // Mensajes
            if (id) {
                meetingOK.classList.remove('d-none');
            } else {
                meetingOK.classList.add('d-none');
            }
        };

        meetingInput?.addEventListener('focus', () => {
            showMeetingList();
            filterMeeting(meetingInput.value);
        });
        meetingInput?.addEventListener('input', () => {
            showMeetingList();
            filterMeeting(meetingInput.value);
        });

        meetingItems.forEach(li => li.addEventListener('click', () => {
            selectMeeting(li.dataset.id, li.dataset.name, li.dataset.description, li.dataset.url);
            hideMeetingList();
        }));

        document.addEventListener('click', e => {
            if (meetingList && !meetingList.contains(e.target) && e.target !== meetingInput) hideMeetingList();
        });
    });
</script>

@push('css')
<style>
    .meeting-details-card {
        background-color: #f9fdf9;
        border: 1px solid rgba(0, 0, 0, .08);
        border-left: 4px solid var(--primary-color, #60a862);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        box-shadow: 0 3px 10px rgba(0, 0, 0, .05);
        transition: all .2s ease-in-out;
    }

    .meeting-details-card:hover {
        box-shadow: 0 5px 14px rgba(0, 0, 0, .1);
        transform: translateY(-2px);
    }

    .meeting-details-card .icon {
        background: #eaf8eb;
        color: #2e7d32;
        font-size: 1.1rem;
    }

    .pickup-type-radio:checked+label {
        font-weight: bold;
        color: var(--primary-color, #28a745);
    }
</style>
@endpush
