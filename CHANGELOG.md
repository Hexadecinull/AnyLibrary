# Changelog

All notable changes to AnyLibrary are documented here.
This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.0.0] — 2026-05-09

### Added
- Initial release — forked and fully repurposed from StreamSuite
- Book browsing via Open Library API (no API key required)
- Manga browsing and reading via MangaDex API
- Audiobook browsing and in-browser player via LibriVox API
- In-browser book reader with:
  - Adjustable font size (S / M / L / XL)
  - Font family switcher (Satoshi, Georgia, Times, System UI, Mono)
  - Dark / Sepia / AMOLED Night reader themes
  - Per-session reading progress tracking (scroll %)
  - Print / Save as PDF support
- Import system: EPUB, PDF, MOBI, TXT, HTML, CBZ, CBR
  - Drag-and-drop import
  - URL import
  - Personal shelf with file management
- Export library manifest as JSON
- Favorites (books, manga, audiobooks) — guest and authenticated
- Reading history with progress tracking — guest and authenticated
- Anonymous guest mode (all features via `al_guest` localStorage token)
- User accounts (register / login / avatar upload)
- Full theming system: Default, Midnight, Forest, Ember, Paper (light)
- Subject/genre filtering and browsing
- Global search across books, manga, and audiobooks
- Trending books via Open Library trending API (daily / weekly / monthly / yearly)
- Manga chapter reader with RTL/LTR toggle
- Audiobook multi-track player with speed control (1×, 1.25×, 1.5×, 1.75×, 2×)
- Settings modal: Appearance, Reading, Library, About tabs
- Keyboard shortcut: `Ctrl+K` / `Cmd+K` to focus search
- Mobile-responsive design with drawer nav
- Clean URL routing via `.htaccess`
- Security headers (X-Content-Type-Options, X-Frame-Options, Referrer-Policy)
- GPL-3.0 license

---

*Previous history: AnyLibrary was built from StreamSuite (v1.x), a streaming client for movies and TV.*
