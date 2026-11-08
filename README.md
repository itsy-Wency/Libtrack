<div align="center">

# 📚 LibTrack

### A calmer, clearer way to manage your library.

<p>
  <img src="https://img.shields.io/badge/Laravel-13-F05340?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13">
  <img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3 or later">
  <img src="https://img.shields.io/badge/Database-MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
</p>

<p>
  <img src="https://readme-typing-svg.demolab.com?font=Inter&weight=600&size=20&pause=1200&color=16A34A&center=true&vCenter=true&width=680&lines=Browse+the+catalog.;Manage+book+requests+and+loans.;Keep+an+eye+on+library+system+health." alt="LibTrack: browse, borrow, and monitor">
</p>

[Features](#-features) · [Laragon setup](#-run-locally-with-laragon-and-mysql) · [Demo accounts](#-demo-accounts) · [Monitoring](#-system-monitoring)

</div>

---

LibTrack is a library-management application for exploring a book catalog, managing borrowing requests, and reviewing library activity. Admins can also monitor application health and operational metrics.

## ✨ Features

| Area | What you can do |
| --- | --- |
| Catalog | Browse books and categories, and view book details |
| Borrowing | Submit and manage requests, and track active loans |
| Library operations | Manage books, categories, users, and activity |
| System health | View availability, request volume, errors, active users, and storage |

## 🚀 Run locally with Laragon and MySQL

### Requirements

- Windows with [Laragon](https://laragon.org/) installed
- Git
- PHP 8.3 or later with the `pdo_mysql` extension enabled
- Composer
- Node.js and npm

### 1. Start Laragon and create the database

Open Laragon and start **MySQL**. Create a database named `libtrack` using HeidiSQL or another MySQL client. You can run:

```sql
CREATE DATABASE libtrack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Clone the GitHub repository

Open PowerShell in the folder where you keep Laragon projects, commonly `C:\laragon\www`:

```powershell
cd C:\laragon\www
git clone https://github.com/itsy-Wency/Libtrack.git
cd Libtrack
```

If Laragon is installed in a different location, use that installation's `www` folder instead.

### 3. Install PHP dependencies and create your `.env` file

```powershell
composer install
Copy-Item .env.example .env
```

`Copy-Item` creates your local `.env` from the template. The `.env` file contains machine-specific settings and should stay local; Git ignores it.

Open `.env` in your editor. Set the application URL and MySQL connection to match Laragon. A typical local Laragon setup uses `root` with an empty password; use the credentials configured on your machine if they differ.

```dotenv
APP_NAME=LibTrack
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=libtrack
DB_USERNAME=root
DB_PASSWORD=
```

Replace the existing `DB_CONNECTION=sqlite` setting and uncomment or add the MySQL settings above. Save `.env` before continuing.

### 4. Generate the app key, migrate, and seed sample data

```powershell
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
```

The migrations create the application's tables in the `libtrack` database. Seeding adds demo accounts, categories, and sample books; it can be run again safely.

### 5. Install frontend packages and start the app

```powershell
npm install
npm run build
composer run dev
```

Open [http://localhost:8000](http://localhost:8000). `composer run dev` starts the Laravel web server, queue listener, and Vite development server. Keep that terminal open while using the app.

## 🔐 Demo accounts

All seeded demo accounts use the password `password`.

| Role | Email |
| --- | --- |
| Admin | `admin@libtrack.test` |
| Librarian | `librarian@libtrack.test` |
| Student | `student@libtrack.test` |

Change or remove demo credentials before using the application in a shared or production environment.

## 📈 System monitoring

The admin dashboard displays web, database, and storage health alongside uptime and downtime estimates, requests per minute, error rate, active users, and storage capacity.

To collect scheduled health samples during local development, open a second PowerShell terminal in the project folder and run:

```powershell
php artisan schedule:work
```

The health checker requests `APP_URL/up`. Keep `APP_URL` set to an address that the local app can reach. Uptime and downtime are estimates based on recorded checks; missing checks reduce monitoring coverage and are not counted as downtime.

## 🧰 Useful commands

```powershell
php artisan route:list
php artisan migrate:status
php artisan system:health-check
npm run build
```

<div align="center">

Made for libraries and the people who love them. 🌿

</div>
