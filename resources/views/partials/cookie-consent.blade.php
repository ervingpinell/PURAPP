{{-- resources/views/partials/cookie-consent.blade.php --}}
@php
// Si ya hay decisión (cookie presente), no mostrar el banner
$hasConsent = !is_null(request()->cookie('gv_cookie_consent'));

// URL a políticas
$policiesIndexUrl = function_exists('localized_route')
? localized_route('policies.index')
: url('/policies');

// Helper de traducciones con fallback
$t = function ($key, $fallback) {
$v = __($key);
return $v !== $key ? $v : $fallback;
};
@endphp

@if (! $hasConsent)
<style>
  /* ===== BANNER ===== */
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
    border: none;
    cursor: pointer;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  }

  .cookie-banner .btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
  }

  .cookie-banner .btn:hover::before {
    width: 300px;
    height: 300px;
  }

  .cookie-accept {
    background: linear-gradient(135deg, #ffd60a 0%, #ffc300 100%);
    color: #1a1a1a;
    font-weight: 700;
  }

  .cookie-accept:hover {
    background: linear-gradient(135deg, #ffc300 0%, #ffb700 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(255, 214, 10, 0.5);
  }

  .cookie-accept:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(255, 214, 10, 0.4);
  }

  .cookie-reject {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: #fff;
    font-weight: 600;
  }

  .cookie-reject:hover {
    background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(220, 53, 69, 0.5);
  }

  .cookie-reject:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
  }

  .cookie-customize {
    background: linear-gradient(135deg, #e8e8e8 0%, #d4d4d4 100%);
    color: #2c2c2c;
    font-weight: 600;
  }

  .cookie-customize:hover {
    background: linear-gradient(135deg, #d4d4d4 0%, #c0c0c0 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(200, 200, 200, 0.4);
  }

  .cookie-customize:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(200, 200, 200, 0.3);
  }

  .cookie-banner .btn:focus-visible {
    outline: 3px solid rgba(255, 255, 255, 0.5);
    outline-offset: 2px;
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

  /* ===== MODAL ===== */
  .cookie-modal {
    display: none;
    position: fixed;
    z-index: 2147483001;
    left: 0;
    top: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    align-items: center;
    justify-content: center;
    padding: 20px;
    overflow-y: auto;
  }

  .cookie-modal.active {
    display: flex;
  }

  .cookie-modal-content {
    background: white;
    border-radius: 12px;
    max-width: 600px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    color: #333;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
  }

  .cookie-modal-header {
    background: #0f5132;
    color: white;
    padding: 20px;
    border-radius: 12px 12px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .cookie-modal-header h3 {
    margin: 0;
    font-size: 1.25rem;
  }

  .cookie-modal-body {
    padding: 20px;
  }

  .cookie-category {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    transition: border-color .2s ease;
  }

  .cookie-category:hover {
    border-color: #0f5132;
  }

  .cookie-category-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
  }

  .cookie-category h4 {
    margin: 0 0 5px 0;
    font-size: 1rem;
    color: #0f5132;
    font-weight: 600;
  }

  .cookie-category p {
    margin: 0;
    font-size: 0.9rem;
    color: #666;
    line-height: 1.4;
  }

  .cookie-category small {
    display: block;
    margin-top: 8px;
    color: #999;
    font-size: 0.85rem;
  }

  /* ===== TOGGLE SWITCH ===== */
  .cookie-toggle {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
    flex-shrink: 0;
  }

  .cookie-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .cookie-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
  }

  .cookie-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
  }

  input:checked+.cookie-slider {
    background-color: #34c759;
  }

  input:checked+.cookie-slider:before {
    transform: translateX(26px);
  }

  input:disabled+.cookie-slider {
    opacity: 0.5;
    cursor: not-allowed;
  }

  .cookie-modal-footer {
    padding: 20px;
    border-top: 1px solid #e0e0e0;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    background: #f8f9fa;
    border-radius: 0 0 12px 12px;
  }

  .btn-close-modal {
    background: transparent;
    border: none;
    color: white;
    font-size: 28px;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    line-height: 1;
    transition: transform .2s ease;
  }

  .btn-close-modal:hover {
    transform: scale(1.1);
  }

  .cookie-modal-body a {
    color: #0f5132;
    text-decoration: underline;
  }

  .cookie-modal-body a:hover {
    color: #0a3d24;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .cookie-banner {
      padding: 12px;
      font-size: 0.85rem;
    }

    .cookie-banner .container {
      flex-direction: column;
      align-items: stretch;
      gap: 12px;
    }

    .cookie-banner .copy h4,
    .cookie-banner .copy strong {
      font-size: 1rem;
    }

    .cookie-banner .actions {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
    }

    /* Make the acceptance button full width on a new row if needed, 
       or just let them flow. 
       Let's try a grid where "Customize" and "Reject" share a row, 
       and "Accept" takes a full row or they all stack compactly. 
       
       Better approach for compactness: 
       Customize | Reject
       Accept All (Full Width)
    */

    .cookie-banner .btn {
      padding: 8px 12px;
      font-size: 0.85rem;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      white-space: normal;
      /* Allow text wrapping if needed */
      line-height: 1.2;
      min-height: 44px;
      /* Touch target size */
    }

    /* Specific layout: Customize and Reject side by side, Accept full width below */
    .cookie-customize {
      grid-column: 1 / 2;
    }

    .cookie-reject {
      grid-column: 2 / 3;
    }

    .cookie-accept {
      grid-column: 1 / -1;
    }

    /* Full width */

    .cookie-modal {
      padding: 10px;
      align-items: flex-end;
      /* Bottom sheet feel */
    }

    .cookie-modal-content {
      max-height: 85vh;
      border-radius: 16px 16px 0 0;
      margin-bottom: 0;
    }
  }
