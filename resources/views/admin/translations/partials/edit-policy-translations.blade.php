@php
    /** @var \App\Models\Policy $item */
    // Variables esperadas: $type, $item, $locale
    $tableId = 'sections-table-' . $item->policy_id;
@endphp

@if ($type === 'policies' && $item instanceof \App\Models\Policy)
  <div class="card mb-4 border-info">
    <div class="card-header bg-info text-white">
      <strong>
        <i class="fas fa-layer-group me-2"></i>
        {{ __('policies.sections_title', ['policy' => ($item->translation()?->name ?? $item->name)]) }}
      </strong>
      <span class="badge bg-dark ms-2">{{ strtoupper($locale) }}</span>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table id="{{ $tableId }}" class="table align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th style="width: 90px;">{{ __('policies.id') }}</th>
              <th>{{ __('policies.name_base') }}</th>
              <th>{{ __('policies.translation_name') }}</th>
              <th>{{ __('policies.translation_content') }}</th>
              <th style="width: 80px;" class="text-center">{{ __('policies.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($item->sections as $index => $section)
              @php
                $translatedSection = $section->translation($locale) ?? $section->translation('es');
                $collapseId = "sec-edit-{$section->section_id}";
              @endphp

              {{-- Fila resumen --}}
              <tr>
                <td>#{{ $section->section_id }}</td>
                <td class="text-break">
                  <span class="badge bg-success">{{ $section->name ?: __('policies.untitled') }}</span>
                </td>
                <td class="text-break">
                  {{ $translatedSection?->name ?: __('policies.section') . ' #' . ($index+1) }}
                </td>
                <td class="text-break">
                  <div class="small">
                    {!! nl2br(e(\Illuminate\Support\Str::limit($translatedSection?->content ?? __('policies.no_content'), 160))) !!}
                  </div>
                </td>
                <td class="text-center">
                  <button
                    class="btn btn-sm btn-primary"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#{{ $collapseId }}"
                    aria-expanded="false"
                    aria-controls="{{ $collapseId }}"
                    title="{{ __('policies.edit') }}"
                  >
                    <i class="fas fa-edit"></i>
                  </button>
                </td>
              </tr>

              {{-- Fila de edición (collapse en DIV dentro del TD) --}}
              <tr>
                <td colspan="5" class="bg-light">
                  <div id="{{ $collapseId }}" class="collapse" data-bs-parent="#{{ $tableId }}">
                    <div class="p-3 border rounded">
                      <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-dark me-2">#{{ $section->section_id }}</span>
                        <span class="text-muted">
                          {{ __('policies.name_base') }}:
                          <strong>{{ $section->name ?: '—' }}</strong>
                        </span>
                        <span class="ms-auto small text-muted">
                          {{ __('policies.editing_locale') ?? __('m_config.translations.select_language_title') }}:
                          <strong>{{ strtoupper($locale) }}</strong>
                        </span>
                      </div>

                      <div class="row g-3">
                        <div class="col-md-6">
                          <label class="form-label">{{ __('policies.translation_name') }}</label>
                          <input
                            type="text"
                            class="form-control"
                            name="section_translations[{{ $section->section_id }}][name]"
                            value="{{ old("section_translations.{$section->section_id}.name", $translatedSection?->name) }}"
                          >
                        </div>
                        <div class="col-12">
                          <label class="form-label">{{ __('policies.translation_content') }}</label>
                          <textarea
                            class="form-control"
                            rows="4"
                            name="section_translations[{{ $section->section_id }}][content]"
                          >{{ old("section_translations.{$section->section_id}.content", $translatedSection?->content) }}</textarea>
                        </div>
                      </div>

                      <div class="text-end mt-3">
                        <button
                          class="btn btn-sm btn-secondary js-collapse-close"
                          type="button"
                          data-collapse-target="#{{ $collapseId }}"
                        >
                          <i class="fas fa-chevron-up me-1"></i> {{ __('policies.close') }}
                        </button>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted">
                  {{ __('policies.no_sections') }}
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="p-3 text-muted small">
        <i class="fas fa-info-circle me-1"></i>
        {{ __('policies.bulk_edit_hint') }}
      </div>
    </div>
  </div>

  {{-- JS: cierre garantizado del collapse --}}
  <script>
    (function () {
      if (window.__policySectionCollapseInit) return;
      window.__policySectionCollapseInit = true;

      document.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-collapse-close');
        if (!btn) return;

        const targetSel = btn.getAttribute('data-collapse-target');
        if (!targetSel) return;

        const el = document.querySelector(targetSel);
        if (!el) return;

        try {
          const instance = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
          instance.hide();
        } catch (err) {
          el.classList.remove('show');
        }
      });
    })();
  </script>
@endif
