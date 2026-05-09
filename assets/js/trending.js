/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

(async () => {
    UI.ensureGuestToken();

    const page   = document.getElementById('trending-page');
    const grid   = document.getElementById('trending-grid');
    const period = page?.dataset.period ?? 'daily';

    if (!grid) return;

    grid.innerHTML = UI.skeletonCards(20);

    try {
        const data  = await API.trending.get(period, 60);
        const items = data.results ?? (Array.isArray(data) ? data : []);
        if (!items.length) {
            grid.innerHTML = '<div class="empty-state" style="grid-column:1/-1;"><div class="empty-state-icon">&#128200;</div><h3>No data available</h3></div>';
            return;
        }
        grid.innerHTML = '';
        UI.appendGrid(grid, items.map((item, i) => ({ ...item, _rank: i + 1 })));
    } catch (err) {
        grid.innerHTML = `<div class="empty-state" style="grid-column:1/-1;"><div class="empty-state-icon">&#128200;</div><h3>Could not load</h3><p>${UI.escHtml(err.message)}</p></div>`;
    }
})();
