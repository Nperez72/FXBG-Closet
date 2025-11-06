# Docker Setup

## Prerequisites

You need Docker installed:
- **Windows/Mac**: [Download Docker Desktop](https://www.docker.com/products/docker-desktop)
- **Linux**: [Install Docker Engine](https://docs.docker.com/engine/install/)

Verify installation:
```bash
docker --version
docker-compose --version
```

---

## Quick Start (3 Steps)

### 1. Start the Containers

```bash
cd C:\Users\Unknown\Work\FXBG-Closet
docker-compose up -d
```

You should see:
```
Creating fxbg-closet-web ... done
Creating fxbg-closet-db  ... done
Creating fxbg-closet-phpmyadmin ... done
```

### 2. Access the App

Open your browser and go to:
- **App**: http://localhost:8000
- **PhpMyAdmin**: http://localhost:8080 (optional)


## What's Running

| Service | URL | Purpose |
|---------|-----|---------|
| FXBG Closet App | http://localhost:8000 | Your volunteer management system |
| PhpMyAdmin | http://localhost:8080 | Database management (optional) |
| MySQL Database | localhost:3306 | Database (port 3306) |

---

## Useful Commands

### View Running Containers
```bash
docker-compose ps
```

### View Container Logs
```bash
# All services
docker-compose logs -f

# Just the web server
docker-compose logs -f web

# Just the database
docker-compose logs -f db
```

### Stop All Containers
```bash
docker-compose stop
```

### Stop and Remove All
```bash
docker-compose down
```

### Restart Everything
```bash
docker-compose restart
```

### Rebuild Containers (after Dockerfile changes)
```bash
docker-compose up -d --build
```

### Access Container Shell
```bash
# Web container
docker-compose exec web bash

# Database container
docker-compose exec db bash
```
## Troubleshooting

### Port Already in Use
If port 8000 or 8080 is already in use:

**Option 1: Stop the other service**
```bash
# Find what's using port 8000
netstat -ano | findstr :8000  # Windows
lsof -i :8000                 # Mac/Linux
```

**Option 2: Change the port in docker-compose.yml**
```yaml
# Change this:
ports:
  - "8000:80"

# To this (uses port 9000 instead):
ports:
  - "9000:80"
```

### Container Won't Start
```bash
# Check logs for errors
docker-compose logs web

# Rebuild from scratch
docker-compose down
docker-compose up -d --build
```

### Database Connection Issues
```bash
# Verify database is running
docker-compose exec db mysqladmin ping -u root -p
# Password: root

# Restart database
docker-compose restart db
```

### Service Worker Not Registering
1. Clear browser cache (DevTools → Storage → Clear site data)
2. Hard refresh (Ctrl+Shift+R or Cmd+Shift+R)
3. Check console for errors (F12 → Console)

---

## Resources

- [Docker Documentation](https://docs.docker.com)
- [Docker Compose Documentation](https://docs.docker.com/compose)
- [Docker Best Practices](https://docs.docker.com/develop/dev-best-practices)
- [PHP Docker Official Image](https://hub.docker.com/_/php)

---
Start containers with
```bash
docker-compose up -d
```

Then visit http://localhost:8000


