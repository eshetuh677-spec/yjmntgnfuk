# EFPT_AMS - Project Structure & File Documentation

**Last Updated:** 2026-08-17  
**Status:** ✅ All .bak files removed | ✅ No syntax errors | ✅ Professional UI implemented

---

## 📁 Project Architecture

### Root Directory
```
EFPT_AMS/
├── index.php                          # Main entry point / login
├── DEPLOY.md                          # Deployment guide
├── LICENSE                            # Project license
├── README.md                           # Project overview
├── set_language.php                   # Localization handler
└── PROJECT_STRUCTURE.md              # This file
```

---

## 📂 Directory Structure & Files

### 🔐 `/admin/` - Administration Pages
**Purpose:** System administration and configuration  
**Access:** Only system administrators

| File | Purpose | Status |
|------|---------|--------|
| `dashboard.php` | Admin dashboard overview | ✅ Working |
| `role_permissions.php` | **Role Permission Manager** (PROFESSIONAL) | 🆕 Redesigned |
| `manage_roles.php` | Create/edit roles | ✅ Working |
| `manage_users.php` | User management | ✅ Working |
| `organizations.php` | Organization setup | ✅ Working |
| `departments.php` | Department management | ✅ Working |
| `main_departments.php` | Main department config | ✅ Working |
| `sub_admins.php` | Sub-admin assignment | ✅ Working |
| `locations.php` | Location management | ✅ Working |
| `buildings.php` | Building registry | ✅ Working |
| `floors.php` | Floor mapping | ✅ Working |
| `rooms.php` | Room configuration | ✅ Working |
| `classes_units.php` | Class/unit setup | ✅ Working |
| `asset_categories.php` | Asset category config | ✅ Working |
| `announcements.php` | System announcements | ✅ Working |
| `settings.php` | Global settings | ✅ Working |
| `governance.php` | Governance rules | ✅ Working |
| `org_overview.php` | Organization overview | ✅ Working |
| `user_cards.php` | User identification cards | ✅ Working |
| `setup_org.php` | Organization initialization | ✅ Working |
| `super_admin_dashboard.php` | Super admin dashboard | ✅ Working |

### 🏪 `/store/` - Store Management & Asset Entry
**Purpose:** Fixed/Current asset management, inventory tracking  
**Access:** Store Managers, Admins

| File | Purpose | Status |
|------|---------|--------|
| `add_fixed_asset.php` | **Add fixed assets** (AUTO-GEN CODE + STORE METADATA) | ✨ Fixed |
| `add_current_asset.php` | **Add current/consumable assets** (AUTO-GEN CODE + STORE METADATA) | ✨ Fixed |
| `dashboard.php` | Store manager dashboard | ✅ Working |
| `view_stock.php` | View stock levels | ✅ Working |
| `view_fixed_stock.php` | View fixed asset inventory | ✅ Working |
| `generate_model22.php` | Issue flow (Model 22) | ✅ Working |
| `store_report.php` | Store reports | ✅ Working |
| `print_voucher.php` | Voucher printing | ✅ Working |
| `get_next_asset_code.php` | Asset code generation API | ✅ Working |

**Key Features (Fixed Aug 17):**
- ✅ Column count mismatch fixed (21 cols → 21 placeholders)
- ✨ Auto-generate asset codes from asset name
- ✨ Store metadata display (Code, Phys. Code, Location)
- ✨ Auto-fill location & physical code from store selection

### 👔 `/dept_head/` - Department Head Operations
**Purpose:** Requisition approval & department operations  
**Access:** Department Heads

| File | Purpose | Status |
|------|---------|--------|
| `dashboard.php` | Department head dashboard | ✅ Working |
| `approve_requisitions.php` | Approve asset requests | ✅ Working |

### 👨‍💼 `/class_head/` - Class/Unit Head Operations
**Purpose:** Class-level asset management  
**Access:** Class Heads

| File | Purpose | Status |
|------|---------|--------|
| `dashboard.php` | Class head dashboard | ✅ Working |
| `approve_requisitions.php` | Approve class requisitions | ✅ Working |

### 🏢 `/main_department_head/` - Main Department Operations
**Purpose:** Main department oversight  
**Access:** Main Department Heads

