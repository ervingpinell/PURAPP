document.addEventListener('DOMContentLoaded', () => {
    const tours = window.VIATOR_TOURS || [];

    tours.forEach(({ id, code }) => {
        const container = document.getElementById(`carousel-${id}`);
        if (!container) return;

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

                matchHeights();
            };

            render();

            // Eventos de flechas
            const prevBtn = document.querySelector(`.carousel-prev[data-tour="${id}"]`);
            const nextBtn = document.querySelector(`.carousel-next[data-tour="${id}"]`);

            if (prevBtn) prevBtn.onclick = () => {
                index = (index - 1 + data.reviews.length) % data.reviews.length;
                render();
            };
            if (nextBtn) nextBtn.onclick = () => {
                index = (index + 1) % data.reviews.length;
                render();
            };

            // Toggle ver más / menos
            container.addEventListener('click', e => {
                if (e.target.classList.contains('review-toggle')) {
                    expanded = !expanded;
                    render();
                    document.querySelectorAll('.review-content').forEach(el =>
                        el.classList.toggle('expanded', expanded)
                    );
                    document.querySelectorAll('.review-toggle').forEach(btn =>
                        btn.textContent = expanded ? 'Ver menos' : 'Ver más'
                    );
                }
            });
        })
        .catch(err => {
            console.error('❌ Error cargando reseñas:', err);
            container.innerHTML = `<p class="text-danger text-center">Error loading reviews.</p>`;
        });
    });

    // Ajusta altura por fila
    function matchHeights() {
        const cards = document.querySelectorAll('.review-card');
        const rows = {};

        cards.forEach(card => {
            const top = card.getBoundingClientRect().top;
            if (!rows[top]) rows[top] = [];
            rows[top].push(card);
        });

        Object.values(rows).forEach(row => {
            let max = 0;
            row.forEach(card => {
                card.style.height = 'auto';
                max = Math.max(max, card.offsetHeight);
            });
            row.forEach(card => {
                card.style.height = max + 'px';
            });
        });
    }

    window.addEventListener('resize', matchHeights);
    setTimeout(matchHeights, 100);
});
