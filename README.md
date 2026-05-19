# FRA User Type Implementation

This document records the Foreign Recruitment Agency (FRA) user type implementation and related Filament changes.

## 1) Data Model and Migrations

### User Type Support
- Added `user_type` to the users table with default `AGENCY` to preserve existing behavior.
- Introduced `User::TYPE_AGENCY` and `User::TYPE_FRA` constants.

### FRA Account Assignment
- Added `foreign_agency_user` pivot table to allow multiple FRA accounts per Foreign Agency and multiple agencies per user.
- Relationships:
  - `User::foreignAgencies()`
  - `ForeignAgency::users()`

**Files**
- `database/migrations/2026_05_19_000000_add_user_type_to_users_table.php`
- `database/migrations/2026_05_19_000001_create_foreign_agency_user_table.php`
- `app/Models/User.php`
- `app/Models/ForeignAgency.php`

## 2) Access Control and Menus

### FRA Navigation
- FRA users only see the `Assigned Workers` menu under the `FRA` group.
- All other resources are restricted from FRA accounts using a shared base resource.

**Files**
- `app/Filament/Resources/BaseResource.php`
- `app/Filament/Resources/WorkerResource.php`

## 3) Worker Visibility and Resume Access

- FRA users only see workers that have deployments assigned to their linked foreign agencies.
- Worker listing remains read-only for FRA users (no create/edit/delete).
- Resume generation (`generate_cv`) remains available.

**Files**
- `app/Filament/Resources/WorkerResource.php`

## 4) FRA Dashboard Stats

- Dashboard widget counts only assigned workers and deployments for the FRA.
- Applications count is set to `0` for FRA users.

**Files**
- `app/Filament/Resources/Resource/Widgets/WorkerOverview.php`

## 5) FRA Monitoring Banner

- Monitoring alerts are scoped to workers under the FRA’s foreign agency deployments.

**Files**
- `app/Services/MonitoringService.php`
- `app/Filament/Resources/WorkerResource/Pages/ListWorkers.php`

## 6) FRA Account Management (Agency)

- Added Filament resource `FRA Accounts` under the agency tenant for creating and managing FRA logins.
- FRA accounts are tied to both the agency tenant (`agency_user`) and foreign agencies (`foreign_agency_user`).
- Passwords are hashed on creation and update.

**Files**
- `app/Filament/Resources/ForeignAgencyAccountResource.php`
- `app/Filament/Resources/ForeignAgencyAccountResource/Pages/ListForeignAgencyAccounts.php`
- `app/Filament/Resources/ForeignAgencyAccountResource/Pages/CreateForeignAgencyAccount.php`
- `app/Filament/Resources/ForeignAgencyAccountResource/Pages/EditForeignAgencyAccount.php`

## 7) Notes

- Existing user management remains for agency users.
- Super Admin user management can still create FRA users and assign foreign agencies.
