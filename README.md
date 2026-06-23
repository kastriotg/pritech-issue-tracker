# Pritech Issue Tracker

A Laravel 13 mini issue tracker for managing projects, issues, tags, comments, and issue members. The app uses Breeze authentication, Blade views, Alpine/Vite frontend assets, SQLite by default, and Pest tests.

## Requirements

- PHP 8.3+
- Composer
- Node.js and npm
- SQLite support enabled for PHP

## Setup

Clone the project, then create the local SQLite database file if it does not exist:

```powershell
if (!(Test-Path database/database.sqlite)) { New-Item -ItemType File database/database.sqlite }
```

Install everything from the project root:

```bash
composer run setup
```

This command installs PHP dependencies, creates `.env` if needed, generates the app key, runs migrations, installs npm dependencies, and builds frontend assets.

For demo data, run:

```bash
php artisan migrate:fresh --seed
```

The seeded demo login is:

```text
Email: test@example.com
Password: password
```

You can also register a new user from the app.

## Run Locally

Start the Laravel server, queue worker, and Vite dev server:

```bash
composer run dev
```

Then open:

```text
http://127.0.0.1:8000
```

If you only need the backend server:

```bash
php artisan serve
```

If frontend changes are not reflected, run one of:

```bash
npm run dev
npm run build
```

## Useful Commands

Run all tests:

```bash
php artisan test --compact
```

Run the main issue tracker feature tests:

```bash
composer run test:entities
```

Format PHP code:

```bash
vendor/bin/pint --format agent
```

Clear cached config:

```bash
php artisan config:clear
```

## Main Features

- Authenticated project CRUD
- Issue CRUD with status, priority, tag, and text search filters
- Tag creation and AJAX tag attach/detach
- AJAX paginated comments and comment creation
- Issue member attach/detach support
- Factories, seeders, and Pest feature coverage
