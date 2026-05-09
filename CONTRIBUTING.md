# Contributing to AnyLibrary

First off, thank you for taking the time to contribute. AnyLibrary is a small open-source project and every improvement counts.

---

## Ways to Contribute

- **Bug reports** — open an issue with reproduction steps and your environment
- **Bug fixes** — fork, fix, and submit a pull request
- **New embed providers** — if you know a reliable free embed source, add it to `api/sources.php`
- **UI improvements** — CSS and JS changes are welcome, keep it vanilla (no frameworks)
- **Documentation** — fix typos, improve clarity, add examples

---

## Development Setup

```bash
# 1. Fork and clone
git clone https://github.com/YOUR_USERNAME/streamsuite.git
cd streamsuite

# 2. Create a local MySQL database and import the schema
mysql -u root -p -e "CREATE DATABASE streamsuite_dev;"
mysql -u root -p streamsuite_dev < docs/schema.sql

# 3. Configure
cp includes/config.example.php includes/config.php
# Edit includes/config.php with your local DB credentials and TMDB API key

# 4. Serve locally (PHP built-in server)
php -S localhost:8080

# 5. Open http://localhost:8080
```

You'll need a free [TMDB API key](https://www.themoviedb.org/settings/api). Registration takes about a minute.

---

## Code Style

**PHP**
- PSR-12 formatting: 4-space indentation, opening braces on same line for control structures
- Strict types preferred: use `(int)`, `(string)` casts explicitly
- No inline comments — use clear function and variable names instead
- All files must include the GPL-3.0 license header block
- Use `never` return type for functions that always call `exit`

**JavaScript**
- Vanilla ES2022 — no frameworks, no build step
- `const` by default, `let` only when reassignment is needed, never `var`
- Arrow functions for callbacks, named functions for methods
- All files must include the GPL-3.0 license header block

**CSS**
- Variables from `tokens.css` only — no magic numbers
- Mobile-first media queries
- No `!important` unless absolutely unavoidable

---

## Pull Request Process

1. Fork the repository and create a branch: `git checkout -b fix/issue-description`
2. Make your changes and confirm the PHP syntax check passes:
   ```bash
   find . -name "*.php" -print0 | xargs -0 -n1 php -l
   ```
3. Open a pull request with a clear description of what changed and why
4. Link any related issues with `Closes #NNN`

---

## Adding an Embed Provider

Edit the `EMBED_PROVIDERS` constant in `api/sources.php`:

```php
'myprovider' => [
    'movie' => 'https://myprovider.example/movie/{tmdb_id}',
    'tv'    => 'https://myprovider.example/tv/{tmdb_id}/{season}/{episode}',
    'label' => 'MyProvider',
    'priority' => 7,
],
```

Lower `priority` = tried first. Please verify the provider is reliably free before submitting.

---

## Reporting Issues

Open a GitHub issue and include:
- What you expected to happen
- What actually happened
- Steps to reproduce
- PHP version, MySQL version, server environment
- Browser console errors if applicable

---

## License

By contributing, you agree your code will be licensed under [GPL-3.0](LICENSE).
