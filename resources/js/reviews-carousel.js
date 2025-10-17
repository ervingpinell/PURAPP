(function () {
  "use strict";

  const G = (typeof window !== "undefined" && window) || {};
  const I18N = G.REVIEWS_I18N || {};

  // ====== NUEVO: estado/umbrales para alturas ======
  const EMBED_STATE = new Map(); // uid -> última altura aplicada
  const MIN_H = 260;             // altura mínima aceptada
  const MAX_H = 1100;            // altura máxima aceptada
  const MAX_STEP = 220;          // salto máximo permitido entre mediciones

  const rAF = (fn) => (G.requestAnimationFrame || ((f) => setTimeout(f, 0)))(fn);

  function toInt(v, def) { const n = parseInt(v, 10); return Number.isFinite(n) ? n : def; }

  function getBaseHeight(root) {
    if (root.dataset.base) return toInt(root.dataset.base, 460);
    const cssVal = getComputedStyle(root).getPropertyValue("--provider-base");
    if (cssVal) return toInt(cssVal, 460);
    return 460;
  }

  // Altura base para un iframe concreto (lee data-base o var inline)
  function getBaseForIframe(ifr) {
    const fromAttr = ifr.closest("[data-base]")?.getAttribute("data-base");
    const fromCss  = ifr.style.getPropertyValue("--embed-h") || getComputedStyle(ifr).getPropertyValue("--embed-h");
    const v = toInt(fromAttr || fromCss, 460);
    return Number.isFinite(v) ? v : 460;
  }

  // Aplica altura de forma consistente usando CSS var y propiedades directas
  function applyEmbedHeight(ifr, h) {
    const shell = ifr?.closest(".iframe-shell");
    if (!ifr || !shell) return;
    const px = `${h}px`;
    shell.style.setProperty("--embed-h", px);
    ifr.style.setProperty("--embed-h", px);

    // Refuerzo para navegadores que tardan con CSS variables
    shell.style.height = px;
    shell.style.minHeight = px;
    ifr.style.height = px;
    ifr.style.minHeight = px;
  }

  function getClampClass(el) {
    if (!el) return null;
    const cls = Array.from(el.classList).find((c) => c === "clamp" || c.startsWith("clamp-"));
    return cls || null;
  }

  function getLineHeightPx(el){
    const cs = getComputedStyle(el);
    const lh = cs.lineHeight;
    if (lh && lh !== "normal") return parseFloat(lh);
    const fs = parseFloat(cs.fontSize) || 16;
    return fs * 1.2;
  }

  function naturalLineCount(el){
    if (!el) return 0;
    const clampClass = getClampClass(el);
    const hadClamp   = clampClass && el.classList.contains(clampClass);
    const wasExp     = el.classList.contains("expanded");

    if (wasExp) el.classList.remove("expanded");
    if (hadClamp) el.classList.remove(clampClass);

    const lh = getLineHeightPx(el);
    const lines = Math.round(el.scrollHeight / Math.max(1, lh));

    if (hadClamp) el.classList.add(clampClass);
    if (wasExp) el.classList.add("expanded");

    return lines;
  }

  function exceedsMaxLines(el){
    const isMobile = window.matchMedia("(max-width:768px)").matches;
    const maxLines = isMobile ? 4 : 5;
    const lines = naturalLineCount(el);
    return lines > maxLines;
  }

  function getTXT(root) {
    return {
      more:        root.dataset.more        || I18N.more      || "Leer más",
      less:        root.dataset.less        || I18N.less      || "Mostrar menos",
      by:          root.dataset.by          || I18N.by        || "Proporcionado por",
      openTitle:   root.dataset.openTitle   || I18N.swalTitle || "Abrir tour",
      openPre:     root.dataset.openPre     || I18N.swalText  || "¿Quieres ir a la página del tour seleccionado?",
      openConfirm: root.dataset.openConfirm || I18N.swalOK    || "Sí, abrir el tour",
      openCancel:  root.dataset.openCancel  || I18N.swalCancel|| "Cancelar",
    };
  }

  /* ---------- Ajuste anti-overlap del título ---------- */
  function adjustTitleLayoutFor(card) {
    if (!card) return;
    const title = card.querySelector(".tour-title-abs");
    if (!title) return;
    const who = card.querySelector(".who-when") || card.querySelector(".review-head") || card.querySelector(".review-meta");

    const cb = card.getBoundingClientRect();
    const wb = who ? who.getBoundingClientRect() : { right: cb.left + cb.width * 0.32 };

    let left = (wb.right - cb.left) + 8;
    left = Math.max(left, cb.width * 0.34);
    left = Math.min(left, cb.width * 0.70);

    const vw = window.innerWidth || document.documentElement.clientWidth || 1024;
    const lines = (vw <= 420) ? 4 : (vw < 768 ? 3 : 2);

    card.style.setProperty("--title-left",  left + "px");
    card.style.setProperty("--title-lines", lines);

    requestAnimationFrame(() => {
      const h = title.scrollHeight;
      card.style.setProperty("--title-h", (h + 6) + "px");
    });
  }

  function setupCard(card, TXT_MORE, TXT_LESS) {
    const title = card.querySelector(".tour-title-abs");
    const wrap  = card.querySelector(".review-textwrap");
    const text  = card.querySelector(".review-content");
    const btn   = card.querySelector(".review-toggle");

    if (title) card.classList.add("pad-title");
    if (!(wrap && text && btn)) { if (title) adjustTitleLayoutFor(card); return; }

    const clampClass = getClampClass(text);
    if (clampClass && !text.classList.contains("expanded")) {
      text.classList.add(clampClass);
    }
    if (clampClass) btn.dataset.clampClass = clampClass;

    const updateBtn = () => {
      if (text.classList.contains("expanded")) return;
      btn.style.display = exceedsMaxLines(text) ? "inline-block" : "none";
      btn.textContent   = TXT_MORE;
      btn.style.position = "absolute";
      adjustTitleLayoutFor(card);
    };

    updateBtn();

    btn.onclick = () => {
      const expanded = text.classList.toggle("expanded");
      if (expanded) {
        if (btn.dataset.clampClass) text.classList.remove(btn.dataset.clampClass);
        btn.textContent = TXT_LESS;
        btn.style.position = "static";
      } else {
        if (btn.dataset.clampClass) text.classList.add(btn.dataset.clampClass);
        btn.textContent = TXT_MORE;
        btn.style.position = "absolute";
        btn.style.display = exceedsMaxLines(text) ? "inline-block" : "none";
      }
      adjustTitleLayoutFor(card);
    };
  }

  function refreshCards(root, TXT_MORE) {
    root.querySelectorAll(".hero-card").forEach((card) => {
      const text = card.querySelector(".review-content");
      const btn  = card.querySelector(".review-toggle");
      if (text && btn && !text.classList.contains("expanded")) {
        btn.style.display = exceedsMaxLines(text) ? "inline-block" : "none";
        btn.textContent = TXT_MORE;
        btn.style.position = "absolute";
      }
      adjustTitleLayoutFor(card);
    });
  }

  function pingVisibleIframes(root, targetOrigin) {
    root.querySelectorAll("iframe.review-embed[data-uid]").forEach((ifr) => {
      try { ifr.contentWindow?.postMessage({ type: "PING_HEIGHT", uid: ifr.dataset.uid }, targetOrigin || "*"); } catch (_) {}
    });
  }

  function loadIframe(ifr, lazySet) {
    if (ifr?.dataset?.src) {
      ifr.src = ifr.dataset.src;
      delete ifr.dataset.src;
      lazySet?.delete(ifr);
    }
  }

  // Antes: relaxHeightsFor hacía overflow visible en varios contenedores
  // Ahora: solo fijamos la altura base de shell/iframe (sin relajar contenedores)
  function primeBaseHeight(ifr, base) {
    if (!ifr) return;
    applyEmbedHeight(ifr, Math.max(MIN_H, base));
  }

  /* =========================================================
   * Indicador segmentado debajo del carrusel (clicable)
   * ========================================================= */
  function setupCarouselSegments(root) {
    const items = root.querySelectorAll(".carousel-item");
    const total = items.length;
    if (total <= 1) return;

    const bar = document.createElement("div");
    bar.className = "carousel-segments";

    const segs = [];
    for (let i = 0; i < total; i++) {
      const s = document.createElement("button");
      s.type = "button";
      s.className = "carousel-segment";
      s.setAttribute("aria-label", `Slide ${i + 1} de ${total}`);
      s.dataset.index = String(i);
      bar.appendChild(s);
      segs.push(s);
    }

    const inner = root.querySelector(".carousel-inner");
    const firstControl = root.querySelector(".carousel-control-prev, .carousel-control-next");
    if (inner && firstControl && firstControl.parentElement === root) {
      root.insertBefore(bar, firstControl);
    } else {
      root.appendChild(bar);
    }

    let current = Math.max(0, Array.from(items).findIndex(it => it.classList.contains("active")));
    if (current === -1) current = 0;

    function render() {
      segs.forEach((el, idx) => el.classList.toggle("is-active", idx === current));
      bar.setAttribute("aria-live", "polite");
      bar.setAttribute("aria-label", `${current + 1} / ${total}`);
    }
    render();

    bar.addEventListener("click", (e) => {
      const btn = e.target.closest(".carousel-segment");
      if (!btn) return;
      const to = parseInt(btn.dataset.index, 10) || 0;
      const bsCarousel = root && G.bootstrap && G.bootstrap.Carousel
        ? G.bootstrap.Carousel.getInstance(root) || new G.bootstrap.Carousel(root)
        : null;
      if (bsCarousel && Number.isFinite(to)) {
        bsCarousel.to(to);
      } else {
        const list = root.querySelectorAll(".carousel-item");
        list.forEach(n => n.classList.remove("active"));
        list[to]?.classList.add("active");
        current = to;
        render();
      }
    });

    root.addEventListener("slide.bs.carousel", (e) => {
      if (typeof e.to === "number") current = e.to;
      render();
    });
    root.addEventListener("slid.bs.carousel", (e) => {
      if (typeof e.to === "number") current = e.to;
      render();
    });
  }

  /* ========================================================= */

  function initOne(root) {
    if (!root || root.__reviewsInit) return;
    root.__reviewsInit = true;

    const TXT  = getTXT(root);
    const BASE = getBaseHeight(root);

    const allowedOrigins = new Set([location.origin]);
    const targetOrigin   = location.origin;

    // Local cards
    root.querySelectorAll(".hero-card").forEach((card) => setupCard(card, TXT.more, TXT.less));

    const onResize = () => refreshCards(root, TXT.more);
    window.addEventListener("resize", onResize, { passive: true });
    root.addEventListener("slid.bs.carousel", onResize);

    // Indicador segmentado
    setupCarouselSegments(root);

    // ====== postMessage desde iframes (con clamps/estado) ======
    window.addEventListener("message", (e) => {
      if (!e || !e.origin || !allowedOrigins.has(e.origin)) return;
      const d = e?.data || {};
      const uid = d.uid;
      if (!uid) return;

      const ifr = root.querySelector('iframe.review-embed[data-uid="' + uid + '"]');
      if (!ifr) return;

      // Si su slide no está activo, guarda estado y no apliques (evita empuje)
      const item = ifr.closest(".carousel-item");
      const isActive = !item || item.classList.contains("active");

      if (d.type === "REVIEW_IFRAME_READY") {
        const b = getBaseForIframe(ifr) || BASE;
        primeBaseHeight(ifr, b);
        const sk = ifr.previousElementSibling;
        if (sk && sk.classList.contains("iframe-skeleton")) sk.classList.add("is-hidden");
        ifr.setAttribute("data-ready","1");
        return;
      }

      if (d.type === "REVIEW_IFRAME_RESIZE") {
        const base = getBaseForIframe(ifr) || BASE;
        let next = toInt(d.height, base);
        if (!Number.isFinite(next)) return;

        // Clamp absoluto
        next = Math.max(MIN_H, Math.min(MAX_H, next));

        // Amortiguación: no crecer de golpe más de MAX_STEP
        const prev = EMBED_STATE.get(uid) ?? base;
        if (next - prev > MAX_STEP) next = prev + MAX_STEP;

        // Guarda estado siempre
        EMBED_STATE.set(uid, next);

        // Aplica solo si el slide está activo
        if (isActive) {
          rAF(() => applyEmbedHeight(ifr, next));
          const sk = ifr.previousElementSibling;
          if (sk && sk.classList.contains("iframe-skeleton")) sk.classList.add("is-hidden");
          ifr.setAttribute("data-ready","1");
        }
        return;
      }

      if (d.type === "OPEN_TOUR") {
        const href = d.href;
        const name = d.name || "";
        if (!href) return;
        const title   = TXT.openTitle;
        const pre     = TXT.openPre;
        const confirm = TXT.openConfirm;
        const cancel  = TXT.openCancel;

        if (G.Swal?.fire) {
          G.Swal.fire({
            icon: "question",
            title: title,
            html: pre + (name ? " <strong>" + name + "</strong>." : ""),
            showCancelButton: true,
            confirmButtonText: confirm,
            cancelButtonText: cancel,
            focusConfirm: true,
          }).then((res) => { if (res.isConfirmed) G.location.assign(href); });
        } else {
          if (G.confirm(title + "\n\n" + pre + (name ? ' "' + name + '"' : "") + ".")) {
            G.location.assign(href);
          }
        }
      }
    }, false);

    // ====== Lazy iframes ======
    const lazyIframes = new Set(Array.from(root.querySelectorAll("iframe.review-embed[data-src]")));
    const io = "IntersectionObserver" in window
      ? new IntersectionObserver((entries) => entries.forEach((entry) => {
          if (entry.isIntersecting) loadIframe(entry.target, lazyIframes);
        }), { root: null, rootMargin: "200px 0px", threshold: 0.2 })
      : null;

    // Fijar base inmediatamente (sin relajar contenedores) y observar
    lazyIframes.forEach((ifr) => {
      primeBaseHeight(ifr, getBaseForIframe(ifr) || BASE);
      io?.observe(ifr);
    });

    // Precarga del siguiente + ajustes de título
    root.addEventListener("slide.bs.carousel", (ev) => {
      const to = ev.to ?? 0;
      const items = root.querySelectorAll(".carousel-item");
      [items[to], items[(to + 1) % items.length]].forEach((slide) => {
        const ifr = slide?.querySelector('iframe.review-embed[data-src]');
        if (ifr) loadIframe(ifr, lazyIframes);
        slide?.querySelectorAll?.(".hero-card").forEach(adjustTitleLayoutFor);
      });
      setTimeout(() => pingVisibleIframes(root, targetOrigin), 120);
    });

    // Al terminar el slide, si el iframe del slide activo tiene altura guardada, aplicarla
    root.addEventListener("slid.bs.carousel", (ev) => {
      const active = root.querySelector(".carousel-item.active iframe.review-embed[data-uid]");
      if (!active) return;
      const uid = active.dataset.uid;
      const base = getBaseForIframe(active) || BASE;
      const h = EMBED_STATE.get(uid) || base;
      applyEmbedHeight(active, Math.max(MIN_H, Math.min(MAX_H, h)));
    });

    // Arranque
    setTimeout(() => {
      lazyIframes.forEach((ifr) => loadIframe(ifr, lazyIframes));
      pingVisibleIframes(root, targetOrigin);
    }, 2500);
    pingVisibleIframes(root, targetOrigin);

    // Ajustes iniciales
    root.querySelectorAll(".hero-card").forEach(adjustTitleLayoutFor);
    if (document.fonts?.ready) document.fonts.ready.then(() => root.querySelectorAll(".hero-card").forEach(adjustTitleLayoutFor));
  }

  function initAll() { document.querySelectorAll(".reviews-block, .home-hero").forEach(initOne); }

  if (document.readyState === "loading") { document.addEventListener("DOMContentLoaded", initAll); } else { initAll(); }

  // Reinit si se inyecta HTML dinámicamente
  if ("MutationObserver" in window) {
    const mo = new MutationObserver((muts) => {
      muts.forEach((m) => {
        m.addedNodes && Array.from(m.addedNodes).forEach((n) => {
          if (!(n instanceof HTMLElement)) return;
          if (n.matches?.(".reviews-block, .home-hero")) initOne(n);
          n.querySelectorAll?.(".reviews-block, .home-hero").forEach(initOne);
        });
      });
    });
    mo.observe(document.documentElement || document.body, { childList: true, subtree: true });
  }
})();
