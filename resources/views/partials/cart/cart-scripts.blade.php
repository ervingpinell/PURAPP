@push('scripts')
<script>
  (function ensureSwal(cb) {
    if (window.Swal) return cb();
    const s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
    s.onload = cb;
    document.head.appendChild(s);
  })(function() {

    document.addEventListener('DOMContentLoaded', () => {
      /* =========================
         Utilidades
         ========================= */
      const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

      const swalInfo = (title, text) =>
        Swal.fire({
          icon: 'info',
          title,
          text,
          confirmButtonColor: '#6c757d'
        });

      const swalSuccess = (text) =>
        Swal.fire({
          icon: 'success',
          title: @json(__('adminlte::adminlte.success')),
          text,
          confirmButtonColor: '#198754',
          timer: 1800,
          showConfirmButton: false
        });

      const clamp = (v, min, max) => Math.max(min, Math.min(max, v));

      /* =========================
         I18N helpers (categorías y validaciones)
         ========================= */
      const $cfg = document.getElementById('mp-config');

      // Mapas inyectables desde Blade en data-attrs (opcionales)
      let CAT_BY_ID = {};
      let CAT_BY_CODE = {};
      try {
        CAT_BY_ID = JSON.parse($cfg?.getAttribute('data-catmap-byid') || '{}') || {};
      } catch (_) {}
      try {
        CAT_BY_CODE = JSON.parse($cfg?.getAttribute('data-catmap-bycode') || '{}') || {};
      } catch (_) {}

      // Fallbacks por código con traducciones de Laravel
      const DEFAULT_CODE_MAP = {
        adult: @json(__('adminlte::adminlte.adult')),
        adults: @json(__('adminlte::adminlte.adults')),
        kid: @json(__('adminlte::adminlte.kid')),
        kids: @json(__('adminlte::adminlte.kids')),
        child: @json(__('adminlte::adminlte.kid')),
        children: @json(__('adminlte::adminlte.kids')),
        senior: @json(__('adminlte::adminlte.senior') ?? 'Senior'),
        student: @json(__('adminlte::adminlte.student') ?? 'Student'),
      };
      CAT_BY_CODE = Object.assign({}, DEFAULT_CODE_MAP, CAT_BY_CODE);

      // Resolver nombre de categoría (para cualquier objeto-like de categoría)
      function resolveCategoryName(catLike) {
        const get = (p, d = null) => p.split('.').reduce((o, k) => (o && o[k] !== undefined) ? o[k] : d, catLike);

        // 1) i18n embebido
        let name =
          get('i18n_name') ||
          get('name') ||
          get('label') ||
          get('category_name') ||
          get('category.name');

        if (name) return String(name);

        // 2) por ID
        const id = Number(get('category_id', get('id', 0))) || 0;
        if (id && CAT_BY_ID && CAT_BY_ID[id]) return String(CAT_BY_ID[id]);

        // 3) por code
        const code = get('code', null);
        if (code) {
          if (CAT_BY_CODE && CAT_BY_CODE[code]) return String(CAT_BY_CODE[code]);
          // fallback: prettify del code
          return String(code).replace(/[_-]+/g, ' ').replace(/\b\w/g, m => m.toUpperCase());
        }

        // 4) prettify del slug
        const slug = String(get('category_slug', get('slug', '')) || '');
        if (slug) {
          return slug.replace(/[_-]+/g, ' ').replace(/\b\w/g, m => m.toUpperCase());
        }

        return 'Category';
      }

      // I18N de validaciones (sin hardcode)
      const I18N = {
        info: @json(__('adminlte::adminlte.info')),
        minAdults: @json(__('carts.validation.min_adults', ['min' => 2])),
        maxKids: @json(__('carts.validation.max_kids', ['max' => 2])),
        maxTotal: @json(__('carts.validation.max_total', ['max' => 12])),
      };

      /* =========================
         1) Confirmar reserva
         ========================= */
      const reservaForm = document.getElementById('confirm-reserva-form');
      if (reservaForm) {
        reservaForm.addEventListener('submit', function(e) {
          e.preventDefault();
          Swal.fire({
            title: @json(__('adminlte::adminlte.confirmReservationTitle')),
            text: @json(__('adminlte::adminlte.confirmReservationText')),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#d33',
            confirmButtonText: @json(__('adminlte::adminlte.confirmReservationConfirm')),
            cancelButtonText: @json(__('adminlte::adminlte.confirmReservationCancel'))
          }).then((r) => {
            if (r.isConfirmed) {
              reservaForm.submit();
            }
          });
        });
      }

      /* =========================
         2) Eliminar ítem (confirmación)
         ========================= */
      document.querySelectorAll('.delete-item-form').forEach(form => {
        form.addEventListener('submit', function(e) {
          e.preventDefault();
          Swal.fire({
            title: @json(__('adminlte::adminlte.deleteItemTitle')),
            text: @json(__('adminlte::adminlte.deleteItemText')),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: @json(__('adminlte::adminlte.deleteItemConfirm')),
            cancelButtonText: @json(__('adminlte::adminlte.deleteItemCancel'))
          }).then((r) => {
            if (r.isConfirmed) {
              form.submit();
            }
          });
        });
      });

      /* =========================
         3) Previsualización Meeting Point
         ========================= */
      const pickupLabel = ($cfg?.dataset?.pickupLabel) || 'Pick-up';
      const updateMpInfo = (selectEl) => {
        if (!selectEl) return;
        let mplist = [];
        try {
          mplist = JSON.parse(selectEl.getAttribute('data-mplist') || '[]');
        } catch (_) {
          mplist = [];
        }
        const box = document.querySelector(selectEl.getAttribute('data-target'));
        if (!box) return;

        const id = selectEl.value ? Number(selectEl.value) : null;
        const found = id ? mplist.find(m => Number(m.id) === id) : null;

        const nameEl = box.querySelector('.mp-name');
        const timeEl = box.querySelector('.mp-time');
        const addrEl = box.querySelector('.mp-addr');
        const linkEl = box.querySelector('.mp-link');

        if (found) {
          box.style.display = 'block';
          if (nameEl) nameEl.textContent = found.name || '';
          if (timeEl) timeEl.textContent = found.pickup_time ? (pickupLabel + ': ' + found.pickup_time) : '';
          if (addrEl) addrEl.innerHTML = found.description ? ('<i class="fas fa-map-marker-alt me-1"></i>' + found.description) : '';
          if (linkEl) {
            if (found.map_url) {
              linkEl.href = found.map_url;
              linkEl.style.display = 'inline-block';
            } else {
              linkEl.style.display = 'none';
            }
          }
        } else {
          box.style.display = 'none';
        }
      };
      document.querySelectorAll('.meetingpoint-select').forEach(sel => {
        updateMpInfo(sel);
        sel.addEventListener('change', () => updateMpInfo(sel));
      });

      /* =========================
         4) Tabs de Pickup (Hotel / Otro / Punto)
         ========================= */
      document.querySelectorAll('.pickup-tabs').forEach(group => {
        const itemId = group.dataset.item;
        const init = group.dataset.init || 'hotel';

        const setActive = (target) => {
          group.querySelectorAll('button').forEach(btn => btn.classList.remove('active', 'btn-secondary'));
          const activeBtn = group.querySelector(`[data-pickup-tab="${target}"]`);
          if (activeBtn) activeBtn.classList.add('active', 'btn-secondary');

          document
            .querySelectorAll(`#pickup-panes-${itemId} .pickup-pane`)
            .forEach(pane => pane.style.display = pane.id.includes(`-${target}-`) ? 'block' : 'none');

          // hidden flag para backend (solo se marca 1 cuando "custom")
          const isOtherHidden = document.getElementById(`is-other-hidden-${itemId}`);
          if (isOtherHidden) isOtherHidden.value = (target === 'custom') ? 1 : 0;

          // limpieza básica de selects cuando cambia a otro modo
          const hotelSelect = document.getElementById(`hotel-select-${itemId}`);
          const mpSelect = document.getElementById(`meetingpoint-select-${itemId}`);
          if (target === 'hotel') {
            if (mpSelect) {
              mpSelect.value = '';
              updateMpInfo(mpSelect);
            }
          } else if (target === 'custom') {
            if (hotelSelect) hotelSelect.value = '';
            if (mpSelect) {
              mpSelect.value = '';
              updateMpInfo(mpSelect);
            }
          } else if (target === 'mp') {
            if (hotelSelect) hotelSelect.value = '';
          }
        };

        group.querySelectorAll('button').forEach(btn => {
          btn.addEventListener('click', () => setActive(btn.dataset.pickupTab));
        });

        setActive(init);
      });

      /* =========================
         5) Evitar doble submit en formularios de edición
         ========================= */
      document.querySelectorAll('.edit-item-form').forEach(f => {
        f.addEventListener('submit', (e) => {
          // validación frontend (ver sección 6)
          if (!validateEditForm(f)) {
            e.preventDefault();
            return;
          }
          const btn = f.querySelector('button[type="submit"]');
          if (btn) {
            btn.disabled = true;
            btn.innerHTML =
              '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' +
              (@json(__('adminlte::adminlte.saving')));
          }
        });
      });

      /* =========================
         6) Real-time Category Validation & Price Calculation
            - Validates against category max and global max (12)
            - Shows live price breakdown
            - Disables submit button when invalid
         ========================= */
      const GLOBAL_MAX_PERSONS = 12;

      function parseIntSafe(v) {
        const n = parseInt(v, 10);
        return isNaN(n) ? 0 : n;
      }

      function attachCategoryValidation(modal) {
        const form = modal.querySelector('form.edit-item-form');
        if (!form) return;

        const itemId = modal.id.replace('editItemModal-', '');
        const inputs = form.querySelectorAll('.category-quantity-input');
        const submitBtn = document.getElementById(`submit-btn-${itemId}`);
        const validationAlert = document.getElementById(`validation-alert-${itemId}`);
        const validationMessage = document.getElementById(`validation-message-${itemId}`);
        const priceBreakdown = document.getElementById(`price-breakdown-${itemId}`);
        const modalTotal = document.getElementById(`modal-total-${itemId}`);

        if (!inputs.length) return;

        const validateAndUpdate = () => {
          let totalPersons = 0;
          let totalPrice = 0;
          let hasError = false;
          let errorMessage = '';
          const breakdown = [];

          inputs.forEach(input => {
            const qty = parseIntSafe(input.value);
            const categoryMax = parseIntSafe(input.dataset.categoryMax);
            const globalMax = parseIntSafe(input.dataset.globalMax);
            const price = parseFloat(input.dataset.categoryPrice) || 0;
            const categoryName = input.dataset.categoryName || 'Category';

            if (qty > 0) {
              totalPersons += qty;
              const subtotal = qty * price;
              totalPrice += subtotal;

              breakdown.push({
                name: categoryName,
                qty: qty,
                price: price,
                subtotal: subtotal
              });

              // Check category max
              if (qty > categoryMax) {
                hasError = true;
                errorMessage = @json(__('carts.validation.category_max_exceeded', ['category' => ':category', 'max' => ':max']))
                  .replace(':category', categoryName)
                  .replace(':max', categoryMax);
              }
            }
          });

          // Check global max
          if (totalPersons > GLOBAL_MAX_PERSONS) {
            hasError = true;
            errorMessage = @json(__('carts.validation.max_total', ['max' => 12]))
              .replace(':max', GLOBAL_MAX_PERSONS);
          }

          // Check minimum (at least 1 person)
          if (totalPersons === 0) {
            hasError = true;
            errorMessage = @json(__('m_bookings.validation.min_one_person_required'));
          }

          // Update validation alert
          if (hasError) {
            validationAlert.style.display = 'block';
            validationMessage.textContent = errorMessage;
            if (submitBtn) {
              submitBtn.disabled = true;
              submitBtn.classList.add('disabled');
            }
          } else {
            validationAlert.style.display = 'none';
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.classList.remove('disabled');
            }
          }

          // Update price breakdown
          if (priceBreakdown) {
            if (breakdown.length > 0) {
              priceBreakdown.innerHTML = breakdown.map(item => `
                <div class="d-flex justify-content-between mb-1">
                  <span>${item.qty}x ${item.name}</span>
                  <span>$${item.subtotal.toFixed(2)}</span>
                </div>
              `).join('');
            } else {
              priceBreakdown.innerHTML = '<div class="text-muted text-center py-2">' +
                @json(__('adminlte::adminlte.noItemsSelected') ?? 'No items selected') +
                '</div>';
            }
          }

          // Update total
          if (modalTotal) {
            modalTotal.textContent = totalPrice.toFixed(2);
          }
        };

        // Attach listeners to all category inputs
        inputs.forEach(input => {
          ['input', 'change'].forEach(event => {
            input.addEventListener(event, validateAndUpdate);
          });
        });

        // Initial validation
        validateAndUpdate();
      }

      // Apply to all edit modals
      document.querySelectorAll('.modal[id^="editItemModal-"]').forEach(modal => {
        attachCategoryValidation(modal);

        // Re-validate when modal is shown (in case data changed)
        modal.addEventListener('shown.bs.modal', () => {
          attachCategoryValidation(modal);
        });
      });

      /* =========================
         7) Código Promocional (Apply / Remove)
         ========================= */
      (function() {
        const promoBtn = document.getElementById('toggle-promo');
        const promoIn = document.getElementById('promo-code');
        const promoMsg = document.getElementById('promo-message');
        const totalEl = document.getElementById('cart-total');

        const baseError = @json(__('carts.messages.invalid_code'));

        if (!promoBtn || !promoIn || !totalEl) return;

        promoBtn.addEventListener('click', async (e) => {
          e.preventDefault();
          const state = promoBtn.dataset.state || 'idle';

          if (state === 'applied') {
            try {
              const res = await fetch(@json(route('public.carts.removePromo')), {
                method: 'DELETE',
                headers: {
                  'X-CSRF-TOKEN': csrf,
                  'Accept': 'application/json'
                }
              });
              const data = await res.json();
              if (res.ok && data.ok) {
                promoBtn.className = 'btn btn-outline-primary';
                promoBtn.dataset.state = 'idle';
                promoBtn.textContent = @json(__('adminlte::adminlte.apply'));

                // Clear message and remove green color
                if (promoMsg) {
                  promoMsg.className = 'mt-2 small';
                  promoMsg.textContent = '';
                }

                // Clear input value
                if (promoIn) promoIn.value = '';

                // Update total
                totalEl.textContent = parseFloat(data.new_total).toFixed(2);

                // Remove promo discount line
                const existingPromo = document.getElementById('promo-discount-line');
                if (existingPromo) existingPromo.remove();
              } else {
                Swal.fire('Oops!', data?.message || baseError, 'error');
              }
            } catch (_) {
              Swal.fire('Oops!', baseError, 'error');
            }
            return;
          }

          // apply
          const code = (promoIn.value || '').trim();
          if (!code) {
            return Swal.fire('Oops!', @json(__('carts.messages.enter_code')), 'info');
          }

          try {
            const res = await fetch(@json(route('public.carts.applyPromo')), {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
              },
              body: JSON.stringify({
                code
              })
            });

            // Handle 429 Rate Limit
            if (res.status === 429) {
              Swal.fire('Attention', @json(__('carts.promo.too_many_attempts')), 'warning');
              return;
            }

            const data = await res.json();

            if (res.ok && data.ok) {
              promoBtn.className = 'btn btn-outline-danger';
              promoBtn.dataset.state = 'applied';
              promoBtn.textContent = @json(__('adminlte::adminlte.remove'));

              // Update message with green color
              if (promoMsg) {
                promoMsg.className = 'mt-2 small text-success';
                promoMsg.innerHTML = '<i class="fas fa-check-circle me-1"></i>' + @json(__('carts.messages.code_applied'));
              }

              // Update total
              totalEl.textContent = parseFloat(data.new_total).toFixed(2);

              // Show promo discount line (like after page refresh)
              const subtotalRow = document.querySelector('.d-flex.justify-content-between.mb-2');
              if (subtotalRow && data.promo) {
                // Remove existing promo line if any
                const existingPromo = document.getElementById('promo-discount-line');
                if (existingPromo) existingPromo.remove();

                // Create new promo line
                const promoLine = document.createElement('div');
                promoLine.id = 'promo-discount-line';
                promoLine.className = 'd-flex justify-content-between mb-2 text-' + (data.promo.operation === 'add' ? 'danger' : 'success');
                promoLine.innerHTML = `
                  <span>
                    <i class="fas fa-tag"></i> ${data.promo.code || 'PROMO'}
                  </span>
                  <span>
                    ${data.promo.operation === 'add' ? '+' : '-'}$${parseFloat(data.promo.adjustment || 0).toFixed(2)}
                  </span>
                `;

                // Insert after subtotal row
                subtotalRow.parentNode.insertBefore(promoLine, subtotalRow.nextSibling);
              }
            } else {
              Swal.fire('Oops!', data?.message || baseError, 'error');
            }
          } catch (err) {
            console.error(err);
            Swal.fire('Oops!', baseError, 'error');
          }
        });
      })();

      /* =========================
         8) Timer (countdown only, no extensions)
         ========================= */
      (function() {
        const box = document.getElementById('cart-timer');
        if (!box) return;

        const remainingEl = document.getElementById('cart-timer-remaining');
        const barEl = document.getElementById('cart-timer-bar');
        const expireEndpoint = box.getAttribute('data-expire-endpoint');

        const totalSecondsCfg = Number(box.getAttribute('data-total-minutes') || '30') * 60;
        let serverExpires = new Date(box.getAttribute('data-expires-at')).getTime();

        const fmt = (sec) => {
          const s = Math.max(0, sec | 0);
          const m = Math.floor(s / 60),
            r = s % 60;
          return String(m).padStart(2, '0') + ':' + String(r).padStart(2, '0');
        };
        const setBar = (remainingSec) => {
          const frac = Math.max(0, Math.min(1, remainingSec / totalSecondsCfg));
          if (barEl) barEl.style.width = (frac * 100).toFixed(2) + '%';
        };

        let rafId = null;
        const tick = () => {
          const now = Date.now();
          const remainingSec = Math.ceil((serverExpires - now) / 1000);
          if (remainingEl) remainingEl.textContent = fmt(remainingSec);
          setBar(remainingSec);
          if (remainingSec <= 0) {
            cancelAnimationFrame(rafId);
            return handleExpire();
          }
          rafId = requestAnimationFrame(tick);
        };

        const handleExpire = async () => {
          // 1. Anunciar expiración
          await Swal.fire({
            icon: 'warning',
            title: @json(__('carts.timer.expired_title') ?? 'Tiempo agotado'),
            text: @json(__('carts.timer.expired_text') ?? 'Tu carrito ha expirado. Serás redirigido al inicio.'),
            confirmButtonText: 'OK',
            allowOutsideClick: false,
            allowEscapeKey: false
          });

          // 2. Llamar al endpoint para limpiar backend
          try {
            await fetch(expireEndpoint, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
              }
            });
          } catch (_) {}

          // 3. Redirigir al home reemplazando el historial para evitar "Atrás"
          window.location.replace('/');
        };

        // Expose countdown for timer widget
        window.cartCountdown = {
          getRemainingSeconds: () => {
            const now = Date.now();
            return Math.max(0, Math.ceil((serverExpires - now) / 1000));
          },
          isExpired: () => {
            const now = Date.now();
            return (serverExpires - now) <= 0;
          }
        };

        tick();
      })();

    }); // DOMContentLoaded
  }); // ensureSwal
</script>
@endpush