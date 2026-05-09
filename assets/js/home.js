/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

(async () => {
    UI.ensureGuestToken();

    const featuredContainer = document.getElementById('featured-container');
    const rowsContainer     = document.getElementById('rows-container');
    const continueRow       = document.getElementById('continue-row');
    const continueItems     = document.getElementById('continue-items');

    let featuredItems = [];
    let featuredIdx   = 0;
    let autoSlideTimer;

    function renderFeatured(item) {
        const cover = item.cover_url ?? '/assets/img/placeholder-cover.svg';
        const authors = UI.authorString(item.authors);
        const type = item.type ?? 'book';
        const badgeClass = type === 'manga' ? 'badge-manga' : type === 'audiobook' ? 'badge-audio' : '';

        featuredContainer.innerHTML = `
<div class="featured-backdrop" id="featured-backdrop" style="background-image:url('${cover}');"></div>
<div class="featured-content featured-slide-in">
    <div class="featured-type-badge ${badgeClass}">${UI.typeLabel(type)}</div>
    <h1>${UI.escHtml(item.title)}</h1>
    <div class="featured-meta">
        ${authors ? `<span>${UI.escHtml(authors)}</span>` : ''}
        ${item.year ? `<span>${item.year}</span>` : ''}
        ${item.rating > 0 ? `<span class="featured-rating">★ ${parseFloat(item.rating).toFixed(1)}</span>` : ''}
        ${item.subjects?.length ? `<span>${UI.escHtml(item.subjects.slice(0,2).join(' · '))}</span>` : ''}
    </div>
    ${item.overview ? `<p class="featured-overview">${UI.escHtml(item.overview)}</p>` : ''}
    <div class="featured-actions">
        <a href="/reader.php?id=${encodeURIComponent(item.id)}&type=${encodeURIComponent(type)}"
           class="btn btn-primary">
            ${type === 'audiobook' ? '&#9654;&#65039; Listen' : '&#128214; Read Now'}
        </a>
        <a href="/detail.php?id=${encodeURIComponent(item.id)}&type=${encodeURIComponent(type)}"
           class="btn btn-secondary">More Info</a>
    </div>
</div>
<div class="featured-dots" id="featured-dots">
    ${featuredItems.map((_, i) => `<button class="featured-dot ${i === featuredIdx ? 'active' : ''}" data-idx="${i}" aria-label="Slide ${i+1}"></button>`).join('')}
</div>`;

        featuredContainer.className = 'featured-section';

        document.getElementById('featured-dots')?.querySelectorAll('.featured-dot').forEach(dot => {
            dot.addEventListener('click', () => {
                clearTimeout(autoSlideTimer);
                featuredIdx = parseInt(dot.dataset.idx, 10);
                renderFeatured(featuredItems[featuredIdx]);
                scheduleAutoSlide();
            });
        });
    }

    function scheduleAutoSlide() {
        clearTimeout(autoSlideTimer);
        autoSlideTimer = setTimeout(() => {
            featuredIdx = (featuredIdx + 1) % featuredItems.length;
            renderFeatured(featuredItems[featuredIdx]);
            scheduleAutoSlide();
        }, 7000);
    }

    function buildRow(id, icon, title, items, seeAllHref = '#') {
        if (!items || !items.length) return '';
        const cards = items.map(item => UI.cardHtml(item)).join('');
        return `
<section class="content-row">
    <div class="row-header">
        <div class="row-header-left">
            <div class="row-icon">${icon}</div>
            <h2 class="text-xl">${UI.escHtml(title)}</h2>
        </div>
        <a href="${seeAllHref}" class="see-all">See all &rarr;</a>
    </div>
    <div class="row-items" id="${id}">${cards}</div>
</section>`;
    }

    async function loadContinueReading() {
        try {
            const history = await API.history.list();
            const inProgress = history.filter(h => h.progress_pct > 0 && h.progress_pct < 100).slice(0, 6);
            if (!inProgress.length) return;
            continueItems.innerHTML = inProgress.map(item => {
                const pct = Math.round(parseFloat(item.progress_pct));
                return `
<a class="card card-continue" href="/reader.php?id=${encodeURIComponent(item.item_id)}&type=${encodeURIComponent(item.item_type)}">
    <div class="card-poster">
        <img data-lazy="true" data-src="${item.cover_url || '/assets/img/placeholder-cover.svg'}"
             alt="${UI.escHtml(item.title)}" onerror="UI.coverFallback(this)">
        <div class="card-continue-progress" style="width:${pct}%;"></div>
    </div>
    <div class="card-body">
        <div class="card-title">${UI.escHtml(item.title)}</div>
        <div class="card-meta card-continue-meta">${pct}% read</div>
        <div class="progress-bar"><div class="progress-fill" style="width:${pct}%;"></div></div>
    </div>
</a>`;
            }).join('');
            continueRow.hidden = false;
            UI.observeImages(continueItems);
        } catch { }
    }

    try {
        const [featured, trendingBooks, trendingManga, audioNew, fantasy] = await Promise.all([
            API.home.featured(),
            API.home.trendingBooks(12),
            API.home.trendingManga(12),
            API.home.audiobooksNew(12),
            API.home.subject('fantasy', 12),
        ]);

        featuredItems = featured;
        if (featuredItems.length) {
            renderFeatured(featuredItems[0]);
            scheduleAutoSlide();
        }

        rowsContainer.innerHTML = [
            buildRow('row-books',    '&#128218;', 'Trending Books',      trendingBooks, '/trending'),
            buildRow('row-manga',    '&#128196;', 'Popular Manga',       trendingManga, '/manga'),
            buildRow('row-audio',    '&#127911;', 'Audiobooks',          audioNew,      '/audiobooks'),
            buildRow('row-fantasy',  '&#9986;&#65039;',  'Fantasy Picks',        fantasy,       '/browse?genre=fantasy'),
        ].join('');

        UI.observeImages(rowsContainer);
        loadContinueReading();
    } catch (err) {
        featuredContainer.innerHTML = `<div class="empty-state" style="min-height:40vh;display:flex;flex-direction:column;justify-content:center;"><div class="empty-state-icon">&#128218;</div><h3>Could not load books</h3><p>${UI.escHtml(err.message)}</p></div>`;
    }
})();
