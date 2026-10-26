# EFPT-AMS
## Ethiopia Federal Police Commission — Technology Expansion Asset Management System

**Version:** 1.0.0  
**Status:** Production Ready (Post-Audit)  
**Framework:** PHP 8.x / MySQL 8.x (XAMPP)  
**Architecture:** MVC-inspired, role-based access control (RBAC), hierarchy-scoped data

> **Professional Audit:** See [`PROJECT_AUDIT_REPORT.md`](PROJECT_AUDIT_REPORT.md) for complete end-to-end verification, bug fixes, and security audit results.

---

## 1. System Overview

EFPT-AMS is a comprehensive asset management system designed for the Ethiopia Federal Police Commission (EFPC). It digitalizes the complete asset lifecycle — from requisition and procurement to issuance, maintenance, transfer, return, inspection, and disposal — across the full organizational hierarchy.

### Key Capabilities
- **Digitalized Asset Tracking:** Every fixed and current asset is tracked by serial number, category, location, custodian, and status.
- **Hierarchy-Based Access:** Data is scoped by Organization → Main Department → Department → Class/Unit, ensuring users only see what they are authorized to manage.
- **Sequential Approval Workflow:** Requisitions follow a formal approval chain: Class Head → Department Head → Main Department Head → Store Manager → User Acceptance.
- **Maintenance Integration:** A dedicated maintenance workflow allows Store Managers to report damaged assets, Maintenance Heads to assign inspectors, and Maintenance Officers to complete repairs with full audit trails.
- **Accountability:** Assets are tied to individual custodians with signature capture and accountability tracking.
- **Multi-Language:** Full UI support for English, Amharic (አማርኛ), and Oromo (Afaan Oromoo).
- **Audit Logging:** All critical actions are recorded in `audit_logs` with before/after values.
- **Notifications:** In-app notifications for approval requests, maintenance assignments, and status updates.

---

## 2. Organizational Hierarchy

The system mirrors the Ethiopia Federal Police Commission structure:

```
Ethiopia Federal Police Commission (Organization)
├── Main Department: Administration & Human Resources
│   ├── Department: Human Resources
│   │   └── Class/Unit: HR Recruitment Unit, HR Training Unit, Employee Relations
│   ├── Department: Finance & Budget
│   │   └── Class/Unit: Budget Control Unit, Audit & Compliance
│   └── Department: Planning & Policy
├── Main Department: Operations & Field Services
│   ├── Department: Field Operations North
│   │   └── Class/Unit: Patrol Unit Alpha, Patrol Unit Beta, Rapid Response Team
│   ├── Department: Field Operations South
│   │   └── Class/Unit: Patrol Unit Gamma, Border Security Unit
│   ├── Department: Traffic Police
│   │   └── Class/Unit: Traffic Control Unit, Highway Patrol
│   ├── Department: Radio Operations
│   │   └── Class/Unit: Radio Communications Unit
│   ├── Department: CCTV & Monitoring
│   │   └── Class/Unit: CCTV Control Room
│   └── Department: Public Order & Riot Control
├── Main Department: Investigation & Forensics
│   ├── Department: Criminal Investigation
│   │   └── Class/Unit: Homicide Division, Economic Crimes, Cybercrime Unit
│   ├── Department: Forensics Laboratory
│   │   └── Class/Unit: DNA Laboratory, Ballistics Unit, Digital Forensics
│   └── Department: Intelligence & Security
│       └── Class/Unit: Counter-Intelligence, Surveillance Unit
├── Main Department: Technology & Digital Innovation
│   ├── Department: ICT Infrastructure
│   │   └── Class/Unit: Network Operations, Data Center, End-User Support
│   ├── Department: Software Development
│   │   └── Class/Unit: Application Development, System Administration
│   └── Department: Cybersecurity
│       └── Class/Unit: SOC (Security Operations), Incident Response
├── Main Department: Maintenance & Engineering
│   ├── Department: Vehicle Maintenance
│   │   └── Class/Unit: Light Vehicle Workshop, Heavy Vehicle Workshop, Spare Parts Store
│   ├── Department: Equipment Repair Workshop
│   │   └── Class/Unit: Electronics Repair, Weapons Maintenance
│   └── Department: Engineering & Construction
│       └── Class/Unit: Building Maintenance, Electrical Services
└── Main Department: Logistics & Supply Chain
    ├── Department: Central Stores
    │   └── Class/Unit: General Stores, Armory, Fuel Depot
    └── Department: Procurement
        └── Class/Unit: Tender & Bidding, Vendor Management
```

