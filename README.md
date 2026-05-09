# AnyLibrary

**Free. Open source. No ads. No subscriptions.**

AnyLibrary is a lightweight, self-hostable online book library built with PHP and vanilla JavaScript. Browse, read, and listen to books, manga, and audiobooks — anonymously or with an account. No tracking. No paywalls.

![AnyLibrary Screenshot](https://raw.githubusercontent.com/Hexadecinull/AnyLibrary/main/docs/screenshot.png)

---

## Features

- **Books** — Millions of titles via the [Open Library](https://openlibrary.org/) API (Internet Archive). No API key required.
- **Manga** — Thousands of series via [MangaDex](https://mangadex.org/). Full chapter reading, tag filtering, RTL/LTR support.
- **Audiobooks** — Thousands of public-domain recordings via [LibriVox](https://librivox.org/). Full in-browser audio player.
- **In-browser reader** — Read books online with adjustable font size, font family, sepia/night/dark themes, and reading progress tracking.
- **Import & Export** — Import EPUB, PDF, MOBI, TXT, HTML, CBZ, and CBR files into your personal shelf. Export a manifest of your library.
- **Printing** — Print any book's detail page or reader content directly to PDF or paper via the browser print dialog.
- **Favorites** — Bookmark any title across all types.
- **Reading history** — Tracks your progress (% read, last position) per book, chapter, and audio track.
- **No account required** — All features work for anonymous guests via a persistent guest token stored in `localStorage`.
- **Themes** — Default, Midnight, Forest, Ember, and Paper (light) — plus reader-specific Sepia and AMOLED Night modes.
- **Zero ads** — AnyLibrary is and will always be ad-free and tracker-free.
- **GPL-3.0** — Fully open source.

---

## Tech stack

| Layer       | Technology                        |
|-------------|-----------------------------------|
| Backend     | PHP 8.1+, PDO/MySQL               |
| Frontend    | Vanilla JS (ES2022, no framework) |
| Styling     | Pure CSS (custom properties)      |
| Book data   | Open Library REST API             |
| Manga data  | MangaDex REST API                 |
| Audiobooks  | LibriVox REST API                 |
| Hosting     | Any shared PHP host (e.g. InfinityFree, NearlyFreeSpeech) |

---

## Self-hosting (quick start)

1. **Clone** the repo and upload files to your web server.
2. **Copy** `includes/config.example.php` to `includes/config.php` and fill in your database credentials and a random `JWT_SECRET`.
3. **Import** `docs/schema.sql` into your MySQL database.
4. **Set permissions**: `storage/books/` directory must be writable by the web server (`chmod 755`).
5. **Set the document root** to the project root (or configure `.htaccess` accordingly).
6. Visit your site — it should work immediately.

See [`docs/INSTALL.md`](docs/INSTALL.md) for the full step-by-step guide, including InfinityFree setup.

---

## Configuration reference

See [`includes/config.example.php`](includes/config.example.php) for all options.

| Constant              | Default         | Description                              |
|-----------------------|-----------------|------------------------------------------|
| `DB_HOST`             | `localhost`     | MySQL host                               |
| `DB_NAME`             | `anylibrary`    | Database name                            |
| `DB_USER`             | `root`          | Database user                            |
| `DB_PASS`             | *(empty)*       | Database password                        |
| `JWT_SECRET`          | *(required)*    | Static random string for session signing |
| `APP_URL`             | *(required)*    | Full public URL, no trailing slash       |
| `MAX_IMPORT_MB`       | `50`            | Max file size for book imports           |
| `IMPORT_STORAGE_PATH` | `storage/books/`| Where imported files are stored on disk  |

---

## Project structure

```
AnyLibrary/
├── api/                   PHP API endpoints (JSON)
│   ├── auth.php
│   ├── browse.php
│   ├── detail.php
│   ├── favorites.php
│   ├── history.php
│   ├── home.php
│   ├── import.php
│   ├── manga.php
│   ├── search.php
│   └── trending.php
├── assets/
│   ├── css/               All stylesheets
│   ├── fonts/             Self-hosted WOFF2 fallbacks
│   ├── icons/             SVG sprite
│   ├── img/               Logos and placeholder art
│   └── js/                Vanilla JS modules
├── docs/
│   ├── schema.sql
│   ├── INSTALL.md
│   └── API.md
├── includes/              PHP shared includes
│   ├── auth.php
│   ├── bootstrap.php
│   ├── config.example.php
│   ├── db.php
│   ├── footer.php
│   ├── head.php
│   ├── nav.php
│   ├── openlibrary.php    Open Library + MangaDex + LibriVox wrappers
│   └── response.php
├── storage/books/         Uploaded book files (gitignored)
├── index.php              Homepage
├── browse.php
├── detail.php
├── reader.php
├── manga.php
├── audiobooks.php
├── trending.php
├── search.php
├── favorites.php
├── history.php
├── settings.php
├── login.php
├── register.php
└── 404.php
```

---

## Data sources & licensing

AnyLibrary does **not** host any copyrighted content.

| Source        | License / Notes                                               |
|---------------|---------------------------------------------------------------|
| Open Library  | [CC0 / Open Data](https://openlibrary.org/developers)         |
| MangaDex      | Community-scanlated; see [MangaDex ToS](https://mangadex.org/about)|
| LibriVox      | Public domain recordings; [CC0](https://librivox.org/pages/public-domain)|

---

## Contributing

Pull requests welcome! See [`CONTRIBUTING.md`](CONTRIBUTING.md) for guidelines.

Issues: [GitHub Issues](https://github.com/Hexadecinull/AnyLibrary/issues)

---

## License

**GPL-3.0** — See [`LICENSE`](LICENSE).

Copyright © 2026 SSMG4 & AnyLibrary Contributors.
