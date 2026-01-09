/* =========================================================
   APP-CORE.JS — HEADER OFFSET + MENÚ MOBILE + SCROLL + UTILIDADES GLOBALES
   (con tinte dinámico iOS/Safari: theme-color)
   ========================================================= */
(function () {
  const $doc = document;

  // Namespace + flags
  const App = (window.App = window.App || {});
  App._toursBound = App._toursBound || false; // evita bind doble

  /* -----------------------------
   * 1) iOS/Safari Theme-Color helpers
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
   * 2) HEADER FIJO: medir altura
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
   * 3) NAVBAR TOGGLE (mobile) + bloqueo del scroll
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
   * 4) SCROLL SUAVE respetando altura del header (robusto)
   * -------------------------------------------- */
  function getScrollContainer(el) {
    let node = el && el.parentElement;
    while (node && node !== document.body && node !== document.documentElement) {
      const cs = getComputedStyle(node);
      const canScrollY = /(auto|scroll)/.test(cs.overflowY || cs.overflow);
      if (canScrollY && node.scrollHeight > node.clientHeight) return node;
      node = node.parentElement;
    }
    return window;
  }

  function getTopWithinContainer(target, container) {
    if (container === window) {
      const rect = target.getBoundingClientRect();
      return (window.pageYOffset || document.documentElement.scrollTop || 0) + rect.top;
    }
    const cRect = container.getBoundingClientRect();
    const tRect = target.getBoundingClientRect();
    const cScroll = container.scrollTop;
    return cScroll + (tRect.top - cRect.top);
  }

  function getNavOffset() {
    const cssVal = getComputedStyle(document.documentElement).getPropertyValue('--nav-h');
    let n = parseInt(cssVal, 10);
    if (!Number.isFinite(n) || n <= 0) {
      const hdr =
        document.querySelector('.navbar-custom') ||
        document.querySelector('header.site-header') ||
        document.getElementById('site-header');
      if (hdr) n = Math.ceil(hdr.getBoundingClientRect().height || 0);
    }
    return Number.isFinite(n) && n > 0 ? n : 0;
  }

  function smoothScrollTo(target) {
    if (!target) return;

    const container = getScrollContainer(target);
    const offset = getNavOffset();
    const rawTop = getTopWithinContainer(target, container);
    let goal = Math.max(0, rawTop - offset - 16);

    const before = container === window
      ? (window.pageYOffset || document.documentElement.scrollTop || 0)
      : container.scrollTop;

    if (container === window) {
      const doc = document.scrollingElement || document.documentElement;
      const maxScroll = (doc.scrollHeight || 0) - window.innerHeight;
      goal = Math.min(goal, maxScroll);
      window.scrollTo({ top: goal, behavior: 'smooth' });
    } else {
      const maxScroll = container.scrollHeight - container.clientHeight;
      goal = Math.min(goal, maxScroll);
      container.scrollTo({ top: goal, behavior: 'smooth' });
    }

    // Fallback si no se movió
    setTimeout(() => {
      const after = container === window
        ? (window.pageYOffset || document.documentElement.scrollTop || 0)
        : container.scrollTop;
      if (Math.abs(after - before) <= 1) {
        try { target.scrollIntoView({ behavior: 'smooth', block: 'start' }); } catch (e) {}
      }
    }, 80);
  }

  // Enlaces a anclas (#id) fuera del menú móvil (genérico)
  $doc.querySelectorAll('a[href^="#"]').forEach((a) => {
    a.addEventListener('click', (ev) => {
      if (a.closest('#navbar-links')) return; // el menú móvil ya maneja hash
      const hash = a.getAttribute('href');
      if (!hash || hash === '#') return;
      const el = $doc.querySelector(hash);
      if (el) { ev.preventDefault(); smoothScrollTo(el); }
    });
  });

  /* -----------------------------
   * 5) Helpers globales expuestos
   * ----------------------------- */
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
  App.getHomeUrl = function getHomeUrl() {
    const logoHome = document.querySelector('.navbar-logo a[href]');
    if (logoHome && logoHome.href) return logoHome.href;
    const loc = App.getCurrentLocale();
    const supported = ['es','en','fr','de','pt'];
    return supported.includes(loc)
      ? `${window.location.origin}/${loc}`
      : `${window.location.origin}/`;
  };

  /* -----------------------------
   * 6) Carrito (event-driven, sin polling)
   * ----------------------------- */
  (function(){
    const meta = document.querySelector('meta[name="cart-count-url"]');
    const COUNT_URL = meta?.content || '/cart/count';
    let inFlightCtrl = null;

    function applyCount(n){
      // Si existe un setter global, úsalo
      if (typeof window.setCartCount === 'function' && window.setCartCount !== applyCount) {
        try { window.setCartCount(n); return; } catch(_){}
      }
      // Fallback: pintar badges
      document.querySelectorAll('.cart-count-badge').forEach(el => {
        el.textContent = n;
        el.style.display = n > 0 ? 'inline-block' : 'none';
        el.classList.remove('flash'); void el.offsetWidth; el.classList.add('flash');
      });
    }

    async function fetchCartCount(){
      try {
        if (inFlightCtrl) inFlightCtrl.abort();
        inFlightCtrl = new AbortController();
        const res = await fetch(COUNT_URL, {
          headers: { 'Accept': 'application/json' },
          signal: inFlightCtrl.signal
        });
        const data = res.ok ? await res.json() : { count: 0 };
        applyCount(Number(data.count || 0));
      } catch(e) {
        if (!(e && e.name === 'AbortError')) {
          console.error('ERROR: Failed to fetch cart count:', e);
        }
      } finally {
        inFlightCtrl = null;
      }
    }

    // Helpers globales
    window.setCartCount = applyCount;        // Asignar directamente el número
    window.refreshCartCount = fetchCartCount; // Forzar fetch si hace falta

    // 1) Sync inicial (una sola vez)
    fetchCartCount();

    // 2) Reaccionar SOLO a eventos explícitos
    window.addEventListener('cart:changed', (e) => {
      const detail = e?.detail || {};
      if (typeof detail.count === 'number') {
        applyCount(detail.count);
      } else {
        fetchCartCount();
      }
    });
  })();

  /* -----------------------------
   * 7) Hardening para carousels/iframes móviles
   * ----------------------------- */
  document.querySelectorAll('.carousel, .carousel-inner, .carousel-item')
    .forEach(el => el.style.transform = 'translateZ(0)');

  /* =========================================================
   * 8) HOME — Animación de cards + Ir a #tours + Leer más/menos
   * ========================================================= */
  App.animateTourCards = function animateTourCards() {
    const cards = document.querySelectorAll('.tour-card');
    if (!cards.length) return;

    cards.forEach((card) => {
      card.style.opacity = '0';
      card.style.transform = 'scale(0.85) translateY(20px)';
      card.style.transition = 'all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)';
    });

    cards.forEach((card, idx) => {
      setTimeout(() => {
        card.style.opacity = '1';
        card.style.transform = 'scale(1) translateY(0)';
      }, idx * 120);
    });
  };

  function closeMobileMenuIfOpen() {
    const mobileLinks = document.getElementById('navbar-links');
    const toggleBtn   = document.getElementById('navbar-toggle');
    if (mobileLinks && mobileLinks.classList.contains('show')) {
      mobileLinks.classList.remove('show');
      document.body.classList.remove('menu-open');
      if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
      refreshThemeColor(true);
    }
  }

  App.initScrollToTours = function initScrollToTours() {
    if (App._toursBound) return; // evita doble bind
    App._toursBound = true;

    function targetToursEl() {
      return document.getElementById('tours') || document.querySelector('[data-anchor="tours"]');
    }

    // Reintenta N veces por si #tours aparece tarde (Livewire/Alpine/etc.)
    function scrollToToursAndAnimate({ retries = 4, delay = 120 } = {}) {
      let target = targetToursEl();
      if (!target && retries > 0) {
        setTimeout(() => scrollToToursAndAnimate({ retries: retries - 1, delay }), delay);
        return;
      }
      if (!target) return;

      smoothScrollTo(target);
      setTimeout(() => App.animateTourCards(), 350);
    }

    // Click en “Tours” (header + footer + cualquier otro con la clase)
    document.querySelectorAll('.scroll-to-tours').forEach(link => {
      link.addEventListener('click', function (e) {
        e.preventDefault(); e.stopPropagation();
        closeMobileMenuIfOpen();

        if (App.isHomePage && App.isHomePage()) {
          // En Home: SIEMPRE scrollea + anima (cada click)
          setTimeout(() => scrollToToursAndAnimate(), 50);
          return;
        }

        // Fuera de Home: redirige a Home con #tours (al llegar SOLO anima)
        const homeUrl = App.getHomeUrl();
        window.location.href = homeUrl.replace(/#.*$/, '') + '#tours';
      }, { passive:false });
    });

    // Llegaste a Home con #tours => SOLO animación (sin scroll)
    if ((App.isHomePage && App.isHomePage()) && /#tours/i.test(String(window.location.hash || ''))) {
      const kickoff = () => setTimeout(() => App.animateTourCards(), 150);
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', kickoff, { once:true });
      } else {
        kickoff();
      }
      // limpia el hash para que no se re-dispare al usar el historial
      try { history.replaceState({}, document.title, window.location.pathname + window.location.search); } catch(e){}
    }
  };

  App.initOverviewToggles = function initOverviewToggles() {
    $doc.querySelectorAll('.toggle-overview-link').forEach(link => {
      link.addEventListener('click', function () {
        const overview = document.getElementById(this.dataset.target);
        if (!overview) return;
        const textMore = this.dataset.textMore || 'Leer más';
        const textLess = this.dataset.textLess || 'Leer menos';
        overview.classList.toggle('expanded');
        this.textContent = overview.classList.contains('expanded') ? textLess : textMore;
      });
    });
  };

  App.initHome = function initHome() {
    if (!(App.isHomePage && App.isHomePage())) return;
    // No animamos automáticamente para no interferir con el comportamiento de "Tours".
    App.initOverviewToggles();
  };

  /* -------- Lanzadores -------- */
  // Bind Tours (siempre, desde cualquier página)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', App.initScrollToTours, { once:true });
  } else { App.initScrollToTours(); }

  // Init de Home (sin animar por defecto)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', App.initHome, { once:true });
  } else { App.initHome(); }

  // Primera sincronización del theme-color
  refreshThemeColor(true);
})();
