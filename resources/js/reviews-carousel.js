/* Reviews carousel (home & tour pages)
   - Soporta múltiples carruseles por página (.reviews-block y .home-hero)
   - i18n: lee primero data-* del root; si faltan, usa window.REVIEWS_I18N; luego defaults
   - Lazy load de iframes (data-src)
   - Ajuste de altura vía postMessage desde el embed
   - Truncado/expandido del texto (respeta clamp-5/clamp-8/clamp)
   - Validación de origen en postMessage
*/
(function () {
  "use strict";

  const G = (typeof window !== "undefined" && window) || {};
  const I18N = G.REVIEWS_I18N || {};

  function toInt(v, def) {
    const n = parseInt(v, 10);
    return Number.isFinite(n) ? n : def;
  }

  function getBaseHeight(root) {
    if (root.dataset.base) return toInt(root.dataset.base, 460);
    const cssVal = getComputedStyle(root).getPropertyValue("--provider-base");
    if (cssVal) return toInt(cssVal, 460);
    return 460;
  }

  function getClampClass(el) {
    if (!el) return null;
    const cls = Array.from(el.classList).find(
      (c) => c === "clamp" || c.startsWith("clamp-")
    );
    return cls || null;
  }

  function needsTruncate(el) {
    if (!el) return false;
    const clampClass = getClampClass(el);
    if (clampClass && !el.classList.contains("expanded")) el.classList.add(clampClass);

    const clone = el.cloneNode(true);
    clone.style.visibility = "hidden";
    clone.style.position = "absolute";
    clone.style.pointerEvents = "none";
    clone.style.height = "auto";
    clone.style.maxHeight = "none";
    clone.style.webkitLineClamp = "unset";
    clone.classList.remove("expanded");
    if (clampClass) clone.classList.remove(clampClass);
    document.body.appendChild(clone);
    const full = clone.scrollHeight;
    const visible = el.clientHeight;
    document.body.removeChild(clone);
    return full > visible + 1;
  }

  function getTXT(root) {
    return {
      more:       root.dataset.more       || I18N.more      || "Leer más",
      less:       root.dataset.less       || I18N.less      || "Mostrar menos",
      by:                             I18N.by        || "Proporcionado por",
      openTitle:  root.dataset.openTitle || I18N.swalTitle  || "Abrir tour",
      openPre:    root.dataset.openPre   || I18N.swalText   || "¿Quieres ir a la página del tour seleccionado?",
      openConfirm:root.dataset.openConfirm|| I18N.swalOK    || "Sí, abrir el tour",
      openCancel: root.dataset.openCancel || I18N.swalCancel|| "Cancelar",
    };
  }

  function setupCard(card, TXT_MORE, TXT_LESS) {
    const title = card.querySelector(".tour-title-abs");
    const wrap  = card.querySelector(".review-textwrap");
    const text  = card.querySelector(".review-content");
    const btn   = card.querySelector(".review-toggle");

    if (title) card.classList.add("pad-title");
    if (!(wrap && text && btn)) return;

    const clampClass = getClampClass(text);
    if (clampClass) btn.dataset.clampClass = clampClass;

    const updateBtn = () => {
      if (text.classList.contains("expanded")) return;
      btn.style.display = needsTruncate(text) ? "inline-block" : "none";
      btn.textContent   = TXT_MORE;
      btn.style.position = "absolute";
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
        updateBtn();
      }
    };
  }

  function refreshCards(root, TXT_MORE) {
    root.querySelectorAll(".hero-card").forEach((card) => {
      const text = card.querySelector(".review-content");
      const btn  = card.querySelector(".review-toggle");
      if (text && btn && !text.classList.contains("expanded")) {
        btn.style.display = needsTruncate(text) ? "inline-block" : "none";
        btn.textContent = TXT_MORE;
        btn.style.position = "absolute";
      }
    });
  }

  function pingVisibleIframes(root, targetOrigin) {
    root.querySelectorAll("iframe.review-embed[data-uid]").forEach((ifr) => {
      try {
        ifr.contentWindow?.postMessage(
          { type: "PING_HEIGHT", uid: ifr.dataset.uid },
          targetOrigin || "*"
        );
      } catch (_) {}
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

    [ifr, shell, card, item, inner].forEach((el) => {
      if (!el) return;
      el.style.maxHeight = "none";
      el.style.overflow = "visible";
    });

    if (ifr) ifr.style.height = base + "px";
    if (shell) { shell.style.height = base + "px"; shell.style.minHeight = base + "px"; }
  }

  function initOne(root) {
    if (!root || root.__reviewsInit) return;
    root.__reviewsInit = true;

    const TXT  = getTXT(root);
    const BASE = getBaseHeight(root);

    // Orígenes permitidos
    const allowedOrigins = new Set([location.origin]);
    const targetOrigin   = location.origin;

    // 1) Setup tarjetas
    root.querySelectorAll(".hero-card").forEach((card) =>
      setupCard(card, TXT.more, TXT.less)
    );

    // 2) Redimensionar / refrescar
    const onResize = () => refreshCards(root, TXT.more);
    window.addEventListener("resize", onResize, { passive: true });
    root.addEventListener("slid.bs.carousel", onResize);

    // 3) postMessage (validando origen)
    window.addEventListener(
      "message",
      (e) => {
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
          if (sk && sk.classList.contains("iframe-skeleton")) sk.remove();
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
          if (sk && sk.classList.contains("iframe-skeleton")) sk.remove();
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
      },
      false
    );


    // 4) Lazy load de iframes
    const lazyIframes = new Set(
      Array.from(root.querySelectorAll("iframe.review-embed[data-src]"))
    );
    const io =
      "IntersectionObserver" in window
        ? new IntersectionObserver(
            (entries) => {
              entries.forEach((entry) => {
                if (entry.isIntersecting) loadIframe(entry.target, lazyIframes);
              });
            },
            { root: null, rootMargin: "200px 0px", threshold: 0.2 }
          )
        : null;

    lazyIframes.forEach((ifr) => {
      io?.observe(ifr);
      relaxHeightsFor(ifr, BASE);
    });

    // 5) Pre-carga de la siguiente slide + ping
    root.addEventListener("slide.bs.carousel", (ev) => {
      const to = ev.to ?? 0;
      const items = root.querySelectorAll(".carousel-item");
      [items[to], items[(to + 1) % items.length]].forEach((slide) => {
        const ifr = slide?.querySelector('iframe.review-embed[data-src]');
        if (ifr) loadIframe(ifr, lazyIframes);
      });
      setTimeout(() => pingVisibleIframes(root, targetOrigin), 120);
    });

    // 6) Arranque: carga perezosa diferida y primer ping
    setTimeout(() => {
      lazyIframes.forEach((ifr) => loadIframe(ifr, lazyIframes));
      pingVisibleIframes(root, targetOrigin);
    }, 2500);
    pingVisibleIframes(root, targetOrigin);
  }

  function initAll() {
    document.querySelectorAll(".reviews-block, .home-hero").forEach(initOne);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAll);
  } else {
    initAll();
  }

  // Reinit si se inyecta HTML dinámicamente
  if ("MutationObserver" in window) {
    const mo = new MutationObserver((muts) => {
      muts.forEach((m) => {
        m.addedNodes &&
          Array.from(m.addedNodes).forEach((n) => {
            if (!(n instanceof HTMLElement)) return;
            if (n.matches?.(".reviews-block, .home-hero")) initOne(n);
            n.querySelectorAll?.(".reviews-block, .home-hero").forEach(initOne);
          });
      });
    });
    mo.observe(document.documentElement || document.body, {
      childList: true,
      subtree: true,
    });
  }
})();
