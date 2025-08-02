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
      msg.classList.remove('text-success');
      return;
    }

    fetch('/apply-promo', {
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
    .then(async res => {
        if (!res.ok) {
            // Si la respuesta no es 'ok' (código de estado no 2xx)
            msg.textContent = 'Error al contactar con el servidor.';
            msg.classList.add('text-danger');
            msg.classList.remove('text-success');
            return;
        }

        const data = await res.json();

        if (data.success) {
            // Si la respuesta tiene un "success" verdadero
            const newTotal = parseFloat(data.new_total) || originalTotal;
            totalEl.textContent = newTotal.toFixed(2);
            const discount = parseFloat(data.discount_applied) || 0;
            msg.textContent = `¡Código aplicado! Descuento: $${discount.toFixed(2)}`;
            msg.classList.remove('text-danger');
            msg.classList.add('text-success');
            document.getElementById('promo_code_hidden').value = code;
        } else {
            // Si el éxito es falso, muestra el mensaje de error
            msg.textContent = data.message || 'Código no válido.';
            msg.classList.add('text-danger');
            msg.classList.remove('text-success');
        }
    })
    .catch(error => {
        // Si ocurre un error en la solicitud (como problemas de red)
        console.error('Error al aplicar el código:', error); // Agrega un log para depuración
        msg.textContent = 'Error de conexión al validar el código.';
        msg.classList.add('text-danger');
        msg.classList.remove('text-success');
    });
  });
});
