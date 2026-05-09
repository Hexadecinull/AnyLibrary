/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

const UI = (() => {

    function coverFallback(img) {
        img.src = '/assets/img/placeholder-cover.svg';
    }

    function lazyImage(img) {
        if (!img || img.dataset.lazy !== 'true') return;
        const src = img.dataset.src;
        if (!src) return;
        const tmp = new Image();
        tmp.onload = () => {
            img.src = src;
            img.classList.add('img-loaded');
        };
        tmp.onerror = () => coverFallback(img);
        tmp.src = src;
    }

    function observeImages(root = document) {
        const imgs = root.querySelectorAll('img[data-lazy="true"]');
        if (!('IntersectionObserver' in window)) {
            imgs.forEach(lazyImage);
            return;
        }
        const obs = new IntersectionObserver((entries) => {
            entries.forEach(e => { if (e.isIntersecting) { lazyImage(e.target); obs.unobserve(e.target); } });
        }, { rootMargin: '200px' });
        imgs.forEach(img => obs.observe(img));
    }

    function typeLabel(type) {
        return { book: 'Book', manga: 'Manga', audiobook: 'Audiobook', imported: 'Imported' }[type] ?? 'Book';
    }

    function authorString(authors) {
        if (!authors || !authors.length) return '';
        if (Array.isArray(authors)) return authors.slice(0, 2).join(', ');
        return String(authors);
    }

    function cardHtml(item, opts = {}) {
        const {
            id, title, authors = [], cover_url, rating, year, type = 'book',
            progress_pct, cover_id,
        } = item;

        const href   = opts.href ?? `/detail.php?id=${encodeURIComponent(id)}&type=${encodeURIComponent(type)}`;
        const cover  = cover_url || '/assets/img/placeholder-cover.svg';
        const auth   = authorString(authors);
        const pct    = parseFloat(progress_pct ?? 0);
        const isImport = type === 'imported';

        const badgeClass = { book: 'type-badge-book', manga: 'type-badge-manga', audiobook: 'type-badge-audio', imported: 'type-badge-imported' }[type] ?? 'type-badge-book';

        return `
<a class="card card-${type}" href="${href}">
    <div class="card-poster">
        <img data-lazy="true" data-src="${cover}" alt="${escHtml(title)}"
             onerror="UI.coverFallback(this)">
        ${pct > 0 ? `<div class="card-continue-progress" style="width:${Math.round(pct)}%;"></div>` : ''}
    </div>
    <div class="card-body">
        <div class="card-title" title="${escHtml(title)}">${escHtml(title)}</div>
        <div class="card-meta">
            <span class="type-badge ${badgeClass}">${typeLabel(type)}</span>
            ${auth ? `<span>${escHtml(auth)}</span>` : ''}
            ${year ? `<span>${year}</span>` : ''}
            ${rating > 0 ? `<span class="card-rating">★ ${rating.toFixed(1)}</span>` : ''}
        </div>
        ${pct > 0 ? `<div class="progress-bar"><div class="progress-fill" style="width:${Math.round(pct)}%;"></div></div>` : ''}
    </div>
</a>`.trim();
    }

    function escHtml(str) {
        return String(str ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function renderGrid(container, items, opts = {}) {
        if (!container) return;
        container.innerHTML = items.map(item => cardHtml(item, opts)).join('');
        observeImages(container);
    }

    function appendGrid(container, items, opts = {}) {
        if (!container) return;
        const frag = document.createDocumentFragment();
        items.forEach(item => {
            const div = document.createElement('div');
            div.innerHTML = cardHtml(item, opts);
            const el = div.firstElementChild;
            if (el) frag.appendChild(el);
        });
        container.appendChild(frag);
        observeImages(container);
    }

    function skeletonCards(n = 6) {
        return Array.from({ length: n }, () => `
<div class="card">
    <div class="card-poster skeleton" style="aspect-ratio:2/3;"></div>
    <div class="card-body">
        <div class="skeleton" style="height:0.75rem;width:80%;border-radius:4px;margin-bottom:0.35rem;"></div>
        <div class="skeleton" style="height:0.65rem;width:50%;border-radius:4px;"></div>
    </div>
</div>`).join('');
    }

    let toastContainer;
    function toast(message, type = 'default', duration = 3500) {
        if (!toastContainer) {
            toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container';
                toastContainer.id = 'toast-container';
                document.body.appendChild(toastContainer);
            }
        }
        const el = document.createElement('div');
        el.className = `toast toast-${type}`;
        el.textContent = message;
        toastContainer.appendChild(el);
        requestAnimationFrame(() => el.classList.add('toast-visible'));
        setTimeout(() => {
            el.classList.remove('toast-visible');
            el.addEventListener('transitionend', () => el.remove(), { once: true });
        }, duration);
    }

    function openModal(overlayId) {
        const overlay = document.getElementById(overlayId);
        if (!overlay) return;
        overlay.style.display = 'flex';
        requestAnimationFrame(() => overlay.classList.add('modal-visible'));
        document.body.style.overflow = 'hidden';
    }

    function closeModal(overlayId) {
        const overlay = document.getElementById(overlayId);
        if (!overlay) return;
        overlay.classList.remove('modal-visible');
        overlay.addEventListener('transitionend', () => {
            overlay.style.display = 'none';
            document.body.style.overflow = '';
        }, { once: true });
    }

    function initModalTabs(overlayId) {
        const overlay = document.getElementById(overlayId);
        if (!overlay) return;
        overlay.querySelectorAll('.modal-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                const panelId = tab.dataset.tab;
                overlay.querySelectorAll('.modal-tab').forEach(t => t.classList.toggle('active', t === tab));
                overlay.querySelectorAll('.modal-tab-panel').forEach(p => {
                    p.style.display = p.dataset.panel === panelId ? '' : 'none';
                });
            });
        });
    }

    function formatProgress(pct) {
        if (!pct || pct <= 0) return '';
        if (pct >= 100) return 'Finished';
        return `${Math.round(pct)}% read`;
    }

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1024 / 1024).toFixed(1) + ' MB';
    }

    function debounce(fn, delay = 300) {
        let timer;
        return (...args) => { clearTimeout(timer); timer = setTimeout(() => fn(...args), delay); };
    }

    function throttle(fn, limit = 200) {
        let last = 0;
        return (...args) => {
            const now = Date.now();
            if (now - last >= limit) { last = now; fn(...args); }
        };
    }

    function ensureGuestToken() {
        let token = localStorage.getItem('al_guest');
        if (!token) {
            token = 'g_' + crypto.randomUUID().replace(/-/g, '');
            localStorage.setItem('al_guest', token);
        }
        return token;
    }

    return {
        cardHtml, renderGrid, appendGrid, skeletonCards, observeImages,
        coverFallback, toast, openModal, closeModal, initModalTabs,
        debounce, throttle, escHtml, formatProgress, formatSize,
        typeLabel, authorString, ensureGuestToken,
    };
})();
