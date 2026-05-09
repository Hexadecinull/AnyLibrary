# AnyLibrary — Installation Guide

## Requirements

- PHP **8.1+** with the `pdo_mysql`, `json`, and `fileinfo` extensions
- MySQL **5.7+** or MariaDB **10.3+**
- A web server with `.htaccess` / `mod_rewrite` support (Apache), or equivalent Nginx config
- Writable `storage/books/` directory

---

## Step 1 — Upload files

Clone or download the repository and upload all files to your web server's document root (or a subdirectory).

```bash
git clone https://github.com/Hexadecinull/AnyLibrary.git
```

---

## Step 2 — Create the database

Log into your hosting control panel (cPanel, DirectAdmin, etc.) and create a new MySQL database and user. Note the credentials.

Import the schema:

```bash
mysql -u YOUR_USER -p YOUR_DB < docs/schema.sql
```

Or use phpMyAdmin → Import → select `docs/schema.sql`.

---

## Step 3 — Configure AnyLibrary

Copy the example config:

```bash
cp includes/config.example.php includes/config.php
```

Edit `includes/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'anylibrary');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');

// Generate once: php -r "echo bin2hex(random_bytes(32));"
define('JWT_SECRET', 'REPLACE_WITH_A_LONG_RANDOM_STRING');

define('APP_URL', 'https://yourdomain.example.com');
```

---

## Step 4 — Storage directory

Create and set permissions on the book upload directory:

```bash
mkdir -p storage/books
chmod 755 storage/books
```

> On shared hosting, the web server user (e.g. `www-data`) must be able to write here. If `755` doesn't work, try `777` only if your host requires it.

---

## Step 5 — Web server

### Apache (`.htaccess` already included)

The included `.htaccess` handles clean URLs and sets PHP limits. Ensure `mod_rewrite` and `AllowOverride All` are enabled.

### Nginx

```nginx
server {
    listen 80;
    server_name yourdomain.example.com;
    root /var/www/anylibrary;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location /storage/books/ {
        deny all;
    }
}
```

> **Important:** The `storage/books/` directory must **not** be publicly accessible. The Nginx config above denies it. On Apache, a `.htaccess` in `storage/` with `Deny from all` is recommended as an extra precaution (the `api/import.php` endpoint serves files with access control).

---

## Step 6 — Visit your site

Navigate to your domain. The homepage should load and immediately display trending books from Open Library.

If you see a blank page, check:
- `APP_ENV` is set to `'development'` temporarily to see PHP errors
- Database credentials are correct
- `storage/books/` is writable

---

## InfinityFree setup notes

InfinityFree (free shared PHP hosting) works well with AnyLibrary:

1. Create an account at [infinityfree.com](https://infinityfree.com)
2. Create a hosting account → note the MySQL host (e.g. `sql123.infinityfree.com`)
3. Database name and user both start with `epiz_XXXXXXX_`
4. Upload files via FTP (FileZilla) to `htdocs/`
5. Import `docs/schema.sql` via Softaculous phpMyAdmin
6. Set `DB_HOST` to the value shown in your control panel

---

## Updating

```bash
git pull origin main
```

Re-run any new migrations from `docs/schema.sql` if the schema has changed (check `CHANGELOG.md`).
