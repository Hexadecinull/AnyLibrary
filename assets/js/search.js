/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

(async () => {
    UI.ensureGuestToken();

    const page         = document.getElementById('search-page');
    const grid         = document.getElementById('search-grid');
    const loadMoreWrap = document.getElementById('search-load-more-wrap');
    const loadMoreBtn  = document.getElementById('search-load-more');
    const emptyState   = document.getElementById('search-empty');
    const typeTabs     = document.getElementById('search-type-tabs');

    if (!page) return;

    let query      = page.dataset.query ?? '';
    let activeType = page.dataset.type  ?? 'all';
    let currentPage = 1;
    let isLoading   = false;
    let hasMore     = false;

    async function load(reset = false) {
        if (!query || isLoading) return;
        isLoading = true;

        if (reset) {
            currentPage = 1;
            hasMore     = false;
            if (grid) grid.innerHTML = UI.skeletonCards(12);
            if (loadMoreWrap) loadMoreWrap.style.display = 'none';
            if (emptyState)   emptyState.style.display   = 'none';
        }

        try {
            const data  = await API.search.query(query, activeType, currentPage, 20);
            const items = data.results ?? [];
            const total = data.total   ?? 0;

            if (reset && grid) grid.innerHTML = '';

            if (!items.length && currentPage === 1) {
                if (emptyState) emptyState.style.display = '';
            } else {
                if (grid) UI.appendGrid(grid, items);
                const loaded = (currentPage - 1) * 20 + items.length;
                hasMore = loaded < total && items.length >= 20;
                if (loadMoreWrap) loadMoreWrap.style.display = hasMore ? '' : 'none';
            }
        } catch (err) {
            if (currentPage === 1 && grid) {
                grid.innerHTML = `<div class="empty-state" style="grid-column:1/-1;"><div class="empty-state-icon">&#128270;</div><h3>Search error</h3><p>${UI.escHtml(err.message)}</p></div>`;
            }
        } finally {
            isLoading = false;
        }
    }

    typeTabs?.querySelectorAll('.chip').forEach(chip => {
        chip.addEventListener('click', () => {
            typeTabs.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            activeType = chip.dataset.type ?? 'all';
            const url = new URL(location.href);
            url.searchParams.set('type', activeType);
            history.replaceState({}, '', url.toString());
            load(true);
        });
    });

    loadMoreBtn?.addEventListener('click', () => { currentPage++; load(false); });

    if (query) load(true);
})();
