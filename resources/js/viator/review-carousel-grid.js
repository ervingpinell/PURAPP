document.addEventListener('DOMContentLoaded', () => {
    const tours = window.VIATOR_TOURS || [];

    tours.forEach(({ id, code }) => {
        const container = document.getElementById(`carousel-${id}`);
        const card = document.getElementById(`card-${id}`);
        if (!container || !card) return;

        fetch('/api/reviews', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({
                productCode: code,
                count: 20,
                start: 1,
                provider: 'VIATOR',
                sortBy: 'MOST_RECENT'
            })
        })
        .then(res => res.ok ? res.json() : Promise.reject(res))
        .then(data => {
            if (!data.reviews || data.reviews.length === 0) {
                container.innerHTML = '<p class="text-muted text-center">No reviews available.</p>';
                return;
            }

            let index = 0;
            let expanded = false;

            const render = () => {
                const r = data.reviews[index];
                const stars = '★'.repeat(Math.round(r.rating)) + '☆'.repeat(5 - Math.round(r.rating));
                const date = r.publishedDate ? new Date(r.publishedDate).toLocaleDateString() : '';
                const isShort = r.text.length < 120;
                const truncated = r.text.length > 250;
                const shortText = truncated ? r.text.slice(0, 250) + '...' : r.text;

                container.innerHTML = `
                    <div class="review-body-wrapper ${isShort ? 'centered' : ''}">
                        <div><strong>${r.userName || 'Anonymous'}</strong><br>${date}</div>
                        <div class="review-stars">${stars} (${r.rating}/5)</div>
                        ${r.title ? `<div class="review-label">${r.title}</div>` : ''}
                        <div class="review-content ${expanded ? 'expanded' : ''}">
                            <p>${expanded || !truncated ? r.text : shortText}</p>
                        </div>
                        ${truncated ? `<button class="review-toggle">${expanded ? 'Ver menos' : 'Ver más'}</button>` : ''}
                    </div>
                `;

                // Expandir visualmente la tarjeta
                if (expanded) {
                    card.classList.add('expanded');
                } else {
                    card.classList.remove('expanded');
                }
            };

            render();

            const prevBtn = document.querySelector(`.carousel-prev[data-tour="${id}"]`);
            const nextBtn = document.querySelector(`.carousel-next[data-tour="${id}"]`);

            if (prevBtn) prevBtn.onclick = () => {
                expanded = false;
                index = (index - 1 + data.reviews.length) % data.reviews.length;
                render();
            };
            if (nextBtn) nextBtn.onclick = () => {
                expanded = false;
                index = (index + 1) % data.reviews.length;
                render();
            };

            container.addEventListener('click', e => {
                if (e.target.classList.contains('review-toggle')) {
                    expanded = !expanded;
                    render();
                }
            });
        })
        .catch(err => {
            console.error('❌ Error cargando reseñas:', err);
            container.innerHTML = `<p class="text-danger text-center">Error loading reviews.</p>`;
        });
    });

    // ✅ Activar modal al hacer clic en el nombre del tour
    document.querySelectorAll('.tour-link').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();

            const id = link.dataset.id;
            const name = link.dataset.name;

            const modalName = document.getElementById('tourModalName');
            const modalLink = document.getElementById('tourModalConfirm');

            if (modalName) modalName.textContent = name;
            if (modalLink) modalLink.href = `/tour/${id}`;

            const modalElement = document.getElementById('confirmTourModal');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        });
    });
});