</style>

<!-- Banner -->
<div class="cookie-banner" id="cookie-banner" role="region" aria-label="{{ $t('cookies.banner_aria', 'Aviso de cookies') }}">
  <div class="container">
    <div class="copy">
      <strong>{{ $t('cookies.title', 'Usamos cookies') }}</strong>
      <div class="mt-1">
        {{ $t('cookies.message', 'Este sitio utiliza cookies para mejorar tu experiencia. Puedes aceptar todas, rechazar las no esenciales o personalizar tus preferencias.') }}
      </div>
    </div>

    <div class="actions">
      <button type="button" class="btn cookie-reject" data-cookie-action="reject">
        {{ $t('cookies.reject', 'Rechazar') }}
      </button>
      <button type="button" class="btn cookie-customize" data-cookie-action="customize">
        {{ $t('cookies.customize', 'Personalizar') }}
      </button>
      <button type="button" class="btn cookie-accept" data-cookie-action="accept">
        {{ $t('cookies.accept_all', 'Aceptar todas') }}
      </button>
    </div>
  </div>
</div>

<!-- Modal de personalización -->
<div class="cookie-modal" id="cookie-modal" role="dialog" aria-modal="true" aria-labelledby="cookie-modal-title">
  <div class="cookie-modal-content">
    <div class="cookie-modal-header">
      <h3 id="cookie-modal-title">{{ $t('cookies.customize_title', 'Personalizar cookies') }}</h3>
      <button type="button" class="btn-close-modal" id="close-modal" aria-label="{{ $t('cookies.close', 'Cerrar') }}">&times;</button>
    </div>

    <div class="cookie-modal-body">
      <!-- Esenciales -->
      <div class="cookie-category">
        <div class="cookie-category-header">
          <div>
            <h4>{{ $t('cookies.essential', 'Cookies esenciales') }}</h4>
            <p>{{ $t('cookies.essential_desc', 'Necesarias para el funcionamiento básico del sitio (login, carrito, seguridad)') }}</p>
          </div>
          <label class="cookie-toggle">
            <input type="checkbox" id="cookie-essential" checked disabled aria-label="{{ $t('cookies.essential', 'Cookies esenciales') }}">
            <span class="cookie-slider"></span>
          </label>
        </div>
        <small>{{ $t('cookies.always_active', 'Siempre activas') }}</small>
      </div>

      <!-- Funcionales -->
      <div class="cookie-category">
        <div class="cookie-category-header">
          <div>
            <h4>{{ $t('cookies.functional', 'Cookies funcionales') }}</h4>
            <p>{{ $t('cookies.functional_desc', 'Recuerdan tus preferencias como idioma, moneda o tema') }}</p>
          </div>
          <label class="cookie-toggle">
            <input type="checkbox" id="cookie-functional" aria-label="{{ $t('cookies.functional', 'Cookies funcionales') }}">
            <span class="cookie-slider"></span>
          </label>
        </div>
      </div>

      <!-- Analíticas -->
      <div class="cookie-category">
        <div class="cookie-category-header">
          <div>
            <h4>{{ $t('cookies.analytics', 'Cookies analíticas') }}</h4>
            <p>{{ $t('cookies.analytics_desc', 'Nos ayudan a entender cómo usas el sitio para mejorarlo (Google Analytics)') }}</p>
          </div>
          <label class="cookie-toggle">
            <input type="checkbox" id="cookie-analytics" aria-label="{{ $t('cookies.analytics', 'Cookies analíticas') }}">
            <span class="cookie-slider"></span>
          </label>
        </div>
      </div>

      <!-- Marketing -->
      <div class="cookie-category">
        <div class="cookie-category-header">
          <div>
            <h4>{{ $t('cookies.marketing', 'Cookies de marketing') }}</h4>
            <p>{{ $t('cookies.marketing_desc', 'Permiten mostrarte anuncios relevantes y medir campañas (Facebook Pixel)') }}</p>
          </div>
          <label class="cookie-toggle">
            <input type="checkbox" id="cookie-marketing" aria-label="{{ $t('cookies.marketing', 'Cookies de marketing') }}">
            <span class="cookie-slider"></span>
          </label>
        </div>
      </div>

      <p style="margin-top: 20px; font-size: 0.9rem; color: #666;">
        <a href="{{ $policiesIndexUrl }}">{{ $t('cookies.learn_more', 'Más información sobre cookies') }}</a>
      </p>
    </div>

    <div class="cookie-modal-footer">
      <button type="button" class="btn btn-success" id="save-preferences">
        {{ $t('cookies.save_preferences', 'Guardar preferencias') }}
      </button>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const banner = document.getElementById('cookie-banner');
    const modal = document.getElementById('cookie-modal');
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    async function savePreferences(url, preferences = null) {
      try {
        const response = await fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          },
          credentials: 'same-origin',
          body: preferences ? JSON.stringify(preferences) : null
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        return response;
      } catch (e) {
        console.error('Cookie consent request failed:', e);
        throw e;
      }
    }

    // Aceptar todas
    document.querySelector('[data-cookie-action="accept"]')?.addEventListener('click', async () => {
      banner.style.display = 'none';
      try {
        await savePreferences('{{ route("cookies.accept") }}');
        setTimeout(() => window.location.reload(), 120);
      } catch (e) {
        banner.style.display = 'block';
      }
    });

    // Rechazar todas (solo esenciales)
    document.querySelector('[data-cookie-action="reject"]')?.addEventListener('click', async () => {
      banner.style.display = 'none';
      try {
        await savePreferences('{{ route("cookies.reject") }}');
      } catch (e) {
        banner.style.display = 'block';
      }
    });

    // Abrir modal de personalización
    document.querySelector('[data-cookie-action="customize"]')?.addEventListener('click', () => {
      modal.classList.add('active');
    });

    // Cerrar modal
    document.getElementById('close-modal')?.addEventListener('click', () => {
      modal.classList.remove('active');
    });

    // Cerrar modal al hacer click fuera
    modal?.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.classList.remove('active');
      }
    });

    // Guardar preferencias personalizadas
    document.getElementById('save-preferences')?.addEventListener('click', async () => {
      const preferences = {
        essential: true,
        functional: document.getElementById('cookie-functional').checked,
        analytics: document.getElementById('cookie-analytics').checked,
        marketing: document.getElementById('cookie-marketing').checked,
      };

      banner.style.display = 'none';
      modal.classList.remove('active');

      try {
        await savePreferences('{{ route("cookies.customize") }}', preferences);

        // Recargar si se aceptaron analíticas o marketing
        if (preferences.analytics || preferences.marketing) {
          setTimeout(() => window.location.reload(), 120);
        }
      } catch (e) {
        banner.style.display = 'block';
        console.error('Failed to save cookie preferences');
      }
    });

    // Keyboard accessibility
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && modal.classList.contains('active')) {
        modal.classList.remove('active');
      }
    });
  });
</script>
@endif