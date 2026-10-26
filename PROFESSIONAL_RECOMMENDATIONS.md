# EFPT_AMS — Professional System Guide & Recommendations

## 1. System Value Proposition

The **EFPT Asset Management System (EFPT_AMS)** is a comprehensive, role-based asset lifecycle management platform built for the Ethiopian Federal Police Commission — Technology Expansion Department.

### Core Value Delivered

| Value Area | How the System Delivers It |
|---|---|
| **Accountability** | Every asset is tracked from requisition → approval → issuance → return/disposal. Full audit trail with user identity, timestamps, and approver signatures. |
| **Transparency** | Real-time dashboards per role. Store managers see stock levels. Department heads see approval queues. Admins see system-wide analytics. |
| **Control** | Hierarchical approval workflow (Class Head → Dept Head → Main Dept Head → Store Manager). Role-based access prevents unauthorized actions. |
| **Efficiency** | Automated voucher generation (Model 22), stock balance calculations, low-stock alerts, and quick-action cards reduce manual paperwork. |
| **Compliance** | Ethiopian calendar support, bilingual interface (English/Amharic/Oromoo), and complete issuance logs support regulatory reporting. |
| **Security** | CSRF protection, rate-limited login, 2FA support, session validation, IP binding, and force-password-change mechanisms. |

---

## 2. How to Use the System

### 2.1 Login & Authentication
1. Navigate to `http://localhost/EFPT_AMS/` (or your deployed URL)
2. Enter your **username** or **badge number** and **password**
3. If 2FA is enabled, enter the TOTP code
4. You will be redirected to your role-specific dashboard

### 2.2 Role-Based Dashboards

| Role | Dashboard Path | Primary Responsibilities |
|---|---|---|
| **System Admin** | `/admin/dashboard.php` | Manage users, organizations, roles, permissions, CCTV, system settings |
| **Org Admin** | `/store/dashboard.php` | Manage stores, assets, and users within assigned organization |
| **Store Manager** | `/store/dashboard.php` | Issue assets, manage stock, approve requisitions, generate vouchers |
| **Main Dept Head** | `/main_department_head/dashboard.php` | Review requisitions across subordinate departments |
| **Department Head** | `/dept_head/dashboard.php` | Approve requisitions from their department |
| **Class Head** | `/class_head/dashboard.php` | Approve requisitions from their class/unit |
| **Maintenance Head** | `/maintenance_head/dashboard.php` | Assign maintenance teams, track repair jobs |
| **Maintenance Officer** | `/maintenance_officer/dashboard.php` | Execute assigned maintenance jobs, update status |
| **Regular User** | `/user/dashboard.php` | Request assets, view issued items, track requisitions, return assets |

### 2.3 Typical Asset Lifecycle

```
REQUEST (Model 20)
    │
    ▼
CLASS HEAD APPROVAL ──┐
    │                 │
    ▼                 │
DEPT HEAD APPROVAL ───┤
    │                 │
    ▼                 │
MAIN DEPT APPROVAL ───┤
    │                 │
    ▼                 │
STORE MANAGER APPROVAL ◄──┘
    │
    ▼
ISSUE (Model 22 Voucher)
    │
    ▼
ASSET ASSIGNED TO USER
    │
    ▼
RETURN / TRANSFER / MAINTENANCE
    │
    ▼
FINAL DISPOSAL / WRITE-OFF
```

### 2.4 Key Features by Page

| Feature | Location | Description |
|---|---|---|
| **Issuance Logs** | Shared → Issuance Logs | Complete transaction history with search, sort, filter by type/date/person |
| **Asset Tracking** | Shared → Asset Audit | Track assignments, expected returns, overdue items |
| **Requisitions** | Shared → View Model 20 | Submit and track asset requests through approval chain |
| **Vouchers** | Store → Generate Model 22 | Issue assets with printable vouchers |
| **Returns** | Shared → Returns | Process asset returns with condition inspection |
| **Transfers** | Shared → Transfer Asset | Move assets between users/stores with approval |
| **Maintenance** | Shared → Maintenance | Assign repair teams, track job progress |
| **Audit Logs** | Shared → Audit Logs | System activity log for administrators |
| **Documents** | Shared → Documents | Upload and manage asset-related documents |
| **Reports** | Shared → Reports | Analytics, exports, and summary reports |