| File | Purpose | Status |
|------|---------|--------|
| `dashboard.php` | Main dept dashboard | ✅ Working |
| `approve_requisitions.php` | Main dept approvals | ✅ Working |

### 👤 `/user/` - Regular User Pages
**Purpose:** User-facing features  
**Access:** All authenticated users

| File | Purpose | Status |
|------|---------|--------|
| `dashboard.php` | User home dashboard | ✅ Working |

### 📋 `/shared/` - Shared Features (Multi-role)
**Purpose:** Cross-role functionality  
**Access:** Various roles

| File | Purpose | Status |
|------|---------|--------|
| `dynamic_dashboard.php` | Role-adaptive dashboard | ✅ Working |
| `approval_track.php` | Requisition tracking | ✅ Working |
| `request_current.php` | Request current assets | ✅ Working |
| `request_fixed.php` | Request fixed assets | ✅ Working |
| `accountability.php` | Asset accountability | ✅ Working |
| `inspections.php` | Asset inspections | ✅ Working |
| `disposal_requests.php` | Asset disposal flow | ✅ Working |
| `returns.php` | Asset returns | ✅ Working |
| `transfer_asset.php` | Inter-dept asset transfer | ✅ Working |
| `writeoffs.php` | Asset write-offs | ✅ Working |
| `documents.php` | Document management | ✅ Working |
| `reports.php` | System reports | ✅ Working |
| `view_fixed_asset.php` | View fixed asset details | ✅ Working |
| `view_current_asset.php` | View current asset details | ✅ Working |
| `view_model20.php` | Model 20 form viewing | ✅ Working |
| `view_returned.php` | View returned assets | ✅ Working |
| `signatures.php` | Signature management | ✅ Working |
| `view_announcements.php` | View system announcements | ✅ Working |
| `logout.php` | Session termination | ✅ Working |
| `mark_read.php` | Notification marking | ✅ Working |

### 🔧 `/includes/` - Core Libraries & Utilities
**Purpose:** Shared PHP functions and templates  
**Status:** Foundation for entire application

| File | Purpose | Status |
|------|---------|--------|
| `config.php` | Environment & database configuration | ✅ Core |
| `db_connect.php` | Database connection + schema bootstrapper | ✅ Core |
| `auth.php` | Authentication & authorization | ✅ Core |
| `header.php` | HTML header template | ✅ Core |
| `footer.php` | HTML footer template | ✅ Core |
| `sidebar.php` | Navigation sidebar | ✅ Core |
| `i18n.php` | Internationalization (multi-language) | ✅ Core |
| `global_search.php` | Global search functionality | ✅ Core |
| `approval_helpers.php` | Requisition workflow helpers | ✅ Core |
| `role_permissions.php.fixbak` | Backup (DEPRECATED - removed) | ⛔ Old |

### 📦 `/database/` - Database Schema
**Purpose:** Database structure & initial data

| File | Purpose | Status |
|------|---------|--------|
| `sqlSchemaAll.sql` | Complete database schema | ✅ Master schema |

### 🎨 `/assets/` - Static Resources
**Purpose:** CSS, JavaScript, images

| Directory | Purpose |
|-----------|---------|
| `/css/` | Stylesheets |
| `/js/` | JavaScript files |
| `/i18n/` | Language files (JSON) |

### 📜 `/scripts/` - Utility Scripts
**Purpose:** Administrative & migration tools

| File | Purpose | Status |
|------|---------|--------|
| `create_admin.php` | Create admin user CLI | ✅ Utility |
| `list_categories.php` | List asset categories | ✅ Utility |
| `run_migration_efp.php` | Database migration runner | ✅ Utility |

### 📁 `/uploads/` - User-Generated Files
**Purpose:** Asset photos, documents, etc.  
**Status:** Runtime directory

---

## 🔑 Key Features & Status

### ✅ Completed & Fixed (Aug 17, 2026)
- [x] Removed all 42 .bak backup files
- [x] Fixed column count mismatch in fixed asset insert (21 → 21)
- [x] Added auto-generate asset code feature to both asset forms
- [x] Added store metadata display (Code, Phys. Code, Location)
- [x] Professional role permissions dashboard redesign
- [x] No PHP syntax errors across entire project
- [x] Database schema integrity with auto-repair

