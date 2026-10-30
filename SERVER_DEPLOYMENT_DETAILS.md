# Server Deployment Details - admin-agency

> **Generated**: 2026-10-23
> **Last Updated**: 2026-10-30 (Worker Blocklisting Feature)
> **Source**: SSH investigation of production server
> **Note**: This file is git-ignored (contains sensitive credentials)

---

## Server Access

```bash
ssh -p 65002 u805024096@178.16.135.22
```

- **IP**: 178.16.135.22 (Hostinger VPS)
- **Port**: 65002
- **User**: u805024096
- **Group**: o200508622

---

## Git Remote

```
origin  git@github.com:renitreni/admin-agency.git (fetch)
origin  git@github.com:renitreni/admin-agency.git (push)
```

Also accessible via HTTPS: `https://github.com/renitreni/admin-agency.git`

---

## Deployed Instances (6+)

All deployed as **`sys.*` subdomains** under `/home/u805024096/domains/`:

| Subdomain | App Name | Environment | APP_URL | Database |
|-----------|----------|-------------|---------|----------|
| **sys.joselinemanpowercorp.com** | Joseline Manpower Portal | `production` | https://sys.joselinemanpowercorp.com | `u805024096_1010` |
| **sys.wsprecruitment.com** | WSP Portal | `local` | https://sys.wsprecruitment.com | (check) |
| **sys.yaramay.online** | Agency Portal | `staging` | https://admin-agency.test | (check) |
| **sys.elb-ph.online** | ELB Portal | `production` | https://admin-agency.test | (check) |
| **sys.elb.com.ph** | ELB Portal | - | - | - |
| **sys.1010ephesians.xyz** | - | - | - | - |

**Additional `sys.*` domains found**:
- sys.ajmalsa.com
- sys.aljouf-baladiya.site
- sys.bulalohan.xyz
- sys.wsprecruitment.com (duplicate)

---

## Detailed: sys.joselinemanpowercorp.com (Production)

### Path

```
/home/u805024096/domains/sys.joselinemanpowercorp.com/public_html/
```

### Environment (.env)

```env
APP_NAME="Joseline Manpower Portal"
APP_ENV=production
APP_KEY=base64:VK+NLv7eZOGRu8qD3WItrygolvMB3U2S8zKxo+RH8I0=
APP_DEBUG=false
APP_URL=https://sys.joselinemanpowercorp.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u805024096_1010
DB_USERNAME=u805024096_1010admin
DB_PASSWORD=Ephesians@2023

QUEUE_CONNECTION=sync
SESSION_DRIVER=file
MAIL_MAILER=log

ALLOWED_EMAIL="yaramayservices@gmail.com"
```

### Database: `u805024096_1010`

#### Tables (35 - including Worker Blocklisting)

```
agencies
agency_user
applications
complaints
concern_reports
concerns
countries
deployments
education
failed_jobs
foreign_agencies
foreign_agency_user
handlers
inquiries
job_posts
media
migrations
monitorings
password_reset_tokens
personal_access_tokens
skills
telescope_entries
telescope_entries_tags
telescope_monitoring
user_login_histories
users
voucher_items
voucher_types
vouchers
work_histories
worker_documents
worker_emergencies
worker_information
workers
worker_blocklists          <-- NEW: Worker Blocklisting Feature
```

#### New Tables for Worker Blocklisting

| Table | Description |
|-------|-------------|
| `worker_blocklists` | Cross-agency worker blocklist entries with agency ownership |
| `applications.blocklist_matches` | JSON column storing blocklist match details on job applications |

#### Users (10)
| ID | Name | Email | Type | Created |
|----|------|-------|------|---------|
| 1 | Yaramay | yaramayservices@gmail.com | AGENCY | 2026-03-09 |
| 2 | Joseline | joseline@gmail.com | AGENCY | 2026-03-09 |
| 3 | Charo | charo@blueorion.com | AGENCY | 2026-04-07 |
| 4 | Brilliant admin | admin@brilliantmanpower.com | AGENCY | 2026-05-08 |
| 5 | WSP Admin | admin@wspinternationalco.com | AGENCY | 2026-05-08 |
| 7 | Demo Admin 1 | demo_1@agency.com | AGENCY | 2026-05-17 |
| 8 | Admin | admin@lmbwsi.com | AGENCY | 2026-06-18 |
| **9** | **yaramay fra** | **yaramayservices-f