---

## 3. File & Folder Structure

```
C:\laragon\www\EFPT_AMS\
│
├── index.php                          # Login page
├── set_language.php                   # Language switcher handler (NEW)
├── set_calendar.php                   # Calendar switcher handler (NEW)
├── sw.js                              # Service Worker (PWA)
├── manifest.json                      # PWA manifest
├── .htaccess                          # Apache security rules
├── .env.example                       # Environment template
│
├── admin/                             # System Administrator Area
│   ├── dashboard.php                  # Admin overview with stats, CCTV, quick-add
│   ├── manage_users.php               # User CRUD
│   ├── manage_roles.php               # Role management
│   ├── role_permissions.php           # Permission matrix (read/write/update/delete)
│   ├── organizations.php              # Organization management
│   ├── org_overview.php               # Org drill-down analytics
│   ├── main_departments.php           # Main department management
│   ├── departments.php                # Department management
│   ├── classes_units.php              # Class/unit management
│   ├── asset_categories.php           # Asset category management
│   ├── stores.php                     # Store management
│   ├── buildings.php                  # Building management
│   ├── floors.php                     # Floor management
│   ├── rooms.php                      # Room management
│   ├── locations.php                  # Location management
│   ├── cctv_cameras.php               # CCTV camera management
│   ├── cctv_map.php                   # Live map monitor
│   ├── announcements.php              # System announcements
│   ├── settings.php                   # System settings
│   ├── import.php / import_process.php # CSV import
│   ├── governance.php                 # Governance framework
│   ├── security_alerts.php            # Security alert dashboard
│   ├── sub_admins.php                 # Sub-admin management
│   ├── super_admin_dashboard.php      # Super admin view
│   ├── user_cards.php                 # User ID card management
│   ├── edit_*.php                     # Edit pages for each entity
│   └── ajax_*.php                     # AJAX handlers
│
├── store/                             # Store Manager & Org Admin Area
│   ├── dashboard.php                  # Store overview with stock, requisitions, transfers, issuance log
│   ├── add_fixed_asset.php            # Register fixed asset
│   ├── add_current_asset.php          # Register consumable/current asset
│   ├── view_stock.php                 # Current stock view with transaction log
│   ├── view_fixed_stock.php           # Fixed asset stock view
│   ├── generate_model22.php           # Issue asset via Model 22 voucher
│   ├── print_voucher.php              # Print Model 22 voucher
│   ├── approve_requisitions.php       # Store-level requisition approval
│   ├── uniform_requisitions.php       # Uniform requisition workflow
│   ├── issue_uniform.php              # Issue uniform items
│   ├── print_uniform_voucher.php      # Print uniform voucher
│   ├── store_report.php               # Store-specific reports
│   ├── get_next_asset_code.php        # AJAX: generate asset code
│   └── ajax_uniform_items.php         # AJAX: uniform items
│
├── shared/                            # Shared Pages (All Roles)
│   ├── issuance_logs.php              # COMPLETE issuance/transaction log (NEW)
│   ├── audit_logs.php                 # System audit log viewer
│   ├── approval_track.php             # Track requisition approval status
│   ├── view_model20.php               # View all requisitions
│   ├── view_fixed_asset.php           # View all fixed assets
│   ├── view_current_asset.php         # View all current assets
│   ├── view_returned.php              # View returned assets
│   ├── request_fixed.php              # Request fixed asset (Model 20)
│   ├── request_current.php            # Request current/consumable asset
│   ├── transfer_asset.php             # Transfer asset between users
│   ├── transfer_voucher.php           # Transfer voucher
│   ├── verify_voucher.php             # Voucher verification
│   ├── returns.php                    # Return asset
│   ├── return_fixed.php               # Return fixed asset
│   ├── return_voucher.php             # Return voucher
│   ├── maintenance.php                # Maintenance workflow
│   ├── inspections.php                # Asset inspections
│   ├── writeoffs.php                  # Asset write-offs
│   ├── disposal_requests.php          # Disposal requests
│   ├── documents.php                  # Document management
│   ├── reports.php                    # Reports & analytics
│   ├── signatures.php                 # Digital signatures
│   ├── accountability.php             # Asset accountability
│   ├── announcements.php              # Announcements viewer
│   ├── view_announcements.php         # Announcements list
│   ├── view_announcements_with_uniform.php # Announcements with uniform
│   ├── asset_audit_dashboard.php      # Asset audit dashboard
│   ├── analytics_widget.php           # Analytics widget
│   ├── analytics_api.php              # Analytics API
│   ├── dynamic_dashboard.php          # Dynamic dashboard for unknown roles
│   ├── change_password.php            # Change password
│   ├── setup_2fa.php                  # Setup 2FA
│   ├── verify_2fa.php                 # Verify 2FA
│   ├── mark_read.php                  # Mark notifications as read
│   ├── logout.php                     # Logout handler
│   └── generate_model22.php           # Redirect to store version
│
├── class_head/                        # Class Head Area
│   ├── dashboard.php                  # Class head overview
│   └── approve_requisitions.php       # Class-level approval
│
├── dept_head/                         # Department Head Area
│   ├── dashboard.php                  # Department head overview
│   └── approve_requisitions.php       # Department-level approval
│
├── main_department_head/              # Main Department Head Area
│   ├── dashboard.php                  # Main dept head overview
│   └── approve_requisitions.php       # Main dept-level approval
│
├── maintenance_head/                  # Maintenance Head Area
│   └── dashboard.php                  # Maintenance head overview
│
├── maintenance_officer/               # Maintenance Officer Area
│   └── dashboard.php                  # Officer/Inspector overview
│
├── user/                              # Regular User Area
│   └── dashboard.php                  # User's personal dashboard
│
├── includes/                          # Core Includes
│   ├── config.php                     # Configuration, constants, .env loader
│   ├── db_connect.php                 # Database connection
│   ├── auth.php                       # Authentication, session, role helpers
│   ├── csrf.php                       # CSRF protection
│   ├── security.php                   # Security utilities
│   ├── validation.php                 # Input validation functions
│   ├── db_helpers.php                 # DB query helpers (db_query, db_execute, esc)
│   ├── db_query.php                   # DB query wrapper
│   ├── db_schema.php                  # Schema migration runner
│   ├── header.php                     # Top navigation header
│   ├── sidebar.php                    # Sidebar navigation
│   ├── footer.php                     # Site footer
│   ├── i18n.php                       # Internationalization
│   ├── ethiopian_calendar.php         # Ethiopian calendar converter
│   ├── role_permissions.php           # Role permission manager
│   ├── approval_helpers.php           # Approval workflow helpers
│   ├── approval_workflow.php          # Approval state machine
│   ├── email_service.php              # Email notifications
│   ├── notifications.php              # In-app notifications
│   ├── rate_limit.php                 # Login rate limiting
│   ├── totp.php                       # 2FA TOTP implementation
│   ├── redis_session.php              # Redis session handler
│   ├── multi_tenancy.php              # Multi-tenant support
│   ├── global_search.php              # Global search functionality
│   ├── stock_balance.php              # Stock balance calculations
│   ├── stock_service.php              # Stock transaction service
│   ├── status_history.php             # Status change history
│   ├── soft_delete.php                # Soft delete support
│   ├── record_auth.php                # Record-level authorization
│   ├── available_stock_reference.php  # Available stock reference widget
│   └── api.php                        # API endpoints
│
├── assets/                            # Frontend Assets
│   ├── css/
│   │   └── style.css                  # Complete stylesheet (dark + light theme)
│   ├── js/
│   │   └── main.js                    # Main JavaScript (sidebar, toasts, modals)
│   ├── images/
│   │   ├── efplogo.png                # EFPT logo
│   │   └── bgi.jpg                    # Background image
│   ├── i18n/
│   │   ├── en.json                    # English translations
│   │   ├── am.json                    # Amharic translations
│   │   └── om.json                    # Oromoo translations
│   └── components/
│       └── language-switcher.html     # Language switcher component
│
├── uploads/                           # Uploaded Files
│   └── signatures/                    # Digital signatures
│
├── database/                          # Database Files
│   ├── sqlSchemaAll.sql               # Complete schema
│   ├── sample_data.sql                # Sample data
│   └── migrations/                    # Schema migrations
│
├── scripts/                           # Utility Scripts
│   ├── seed_sample_data.php           # Seed database
│   ├── seed_cctv_cameras.php          # Seed CCTV data
│   ├── run_migration_efp.php          # Run migrations
│   └── dev/                           # Development helpers
│
├── tests/                             # PHPUnit Tests
│   ├── bootstrap.php
│   ├── StockServiceTest.php
│   └── ValidationTest.php
│
├── docs/                              # Documentation
│   └── TESTING_FLOW.md
│
├── docker/                            # Docker config
│   ├── Dockerfile
│   └── docker-compose.yml
│
├── .github/                           # CI/CD
│   └── workflows/
│       └── ci-cd.yml
│
└── [various .md files]                # Project documentation
```

