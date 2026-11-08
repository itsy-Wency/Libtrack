# LibTrack

LibTrack is a Laravel library-management app for browsing a book catalog, requesting and managing loans, and reviewing library activity. Its admin dashboard also reports application health and operational metrics.

## Requirements

- Git
- PHP 8.3 or later with SQLite support enabled
- Composer
- Node.js and npm

The project uses SQLite by default, so a separate database server is not required.

## Clone and run

Replace `YOUR-USERNAME` and `YOUR-REPOSITORY` with the GitHub account and repository that contain this project.

```powershell
git clone https://github.com/YOUR-USERNAME/YOUR-REPOSITORY.git
cd YOUR-REPOSITORY
```

Create the SQLite database file if it is not already in the clone, then run the project setup script. It installs PHP and JavaScript dependencies, creates `.env`, generates the application key, runs migrations, and builds frontend assets.

```powershell
if (-not (Test-Path database/database.sqlite)) {
    New-Item -ItemType File -Path database/database.sqlite | Out-Null
}

composer run setup
```

Create the demo accounts and make uploaded book covers publicly accessible:

```powershell
php artisan db:seed
php artisan storage:link
```

Start the local development processes:

```powershell
composer run dev
```

Open [http://localhost:8000](http://localhost:8000). The development command runs the PHP server, queue listener, and Vite process.

## Demo accounts

All demo accounts use the password `password`.

| Role | Email |
| --- | --- |
| Admin | `admin@libtrack.test` |
| Librarian | `librarian@libtrack.test` |
| Student | `student@libtrack.test` |

The seeder uses `updateOrCreate` and `firstOrCreate`, so it can be run again without duplicating these demo records.

## System monitoring

The admin dashboard shows web endpoint health, database and storage checks, uptime and downtime estimates, request volume, 5xx error rate, active users, and disk capacity. Health checks run every minute and are retained locally; request counters and active sessions use the configured cache and session stores.

To collect health samples, start Laravel's scheduler in a separate terminal:

```powershell
php artisan schedule:work
```

The app probes `APP_URL/up`, so keep `APP_URL` in `.env` set to an address reachable from the machine running the scheduler. Uptime and downtime are estimates from recorded checks. Missing checks lower monitoring coverage and are not counted as downtime.

## Useful commands

```powershell
php artisan route:list       # Show registered application routes
php artisan migrate:status   # Show migration status
php artisan db:seed          # Create or refresh demo records
php artisan system:health-check  # Record one health sample immediately
```

To rebuild frontend assets after changing CSS or JavaScript:

```powershell
npm run build
```