--- (rest of file unchanged) ---

---

## Worker Blocklisting Feature - Deployment Notes (2026-10-30)

### New Migrations to Run on Each Deployment

```bash
# Run on each sys.* deployment
php artisan migrate --force

# Migrations added:
# 1. 2026_10_30_022452_create_worker_blocklists_table
#    - Creates worker_blocklists table with agency_id FK, passport_number/email indexes
#    - Soft deletes, is_active flag, match_confidence enum (high/medium/low)
#
# 2. 2026_10_30_023422_add_blocklist_matches_to_applications_table
#    - Adds blocklist_matches JSON column to applications table
```

### New Files to Deploy

| Path | Description |
|------|-------------|
| `app/Models/WorkerBlocklist.php` | Model with matching algorithm for Philippines worker data |
| `app/Policies/WorkerBlocklistPolicy.php` | Agency-level access control (own=CRUD, others=read-only) |
| `app/Services/WorkerBlocklistService.php` | Blocklist management & matching service |
| `app/Filament/Resources/WorkerBlocklistResource.php` | Admin UI resource (Master Data group) |
| `app/Filament/Resources/WorkerBlocklistResource/Pages/` | List, Create, Edit, View pages |
| `app/Models/Application.php` | Added `blocklist_matches` to `$fillable` and `$casts` |
| `app/Actions/NewApplicationAction.php` | Blocklist check on CV submission |
| `app/Filament/Resources/WorkerResource.php` | Added blocklist badge & blocklist action |
| `app/Filament/Resources/WorkerResource/Pages/EditWorker.php` | "Blocklist Worker" header action |
| `app/Filament/Resources/WorkerResource/Pages/CreateWorker.php` | Blocklist warning notification |
| `app/Providers/AuthServiceProvider.php` | Registered WorkerBlocklistPolicy |
| `tests/Unit/WorkerBlocklistServiceTest.php` | 13 unit tests |

### Deployment Checklist per Instance

```bash
# 1. Pull latest code
cd /home/u805024096/domains/sys.{domain}/public_html/
git pull origin main

# 2. Install/update dependencies
composer install --optimize-autoloader --no-dev

# 3. Run migrations (NEW: worker_blocklists table + blocklist_matches column)
php artisan migrate --force

# 4. Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 5. Rebuild Filament assets (if new resources added)
php artisan filament:upgrade

# 6. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Verify blocklist table exists
php artisan tinker --execute="echo \Schema::hasTable('worker_blocklists') ? 'OK' : 'MISSING';"

# 8. Run tests
php artisan test --filter=WorkerBlocklistServiceTest
```

### Feature Verification

After deployment, verify in Filament admin:

1. **Master Data → Worker Blocklist** — New resource visible
2. **Workers table** — "Blocklist Status" column shows badge if worker matches other agency's blocklist
3. **Edit Worker** — "Blocklist Worker" button in header (own agency only)
4. **Create Worker** — Warning notification if blocklist match found
5. **API CV Submission** — Returns 409 if high-confidence blocklist match

### Database Schema Changes

```sql
-- worker_blocklists table
CREATE TABLE `worker_blocklists` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `agency_id` BIGINT UNSIGNED NOT NULL,
    `blocked_by` BIGINT UNSIGNED NULL,
    `worker_id` BIGINT UNSIGNED NULL,
    `first_name` VARCHAR(255) NULL,
    `last_name` VARCHAR(255) NULL,
    `middle_name` VARCHAR(255) NULL,
    `passport_number` VARCHAR(255) NULL,  -- INDEXED
    `email` VARCHAR(255) NULL,            -- INDEXED
    `contact_number` VARCHAR(255) NULL,
    `date_birth` DATE NULL,
    `place_birth` VARCHAR(255) NULL,
    `reason` TEXT NOT NULL,
    `notes` TEXT NULL,
    `match_confidence` ENUM('high','medium','low') DEFAULT 'medium',
    `is_active` BOOLEAN DEFAULT TRUE,
    `blocked_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    INDEX `worker_blocklists_agency_id_is_active_index` (`agency_id`, `is_active`),
    INDEX `worker_blocklists_passport_number_index` (`passport_number`),
    INDEX `worker_blocklists_email_index` (`email`),
    FOREIGN KEY (`agency_id`) REFERENCES `agencies`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`blocked_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`worker_id`) REFERENCES `workers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- applications.blocklist_matches column
