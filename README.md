# Multi-tenant Laravel SaaS boilerplate

[![CI](https://github.com/ajaythakkar/multi-tenant-laravel-saas-boilerplate/actions/workflows/ci.yml/badge.svg)](https://github.com/ajaythakkar/multi-tenant-laravel-saas-boilerplate/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A starting point for multi-tenant SaaS apps built on Laravel. Every tenant gets its own subdomain and its own database.

## Features

- Tenant sign-up page on the central domain: pick a subdomain, and the app creates a tenant, a domain and a database.
- One database per tenant. Tenant databases are created and migrated automatically when the tenant is created.
- Tenancy is identified by domain (`acme.example.com`), powered by [stancl/tenancy v3](https://tenancyforlaravel.com/docs/v3/).
- Central routes (`routes/web.php`) and tenant routes (`routes/tenant.php`) are kept apart. Central pages are not served on tenant domains, and unknown subdomains return 404.
- Subdomain validation covers DNS-safe names, reserved names (`www`, `admin`, `api`, ...), uniqueness and rate limiting.
- Feature tests and GitHub Actions CI (tests, `composer audit`, `npm audit`). Dependabot is configured.

## Stack

| Part | Version |
| --- | --- |
| PHP | 8.3 or newer |
| Laravel | 13 |
| stancl/tenancy | 3.x |
| Frontend build | Vite |
| Database | MySQL 8 / MariaDB (or PostgreSQL) |

## Requirements

- PHP 8.3+ with the usual Laravel extensions (`pdo_mysql`, `mbstring`, `openssl`, ...)
- Composer 2
- Node.js 20+ and npm
- A MySQL user that is allowed to `CREATE DATABASE`, because each tenant gets a new database

## Installation

```bash
git clone https://github.com/ajaythakkar/multi-tenant-laravel-saas-boilerplate.git
cd multi-tenant-laravel-saas-boilerplate

composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```dotenv
# The first domain is the parent of tenant subdomains (acme.localhost, acme.example.com, ...)
CENTRAL_DOMAINS=localhost,127.0.0.1

DB_CONNECTION=mysql
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

Create the central database, then:

```bash
php artisan migrate
npm install
npm run build
```

## Local development

```bash
php artisan serve
```

1. Open <http://localhost:8000/register> and register the subdomain `acme`.
2. Open <http://acme.localhost:8000>. You see the tenant app with the tenant's id.

Most browsers resolve `*.localhost` to `127.0.0.1` without extra setup. If yours does not, add `127.0.0.1 acme.localhost` to your hosts file.

## How it works

| Where | What |
| --- | --- |
| `routes/web.php` | Central routes, bound to every domain in `CENTRAL_DOMAINS` |
| `routes/tenant.php` | Tenant routes (`InitializeTenancyByDomain` + `PreventAccessFromCentralDomains`) |
| `app/Http/Controllers/RegisterTenantController.php` | Validates the subdomain and creates the tenant + domain |
| `app/Providers/TenancyServiceProvider.php` | Tenancy events: create, migrate and delete tenant databases |
| `app/Models/Tenant.php` | Tenant model (extra attributes like `plan` go in the `data` JSON column) |
| `database/migrations` | Central migrations (`tenants`, `domains`, ...) |
| `database/migrations/tenant` | Migrations run inside every tenant database |
| `config/tenancy.php` | Tenancy settings (bootstrappers, database prefix `tenant`, ...) |

Tenant database names are `tenant` + the tenant UUID.

Useful commands:

```bash
php artisan tenants:migrate            # run tenant migrations for all tenants
php artisan tenants:migrate --tenants=<id>
php artisan tenants:seed
php artisan tenants:list
```

### Cache, sessions and queues

The defaults in `.env.example` are `CACHE_STORE=file`, `SESSION_DRIVER=file` and `QUEUE_CONNECTION=sync`. These defaults work on both central and tenant domains without extra tables.

- **Per-tenant cache:** use Redis or Memcached, then enable `CacheTenancyBootstrapper` in `config/tenancy.php`. It needs a cache store that supports tags.
- **Queues:** in production, create tenant databases in a queued job (`shouldBeQueued(true)` in `TenancyServiceProvider`). Run a real queue worker.

See the [tenancy docs](https://tenancyforlaravel.com/docs/v3/) for details.

## Server setup

Point a wildcard DNS record (`*.example.com`) at the server, and set `CENTRAL_DOMAINS=example.com` in `.env`. The document root must be the `public/` directory.

**Apache**

```apache
<VirtualHost *:80>
    ServerName example.com
    ServerAlias *.example.com
    DocumentRoot /var/www/example.com/public

    <Directory /var/www/example.com/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

**Nginx**

```nginx
server {
    listen 80;
    server_name example.com *.example.com;
    root /var/www/example.com/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

For HTTPS, use a wildcard certificate, for example Let's Encrypt with a DNS challenge.

> **Note:** Shared hosting / cPanel usually does not let the app create databases on the fly. This setup needs a server where the DB user has `CREATE DATABASE` rights.

## Testing

```bash
php artisan test
```

The tests use in-memory SQLite and do not touch your MySQL databases.

## Upgrading from the Laravel 6 version

The first version of this project used Laravel 6 and stancl/tenancy v2. This version is a rewrite on Laravel 13 and tenancy v3. There is no in-place upgrade path. Tenancy v3 uses a different tenant storage layout, so start a fresh install and move your code across.

## Security

Please do not open public issues for security problems. See [SECURITY.md](SECURITY.md).

## License

Released under the [MIT License](LICENSE). Third-party packages keep their own licenses.
