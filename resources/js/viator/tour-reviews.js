document.addEventListener('DOMContentLoaded', () => {
  const tourId = window.tourId;
  const container = document.getElementById(`review-carousel-tour-${tourId}`);
  const prev = document.querySelector(`.carousel-prev[data-tour="${tourId}"]`);
  const next = document.querySelector(`.carousel-next[data-tour="${tourId}"]`);
  const wrapper = container?.closest('.tour-review-carousel');

  if (!container || !window.productCode) {
  container.innerHTML = '<p class="text-muted text-center">No se encontraron reseñas relacionadas a este tour.</p>';
  return;
}

  fetch(`/api/reviews`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
    },
    body: JSON.stringify({
      productCode: window.productCode,
      count: 20,
      start: 1,
      provider: 'VIATOR',
      sortBy: 'MOST_RECENT'
    })
  })
  .then(res => res.ok ? res.json() : Promise.reject(res))
  .then(data => {
    if (!data.reviews || data.reviews.length === 0) {
      container.innerHTML = '<p class="text-muted text-center">No hay reseñas disponibles.</p>';
      return;
    }

    const reviews = data.reviews;
    let index = 0;
    let expanded = false;

    const renderReview = (i) => {
      const r = reviews[i];
      const stars = '★'.repeat(Math.round(r.rating)) + '☆'.repeat(5 - Math.round(r.rating));
      const date = r.publishedDate ? new Date(r.publishedDate).toLocaleDateString() : '';
      const label = r.title ? `<div class="review-label">${r.title}</div>` : '';

      const fullText = r.text.trim();
      const truncated = fullText.length > 250;
      const shortText = truncated ? fullText.slice(0, 250).trim() + '...' : fullText;

      container.innerHTML = `
        <div class="review-body-wrapper">
          <div class="review-header">
            <strong>${r.userName || 'Anónimo'}</strong>
            <small>${date}</small>
            <div class="review-stars">${stars} <span class="rating-number">(${r.rating}/5)</span></div>
            ${label}
          </div>
          <div class="review-content ${expanded ? 'expanded' : ''}" id="review-text-${i}">${expanded ? fullText : shortText}</div>
          <div class="review-footer">
            ${truncated ? `<button class="toggle-review">${expanded ? 'Ver menos' : 'Ver más'}</button>` : ''}
          </div>
        </div>
      `;

      if (wrapper) {
        wrapper.classList.toggle('expanded', expanded);
      }

      const toggleBtn = container.querySelector('.toggle-review');
      const reviewText = container.querySelector(`#review-text-${i}`);

      if (toggleBtn && reviewText) {
        toggleBtn.addEventListener('click', () => {
          expanded = !expanded;
          reviewText.classList.toggle('expanded', expanded);
          reviewText.textContent = expanded ? fullText : shortText;
          toggleBtn.textContent = expanded ? 'Ver menos' : 'Ver más';

          if (wrapper) {
            wrapper.classList.toggle('expanded', expanded);
          }
        });
      }
    };

    renderReview(index);

    prev?.addEventListener('click', () => {
      index = (index - 1 + reviews.length) % reviews.length;
      expanded = false;
      renderReview(index);
    });

    next?.addEventListener('click', () => {
      index = (index + 1) % reviews.length;
      expanded = false;
      renderReview(index);
    });
  })
  .catch(err => {
    console.error('❌ Error al cargar reseñas:', err);
    container.innerHTML = '<p class="text-danger text-center">Error al cargar las reseñas.</p>';
  });
});
