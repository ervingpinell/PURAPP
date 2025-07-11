{{-- resources/views/partials/language-switcher.blade.php --}}
<div class="dropdown language-switcher">
    <button class="language-switcher-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        @php
            $flag = match(app()->getLocale()) {
                'es' => 'es.png',
                'en' => 'gb.png',
                'fr' => 'fr.png',
                default => 'es.png'
            };
        @endphp
        <img src="{{ asset('images/' . $flag) }}" alt="Current language">
        {{ strtoupper(app()->getLocale()) }}
    </button>
    <ul class="dropdown-menu language-switcher-menu dropdown-menu-end" aria-labelledby="languageDropdown">
        <li>
            <a class="language-switcher-item" href="{{ route('switch.language', 'es') }}">
                <img src="{{ asset('images/es.png') }}"> Español
            </a>
        </li>
        <li>
            <a class="language-switcher-item" href="{{ route('switch.language', 'en') }}">
                <img src="{{ asset('images/gb.png') }}"> English
            </a>
        </li>
        <li>
            <a class="language-switcher-item" href="{{ route('switch.language', 'fr') }}">
                <img src="{{ asset('images/fr.png') }}"> Français
            </a>
        </li>
    </ul>
</div>