---

## 4. Professional Recommendations

### 4.1 Immediate Improvements (High Priority)

| # | Recommendation | Impact | Effort |
|---|---|---|---|
| 1 | **Add issuance log export (CSV/Excel)** | Users can download full transaction history for offline analysis | Low |
| 2 | **Add barcode/QR code scanning for asset codes** | Faster asset lookup and issuance | Medium |
| 3 | **Implement asset condition history timeline** | Track condition changes over time (new → good → fair → poor → damaged) | Medium |
| 4 | **Add email notifications for approval actions** | Stakeholders get instant alerts | Medium |
| 5 | **Add bulk import validation with preview** | Prevent bad data from entering the system | Medium |
| 6 | **Implement asset depreciation tracking** | Financial reporting for fixed assets | High |
| 7 | **Add mobile-responsive tables with horizontal scroll** | Better UX on small screens | Low |
| 8 | **Add dashboard customization (drag-drop widgets)** | Users arrange their own dashboard | High |
| 9 | **Implement audit log export with filters** | Compliance reporting | Low |
| 10 | **Add asset barcode label printing** | Print labels directly from the system | Medium |

### 4.2 Security Enhancements

| # | Recommendation | Status |
|---|---|---|
| 1 | **Implement password complexity meter on registration** | ✅ Partially done (policy exists) |
| 2 | **Add session concurrency limit** | Recommended |
| 3 | **Implement IP whitelisting for admin accounts** | Recommended |
| 4 | **Add suspicious activity detection (geolocation, device fingerprint)** | Recommended |
| 5 | **Encrypt sensitive fields at rest (badge numbers, personal info)** | Recommended |
| 6 | **Add automatic session timeout warning modal** | Recommended |

