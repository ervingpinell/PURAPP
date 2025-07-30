document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('viator-carousel');
    const inner = container?.querySelector('.carousel-inner');
    const productCode = window.VIATOR_CAROUSEL_PRODUCT_CODE || null;

    if (!container || !inner || !productCode) return;

    fetch('/api/reviews', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
            productCode,
            count: 5,
            start: 1,
            provider: 'VIATOR',
            sortBy: 'MOST_RECENT',
        })
    })
    .then(res => res.ok ? res.json() : res.json().then(err => { throw new Error(err?.error || `HTTP ${res.status}`); }))
    .then(data => {
        if (!data.reviews || data.reviews.length === 0) {
            container.outerHTML = '<p class="text-muted">No hay reseñas para mostrar.</p>';
            return;
        }

        inner.innerHTML = ''; // Evita duplicación

        data.reviews.forEach((r, index) => {
            const slide = document.createElement('div');
            slide.classList.add('carousel-item');
            if (index === 0) slide.classList.add('active');

            const avatar = r.avatarUrl || '/images/avatar-default.png';
            const date = r.publishedDate ? new Date(r.publishedDate).toLocaleDateString() : 'Fecha no disponible';

            const stars = renderStars(r.rating);

            const content = document.createElement('div');
            content.classList.add('review-item', 'card', 'shadow-sm', 'border-0', 'mx-auto', 'w-100');
            content.style.maxWidth = '600px';

            content.innerHTML = `
                <div class="card-body d-flex flex-column justify-content-between" style="min-height: 270px;">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <img src="${avatar}" alt="Avatar" class="rounded-circle me-3" width="50" height="50">
                            <div>
                                <h6 class="mb-0">${r.userName || 'Anónimo'}</h6>
                                <small class="text-muted">${date}</small>
                            </div>
                        </div>
                        ${stars}
                        <h5 class="card-title">${r.title}</h5>
                        <p class="card-text">${r.text}</p>
                    </div>
                    <div class="text-end mt-3">
                        <a href="https://www.viator.com/tours/La-Fortuna/Nature-Lover-Combo-Tour-Hanging-Bridges-La-Fortuna-Waterfall-and-Arenal-Volcano-Hike/d821-12732P5"
                           target="_blank"
                           class="text-muted small text-decoration-none">
                            Powered by Viator
                        </a>
                    </div>
                </div>
            `;

            slide.appendChild(content);
            inner.appendChild(slide);
        });
    })
    .catch(err => {
        console.error(`❌ Error en carrusel Viator (${productCode}):`, err);
        container.outerHTML = '<p class="text-danger">Error al cargar comentarios.</p>';
    });
});

function renderStars(rating) {
    const fullStars = Math.floor(rating);
    const halfStar = rating % 1 >= 0.5 ? 1 : 0;
    const emptyStars = 5 - fullStars - halfStar;

    let stars = '';
    for (let i = 0; i < fullStars; i++) stars += '★';
    if (halfStar) stars += '⯨'; // o '½' o '☆' si prefieres no mostrar media estrella
    for (let i = 0; i < emptyStars; i++) stars += '☆';

    return `<div class="mb-2 text-warning" style="font-size: 1.2rem;">${stars} <span class="text-muted" style="font-size: 0.85rem;">(${rating}/5)</span></div>`;
}
