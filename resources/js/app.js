/* =========================================================
   APP-CORE.JS — HEADER OFFSET + MENÚ MOBILE + SCROLL + UTILIDADES GLOBALES
   (con tinte dinámico iOS/Safari: theme-color)
   ========================================================= */
(function () {
  const $doc = document;

  // Exponer un namespace liviano para helpers compartidos
  const App = (window.App = window.App || {});

  /* -----------------------------
   * 0) iOS/Safari Theme-Color helpers
   * ----------------------------- */
  function ensureThemeMeta() {
    let meta = $doc.querySelector('#themeColorMeta[name="theme-color"]');
    if (!meta) {
      meta = $doc.createElement('meta');
      meta.setAttribute('name', 'theme-color');
      meta.id = 'themeColorMeta';
      meta.setAttribute('content', '#0f2419'); // color por defecto
      $doc.head.appendChild(meta);
    }
    return meta;
  }
  const themeMeta = ensureThemeMeta();
  const TOP_COLOR_DEFAULT = themeMeta.getAttribute('content') || '#0f2419';
  const FOOTER_COLOR = TOP_COLOR_DEFAULT;

  function setThemeColor(c) {
    if (themeMeta && themeMeta.getAttribute('content') !== c) {
      themeMeta.setAttribute('content', c);
    }
  }
  function nearFooter() {
    const footer = $doc.querySelector('.footer-nature');
    if (!footer) return false;
    const rect = footer.getBoundingClientRect();
    return rect.top < window.innerHeight * 1.2;
  }
  function refreshThemeColor(forceTop = false) {
    const menuOpen = document.body.classList.contains('menu-open');
    if (forceTop || menuOpen) { setThemeColor(TOP_COLOR_DEFAULT); return; }
    setThemeColor(nearFooter() ? FOOTER_COLOR : TOP_COLOR_DEFAULT);
  }

  /* -----------------------------
   * 1) HEADER FIJO: medir altura
   * ----------------------------- */
  const header =
    $doc.querySelector('.navbar-custom') ||
    $doc.querySelector('header.site-header') ||
    $doc.getElementById('site-header');

  function setNavH() {
    if (!header) return;
    const h = Math.ceil(header.getBoundingClientRect().height || 0);
    if (h > 0) {
      document.documentElement.style.setProperty('--nav-h', h + 'px');
      document.body.classList.add('has-fixed-navbar');
    }
  }
  let t;
  const debounce = (fn, ms) => { clearTimeout(t); t = setTimeout(fn, ms); };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setNavH, { once:true });
  } else { setNavH(); }
  window.addEventListener('resize', () => { debounce(setNavH, 120); refreshThemeColor(); }, { passive:true });
  window.addEventListener('orientationchange', () => setTimeout(() => { setNavH(); refreshThemeColor(true); }, 200), { passive:true });
  window.addEventListener('load', () => setTimeout(() => { setNavH(); refreshThemeColor(); }, 100));
  window.addEventListener('pageshow', () => setTimeout(() => { setNavH(); refreshThemeColor(); }, 60));
  window.addEventListener('scroll', () => refreshThemeColor(), { passive:true });
  if (document.fonts?.ready) document.fonts.ready.then(() => { setNavH(); refreshThemeColor(); });
  if (header) {
    header.querySelectorAll('img').forEach((img) => {
      if (!img.complete) img.addEventListener('load', () => { setNavH(); refreshThemeColor(); }, { once:true });
    });
  }

  /* -------------------------------------------------
   * 2) NAVBAR TOGGLE (mobile) + bloqueo del scroll
   * ------------------------------------------------- */
  function initMobileMenu(){
    const toggleBtn   = $doc.getElementById('navbar-toggle');
    const mobileLinks = $doc.getElementById('navbar-links');

    function closeMenu() {
      if (!mobileLinks) return;
      mobileLinks.classList.remove('show');
      document.body.classList.remove('menu-open');
      if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
      refreshThemeColor(true);
    }
    function openMenu() {
      if (!mobileLinks) return;
      mobileLinks.classList.add('show');
      document.body.classList.add('menu-open');
      if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
      refreshThemeColor(true);
    }

    if (toggleBtn && mobileLinks) {
      toggleBtn.addEventListener('click', (e) => {
        e.preventDefault(); e.stopPropagation();
        mobileLinks.classList.contains('show') ? closeMenu() : openMenu();
      });
      mobileLinks.addEventListener('click', (e) => e.stopPropagation());
      mobileLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', (e) => {
          const href = link.getAttribute('href') || '';
          const isHash = href.startsWith('#') && href.length > 1;
          e.preventDefault(); e.stopPropagation();
          setTimeout(() => {
            closeMenu();
            if (isHash) {
              const target = document.querySelector(href);
              if (target) {
                const v = getComputedStyle(document.documentElement).getPropertyValue('--nav-h');
                const offset = parseInt(v, 10) || 0;
                const rect = target.getBoundingClientRect();
                const absoluteY = window.pageYOffset + rect.top;
                window.scrollTo({ top: absoluteY - offset - 16, behavior: 'smooth' });
              }
            } else if (href && href !== '#') {
              window.location.href = href;
            }
          }, 50);
        });
      });
      document.addEventListener('click', closeMenu);
    }
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMobileMenu, { once:true });
  } else { initMobileMenu(); }

  /* --------------------------------------------
   * 3) SCROLL SUAVE respetando altura del header
   * -------------------------------------------- */
  function getNavOffset() {
    const v = getComputedStyle(document.documentElement).getPropertyValue('--nav-h');
    const n = parseInt(v, 10);
    return Number.isFinite(n) ? n : 0;
  }
  function smoothScrollTo(target) {
    if (!target) return;
    const rect = target.getBoundingClientRect();
    const absoluteY = window.pageYOffset + rect.top;
    const offset = getNavOffset();
    window.scrollTo({ top: absoluteY - offset - 16, behavior: 'smooth' });
  }
  // Enlaces a anclas (#id) fuera del menú móvil
  $doc.querySelectorAll('a[href^="#"]').forEach((a) => {
    a.addEventListener('click', (ev) => {
      if (a.closest('#navbar-links')) return;
      const hash = a.getAttribute('href');
      if (!hash || hash === '#') return;
      const el = $doc.querySelector(hash);
      if (el) { ev.preventDefault(); smoothScrollTo(el); }
    });
  });

  /* 4) Helpers globales expuestos */
  App.getCurrentLocale = function getCurrentLocale() {
    const metaLocale = document.querySelector('meta[name="locale"]');
    if (metaLocale) {
      const locale = metaLocale.getAttribute('content');
      if (locale) return locale;
    }
    const path = window.location.pathname;
    const pathParts = path.split('/').filter(Boolean);
    const supported = ['es', 'en', 'fr', 'de', 'pt'];
    if (pathParts.length > 0 && supported.includes(pathParts[0])) return pathParts[0];
    return 'es';
  };
  App.isHomePage = function isHomePage() {
    const path = window.location.pathname;
    const parts = path.split('/').filter(Boolean);
    const supported = ['es', 'en', 'fr', 'de', 'pt'];
    return parts.length === 0 || (parts.length === 1 && supported.includes(parts[0]));
  };

  /* 5) Carrito (global) */
  function updateCartCount() {
    fetch('/cart/count', { headers: { 'Accept': 'application/json' }})
      .then(res => res.ok ? res.json() : Promise.reject(`HTTP ${res.status}`))
      .then(data => {
        const n = Number(data.count || 0);
        if (typeof window.setCartCount === 'function') {
          window.setCartCount(n);
        } else {
          document.querySelectorAll('.cart-count-badge').forEach(el => {
            el.textContent = n;
            el.style.display = n > 0 ? 'inline-block' : 'none';
            el.classList.remove('flash'); void el.offsetWidth; el.classList.add('flash');
          });
        }
      })
      .catch(err => console.error('❌ Error al obtener la cantidad del carrito:', err));
  }
  updateCartCount();
  document.addEventListener('visibilitychange', () => { if (!document.hidden) { updateCartCount(); refreshThemeColor(); } });
  setInterval(() => { updateCartCount(); refreshThemeColor(); }, 30000);
  window.addEventListener('cart:changed', updateCartCount);

  /* 6) Hardening para carousels/iframes móviles */
  document.querySelectorAll('.carousel, .carousel-inner, .carousel-item')
    .forEach(el => el.style.transform = 'translateZ(0)');

  // Primera sincronización del theme-color
  refreshThemeColor(true);
})();