---

## 3. Actors / Roles (9 Total)

The system defines **9 roles** with granular permissions managed via the `role_permissions` table:

| # | Role Key | Label | Dashboard | Primary Responsibility |
|---|----------|-------|-----------|----------------------|
| 1 | `admin` | System Administrator | `admin/dashboard.php` | Full system access, user management, organization setup, role permissions |
| 2 | `org_admin` | Organization Administrator | `store/dashboard.php` | Scoped admin for one organization; manages users, departments, classes within assigned org |
| 3 | `store_manager` | Store Manager | `store/dashboard.php` | Asset registration, stock management, requisition fulfillment, voucher issuance, maintenance reporting |
| 4 | `main_department_head` | Main Department Head | `main_department_head/dashboard.php` | Approves requisitions at main dept level, oversees main dept assets |
| 5 | `department_head` | Department Head | `dept_head/dashboard.php` | Approves requisitions at dept level, manages dept assets |
| 6 | `class_head` | Class Head | `class_head/dashboard.php` | Approves requisitions at class level, manages class assets, assigns maintenance teams |
| 7 | `maintenance_head` | Maintenance Head | `maintenance_head/dashboard.php` | Receives maintenance reports, assigns inspectors/team members, completes maintenance jobs |
| 8 | `maintenance_officer` | Maintenance Officer / Inspector | `maintenance_officer/dashboard.php` | Performs inspections, logs progress, executes repairs, records outcomes |
| 9 | `regular_user` | Regular User | `user/dashboard.php` | Requests assets, tracks requisitions, returns assets, views personal dashboard |

### Role Permission Matrix

| Permission | admin | org_admin | store_manager | main_dept_head | dept_head | class_head | maint_head | maint_officer | regular_user |
|------------|-------|-----------|---------------|----------------|-----------|------------|------------|---------------|--------------|
| Read | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Write | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✗ |
| Update | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✗ | ✗ |
| Delete | ✓ | ✓ | ✓ | ✓ | ✗ | ✗ | ✓ | ✗ | ✗ |

---

## 4. Sample Users & Demo Credentials

After running the seed script (`scripts/seed_sample_data.php`), **22 demo users** across all 9 roles are available. Use these credentials to test the system.

### Complete Credentials Table

| Username | Password | Full Name | Role | Organization | Main Dept | Department | Class/Unit |
|----------|----------|-----------|------|------------|-----------|------------|------------|
| `admin` | `Admin@123` | System Administrator | admin | — | — | — | — |
| `Nuru` | `Nuru@123` | Nuru Ali | admin | EFPC | — | — | — |
| `org.admin` | `org123` | Dinaol Abera Abebe | org_admin | EFPC | — | — | — |
| `store.abadina` | `store123` | Habtamu Eshetu Bekele | store_manager | EFPC | Logistics & Supply Chain | Central Stores | — |
| `store.mexico` | `store123` | Tadesse Lemma Girma | store_manager | EFPC | Logistics & Supply Chain | Central Stores | — |
| `maindept.head` | `maint123` | Diriba Gruma Tolera | main_department_head | EFPC | Technology & Digital Innovation | — | — |
| `dept.sysdev` | `dept123` | Teshale Tuffa Bekele | department_head | EFPC | Technology & Digital Innovation | Software Development | Application Development |
| `dept.network` | `dept123` | Abebe Geleta Feyissa | department_head | EFPC | Technology & Digital Innovation | ICT Infrastructure | Network Operations |
| `dept.radio` | `dept123` | Kebede Mulugeta Desta | department_head | EFPC | Operations & Field Services | Radio Operations | Radio Communications Unit |
| `dept.cctv` | `dept123` | Yohannes Tekle Hailu | department_head | EFPC | Operations & Field Services | CCTV & Monitoring | CCTV Control Room |
| `class.software` | `class123` | Bewketu Animaw Worku | class_head | EFPC | Technology & Digital Innovation | Software Development | Application Development |
| `class.patrol.a` | `class123` | Sgt. Alemu Kebede | class_head | EFPC | Operations & Field Services | Field Operations North | Patrol Unit Alpha |
| `dept.hr` | `dept123` | W/ro Fatuma Ahmed | department_head | EFPC | Administration & Human Resources | Human Resources | — |
| `maint.head` | `mainthead123` | Eng. Getachew Worku | maintenance_head | EFPC | Maintenance & Engineering | Vehicle Maintenance | Light Vehicle Workshop |
| `insp.1` | `insp123` | Tech. Daniel Tesfaye | maintenance_officer | EFPC | Maintenance & Engineering | Equipment Repair Workshop | Electronics Repair |
| `insp.2` | `insp123` | Tech. Meron Abebe | maintenance_officer | EFPC | Maintenance & Engineering | Equipment Repair Workshop | Electronics Repair |
| `user.elyas` | `user123` | Elyas Bakbag Mohammed | regular_user | EFPC | Operations & Field Services | Field Operations North | Patrol Unit Alpha |
| `user.fatuma` | `user123` | Fatuma Aliyi Hassan | regular_user | EFPC | Technology & Digital Innovation | ICT Infrastructure | End-User Support |
| `user.solomon` | `user123` | Solomon Tesfaye Kebede | regular_user | EFPC | Technology & Digital Innovation | ICT Infrastructure | Network Operations |
| `user.meron` | `user123` | Meron Getachew Wolde | regular_user | EFPC | Investigation & Forensics | Criminal Investigation | Homicide Division |
| `user.dawit` | `user123` | Dawit Bekele Alemu | regular_user | EFPC | Administration & Human Resources | Finance & Budget | Budget Control Unit |
| `user.patrol.1` | `user123` | Const. Abebe Kebede | regular_user | EFPC | Operations & Field Services | Field Operations North | Patrol Unit Alpha |

