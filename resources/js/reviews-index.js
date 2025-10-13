(function () {
  "use strict";

  const root = document.getElementById("reviews-page") || document.body;
  const TXT = {
    more:       root?.dataset.more      || "Ver más",
    less:       root?.dataset.less      || "Mostrar menos",
    by:         root?.dataset.by        || "Proporcionado por",
    swalTitle:  root?.dataset.swalTitle || "¿Abrir tour?",
    swalText:   root?.dataset.swalText  || "Estás a punto de abrir la página del tour",
    swalOK:     root?.dataset.swalOk    || "Abrir ahora",
    swalCancel: root?.dataset.swalCancel|| "Cancelar",
  };

  /* -------- Títulos iguales -------- */
  function equalizeReviewTitles() {
    const titles = Array.from(document.querySelectorAll(".review-title"));
    if (!titles.length) return;
    titles.forEach((t) => (t.style.height = "auto"));
    let max = 0;
    titles.forEach((t) => (max = Math.max(max, t.offsetHeight)));
    titles.forEach((t) => (t.style.height = max + "px"));
  }
  function runEqualizeOnceReady() {
    if (document.fonts?.ready) {
      document.fonts.ready.then(() => requestAnimationFrame(equalizeReviewTitles));
    } else {
      window.addEventListener("load", () => requestAnimationFrame(equalizeReviewTitles), { once: true });
    }
  }
  let _resizeTimer;
  window.addEventListener("resize", () => {
    clearTimeout(_resizeTimer);
    _resizeTimer = setTimeout(() => { equalizeReviewTitles(); adjustAllTitles(); }, 150);
  });

  /* -------- Utils -------- */
  function getProvLabelFromSlide(slide) {
    return slide.getAttribute("data-provider-label") || slide.getAttribute("data-prov-label") || "—";
  }

  // clamp helper
  function needsTruncate(textEl) {
    if (!textEl) return false;
    const clone = textEl.cloneNode(true);
    const cs = getComputedStyle(textEl);
    clone.style.visibility = "hidden";
    clone.style.position = "absolute";
    clone.style.pointerEvents = "none";
    clone.style.height = "auto";
    clone.style.maxHeight = "none";
    clone.style.webkitLineClamp = "unset";
    clone.style.overflow = "visible";
    clone.style.display = "block";
    clone.style.whiteSpace = cs.whiteSpace;
    clone.style.lineHeight = cs.lineHeight;
    clone.style.fontSize = cs.fontSize;
    clone.style.width = textEl.clientWidth + "px";
    clone.classList.remove("expanded");
    document.body.appendChild(clone);
    const full = clone.scrollHeight;
    document.body.removeChild(clone);
    const visible = textEl.clientHeight;
    return full > visible + 1;
  }

  function ensureReadMore(slideEl) {
    const content = slideEl.querySelector(".review-content");
    if (!content) return;

    const needs = needsTruncate(content);
    let btn = slideEl.querySelector(".review-toggle");

    if (needs) {
      if (!btn) {
        btn = document.createElement("button");
        btn.type = "button";
        btn.className = "review-toggle";
        btn.textContent = TXT.more;
        content.insertAdjacentElement("afterend", btn);

        btn.addEventListener("click", function () {
          const expanded = !content.classList.contains("expanded");
          content.classList.toggle("expanded", expanded);
          const card = slideEl.closest(".review-card");
          if (card) card.classList.toggle("expanded-card", expanded);
          btn.textContent = expanded ? TXT.less : TXT.more;
          adjustTitleLayoutFor(card || slideEl);
        });
      }
    } else {
      if (btn) btn.remove();
      content.classList.remove("expanded");
      const card = slideEl.closest(".review-card");
      if (card) card.classList.remove("expanded-card");
    }
  }

  /* -------- Ajuste anti-overlap para tarjetas locales -------- */
  function adjustTitleLayoutFor(scope) {
    const card  = (scope && (scope.closest?.(".hero-card") || scope)) || document;
    const title = card.querySelector?.(".tour-title-abs");
    if (!title) return;
    const who = card.querySelector?.(".who-when") || card.querySelector?.(".review-head") || card.querySelector?.(".review-meta");

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

  function adjustAllTitles() {
    document.querySelectorAll(".hero-card, .review-item").forEach(adjustTitleLayoutFor);
  }

  /* -------- Iframes -------- */
  function _setShellAndIframeHeight(ifr, shell, px) {
    const newH = Math.max(120, Number(px) || 0);
    const curH = Number(ifr.style.height?.replace("px", "")) || 0;
    if (Math.abs(newH - curH) < 4) return;
    if (shell) shell.style.setProperty("--h", newH + "px");
    ifr.style.height = newH + "px";
  }

  function mountIframe(ifr) {
    if (!ifr || ifr.dataset.mounted === "1") return;
    const rawAttr = (ifr.getAttribute("data-src") || "").trim();
    const src = rawAttr ? rawAttr.replace(/&amp;/g, "&").replace(/\s+/g, "") : "";
    if (src) ifr.src = src;
    ifr.dataset.mounted = "1";
    if (!ifr.dataset.uid) ifr.dataset.uid = "u" + Math.random().toString(36).slice(2, 10);
    if (!ifr.classList.contains("review-embed")) ifr.classList.add("review-embed");

    const shell = ifr.closest(".iframe-shell");
    const skeleton = shell ? shell.querySelector(".iframe-skeleton") : null;

    ifr.addEventListener("load", function(){
      if (skeleton) skeleton.classList.add("is-hidden");
      ifr.setAttribute("data-ready","1");
      try { ifr.contentWindow?.postMessage({ type:"PING_HEIGHT", uid:ifr.dataset.uid }, "*"); } catch(e){}
      setTimeout(() => { try { ifr.contentWindow?.postMessage({ type:"PING_HEIGHT", uid:ifr.dataset.uid }, "*"); } catch(e){} }, 250);
    }, { once:true });
  }

  function observeAndMount() {
    const iframes = document.querySelectorAll("iframe.review-iframe");
    if (!iframes.length) return;
    const vh = window.innerHeight || document.documentElement.clientHeight;

    iframes.forEach((ifr) => {
      const rect = ifr.getBoundingClientRect();
      const inView = rect.top < vh && rect.bottom > 0 && getComputedStyle(ifr).display !== "none";
      if (inView) mountIframe(ifr);
    });

    if (!("IntersectionObserver" in window)) { iframes.forEach(mountIframe); return; }
    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry) => { if (entry.isIntersecting) { mountIframe(entry.target); io.unobserve(entry.target); } });
    }, { root:null, rootMargin:"200px", threshold:0.01 });
    iframes.forEach((ifr) => io.observe(ifr));
  }

  window.addEventListener("message", function onMessage(e){
    const d = e.data || {};
    if (!d) return;

    const ifr = Array.from(document.querySelectorAll("iframe.review-iframe"))
      .find(el => { try { return el.contentWindow === e.source; } catch(_) { return false; } });
    if (!ifr) return;

    const shell = ifr.closest(".iframe-shell");

    if (d.type === "REVIEW_IFRAME_RESIZE" && typeof d.height === "number") {
      _setShellAndIframeHeight(ifr, shell, d.height);
      const sk = shell ? shell.querySelector(".iframe-skeleton") : null;
      if (sk) sk.classList.add("is-hidden");
      ifr.setAttribute("data-ready","1");
    }

    if (d.type === "REVIEW_IFRAME_READY") {
      const sk = shell ? shell.querySelector(".iframe-skeleton") : null;
      if (sk) sk.classList.add("is-hidden");
      ifr.setAttribute("data-ready","1");
    }
  }, false);

  function advanceIframe(ifr, delta) {
    if (!ifr) return;
    const limit = Math.max(1, parseInt(ifr.dataset.limit || "0", 10) || 8);
    let nth = Math.max(1, parseInt(ifr.dataset.nth || "1", 10) || 1);
    nth = ((nth - 1 + delta) % limit + limit) % limit + 1;
    const base = (ifr.getAttribute("data-src") || ifr.src || "").replace(/&amp;/g, "&").trim();
    if (!base) return;
    const url = new URL(base, window.location.origin);
    url.searchParams.set("nth", String(nth));
    url.searchParams.set("uid", "u" + Math.random().toString(36).slice(2, 10));
    url.searchParams.set("r", Date.now().toString(36));
    const next = url.pathname + "?" + url.searchParams.toString();
    ifr.setAttribute("data-src", next);
    ifr.dataset.nth = String(nth);
    ifr.dataset.mounted = "0";
    ifr.removeAttribute("src");
    mountIframe(ifr);
  }

  /* ======== Indicador segmentado por tarjeta ======== */
  function buildSegments(host, total, onJump) {
    // evita duplicar
    let bar = host.querySelector(":scope > .carousel-segments");
    if (bar) return { bar, setActive: (i)=>{ bar.querySelectorAll(".carousel-segment").forEach((s,idx)=>s.classList.toggle("is-active", idx===i)); } };

    bar = document.createElement("div");
    bar.className = "carousel-segments";
    bar.setAttribute("role", "tablist");
    bar.setAttribute("aria-label", "Indicador de reseñas");

    const segs = [];
    for (let i = 0; i < total; i++) {
      const s = document.createElement("button");
      s.type = "button";
      s.className = "carousel-segment";
      s.dataset.index = String(i);
      s.setAttribute("role", "tab");
      s.setAttribute("aria-label", `Slide ${i + 1} de ${total}`);
      s.addEventListener("click", () => onJump(i));
      bar.appendChild(s);
      segs.push(s);
    }

    // Insertarlo tras .js-slides y antes de .carousel-buttons-row si existe
    const slidesWrap = host.querySelector(".js-slides");
    const controls = host.querySelector(".carousel-buttons-row");
    if (slidesWrap && controls && controls.parentElement === host) {
      host.insertBefore(bar, controls);
    } else {
      host.appendChild(bar);
    }

    return {
      bar,
      setActive(i){
        segs.forEach((s,idx)=>s.classList.toggle("is-active", idx===i));
        bar.setAttribute("data-current", `${i+1}/${total}`);
      }
    };
  }

  /* -------- Carrusel simple (grid por tour) -------- */
  function initCarousel(carousel) {
    const slidesWrap = carousel.querySelector(".js-slides");
    if (!slidesWrap) return;
    const slides = Array.from(slidesWrap.querySelectorAll(".review-item"));
    if (!slides.length) return;

    const poweredEl = carousel.querySelector(".js-powered");

    // ¿modo "múltiples slides" o "un iframe que rota nth"?
    const multipleSlides = slides.length > 1;
    let idx = slides.findIndex(el => el.style.display !== "none");
    if (idx < 0) idx = 0;

    // Para iframe-only
    let iframeInfo = null;
    if (!multipleSlides) {
      const ifr = slides[0].querySelector("iframe.review-iframe");
      const limit = Math.max(1, parseInt(ifr?.dataset.limit || "8", 10) || 8);
      let nth = Math.max(1, parseInt(ifr?.dataset.nth || "1", 10) || 1);
      iframeInfo = { ifr, limit, nth };
    }

    // Segments
    const totalSegments = multipleSlides ? slides.length : (iframeInfo?.limit || 1);
    const { setActive } = buildSegments(carousel, totalSegments, (to) => {
      if (multipleSlides) {
        idx = to;
        render();
      } else if (iframeInfo) {
        iframeInfo.nth = to + 1;
        if (iframeInfo.ifr) {
          iframeInfo.ifr.dataset.nth = String(iframeInfo.nth);
          advanceIframe(iframeInfo.ifr, 0);
        }
        renderSegmentsOnly();
      }
    });

    function setPoweredFromSlide(slide) {
      if (!poweredEl || !slide) return;
      poweredEl.textContent = `${TXT.by} ${getProvLabelFromSlide(slide)}`;
    }

    function renderSegmentsOnly() {
      // actualizar segmento activo
      const activeIndex = multipleSlides ? idx : ((iframeInfo?.nth || 1) - 1);
      setActive(activeIndex);
    }

    function render() {
      if (multipleSlides) {
        slides.forEach((el, i) => (el.style.display = i === idx ? "" : "none"));
        const visible = slides[idx] || slides[0];
        setPoweredFromSlide(visible);
        ensureReadMore(visible);
        adjustTitleLayoutFor(visible);
        const ifr = visible.querySelector("iframe.review-iframe");
        if (ifr) mountIframe(ifr);
      } else {
        // solo iframe: asegurar montaje
        const ifr = iframeInfo?.ifr;
        if (ifr) mountIframe(ifr);
      }
      renderSegmentsOnly();
    }
    render();

    const tourId = carousel.dataset.tour;
    const prev = document.querySelector(`.carousel-prev[data-tour="${tourId}"]`);
    const next = document.querySelector(`.carousel-next[data-tour="${tourId}"]`);

    if (prev) prev.addEventListener("click", () => {
      if (multipleSlides) {
        idx = (idx - 1 + slides.length) % slides.length;
        render();
      } else if (iframeInfo) {
        iframeInfo.nth = ((iframeInfo.nth - 2 + iframeInfo.limit) % iframeInfo.limit) + 1;
        if (iframeInfo.ifr) {
          iframeInfo.ifr.dataset.nth = String(iframeInfo.nth);
          advanceIframe(iframeInfo.ifr, 0);
        }
        renderSegmentsOnly();
      }
    });

    if (next) next.addEventListener("click", () => {
      if (multipleSlides) {
        idx = (idx + 1) % slides.length;
        render();
      } else if (iframeInfo) {
        iframeInfo.nth = (iframeInfo.nth % iframeInfo.limit) + 1;
        if (iframeInfo.ifr) {
          iframeInfo.ifr.dataset.nth = String(iframeInfo.nth);
          advanceIframe(iframeInfo.ifr, 0);
        }
        renderSegmentsOnly();
      }
    });

    window.addEventListener("resize", () => {
      const visible = multipleSlides ? (slides[idx] || slides[0]) : slides[0];
      ensureReadMore(visible);
      adjustTitleLayoutFor(visible);
    });

    if (document.fonts?.ready) document.fonts.ready.then(() => {
      const visible = multipleSlides ? (slides[idx] || slides[0]) : slides[0];
      ensureReadMore(visible);
      adjustTitleLayoutFor(visible);
    });
  }

  /* -------- Start -------- */
  function start() {
    document.querySelectorAll(".js-carousel").forEach(initCarousel);
    observeAndMount();
    runEqualizeOnceReady();
    adjustAllTitles();
  }
  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", start); else start();

  /* -------- Confirm abrir tour -------- */
  document.addEventListener("click", function (e) {
    const a = e.target.closest("a.tour-link");
    if (!a) return;
    if (e.metaKey || e.ctrlKey || e.shiftKey || e.button === 1) return;
    e.preventDefault();
    const href = a.getAttribute("href"); if (!href || href === "#") return;
    const name = a.textContent.trim();
    if (window.Swal?.fire) {
      window.Swal.fire({
        icon:"question",
        title:TXT.swalTitle,
        html:`${TXT.swalText} <strong>${escapeHtml(name)}</strong>.`,
        showCancelButton:true,
        confirmButtonText:TXT.swalOK,
        cancelButtonText:TXT.swalCancel,
        focusConfirm:true,
      }).then((res)=>{ if(res.isConfirmed) window.location.assign(href); });
    } else {
      if (confirm(`${TXT.swalTitle}\n\n${TXT.swalText} ${name ? `"${name}"` : ""}.`)) window.location.assign(href);
    }
  });

  function escapeHtml(str) {
    return String(str)
      .replaceAll("&","&amp;").replaceAll("<","&lt;").replaceAll(">", "&gt;")
      .replaceAll('"',"&quot;").replaceAll("'","&#039;");
  }
})();
