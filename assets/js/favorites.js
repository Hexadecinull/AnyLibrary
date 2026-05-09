/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

(async () => {
    UI.ensureGuestToken();

    const grid       = document.getElementById('favorites-grid');
    const emptyState = document.getElementById('favorites-empty');
    const typeTabs   = document.getElementById('fav-type-tabs');

    let allItems    = [];
    let activeType  = 'all';

    function render(items) {
        if (!grid) return;
        if (!items.length) {
            grid.innerHTML = '';
            if (emptyState) emptyState.style.display = '';
            return;
        }
        if (emptyState) emptyState.style.display = 'none';
        UI.renderGrid(grid, items.map(item => ({
            id:        item.item_id,
            type:      item.item_type,
            title:     item.title,
            authors:   item.authors ?? [],
            cover_url: item.cover_url,
        })));
    }

    function filterAndRender() {
        const filtered = activeType === 'all'
            ? allItems
            : allItems.filter(i => i.item_type === activeType);
        render(filtered);
    }

    typeTabs?.querySelectorAll('.chip').forEach(chip => {
        chip.addEventListener('click', () => {
            typeTabs.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            activeType = chip.dataset.type ?? 'all';
            filterAndRender();
        });
    });

    try {
        if (grid) grid.innerHTML = UI.skeletonCards(12);
        allItems = await API.favorites.list();
        filterAndRender();
    } catch (err) {
        if (grid) grid.innerHTML = `<div class="empty-state" style="grid-column:1/-1;"><div class="empty-state-icon">&#10084;&#65039;</div><h3>Could not load favorites</h3><p>${UI.escHtml(err.message)}</p></div>`;
    }
})();
