/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 * (GPL-3.0 license)
 */

const ImportExport = (() => {

    const ACCEPTED_EXTS = ['epub', 'pdf', 'mobi', 'txt', 'html', 'htm', 'cbz', 'cbr'];

    function ext(filename) {
        return filename.split('.').pop().toLowerCase();
    }

    function isAllowed(file) {
        return ACCEPTED_EXTS.includes(ext(file.name));
    }

    async function uploadFile(file) {
        const dropZone     = document.getElementById('book-drop-zone');
        const progressWrap = document.getElementById('import-progress');
        const progressFill = document.getElementById('import-progress-fill');
        const statusEl     = document.getElementById('import-status');

        if (!isAllowed(file)) {
            UI.toast(`Format not supported: .${ext(file.name)}`, 'error');
            return;
        }

        if (progressWrap) progressWrap.style.display = '';
        if (dropZone)     dropZone.style.display      = 'none';
        if (statusEl)     statusEl.textContent        = `Uploading ${file.name}…`;

        try {
            const result = await API.import.upload(file, pct => {
                if (progressFill) progressFill.style.width = Math.round(pct * 100) + '%';
                if (statusEl)     statusEl.textContent     = `Uploading… ${Math.round(pct * 100)}%`;
            });

            if (progressFill) progressFill.style.width = '100%';
            if (statusEl)     statusEl.textContent = '✓ Import complete!';
            UI.toast(`"${result.title}" imported to your shelf`, 'success');

            setTimeout(() => {
                if (progressWrap) progressWrap.style.display = 'none';
                if (dropZone)     dropZone.style.display      = '';
                if (progressFill) progressFill.style.width    = '0%';
                loadShelf();
            }, 1800);
        } catch (err) {
            if (progressWrap) progressWrap.style.display = 'none';
            if (dropZone)     dropZone.style.display      = '';
            UI.toast('Import failed: ' + err.message, 'error');
        }
    }

    async function loadShelf() {
        const shelfList  = document.getElementById('import-shelf-list');
        const shelfEmpty = document.getElementById('import-shelf-empty');
        if (!shelfList) return;

        shelfList.innerHTML = '<div style="color:var(--c-text-3);font-size:0.85rem;padding:1rem 0;">Loading your shelf…</div>';

        try {
            const items = await API.import.list();
            if (!items.length) {
                shelfList.innerHTML = '';
                if (shelfEmpty) shelfEmpty.style.display = '';
                return;
            }
            if (shelfEmpty) shelfEmpty.style.display = 'none';
            shelfList.innerHTML = items.map(item => `
<div class="card" style="cursor:pointer;" title="${UI.escHtml(item.original_name)}">
    <div class="card-poster" style="aspect-ratio:2/3;background:var(--c-bg-3);display:flex;align-items:center;justify-content:center;font-size:2rem;color:var(--c-text-3);">
        ${extIcon(item.file_ext)}
    </div>
    <div class="card-body">
        <div class="card-title">${UI.escHtml(item.title)}</div>
        <div class="card-meta">
            <span class="type-badge type-badge-imported">${item.file_ext.toUpperCase()}</span>
            <span>${UI.formatSize(item.file_size)}</span>
        </div>
        <div style="display:flex;gap:0.4rem;margin-top:0.5rem;">
            <a href="/reader.php?id=${encodeURIComponent(item.id)}&type=imported&src=${encodeURIComponent(API.import.serveUrl(item.id))}"
               class="btn btn-primary btn-sm" style="flex:1;">Read</a>
            <button class="btn btn-ghost btn-sm" data-delete-id="${item.id}" title="Delete from shelf" style="color:var(--c-red);">✕</button>
        </div>
    </div>
</div>`).join('');

            shelfList.querySelectorAll('[data-delete-id]').forEach(btn => {
                btn.addEventListener('click', async e => {
                    e.stopPropagation();
                    const id = btn.dataset.deleteId;
                    if (!confirm('Remove this book from your shelf?')) return;
                    try {
                        await API.import.delete(id);
                        UI.toast('Removed from shelf', 'success');
                        loadShelf();
                    } catch {
                        UI.toast('Could not delete', 'error');
                    }
                });
            });
        } catch (err) {
            shelfList.innerHTML = `<p style="color:var(--c-text-3);font-size:0.85rem;">${UI.escHtml(err.message)}</p>`;
        }
    }

    function extIcon(ext) {
        const icons = { epub: '📚', pdf: '📄', mobi: '📖', txt: '📝', html: '🌐', htm: '🌐', cbz: '🖼️', cbr: '🖼️' };
        return icons[ext] ?? '📂';
    }

    async function exportLibrary() {
        try {
            const items = await API.import.list();
            if (!items.length) {
                UI.toast('Your shelf is empty — nothing to export', 'default');
                return;
            }
            const manifest = {
                exported_at: new Date().toISOString(),
                app: 'AnyLibrary',
                version: '1.0',
                books: items.map(i => ({
                    id:            i.id,
                    title:         i.title,
                    original_name: i.original_name,
                    format:        i.file_ext,
                    size_bytes:    i.file_size,
                    import_date:   i.created_at,
                    download_url:  API.import.serveUrl(i.id),
                })),
            };
            const blob = new Blob([JSON.stringify(manifest, null, 2)], { type: 'application/json' });
            const url  = URL.createObjectURL(blob);
            const a    = document.createElement('a');
            a.href     = url;
            a.download = `anylibrary-export-${Date.now()}.json`;
            document.body.appendChild(a);
            a.click();
            a.remove();
            URL.revokeObjectURL(url);
            UI.toast(`Exported manifest for ${items.length} book${items.length > 1 ? 's' : ''}`, 'success');
        } catch (err) {
            UI.toast('Export failed: ' + err.message, 'error');
        }
    }

    function init() {
        const dropZone  = document.getElementById('book-drop-zone');
        const fileInput = document.getElementById('book-file-input');
        const urlInput  = document.getElementById('import-url-input');
        const urlBtn    = document.getElementById('import-url-btn');
        const exportBtn = document.getElementById('export-library-btn');

        if (dropZone) {
            dropZone.addEventListener('click', () => fileInput?.click());
            dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
            dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
            dropZone.addEventListener('drop', e => {
                e.preventDefault();
                dropZone.classList.remove('drag-over');
                const file = e.dataTransfer?.files[0];
                if (file) uploadFile(file);
            });
        }

        fileInput?.addEventListener('change', e => {
            const file = e.target.files?.[0];
            if (file) uploadFile(file);
            e.target.value = '';
        });

        urlBtn?.addEventListener('click', async () => {
            const url = urlInput?.value.trim();
            if (!url) { UI.toast('Enter a URL', 'error'); return; }
            try {
                const res  = await fetch(url, { method: 'HEAD' });
                const disp = res.headers.get('content-disposition') ?? '';
                const name = disp.match(/filename="?([^"]+)"?/)?.[1]
                    ?? url.split('/').pop()?.split('?')[0]
                    ?? 'book.epub';
                const type = res.headers.get('content-type') ?? '';
                const fileRes = await fetch(url);
                const blob    = await fileRes.blob();
                const file    = new File([blob], name, { type });
                await uploadFile(file);
            } catch (err) {
                UI.toast('Failed to fetch URL: ' + err.message, 'error');
            }
        });

        exportBtn?.addEventListener('click', exportLibrary);

        const importOverlay = document.getElementById('import-overlay');
        if (importOverlay) {
            const observer = new MutationObserver(() => {
                if (importOverlay.style.display !== 'none') {
                    const activePanel = importOverlay.querySelector('.modal-tab-panel[style=""]');
                    if (activePanel?.dataset.panel === 'shelf') loadShelf();
                }
            });
            observer.observe(importOverlay, { attributes: true, attributeFilter: ['style'] });

            importOverlay.querySelectorAll('.modal-tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    if (tab.dataset.tab === 'shelf') loadShelf();
                });
            });
        }
    }

    return { init, loadShelf, exportLibrary, uploadFile };
})();
