/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

(() => {
    UI.ensureGuestToken();

    const THEME_KEY    = 'al_theme';
    const FONT_KEY     = 'al_font';
    const PREFS_KEYS   = {
        readerFontSize: 'al_reader_font_size',
        readerTheme:    'al_reader_theme',
        progressBadge:  'al_progress_badge',
        resume:         'al_resume',
        mangaDir:       'al_manga_dir',
        mature:         'al_mature',
        contentLang:    'al_content_lang',
    };

    function applyTheme(theme) {
        document.documentElement.dataset.theme = theme ?? '';
        localStorage.setItem(THEME_KEY, theme ?? '');
        document.querySelectorAll('.theme-swatch').forEach(s => {
            const active = s.dataset.theme === (theme ?? '');
            s.style.outline = active ? '2px solid var(--c-accent)' : '';
            s.style.outlineOffset = active ? '2px' : '';
        });
    }

    function applyFont(font) {
        document.documentElement.style.setProperty(
            '--font-body',
            font === 'system' ? 'system-ui, sans-serif'
            : font === 'mono' ? 'var(--font-mono)'
            : 'Satoshi, "Helvetica Neue", Arial, sans-serif'
        );
        localStorage.setItem(FONT_KEY, font ?? '');
    }

    function loadPrefs() {
        applyTheme(localStorage.getItem(THEME_KEY) ?? '');
        applyFont(localStorage.getItem(FONT_KEY) ?? '');

        const readerFontSizePicker = document.getElementById('reader-font-size-picker');
        if (readerFontSizePicker) {
            readerFontSizePicker.value = localStorage.getItem(PREFS_KEYS.readerFontSize) ?? '1rem';
        }
        const readerThemePicker = document.getElementById('reader-theme-picker');
        if (readerThemePicker) {
            readerThemePicker.value = localStorage.getItem(PREFS_KEYS.readerTheme) ?? 'default';
        }
        const progressBadge = document.getElementById('toggle-progress-badge');
        if (progressBadge) progressBadge.checked = localStorage.getItem(PREFS_KEYS.progressBadge) !== 'false';
        const resume = document.getElementById('toggle-resume');
        if (resume) resume.checked = localStorage.getItem(PREFS_KEYS.resume) !== 'false';
        const mangaDir = document.getElementById('manga-direction-picker');
        if (mangaDir) mangaDir.value = localStorage.getItem(PREFS_KEYS.mangaDir) ?? 'rtl';
        const mature = document.getElementById('toggle-mature');
        if (mature) mature.checked = localStorage.getItem(PREFS_KEYS.mature) === 'true';
        const contentLang = document.getElementById('content-lang-picker');
        if (contentLang) contentLang.value = localStorage.getItem(PREFS_KEYS.contentLang) ?? 'en';
        const fontPicker = document.getElementById('font-picker');
        if (fontPicker) fontPicker.value = localStorage.getItem(FONT_KEY) ?? '';
        const langPicker = document.getElementById('lang-picker');
        if (langPicker) langPicker.value = localStorage.getItem('al_lang') ?? 'en';
    }

    function initSettingsModal() {
        const settingsBtn     = document.getElementById('settings-btn');
        const settingsOverlay = document.getElementById('settings-overlay');
        const settingsClose   = document.getElementById('settings-close');
        const themePicker     = document.getElementById('theme-picker');
        const fontPicker      = document.getElementById('font-picker');

        settingsBtn?.addEventListener('click', () => UI.openModal('settings-overlay'));
        settingsClose?.addEventListener('click', () => UI.closeModal('settings-overlay'));
        settingsOverlay?.addEventListener('click', e => {
            if (e.target === settingsOverlay) UI.closeModal('settings-overlay');
        });

        document.getElementById('mobile-settings-btn')?.addEventListener('click', e => {
            e.preventDefault();
            closeMobileMenu();
            UI.openModal('settings-overlay');
        });

        UI.initModalTabs('settings-overlay');

        themePicker?.querySelectorAll('.theme-swatch').forEach(swatch => {
            swatch.addEventListener('click', () => applyTheme(swatch.dataset.theme ?? ''));
        });

        fontPicker?.addEventListener('change', e => applyFont(e.target.value));

        document.getElementById('reader-font-size-picker')?.addEventListener('change', e => {
            localStorage.setItem(PREFS_KEYS.readerFontSize, e.target.value);
        });
        document.getElementById('reader-theme-picker')?.addEventListener('change', e => {
            localStorage.setItem(PREFS_KEYS.readerTheme, e.target.value);
        });
        document.getElementById('toggle-progress-badge')?.addEventListener('change', e => {
            localStorage.setItem(PREFS_KEYS.progressBadge, e.target.checked);
        });
        document.getElementById('toggle-resume')?.addEventListener('change', e => {
            localStorage.setItem(PREFS_KEYS.resume, e.target.checked);
        });
        document.getElementById('manga-direction-picker')?.addEventListener('change', e => {
            localStorage.setItem(PREFS_KEYS.mangaDir, e.target.value);
        });
        document.getElementById('toggle-mature')?.addEventListener('change', e => {
            localStorage.setItem(PREFS_KEYS.mature, e.target.checked);
        });
        document.getElementById('content-lang-picker')?.addEventListener('change', e => {
            localStorage.setItem(PREFS_KEYS.contentLang, e.target.value);
        });
        document.getElementById('lang-picker')?.addEventListener('change', e => {
            localStorage.setItem('al_lang', e.target.value);
        });
        document.getElementById('clear-history-btn')?.addEventListener('click', async () => {
            if (!confirm('Clear all reading history?')) return;
            try {
                await API.history.clear();
                UI.toast('History cleared', 'success');
            } catch { UI.toast('Failed', 'error'); }
        });
        document.getElementById('export-library-btn')?.addEventListener('click', () => {
            if (typeof ImportExport !== 'undefined') ImportExport.exportLibrary();
        });
    }

    function initAccountModal() {
        const accountBtn     = document.getElementById('account-btn');
        const accountOverlay = document.getElementById('account-overlay');
        const accountClose   = document.getElementById('account-close');

        accountBtn?.addEventListener('click', () => UI.openModal('account-overlay'));
        accountClose?.addEventListener('click', () => UI.closeModal('account-overlay'));
        accountOverlay?.addEventListener('click', e => {
            if (e.target === accountOverlay) UI.closeModal('account-overlay');
        });

        document.getElementById('mobile-account-btn')?.addEventListener('click', e => {
            e.preventDefault();
            closeMobileMenu();
            UI.openModal('account-overlay');
        });

        UI.initModalTabs('account-overlay');

        const loginForm   = document.getElementById('login-form');
        const regForm     = document.getElementById('register-form');
        const loginError  = document.getElementById('login-error');
        const regError    = document.getElementById('register-error');
        const pwField     = document.getElementById('reg-password');
        const pwFill      = document.getElementById('pw-strength-fill');
        const pwLabel     = document.getElementById('pw-strength-label');

        pwField?.addEventListener('input', () => {
            const v = pwField.value;
            let score = 0;
            if (v.length >= 8)                score++;
            if (/[A-Z]/.test(v))              score++;
            if (/[0-9]/.test(v))              score++;
            if (/[^a-zA-Z0-9]/.test(v))       score++;
            const pct     = score * 25;
            const colors  = ['#ef4444', '#f97316', '#eab308', '#22c55e'];
            const labels  = ['Weak', 'Fair', 'Good', 'Strong'];
            if (pwFill)  { pwFill.style.width = pct + '%'; pwFill.style.background = colors[score - 1] ?? 'var(--c-bg-4)'; }
            if (pwLabel) pwLabel.textContent = v.length ? (labels[score - 1] ?? '') : '';
        });

        loginForm?.addEventListener('submit', async e => {
            e.preventDefault();
            const email = document.getElementById('login-email')?.value.trim();
            const pass  = document.getElementById('login-password')?.value;
            if (loginError) loginError.style.display = 'none';
            try {
                const res = await API.auth.login(email, pass);
                localStorage.setItem('al_token', res.token ?? '');
                UI.closeModal('account-overlay');
                UI.toast(`Welcome back, ${res.name ?? 'reader'} 👋`, 'success');
                location.reload();
            } catch (err) {
                if (loginError) { loginError.textContent = err.message; loginError.style.display = ''; }
            }
        });

        regForm?.addEventListener('submit', async e => {
            e.preventDefault();
            const name  = document.getElementById('reg-name')?.value.trim();
            const email = document.getElementById('reg-email')?.value.trim();
            const pass  = document.getElementById('reg-password')?.value;
            if (regError) regError.style.display = 'none';
            try {
                const res = await API.auth.register(name, email, pass);
                localStorage.setItem('al_token', res.token ?? '');
                UI.closeModal('account-overlay');
                UI.toast(`Welcome to AnyLibrary, ${name || 'reader'}! 🎉`, 'success');
                location.reload();
            } catch (err) {
                if (regError) { regError.textContent = err.message; regError.style.display = ''; }
            }
        });

        const avatarBtn   = document.getElementById('avatar-upload-btn');
        const avatarInput = document.getElementById('avatar-input');
        avatarBtn?.addEventListener('click', () => avatarInput?.click());
        avatarInput?.addEventListener('change', e => {
            const file = e.target.files?.[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = ev => {
                    const img = avatarBtn.querySelector('img') ?? document.createElement('img');
                    img.src   = ev.target.result;
                    avatarBtn.innerHTML = '';
                    avatarBtn.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    function initImportModal() {
        const importBtn     = document.getElementById('import-btn');
        const importOverlay = document.getElementById('import-overlay');
        const importClose   = document.getElementById('import-close');

        importBtn?.addEventListener('click', () => UI.openModal('import-overlay'));
        importClose?.addEventListener('click', () => UI.closeModal('import-overlay'));
        importOverlay?.addEventListener('click', e => {
            if (e.target === importOverlay) UI.closeModal('import-overlay');
        });

        document.getElementById('mobile-import-btn')?.addEventListener('click', e => {
            e.preventDefault();
            closeMobileMenu();
            UI.openModal('import-overlay');
        });

        UI.initModalTabs('import-overlay');

        if (typeof ImportExport !== 'undefined') ImportExport.init();
    }

    function initSearch() {
        const form     = document.getElementById('search-form');
        const input    = document.getElementById('search-input');
        const dropdown = document.getElementById('search-dropdown');
        const mobileInput = document.getElementById('mobile-search-input');

        let lastQuery = '';

        const suggest = UI.debounce(async (q) => {
            if (q.length < 2) { dropdown.innerHTML = ''; return; }
            if (q === lastQuery) return;
            lastQuery = q;
            try {
                const data  = await API.search.query(q, 'all', 1, 6);
                const items = data.results ?? [];
                if (!items.length) { dropdown.innerHTML = ''; return; }
                dropdown.innerHTML = items.map(item => `
<a class="search-result" href="/detail.php?id=${encodeURIComponent(item.id)}&type=${encodeURIComponent(item.type ?? 'book')}">
    <div class="search-result-thumb">
        <img src="${item.cover_url ?? '/assets/img/placeholder-cover.svg'}"
             alt="" onerror="UI.coverFallback(this)">
    </div>
    <div class="search-result-info">
        <div class="search-result-title">${UI.escHtml(item.title)}</div>
        <div class="search-result-meta">${UI.escHtml(UI.authorString(item.authors ?? []))} · <span class="type-badge ${item.type === 'manga' ? 'type-badge-manga' : item.type === 'audiobook' ? 'type-badge-audio' : 'type-badge-book'}">${UI.typeLabel(item.type ?? 'book')}</span></div>
    </div>
</a>`).join('');
                input?.setAttribute('aria-expanded', 'true');
            } catch { dropdown.innerHTML = ''; }
        }, 280);

        input?.addEventListener('input', e => suggest(e.target.value.trim()));
        input?.addEventListener('focus',  e => { if (e.target.value.trim()) suggest(e.target.value.trim()); });

        document.addEventListener('click', e => {
            if (!form?.contains(e.target)) {
                dropdown.innerHTML = '';
                input?.setAttribute('aria-expanded', 'false');
            }
        });

        form?.addEventListener('submit', e => {
            e.preventDefault();
            const q = input?.value.trim();
            if (q) location.href = `/search.php?q=${encodeURIComponent(q)}`;
        });

        mobileInput?.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                const q = mobileInput.value.trim();
                if (q) location.href = `/search.php?q=${encodeURIComponent(q)}`;
            }
        });
    }

    function initMobileMenu() {
        const menuBtn = document.getElementById('mobile-menu-btn');
        const drawer  = document.getElementById('mobile-drawer');
        if (!menuBtn || !drawer) return;

        menuBtn.addEventListener('click', () => {
            const open = drawer.style.display !== 'none';
            drawer.style.display = open ? 'none' : 'block';
            menuBtn.setAttribute('aria-expanded', String(!open));
            document.body.style.overflow = open ? '' : 'hidden';
        });

        document.addEventListener('click', e => {
            if (!drawer.contains(e.target) && !menuBtn.contains(e.target) && drawer.style.display !== 'none') {
                closeMobileMenu();
            }
        });
    }

    function closeMobileMenu() {
        const drawer  = document.getElementById('mobile-drawer');
        const menuBtn = document.getElementById('mobile-menu-btn');
        if (drawer)  drawer.style.display = 'none';
        if (menuBtn) menuBtn.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            UI.closeModal('settings-overlay');
            UI.closeModal('account-overlay');
            UI.closeModal('import-overlay');
            closeMobileMenu();
        }
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            document.getElementById('search-input')?.focus();
        }
    });

    loadPrefs();
    initSettingsModal();
    initAccountModal();
    initImportModal();
    initSearch();
    initMobileMenu();
    UI.observeImages();
})();
