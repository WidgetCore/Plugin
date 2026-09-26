(function () {
  'use strict';

  var SELECTOR = '[data-wgcr-search]';
  var DEBOUNCE = 250;
  var cfg = window.WGCRSearch || {};
  var MIN = parseInt(cfg.minChars, 10) || 2;
  var MAX = parseInt(cfg.maxResults, 10) || 50;
  var PAGINATIONS = ['numbers', 'prev_next', 'load_on_click', 'load_on_scroll'];

  function esc(str) {
    return String(str == null ? '' : str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function safeUrl(url) {
    url = String(url || '');
    return /^(https?:)?\/\//i.test(url) || url.charAt(0) === '/' ? url : '';
  }

  function debounce(fn, wait) {
    var t;
    return function () {
      var ctx = this, args = arguments;
      clearTimeout(t);
      t = setTimeout(function () { fn.apply(ctx, args); }, wait);
    };
  }

  function t(key, fallback) {
    var c = window.WGCRSearch || cfg;
    return (c.i18n && c.i18n[key]) ? String(c.i18n[key]) : fallback;
  }

  function restUrl() {
    var c = window.WGCRSearch || cfg;
    return c.restUrl || '/wp-json/wgcr/v1/search';
  }

  function homeUrl(form) {
    var c = window.WGCRSearch || cfg;
    return (form && form.getAttribute('action')) || c.homeUrl || '/';
  }

  function parseQuery(root) {
    var out = {};
    try { out = JSON.parse(root.getAttribute('data-query') || '{}') || {}; } catch (err) { out = {}; }
    return out;
  }

  function queryParams(q) {
    var p = '';
    if (q.orderby && q.orderby !== 'relevance') {
      p += '&orderby=' + encodeURIComponent(q.orderby) + '&order=' + encodeURIComponent(q.order === 'ASC' ? 'ASC' : 'DESC');
    }
    if (q.date && q.date !== 'all') { p += '&date=' + encodeURIComponent(q.date); }
    if (q.terms) { p += '&terms=' + encodeURIComponent(q.terms) + '&terms_op=' + encodeURIComponent(q.terms_op === 'include' ? 'include' : 'exclude'); }
    if (String(q.ignore_sticky) === '0') { p += '&ignore_sticky=0'; }
    if (q.query_id) { p += '&query_id=' + encodeURIComponent(q.query_id); }
    return p;
  }

  var DOC_ICON = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/></svg>';

  function hoistLink(markup) {
    var m = markup.match(/\bhref\s*=\s*("([^"]*)"|'([^']*)')/i);
    var href = m ? (m[2] != null ? m[2] : m[3]) : '';
    if (!safeUrl(href)) { return; }
    var links = document.querySelectorAll('link[rel="stylesheet"]');
    for (var i = 0; i < links.length; i++) {
      if (links[i].getAttribute('href') === href) { return; }
    }
    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = href;
    (document.head || document.documentElement).appendChild(link);
  }

  function tplAssets(html, bits) {
    return String(html == null ? '' : html)
      .replace(/<style[^>]*>([\s\S]*?)<\/style>/gi, function (m, css) { bits[css.replace(/\s+/g, ' ')] = css; return ''; })
      .replace(/<link\b[^>]*>/gi, function (m) { if (/stylesheet/i.test(m)) { hoistLink(m); } return ''; });
  }

  function tplHasContent(html) {
    return /<(img|svg|picture|video|iframe|canvas)\b/i.test(html) ||
      html.replace(/<[^>]*>/g, '').replace(/&[a-z#0-9]+;/gi, ' ').replace(/\s+/g, ' ').trim().length > 0;
  }

  function pageWindow(page, pages) {
    var out = [];
    var last = 0;
    for (var p = 1; p <= pages; p++) {
      if (p === 1 || p === pages || Math.abs(p - page) <= 2) {
        if (last && p - last > 1) { out.push(0); }
        out.push(p);
        last = p;
      }
    }
    return out;
  }

  function bind(root) {
    if (!root || root.getAttribute('data-wgcr-search-init') === '1') { return; }
    var input = root.querySelector('.wgcr-search-input');
    var panel = root.querySelector('.wgcr-search-panel');
    var list = root.querySelector('.wgcr-search-list');
    var status = root.querySelector('.wgcr-search-status');
    var allLink = root.querySelector('.wgcr-search-all');
    var form = root.querySelector('.wgcr-search-form');
    var pager = root.querySelector('.wgcr-search-pagination');
    var more = root.querySelector('.wgcr-search-more');
    var nomore = root.querySelector('.wgcr-search-nomore');
    if (!input || !panel || !list) { return; }
    root.setAttribute('data-wgcr-search-init', '1');

    var source = root.getAttribute('data-source') || 'post';
    var limit = Math.max(1, Math.min(MAX, parseInt(root.getAttribute('data-limit'), 10) || 5));
    var showThumb = root.getAttribute('data-thumb') === 'yes';
    var showExcerpt = root.getAttribute('data-excerpt') === 'yes';
    var allText = root.getAttribute('data-all') || '';
    var tpl = root.getAttribute('data-template') || '';
    var displayMode = (root.getAttribute('data-display-mode') || 'modal').toLowerCase() === 'page' ? 'page' : 'modal';
    var pagination = root.getAttribute('data-pagination') || 'none';
    if (PAGINATIONS.indexOf(pagination) === -1) { pagination = 'none'; }
    var extra = queryParams(parseQuery(root));
    if (allLink && allText) { allLink.textContent = allText; }
    var active = -1;
    var controller = null;
    var seq = 0;
    var listId = list.id;
    var page = 1;
    var pages = 1;
    var total = 0;
    var lastQ = '';
    var observer = null;
    var tplBits = {};
    var refocus = '';

    function options() { return list.querySelectorAll('[role="option"]'); }

    function syncTplStyles(bits) {
      var css = '';
      for (var k in bits) { if (Object.prototype.hasOwnProperty.call(bits, k)) { css += bits[k] + '\n'; } }
      var el = panel.querySelector('style.wgcr-search-tpl-style');
      if (!css) {
        if (el && el.parentNode) { el.parentNode.removeChild(el); }
        return;
      }
      if (!el) { el = document.createElement('style'); el.className = 'wgcr-search-tpl-style'; }
      el.textContent = css;
      if (!el.parentNode) { panel.insertBefore(el, panel.firstChild); }
    }

    function initTplContent() {
      var hidden = list.querySelectorAll('.elementor-invisible');
      for (var i = 0; i < hidden.length; i++) { hidden[i].classList.remove('elementor-invisible'); }
      var fe = window.elementorFrontend;
      if (fe && fe.elementsHandler && typeof fe.elementsHandler.runReadyTrigger === 'function') {
        var els = list.querySelectorAll('[data-element_type]');
        for (var j = 0; j < els.length; j++) {
          try { fe.elementsHandler.runReadyTrigger(els[j]); } catch (err) {}
        }
      }
    }

    function open() {
      panel.hidden = false;
      input.setAttribute('aria-expanded', 'true');
    }
    function close() {
      if (displayMode === 'page') { return; }
      panel.hidden = true;
      input.setAttribute('aria-expanded', 'false');
      input.removeAttribute('aria-activedescendant');
      setActive(-1);
    }
    function setLoading(on) {
      root.classList.toggle('is-loading', !!on);
      input.setAttribute('aria-busy', on ? 'true' : 'false');
    }
    function setActive(i) {
      var opts = options();
      active = i;
      for (var k = 0; k < opts.length; k++) {
        opts[k].setAttribute('aria-selected', k === i ? 'true' : 'false');
      }
      if (i >= 0 && opts[i]) {
        input.setAttribute('aria-activedescendant', opts[i].id);
        if (opts[i].scrollIntoView) { opts[i].scrollIntoView({ block: 'nearest' }); }
      } else {
        input.removeAttribute('aria-activedescendant');
      }
    }
    function optionUrl(li) {
      var href = li.getAttribute('data-href') || '';
      if (!href) {
        var a = li.querySelector('a.wgcr-search-link');
        href = a ? a.href : '';
      }
      return href && href !== '#' && !/#$/.test(href) ? href : '';
    }
    function allUrl(q) {
      var base = homeUrl(form);
      var sep = base.indexOf('?') === -1 ? '?' : '&';
      return base + sep + 's=' + encodeURIComponent(q) + '&post_type=' + encodeURIComponent(source);
    }

    function resetPager() {
      if (pager) { pager.innerHTML = ''; pager.hidden = true; }
      if (more) { more.hidden = true; }
      if (nomore) { nomore.hidden = true; }
      watchScroll(false);
    }

    function watchScroll(on) {
      var sentinel = list.querySelector('.wgcr-search-sentinel');
      if (observer) { observer.disconnect(); observer = null; }
      if (sentinel && sentinel.parentNode) { sentinel.parentNode.removeChild(sentinel); }
      if (!on || pagination !== 'load_on_scroll' || typeof IntersectionObserver === 'undefined') { return; }
      sentinel = document.createElement('li');
      sentinel.className = 'wgcr-search-sentinel';
      sentinel.setAttribute('role', 'presentation');
      sentinel.setAttribute('aria-hidden', 'true');
      list.appendChild(sentinel);
      observer = new IntersectionObserver(function (entries) {
        for (var i = 0; i < entries.length; i++) {
          if (entries[i].isIntersecting) { loadMore(); return; }
        }
      }, { rootMargin: '0px 0px 120px 0px' });
      observer.observe(sentinel);
    }

    function pagerHtml() {
      var h = '<button type="button" class="wgcr-search-page wgcr-search-page--prev" data-page="' + (page - 1) + '"' + (page <= 1 ? ' disabled' : '') + '>' + esc(t('prev', 'قبلی')) + '</button>';
      if (pagination === 'numbers') {
        var win = pageWindow(page, pages);
        for (var i = 0; i < win.length; i++) {
          var p = win[i];
          if (!p) { h += '<span class="wgcr-search-page wgcr-search-page--gap" aria-hidden="true">…</span>'; continue; }
          h += '<button type="button" class="wgcr-search-page" data-page="' + p + '"' + (p === page ? ' aria-current="page"' : '') +
            ' aria-label="' + esc(t('page', 'صفحه %d').replace('%d', String(p))) + '">' + p + '</button>';
        }
      } else {
        h += '<span class="wgcr-search-page wgcr-search-page--info" aria-current="page">' + esc(t('pageOf', 'صفحه %1$d از %2$d').replace('%1$d', String(page)).replace('%2$d', String(pages))) + '</span>';
      }
      h += '<button type="button" class="wgcr-search-page wgcr-search-page--next" data-page="' + (page + 1) + '"' + (page >= pages ? ' disabled' : '') + '>' + esc(t('next', 'بعدی')) + '</button>';
      return h;
    }

    function renderPager() {
      if (pagination === 'none') { return; }
      var hasMore = page < pages;
      if (pager) {
        var show = (pagination === 'numbers' || pagination === 'prev_next') && pages > 1;
        pager.innerHTML = show ? pagerHtml() : '';
        pager.hidden = !show;
      }
      if (more) {
        more.hidden = !(pagination === 'load_on_click' && hasMore);
        if (more.hidden && document.activeElement === more) { input.focus(); }
      }
      if (nomore) { nomore.hidden = !((pagination === 'load_on_click' || pagination === 'load_on_scroll') && !hasMore && page > 1); }
      if (refocus && pager && !pager.hidden) {
        var target = pager.querySelector(refocus);
        if (!target || target.disabled) { target = pager.querySelector('button[aria-current="page"]'); }
        if (target && target.focus) { target.focus(); }
      }
      refocus = '';
      watchScroll(hasMore);
    }

    function itemHtml(it, idx, bits) {
      var href = safeUrl(it.link) || '#';
      var thumb = '';
      if (showThumb) {
        thumb = safeUrl(it.thumb)
          ? '<img class="wgcr-search-thumb" src="' + esc(safeUrl(it.thumb)) + '" alt="" loading="lazy">'
          : '<span class="wgcr-search-thumb wgcr-search-thumb--empty">' + DOC_ICON + '</span>';
      }
      var tplHtml = it.html ? tplAssets(it.html, bits) : '';
      var id = esc(listId + '-opt-' + idx);
      if (tplHtml && tplHasContent(tplHtml)) {
        return '<li class="wgcr-search-item wgcr-search-item--tpl" role="option" id="' + id + '" aria-selected="false" data-href="' + esc(href) + '">' + tplHtml + '</li>';
      }
      return '<li class="wgcr-search-item" role="option" id="' + id + '" aria-selected="false">' +
        '<a class="wgcr-search-link" href="' + esc(href) + '" tabindex="-1">' + thumb +
        '<span class="wgcr-search-body"><span class="wgcr-search-title">' + esc(it.title) + '</span>' +
        (showExcerpt && it.excerpt ? '<span class="wgcr-search-excerpt">' + esc(it.excerpt) + '</span>' : '') +
        (it.date ? '<span class="wgcr-search-date">' + esc(it.date) + '</span>' : '') +
        '</span></a></li>';
    }

    function render(items, q, append) {
      var offset = append ? options().length : 0;
      if (!append) {
        active = -1;
        input.removeAttribute('aria-activedescendant');
      }
      var bits = {};
      var html = '';
      for (var i = 0; i < items.length; i++) { html += itemHtml(items[i] || {}, offset + i, bits); }
      if (append) {
        watchScroll(false);
        list.insertAdjacentHTML('beforeend', html);
        for (var k in bits) { if (Object.prototype.hasOwnProperty.call(bits, k)) { tplBits[k] = bits[k]; } }
      } else {
        list.innerHTML = html;
        list.scrollTop = 0;
        tplBits = items.length ? bits : {};
      }
      var count = options().length;
      if (!count) {
        status.textContent = t('noResults', 'نتیجه‌ای برای «%s» پیدا نشد.').replace('%s', q);
      } else {
        status.textContent = t('found', 'تعداد نتایج: %d').replace('%d', String(Math.max(count, total)));
      }
      syncTplStyles(tplBits);
      initTplContent();
      if (allLink) {
        allLink.href = allUrl(q);
        allLink.hidden = !count;
      }
      renderPager();
      open();
    }

    function search(q, pg, append) {
      var my = ++seq;
      if (controller && controller.abort) { controller.abort(); }
      controller = typeof AbortController !== 'undefined' ? new AbortController() : null;
      var url = restUrl();
      url += (url.indexOf('?') === -1 ? '?' : '&') +
        'q=' + encodeURIComponent(q) + '&type=' + encodeURIComponent(source) + '&limit=' + limit + (tpl ? '&template=' + encodeURIComponent(tpl) : '') +
        (pagination !== 'none' ? '&page=' + pg : '') + extra;
      setLoading(true);
      fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json' }, signal: controller ? controller.signal : undefined })
        .then(function (r) {
          if (!r.ok) { throw new Error('HTTP ' + r.status); }
          var h = r.headers && typeof r.headers.get === 'function' ? r.headers : null;
          var tp = h ? parseInt(h.get('X-WP-TotalPages'), 10) : 0;
          var tt = h ? parseInt(h.get('X-WP-Total'), 10) : 0;
          return r.json().then(function (data) { return { data: data, pages: tp, total: tt }; });
        })
        .then(function (res) {
          if (my !== seq) { return; }
          var data = Array.isArray(res.data) ? res.data : [];
          page = pg;
          pages = Math.max(1, res.pages || 0);
          total = res.total || 0;
          lastQ = q;
          render(data, q, append);
        })
        .catch(function (err) {
          if (my !== seq || (err && err.name === 'AbortError')) { return; }
          list.innerHTML = '';
          tplBits = {};
          syncTplStyles({});
          resetPager();
          status.textContent = t('error', 'خطا در دریافت نتایج. دوباره تلاش کنید.');
          if (allLink) { allLink.hidden = true; }
          open();
        })
        .then(function () { if (my === seq) { setLoading(false); } });
    }

    function loadMore() {
      if (!lastQ || page >= pages || root.classList.contains('is-loading')) { return; }
      search(lastQ, page + 1, true);
    }

    function goTo(pg) {
      if (!lastQ || pg < 1 || pg > pages || pg === page) { return; }
      search(lastQ, pg, false);
    }

    var run = debounce(function () {
      var q = input.value.trim();
      if (q.length < MIN) {
        seq++;
        if (controller && controller.abort) { controller.abort(); }
        setLoading(false);
        list.innerHTML = '';
        tplBits = {};
        syncTplStyles({});
        resetPager();
        lastQ = '';
        status.textContent = '';
        if (displayMode === 'page') {
          if (allLink) { allLink.hidden = true; }
          input.setAttribute('aria-expanded', 'true');
        } else {
          close();
        }
        return;
      }
      search(q, 1, false);
    }, DEBOUNCE);

    input.addEventListener('input', run);
    input.addEventListener('focus', function () {
      if (displayMode === 'page') {
        if (input.value.trim().length >= MIN) { open(); }
        return;
      }
      if (input.value.trim().length >= MIN && (list.children.length || status.textContent)) { open(); }
    });
    input.addEventListener('keydown', function (e) {
      var opts = options();
      var key = e.key;
      if (key === 'ArrowDown' || key === 'Down') {
        if (!opts.length) { return; }
        e.preventDefault();
        open();
        setActive(active < opts.length - 1 ? active + 1 : 0);
      } else if (key === 'ArrowUp' || key === 'Up') {
        if (!opts.length) { return; }
        e.preventDefault();
        open();
        setActive(active > 0 ? active - 1 : opts.length - 1);
      } else if (key === 'Enter') {
        if (!panel.hidden && active >= 0 && opts[active]) {
          var url = optionUrl(opts[active]);
          if (url) {
            e.preventDefault();
            window.location.href = url;
          }
        }
      } else if (key === 'Escape' || key === 'Esc') {
        if (displayMode === 'page') {
          if (input.value) { e.preventDefault(); input.value = ''; run(); }
          return;
        }
        if (!panel.hidden) { e.preventDefault(); close(); }
        else if (input.value) { input.value = ''; }
      }
    });
    list.addEventListener('mousemove', function (e) {
      var li = e.target && e.target.closest ? e.target.closest('[role="option"]') : null;
      if (!li || tpl || li.classList.contains('wgcr-search-item--tpl')) { return; }
      var opts = options();
      for (var i = 0; i < opts.length; i++) { if (opts[i] === li && i !== active) { setActive(i); } }
    });
    list.addEventListener('click', function (e) {
      var li = e.target && e.target.closest ? e.target.closest('.wgcr-search-item--tpl') : null;
      if (!li || li.querySelector('a[href]') || e.target.closest('button,input,select,textarea,label')) { return; }
      var url = optionUrl(li);
      if (url) { window.location.href = url; }
    });
    if (pager) {
      pager.addEventListener('click', function (e) {
        var btn = e.target && e.target.closest ? e.target.closest('.wgcr-search-page[data-page]') : null;
        if (!btn || btn.disabled) { return; }
        e.preventDefault();
        var pg = parseInt(btn.getAttribute('data-page'), 10) || 1;
        if (pg === page) { return; }
        refocus = btn.classList.contains('wgcr-search-page--prev') ? '.wgcr-search-page--prev' : (btn.classList.contains('wgcr-search-page--next') ? '.wgcr-search-page--next' : 'button[aria-current="page"]');
        goTo(pg);
      });
    }
    if (more) {
      more.addEventListener('click', function (e) { e.preventDefault(); loadMore(); });
    }
    document.addEventListener('click', function (e) {
      if (displayMode === 'page') { return; }
      if (!root.contains(e.target)) { close(); }
    });
    root.addEventListener('focusout', function (e) {
      if (displayMode === 'page') { return; }
      if (e.relatedTarget && !root.contains(e.relatedTarget)) { close(); }
    });
  }

  function initAll(ctx) {
    var scope = ctx && ctx.querySelectorAll ? ctx : document;
    if (scope.matches && scope.matches(SELECTOR)) { bind(scope); }
    var nodes = scope.querySelectorAll(SELECTOR);
    for (var i = 0; i < nodes.length; i++) { bind(nodes[i]); }
  }

  function lazy(e) {
    var t = e.target;
    var root = t && t.closest ? t.closest(SELECTOR) : null;
    if (root && root.getAttribute('data-wgcr-search-init') !== '1') {
      bind(root);
    }
  }
  document.addEventListener('focusin', lazy, true);
  document.addEventListener('input', lazy, true);

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { initAll(document); });
  } else {
    initAll(document);
  }

  function hookElementor() {
    if (window.elementorFrontend && window.elementorFrontend.hooks) {
      window.elementorFrontend.hooks.addAction('frontend/element_ready/wgcr-search.default', function ($scope) {
        initAll($scope && $scope[0] ? $scope[0] : document);
      });
      return true;
    }
    return false;
  }
  if (!hookElementor()) {
    window.addEventListener('load', function () { hookElementor(); initAll(document); });
  }
})();
