/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

const API = (() => {
    const BASE = '/api';

    async function request(path, params = {}) {
        const url = new URL(BASE + path, location.origin);
        for (const [k, v] of Object.entries(params)) {
            if (v !== undefined && v !== null && v !== '') url.searchParams.set(k, v);
        }
        const headers = { 'Accept': 'application/json' };
        const gt = localStorage.getItem('al_guest');
        if (gt) headers['X-Guest-Token'] = gt;
        const token = localStorage.getItem('al_token');
        if (token) headers['Authorization'] = 'Bearer ' + token;

        const res = await fetch(url.toString(), { headers });
        const json = await res.json();
        if (!json.success) throw new Error(json.error ?? 'API error');
        return json.data;
    }

    async function post(path, body = {}, params = {}) {
        const url = new URL(BASE + path, location.origin);
        for (const [k, v] of Object.entries(params)) {
            if (v !== undefined && v !== null && v !== '') url.searchParams.set(k, v);
        }
        const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
        const gt = localStorage.getItem('al_guest');
        if (gt) headers['X-Guest-Token'] = gt;
        const token = localStorage.getItem('al_token');
        if (token) headers['Authorization'] = 'Bearer ' + token;

        const res = await fetch(url.toString(), { method: 'POST', headers, body: JSON.stringify(body) });
        const json = await res.json();
        if (!json.success) throw new Error(json.error ?? 'API error');
        return json.data;
    }

    return {
        home: {
            featured:        ()              => request('/home.php', { section: 'featured' }),
            trendingBooks:   (limit = 12)    => request('/home.php', { section: 'trending_books', limit }),
            trendingManga:   (limit = 12)    => request('/home.php', { section: 'trending_manga', limit }),
            audiobooksNew:   (limit = 12)    => request('/home.php', { section: 'audiobooks_new', limit }),
            subject:         (subject, n=12) => request('/home.php', { section: 'subject', subject, limit: n }),
        },

        search: {
            query: (q, type = 'all', page = 1, limit = 20) =>
                request('/search.php', { q, type, page, limit }),
        },

        detail: {
            get: (id, type = 'book') => request('/detail.php', { id, type }),
        },

        browse: {
            get: (type, genre, sort, lang, page, limit) =>
                request('/browse.php', { type, genre, sort, lang, page, limit }),
        },

        trending: {
            get: (period = 'daily', limit = 20) =>
                request('/trending.php', { period, limit }),
        },

        manga: {
            search:       (q, limit, offset, tag) => request('/manga.php', { action: 'search', q, limit, offset, tag }),
            trending:     (limit = 20)             => request('/manga.php', { action: 'trending', limit }),
            chapterPages: (chapterId)              => request('/manga.php', { action: 'chapter_pages', chapter_id: chapterId }),
        },

        favorites: {
            list:   ()                => request('/favorites.php', { action: 'list' }),
            add:    (item)            => post('/favorites.php', item, { action: 'add' }),
            remove: (itemId)          => post('/favorites.php', { item_id: itemId }, { action: 'remove' }),
            check:  (itemId)          => post('/favorites.php', { item_id: itemId }, { action: 'check' }),
        },

        history: {
            list:   ()     => request('/history.php', { action: 'list' }),
            upsert: (item) => post('/history.php', item, { action: 'upsert' }),
            remove: (id)   => post('/history.php', { item_id: id }, { action: 'remove' }),
            clear:  ()     => post('/history.php', {}, { action: 'clear' }),
        },

        import: {
            list:   ()       => request('/import.php', { action: 'list' }),
            delete: (id)     => post('/import.php', { id }, { action: 'delete' }),
            serveUrl: (id)   => `/api/import.php?action=serve&id=${id}`,
            upload: async (file, onProgress) => {
                const formData = new FormData();
                formData.append('file', file);
                return new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest();
                    const url = '/api/import.php?action=upload';
                    xhr.open('POST', url);
                    const gt = localStorage.getItem('al_guest');
                    if (gt) xhr.setRequestHeader('X-Guest-Token', gt);
                    const token = localStorage.getItem('al_token');
                    if (token) xhr.setRequestHeader('Authorization', 'Bearer ' + token);
                    xhr.upload.addEventListener('progress', e => {
                        if (e.lengthComputable && onProgress) onProgress(e.loaded / e.total);
                    });
                    xhr.addEventListener('load', () => {
                        try {
                            const json = JSON.parse(xhr.responseText);
                            if (json.success) resolve(json.data);
                            else reject(new Error(json.error ?? 'Upload failed'));
                        } catch { reject(new Error('Upload failed')); }
                    });
                    xhr.addEventListener('error', () => reject(new Error('Network error')));
                    xhr.send(formData);
                });
            },
        },

        auth: {
            login:    (email, password) => post('/auth.php', { email, password }, { action: 'login' }),
            register: (name, email, password, avatar) => post('/auth.php', { name, email, password, avatar }, { action: 'register' }),
            logout:   ()                => post('/auth.php', {}, { action: 'logout' }),
            me:       ()                => request('/auth.php', { action: 'me' }),
        },
    };
})();
