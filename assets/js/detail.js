/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

(async () => {
    UI.ensureGuestToken();

    const page    = document.getElementById('detail-page');
    const id      = page?.dataset.id    ?? '';
    const type    = page?.dataset.type  ?? 'book';
    const poster  = document.getElementById('detail-poster');
    const info    = document.getElementById('detail-info');
    const sections = document.getElementById('detail-sections');
    const backdrop = document.getElementById('detail-backdrop');

    if (!id) return;

    function renderBook(data) {
        document.title = `${data.title} — AnyLibrary`;
        const cover    = data.cover_url ?? '/assets/img/placeholder-cover.svg';
        const authors  = UI.authorString(data.authors?.map(a => a.name ?? a) ?? []);

        if (backdrop) {
            backdrop.style.backgroundImage = `url('${cover}')`;
        }

        poster.outerHTML = `
<div class="detail-poster" id="detail-poster">
    <img src="${cover}" alt="${UI.escHtml(data.title)}" onerror="UI.coverFallback(this)">
</div>`;

        info.innerHTML = `
<h1>${UI.escHtml(data.title)}</h1>
${authors ? `<div class="detail-author">by ${UI.escHtml(authors)}</div>` : ''}
<div class="detail-meta">
    <span class="type-badge type-badge-book">Book</span>
    ${data.first_year ? `<span>${data.first_year}</span>` : ''}
    ${data.rating > 0 ? `<span class="rating">&#9733; ${parseFloat(data.rating).toFixed(1)} (${(data.rating_count ?? 0).toLocaleString()} ratings)</span>` : ''}
</div>
<div class="detail-actions">
    <a href="/reader.php?id=${encodeURIComponent(id)}&type=book" class="btn btn-primary">&#128214; Read Now</a>
    <button class="btn btn-secondary" id="fav-btn">&#9825; Favorite</button>
    <button class="btn btn-secondary" id="share-btn" onclick="navigator.share?.({title:'${UI.escHtml(data.title)}',url:location.href})||navigator.clipboard?.writeText(location.href)">&#128279; Share</button>
    <button class="btn btn-ghost" onclick="window.print()" title="Print this page">&#128424; Print</button>
</div>
${data.description ? `<p class="overview">${UI.escHtml(data.description)}</p>` : ''}`;

        sections.innerHTML = `
${data.subjects?.length ? `
<div class="detail-section">
    <h2>Subjects</h2>
    <div class="subjects-list chip-group">
        ${data.subjects.slice(0, 18).map(s => `<a href="/browse?genre=${encodeURIComponent(s.toLowerCase().replace(/ /g,'_'))}" class="chip">${UI.escHtml(s)}</a>`).join('')}
    </div>
</div>` : ''}

${data.related?.length ? `
<div class="detail-section">
    <h2>Related Books</h2>
    <div class="related-grid">${data.related.map(r => UI.cardHtml(r)).join('')}</div>
</div>` : ''}`;

        UI.observeImages(document);
        initFavButton(id, type, data.title, data.cover_url, data.authors?.map(a => a.name ?? a) ?? []);

        API.history.upsert({
            item_id: id, item_type: 'book', title: data.title,
            cover_url: data.cover_url ?? '', authors: data.authors?.map(a => a.name ?? a) ?? [],
            progress_pct: 0,
        }).catch(() => {});
    }

    function renderManga(data) {
        document.title = `${data.title} — AnyLibrary`;
        const cover = data.cover_url ?? '/assets/img/placeholder-cover.svg';
        const authors = UI.authorString(data.authors ?? []);

        if (backdrop) backdrop.style.backgroundImage = `url('${cover}')`;

        document.getElementById('detail-poster').outerHTML = `
<div class="detail-poster" id="detail-poster">
    <img src="${cover}" alt="${UI.escHtml(data.title)}" onerror="UI.coverFallback(this)">
</div>`;

        info.innerHTML = `
<h1>${UI.escHtml(data.title)}</h1>
${authors ? `<div class="detail-author">by ${UI.escHtml(authors)}</div>` : ''}
<div class="detail-meta">
    <span class="type-badge type-badge-manga">Manga</span>
    ${data.year ? `<span>${data.year}</span>` : ''}
    <span>${UI.escHtml(data.status ?? '')}</span>
    ${data.rating > 0 ? `<span class="rating">&#9733; ${parseFloat(data.rating).toFixed(1)}</span>` : ''}
</div>
<div class="detail-actions">
    <a href="/reader.php?id=${encodeURIComponent(id)}&type=manga" class="btn btn-primary">&#128196; Start Reading</a>
    <button class="btn btn-secondary" id="fav-btn">&#9825; Favorite</button>
</div>
${data.desc ? `<p class="overview">${UI.escHtml(data.desc)}</p>` : ''}`;

        const chapterHtml = (data.chapters ?? []).slice(0, 100).map(c => `
<div class="chapter-item" data-chapter-id="${UI.escHtml(c.id)}" role="link" tabindex="0"
     onclick="location.href='/reader.php?id=${encodeURIComponent(c.id)}&type=manga&manga_id=${encodeURIComponent(id)}'">
    <span class="chapter-number">Ch. ${UI.escHtml(c.chapter)}</span>
    <span class="chapter-title">${c.title ? UI.escHtml(c.title) : '&mdash;'}</span>
    <span class="chapter-date">${c.published ? new Date(c.published).toLocaleDateString() : ''}</span>
</div>`).join('');

        sections.innerHTML = `
${data.tags?.length ? `<div class="detail-section"><h2>Tags</h2><div class="chip-group">${data.tags.map(t => `<span class="chip">${UI.escHtml(t)}</span>`).join('')}</div></div>` : ''}
${chapterHtml ? `<div class="detail-section"><h2>Chapters (${(data.chapters ?? []).length})</h2><div class="chapters-list">${chapterHtml}</div></div>` : ''}`;

        initFavButton(id, type, data.title, data.cover_url, data.authors ?? []);
        API.history.upsert({ item_id: id, item_type: 'manga', title: data.title, cover_url: data.cover_url ?? '', authors: data.authors ?? [], progress_pct: 0 }).catch(() => {});
    }

    function renderAudiobook(data) {
        document.title = `${data.title} — AnyLibrary`;
        const cover = data.cover_url ?? '/assets/img/placeholder-cover.svg';
        const authors = UI.authorString(data.authors ?? []);

        if (backdrop) backdrop.style.backgroundImage = `url('${cover}')`;

        document.getElementById('detail-poster').outerHTML = `
<div class="detail-poster" id="detail-poster">
    <img src="${cover}" alt="${UI.escHtml(data.title)}" onerror="UI.coverFallback(this)" style="aspect-ratio:1/1;border-radius:var(--radius-l);">
</div>`;

        info.innerHTML = `
<h1>${UI.escHtml(data.title)}</h1>
${authors ? `<div class="detail-author">by ${UI.escHtml(authors)}</div>` : ''}
<div class="detail-meta">
    <span class="type-badge type-badge-audio">Audiobook</span>
    ${data.year ? `<span>${data.year}</span>` : ''}
    <span>${UI.escHtml(data.language ?? '')}</span>
</div>
<div class="detail-actions">
    <a href="/reader.php?id=${encodeURIComponent(id)}&type=audiobook" class="btn btn-primary" style="background:var(--c-green);">&#9654;&#65039; Listen Now</a>
    <button class="btn btn-secondary" id="fav-btn">&#9825; Favorite</button>
    ${data.url_zip ? `<a href="${UI.escHtml(data.url_zip)}" class="btn btn-ghost" download>&#11015;&#65039; Download ZIP</a>` : ''}
</div>
${data.description ? `<p class="overview">${UI.escHtml(data.description)}</p>` : ''}`;

        const tracks = Array.isArray(data.sections) ? data.sections : [];
        const tracksHtml = tracks.slice(0, 50).map((t, i) => `
<div class="track-item" data-src="${UI.escHtml(t.listen_url ?? '')}" data-idx="${i}">
    <span class="track-number">${i + 1}</span>
    <button class="track-play-btn" aria-label="Play ${UI.escHtml(t.title ?? '')}">&#9654;</button>
    <span class="track-title">${UI.escHtml(t.title ?? `Section ${i+1}`)}</span>
    <span class="track-duration">${t.playtime ?? ''}</span>
</div>`).join('');

        sections.innerHTML = `
${data.genres?.length ? `<div class="detail-section"><h2>Genres</h2><div class="chip-group">${data.genres.map(g => `<a href="/browse?type=audiobook&genre=${encodeURIComponent(g)}" class="chip">${UI.escHtml(g)}</a>`).join('')}</div></div>` : ''}
${tracksHtml ? `<div class="detail-section"><h2>Tracks</h2><div class="tracks-list">${tracksHtml}</div></div>` : ''}`;

        document.querySelectorAll('.track-item').forEach(item => {
            item.querySelector('.track-play-btn')?.addEventListener('click', () => {
                const src = item.dataset.src;
                if (src) {
                    const audio = document.getElementById('global-audio') ?? (() => {
                        const a = document.createElement('audio');
                        a.id = 'global-audio';
                        a.controls = true;
                        a.style = 'position:fixed;bottom:0;left:0;width:100%;z-index:999;';
                        document.body.appendChild(a);
                        return a;
                    })();
                    audio.src = src;
                    audio.play();
                }
            });
        });

        initFavButton(id, type, data.title, data.cover_url, data.authors ?? []);
        API.history.upsert({ item_id: id, item_type: 'audiobook', title: data.title, cover_url: data.cover_url ?? '', authors: data.authors ?? [], progress_pct: 0 }).catch(() => {});
    }

    async function initFavButton(itemId, itemType, title, coverUrl, authors) {
        const btn = document.getElementById('fav-btn');
        if (!btn) return;
        try {
            const res = await API.favorites.check(itemId);
            updateFavBtn(btn, res.favorited);
        } catch { }

        btn.addEventListener('click', async () => {
            const isFav = btn.dataset.fav === 'true';
            try {
                if (isFav) {
                    await API.favorites.remove(itemId);
                    updateFavBtn(btn, false);
                    UI.toast('Removed from favorites', 'default');
                } else {
                    await API.favorites.add({ item_id: itemId, item_type: itemType, title, cover_url: coverUrl ?? '', authors });
                    updateFavBtn(btn, true);
                    UI.toast('Added to favorites ♥', 'success');
                }
            } catch { UI.toast('Something went wrong', 'error'); }
        });
    }

    function updateFavBtn(btn, isFav) {
        btn.dataset.fav = String(isFav);
        btn.innerHTML = isFav ? '&#9829; Favorited' : '&#9825; Favorite';
        btn.style.color = isFav ? 'var(--c-red)' : '';
    }

    try {
        const data = await API.detail.get(id, type);
        if (type === 'book')      renderBook(data);
        else if (type === 'manga')     renderManga(data);
        else if (type === 'audiobook') renderAudiobook(data);
    } catch (err) {
        info.innerHTML = `<div class="empty-state"><div class="empty-state-icon">&#128218;</div><h3>Could not load</h3><p>${UI.escHtml(err.message)}</p></div>`;
        poster.innerHTML = '';
    }
})();
