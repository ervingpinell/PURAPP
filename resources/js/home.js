/* =========================================================
   HOME.JS — Lógica específica de la Home
   - Animación de .tour-card
   - Botón "Ir a tours" y hash #tours
   - Igualar altura de títulos (cards y modales) + centrado vertical
   - Leer más/menos
   ========================================================= */
(function () {
  const $doc = document;
  const App = window.App || {};

  // Seguridad: solo ejecuta si es home
  function onHome(fn){ (App.isHomePage && App.isHomePage()) && fn(); }

  /* Helpers */
  function wrapTitleIfNeeded(el){
    if (!el) return;
    // Evitar doble wrap
    if (!el.querySelector('.title-inner')) {
      const span = document.createElement('span');
      span.className = 'title-inner';
      // mueve los nodos de texto/hijos dentro del span
      while (el.firstChild) span.appendChild(el.firstChild);
      el.appendChild(span);
    }
  }

  function equalizeInScope(scopeSelector, titleSelector){
    const scope = document.querySelector(scopeSelector);
    if (!scope) return;
    const titles = Array.from(scope.querySelectorAll(titleSelector));
    if (!titles.length) return;

    // preparar: quitar alturas previas, asegurar envoltorio y clase
    titles.forEach(t => {
      t.classList.remove('equalized');
      t.style.height = '';
      wrapTitleIfNeeded(t);
    });

    // Calcula altura máxima visible
    const maxH = Math.max(...titles.map(t => t.offsetHeight));
    if (!Number.isFinite(maxH) || maxH <= 0) return;

    // Aplica altura fija y centra verticalmente
    titles.forEach(t => {
      t.style.height = maxH + 'px';
      t.classList.add('equalized');
    });
  }

  // Igualar títulos de:
  // - Cards en home:         .tours-section .tour-card .card-title
  // - Cards dentro del modal: .tour-modal-card .card-title (por modal abierto)
  function equalizeCardTitles() {
    // Home (lista principal) — siempre
    equalizeInScope('.tours-section', '.tour-card .card-title');

    // Modales visibles — por cada modal abierto
    document.querySelectorAll('.modal.show').forEach(modal => {
      equalizeInScope('#' + modal.id, '.tour-modal-card .card-title');
    });
  }

  /* 1) Animación de cards */
  function animateTourCards() {
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
    // tras animación, igualar títulos
    setTimeout(equalizeCardTitles, 650);
  }

  /* 2) Scroll a #tours */
  function initScrollToTours() {
    function getNavOffset() {
      const v = getComputedStyle(document.documentElement).getPropertyValue('--nav-h');
      const n = parseInt(v, 10);
      return Number.isFinite(n) ? n : 0;
    }
    function smoothScrollTo(target) {
      const rect = target.getBoundingClientRect();
      const absoluteY = window.pageYOffset + rect.top;
      const offset = getNavOffset();
      window.scrollTo({ top: absoluteY - offset - 16, behavior: 'smooth' });
    }

    $doc.querySelectorAll('.scroll-to-tours').forEach(link => {
      link.addEventListener('click', function (e) {
        e.preventDefault(); e.stopPropagation();
        setTimeout(() => {
          const target = document.getElementById('tours') || document.querySelector('[data-anchor="tours"]');
          if (target) { smoothScrollTo(target); setTimeout(animateTourCards, 400); }
        }, 50);
      });
    });

    if (window.location.hash === '#tours') {
      setTimeout(() => {
        const target = document.getElementById('tours') || document.querySelector('[data-anchor="tours"]');
        if (target) { smoothScrollTo(target); setTimeout(animateTourCards, 400); }
      }, 200);
    }
  }

  /* 3) Leer más / Leer menos (home) */
  function initOverviewToggles() {
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
  }

  /* 4) Eventos */
  function bind() {
    const resizeEqualize = () => setTimeout(equalizeCardTitles, 50);

    // Inicial + re-equalizaciones
    equalizeCardTitles();
    window.addEventListener('resize', resizeEqualize, { passive:true });
    window.addEventListener('orientationchange', () => setTimeout(equalizeCardTitles, 200), { passive:true });
    window.addEventListener('pageshow', () => setTimeout(equalizeCardTitles, 60));
    if (document.fonts?.ready) document.fonts.ready.then(() => setTimeout(equalizeCardTitles, 60));

    // Recalcular cuando se abre un modal (Bootstrap)
    $doc.addEventListener('shown.bs.modal', equalizeCardTitles);
    $doc.addEventListener('hidden.bs.modal', equalizeCardTitles);

    // Imágenes de cards pueden cambiar alturas
    $doc.querySelectorAll('.tour-card img, .tour-modal-card img').forEach(img => {
      if (!img.complete) img.addEventListener('load', () => setTimeout(equalizeCardTitles, 30), { once:true });
    });
  }

  /* Init solo en Home */
  onHome(() => {
    initScrollToTours();
    initOverviewToggles();
    bind();
  });
})();