### Quick Login Reference

| Role | Username | Password |
|------|----------|----------|
| System Admin | `admin` | `Admin@123` |
| Org Admin | `org.admin` | `org123` |
| Store Manager | `store.abadina` | `store123` |
| Main Dept Head | `maindept.head` | `maint123` |
| Department Head | `dept.sysdev` | `dept123` |
| Class Head | `class.software` | `class123` |
| Maintenance Head | `maint.head` | `mainthead123` |
| Maintenance Officer | `insp.1` | `insp123` |
| Regular User | `user.elyas` | `user123` |

### End-to-End Test Scenario

To test the complete asset lifecycle, log in as these users in sequence:

1. **`user.elyas` / `user123`** — Regular User requesting a laptop
2. **`class.patrol.a` / `class123`** — Class Head approving the request
3. **`dept.sysdev` / `dept123`** — Department Head reviewing
4. **`maindept.head` / `maint123`** — Main Department Head approving
5. **`store.abadina` / `store123`** — Store Manager issuing the asset
6. **`maint.head` / `mainthead123`** — Maintenance Head reporting a damaged asset
7. **`insp.1` / `insp123`** — Maintenance Officer performing the repair

---

## 5. Key Workflows

### 5.1 Asset Requisition Workflow (Model 20)

```
Regular User
  │  Requests asset via Model 20 Requisition
  ▼
Class Head
  │  Reviews & approves/rejects at class level
  ▼
Department Head
  │  Reviews & approves/rejects at dept level
  ▼
Main Department Head
  │  Reviews & approves/rejects at main dept level
  ▼
Store Manager
  │  Fulfills requisition → issues Model 22 Voucher
  ▼
User Acceptance
  │  User confirms receipt
  ▼
Asset Status: ISSUED (with custodian tracked)
```

### 5.2 Asset Return Workflow (Model 19)

```
Regular User / Custodian
  │  Initiates return request
  ▼
Store Manager
  │  Inspects asset condition
  │  Accepts return → Asset → AVAILABLE
  │  OR rejects with reason
  ▼
Asset Status: AVAILABLE (or DAMAGED if condemned)
```

### 5.3 Maintenance Workflow (Section 21)

