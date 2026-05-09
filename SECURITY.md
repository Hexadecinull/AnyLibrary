# Security Policy

## Supported Versions

Only the latest release on the `main` branch receives security fixes.

## Reporting a Vulnerability

**Please do not open a public GitHub issue for security vulnerabilities.**

Email the maintainer directly or use GitHub's private security advisory feature:
`Settings → Security → Advisories → New draft advisory`

Include:
- Description of the vulnerability
- Steps to reproduce
- Potential impact
- Any suggested fix if you have one

You can expect an acknowledgement within 72 hours.

---

## Security Model

AnyLibrary is a PHP web application intended for self-hosting. Here is what is and is not in scope:

**In scope:**
- SQL injection vulnerabilities
- Authentication bypass or session fixation
- Stored or reflected XSS
- Path traversal or arbitrary file read/write
- Information disclosure (e.g. stack traces in production)

**Out of scope:**
- Vulnerabilities in third-party embed providers (VidSrc, AutoEmbed, etc.) — report those upstream
- Clickjacking via the player iframe (by design; embed providers require iframe embedding)
- Phishing attacks hosted on unrelated domains
- Denial-of-service via volume (no rate limiting SLA)

---

## Hardening Checklist for Operators

Before deploying to production, verify:

- [ ] `includes/config.php` is not accessible via HTTP (`.htaccess` blocks it by default)
- [ ] `JWT_SECRET` is a long, static random string — not the placeholder value
- [ ] `APP_ENV` is set to `production`
- [ ] PHP `display_errors` is `Off` in `php.ini`
- [ ] The database user has only `SELECT, INSERT, UPDATE, DELETE` privileges — not `DROP, CREATE, ALTER`
- [ ] HTTPS is enforced (`.htaccess` redirect is enabled)
- [ ] File permissions: `config.php` at `640`, web root at `644`/`755`

---

## Known Limitations

- No rate limiting on API endpoints or login attempts
- Guest tokens are UUIDs stored in `localStorage` — they are not cryptographically bound to a session
- The TMDB API key is stored server-side and never exposed to clients
