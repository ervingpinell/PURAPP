/**
 * JS del iframe (embed):
 * - Inserta dinámicamente “Ver más/menos” si falta
 * - Truncado robusto (-webkit-line-clamp compatible)
 * - Notifica altura al padre (READY/RESIZE) y responde a PING_HEIGHT
 * - OPEN_TOUR hacia el padre
 */
(function () {
  "use strict";

  const qs = (s, el = document) => el.querySelector(s);

  const body = document.body;
  // Soporta HERO (.hero-card) y CARD/SITE (.review-item)
  const root = qs(".hero-card") || qs(".review-item");
  if (!root) return;

  const TXT_MORE = body.dataset.more || "Ver más";
  const TXT_LESS = body.dataset.less || "Ver menos";
  const BASE     = parseInt(body.dataset.base || "460", 10);
  const UID      = body.dataset.uid || "";

  const rAF = (fn) => window.requestAnimationFrame ? requestAnimationFrame(fn) : setTimeout(fn, 0);

  function getClampClass(textEl) {
    if (!textEl) return null;
    const classes = Array.from(textEl.classList);
    const clampNum = classes.find(c => /^clamp-\d+$/i.test(c));
    if (clampNum) return clampNum;
    return classes.find(c => c === "clamp") || null;
  }

  function needsTruncate(textEl) {
    if (!textEl) return false;
    const clampClass = getClampClass(textEl);
    if (clampClass && !textEl.classList.contains("expanded")) {
      textEl.classList.add(clampClass);
    }
    const clone = textEl.cloneNode(true);
    const cs = getComputedStyle(textEl);
    clone.style.visibility    = "hidden";
    clone.style.position      = "absolute";
    clone.style.pointerEvents = "none";
    clone.style.height        = "auto";
    clone.style.maxHeight     = "none";
    clone.style.overflow      = "visible";
    clone.style.display       = "block";     // rompe -webkit-box
    clone.style.webkitLineClamp = "unset";
    clone.style.whiteSpace    = cs.whiteSpace;
    clone.style.lineHeight    = cs.lineHeight;
    clone.style.fontSize      = cs.fontSize;
    clone.style.width         = textEl.clientWidth + "px";
    clone.classList.remove("expanded");
    if (clampClass) clone.classList.remove(clampClass);
    document.body.appendChild(clone);
    const full = clone.scrollHeight;
    document.body.removeChild(clone);
    const visible = textEl.clientHeight;
    return full > visible + 1;
  }

  function currentHeight() {
    const h = Math.max(BASE, root.scrollHeight || root.offsetHeight || BASE);
    return Math.min(1800, h);
  }

  function postReady()  { try { window.parent?.postMessage({ type: "REVIEW_IFRAME_READY",  uid: UID }, "*"); } catch (_) {} }
  function postResize() { try { window.parent?.postMessage({ type: "REVIEW_IFRAME_RESIZE", uid: UID, height: currentHeight() }, "*"); } catch (_) {} }

  function setupCard() {
    const isHero = !!qs(".review-textwrap", root); // sólo existe en HERO
    const text   = qs(".review-content", root);
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
      // En HERO lo queríamos absoluto; en CARD/SITE, flujo normal
      btn.style.position = isHero ? "absolute" : "static";
    };

    updateBtn();

    btn.onclick = () => {
      const expanded = text.classList.toggle("expanded");
      // La clase puede no tener efecto en CARD/SITE, pero no estorba
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
      rAF(postResize);
    };

    // Recalcular en resize/cambios de layout
    window.addEventListener("resize", () => { updateBtn(); postResize(); }, { passive: true });

    if ("ResizeObserver" in window) {
      const ro = new ResizeObserver(() => { updateBtn(); postResize(); });
      ro.observe(text);
    }
    if (document.fonts?.ready) {
      document.fonts.ready.then(() => { updateBtn(); postResize(); });
    }
  }

  // Abrir tour (sólo aplica al HERO con link de título)
  document.addEventListener("click", (ev) => {
    const a = ev.target.closest("a.open-parent-modal");
    if (!a) return;
    ev.preventDefault();
    try {
      window.parent?.postMessage({
        type: "OPEN_TOUR",
        uid: UID,
        href: a.href,
        name: a.dataset.name || a.textContent.trim()
      }, "*");
    } catch (_) {}
  });

  window.addEventListener("message", (e) => {
    const d = e?.data || {};
    if (d?.type === "PING_HEIGHT") postResize();
  }, false);

  document.addEventListener("DOMContentLoaded", () => {
    setupCard();
    postReady();
    postResize();
    setTimeout(postResize, 250);
    window.addEventListener("load", postResize);
  });
})();