```
Store Manager
  │  Reports asset for maintenance
  │  Selects: Asset, Maintenance Team, Type, Description
  │  Preserves custodian ID through cycle
  ▼
Asset Status → under_maintenance
  │
  ▼
Class Head / Maintenance Head
  │  Receives notification
  │  Assigns team members (class members + maintenance officers)
  │  Selects team lead
  │  Records assignment notes
  ▼
Maintenance Officers (Inspectors)
  │  Receive notification of assignment
  │  See jobs in "My Assigned Jobs"
  │  Log progress notes
  │  Execute repairs
  ▼
Class Head / Maintenance Head
  │  Marks job complete with outcome:
  │    - REPAIRED → Asset → AVAILABLE (or ISSUED if had custodian)
  │    - UNREPAIRABLE → Asset → DAMAGED
  │    - NEEDS PARTS → stays UNDER_MAINTENANCE
  ▼
Notifications sent to Store Manager, original custodian, assigned inspector
  ▼
Store Manager
  │  Reviews outcome
  │  If repaired: makes asset available for re-issuance
  │  If unrepairable: initiates disposal process
```

### 5.4 Transfer Workflow

```
Store Manager (Source)
  │  Initiates transfer to another store
  ▼
Store Manager (Destination)
  │  Accepts transfer
  ▼
Asset Status: ASSIGNED (to new store)
```

### 5.5 Inspection Workflow

```
Store Manager / Class Head
  │  Creates inspection record
  ▼
Inspector / Maintenance Officer
  │  Conducts inspection
  │  Records findings
  ▼
Result: PASS / FAIL / NEEDS REPAIR
  │
  ├── PASS → Asset remains in current status
  ├── FAIL → Sent to maintenance
  └── NEEDS REPAIR → Sent to maintenance
```

---

## 6. Database Schema

The complete application schema is consolidated in **`database/sqlSchemaAll.sql`** (59 tables). The five `notifications_backup_*` rollback snapshots in some existing databases are intentionally excluded. Seven verified-empty legacy tables were removed; run `database/migrations/prune_empty_legacy_tables.php` on an older installation to remove them safely (the migration refuses populated or externally referenced tables).

### Core Tables

| Table | Purpose |
|-------|---------|
| `users` | All system users with roles and hierarchy FK columns |
| `organizations` | Top-level orgs (EFPC is one org) |
| `main_departments` | Level 2: Main departments |
| `departments` | Level 3: Departments under main departments |
| `classes_units` | Level 4: Teams/units under departments |
| `fixed_assets` | Durable assets with serial numbers, status, custodian tracking |
| `current_assets` | Consumable/current assets (stock items) |
| `model20_requisitions` | Asset requisition requests (sequential approval) |
| `login_attempts` | Login rate-limiting history |
| `model22_vouchers` | Asset issuance vouchers |
| `asset_returns` | Return requests |
| `asset_transfers` | Transfer between stores |
| `maintenance_records` | Maintenance jobs with full lifecycle tracking |
| `maintenance_team_members` | Team assignments per maintenance job |
| `asset_inspections` | Inspection records |
| `disposal_requests` | Disposal/write-off requests |
| `writeoff_requests` | Write-off requests |
| `stock_transactions` | Stock movement records |
| `notifications` | In-app notifications |
| `announcements` | System-wide announcements |
| `role_permissions` | Granular CRUD permissions per role |
| `role_module_permissions` | Module-level permission overrides |
| `user_scopes` | Delegated access scopes |
| `revoked_delegation_tasks` | Pending automation tasks for revoked delegation |
| `audit_logs` | Audit trail of all critical actions |
| `property_documents` | Document management |
| `signatures` | Digital signature tracking |
| `user_cards` | User ID card management |
| `asset_categories` | Category tree for assets |
| `suppliers` | Supplier / vendor registry |
| `stores` | Physical store/location management |
| `buildings` | Building registry |
| `floors` | Floor registry |
| `rooms` | Room registry |
| `locations` | Hierarchical location management |
| `app_settings` | Application settings (categories, locations, stores) |
| `system_settings` | System-wide settings |

### Supplemental Tables (Uniform & CCTV)

| Table | Purpose |
|-------|---------|
| `uniform_item_types` | Uniform categories (Uniforms, Footwear, Accessories) |
| `uniform_item_selections` | Selectable items (Scope, Pan, Notebook, etc.) |
| `uniform_item_sizes` | Available sizes per selection |
| `uniform_requisitions` | Uniform requisition requests |
| `uniform_requisition_items` | Items in each requisition |
| `uniform_vouchers` | Issued uniform vouchers |
| `uniform_requisition_notifications` | Notifications for uniform workflow |
| `cctv_cameras` | CCTV camera registry |
| `cctv_status_logs` | Camera status history |

### Foreign Key Constraints

