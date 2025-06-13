{{-- resources/views/partials/language-selector.blade.php --}}
<div class="dropdown">
    <button class="btn btn-sm btn-outline-light dropdown-toggle d-flex align-items-center gap-2" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        @php
            $flag = match(app()->getLocale()) {
                'es' => 'es.png',
                'en' => 'gb.png',
                'fr' => 'fr.png',
                default => 'es.png'
            };
        @endphp
        <img src="{{ asset('images/' . $flag) }}" alt="Idioma actual" width="20" height="15">
        {{ strtoupper(app()->getLocale()) }}
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
        <li>
            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('cambiar.idioma', 'es') }}">
                <img src="{{ asset('images/es.png') }}" width="20" height="15"> Español
            </a>
        </li>
        <li>
            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('cambiar.idioma', 'en') }}">
                <img src="{{ asset('images/gb.png') }}" width="20" height="15"> English
            </a>
        </li>
        <li>
            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('cambiar.idioma', 'fr') }}">
                <img src="{{ asset('images/fr.png') }}" width="20" height="15"> Français
            </a>
        </li>
    </ul>
</div>
