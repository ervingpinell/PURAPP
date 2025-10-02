(function () {
  "use strict";

  const G = (typeof window !== "undefined" && window) || {};
  const I18N = G.REVIEWS_I18N || {};

  function toInt(v, def) { const n = parseInt(v, 10); return Number.isFinite(n) ? n : def; }

  function getBaseHeight(root) {
    if (root.dataset.base) return toInt(root.dataset.base, 460);
    const cssVal = getComputedStyle(root).getPropertyValue("--provider-base");
    if (cssVal) return toInt(cssVal, 460);
    return 460;
  }

  function getClampClass(el) {
    if (!el) return null;
    const cls = Array.from(el.classList).find((c) => c === "clamp" || c.startsWith("clamp-"));
    return cls || null;
  }

  /* === Nueva lógica robusta: contar líneas reales y comparar con el umbral (4/5) === */
  function getLineHeightPx(el){
    const cs = getComputedStyle(el);
    const lh = cs.lineHeight;
    if (lh && lh !== "normal") return parseFloat(lh);
    const fs = parseFloat(cs.fontSize) || 16;
    return fs * 1.2; // aprox para 'normal'
  }

  function naturalLineCount(el){
    if (!el) return 0;
    const clampClass = getClampClass(el);
    const hadClamp   = clampClass && el.classList.contains(clampClass);
    const wasExp     = el.classList.contains("expanded");

    // Quitar clamp/expanded para medir líneas naturales
    if (wasExp) el.classList.remove("expanded");
    if (hadClamp) el.classList.remove(clampClass);

    // Forzar reflow
    const lh = getLineHeightPx(el);
    const lines = Math.round(el.scrollHeight / Math.max(1, lh));

    // Restaurar estado
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
  /* ====================================================================== */

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

  /* ---------- Ajuste anti-overlap del título para tarjetas locales ---------- */
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
      text.classList.add(clampClass); // nos aseguramos de que el clamp esté activo visualmente
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

  function relaxHeightsFor(ifr, base) {
    const shell = ifr?.parentElement;
    const card  = ifr?.closest(".hero-card");
    const item  = ifr?.closest(".carousel-item");
    const inner = ifr?.closest(".carousel-inner");

    [ifr, shell, card, item, inner].forEach((el) => { if (!el) return; el.style.maxHeight = "none"; el.style.overflow = "visible"; });

    if (ifr) ifr.style.height = base + "px";
    if (shell) { shell.style.height = base + "px"; shell.style.minHeight = base + "px"; }
  }

  function initOne(root) {
    if (!root || root.__reviewsInit) return;
    root.__reviewsInit = true;

    const TXT  = getTXT(root);
    const BASE = getBaseHeight(root);

    const allowedOrigins = new Set([location.origin]);
    const targetOrigin   = location.origin;

    // 1) Setup tarjetas locales
    root.querySelectorAll(".hero-card").forEach((card) => setupCard(card, TXT.more, TXT.less));

    // 2) Redimensionar / refrescar
    const onResize = () => refreshCards(root, TXT.more);
    window.addEventListener("resize", onResize, { passive: true });
    root.addEventListener("slid.bs.carousel", onResize);

    // 3) postMessage (validando origen)
    window.addEventListener("message", (e) => {
      if (!e || !e.origin || !allowedOrigins.has(e.origin)) return;
      const d = e?.data || {};
      const uid = d.uid;
      if (!uid) return;

      const ifr = root.querySelector('iframe.review-embed[data-uid="' + uid + '"]');
      if (!ifr) return;
      const shell = ifr.parentElement;

      if (d.type === "REVIEW_IFRAME_READY") {
        relaxHeightsFor(ifr, BASE);
        const sk = ifr.previousElementSibling;
        if (sk && sk.classList.contains("iframe-skeleton")) sk.classList.add("is-hidden");
        ifr.setAttribute("data-ready","1");
        return;
      }

      if (d.type === "REVIEW_IFRAME_RESIZE") {
        const h = Math.max(BASE, Math.min(2000, parseInt(d.height, 10) || 0));
        relaxHeightsFor(ifr, BASE);
        ifr.style.height = h + "px";
        if (shell && shell.classList.contains("iframe-shell")) {
          shell.style.minHeight = h + "px";
          shell.style.height = h + "px";
        }
        const sk = ifr.previousElementSibling;
        if (sk && sk.classList.contains("iframe-skeleton")) sk.classList.add("is-hidden");
        ifr.setAttribute("data-ready","1");
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

    // 4) Lazy load de iframes
    const lazyIframes = new Set(Array.from(root.querySelectorAll("iframe.review-embed[data-src]")));
    const io = "IntersectionObserver" in window
      ? new IntersectionObserver((entries) => entries.forEach((entry) => { if (entry.isIntersecting) loadIframe(entry.target, lazyIframes); }),
                                 { root: null, rootMargin: "200px 0px", threshold: 0.2 })
      : null;

    lazyIframes.forEach((ifr) => { io?.observe(ifr); relaxHeightsFor(ifr, BASE); });

    // 5) Pre-carga de la siguiente slide + ping
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

    // 6) Arranque
    setTimeout(() => { lazyIframes.forEach((ifr) => loadIframe(ifr, lazyIframes)); pingVisibleIframes(root, targetOrigin); }, 2500);
    pingVisibleIframes(root, targetOrigin);
    // Ajuste inicial de títulos locales
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