All foreign keys are defined in Section 17 of `sqlSchemaAll.sql` with proper `ON DELETE SET NULL` or `ON DELETE CASCADE` behavior. Key constraints:
- `users` table links to `organizations`, `main_departments`, `departments`, `classes_units`, `stores`
- All transactional tables (`model20_requisitions`, `asset_transfers`, `asset_returns`, etc.) link to `users` for approvers/custodians
- Child records use `ON DELETE CASCADE` (e.g., `requisition_items` → `requisitions`)

### Asset Status Enum

| Status | Meaning |
|--------|---------|
| `available` | In store, ready for issue |
| `assigned` | Issued to a custodian |
| `returned` | Returned pending store acceptance |
| `condemned` | Deemed unfit for use |
| `under_maintenance` | Currently in repair |
| `damaged` | Unrepairable, flagged for disposal |

---

## 7. Deployment Guide

### Prerequisites
- XAMPP (PHP 8.0+, MySQL 8.0+) or equivalent LAMP stack
- Apache web server with `mod_rewrite`
- Modern browser (Chrome, Firefox, Edge)
- PHP extensions: `mysqli`, `pdo`, `json`, `mbstring`, `openssl`, `gd`

### Installation Steps

1. **Copy Project to Web Root**
   ```
   C:\xampp\htdocs\EFPT_AMS\
   ```

2. **Create Database**
   ```sql
   CREATE DATABASE efpt_ams CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
   Or import via phpMyAdmin: http://localhost/phpmyadmin

3. **Import Schema**
   ```bash
   mysql -u root -p efpt_ams < database/sqlSchemaAll.sql
   ```
   Alternatively, use phpMyAdmin's Import feature.

4. **Configure Database Connection**
   Edit `includes/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // XAMPP default is empty
   define('DB_NAME', 'efpt_ams');
   ```

5. **Seed Sample Data (Optional)**
   ```bash
   php scripts/seed_sample_data.php
   ```
   This creates the EFPC organization, 7 main departments, 25 departments, 44 classes/units, 22 demo users with known credentials, 15 sample assets, maintenance records, and a welcome announcement.

6. **Access the System**
   - URL: `http://localhost/EFPT_AMS/`
   - Username: `admin`
   - Password: `Admin@123`

### Post-Deployment Checklist
- [ ] Verify database connection
- [ ] Login as admin and change the default password
- [ ] Run `scripts/seed_sample_data.php` for demo data
- [ ] Assign users to their correct organizational hierarchy
- [ ] Configure asset categories if needed
- [ ] Set up stores and physical locations
- [ ] Test the maintenance workflow end-to-end
- [ ] Verify audit logging is working

---

## 8. File Structure

