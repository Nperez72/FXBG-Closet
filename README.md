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

# Set Up

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



**Current Branch:** `dang/pwa`  
**Status:** ✅ PWA & Docker Setup Complete

