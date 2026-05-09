<?php
/*
 * AnyLibrary — Free, open-source book library
 * Copyright (C) 2026  AnyLibrary Contributors
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

if (!isset($activePage)) $activePage = '';
$searchVal = htmlspecialchars(trim($_GET['q'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<header class="site-header">
    <div class="header-inner">
        <a href="/" class="logo">
            <img src="/assets/img/logo-mark.svg" alt="" class="logo-mark">
            <span>AnyLibrary</span>
        </a>
        <nav class="nav-links" aria-label="Primary">
            <a href="/"              class="nav-link <?= $activePage==='home'       ?'active':'' ?>"><span data-i18n="Home">Home</span></a>
            <a href="/browse?type=book"  class="nav-link <?= $activePage==='books'  ?'active':'' ?>"><span data-i18n="Books">Books</span></a>
            <a href="/audiobooks"    class="nav-link <?= $activePage==='audiobooks' ?'active':'' ?>"><span data-i18n="Audiobooks">Audiobooks</span></a>
            <a href="/manga"         class="nav-link <?= $activePage==='manga'      ?'active':'' ?>"><span data-i18n="Manga">Manga</span></a>
            <a href="/browse"        class="nav-link <?= $activePage==='browse'     ?'active':'' ?>"><span data-i18n="Browse">Browse</span></a>
            <a href="/trending"      class="nav-link <?= $activePage==='trending'   ?'active':'' ?>"><span data-i18n="Popular">Popular</span></a>
        </nav>
        <div class="header-center">
            <div class="search-wrap" id="search-wrap">
                <form class="search-bar" id="search-form" role="search">
                    <input type="text" id="search-input" name="q"
                           value="<?= $searchVal ?>"
                           placeholder="Search books, authors&#8230;"
                           autocomplete="off"
                           aria-label="Search"
                           aria-controls="search-dropdown"
                           aria-expanded="false">
                    <button type="submit" class="btn btn-icon" aria-label="Search">
                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <circle cx="6.5" cy="6.5" r="5" stroke="currentColor" stroke-width="1.6"/>
                            <path d="M10.5 10.5L14 14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                    </button>
                </form>
                <div class="search-dropdown" id="search-dropdown" role="listbox" aria-label="Search suggestions"></div>
            </div>
        </div>
        <div class="header-actions">
            <button class="header-btn" id="import-btn" aria-label="Import a book" title="Import Book">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
            </button>
            <button class="header-btn" id="account-btn" aria-label="Account" title="Account">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                </svg>
            </button>
            <button class="header-btn" id="settings-btn" aria-label="Settings" title="Settings">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                </svg>
            </button>
            <button class="header-btn mobile-menu-btn" id="mobile-menu-btn" aria-label="Open menu" aria-expanded="false">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
        </div>
    </div>
</header>

<!-- Settings modal -->
<div class="modal-overlay" id="settings-overlay" style="display:none;" role="dialog" aria-modal="true" aria-label="Settings">
    <div class="modal-box modal-box-wide" id="settings-box">
        <div class="modal-header">
            <div class="modal-tabs">
                <button class="modal-tab active" data-tab="appearance">Appearance</button>
                <button class="modal-tab" data-tab="reading">Reading</button>
                <button class="modal-tab" data-tab="library">Library</button>
                <button class="modal-tab" data-tab="about">About</button>
            </div>
            <button class="modal-close" id="settings-close" aria-label="Close">&#10005;</button>
        </div>
        <div class="modal-body">
            <div class="modal-tab-panel active" data-panel="appearance">
                <div class="settings-group">
                    <div class="settings-group-label">Theme</div>
                    <div class="theme-picker" id="theme-picker">
                        <div class="theme-swatch" data-theme="" style="background:#0c0c0d;outline:2px solid #3d8ef8;" title="Default (Blue)">
                            <span>Default</span>
                        </div>
                        <div class="theme-swatch" data-theme="midnight" style="background:#060610;" title="Midnight">
                            <span>Midnight</span>
                        </div>
                        <div class="theme-swatch" data-theme="forest" style="background:#080f0c;" title="Forest">
                            <span>Forest</span>
                        </div>
                        <div class="theme-swatch" data-theme="ember" style="background:#0f0b08;" title="Ember">
                            <span>Ember</span>
                        </div>
                        <div class="theme-swatch" data-theme="paper" style="background:#f4f2ee;color:#1a1a22;" title="Paper (Light)">
                            <span>Paper</span>
                        </div>
                    </div>
                </div>
                <div class="settings-row">
                    <div><div class="settings-row-label">Font</div></div>
                    <select class="select-field" id="font-picker">
                        <option value="">Default (Satoshi)</option>
                        <option value="system">System UI</option>
                        <option value="mono">Monospace</option>
                    </select>
                </div>
            </div>
            <div class="modal-tab-panel" data-panel="reading" style="display:none;">
                <div class="settings-row">
                    <div>
                        <div class="settings-row-label">Default reader font size</div>
                        <div class="settings-row-desc">Base font size for in-browser reading</div>
                    </div>
                    <select class="select-field" id="reader-font-size-picker">
                        <option value="0.9rem">Small</option>
                        <option value="1rem" selected>Medium</option>
                        <option value="1.1rem">Large</option>
                        <option value="1.25rem">X-Large</option>
                    </select>
                </div>
                <div class="settings-row">
                    <div>
                        <div class="settings-row-label">Default reader theme</div>
                    </div>
                    <select class="select-field" id="reader-theme-picker">
                        <option value="default">Default (Dark)</option>
                        <option value="sepia">Sepia</option>
                        <option value="night">Night / AMOLED</option>
                    </select>
                </div>
                <div class="settings-row">
                    <div>
                        <div class="settings-row-label">Show reading progress badge</div>
                        <div class="settings-row-desc">Display % read on book covers</div>
                    </div>
                    <label class="toggle">
                        <input type="checkbox" id="toggle-progress-badge" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="settings-row">
                    <div>
                        <div class="settings-row-label">Remember reading position</div>
                        <div class="settings-row-desc">Resume where you left off</div>
                    </div>
                    <label class="toggle">
                        <input type="checkbox" id="toggle-resume" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="settings-row">
                    <div>
                        <div class="settings-row-label">Manga reading direction</div>
                    </div>
                    <select class="select-field" id="manga-direction-picker">
                        <option value="rtl">Right to Left (Japanese)</option>
                        <option value="ltr">Left to Right (Western)</option>
                        <option value="vertical">Vertical scroll</option>
                    </select>
                </div>
            </div>
            <div class="modal-tab-panel" data-panel="library" style="display:none;">
                <div class="settings-row">
                    <div>
                        <div class="settings-row-label">Show mature content</div>
                        <div class="settings-row-desc">Show 18+ rated books and manga in browse &amp; search</div>
                    </div>
                    <label class="toggle">
                        <input type="checkbox" id="toggle-mature">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="settings-row">
                    <div>
                        <div class="settings-row-label">Preferred content language</div>
                    </div>
                    <select class="select-field" id="content-lang-picker">
                        <option value="en">English</option>
                        <option value="fr">Français</option>
                        <option value="ja">日本語</option>
                        <option value="es">Español</option>
                        <option value="de">Deutsch</option>
                        <option value="zh">中文</option>
                        <option value="ko">한국어</option>
                        <option value="pt">Português</option>
                        <option value="ar">العربية</option>
                        <option value="ru">Русский</option>
                    </select>
                </div>
                <div class="settings-row">
                    <div>
                        <div class="settings-row-label">Export my library</div>
                        <div class="settings-row-desc">Download all your imported books as a ZIP</div>
                    </div>
                    <button class="btn btn-secondary btn-sm" id="export-library-btn">Export</button>
                </div>
                <div class="settings-row">
                    <div>
                        <div class="settings-row-label">Clear reading history</div>
                        <div class="settings-row-desc">Remove all progress tracking data</div>
                    </div>
                    <button class="btn btn-ghost btn-sm" id="clear-history-btn" style="color:var(--c-red);">Clear</button>
                </div>
            </div>
            <div class="modal-tab-panel" data-panel="about" style="display:none;">
                <div class="about-section">
                    <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem;">
                        <img src="/assets/img/logo-mark.svg" alt="AnyLibrary" style="width:40px;height:40px;">
                        <div>
                            <div style="font-weight:700;font-size:1.05rem;">AnyLibrary</div>
                            <div style="font-size:0.75rem;color:var(--c-text-3);font-family:var(--font-mono);">Free. Open source. No ads.</div>
                        </div>
                    </div>
                    <p>AnyLibrary is a free, open-source online book library for reading books, manga, and audiobooks — anonymously or with an account. No subscriptions. No ads. No tracking.</p>
                    <p>Built with PHP and vanilla JS. Book data is provided by the <a href="https://openlibrary.org" target="_blank" rel="noopener noreferrer">Open Library</a> API. Manga data via <a href="https://mangadex.org" target="_blank" rel="noopener noreferrer">MangaDex</a>. Audiobooks via <a href="https://librivox.org" target="_blank" rel="noopener noreferrer">LibriVox</a>. AnyLibrary does not host any copyrighted content.</p>
                    <p>
                        <a href="https://github.com/Hexadecinull/AnyLibrary" target="_blank" rel="noopener noreferrer">&#8599; Source Code on GitHub</a>&nbsp;&nbsp;
                        <a href="/terms">Terms of Service</a>&nbsp;&nbsp;
                        <a href="/privacy">Privacy Policy</a>
                    </p>
                    <p style="color:var(--c-text-3);font-size:0.78rem;border-top:1px solid var(--c-border);padding-top:0.75rem;margin-top:0.75rem;">
                        &copy; <?= date('Y') ?> AnyLibrary Contributors &mdash; Licensed under GPL-3.0<br>
                        Book data: <a href="https://openlibrary.org/" target="_blank" rel="noopener noreferrer">Open Library (Internet Archive)</a> &middot;
                        Manga: <a href="https://mangadex.org" target="_blank" rel="noopener noreferrer">MangaDex</a> &middot;
                        Audiobooks: <a href="https://librivox.org" target="_blank" rel="noopener noreferrer">LibriVox</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Account modal -->
<div class="modal-overlay" id="account-overlay" style="display:none;" role="dialog" aria-modal="true" aria-label="Account">
    <div class="modal-box" id="account-box">
        <div class="modal-header">
            <div class="modal-tabs">
                <button class="modal-tab active" data-tab="login">Sign In</button>
                <button class="modal-tab" data-tab="register">Create Account</button>
            </div>
            <button class="modal-close" id="account-close" aria-label="Close">&#10005;</button>
        </div>
        <div class="modal-body">
            <div class="modal-tab-panel active" data-panel="login">
                <form class="auth-form" id="login-form" novalidate>
                    <div class="form-group">
                        <label class="form-label text-s" for="login-email">Email</label>
                        <input type="email" id="login-email" class="form-input" placeholder="you@example.com" autocomplete="email">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-s" for="login-password">Password</label>
                        <input type="password" id="login-password" class="form-input" placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;" autocomplete="current-password">
                    </div>
                    <button type="submit" class="btn btn-primary w-full">Sign In</button>
                    <button type="button" id="forgot-pw-btn" class="btn btn-ghost btn-sm" style="align-self:flex-start;">Forgot password?</button>
                    <div id="login-error" class="text-s" style="color:var(--c-red);display:none;"></div>
                </form>
            </div>
            <div class="modal-tab-panel" data-panel="register" style="display:none;">
                <form class="auth-form" id="register-form" novalidate>
                    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:0.75rem;">
                        <div class="avatar-upload-btn" id="avatar-upload-btn" title="Click to upload avatar (max 1 MB)">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                            </svg>
                        </div>
                        <span class="text-s" style="color:var(--c-text-3);">Optional avatar<br>Max 1&nbsp;MB &middot; JPG / PNG / GIF</span>
                        <input type="file" id="avatar-input" accept="image/jpeg,image/png,image/gif" style="display:none;">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-s" for="reg-name">Display name</label>
                        <input type="text" id="reg-name" class="form-input" placeholder="Your name" autocomplete="name">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-s" for="reg-email">Email</label>
                        <input type="email" id="reg-email" class="form-input" placeholder="you@example.com" autocomplete="email">
                    </div>
                    <div class="form-group">
                        <label class="form-label text-s" for="reg-password">Password</label>
                        <input type="password" id="reg-password" class="form-input" placeholder="Min 8 characters" autocomplete="new-password">
                        <div class="password-strength" style="margin-top:0.35rem;">
                            <div class="password-strength-fill" id="pw-strength-fill"></div>
                        </div>
                        <div class="text-xs" id="pw-strength-label" style="color:var(--c-text-3);margin-top:0.2rem;"></div>
                    </div>
                    <button type="submit" class="btn btn-primary w-full">Create Account</button>
                    <div id="register-error" class="text-s" style="color:var(--c-red);display:none;"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Import book modal -->
<div class="modal-overlay" id="import-overlay" style="display:none;" role="dialog" aria-modal="true" aria-label="Import a book">
    <div class="modal-box modal-box-wide" id="import-box">
        <div class="modal-header">
            <div class="modal-tabs">
                <button class="modal-tab active" data-tab="file">Import File</button>
                <button class="modal-tab" data-tab="url">Import by URL</button>
                <button class="modal-tab" data-tab="shelf">My Shelf</button>
            </div>
            <button class="modal-close" id="import-close" aria-label="Close">&#10005;</button>
        </div>
        <div class="modal-body">
            <div class="modal-tab-panel active" data-panel="file">
                <div class="drop-zone" id="book-drop-zone">
                    <div class="drop-zone-icon">&#128218;</div>
                    <p>Drop an e-book file here or click to browse</p>
                    <small>EPUB &middot; PDF &middot; MOBI &middot; TXT &middot; HTML &middot; CBZ &middot; CBR</small>
                    <input type="file" id="book-file-input" accept=".epub,.pdf,.mobi,.txt,.html,.htm,.cbz,.cbr" style="display:none;">
                </div>
                <div id="import-progress" style="display:none;margin-top:1rem;">
                    <div class="progress-bar"><div class="progress-fill" id="import-progress-fill" style="width:0%"></div></div>
                    <p class="text-s" style="color:var(--c-text-3);margin-top:0.4rem;" id="import-status">Processing…</p>
                </div>
            </div>
            <div class="modal-tab-panel" data-panel="url" style="display:none;">
                <div class="form-group" style="margin-bottom:1rem;">
                    <label class="form-label" for="import-url-input">Book URL</label>
                    <input type="url" id="import-url-input" class="form-input" placeholder="https://example.com/book.epub">
                </div>
                <p class="text-s" style="color:var(--c-text-3);margin-bottom:1rem;">Paste the direct URL of an EPUB, PDF, or plain-text file to import it into your shelf.</p>
                <button class="btn btn-primary" id="import-url-btn">Import</button>
            </div>
            <div class="modal-tab-panel" data-panel="shelf" style="display:none;">
                <div id="import-shelf-list" class="shelf-grid"></div>
                <div id="import-shelf-empty" class="empty-state" style="display:none;">
                    <div class="empty-state-icon">&#128218;</div>
                    <h3>Your shelf is empty</h3>
                    <p>Import EPUB, PDF, or text files to build your personal offline library.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile drawer -->
<div class="mobile-drawer" id="mobile-drawer" style="display:none;">
    <div class="mobile-search">
        <input type="text" id="mobile-search-input" placeholder="Search books, authors&#8230;" autocomplete="off">
    </div>
    <a href="/"              class="mobile-nav-item <?= $activePage==='home'       ?'active':'' ?>">Home</a>
    <a href="/browse?type=book"  class="mobile-nav-item <?= $activePage==='books'  ?'active':'' ?>">Books</a>
    <a href="/audiobooks"    class="mobile-nav-item <?= $activePage==='audiobooks' ?'active':'' ?>">Audiobooks</a>
    <a href="/manga"         class="mobile-nav-item <?= $activePage==='manga'      ?'active':'' ?>">Manga</a>
    <a href="/browse"        class="mobile-nav-item <?= $activePage==='browse'     ?'active':'' ?>">Browse All</a>
    <a href="/trending"      class="mobile-nav-item <?= $activePage==='trending'   ?'active':'' ?>">Popular</a>
    <a href="/favorites"     class="mobile-nav-item <?= $activePage==='favorites'  ?'active':'' ?>">Favorites</a>
    <a href="/history"       class="mobile-nav-item <?= $activePage==='history'    ?'active':'' ?>">Reading History</a>
    <hr style="border-color:var(--c-border);margin:0.25rem 0;">
    <a href="#" class="mobile-nav-item" id="mobile-import-btn">&#128218; Import Book</a>
    <a href="#" class="mobile-nav-item" id="mobile-account-btn">&#128100; Account</a>
    <a href="#" class="mobile-nav-item" id="mobile-settings-btn">&#9881;&#65039; Settings</a>
</div>