### 4.3 UX/UI Improvements

| # | Recommendation | Status |
|---|---|---|
| 1 | **Light theme text readability** | ✅ Fixed (all text colors now adapt) |
| 2 | **Calendar widget in header** | ✅ Added (shows current date, toggle between Gregorian/Ethiopian) |
| 3 | **Toast notifications for all CRUD actions** | ✅ Implemented |
| 4 | **Loading spinners on all form submits** | ✅ Implemented |
| 5 | **Keyboard shortcuts (Ctrl+S, Ctrl+N, Ctrl+/)**** | ✅ Implemented |
| 6 | **Breadcrumb navigation on all pages** | ✅ Implemented |
| 7 | **Empty state illustrations with actionable CTAs** | Recommended |
| 8 | **Dark mode as default with light mode toggle** | ✅ Implemented |

### 4.4 Data Integrity Recommendations

| # | Recommendation | Status |
|---|---|---|
| 1 | **Database foreign key constraints** | ✅ Implemented |
| 2 | **Soft delete for all major tables** | ✅ Implemented |
| 3 | **Stock balance triggers/checks** | ✅ Implemented |
| 4 | **Unique constraints on asset codes, voucher numbers** | ✅ Implemented |
| 5 | **Audit trail for all data changes** | ✅ Implemented |

