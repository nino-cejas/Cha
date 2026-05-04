# Barangay Bonbon – Management System (BMS)

A PHP/MySQL web application for managing barangay residents and providing a resident portal for document-request services.

---

## Requirements

| Requirement | Version |
|---|---|
| [XAMPP](https://www.apachefriends.org/) | 8.x (PHP 8.0+, MySQL 5.7+) |
| Web browser | Any modern browser |

---

## Setup Instructions (XAMPP)

### 1 – Place the project in htdocs

Copy (or clone) the entire project folder to:

```
C:\xampp\htdocs\BMS\
```

Your folder structure should look like this:

```
htdocs/
└── BMS/
    ├── index.php
    ├── schema.sql
    ├── styles.css
    ├── app.js
    ├── config/
    │   ├── db.php
    │   └── auth.php
    ├── api/
    │   ├── login.php
    │   ├── logout.php
    │   ├── register.php
    │   ├── auth.php
    │   ├── officials.php
    │   └── clearance.php
    ├── admin/
    │   ├── dashboard.php
    │   ├── residents.php
    │   └── resident_add.php
    ├── resident/
    │   └── home.php
    └── assets/
        ├── muni.jpg
        └── style.css
```

### 2 – Start XAMPP services

1. Open **XAMPP Control Panel**.
2. Start **Apache** and **MySQL**.

### 3 – Import the database schema

**Option A – phpMyAdmin (recommended)**

1. Open your browser and go to `http://localhost/phpmyadmin`.
2. Click **New** in the left sidebar to create a new database (or skip — the SQL handles it).
3. Click the **SQL** tab.
4. Copy the contents of `schema.sql` and paste it, then click **Go**.

**Option B – MySQL command line**

```bash
mysql -u root -p < C:\xampp\htdocs\BMS\schema.sql
```

### 4 – Configure database credentials

Open `config/db.php` and adjust if needed:

```php
$host   = 'localhost';   // MySQL host
$dbname = 'barangay_db'; // Database name (created by schema.sql)
$dbuser = 'root';        // MySQL username
$dbpass = '';            // MySQL password (blank by default in XAMPP)
```

If your XAMPP folder is named something other than `BMS`, also change:

```php
define('BASE_URL', '/BMS');  // Change /BMS to your folder name
```

### 5 – Open the application

Go to:

```
http://localhost/BMS/
```

You will see the **Login / Register** page.

---

## First Steps

1. **Register an account** using the Register tab.
   - All self-registered accounts default to the `resident` role.
2. **To create an admin account**, insert one directly in phpMyAdmin:
   ```sql
   INSERT INTO users (full_name, email, password_hash, role)
   VALUES ('Admin User', 'admin@barangay.ph', '$2y$10$...', 'admin');
   ```
   Or run `api/register.php` then update the role in phpMyAdmin:
   ```sql
   UPDATE users SET role = 'admin' WHERE email = 'your@email.com';
   ```
3. Log in with an admin account to access the **Admin Dashboard** where you can:
   - View demographic statistics.
   - Browse and search the residents list.
   - Add new residents to the registry.

---

## Project Structure

| Path | Description |
|---|---|
| `index.php` | Login & Register page (entry point) |
| `schema.sql` | Database schema (run once to set up) |
| `config/db.php` | PDO database connection + `BASE_URL` constant |
| `config/auth.php` | `require_login()` and `require_role()` helpers |
| `api/login.php` | JSON login endpoint |
| `api/register.php` | JSON register endpoint |
| `api/logout.php` | Logout (JSON or redirect) |
| `api/auth.php` | Legacy AJAX auth endpoint (used by `app.js`) |
| `api/officials.php` | Officials AJAX endpoint |
| `api/clearance.php` | Clearance requests AJAX endpoint |
| `admin/dashboard.php` | Admin – demographics overview |
| `admin/residents.php` | Admin – residents list & search |
| `admin/resident_add.php` | Admin – add new resident form |
| `resident/home.php` | Resident – portal home page |
| `assets/muni.jpg` | Background image |
| `assets/style.css` | Admin/dashboard CSS |
| `styles.css` | Auth-page CSS |

---

## GitHub Pages Limitation

> ⚠️ **Note:** This project uses PHP and MySQL and **cannot be hosted on GitHub Pages**. GitHub Pages only serves static HTML/CSS/JS files and does not support server-side scripting.
>
> To run this project you need a PHP-enabled web server such as:
> - **XAMPP / WAMP / LAMP** (local development)
> - **cPanel hosting** with PHP & MySQL support
> - **VPS** running Apache/Nginx + PHP + MySQL