```
EFPT_AMS/
├── index.php                     # Login page
├── set_language.php              # Language switcher handler (AJAX)
├── includes/
│   ├── config.php                # DB credentials, site constants
│   ├── db_connect.php            # DB connection + ensure_schema() auto-migration
│   ├── auth.php                  # Authentication, RBAC, hierarchy scoping
│   ├── header.php                # HTML head + navigation branding
│   ├── footer.php                # HTML footer + scripts
│   ├── sidebar.php               # Role-aware navigation
│   ├── i18n.php                  # Translation framework (en, am, om)
│   ├── global_search.php         # Global search across records
│   └── approval_helpers.php      # Approval chain logic helpers
├── admin/                        # System Administrator pages
│   ├── dashboard.php             # Admin overview stats
│   ├── manage_users.php          # User CRUD
│   ├── edit_user.php             # User detail/edit
│   ├── organizations.php         # Organization management
│   ├── main_departments.php      # Main department CRUD
│   ├── departments.php           # Department CRUD
│   ├── classes_units.php         # Class/unit CRUD
│   ├── role_permissions.php      # Role permission matrix
│   ├── announcements.php         # Announcement management
│   ├── settings.php              # System settings
│   └── ...                       # Stores, buildings, assets, etc.
├── store/                        # Store Manager + Org Admin
│   ├── dashboard.php             # Store overview
│   ├── add_fixed_asset.php       # Register fixed asset
│   ├── view_stock.php            # Full inventory
│   ├── generate_model22.php      # Issue voucher (Model 22)
│   └── ...                       # Transfer, maintenance, reports
├── shared/                       # Cross-role shared pages
│   ├── view_fixed_asset.php      # Fixed asset list/search
│   ├── view_current_asset.php    # Current asset list/search
│   ├── request_fixed.php         # Request fixed asset
│   ├── request_current.php       # Request current asset
│   ├── transfer_asset.php        # Asset transfer
│   ├── returns.php               # Process returns (Model 19)
│   ├── view_returned.php         # Returned assets view
│   ├── maintenance.php           # Maintenance workflow
│   ├── inspections.php           # Inspection records
│   ├── writeoffs.php             # Write-off processing
│   ├── disposal_requests.php     # Disposal requests
│   ├── reports.php               # Analytics & exports
│   ├── documents.php             # Document management
│   ├── dynamic_dashboard.php     # Generic role dashboard
│   └── ...
├── user/                         # Regular User
│   └── dashboard.php
├── dept_head/                    # Department Head
│   ├── dashboard.php
│   └── approve_requisitions.php
├── class_head/                   # Class Head
│   ├── dashboard.php
│   └── approve_requisitions.php
├── main_department_head/         # Main Department Head
│   ├── dashboard.php
│   └── approve_requisitions.php
├── maintenance_head/             # Maintenance Head
│   └── dashboard.php
├── maintenance_officer/          # Maintenance Officer
│   └── dashboard.php
├── scripts/                      # CLI setup/migration tools
│   ├── create_admin.php          # Create initial admin user
│   └── seed_sample_data.php      # Sample data generator
├── database/
│   └── sqlSchemaAll.sql          # Master schema (59 application tables)
├── assets/
│   ├── css/style.css             # Dark theme application styles
│   ├── js/main.js                # JavaScript utilities
│   └── i18n/                     # Translations (en.json, am.json, om.json)
└── uploads/                      # User uploads (signatures, documents)
```

---

## 9. Security Notes

- **Authentication:** Session-based with timeout (30 min idle)
- **Authorization:** Role-based access control via `require_role()` and `has_role()`
- **Hierarchy Scoping:** Users can only access records within their organizational scope (Organization → Main Dept → Dept → Class)
- **Password Security:** Passwords hashed with `password_hash()` (bcrypt, cost 10)
- **SQL Injection Prevention:** All queries use prepared statements via `db_query()` and `db_execute()`
- **XSS Prevention:** All user-facing output uses `esc()` (htmlspecialchars wrapper)
- **Audit Logging:** All critical actions logged to `audit_logs` table
- **Session Security:** Session ID regenerated on login; idle timeout enforced

### Remaining Recommendations (Not Yet Implemented)
- Environment-based configuration (`.env`) instead of hardcoded credentials
- HTTPS enforcement for production deployment
- Two-factor authentication (2FA) for enhanced security
- API rate limiting for public endpoints

---

## 10. Troubleshooting

| Issue | Solution |
|-------|----------|
| Database connection failed | Check `includes/config.php` for DB credentials; ensure MySQL is running |
| "Table doesn't exist" error | Import the canonical schema with `mysql -u root -p < database/sqlSchemaAll.sql`, then run applicable migrations from `database/migrations/`. |
| Sample data not loading | Run `php scripts/seed_sample_data.php` from the project root |
| Role not appearing in dropdown | Ensure the role exists in `role_permissions` table; run seed script |
| Asset status not updating | Check `fixed_assets.status` ENUM includes `under_maintenance` and `damaged` |
| Permission denied | Check `role_permissions` table for the user's role |
| Maintenance workflow not working | Ensure `maintenance_class_id` and related columns exist; these are added by `ensure_schema()` |

---

## 11. Documentation

| Document | Purpose |
|----------|---------|
| `README.md` | This file — quick start and system overview |
| `PROJECT_AUDIT_REPORT.md` | Full audit report with bug findings and fixes |
| `QUICK_START.md` | Step-by-step quick start guide |
| `DEPLOY.md` | Detailed deployment documentation |
| `PROJECT_STRUCTURE.md` | Full project structure and architecture |
| `SYSTEM_FLOWS.md` | Detailed workflow diagrams and data flows |

---

## 12. Contact

**Project:** Ethiopia Federal Police Commission — Technology Expansion Asset Management System  
**Organization:** Ethiopia Federal Police Commission (EFPC)  
**Support:** `admin@efpt.gov.et`

---

*Last updated: August 2026 — Version 1.0.0*