### 4.5 Scalability Recommendations

| # | Recommendation | Impact |
|---|---|---|
| 1 | **Add Redis caching for dashboard stats** | Reduces DB load by 60-80% |
| 2 | **Implement queue system for email notifications** | Prevents UI blocking |
| 3 | **Add database read replicas** | Handle concurrent users |
| 4 | **Implement API versioning** | Future-proof integrations |
| 5 | **Add Elasticsearch for full-text search** | Faster search across assets, vouchers, users |

---

## 5. Issuance Logs — Feature Overview

The new **Issuance Logs** page (`shared/issuance_logs.php`) provides complete transaction tracking:

### What It Tracks
- **Who** — Person who performed the transaction (store manager, admin)
- **To whom** — Person who received the asset (with badge number, department)
- **What** — Asset name, code, category, type (fixed/current/consumable)
- **When** — Transaction date and creation timestamp
- **Where** — Location name from the locations table
- **Why** — Reference type (requisition, transfer, return, disposal)
- **How much** — Quantity, unit price, total value
- **Reference** — Voucher number, requisition number with direct links

### Search & Filter
- Full-text search across asset names, codes, voucher numbers, person names
- Filter by asset type (fixed/current/consumable)
- Filter by transaction type (issue/return/transfer/purchase/adjustment/disposal)
- Filter by date range
- Filter by person name (issued to / performed by)

### Sorting
- Date (newest/oldest)
- Asset name (A-Z / Z-A)
- Person name (A-Z / Z-A)
- Asset type (A-Z)
- Quantity (low-high / high-low)

### Role Access
- Admin, Org Admin, Store Manager, Main Dept Head, Dept Head, Class Head, Maintenance Head, Maintenance Officer

---

## 6. Theme System

### Dark Theme (Default)
- Navy blue backgrounds (`#253d6a`, `#1a2a4a`, `#0f1e35`)
- White/light text for high contrast
- Professional law-enforcement aesthetic

### Light Theme
- White/light gray backgrounds (`#f0f4f8`, `#ffffff`)
- Dark text (`#1a2a3a`) for readability
- Same accent colors (blue `#0d6efd`)
- Toggle via header button — preference saved to `localStorage`

### Calendar Toggle
- Gregorian (default) or Ethiopian calendar
- Preference saved to session
- All dates throughout the system automatically convert

---

## 7. Summary of Changes Made

