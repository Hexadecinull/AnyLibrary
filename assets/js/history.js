/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

(async () => {
    UI.ensureGuestToken();

    const grid      = document.getElementById('history-grid');
    const empty     = document.getElementById('history-empty');
    const clearBtn  = document.getElementById('clear-all-history-btn');

    async function load() {
        if (grid) grid.innerHTML = UI.skeletonCards(12);
        try {
            const items = await API.history.list();
            if (!items.length) {
                if (grid)  grid.innerHTML = '';
                if (empty) empty.style.display = '';
                return;
            }
            if (empty) empty.style.display = 'none';
            UI.renderGrid(grid, items.map(item => ({
                id:           item.item_id,
                type:         item.item_type,
                title:        item.title,
                authors:      item.authors ?? [],
                cover_url:    item.cover_url,
                progress_pct: item.progress_pct ?? 0,
            })));
        } catch (err) {
            if (grid) grid.innerHTML = `<div class="empty-state" style="grid-column:1/-1;"><div class="empty-state-icon">&#128336;</div><h3>Could not load history</h3><p>${UI.escHtml(err.message)}</p></div>`;
        }
    }

    clearBtn?.addEventListener('click', async () => {
        if (!confirm('Clear all reading history? This cannot be undone.')) return;
        try {
            await API.history.clear();
            UI.toast('Reading history cleared', 'success');
            load();
        } catch {
            UI.toast('Failed to clear history', 'error');
        }
    });

    load();
})();
