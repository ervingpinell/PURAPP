window.__REVIEWS_PROMISES__ = window.__REVIEWS_PROMISES__ || {};
window.__REVIEWS_CACHE__    = window.__REVIEWS_CACHE__    || {};
window.__REVIEWS_TS__       = window.__REVIEWS_TS__       || {};

function isFresh(code, ttlMs) {
  if (!ttlMs) return !!window.__REVIEWS_CACHE__[code];
  const ts = window.__REVIEWS_TS__[code] || 0;
  return Date.now() - ts < ttlMs;
}


function filterHighRated(reviews = []) {
  return reviews.filter(r => Number(r?.rating) >= 4);
}

/**
 * GET cacheable + memoria con TTL opcional
 * opts: { count, start, provider, sortBy, ttlMs, force }
 */
export function fetchReviewsOnce(code, opts = {}) {
  if (!code) return Promise.resolve({ reviews: [] });

  const { ttlMs = 0, force = false, ...params } = opts;

  // 1) cache en memoria válido
  if (!force && isFresh(code, ttlMs)) {
    return Promise.resolve(window.__REVIEWS_CACHE__[code]);
  }

  // 2) evita duplicar la misma petición
  if (window.__REVIEWS_PROMISES__[code]) {
    return window.__REVIEWS_PROMISES__[code];
  }

  const qs = new URLSearchParams({
    count: 5,
    start: 1,
    provider: 'ALL',
    sortBy: 'MOST_RECENT',
    ...params,
  });

  const p = fetch(`/api/reviews/${encodeURIComponent(code)}?` + qs.toString(), {
    headers: { 'Accept': 'application/json' },
  })
    .then(r => (r.ok ? r.json() : Promise.reject(r)))
    .then(data => {
      // Aplica filtro justo al recibir la respuesta
      if (Array.isArray(data?.reviews)) {
        data.reviews = filterHighRated(data.reviews);
      } else {
        data = { reviews: [] };
      }
      window.__REVIEWS_CACHE__[code] = data;      // guarda payload filtrado
      window.__REVIEWS_TS__[code]    = Date.now(); // marca fresco
      return data;
    })
    .finally(() => { delete window.__REVIEWS_PROMISES__[code]; });

  window.__REVIEWS_PROMISES__[code] = p;
  return p;
}

/**
 * POST batch + memoria con TTL opcional
 * opts: { count, start, provider, sortBy, ttlMs, force }
 * Devuelve: { results: { CODE: payloadFiltrado } }
 */
export function fetchReviewsBatch(productCodes = [], opts = {}) {
  if (!Array.isArray(productCodes) || productCodes.length === 0) {
    return Promise.resolve({ results: {} });
  }

  const { ttlMs = 0, force = false, ...params } = opts;

  const known = {};
  const missing = [];
  for (const code of productCodes) {
    if (!force && isFresh(code, ttlMs)) {
      known[code] = window.__REVIEWS_CACHE__[code];
    } else {
      missing.push(code);
    }
  }

  if (missing.length === 0) {
    return Promise.resolve({ results: known });
  }

  const body = {
    productCodes: missing,
    count: params.count ?? 5,
    start: params.start ?? 1,
    provider: params.provider ?? 'ALL',
    sortBy: params.sortBy ?? 'MOST_RECENT',
  };

  const cacheKey = '__BATCH__' + missing.sort().join(',');
  if (window.__REVIEWS_PROMISES__[cacheKey]) {
    return window.__REVIEWS_PROMISES__[cacheKey].then(() => ({
      results: { ...known, ...Object.fromEntries(missing.map(c => [c, window.__REVIEWS_CACHE__[c]])) }
    }));
  }

  const p = fetch('/api/reviews/batch', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body: JSON.stringify(body),
  })
    .then(r => (r.ok ? r.json() : Promise.reject(r)))
    .then(payload => {
      const results = payload?.results || {};
      for (const code of Object.keys(results)) {
        const data = results[code] || {};
        if (Array.isArray(data?.reviews)) {
          data.reviews = filterHighRated(data.reviews);
        } else {
          results[code] = { reviews: [] };
        }
        window.__REVIEWS_CACHE__[code] = results[code];
        window.__REVIEWS_TS__[code]    = Date.now();
      }
      return { results: { ...known, ...results } };
    })
    .finally(() => { delete window.__REVIEWS_PROMISES__[cacheKey]; });

  window.__REVIEWS_PROMISES__[cacheKey] = p;
  return p;
}
