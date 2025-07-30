export default function renderReviewItem(r) {
    const card = document.createElement('div');
    card.classList.add('review-item', 'border', 'rounded', 'p-3', 'mb-3', 'bg-light');

    const avatar = r.avatarUrl || '/images/avatar-default.png';
    const date = r.publishedDate ? new Date(r.publishedDate).toLocaleDateString() : 'Fecha no disponible';

    card.innerHTML = `
        <div class="d-flex align-items-center mb-2">
            <img src="${avatar}" class="rounded-circle me-2" alt="Avatar" width="40" height="40">
            <div>
                <strong>${r.userName || 'Anónimo'}</strong><br>
                <small class="text-muted">${date}</small>
            </div>
        </div>
        <div class="rating mb-2">⭐️ ${r.rating}/5</div>
        <h6 class="fw-bold">${r.title}</h6>
        <p class="mb-0">${r.text}</p>
    `;
    return card;
}
