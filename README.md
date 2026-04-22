# RPG Framework

A lightweight and high-performance PHP framework designed for simplicity and flexibility.

## What's New

- Composer project support (`composer.json`)
- Composer autoload integration in bootstrap (`system/boot.php`)
- Development server command: `composer serve`
- Query and model safety improvements with prepared statements in core model methods
- Routing/query parsing reliability fixes
- Safer request helper accessors (`get/post/file/cookie`) for missing keys
- Log reader resilience when log files do not exist yet
- Session cleanup optimization

## Requirements

- PHP 7.4+
- Composer 2+

## Quick Start (Composer)

```bash
composer install
composer dump-autoload
composer serve
```

Open:

- `http://127.0.0.1:8000`

## Project Structure

- `public/` → web root and entrypoint (`public/index.php`)
- `app/controllers/` → route controllers
- `app/models/` → application models
- `app/views/` → view files
- `app/root/config.php` → app configuration
- `system/` → framework core

## Configuration

Application configuration file:

- `app/root/config.php`

Set your database connection details there:

- driver
- host
- user
- password
- db name

## Routing

Routes are mapped by controller filename/classname in `app/controllers`.

For example, URL:

- `/contact`

maps to:

- `app/controllers/contact.php`

Example:

```php
<?php

class contact extends controller
{
    public function main()
    {
        echo "Hello World!";
    }
}
```

## Development Server

Run local development server:

```bash
composer serve
```

Equivalent command:

```bash
php -S 127.0.0.1:8000 -t public
```

## Web Server Setup

### Apache

An `.htaccess` file is included in `public/`.

### Nginx

```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/html/rpg/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ ^/index\.php(/|$) {
        fastcgi_hide_header X-Powered-By;
        include fastcgi_params;
        fastcgi_pass unix:/run/php-fpm/www.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Defaults

Default system settings are in `system/settings.php`.

- `$index`: default controller for `/`
- `$not_found`: fallback controller for unknown routes

## Logs

Framework logs are stored under:

- `system/logs/access/`
- `system/logs/error/`

Composer install/update scripts ensure these directories exist.

## License

MIT
