/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

(async () => {
    UI.ensureGuestToken();

    const grid         = document.getElementById('manga-grid');
    const tagFilters   = document.getElementById('manga-tag-filters');
    const loadMoreWrap = document.getElementById('manga-load-more-wrap');
    const loadMoreBtn  = document.getElementById('manga-load-more');

    let activeTag   = '';
    let offset      = 0;
    let isLoading   = false;
    const LIMIT     = 20;

    async function load(reset = false) {
        if (isLoading) return;
        isLoading = true;

        if (reset) {
            offset = 0;
            grid.innerHTML = UI.skeletonCards(20);
            if (loadMoreWrap) loadMoreWrap.style.display = 'none';
        }

        try {
            const data  = await API.manga.search('', LIMIT, offset, activeTag);
            const items = data.results ?? [];
            const total = data.total   ?? 0;

            if (reset) grid.innerHTML = '';

            if (!items.length && offset === 0) {
                grid.innerHTML = '<div class="empty-state" style="grid-column:1/-1;"><div class="empty-state-icon">&#128196;</div><h3>No manga found</h3></div>';
            } else {
                UI.appendGrid(grid, items.map(m => ({
                    ...m,
                    type: 'manga',
                    id:   m.id,
                    authors: m.authors ?? [],
                })));
                offset += items.length;
                if (loadMoreWrap) loadMoreWrap.style.display = offset < total ? '' : 'none';
            }
        } catch (err) {
            if (offset === 0) {
                grid.innerHTML = `<div class="empty-state" style="grid-column:1/-1;"><div class="empty-state-icon">&#128196;</div><h3>Could not load</h3><p>${UI.escHtml(err.message)}</p></div>`;
            } else {
                UI.toast('Failed to load more', 'error');
            }
        } finally {
            isLoading = false;
        }
    }

    tagFilters?.querySelectorAll('.chip').forEach(chip => {
        chip.addEventListener('click', () => {
            tagFilters.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            activeTag = chip.dataset.tag ?? '';
            load(true);
        });
    });

    loadMoreBtn?.addEventListener('click', () => load(false));

    load(true);
})();
