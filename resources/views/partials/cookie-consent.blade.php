{{-- resources/views/partials/cookie-consent.blade.php --}}
@php
// Si ya hay decisión (cookie presente), no mostrar el banner (doble guard por si se incluye directo)
$hasConsent = !is_null(request()->cookie('gv_cookie_consent'));

// URL a "todas las políticas"
$policiesIndexUrl = function_exists('localized_route')
? localized_route('policies.index')
: url('/policies');

// Helper de traducciones con fallback suave
$t = function ($key, $fallback) {
$v = __($key);
return $v !== $key ? $v : $fallback;
};
@endphp

@if (! $hasConsent)
<style>
  .cookie-banner {
    position: fixed;
    z-index: 2147483000;
    left: 0;
    right: 0;
    bottom: 0;
    background: #0f5132;
    color: #fff;
    padding: 14px 16px;
    box-shadow: 0 -6px 24px rgba(0, 0, 0, .18);
    font-size: 0.95rem;
  }

  .cookie-banner a {
    color: #fff;
    text-decoration: underline;
  }

  .cookie-banner .container {
    max-width: 1100px;
    margin: 0 auto;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    justify-content: space-between;
  }

  .cookie-banner .copy {
    flex: 1 1 420px;
  }

  .cookie-banner .actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .cookie-banner .btn {
    border: 0;
    cursor: pointer;
    padding: 10px 14px;
    border-radius: 8px;
    font-weight: 600;
  }

  .cookie-accept {
    background: #34c759;
    color: #0b2e13;
  }

  .cookie-reject {
    background: #6c757d;
    color: #fff;
  }

  .cookie-more {
    background: transparent;
    color: #fff;
    text-decoration: underline;
    padding: 0 4px;
  }

  @media (prefers-reduced-motion: no-preference) {
    .cookie-banner {
      animation: slideUp .25s ease-out;
    }

    @keyframes slideUp {
      from {
        transform: translateY(100%);
      }

      to {
        transform: translateY(0);
      }
    }
  }

  /* --- Hover/Focus: resaltar con borde, sin cambiar colores --- */
  .cookie-banner .btn {
    border: 2px solid transparent;
    background-clip: padding-box;
    transition: border-color .15s ease, box-shadow .15s ease;
  }

  .cookie-banner .btn:hover,
  .cookie-banner .btn:focus-visible {
    border-color: #ffffff;
    box-shadow: 0 0 0 2px rgba(255, 255, 255, .35) inset;
  }

  .cookie-accept:hover {
    background: #34c759;
    color: #0b2e13;
  }

  .cookie-reject:hover {
    background: #6c757d;
    color: #ffffff;
  }

  .cookie-more:hover {
    background: transparent;
    color: #ffffff;
    text-decoration: underline;
  }

  /* Focus accesible */
  .cookie-banner .btn:focus-visible,
  .cookie-banner a:focus-visible {
    outline: 2px solid #ffffff;
    outline-offset: 2px;
  }

  .cookie-banner .btn:hover {
    opacity: 1;
  }
</style>

<div class="cookie-banner" id="cookie-banner" role="region" aria-label="{{ $t('cookies.banner_aria', 'Aviso de cookies') }}">
  <div class="container">
    <div class="copy">
      <strong>{{ $t('cookies.title', 'Usamos cookies') }}</strong>
      <div class="mt-1">
        {{ $t('cookies.message', 'Este sitio utiliza cookies propias y de terceros para mejorar tu experiencia, analizar el tráfico y fines de medición. Puedes aceptar o rechazar el uso no esencial. Consulta más información en nuestras políticas.') }}
        <a href="{{ $policiesIndexUrl }}" class="cookie-more-link">
          {{ $t('cookies.learn_more', 'Más información') }}
        </a>
      </div>
    </div>

    <div class="actions">
      <button type="button" class="btn cookie-reject" data-cookie-action="reject">
        {{ $t('cookies.reject', 'Rechazar') }}
      </button>
      <button type="button" class="btn cookie-accept" data-cookie-action="accept">
        {{ $t('cookies.accept', 'Aceptar') }}
      </button>
    </div>
  </div>
</div>

<noscript>
  <div class="cookie-banner" style="position: static;">
    <div class="container">
      <div class="copy">
        <strong>{{ $t('cookies.title', 'Usamos cookies') }}</strong>
        <div class="mt-1">
          {{ $t('cookies.noscript', 'Para gestionar tu consentimiento habilita JavaScript. Puedes revisar nuestras políticas aquí:') }}
          <a href="{{ $policiesIndexUrl }}">{{ $t('cookies.learn_more', 'Read more') }}</a>
        </div>
      </div>
    </div>
  </div>
</noscript>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const banner = document.getElementById('cookie-banner');
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    async function postConsent(url) {
      try {
        const response = await fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
          },
          credentials: 'same-origin'
        });

        // ✅ Validar respuesta
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        return response;
      } catch (e) {
        console.error('Cookie consent request failed:', e);
        throw e;
      }
    }

    document.querySelectorAll('[data-cookie-action]').forEach(btn => {
      btn.addEventListener('click', async () => {
        const action = btn.dataset.cookieAction;
        const url = action === 'accept' ?
          @json(route('cookies.accept')) :
          @json(route('cookies.reject'));

        // Esconde al instante para buena UX
        if (banner) banner.style.display = 'none';

        try {
          // Sincroniza con el servidor (setea gv_cookie_consent)
          await postConsent(url);

          // Recarga SOLO si se aceptó (para inyectar GA/Pixel en el layout)
          if (action === 'accept') {
            setTimeout(() => window.location.reload(), 120);
          }
        } catch (e) {
          // ✅ Restaurar banner si falla
          if (banner) banner.style.display = 'block';
          console.error('Failed to save cookie preference');
        }
      });
    });
  });
</script>
@endif