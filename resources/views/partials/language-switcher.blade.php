{{-- resources/views/partials/language-switcher.blade.php --}}
<div class="dropdown language-switcher">
    <button class="language-switcher-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        @php
            $flag = match(app()->getLocale()) {
                'es' => 'es.svg',
                'en' => 'en.svg',
                'fr' => 'fr.svg',
                'pt' => 'pt.svg',
                'de' => 'de.svg',
                default => 'es.svg'
            };
        @endphp
        <img src="{{ asset('svg/flags/' . $flag) }}" alt="Current language" width="20">
        {{ strtoupper(app()->getLocale()) }}
    </button>
    <ul class="dropdown-menu language-switcher-menu dropdown-menu-end" aria-labelledby="languageDropdown">
        <li><a class="language-switcher-item" href="{{ route('switch.language', 'es') }}">
            <img src="{{ asset('svg/flags/es.svg') }}" width="20"> Español</a>
        </li>
        <li><a class="language-switcher-item" href="{{ route('switch.language', 'en') }}">
            <img src="{{ asset('svg/flags/en.svg') }}" width="20"> English</a>
        </li>
        <li><a class="language-switcher-item" href="{{ route('switch.language', 'fr') }}">
            <img src="{{ asset('svg/flags/fr.svg') }}" width="20"> Français</a>
        </li>
        <li><a class="language-switcher-item" href="{{ route('switch.language', 'pt') }}">
            <img src="{{ asset('svg/flags/pt.svg') }}" width="20"> Português</a>
        </li>
        <li><a class="language-switcher-item" href="{{ route('switch.language', 'de') }}">
            <img src="{{ asset('svg/flags/de.svg') }}" width="20"> Deutsch</a>
        </li>
    </ul>
</div>