ALTER TABLE `applications` ADD `blocklist_matches` JSON NULL;
```

### Key Configuration

**No new env variables required** - feature works with existing configuration.

**Filament Navigation**: Added to "Master Data" group with navigation badge showing active blocklist count.

**Authorization**: 
- `viewAny`, `view` → All authenticated users (for cross-agency checking)
- `create` → All authenticated users (create for own agency)
- `update`, `delete` → Only owning agency (enforced by WorkerBlocklistPolicy)

### Testing

```bash
# Run all tests
php artisan test

# Run blocklist-specific tests
php artisan test --filter=WorkerBlocklistServiceTest

# Expected: 13 passed (matching algorithm, agency isolation, FK constraints)
```

### Rollback Plan

If issues arise:

```bash
# Revert migrations
php artisan migrate:rollback --step=2

# Or specific migrations
php artisan migrate:reset --path=database/migrations/2026_10_30_022452_create_worker_blocklists_table.php
php artisan migrate:reset --path=database/migrations/2026_10_30_023422_add_blocklist_matches_to_applications_table.php

# Clear caches
php artisan config:clear && php artisan cache:clear
```

---

## Other Server (NOT admin-agency)

**187.127.100.178** - Different projects:
- `/var/www/asclepius` - Medical system
- `/var/www/jobconnections` - Vite/React
- `/var/www/rozana-travels` - Backend + Frontend
- `/var/www/yaramay` - Vite/React frontend
- `/var/www/html` - Static assets

---

## Deployment Pattern

```
/home/u805024096/domains/sys.{domain}/public_html/
```

Each `sys.*` subdomain = separate Laravel deployment = separate database = separate agency tenant

---

## Key Observations

1. **Multi-tenant architecture**: Single codebase, multiple databases, one per agency
2. **FRA implementation active**: 1 FRA user linked to 1 foreign agency
3. **Real production data**: Live deployments, monitoring, emergency reports
4. **Telescope enabled**: Debug tables present in production
5. **Media library**: Spatie media table in use
6. **Queue**: Sync driver (no Horizon/Redis queues)
7. **Mail**: Log driver (Mailpit for local, no production SMTP configured)
8. **Worker Blocklisting**: Cross-agency worker identity verification via weighted matching algorithm

---

## Credentials Summary (SENSITIVE)

| Deployment | DB Name | DB User | DB Password |
|------------|---------|---------|-------------|
| sys.joselinemanpowercorp.com | u805024096_1010 | u805024096_1010admin | Ephesians@2023 |
| sys.wsprecruitment.com | (check .env) | (check .env) | (check .env) |
| sys.yaramay.online | (check .env) | (check .env) | (check .env) |
| sys.elb-ph.online | (check .env) | (check .env) | (check .env) |

**SSH Key**: Uses SSH agent forwarding from local machine

---

## Useful Commands

```bash
# Check all admin-agency deployments
ssh -p 65002 u805024096@178.16.135.22 'find /home/u805024096/domains -maxdepth 3 -name ".git" -execdir sh -c "git remote -v | grep -q admin-agency && pwd" \; 2>/dev/null'

# Access specific deployment DB
ssh -p 65002 u805024096@178.16.135.22 'mysql -u u805024096_1010admin -pEphesians@2023 u805024096_1010'

# View logs
ssh -p 65002 u805024096@178.16.135.22 'tail -f /home/u805024096/domains/sys.joselinemanpowercorp.com/public_html/storage/logs/laravel.log'

# Verify blocklist table on specific deployment
ssh -p 65002 u805024096@178.16.135.22 'cd /home/u805024096/domains/sys.joselinemanpowercorp.com/public_html && php artisan tinker --execute="echo DB::table(\"worker_blocklists\")->count();"'
```
