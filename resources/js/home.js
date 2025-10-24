/* =========================================================
   HOME.JS — Lógica específica de la Home (versión simplificada)
   - Usa App.animateTourCards y App.initScrollToTours (definidas en app.js)
   - Leer más/menos
   ========================================================= */
(function () {
  const $doc = document;
  const App = window.App || {};

  // Seguridad: solo ejecuta si es home
  function onHome(fn){ (App.isHomePage && App.isHomePage()) && fn(); }

  /* Leer más / Leer menos (home) */
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

  /* Init solo en Home */
  onHome(() => {
    // Animación inicial de cards:
    if (typeof App.animateTourCards === 'function') App.animateTourCards();

    // Scroll a #tours:
    if (typeof App.initScrollToTours === 'function') App.initScrollToTours();

    // Toggle leer más/menos:
    initOverviewToggles();
  });
})();
