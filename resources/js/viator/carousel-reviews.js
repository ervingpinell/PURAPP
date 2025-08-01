document.addEventListener('DOMContentLoaded', async () => {
    const container = document.getElementById('viator-carousel');
    const inner = container?.querySelector('.carousel-inner');
    const products = window.VIATOR_CAROUSEL_PRODUCTS || [];

    if (!container || !inner || products.length === 0) return;

    let slides = [];

    for (const { code, name, id } of products) {
        try {
            const res = await fetch('/api/reviews', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    productCode: code,
                    count: 5,
                    start: 1,
                    provider: 'VIATOR',
                    sortBy: 'MOST_RECENT',
                })
            });

            if (!res.ok) throw new Error('Error HTTP: ' + res.status);
            const data = await res.json();

            const reviews = (data.reviews || []).sort(() => 0.5 - Math.random()).slice(0, 2);

            reviews.forEach((r, index) => {
                const avatar = r.avatarUrl || '/images/avatar-default.png';
                const date = r.publishedDate ? new Date(r.publishedDate).toLocaleDateString() : 'Fecha no disponible';
                const stars = renderStars(r.rating);
                const userName = r.userName || 'Anónimo';
                const reviewTitle = r.title || '';
                const text = r.text || '';
                const collapsedText = text.split(' ').slice(0, 45).join(' ');
                const needsCollapse = text.split(' ').length > 45;

                const slide = document.createElement('div');
                slide.classList.add('carousel-item');
                if (slides.length === 0) slide.classList.add('active');

                slide.innerHTML = `
                    <div class="review-item card shadow-sm border-0 mx-auto w-100" style="max-width: 600px;">
                        <div class="card-body d-flex flex-column justify-content-between position-relative" style="min-height: 400px;">
                            <span class="tour-name fw-semibold">
                                <a href="#" class="tour-link text-success fw-semibold d-inline-block"
                                   data-id="${id}" data-name="${name}"
                                   style="text-decoration: underline;">
                                    ${name}
                                </a>
                            </span>

                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <img src="${avatar}" alt="Foto de ${userName}" class="rounded-circle me-3" width="50" height="50">
                                    <div>
                                        <h6 class="mb-0">${userName}</h6>
                                        <small class="text-muted">${date}</small>
                                    </div>
                                </div>
                                ${stars}
                                ${reviewTitle ? `<h5 class="card-title">${reviewTitle}</h5>` : ''}
                                <p class="card-text" id="review-text-${slides.length}">${needsCollapse ? collapsedText + '…' : text}</p>
                                ${needsCollapse
                                    ? `<a href="#" class="text-decoration-none small toggle-review" data-index="${slides.length}" data-full="${encodeURIComponent(text)}" data-short="${encodeURIComponent(collapsedText)}">Ver más</a>`
                                    : ''
                                }
                            </div>
                            <div class="text-end mt-3">
                                <a href="https://www.viator.com/searchResults/all?q=${encodeURIComponent(name)}"
                                   target="_blank"
                                   class="text-muted small text-decoration-none viator-credit"
                                   title="Ver ${name} en Viator">
                                    Powered by Viator
                                </a>
                            </div>
                        </div>
                    </div>
                `;
                slides.push(slide);
            });

        } catch (err) {
            console.error(`❌ Error cargando reviews de ${code}:`, err);
        }
    }

    if (slides.length === 0) {
        container.outerHTML = '<p class="text-muted">No hay reseñas para mostrar.</p>';
        return;
    }

    inner.innerHTML = '';
    slides.forEach(slide => inner.appendChild(slide));

    // 🔁 Toggle "Ver más / Ver menos"
    document.querySelectorAll('.toggle-review').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const index = this.dataset.index;
            const textEl = document.getElementById(`review-text-${index}`);
            const shortText = decodeURIComponent(this.dataset.short) + '…';
            const fullText = decodeURIComponent(this.dataset.full);
            const isCollapsed = this.textContent.includes('más');

            textEl.textContent = isCollapsed ? fullText : shortText;
            this.textContent = isCollapsed ? 'Ver menos' : 'Ver más';

            const cardBody = textEl.closest('.card-body');
            cardBody.style.maxHeight = isCollapsed ? 'none' : '400px';
        });
    });

    // ✅ Confirmación con modal
    document.querySelectorAll('.tour-link').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const tourId = this.dataset.id;
            const tourName = this.dataset.name;

            const modalText = document.getElementById('confirmTourModalText');
            const modalGoBtn = document.getElementById('confirmTourModalGo');
            const modalConfirmBtn = document.getElementById('tourModalConfirm');

            // Actualizar contenido y enlaces del modal
            if (modalText) {
                modalText.innerHTML = `¿Deseas ir al tour "<strong>${tourName}</strong>"?`;
            }
            if (modalGoBtn) modalGoBtn.href = `/tour/${tourId}`;
            if (modalConfirmBtn) modalConfirmBtn.href = `/tour/${tourId}`;

            const modalEl = document.getElementById('confirmTourModal');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        });
    });
});

function renderStars(rating) {
    const fullStars = Math.floor(rating);
    const halfStar = rating % 1 >= 0.5 ? 1 : 0;
    const emptyStars = 5 - fullStars - halfStar;

    let stars = '';
    for (let i = 0; i < fullStars; i++) stars += '★';
    if (halfStar) stars += '⯨';
    for (let i = 0; i < emptyStars; i++) stars += '☆';

    const formattedRating = Number.isInteger(rating) ? rating : rating.toFixed(1);
    return `<div class="mb-2 text-warning review-stars">${stars}<span class="rating-number"> (${formattedRating}/5)</span></div>`;
}
