document.addEventListener('DOMContentLoaded', () => {
  // ✅ Toggle hamburguesa
  const toggle = document.getElementById('navbar-toggle');
  const links = document.getElementById('navbar-links');
  if (toggle && links) {
    toggle.addEventListener('click', () => {
      links.classList.toggle('show');
    });
  }

  // ✅ Opcional: cerrar cuando se hace clic en un link
  document.querySelectorAll('.navbar-links a').forEach(link => {
    link.addEventListener('click', () => {
      links.classList.remove('show');
    });
  });

  // ✅ Tu lógica para overview se queda igual
  const toggleLinks = document.querySelectorAll('.toggle-overview-link');
  toggleLinks.forEach(link => {
    link.addEventListener('click', function () {
      const targetId = this.dataset.target;
      const overview = document.getElementById(targetId);

      if (overview.classList.contains('expanded')) {
        overview.style.maxHeight = '4.5em';
        overview.classList.remove('expanded');
        overview.classList.add('collapsed');
        this.textContent = 'Leer más';
      } else {
        overview.style.maxHeight = overview.scrollHeight + 'px';
        overview.classList.remove('collapsed');
        overview.classList.add('expanded');
        this.textContent = 'Leer menos';
      }
    });
  });
});