| File | Change |
|---|---|
| `set_language.php` | **CREATED** — Language switcher AJAX handler |
| `set_calendar.php` | **CREATED** — Calendar switcher AJAX handler |
| `assets/css/style.css` | **UPDATED** — Added `[data-theme="light"]` styles, `.dark-theme` body class, all text colors adapt |
| `includes/header.php` | **UPDATED** — Added calendar widget dropdown, fixed toggle handlers, improved click-outside behavior |
| `shared/issuance_logs.php` | **CREATED** — Comprehensive issuance log with search, sort, filter, pagination |
| `includes/sidebar.php` | **UPDATED** — Added Issuance Logs menu item |
| `admin/dashboard.php` | **UPDATED** — Added Issuance Logs quick link |
| `store/dashboard.php` | **UPDATED** — Added Issuance Logs quick action card |
| `database/migrations/add_serial_number_tracking.sql` | **CREATED** — Migration adding serial_number, keeper_id, source_table columns |
| `database/sqlSchemaAll.sql` | **UPDATED** — Added serial_number, keeper_id columns to fixed_assets, current_assets; serial_number + source_table to model22_vouchers; serial_number to stock_transactions |
| `includes/db_schema.php` | **UPDATED** — Added `ensure_serial_number_columns()` for runtime auto-migration |
| `includes/stock_service.php` | **UPDATED** — `logTransaction()` records serial_number; `assignFixedAsset()` fetches serials per row |
| `store/generate_model22.php` | **UPDATED** — Serial number capture from dropdown, display on form, included in voucher INSERT |
| `store/print_voucher.php` | **UPDATED** — Displays serial number in voucher info grid |
| `shared/transfer_voucher.php` | **UPDATED** — Displays serial number in asset information table |
| `shared/view_fixed_asset.php` | **UPDATED** — Edit modal includes serial_number and keeper_id fields |
| `store/view_fixed_stock.php` | **UPDATED** — Serial number column in asset table and transaction log |
| `store/view_stock.php` | **UPDATED** — Serial number column in asset table and transaction log |

---

## 8. Serial Number & Keeper Tracking

The system tracks individual asset serial numbers and keeper assignments across the full lifecycle:

### Database Schema Enhancements
| Table | Column Added | Purpose |
|---|---|---|
| `fixed_assets` | `serial_number` | Unique serial for each physical item |
| `fixed_assets` | `keeper_id` | Current user responsible for the asset |
| `current_assets` | `serial_number` | Serial for tracked consumables (optional) |
| `current_assets` | `keeper_id` | Current custodian of consumable stock |
| `model22_vouchers` | `serial_number` | Serial number of the specific unit issued |
| `model22_vouchers` | `source_table` | Origin of issued asset (`fixed_assets` or `current_assets`) |
| `stock_transactions` | `serial_number` | Serial captured at issuance/return |

### Key Pages Updated
- **Store → Issue Voucher (M22)** (`store/generate_model22.php`) — captures serial number from the dropdown selection and displays it on the voucher
- **Store → Print Voucher** (`store/print_voucher.php`) — shows "Asset Serial No" in the voucher detail grid
- **Shared → Transfer Voucher** (`shared/transfer_voucher.php`) — displays serial number in the asset information table
- **Shared → View Fixed Asset** (`shared/view_fixed_asset.php`) — edit modal includes serial number and keeper fields
- **Store → View Fixed Stock** (`store/view_fixed_stock.php`) — serial number column in asset table and transaction log
- **Store → View Stock** (`store/view_stock.php`) — serial number column in asset table and transaction log
- **Store → Add Fixed Asset** (`store/add_fixed_asset.php`) — serial number + keeper captured at registration
- **Store → Add Current Asset** (`store/add_current_asset.php`) — serial number optional, captured at registration
- **includes/stock_service.php** — `assignFixedAsset()` fetches serials from individual rows; `logTransaction()` records serial in audit trail

### Stock Decrement Behavior
- **Fixed assets**: each database row represents one physical unit. Issuing via Model 22 marks specific rows as `assigned` and logs their serial numbers individually.
- **Current assets**: bulk quantity decrement (consumables typically lack per-unit serials). Serial number is optional in the form.

---

## 9. Next Steps

1. **Test the new features** — Verify issuance logs, calendar widget, and light theme on all role dashboards
2. **Populate sample data** — Use `scripts/seed_sample_data.php` for testing
3. **Configure .env** — Set database credentials and security settings
4. **Run migrations** — Execute `database/migrations/` scripts if upgrading from an older version
5. **Review role permissions** — Go to Admin → Role Permissions to fine-tune access
6. **Set up email service** — Configure SMTP in `.env` for email notifications

---

*Document generated as part of the EFPT_AMS professional audit and enhancement.*
*System Version: 1.0.0*
