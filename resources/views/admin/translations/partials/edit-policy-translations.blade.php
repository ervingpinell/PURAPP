{{-- resources/views/admin/translations/partials/edit-policy-translations.blade.php --}}
@php
    /** @var \App\Models\Policy $item */
    // $type, $item, $locale vienen desde admin/translations/edit.blade.php
@endphp

@if ($type === 'policies' && $item instanceof \App\Models\Policy)
  {{-- EXISTENTES --}}
  <div class="card mb-3">
    <div class="card-header bg-info text-white">
      <i class="fas fa-list-alt me-2"></i> Secciones existentes ({{ strtoupper($locale) }})
    </div>

    <div class="card-body p-0">
      @if($item->sections->isEmpty())
        <div class="p-3 text-muted">Esta política no tiene secciones todavía.</div>
      @else
        <div class="accordion" id="policySectionsAdmin">
          @foreach($item->sections as $idx => $section)
            @php
              $st  = $section->translation($locale) ?? $section->translation(config('app.fallback_locale'));
              $sid = "policy-sec-{$section->section_id}";
            @endphp

            <div class="accordion-item border-0 border-bottom">
              <h2 class="accordion-header" id="heading-{{ $sid }}">
                <button
                  class="accordion-button bg-white px-3 shadow-none collapsed"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#collapse-{{ $sid }}"
                  aria-expanded="false"
                  aria-controls="collapse-{{ $sid }}"
                >
                  <span class="me-2 d-inline-flex align-items-center" aria-hidden="true">
                    <i class="fas fa-plus fa-fw icon-plus"></i>
                    <i class="fas fa-minus fa-fw icon-minus d-none"></i>
                  </span>
                  {{ $st?->title ?: "Sección #".($idx+1) }}
                  <small class="ms-2 text-muted">#{{ $section->section_id }}</small>
                </button>
              </h2>

              <div id="collapse-{{ $sid }}" class="accordion-collapse collapse">
                <div class="accordion-body">
                  <div class="row g-3">
                    {{-- Metadatos de la sección --}}
                    <div class="col-md-3">
                      <label class="form-label">Orden</label>
                      <input type="number" class="form-control"
                             name="section_meta[{{ $section->section_id }}][sort_order]"
                             value="{{ old("section_meta.{$section->section_id}.sort_order", $section->sort_order) }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               id="secActive{{ $section->section_id }}"
                               name="section_meta[{{ $section->section_id }}][is_active]"
                               value="1" {{ old("section_meta.{$section->section_id}.is_active", $section->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="secActive{{ $section->section_id }}">Activa</label>
                      </div>
                    </div>

                    {{-- Traducción --}}
                    <div class="col-12">
                      <label class="form-label">Título ({{ strtoupper($locale) }})</label>
                      <input type="text" class="form-control"
                             name="section_translations[{{ $section->section_id }}][title]"
                             value="{{ old("section_translations.{$section->section_id}.title", $st?->title) }}">
                    </div>

                    <div class="col-12">
                      <label class="form-label">Contenido ({{ strtoupper($locale) }})</label>
                      <textarea class="form-control" rows="6"
                                name="section_translations[{{ $section->section_id }}][content]">{{ old("section_translations.{$section->section_id}.content", $st?->content) }}</textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>

  {{-- NUEVAS --}}
  <div class="card mb-4">
    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
      <span><i class="fas fa-plus-circle me-2"></i> Agregar nuevas secciones ({{ strtoupper($locale) }})</span>
      <button type="button" class="btn btn-light btn-sm" id="btnAddNewSection">
        <i class="fas fa-plus"></i> Añadir sección
      </button>
    </div>

    <div class="card-body" id="newSectionsContainer">
      {{-- Aquí se inyectan bloques de nuevas secciones vía JS --}}
      <p class="text-muted mb-0" id="newSectionsEmptyHint">No has agregado nuevas secciones todavía.</p>
    </div>
  </div>

  @push('css')
  <style>
    .accordion-button::after{ content:none!important; }
    .accordion-button .icon-plus,.accordion-button .icon-minus{ width:1em; text-align:center; }
    .accordion-button.collapsed .icon-plus{ display:inline-block!important; }
    .accordion-button.collapsed .icon-minus{ display:none!important; }
    .accordion-button:not(.collapsed) .icon-plus{ display:none!important; }
    .accordion-button:not(.collapsed) .icon-minus{ display:inline-block!important; }

    .new-section-card{ background:rgba(0,0,0,.02); border:1px dashed #bbb; border-radius:.5rem; padding:1rem; margin-bottom:1rem; }
    .dark-mode .new-section-card{ background:rgba(255,255,255,.03); }
  </style>
  @endpush

  @push('js')
  <script>
  (function(){
    // Alterna clase collapsed para +/− en existentes
    const q  = (s,r=document)=>r.querySelector(s);
    const qa = (s,r=document)=>Array.prototype.slice.call(r.querySelectorAll(s));

    qa('#policySectionsAdmin .accordion-collapse').forEach(col=>{
      const btn=q(`[data-bs-target="#${col.id}"]`);
      if(!btn) return;
      if(col.classList.contains('show')){
        btn.classList.remove('collapsed'); btn.setAttribute('aria-expanded','true');
      }else{
        btn.classList.add('collapsed'); btn.setAttribute('aria-expanded','false');
      }
    });
    document.addEventListener('shown.bs.collapse',ev=>{
      const id=ev.target.id, btn=q(`[data-bs-target="#${id}"]`);
      if(btn){ btn.classList.remove('collapsed'); btn.setAttribute('aria-expanded','true'); }
    });
    document.addEventListener('hidden.bs.collapse',ev=>{
      const id=ev.target.id, btn=q(`[data-bs-target="#${id}"]`);
      if(btn){ btn.classList.add('collapsed'); btn.setAttribute('aria-expanded','false'); }
    });

    // Añadir nuevas secciones
    const container = document.getElementById('newSectionsContainer');
    const emptyHint = document.getElementById('newSectionsEmptyHint');
    const addBtn    = document.getElementById('btnAddNewSection');
    let newIdx = 0;

    function addNewSection(){
      if(emptyHint) emptyHint.style.display = 'none';
      const idx = newIdx++;

      const wrapper = document.createElement('div');
      wrapper.className = 'new-section-card';
      wrapper.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-2">
          <strong>Nueva sección</strong>
          <button type="button" class="btn btn-sm btn-outline-danger btnRemoveNew">
            <i class="fas fa-trash"></i> Quitar
          </button>
        </div>

        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Orden</label>
            <input type="number" class="form-control" name="new_sections[${idx}][sort_order]" value="${idx+1}">
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="newActive${idx}" name="new_sections[${idx}][is_active]" value="1" checked>
              <label class="form-check-label" for="newActive${idx}">Activa</label>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label">Título ({{ strtoupper($locale) }})</label>
            <input type="text" class="form-control" name="new_sections[${idx}][title]" placeholder="Título de la sección">
          </div>

          <div class="col-12">
            <label class="form-label">Contenido ({{ strtoupper($locale) }})</label>
            <textarea class="form-control" rows="5" name="new_sections[${idx}][content]" placeholder="Contenido de la sección"></textarea>
          </div>
        </div>
      `;

      wrapper.querySelector('.btnRemoveNew').addEventListener('click', ()=>{
        wrapper.remove();
        if(container.querySelectorAll('.new-section-card').length===0 && emptyHint){
          emptyHint.style.display = '';
        }
      });

      container.appendChild(wrapper);
    }

    if(addBtn) addBtn.addEventListener('click', addNewSection);

    // Si no hay secciones existentes, añade 1 bloque por UX
    @if($item->sections->isEmpty())
      addNewSection();
    @endif
  })();
  </script>
  @endpush
@endif
