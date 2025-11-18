/**
 * JS del iframe (embed):
 * - Inserta dinámicamente “Ver más/menos” si falta
 * - Truncado robusto (-webkit-line-clamp compatible)
 * - Notifica altura al padre (READY/RESIZE) y responde a PING_HEIGHT
 * - OPEN_TOUR hacia el padre
 * - Ajuste anti-overlap del título del tour (mobile/resize/fonts)
 */
(function () {
  "use strict";

  const qs = (s, el = document) => el.querySelector(s);

  const body = document.body;
  // Soporta HERO (.hero-card) y CARD/SITE (.review-item)
  const root = qs(".hero-card") || qs(".review-item");
  if (!root) return;

  // --- parámetros desde URL (cache-friendly)
  const params   = new URLSearchParams(location.search);
  const TXT_MORE = body.dataset.more || "Ver más";
  const TXT_LESS = body.dataset.less || "Mostrar menos";
  const BASE     = parseInt(params.get("base") || body.dataset.base || "460", 10);
  const UID      = params.get("uid") || body.dataset.uid || "";

  const rAF = (fn) =>
    window.requestAnimationFrame ? requestAnimationFrame(fn) : setTimeout(fn, 0);

  function getClampClass(textEl) {
    if (!textEl) return null;
    const classes = Array.from(textEl.classList);
    const clampNum = classes.find((c) => /^clamp-\d+$/i.test(c));
    if (clampNum) return clampNum;
    return classes.find((c) => c === "clamp") || null;
  }

  function needsTruncate(textEl) {
    if (!textEl) return false;
    const clampClass = getClampClass(textEl);
    if (clampClass && !textEl.classList.contains("expanded")) {
      textEl.classList.add(clampClass);
    }
    const clone = textEl.cloneNode(true);
    const cs = getComputedStyle(textEl);
    clone.style.visibility = "hidden";
    clone.style.position = "absolute";
    clone.style.pointerEvents = "none";
    clone.style.height = "auto";
    clone.style.maxHeight = "none";
    clone.style.overflow = "visible";
    clone.style.display = "block"; // rompe -webkit-box
    clone.style.webkitLineClamp = "unset";
    clone.style.whiteSpace = cs.whiteSpace;
    clone.style.lineHeight = cs.lineHeight;
    clone.style.fontSize = cs.fontSize;
    clone.style.width = textEl.clientWidth + "px";
    clone.classList.remove("expanded");
    const cc = getClampClass(clone);
    if (cc) clone.classList.remove(cc);
    document.body.appendChild(clone);
    const full = clone.scrollHeight;
    document.body.removeChild(clone);
    const visible = textEl.clientHeight;
    return full > visible + 1;
  }

  // 🔥 Medimos la ALTURA VISIBLE de la card (root),
  // no el scrollHeight del documento (que siempre refleja el texto completo).
  function currentHeight() {
    let h = 0;
    try {
      const rect = root.getBoundingClientRect();
      h = rect.height || 0;
    } catch (_) {
      h = 0;
    }

    if (!h || !isFinite(h)) {
      h = BASE;
    }

    h = Math.max(BASE, h);
    // límite razonable para no tener iframes gigantes
    return Math.min(1800, h + 2);
  }

  function postReady() {
    try {
      window.parent?.postMessage(
        { type: "REVIEW_IFRAME_READY", uid: UID },
        "*"
      );
    } catch (_) {}
  }
  function postResize() {
    try {
      window.parent?.postMessage(
        { type: "REVIEW_IFRAME_RESIZE", uid: UID, height: currentHeight() },
        "*"
      );
    } catch (_) {}
  }

  /* ---------- Ajuste anti-overlap del título ---------- */
  function adjustTitleLayout() {
    const card  = qs(".hero-card", root) || root;
    const title = qs(".tour-title-abs", card);
    const who   = qs(".who-when", card) || qs(".review-head", card);
    if (!(card && title)) return;

    const cb = card.getBoundingClientRect();
    const wb = who
      ? who.getBoundingClientRect()
      : { right: cb.left + cb.width * 0.32 };

    // Borde izquierdo del título: justo después del bloque del autor (+8px)
    let left = wb.right - cb.left + 8;
    left = Math.max(left, cb.width * 0.34); // deja mínimo ~34% para autor/avatar
    left = Math.min(left, cb.width * 0.7);  // nunca invadas demasiado

    // Nº de líneas según viewport
    const lines =
      window.innerWidth <= 420 ? 4 : window.innerWidth < 768 ? 3 : 2;

    card.style.setProperty("--title-left", left + "px");
    card.style.setProperty("--title-lines", lines);

    // Tras aplicar clamp, medimos altura real y la reservamos
    rAF(() => {
      const h = title.scrollHeight;
      card.style.setProperty("--title-h", h + 6 + "px");
      postResize(); // puede cambiar la altura del iframe
    });
  }

  function setupCard() {
    const isHero = !!qs(".review-textwrap", root); // sólo existe en HERO
    const text = qs(".review-content", root);
    if (!text) return;

    // Crea el botón si no existe (card/site llega sin él)
    let btn = qs(".review-toggle", root);
    if (!btn) {
      btn = document.createElement("button");
      btn.type = "button";
      btn.className = "review-toggle";
      btn.style.display = "none";
      text.insertAdjacentElement("afterend", btn);
    }

    const clampClass = getClampClass(text);
    if (clampClass) btn.dataset.clampClass = clampClass;

    const updateBtn = () => {
      if (text.classList.contains("expanded")) return;
      const show = needsTruncate(text);
      btn.style.display = show ? "inline-block" : "none";
      btn.textContent = TXT_MORE;
      btn.style.position = isHero ? "absolute" : "static";
    };

    updateBtn();
    adjustTitleLayout();

    btn.onclick = () => {
      const expanded = text.classList.toggle("expanded");
      root.classList.toggle("expanded-card", expanded);
      if (expanded) {
        if (btn.dataset.clampClass) text.classList.remove(btn.dataset.clampClass);
        btn.textContent = TXT_LESS;
        btn.style.position = "static";
      } else {
        if (btn.dataset.clampClass) text.classList.add(btn.dataset.clampClass);
        btn.textContent = TXT_MORE;
        btn.style.position = isHero ? "absolute" : "static";
        updateBtn();
      }
      rAF(() => {
        adjustTitleLayout();
        postResize();
      });
    };

    window.addEventListener(
      "resize",
      () => {
        updateBtn();
        adjustTitleLayout();
        postResize();
      },
      { passive: true }
    );

    if ("ResizeObserver" in window) {
      const ro = new ResizeObserver(() => {
        updateBtn();
        adjustTitleLayout();
        postResize();
      });
      ro.observe(text);
    }
    if (document.fonts?.ready) {
      document.fonts.ready.then(() => {
        updateBtn();
        adjustTitleLayout();
        postResize();
      });
    }
  }

  // Abrir tour (sólo aplica al HERO con link de título)
  document.addEventListener("click", (ev) => {
    const a = ev.target.closest("a.open-parent-modal");
    if (!a) return;
    ev.preventDefault();
    try {
      window.parent?.postMessage(
        {
          type: "OPEN_TOUR",
          uid: UID,
          href: a.href,
          name: a.dataset.name || a.textContent.trim(),
        },
        "*"
      );
    } catch (_) {}
  });

  window.addEventListener(
    "message",
    (e) => {
      const d = e?.data || {};
      if (d?.type === "PING_HEIGHT") postResize();
    },
    false
  );

  document.addEventListener("DOMContentLoaded", () => {
    setupCard();
    postReady();
    postResize();
    setTimeout(() => {
      adjustTitleLayout();
      postResize();
    }, 150);
    setTimeout(postResize, 250);
    window.addEventListener("load", () => {
      adjustTitleLayout();
      postResize();
    });
  });
})();
