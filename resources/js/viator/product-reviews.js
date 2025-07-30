import renderReviewItem from './render-reviews.js';

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('single-review-container');
    const productCode = window.VIATOR_PRODUCT_CODE || null;
    if (!container || !productCode) return;

    let start = 1;
    const count = 5;

    function loadReviews() {
        fetch('/api/reviews', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                productCode,
                count,
                start,
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
                    if (start === 1) container.innerHTML = '<p>No hay reseñas disponibles.</p>';
                    loadMoreBtn?.remove();
                    return;
                }

                data.reviews.forEach(r => {
                    const review = renderReviewItem(r);
                    container.appendChild(review);
                });

                start += count;
            })
            .catch(err => {
                console.error(`❌ Error al cargar reseñas del producto ${productCode}:`, err);
                container.innerHTML = '<p class="text-danger">Error al cargar reseñas.</p>';
            });
    }

    const loadMoreBtn = document.createElement('button');
    loadMoreBtn.classList.add('btn', 'btn-outline-success', 'mt-3');
    loadMoreBtn.textContent = 'Cargar más comentarios';
    loadMoreBtn.addEventListener('click', loadReviews);

    container.innerHTML = '';
    container.after(loadMoreBtn);
    loadReviews();
});
