@php
  /** @var \App\Models\Policy $item */
  $targetLocale = $locale ?? 'en';
  $sections     = $item->sections ?? collect();
@endphp

@if($sections->count())
  <div class="card mb-3">
    <div class="card-header bg-info text-white">
      <h5 class="mb-0">
        <i class="fas fa-list-alt mr-2"></i> {{ __('m_config.translations.policy_sections') }}
        <span class="badge bg-dark ml-2">{{ strtoupper($targetLocale) }}</span>
      </h5>
    </div>

    <div class="card-body">
      @foreach($sections as $sIndex => $section)
        @php
          $secTr      = method_exists($section, 'translate') ? $section->translate($targetLocale) : null;
          $sectionId  = $section->section_id ?? $section->getKey();
          $nameInput  = "section_translations.{$sectionId}.name";
          $contInput  = "section_translations.{$sectionId}.content";
          $nameId     = "section_name_{$sectionId}";
          $contentId  = "section_content_{$sectionId}";
        @endphp

        <div class="card mb-2">
          <div class="card-header bg-light">
            <strong>{{ __('m_config.translations.section') }} {{ $sIndex + 1 }}</strong>
          </div>
          <div class="card-body">
            <div class="form-group mb-3">
              <label for="{{ $nameId }}">
                <i class="far fa-edit mr-1"></i>
                {{ __('m_config.translations.section_name') }} ({{ strtoupper($targetLocale) }})
              </label>
              <input
                type="text"
                id="{{ $nameId }}"
                class="form-control"
                name="section_translations[{{ $sectionId }}][name]"
                value="{{ old($nameInput, $secTr->name ?? $section->name ?? '') }}"
              >
            </div>

            <div class="form-group mb-0">
              <label for="{{ $contentId }}">
                <i class="far fa-edit mr-1"></i>
                {{ __('m_config.translations.section_content') }} ({{ strtoupper($targetLocale) }})
              </label>
              <textarea
                id="{{ $contentId }}"
                name="section_translations[{{ $sectionId }}][content]"
                class="form-control"
                rows="4"
              >{{ old($contInput, $secTr->content ?? $section->content ?? '') }}</textarea>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
@endif
