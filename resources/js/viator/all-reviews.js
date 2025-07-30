import renderReviewItem from './render-reviews.js';

document.addEventListener('DOMContentLoaded', () => {
    const productCodes = window.VIATOR_PRODUCT_CODES || [];
    const container = document.getElementById('all-reviews-container');
    if (!container) return;

    if (productCodes.length === 0) {
        container.innerHTML = '<p>No hay productos para mostrar.</p>';
        return;
    }

    container.innerHTML = '';

    productCodes.forEach(code => {
        const section = document.createElement('section');
        section.classList.add('mb-5');
        section.innerHTML = `
            <h3 class="h5 mb-3">Producto: ${code}</h3>
            <div class="review-list"></div>
            <div class="text-center my-2">
                <button class="btn btn-sm btn-outline-success load-more-btn" data-code="${code}" data-start="1">Cargar más</button>
            </div>
            <div class="viator-credit text-center mt-2">
                <small>
                    Reseñas proporcionadas por
                    <a href="https://www.viator.com/searchResults/all?search=${code}" target="_blank" rel="nofollow noopener">
                        Viator
                    </a>
                </small>
            </div>
        `;
        container.appendChild(section);

        loadReviews(code, section.querySelector('.review-list'), section.querySelector('.load-more-btn'));
    });

    function generateStars(rating = 0) {
        const filled = Math.round(rating);
        return '★'.repeat(filled) + '☆'.repeat(5 - filled);
    }

    function loadReviews(productCode, listContainer, button) {
        const start = parseInt(button.dataset.start, 10);

        button.disabled = true;
        button.textContent = 'Cargando...';

        fetch('/api/reviews', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                productCode,
                count: 3,
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
                    button.remove();
                    if (start === 1) {
                        listContainer.innerHTML = '<p>No hay reseñas para este producto.</p>';
                    }
                    return;
                }

                data.reviews.forEach(r => {
                    const avatar = r.avatarUrl || '/images/avatar-default.png';
                    const date = r.publishedDate ? new Date(r.publishedDate).toLocaleDateString() : 'Fecha no disponible';
                    const stars = generateStars(r.rating);

                    const item = document.createElement('div');
                    item.classList.add('review-item', 'card', 'shadow-sm', 'border-0', 'mb-3');
                    item.style.maxWidth = '600px';
                    item.style.margin = '0 auto';

                    item.innerHTML = `
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="${avatar}" alt="Avatar" class="rounded-circle me-3" width="50" height="50">
                                <div>
                                    <h6 class="mb-0">${r.userName || 'Anónimo'}</h6>
                                    <small class="text-muted">${date}</small>
                                </div>
                            </div>
                            <div class="review-stars mb-2 text-warning">${stars}</div>
                            <h5 class="card-title">${r.title}</h5>
                            <p class="card-text">${r.text}</p>
                        </div>
                    `;

                    listContainer.appendChild(item);
                });

                button.dataset.start = start + 3;
                button.disabled = false;
                button.textContent = 'Cargar más';
            })
            .catch(err => {
                console.error(`❌ Error al cargar reseñas del producto ${productCode}:`, err);
                button.textContent = 'Error';
                button.classList.remove('btn-outline-success');
                button.classList.add('btn-danger');
            });
    }

    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('load-more-btn')) {
            const code = e.target.dataset.code;
            const section = e.target.closest('section');
            const list = section.querySelector('.review-list');
            loadReviews(code, list, e.target);
        }
    });
});
