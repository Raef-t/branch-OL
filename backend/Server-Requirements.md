# Server Requirements (Laravel API + Next.js Frontend)

## 1) Scope
This document defines the production server requirements for:
- Backend API: Laravel 12 (PHP ^8.2)
- Frontend: React Next.js

Assumption:
- Single server hosts both API and Next.js app (SSR).
- If you split into two servers later, sizing can be reduced per server.

---

## 2) Required Software Stack
- OS: Ubuntu 22.04 LTS or 24.04 LTS
- Web Server: Nginx
- PHP: 8.2 or 8.3 (PHP-FPM)
- Composer: v2
- Database: MySQL 8 (or MariaDB 10.6+)
- Cache/Queue backend: Redis 6+
- Node.js: 20 LTS or 22 LTS (for Next.js)
- Process manager: Supervisor or systemd (for queue workers and Next.js process)
- SSL: Let's Encrypt (auto-renew)

---

## 3) PHP Extensions (Mandatory)
- bcmath
- ctype
- curl
- dom
- fileinfo
- gd
- intl
- json
- mbstring
- openssl
- pdo_mysql
- tokenizer
- xml
- zip
- redis (phpredis)

---

## 4) Estimated Infrastructure Sizing

### A) Minimum Production (small usage)
- CPU: 4 vCPU
- RAM: 8 GB
- Disk: 120 GB NVMe SSD
- Suitable for: low-medium load, early production

### B) Recommended Production (stable baseline)
- CPU: 8 vCPU
- RAM: 16 GB
- Disk: 200 GB NVMe SSD
- Suitable for: medium load + background jobs + Next.js SSR with good headroom

### C) Growth / High Traffic
- CPU: 12-16 vCPU
- RAM: 32 GB
- Disk: 300-500 GB NVMe SSD
- Suitable for: higher concurrency, heavier reports, large file growth

---

## 5) Disk Allocation Guidance (Approximate)
For the **recommended** 200 GB setup:
- OS + updates + base logs: 20-30 GB
- App code + dependencies (Laravel + Node builds): 10-20 GB
- Database data/indexes: 40-80 GB (growth-dependent)
- Uploaded files (`storage`): 50-100+ GB (growth-dependent)
- Free safety buffer: 20-30 GB

Notes:
- If uploads are moved to object storage (S3-compatible), local disk can be reduced.
- Enable log rotation to prevent disk fill.

---

## 6) Runtime Services (Must Be Active)
- Laravel Queue Worker (always on):
  - `php artisan queue:work`
- Laravel Scheduler (every minute via cron):
  - `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1`
- Next.js app process (always on):
  - `next start` managed by PM2/systemd

---

## 7) Networking / Domains
- `api.yourdomain.com` -> Laravel (Nginx + PHP-FPM)
- `app.yourdomain.com` -> Next.js (reverse proxy to Node process)
- Open ports only: 22, 80, 443
- Configure CORS and Sanctum/session domain rules according to subdomains

---

## 8) Security & Operations
- `APP_ENV=production`
- `APP_DEBUG=false`
- Daily backups:
  - MySQL dump
  - Laravel `storage` files
- Monitoring:
  - CPU, RAM, disk, nginx/php-fpm/mysql/redis
  - queue worker health and restart policy
- Firewall + Fail2Ban recommended

---

## 9) External Access Requirements
- Outbound HTTPS access to Firebase/Google APIs
- Outbound SMTP port (usually 587) for mail provider

---

## 10) Recommended Delivery to Hosting Provider
Please provision a production Linux server with:
- Ubuntu LTS
- Nginx + PHP-FPM 8.2/8.3 + MySQL 8 + Redis 6+
- Node.js 20/22 LTS
- Supervisor/systemd for long-running processes
- SSL with auto-renew
- Daily backups and basic monitoring

Preferred baseline capacity:
- 8 vCPU, 16 GB RAM, 200 GB NVMe

