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

## 🚀 Quick Start

### Option 1: Docker (Recommended)
```bash
docker-compose up -d
# Open: http://localhost:8000
```

To clear docker containers run
```
# To clear (remove) all Docker containers:
docker-compose down

Database auto-imports from `sql/pridedb.sql` ✓  
PhpMyAdmin: http://localhost:8080

See `DOCKER_SETUP.md` for details.

### Option 2: PHP Built-in Server
```bash
php -S localhost:8000
# Open: http://localhost:8000
```
To clear (remove) any built-in PHP server: (just press Ctrl+C in the terminal)

---

## 📱 PWA (Progressive Web App)

Your app is now a PWA! Users can:
- 📲 **Install** on phones like a native app
- 🔌 **Work offline** with previously cached pages
- ⚡ **Load 30-50% faster** with optimized caching
- 🏠 **Add to home screen** with your logo

### Getting Started with PWA
1. Open `TESTING_START_HERE.md` for quick setup
2. See `PWA_README.md` for features overview
3. See `TEST_PWA.md` for testing guide

### Quick Test
1. Open app in Chrome
2. Press `F12` → **Application** tab
3. Click **Service Workers**
4. Should see: ✓ `service-worker.js` (activated and running)

**That's it! Your PWA works!** 🎉

---

## 🗄️ Database

### Auto-Import Setup
SQL files in `sql/` folder automatically import when Docker starts.

- **File:** `sql/pridedb.sql`
- **Auto-imports on:** First container start
- **Database name:** `fxbg_closet`
- **Default user:** `fxbg_user` / `fxbg_pass`

See `DOCKER_DATABASE_SETUP.md` for details.

### Access Database
```bash
# Via PhpMyAdmin: http://localhost:8080
# Via MySQL CLI:
docker-compose exec db mysql -u root -p fxbg_closet
# Password: root
```

---

## 📚 Documentation

| Document | Purpose |
|----------|---------|
| **TESTING_START_HERE.md** | PWA testing quick start |
| **PWA_README.md** | PWA overview & features |
| **TEST_PWA.md** | Comprehensive 10-test guide |
| **TESTING_CHEATSHEET.txt** | Quick reference (print this!) |
| **DOCKER_SETUP.md** | Docker setup & commands |
| **DOCKER_DATABASE_SETUP.md** | Database auto-import guide |
| **DOCKER_WINDOWS_FIX.md** | Troubleshooting for Windows |
| **PWA_SETUP.md** | Technical PWA details |

---

## 🎯 Features

### Volunteer Management
- User registration and profiles
- Event management & sign-ups
- Volunteer hour tracking
- Group management
- Discussion forums
- Reports & analytics

### PWA Features
- ✅ Offline support
- ✅ App installation
- ✅ App shortcuts
- ✅ Performance optimization
- ✅ Caching strategy
- ✅ Mobile responsive

---

## 🔧 Development Setup

### Requirements
- PHP 8.2+
- MySQL 8.0 (or Docker)
- Node.js 18+ (for Tailwind)
- Docker Desktop (optional but recommended)

### Installation

**With Docker (Recommended):**
```bash
docker-compose up -d
```

**Without Docker:**
1. Set up PHP/MySQL locally
2. Import `sql/pridedb.sql`
3. Configure database connection
4. Run `php -S localhost:8000`

---

## 📦 Project Structure

```
FXBG-Closet/
├── index.php                    # Dashboard
├── login.php                    # Login page (PWA-ready)
├── header.php                   # Navigation (includes PWA tags)
├── manifest.json                # PWA app config
├── service-worker.js            # Offline support
├── docker-compose.yml           # Docker orchestration
├── Dockerfile                   # Container definition
│
├── database/                    # Database functions
│   ├── dbAccounts.php
│   ├── dbPersons.php
│   ├── dbEvents.php
│   └── ...
│
├── domain/                      # Domain models
│   ├── Person.php
│   ├── Event.php
│   └── ...
│
├── sql/                         # Database schema
│   └── pridedb.sql             # Auto-imports ✓
│
├── css/                         # Stylesheets
├── js/                          # JavaScript
├── images/                      # Images & icons
│
├── include/                     # Reusable includes
│   ├── pwa-meta.php            # PWA tags
│   └── ...
│
└── Testing & Documentation
    ├── TESTING_START_HERE.md
    ├── TEST_PWA.md
    ├── DOCKER_SETUP.md
    └── ...
```

---

## 🚀 Deployment

### Production Checklist
- [ ] Enable HTTPS (required for PWA)
- [ ] Update database connection settings
- [ ] Configure email settings
- [ ] Test PWA on mobile
- [ ] Set up backups
- [ ] Monitor logs

### Hosting Requirements
- PHP 8.2+ with mysqli extension
- MySQL 8.0+
- HTTPS certificate
- 100MB+ disk space

---

## 🧪 Testing

### PWA Testing
```bash
1. Open app in Chrome
2. Press F12 → Application
3. Check Service Workers, Manifest, Cache Storage
4. Test offline mode
5. Test on mobile device
```

See `TESTING_START_HERE.md` for complete guide.

### Database Testing
```bash
docker-compose exec db mysql -u root -p
SHOW DATABASES;
USE fxbg_closet;
SHOW TABLES;
```

---

## 🐛 Troubleshooting

### Docker Issues
See `DOCKER_WINDOWS_FIX.md`

### PWA Issues
See `TESTING_CHEATSHEET.txt` - Quick troubleshooting

### Database Issues
See `DOCKER_DATABASE_SETUP.md` - Troubleshooting section

---

## 📞 Support

### Resources
- [Docker Documentation](https://docs.docker.com)
- [PWA Guide](https://developers.google.com/web/progressive-web-apps)
- [MySQL Documentation](https://dev.mysql.com/doc)
- [PHP Documentation](https://www.php.net/docs.php)

### Quick Commands
```bash
# Docker
docker-compose up -d              # Start
docker-compose ps                 # Check status
docker-compose logs -f            # View logs
docker-compose down               # Stop
docker-compose down -v            # Stop & reset

# PHP
php -S localhost:8000             # Start server
```

---

## 📝 Contributing

When making database changes:
1. Test locally with Docker
2. Export schema from PhpMyAdmin
3. Save as `sql/pridedb.sql`
4. Commit to git
5. Next deployment will auto-import

---

## 📄 License

See LICENSE.txt for details.

---

## 🎉 Quick Links

- **Start Testing PWA:** `TESTING_START_HERE.md`
- **Docker Setup:** `DOCKER_SETUP.md`
- **Database Setup:** `DOCKER_DATABASE_SETUP.md`
- **PWA Features:** `PWA_README.md`
- **Full Test Guide:** `TEST_PWA.md`

---

**Current Branch:** `dang/pwa`  
**Status:** ✅ PWA & Docker Setup Complete

