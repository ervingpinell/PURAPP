/* =========================================================
   HOME.JS — Lógica específica de la Home (versión simplificada)
   - Usa App.animateTourCards y App.initScrollToTours (definidas en app.js)
   - Leer más/menos
   - Iframe resize listener para reviews
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

  /* Listen for iframe resize messages from review embeds */
  function initIframeResizeListener() {
    window.addEventListener('message', (event) => {
      const data = event.data;
      
      // Handle review iframe resize
      if (data && data.type === 'REVIEW_IFRAME_RESIZE' && data.uid && data.height) {
        const iframe = document.querySelector(`iframe.review-embed[data-uid="${data.uid}"]`);
        if (iframe) {
          const newHeight = Math.max(200, Math.min(1800, parseInt(data.height, 10)));
          iframe.style.height = newHeight + 'px';
          
          // Also update the shell container if it exists
          const shell = iframe.closest('.iframe-shell');
          if (shell) {
            shell.style.setProperty('--embed-h', newHeight + 'px');
          }
        }
      }
      
      // Handle ready message
      if (data && data.type === 'REVIEW_IFRAME_READY' && data.uid) {
        const iframe = document.querySelector(`iframe.review-embed[data-uid="${data.uid}"]`);
        if (iframe) {
          // Hide skeleton loader
          const shell = iframe.closest('.iframe-shell');
          if (shell) {
            const skeleton = shell.querySelector('.iframe-skeleton');
            if (skeleton) {
              skeleton.classList.add('is-hidden');
            }
          }
        }
      }
    }, false);
  }

  /* Init solo en Home */
  onHome(() => {
    // Animación inicial de cards:
    if (typeof App.animateTourCards === 'function') App.animateTourCards();

    // Scroll a #tours:
    if (typeof App.initScrollToTours === 'function') App.initScrollToTours();

    // Toggle leer más/menos:
    initOverviewToggles();
    
    // Iframe resize listener:
    initIframeResizeListener();
  });
})();
