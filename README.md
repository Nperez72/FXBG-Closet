# FXBG-Closet

Volunteer Management System for Fredericksburg Pride

**Team Members:**
- Bethanie Hackett
- Joey Ryerson
- Colin Chu
- Nicolas Perez-merino
- Dang Phung
- Owen Lach

---

## Tech Stack & Platforms

| Technology | Version | Purpose |
|------------|---------|---------|
| **PHP** | 8.2+ | Backend server-side language |
| **MySQL / MariaDB** | 10.4+ | Database |
| **TailwindCSS** | 4.1.4 | CSS framework for styling |
| **PHPMailer** | 7.0 | Email sending functionality |
| **Composer** | Latest | PHP dependency management |

---

## Prerequisites

Before setting up the project, ensure you have:

- **Git** - for cloning the repository
- **PHP 8.2+** - for running the application
- **Composer** - for PHP dependencies (PHPMailer)
- **MySQL/MariaDB** - for the database
- One of the following development environments:
  - Docker Desktop (recommended)
  - XAMPP
  - Laragon

---

## Setup Options

Choose **one** of the following setup methods:

### Option 1: Docker (Recommended)

Docker provides the easiest setup with all services pre-configured.

**Prerequisites:**
- [Docker Desktop](https://www.docker.com/products/docker-desktop) installed

**Steps:**
```bash
# Clone the repository
git clone <repository-url>
cd FXBG-Closet

# Start all containers
docker-compose up -d
```

**Access Points:**
- **App**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080

**Database auto-imports from `sql/pridedb.sql` ✓**

**Stop containers:**
```bash
docker-compose down
```

See `DOCKER_SETUP.md` for detailed Docker documentation.

---

### Option 2: XAMPP

XAMPP provides Apache, MySQL, and PHP in one package.

**Step 1: Install XAMPP**
1. Download from [apachefriends.org](https://www.apachefriends.org/)
2. Install with default settings
3. Start **Apache** and **MySQL** from XAMPP Control Panel

**Step 2: Clone/Copy Project**
```bash
# Clone directly into XAMPP's htdocs folder
cd C:\xampp\htdocs
git clone <repository-url> FXBG-Closet
```

Or copy the project folder to `C:\xampp\htdocs\FXBG-Closet`

**Step 3: Create Database User**
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Go to **User accounts** tab
3. Click **Add user account**
4. Fill in:
   - Username: `pridedb`
   - Host: `localhost`
   - Password: `pridedb`
5. Check **Create database with same name and grant all privileges**
6. Click **Go**

**Step 4: Import Database**
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Select the `pridedb` database from the left sidebar
3. Click the **Import** tab
4. Click **Choose File** and select `sql/pridedb.sql` from the project folder
5. Scroll down and click **Import**

**Step 5: Install Composer Dependencies**
```bash
cd C:\xampp\htdocs\FXBG-Closet
composer install
```

**Step 6: Access the App**
- Open http://localhost/FXBG-Closet

---

### Option 3: Laragon

Laragon is a modern, lightweight alternative to XAMPP for Windows.

**Step 1: Install Laragon**
1. Download from [laragon.org](https://laragon.org/download/)
2. Install with default settings
3. Click **Start All** in Laragon

**Step 2: Clone/Copy Project**
```bash
# Clone into Laragon's www folder
cd C:\laragon\www
git clone <repository-url> FXBG-Closet
```

Or copy the project folder to `C:\laragon\www\FXBG-Closet`

**Step 3: Create Database User**
1. Open phpMyAdmin: http://localhost/phpmyadmin (or click "Database" in Laragon)
2. Go to **User accounts** tab
3. Click **Add user account**
4. Fill in:
   - Username: `pridedb`
   - Host: `localhost`
   - Password: `pridedb`
5. Check **Create database with same name and grant all privileges**
6. Click **Go**

**Step 4: Import Database**
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Select the `pridedb` database from the left sidebar
3. Click the **Import** tab
4. Click **Choose File** and select `sql/pridedb.sql` from the project folder
5. Scroll down and click **Import**

**Step 5: Install Composer Dependencies**
```bash
cd C:\laragon\www\FXBG-Closet
composer install
```

**Step 6: Access the App**
- Open http://localhost/FXBG-Closet
- Or use Laragon's pretty URLs: http://fxbg-closet.test

---

### Option 4: PHP Built-in Server

For quick testing without full stack (requires separate MySQL setup).

```bash
cd FXBG-Closet
php -S localhost:8000
```

Open http://localhost:8000

**Note:** You'll need MySQL running separately and the database imported manually.

---

## Database Configuration

The application connects to MySQL with these credentials:

| Setting | Value |
|---------|-------|
| Host | `localhost` |
| Database | `pridedb` |
| Username | `pridedb` |
| Password | `pridedb` |

Configuration is in `database/dbinfo.php`.

**Database File Location:** `sql/pridedb.sql`

---

# Composer Setup

This project uses Composer to manage PHP dependencies, primarily for PHPMailer email functionality.

## Installing Composer

1. **Download Composer**
   - Visit [getcomposer.org](https://getcomposer.org/download/)
   - Follow the installation instructions for your operating system

2. **Verify Installation**
   ```bash
   composer --version
   ```

## Installing Project Dependencies

Navigate to the project root directory and run:

```bash
composer install
```

This will install PHPMailer and other dependencies defined in `composer.json`.

---

# Email Functionality

The project uses PHPMailer for sending emails. Configuration is handled in `email.php`.

## Testing Emails Locally

For local development, you can use either **MailHog** or **Mailpit** to catch and view test emails without sending them to real recipients. Both tools work identically but Mailpit offers a more modern interface with better performance.

### Installation

Choose either MailHog or Mailpit (both use the same ports):

**Windows (with Chocolatey):**
```bash
choco install mailhog
# OR
choco install mailpit
```

**macOS (with Homebrew):**
```bash
brew install mailhog
# OR
brew install mailpit
```

**Linux:**
- Download MailHog from [GitHub releases](https://github.com/mailhog/MailHog/releases)
- Download Mailpit from [GitHub releases](https://github.com/axllent/mailpit/releases)

### Running the Email Catcher

Open terminal and run either:

```bash
mailhog
```

or

```bash
mailpit
```

**Configuration:**
- **SMTP server:** `127.0.0.1:1025`
- **Web UI:** http://localhost:8025

### Viewing Test Emails

1. Open browser to http://localhost:8025
2. All test emails appear here
3. No actual emails are sent (safe for testing)

## Using a Temporary Gmail Account

For testing with real email sending:

### Step 1: Create a Gmail Account
1. Go to [accounts.google.com](https://accounts.google.com)
2. Click **Create account**
3. Fill in account details
4. Complete verification process

### Step 2: Enable 2-Step Verification
1. Go to [myaccount.google.com/security](https://myaccount.google.com/security)
2. Click **2-Step Verification**
3. Follow the prompts to enable it

### Step 3: Create an App Password
1. Go to [myaccount.google.com](https://myaccount.google.com)
2. In the search bar, search for **App passwords**, and select that option
3. Create a name for the password
4. Copy the 16-character password

### Step 4: Configure PHPMailer

Open `email.php` and update the following configuration:

**Set your Gmail credentials:**
```php
$host = 'smtp.gmail.com';
$port = 587;
$fromEmail = 'your-temporary-email@gmail.com';
$username = 'your-temporary-email@gmail.com';
$password = 'your-16-character-app-password';  // App password from Step 3
```

**Enable SMTP authentication:**

Comment out this line:
```php
$mail->SMTPAuth = false;
```

Uncomment the following lines:
```php
$mail->SMTPAuth = true;
$mail->Username = $username;
$mail->Password = $password;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
```

---

# Troubleshooting

## Composer Issues

```bash
# Clear cache
composer clear-cache

# Reinstall dependencies
rm -rf vendor/
composer install
```

**Windows (PowerShell):**
```powershell
Remove-Item -Recurse -Force vendor
composer install
```

## Database Issues

**"Database not selected" or connection errors:**
1. Verify MySQL is running (check XAMPP/Laragon control panel)
2. Confirm `pridedb` database exists in phpMyAdmin
3. Verify user `pridedb` has correct permissions
4. Check credentials in `database/dbinfo.php`

**Import fails:**
1. Make sure you selected the `pridedb` database before importing
2. Check that `sql/pridedb.sql` file exists
3. Try importing in smaller chunks if timeout occurs

## Email Issues

**Emails not sending?**
- Check MailHog/Mailpit is running on port 1025
- Verify `email.php` configuration
- Check error logs in browser console

**Attachments failing?**
- Verify file paths are correct
- Check file permissions
- Ensure files exist before sending

**Port 1025 already in use?**
- Stop any running MailHog/Mailpit instances
- Check for other applications using the port:
  - **Windows:** `netstat -ano | findstr :1025`
  - **macOS/Linux:** `lsof -i :1025`

## Port Conflicts

**Port 80 or 3306 already in use (XAMPP/Laragon):**
- Stop other web servers (IIS, other Apache instances)
- Stop other MySQL instances
- Check with:
  - **Windows:** `netstat -ano | findstr :80`
  - **macOS/Linux:** `lsof -i :80`

## TailwindCSS

If styles aren't applying:
```bash
npm install
npx tailwindcss -o css/output.css --watch
```

---

# Project Structure

```
FXBG-Closet/
├── css/                  # Stylesheets
├── database/             # Database connection and queries
│   └── dbinfo.php        # Database configuration
├── domain/               # Domain models
├── images/               # Image assets
├── include/              # PHP includes
├── js/                   # JavaScript files
├── lib/                  # Third-party libraries
├── sql/                  # Database SQL files
│   └── pridedb.sql       # Main database schema/data
├── uploads/              # User uploads
├── vendor/               # Composer dependencies
├── composer.json         # PHP dependencies
├── docker-compose.yml    # Docker configuration
├── email.php             # Email functionality
├── index.php             # Application entry point
└── README.md             # This file
```

---

# Quick Reference

| Task | Command/URL |
|------|-------------|
| Start Docker | `docker-compose up -d` |
| Stop Docker | `docker-compose down` |
| Install PHP deps | `composer install` |
| App URL (Docker) | http://localhost:8000 |
| App URL (XAMPP/Laragon) | http://localhost/FXBG-Closet |
| phpMyAdmin (Docker) | http://localhost:8080 |
| phpMyAdmin (XAMPP) | http://localhost/phpmyadmin |
| MailHog/Mailpit UI | http://localhost:8025 |

---

## License

See `LICENSE.txt` for details.
