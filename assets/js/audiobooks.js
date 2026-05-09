/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

(async () => {
    UI.ensureGuestToken();

    const grid         = document.getElementById('audiobooks-grid');
    const genreFilters = document.getElementById('audio-genre-filters');
    const loadMoreWrap = document.getElementById('audio-load-more-wrap');
    const loadMoreBtn  = document.getElementById('audio-load-more');

    let activeGenre = '';
    let offset      = 0;
    let isLoading   = false;
    const LIMIT     = 20;

    async function load(reset = false) {
        if (isLoading) return;
        isLoading = true;

        if (reset) {
            offset = 0;
            grid.innerHTML = UI.skeletonCards(20).replace(/aspect-ratio:2\/3/g, 'aspect-ratio:1/1');
            if (loadMoreWrap) loadMoreWrap.style.display = 'none';
        }

        try {
            const data  = await API.browse.get('audiobook', activeGenre, 'trending', '', Math.floor(offset / LIMIT) + 1, LIMIT);
            const items = data.results ?? [];

            if (reset) grid.innerHTML = '';

            if (!items.length && offset === 0) {
                grid.innerHTML = '<div class="empty-state" style="grid-column:1/-1;"><div class="empty-state-icon">&#127911;</div><h3>No audiobooks found</h3><p>Try a different genre.</p></div>';
            } else {
                UI.appendGrid(grid, items.map(a => ({ ...a, type: 'audiobook' })));
                offset += items.length;
                if (loadMoreWrap) loadMoreWrap.style.display = items.length >= LIMIT ? '' : 'none';
            }
        } catch (err) {
            if (offset === 0) {
                grid.innerHTML = `<div class="empty-state" style="grid-column:1/-1;"><div class="empty-state-icon">&#127911;</div><h3>Could not load</h3><p>${UI.escHtml(err.message)}</p></div>`;
            } else {
                UI.toast('Failed to load more', 'error');
            }
        } finally {
            isLoading = false;
        }
    }

    genreFilters?.querySelectorAll('.chip').forEach(chip => {
        chip.addEventListener('click', () => {
            genreFilters.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            activeGenre = chip.dataset.genre ?? '';
            load(true);
        });
    });

    loadMoreBtn?.addEventListener('click', () => load(false));

    load(true);
})();
