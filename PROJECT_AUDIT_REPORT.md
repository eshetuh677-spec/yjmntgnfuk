# EFPT-AMS — Professional Project Audit & Verification Report

**Current verification:** October 8, 2026  
**Historical audit snapshot:** August 21, 2026 (last updated September 4, 2026)
**Project:** Ethiopia Federal Police Commission — Technology Expansion Asset Management System  
**Version:** 1.0.0  
**Report note:** Sections 1–12 preserve the earlier audit snapshot. Its counts, security findings, and deployment recommendations have not all been re-audited and must not be treated as current certification.

---

## Executive Summary

This document records an earlier project review, not a current production-readiness certification. Current automated verification and the fixes made during the October 8, 2026 review are recorded in [Current Verification](#13-current-verification-october-8-2026).

**Overall Grade: B+** (was C, after fixes)

---

## 1. System Architecture

### Technology Stack
- **Backend:** PHP 8.x (Procedural MVC-inspired)
- **Database:** MySQL 8.x / MariaDB
- **Frontend:** HTML5, CSS3 (custom dark theme), Vanilla JavaScript
- **Server:** XAMPP (Apache + MySQL)
- **Architecture:** Role-Based Access Control (RBAC) with hierarchy-scoped data

### Directory Structure
```
EFPT_AMS/
├── includes/           # Core framework (config, DB, auth, sidebar, header, footer)
├── admin/              # System Administrator pages (22 files)
├── store/              # Store Manager + Org Admin operations
├── shared/             # Cross-role pages (assets, requisitions, maintenance, etc.)
├── user/               # Regular User dashboard
├── dept_head/          # Department Head dashboard + approvals
├── class_head/         # Class Head dashboard + approvals
├── main_department_head/ # Main Dept Head dashboard + approvals
├── maintenance_head/   # Maintenance Head dashboard
├── maintenance_officer/ # Maintenance Officer / Inspector dashboard
├── scripts/            # CLI setup/migration tools
├── database/           # SQL schema and migrations
├── assets/             # CSS, JS, i18n translations
├── uploads/            # User uploads (signatures)
└── README.md           # Project documentation
```

---

## 2. Organizational Hierarchy (EFPT Structure)

```
Ethiopia Federal Police Commission (Organization)
├── Main Department: Administration & Human Resources
│   ├── Department: Human Resources
│   ├── Department: Finance & Budget
│   └── Department: Planning & Policy
├── Main Department: Operations & Field Services
│   ├── Department: Field Operations North
│   │   └── Class/Unit: Patrol Unit Alpha
│   │   └── Class/Unit: Rapid Response Team
│   ├── Department: Field Operations South
│   ├── Department: Traffic Police
│   └── Department: Public Order & Riot Control
├── Main Department: Investigation & Forensics
│   ├── Department: Criminal Investigation
│   ├── Department: Forensics Laboratory
│   └── Department: Intelligence & Security
├── Main Department: Technology & Digital Innovation
│   ├── Department: ICT Infrastructure
│   │   └── Class/Unit: Network Operations
│   ├── Department: Software Development
│   └── Department: Cybersecurity
├── Main Department: Maintenance & Engineering
│   ├── Department: Vehicle Maintenance
│   │   └── Class/Unit: Light Vehicle Workshop
│   ├── Department: Equipment Repair Workshop
│   └── Department: Engineering & Construction
└── Main Department: Logistics & Supply Chain
    ├── Department: Central Stores
    └── Department: Procurement
```

---

## 3. Actors / Roles (9 Total)

| # | Role | Dashboard | Primary Function |
|---|------|-----------|------------------|
| 1 | `admin` | `admin/dashboard.php` | Full system access, user management, org setup |
| 2 | `org_admin` | `store/dashboard.php` | Scoped admin for one organization |
| 3 | `store_manager` | `store/dashboard.php` | Asset registration, fulfillment, maintenance reporting |
| 4 | `main_department_head` | `main_department_head/dashboard.php` | Approves requisitions at main dept level |
| 5 | `department_head` | `dept_head/dashboard.php` | Approves requisitions at dept level |
| 6 | `class_head` | `class_head/dashboard.php` | Approves requisitions at class level, assigns maintenance teams |
| 7 | `maintenance_head` | `maintenance_head/dashboard.php` | Receives reports, assigns inspectors, completes jobs |
| 8 | `maintenance_officer` | `maintenance_officer/dashboard.php` | Performs inspections, logs progress, executes repairs |
| 9 | `regular_user` | `user/dashboard.php` | Requests assets, tracks requisitions, returns assets |

---

## 4. Workflow Verification

### 4.1 Asset Requisition Workflow (Model 20)
```
Regular User → Class Head → Dept Head → Main Dept Head → Store Manager → User Acceptance
```
**Status:** ✅ WORKING END-TO-END

### 4.2 Maintenance Workflow (Section 21)
```
Store Manager → (Report) → Maintenance Head → (Assign Team) → Maintenance Officer → (Complete) → Asset Restored
```
**Status:** ✅ WORKING END-TO-END

### 4.3 Asset Return Workflow (Model 19)
```
User → Return Request → Store Manager → Inspect → Accept/Reject
```
**Status:** ✅ WORKING END-TO-END

### 4.4 Transfer Workflow
```
Store Manager (Source) → Transfer → Store Manager (Destination) → Accept
```
**Status:** ✅ WORKING END-TO-END

---

## 5. Critical Bugs Found & Fixed

### FIXED: Class Head Dashboard (BUG-001)
**Problem:** Queried legacy `requisitions` and `assets` tables  
**Fix:** Updated to `model20_requisitions` and `fixed_assets`/`current_assets`  
**File:** `class_head/dashboard.php`  
**Status:** ✅ FIXED

### FIXED: Main Dept Head Dashboard (BUG-002)
**Problem:** Queried legacy `requisitions` and `assets` tables  
**Fix:** Updated to `model20_requisitions` and `fixed_assets`/`current_assets`  
**File:** `main_department_head/dashboard.php`  
**Status:** ✅ FIXED

### FIXED: Dept Head Dashboard (BUG-003)
**Problem:** Queried legacy `assets` table for asset counts  
**Fix:** Updated to `fixed_assets` and `current_assets`  
**File:** `dept_head/dashboard.php`  
**Status:** ✅ FIXED

### FIXED: Header Navigation Bug (BUG-004)
**Problem:** `main_department_head` header brand link pointed to `user/dashboard.php` (access denied)  
**Fix:** Added `case 'main_department_head'` in `includes/header.php`  
**File:** `includes/header.php`  
**Status:** ✅ FIXED

### FIXED: Role Permissions System (BUG-005)
**Problem:** Dead code referenced non-existent `roles` table and `role_id` column  
**Fix:** Removed dead code paths, normalized to ENUM-based `role_key` system  
**Files:** `includes/auth.php`, `admin/manage_roles.php`, `shared/dynamic_dashboard.php`  
**Status:** ✅ FIXED

### FIXED: Database Schema (BUG-006)
**Problem:** `users.role` ENUM missing `maintenance_head` and `maintenance_officer`  
**Fix:** Updated ENUM definition and auto-migration in `db_connect.php`  
**Status:** ✅ FIXED

---

## 6. Security Audit Results

### ✅ PASS: SQL Injection
All database queries use prepared statements (`db_query`, `db_execute`) or properly escaped values.

### ✅ PASS: XSS Protection
All user-facing output uses `esc()` or `htmlspecialchars()`.

### ⚠️ WARNING: CSRF Protection
**Issue:** No CSRF tokens on any POST form  
**Impact:** Medium — forms vulnerable to cross-site request forgery  
**Recommendation:** Add CSRF token generation and validation to all POST forms

### ⚠️ WARNING: Hardcoded Credentials
**Issue:** Database credentials in `config.php`, default admin password displayed on login  
**Impact:** High for production, acceptable for local dev  
**Recommendation:** Move credentials to environment variables, enforce password change on first login

### ⚠️ WARNING: Schema Migration on Every Request
**Issue:** `ensure_schema()` runs ALTER TABLE on every page load  
**Impact:** Performance overhead in production  
**Recommendation:** Run migrations once via CLI, remove from request cycle

---

## 7. Database Schema Status

### Historical snapshot: 53 tables reported (superseded by Section 13)
All tables exist and are properly indexed. The uniform requisition, CCTV, and maintenance team tables are now included in the single schema file.
- ✅ `users` (with maintenance roles in ENUM)
- ✅ `organizations`, `main_departments`, `departments`, `classes_units`
- ✅ `fixed_assets`, `current_assets`
- ✅ `model20_requisitions`, `model22_vouchers`
- ✅ `asset_returns`, `asset_transfers`
- ✅ `maintenance_records`, `maintenance_team_members`
- ✅ `notifications`, `announcements`
- ✅ `role_permissions` (with `role_key` + `modules` columns)
- ✅ Foreign keys properly ordered and enforced

### No missing tables — all tables consolidated into `sqlSchemaAll.sql`

---

## 8. Sample Data & Demo Credentials

### Database State (After Seed Script)
- Organizations: 1 (EFPC)
- Main Departments: 7
- Departments: 23
- Classes/Units: 40
- Fixed Assets: 33
- Users: 22 (16 original + 6 added)
- Announcements: 3

### Demo Users

| Username | Password | Role | Full Name |
|----------|----------|------|-----------|
| `admin` | `Admin@123` | System Administrator | System Administrator |
| `Nuru` | `Nuru@123` | System Administrator | Nuru Ali |
| `org.admin` | `org123` | Organization Admin | Dinaol Abera Abebe |
| `store.abadina` | `store123` | Store Manager | Habtamu Eshetu Bekele |
| `store.mexico` | `store123` | Store Manager | Tadesse Lemma Girma |
| `maindept.head` | `maint123` | Main Dept Head | Diriba Gruma Tolera |
| `dept.sysdev` | `dept123` | Dept Head | Teshale Tuffa Bekele |
| `dept.network` | `dept123` | Dept Head | Abebe Geleta Feyissa |
| `dept.radio` | `dept123` | Dept Head | Kebede Mulugeta Desta |
| `dept.cctv` | `dept123` | Dept Head | Yohannes Tekle Hailu |
| `class.software` | `class123` | Class Head | Bewketu Animaw Worku |
| `class.patrol.a` | `class123` | Class Head | Sgt. Alemu Kebede |
| `dept.hr` | `dept123` | Dept Head | W/ro Fatuma Ahmed |
| `maint.head` | `mainthead123` | Maintenance Head | Eng. Getachew Worku |
| `insp.1` | `insp123` | Maintenance Officer | Tech. Daniel Tesfaye |
| `insp.2` | `insp123` | Maintenance Officer | Tech. Meron Abebe |
| `user.elyas` | `user123` | Regular User | Elyas Bakbag Mohammed |
| `user.fatuma` | `user123` | Regular User | Fatuma Aliyi Hassan |
| `user.solomon` | `user123` | Regular User | Solomon Tesfaye Kebede |
| `user.meron` | `user123` | Regular User | Meron Getachew Wolde |
| `user.dawit` | `user123` | Regular User | Dawit Bekele Alemu |
| `user.patrol.1` | `user123` | Regular User | Const. Abebe Kebede |

---

## 10. Serial Number & Keeper Tracking (September 2026 Update)

### Problem Identified
PHP INSERT/UPDATE statements referenced `serial_number`, `keeper_id`, and `source_table` columns across `fixed_assets`, `current_assets`, `model22_vouchers`, and `stock_transactions` tables — but these columns did not exist in the schema, causing SQL errors on production database operations.

### Fixes Applied
| File | Change |
|---|---|
| `database/migrations/add_serial_number_tracking.sql` | **CREATED** — Idempotent migration adding all missing columns with INFORMATION_SCHEMA guards |
| `database/sqlSchemaAll.sql` | **UPDATED** — Added `serial_number`, `keeper_id` to `fixed_assets`; `serial_number` to `current_assets`; `serial_number`, `source_table` to `model22_vouchers`; `serial_number` to `stock_transactions` |
| `includes/db_schema.php` | **UPDATED** — Added `ensure_serial_number_columns()` called from `ensure_schema()` for runtime auto-migration |
| `includes/stock_service.php` | **UPDATED** — `logTransaction()` accepts and stores `serial_number`; `assignFixedAsset()` fetches serial per-row |
| `store/generate_model22.php` | **UPDATED** — Serial number captured from dropdown, stored in voucher, displayed on form and preview |
| `store/print_voucher.php` | **UPDATED** — Displays "Asset Serial No" in voucher info grid |
| `shared/transfer_voucher.php` | **UPDATED** — Displays serial number in asset information table |
| `shared/view_fixed_asset.php` | **UPDATED** — Edit modal includes serial_number and keeper_id fields |
| `store/view_fixed_stock.php` | **UPDATED** — Serial number column in asset listing and transaction log |
| `store/view_stock.php` | **UPDATED** — Serial number column in asset listing and transaction log |

**Status:** ✅ FIXED — All PHP INSERT/UPDATE statements now reference columns that exist in the schema

---

## 11. Recommendations for Production Deployment

### Before Launch
1. **Fix CSRF protection** — Add tokens to all POST forms
2. **Move credentials to environment** — Use `.env` file, not hardcoded
3. **Enforce password change** — Force admin to change default password on first login
4. **Run migrations once** — Remove `ensure_schema()` from request cycle, run via CLI
5. **Enable HTTPS** — Update `SITE_URL` to use `https://`
6. **Add email notifications** — For approval requests and maintenance alerts
7. **Add password reset** — Self-service password recovery

### Nice-to-Have
1. Barcode/QR scanning for asset tracking
2. Mobile-responsive design
3. Advanced analytics dashboard
4. Bulk asset import/export (CSV/Excel)
5. Asset depreciation tracking
6. REST API for external integrations

---

## 12. Conclusion

EFPT-AMS is a **solid, well-architected asset management system** with:
- Complete role-based access control across 9 distinct roles
- End-to-end workflows for requisition, maintenance, returns, and transfers
- Professional dark-themed UI with hierarchy-aware navigation
- Audit logging and notification systems
- Multi-language support framework

All critical bugs identified during the audit have been fixed. The system is **ready for demonstration and deployment** with the recommended security enhancements to be addressed in the next iteration.

---

*Report generated: August 21, 2026*  
*System: EFPT-AMS v1.0.0*  
*Historical snapshot last updated: September 4, 2026*

---

## 13. Current Verification (October 8, 2026)

### Automated checks
- PHP syntax: all 216 project PHP files passed `php -l`.
- PHPUnit: 55 tests passed with 210 assertions (latest full-suite run after notification and schema cleanup).
- Browser layout: the Admin Dashboard and CCTV Camera Management page were checked at 320, 360, 375, 414, 768, and 1280 CSS pixels; neither page had document-level horizontal overflow.

### Issues fixed during this verification
- Repaired a PHP parse error in `admin/cctv_cameras.php` caused by unescaped quotes in the inline stylesheet.
- Removed an externally hosted Font Awesome stylesheet from the Admin Dashboard that was blocked by the Content Security Policy.
- Switched CCTV pages to the bundled Leaflet CSS and JavaScript instead of blocked CDN copies.
- Replaced the CCTV offline tile endpoint's GD-dependent PNG generation with SVG output, so it works without the PHP GD extension.
- Aligned the Apache and PHP Content Security Policy allowlists with the map tile and geocoding providers already used by the application.
- Improved compact navigation, page-title actions, card headings, and global search layout for small screens.
- Applied the existing `blocked_ips` migration to the local database after confirming the table was missing; verified its foreign key and login lookup.
- Consolidated all 59 application tables in `database/sqlSchemaAll.sql`; a fresh isolated import succeeded. The live database has five separately preserved `notifications_backup_*` rollback snapshots outside the application schema.
- Removed seven legacy tables only after confirming they were empty and unused; the guarded CLI migration checks for rows and external foreign-key references before dropping them.
- Reconciled notification workflow tables and runtime-created columns with the canonical schema. The live non-backup table inventory matches the schema.

### Verification limits and remaining work
- The automated suite and browser checks do not constitute end-to-end verification of every role, form, workflow, or database operation.
- External basemap and geocoding availability depends on network access and was not certified by these checks. The local SVG tile is an offline placeholder grid, not a geographic basemap.
- Review the historical production recommendations in Section 11 against the deployment environment before release; this verification does not certify the application as production-ready.
