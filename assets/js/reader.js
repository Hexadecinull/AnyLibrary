/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

(async () => {
    UI.ensureGuestToken();

    const page      = document.getElementById('reader-page');
    const id        = page?.dataset.id   ?? '';
    const type      = page?.dataset.type ?? 'book';
    const srcAttr   = page?.dataset.src  ?? '';
    const content   = document.getElementById('reader-content');
    const titleEl   = document.getElementById('reader-topbar-title');
    const progressFill = document.getElementById('reader-progress-fill');
    const settingsPanel = document.getElementById('reader-settings-panel');
    const settingsBtn   = document.getElementById('reader-settings-btn');

    let currentBookData = null;
    let progressSaveTimer;
    let currentMangaPages = [];
    let currentMangaPage  = 0;

    const prefs = {
        fontSize:  localStorage.getItem('al_reader_font_size')  || '1rem',
        theme:     localStorage.getItem('al_reader_theme')       || 'default',
        fontFamily:localStorage.getItem('al_reader_font_family') || 'var(--font-body)',
    };

    function applyPrefs() {
        document.body.dataset.readerTheme = prefs.theme;
        if (content) {
            content.style.setProperty('--reader-font-size',   prefs.fontSize);
            content.style.setProperty('--reader-line-height', '1.9');
            content.style.setProperty('--reader-font',        prefs.fontFamily);
            content.style.fontSize   = prefs.fontSize;
            content.style.fontFamily = prefs.fontFamily;
        }
        document.querySelectorAll('.reader-font-size-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.size === prefs.fontSize);
        });
        document.querySelectorAll('.reader-theme-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.theme === prefs.theme);
        });
    }

    function savePrefs() {
        localStorage.setItem('al_reader_font_size',   prefs.fontSize);
        localStorage.setItem('al_reader_theme',       prefs.theme);
        localStorage.setItem('al_reader_font_family', prefs.fontFamily);
    }

    document.querySelectorAll('.reader-font-size-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            prefs.fontSize = btn.dataset.size;
            applyPrefs(); savePrefs();
        });
    });

    document.querySelectorAll('.reader-theme-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            prefs.theme = btn.dataset.theme;
            applyPrefs(); savePrefs();
        });
    });

    document.getElementById('reader-font-select')?.addEventListener('change', e => {
        prefs.fontFamily = e.target.value;
        applyPrefs(); savePrefs();
    });

    settingsBtn?.addEventListener('click', () => {
        settingsPanel?.classList.toggle('open');
    });

    document.addEventListener('click', e => {
        if (settingsPanel?.classList.contains('open') &&
            !settingsPanel.contains(e.target) && e.target !== settingsBtn) {
            settingsPanel.classList.remove('open');
        }
    });

    document.getElementById('reader-print-btn')?.addEventListener('click', () => window.print());

    function updateScrollProgress() {
        const el   = document.scrollingElement;
        const pct  = el.scrollHeight <= el.clientHeight
            ? 0
            : (el.scrollTop / (el.scrollHeight - el.clientHeight)) * 100;
        if (progressFill) progressFill.style.width = pct.toFixed(1) + '%';

        clearTimeout(progressSaveTimer);
        progressSaveTimer = setTimeout(() => {
            if (currentBookData && id) {
                API.history.upsert({
                    item_id:       id,
                    item_type:     type,
                    title:         currentBookData.title ?? '',
                    cover_url:     currentBookData.cover_url ?? '',
                    authors:       currentBookData.authors?.map(a => a.name ?? a) ?? [],
                    progress_pct:  parseFloat(pct.toFixed(1)),
                    last_position: { scroll_pct: pct },
                }).catch(() => {});
            }
        }, 1500);
    }

    document.addEventListener('scroll', UI.throttle(updateScrollProgress, 150));

    applyPrefs();

    async function renderEpubOrText(data) {
        currentBookData = data;
        if (titleEl) titleEl.textContent = data.title ?? 'Reading…';

        const readUrl = `https://openlibrary.org/works/${id}/editions.json?limit=1`;
        const iaUrl   = `https://archive.org/services/img/${id}`;

        content.innerHTML = `
<div style="text-align:center;margin-bottom:3rem;">
    <img src="${data.cover_url || '/assets/img/placeholder-cover.svg'}"
         alt="${UI.escHtml(data.title)}"
         style="max-width:200px;border-radius:var(--radius-l);box-shadow:var(--shadow-l);margin:0 auto 1.5rem;"
         onerror="UI.coverFallback(this)">
    <h1 style="font-size:1.8em;margin-bottom:0.5rem;">${UI.escHtml(data.title)}</h1>
    <p style="color:var(--c-text-3);font-size:0.9em;">${UI.escHtml(UI.authorString(data.authors?.map(a=>a.name??a)??[]))}</p>
</div>
<div style="background:var(--c-bg-3);border:1px solid var(--c-border);border-radius:var(--radius-l);padding:2rem;text-align:center;margin-bottom:2rem;">
    <p style="color:var(--c-text-2);margin-bottom:1.25rem;line-height:1.7;">
        ${data.description ? UI.escHtml(data.description.slice(0, 400)) + (data.description.length > 400 ? '…' : '') : 'No description available.'}
    </p>
    <div style="display:flex;gap:0.75rem;justify-content:center;flex-wrap:wrap;">
        <a href="https://openlibrary.org/works/${id}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
            &#128279; Read on Open Library
        </a>
        <a href="https://archive.org/search?query=${encodeURIComponent(data.title)}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">
            &#128218; Internet Archive
        </a>
        ${data.links?.length ? data.links.slice(0,3).map(l => `<a href="${UI.escHtml(l.url)}" target="_blank" rel="noopener noreferrer" class="btn btn-ghost">&#128279; ${UI.escHtml(l.title)}</a>`).join('') : ''}
    </div>
</div>
<div style="color:var(--c-text-3);font-size:0.8rem;text-align:center;padding:1rem 0;">
    <p>In-browser EPUB rendering requires an EPUB file to be imported. Import the EPUB of this book using the import button in the top navigation, or read it directly on Open Library or the Internet Archive.</p>
</div>`;
    }

    async function renderMangaReader(chapterId) {
        if (titleEl) titleEl.textContent = 'Loading chapter…';
        content.innerHTML = '<div style="text-align:center;padding:3rem;color:var(--c-text-3);">Loading pages…</div>';

        try {
            const data = await API.manga.chapterPages(chapterId);
            currentMangaPages = data.pages ?? [];
            currentMangaPage  = 0;

            const body = document.getElementById('reader-body');
            body.className = 'manga-reader-body';
            body.innerHTML = currentMangaPages.map((src, i) => `
<div class="manga-page">
    <img data-lazy="true" data-src="${UI.escHtml(src)}" alt="Page ${i+1}" loading="lazy">
</div>`).join('') + `
<nav class="manga-nav" aria-label="Chapter navigation">
    <button class="btn btn-ghost btn-icon" id="manga-prev" title="Previous chapter" aria-label="Previous chapter">&#8249;</button>
    <span class="manga-page-indicator" id="manga-pi">Scroll to read</span>
    <button class="btn btn-ghost btn-icon" id="manga-next" title="Next chapter" aria-label="Next chapter">&#8250;</button>
</nav>`;

            if (titleEl) titleEl.textContent = `Chapter`;
            UI.observeImages(body);
        } catch (err) {
            content.innerHTML = `<div class="empty-state"><div class="empty-state-icon">&#128196;</div><h3>Could not load chapter</h3><p>${UI.escHtml(err.message)}</p></div>`;
        }
    }

    async function renderAudiobookPlayer(data) {
        currentBookData = data;
        if (titleEl) titleEl.textContent = data.title ?? 'Listening…';

        const tracks = Array.isArray(data.sections) ? data.sections : [];
        let currentTrack = 0;
        let audio;

        content.innerHTML = `
<div style="text-align:center;margin-bottom:2rem;">
    <img src="${data.cover_url || '/assets/img/placeholder-cover.svg'}"
         alt="${UI.escHtml(data.title)}"
         style="width:180px;height:180px;object-fit:cover;border-radius:var(--radius-l);box-shadow:var(--shadow-l);margin:0 auto 1.5rem;"
         onerror="UI.coverFallback(this)">
    <h1 style="font-size:1.4em;margin-bottom:0.4rem;">${UI.escHtml(data.title)}</h1>
    <p style="color:var(--c-text-3);">${UI.escHtml(UI.authorString(data.authors ?? []))}</p>
</div>
<div id="player-placeholder"></div>`;

        const playerEl = document.getElementById('player-placeholder');
        if (!tracks.length) {
            playerEl.innerHTML = `<div class="empty-state"><div class="empty-state-icon">&#127911;</div><h3>No playable tracks found</h3><p><a href="${UI.escHtml(data.url_rss ?? '')}" class="btn btn-primary" target="_blank" rel="noopener noreferrer">Open in podcast app (RSS)</a></p></div>`;
            return;
        }

        function buildPlayerBar() {
            const t = tracks[currentTrack];
            document.querySelector('.audio-player-bar')?.remove();
            const bar = document.createElement('div');
            bar.className = 'audio-player-bar';
            bar.innerHTML = `
<div class="audio-player-top">
    <img class="audio-cover" src="${data.cover_url || '/assets/img/placeholder-cover.svg'}" alt="" onerror="UI.coverFallback(this)">
    <div class="audio-info">
        <div class="audio-title">${UI.escHtml(t.title ?? `Section ${currentTrack + 1}`)}</div>
        <div class="audio-author">${UI.escHtml(data.title)}</div>
    </div>
    <div class="audio-controls">
        <button class="btn btn-ghost btn-icon" id="ap-prev" title="Previous" ${currentTrack===0?'disabled':''}>&#8249;</button>
        <button class="btn btn-primary btn-icon" id="ap-play" style="border-radius:50%;width:36px;height:36px;" title="Play / Pause">&#9654;</button>
        <button class="btn btn-ghost btn-icon" id="ap-next" title="Next" ${currentTrack>=tracks.length-1?'disabled':''}>&#8250;</button>
        <button class="speed-btn" id="ap-speed">1×</button>
    </div>
</div>
<div style="display:flex;align-items:center;gap:0.75rem;">
    <span class="audio-time" id="ap-current">0:00</span>
    <input type="range" class="audio-seek" id="ap-seek" value="0" min="0" max="100" step="0.1">
    <span class="audio-time" id="ap-total">--:--</span>
</div>`;
            document.body.appendChild(bar);

            audio = new Audio(t.listen_url ?? '');
            const playBtn = document.getElementById('ap-play');
            const seekEl  = document.getElementById('ap-seek');
            const curEl   = document.getElementById('ap-current');
            const totEl   = document.getElementById('ap-total');
            const speeds  = [1, 1.25, 1.5, 1.75, 2];
            let speedIdx  = 0;

            function fmt(s) {
                s = Math.floor(s);
                const m = Math.floor(s / 60);
                return `${m}:${String(s % 60).padStart(2, '0')}`;
            }

            audio.addEventListener('loadedmetadata', () => { totEl.textContent = fmt(audio.duration); seekEl.max = audio.duration; });
            audio.addEventListener('timeupdate', () => {
                curEl.textContent = fmt(audio.currentTime);
                seekEl.value = audio.currentTime;
                const pct = audio.duration ? (audio.currentTime / audio.duration) * 100 : 0;
                API.history.upsert({ item_id: id, item_type: 'audiobook', title: data.title, cover_url: data.cover_url ?? '', authors: data.authors ?? [], progress_pct: pct, last_position: { track: currentTrack, time: audio.currentTime } }).catch(()=>{});
            });
            audio.addEventListener('ended', () => {
                if (currentTrack < tracks.length - 1) { currentTrack++; buildPlayerBar(); audio.play(); }
            });
            audio.play().catch(() => {});
            playBtn.textContent = '⏸';
            playBtn.addEventListener('click', () => {
                if (audio.paused) { audio.play(); playBtn.textContent = '⏸'; }
                else { audio.pause(); playBtn.textContent = '▶'; }
            });
            seekEl.addEventListener('input', () => { audio.currentTime = seekEl.value; });
            document.getElementById('ap-prev')?.addEventListener('click', () => { if (currentTrack > 0) { currentTrack--; buildPlayerBar(); audio.play(); } });
            document.getElementById('ap-next')?.addEventListener('click', () => { if (currentTrack < tracks.length - 1) { currentTrack++; buildPlayerBar(); audio.play(); } });
            document.getElementById('ap-speed')?.addEventListener('click', e => {
                speedIdx = (speedIdx + 1) % speeds.length;
                audio.playbackRate = speeds[speedIdx];
                e.target.textContent = speeds[speedIdx] + '×';
            });
        }

        playerEl.innerHTML = `<div class="tracks-list">${tracks.slice(0, 50).map((t, i) => `
<div class="track-item" style="cursor:pointer;" onclick="const a=document.querySelector('audio');if(a){a.src='${UI.escHtml(t.listen_url??'')}';a.play();}">
    <span class="track-number">${i+1}</span>
    <span class="track-title">${UI.escHtml(t.title ?? `Section ${i+1}`)}</span>
    <span class="track-duration">${t.playtime ?? ''}</span>
</div>`).join('')}</div>`;

        buildPlayerBar();
    }

    try {
        if (type === 'book' || type === 'imported') {
            const data = await API.detail.get(id, 'book');
            await renderEpubOrText(data);
        } else if (type === 'manga') {
            await renderMangaReader(id);
        } else if (type === 'audiobook') {
            const data = await API.detail.get(id, 'audiobook');
            await renderAudiobookPlayer(data);
        }
    } catch (err) {
        if (content) content.innerHTML = `<div class="empty-state"><div class="empty-state-icon">&#128218;</div><h3>Could not load</h3><p>${UI.escHtml(err.message)}</p></div>`;
    }
})();
