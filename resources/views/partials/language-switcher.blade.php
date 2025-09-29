@php
  $locale = app()->getLocale();
  $map = [
    'es' => ['flag'=>'es.svg','label'=>'ES'],
    'en' => ['flag'=>'en.svg','label'=>'EN'],
    'fr' => ['flag'=>'fr.svg','label'=>'FR'],
    'de' => ['flag'=>'de.svg','label'=>'DE'],
    'pt' => ['flag'=>'pt.svg','label'=>'PT'],
  ];
  $current = $map[$locale] ?? $map['es'];
@endphp

<div class="dropdown language-switcher">
  <button class="language-switcher-toggle btn btn-outline-secondary dropdown-toggle" type="button"
          id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
    <img src="{{ asset('svg/flags/'.$current['flag']) }}" width="20" class="me-1" alt=""> {{ $current['label'] }}
  </button>
  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
    @foreach ($map as $code => $info)
      <li>
        <a class="dropdown-item language-switcher-item"
           href="{{ url()->current() === url('/') ? url('/'.$code) : \App\Support\LocaleUrl::swap(request(), $code) }}">
          <img src="{{ asset('svg/flags/'.$info['flag']) }}" width="20" class="me-1" alt=""> {{ $info['label'] }}
        </a>
      </li>
    @endforeach
  </ul>
</div>