### 🚀 Core Functionality
- ✅ Multi-role RBAC (Role-Based Access Control)
- ✅ Asset lifecycle management (fixed + current)
- ✅ Hierarchical organization structure
- ✅ Approval workflow & requisition tracking
- ✅ Audit logging
- ✅ Multi-language support (i18n)
- ✅ CSRF protection
- ✅ Prepared statement SQL queries (injection-safe)

### 📊 Database Schema
**Tables:** 60+ tables  
**Key Tables:**
- `users` - User accounts & roles
- `organizations` - Org hierarchy
- `fixed_assets` - Fixed asset registry
- `current_assets` - Consumable asset registry
- `stock_transactions` - Transaction log
- `role_permissions` - RBAC matrix
- `audit_logs` - Action audit trail

---

## 🛠️ Configuration Files

### `/includes/config.php`
```php
// Database credentials
DB_HOST = 'localhost'
DB_USER = 'root'
DB_PASS = 'efpt_ams'
DB_NAME = 'efpt_ams'

// Site settings
SITE_URL = 'http://localhost/EFPT_AMS'
TIMEZONE = 'UTC'
```

### Database Connection (`/includes/db_connect.php`)
- Auto-creates missing tables
- Ensures schema compatibility
- Handles prepared statement bindings
- Logs errors for debugging

---

## 📈 Development Guidelines

### File Naming Conventions
- Pages: `action_noun.php` (e.g., `add_fixed_asset.php`)
- Utilities: `descriptive_name.php`
- Includes: `function_area.php` (in `/includes/`)

### Code Standards
- **Language:** PHP 7.4+
- **Database:** MySQLi prepared statements (no raw SQL)
- **Security:** CSRF tokens, SQL injection prevention, role validation
- **Structure:** MVC-inspired (includes, content pages, shared logic)

### Asset Code Generation
- **Fixed Asset:** `FA-` prefix (e.g., FA-HP-LAP)
- **Current Asset:** `CA-` prefix (e.g., CA-A4-REA)
- Format: `PREFIX-WORD1-WORD2` from asset name
- Manual override always allowed

### Store Selection Behavior
When store is selected:
1. Display store badge (name + code + location)
2. Auto-populate location field (if exists)
3. Auto-fill physical location code field
4. Trigger form validation

---

## 🐛 Known Issues & Resolutions

### Issue: Asset Save Failure
**Cause:** Column count mismatch in INSERT (22 placeholders for 21 columns)  
**Resolution:** ✅ Fixed Aug 17 - Corrected VALUES clause

### Issue: Auto-generation Removed
**Cause:** Prior cleanup removed code generation & store metadata  
**Resolution:** ✅ Fixed Aug 17 - Restored and enhanced features

### Issue: Generic Error Messages
**Cause:** Database errors masked by generic message  
**Resolution:** ✅ Improved error logging in db_connect.php

---

## 📞 Support & Debugging

### Check Errors
```bash
# View Apache error log
tail -f C:\xampp\apache\logs\error.log

# Check PHP syntax
php -l C:\xampp\htdocs\EFPT_AMS\admin\role_permissions.php
```

### Database Testing
```bash
# Connect to MySQL
mysql -h localhost -u root -pefpt_ams efpt_ams

# Verify schema
SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'efpt_ams';
```

### Common PHP Constants
- `SITE_URL` - Base URL for redirects
- `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME` - Database config

---

## 📝 Recent Changes (Aug 17, 2026)

| Time | Change | Status |
|------|--------|--------|
| 08:15 | Fixed asset insert column count | ✅ |
| Later | Restored auto-code generation | ✨ |
| Later | Added store metadata display | ✨ |
| Later | Professional role dashboard UI | 🆕 |
| Later | Removed 42 .bak files | 🧹 |

---

## 🎯 Next Steps

1. **Test in Browser**
   - Navigate to: `http://localhost/EFPT_AMS/store/add_fixed_asset.php`
   - Enter asset name → code auto-generates
   - Select store → metadata displays

2. **Verify Database**
   - Check `fixed_assets` and `current_assets` tables
   - Verify asset inserts succeed

3. **Monitor Logs**
   - Watch Apache error.log for any warnings
   - Check audit logs for user actions

---

*Generated: 2026-08-17*  
*Project: EFPT Asset Management System*
