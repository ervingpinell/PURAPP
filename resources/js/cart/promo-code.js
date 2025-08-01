document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('apply-promo');
  const input = document.getElementById('promo-code');
  const totalEl = document.getElementById('cart-total');
  const msg = document.getElementById('promo-message');

  if (!btn || !input || !totalEl) return;

  const originalTotal = parseFloat(totalEl.textContent);

  btn.addEventListener('click', () => {
    const code = input.value.trim();
    if (!code) {
      msg.textContent = 'Por favor, ingresa un código.';
      msg.classList.remove('text-danger');
      return;
    }

    fetch('/api/apply-promo', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      body: JSON.stringify({
        code: code,
        total: originalTotal
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        totalEl.textContent = data.new_total.toFixed(2);
        msg.textContent = `¡Código aplicado! Descuento: $${data.discount_applied.toFixed(2)}`;
        msg.classList.remove('text-danger');
        msg.classList.add('text-success');
        document.getElementById('promo_code_hidden').value = code;
      } else {
        msg.textContent = data.message;
        msg.classList.add('text-danger');
        msg.classList.remove('text-success');
      }
    })
    .catch(() => {
      msg.textContent = 'Error al validar el código.';
      msg.classList.add('text-danger');
      msg.classList.remove('text-success');
    });
  });
});
