document.addEventListener('DOMContentLoaded', () => {
  const tourId = window.tourId;
  const container = document.getElementById(`review-carousel-tour-${tourId}`);
  const prev = document.querySelector(`.carousel-prev[data-tour="${tourId}"]`);
  const next = document.querySelector(`.carousel-next[data-tour="${tourId}"]`);

  if (!container || !window.productCode) return;

  fetch(`/api/reviews`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
    },
    body: JSON.stringify({
      productCode: window.productCode,
      count: 5,
      start: 1,
      provider: 'VIATOR',
      sortBy: 'MOST_RECENT'
    })
  })
  .then(res => {
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
  })
  .then(data => {
    if (!data.reviews || data.reviews.length === 0) {
      container.innerHTML = '<p class="text-muted text-center">No hay reseñas disponibles.</p>';
      return;
    }

    const reviews = data.reviews;
    let index = 0;

    const renderReview = (i) => {
      const r = reviews[i];
      const stars = '★'.repeat(Math.round(r.rating)) + '☆'.repeat(5 - Math.round(r.rating));
      const date = r.publishedDate ? new Date(r.publishedDate).toLocaleDateString() : '';
      const label = r.title ? `<div class="review-label">${r.title}</div>` : '';
      const truncated = r.text.length > 250;
      const content = truncated ? r.text.slice(0, 250) + '...' : r.text;

      container.innerHTML = `
        <div class="review-body-wrapper ${r.text.length < 120 ? 'centered' : ''}">
          <strong>${r.userName || 'Anónimo'}</strong><br>
          <small>${date}</small>
          <div class="review-stars">${stars} (${r.rating}/5)</div>
          ${label}
          <div class="review-content">${content}</div>
        </div>
      `;
    };

    renderReview(index);

    prev?.addEventListener('click', () => {
      index = (index - 1 + reviews.length) % reviews.length;
      renderReview(index);
    });

    next?.addEventListener('click', () => {
      index = (index + 1) % reviews.length;
      renderReview(index);
    });
  })
  .catch(err => {
    console.error('❌ Error al cargar reseñas:', err);
    container.innerHTML = '<p class="text-danger text-center">Error al cargar las reseñas.</p>';
  });
});
