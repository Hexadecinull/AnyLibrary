/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

(async () => {
    UI.ensureGuestToken();

    const page         = document.getElementById('browse-page');
    const grid         = document.getElementById('browse-grid');
    const loadMoreWrap = document.getElementById('browse-load-more-wrap');
    const loadMoreBtn  = document.getElementById('browse-load-more');
    const emptyState   = document.getElementById('browse-empty');
    const typeFilter   = document.getElementById('type-filter');
    const genreFilter  = document.getElementById('genre-filter');
    const sortFilter   = document.getElementById('sort-filter');
    const langFilter   = document.getElementById('lang-filter');

    let currentPage  = 1;
    let isLoading    = false;
    let hasMore      = true;
    let activeParams = {
        type:  page?.dataset.type  ?? 'book',
        genre: page?.dataset.genre ?? '',
        sort:  page?.dataset.sort  ?? 'trending',
        lang:  page?.dataset.lang  ?? '',
    };

    function syncFiltersToParams() {
        activeParams.type  = typeFilter?.value  ?? activeParams.type;
        activeParams.genre = genreFilter?.value ?? activeParams.genre;
        activeParams.sort  = sortFilter?.value  ?? activeParams.sort;
        activeParams.lang  = langFilter?.value  ?? activeParams.lang;
    }

    function pushState() {
        const url = new URL(location.href);
        for (const [k, v] of Object.entries(activeParams)) {
            if (v) url.searchParams.set(k, v);
            else   url.searchParams.delete(k);
        }
        history.replaceState({}, '', url.toString());
    }

    async function load(reset = false) {
        if (isLoading) return;
        isLoading = true;

        if (reset) {
            currentPage = 1;
            hasMore     = true;
            grid.innerHTML = UI.skeletonCards(12);
            if (loadMoreWrap) loadMoreWrap.style.display = 'none';
            if (emptyState)   emptyState.style.display   = 'none';
        }

        try {
            const data = await API.browse.get(
                activeParams.type,
                activeParams.genre,
                activeParams.sort,
                activeParams.lang,
                currentPage,
                20,
            );
            const items = data.results ?? [];
            const total = data.total   ?? 0;

            if (reset) grid.innerHTML = '';

            if (!items.length && currentPage === 1) {
                if (emptyState) emptyState.style.display = '';
            } else {
                UI.appendGrid(grid, items);
                const loaded = (currentPage - 1) * 20 + items.length;
                hasMore = loaded < total && items.length >= 20;
                if (loadMoreWrap) loadMoreWrap.style.display = hasMore ? '' : 'none';
            }
        } catch (err) {
            if (currentPage === 1) {
                grid.innerHTML = `<div class="empty-state" style="grid-column:1/-1;"><div class="empty-state-icon">&#128218;</div><h3>Could not load</h3><p>${UI.escHtml(err.message)}</p></div>`;
            } else {
                UI.toast('Failed to load more', 'error');
            }
        } finally {
            isLoading = false;
        }
    }

    function onFilterChange() {
        syncFiltersToParams();
        pushState();
        load(true);
    }

    typeFilter?.addEventListener('change', onFilterChange);
    genreFilter?.addEventListener('change', onFilterChange);
    sortFilter?.addEventListener('change', onFilterChange);
    langFilter?.addEventListener('change', onFilterChange);

    loadMoreBtn?.addEventListener('click', () => {
        currentPage++;
        load(false);
    });

    if (typeFilter)  typeFilter.value  = activeParams.type;
    if (genreFilter) genreFilter.value = activeParams.genre;
    if (sortFilter)  sortFilter.value  = activeParams.sort;
    if (langFilter)  langFilter.value  = activeParams.lang;

    load(true);
})();
