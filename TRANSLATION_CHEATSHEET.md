# Translation Quick Reference - Cheat Sheet

## Copy & Paste Template for Any Page

```php
<?php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';
require_once '../includes/i18n.php';  // ← REQUIRED!

// Example: Get current user
$user = current_user();

// Example: Translated page title
$page_title = t('page.YOUR_PAGE_NAME');

// Example: Translated error/success
$error = t('errors.assetNotFound');
$success = t('success.assetAdded');

include '../includes/header.php';
?>

<!-- HTML Example -->
<h1><?= t('page.YOUR_PAGE_NAME') ?></h1>
<button><?= t('actions.approve') ?></button>
```

## Common Translation Keys by Category

### Page Titles
```php
t('page.admin_dashboard_title')           // Admin Dashboard
t('page.store_dashboard_title')           // Store Manager Dashboard
t('page.request_current_title')           // Request Form — Current Asset
t('page.request_fixed_title')             // Request Form — Fixed Asset
t('page.view_requisitions_title')         // View Requisitions
t('page.manage_users_title')              // Manage Users
t('page.manage_roles_title')              // Manage Roles
t('page.organizations_title')             // Organizations
t('page.departments_title')               // Departments
t('page.stores_title')                    // Stores
t('page.announcements_title')             // Announcements
```

### Dashboard Titles & Stats
```php
t('dashboard.admin.title')                // Admin Dashboard
t('dashboard.admin.stats.activeUsers')    // Active Users
t('dashboard.admin.stats.organizations')  // Organizations
t('dashboard.admin.stats.fixedAssets')    // Fixed Assets
t('dashboard.store.title')                // Store Manager Dashboard
t('dashboard.store.stats.availableFixedAssets')  // Available Fixed Assets
```

### Role Names
```php
t('roles.admin')                // System Administrator
t('roles.org_admin')            // Organization Administrator  
t('roles.store_manager')        // Store Manager
t('roles.main_department_head') // Main Department Head
t('roles.department_head')      // Department Head
t('roles.class_head')           // Class Head
t('roles.regular_user')         // Regular User
```

### Role Tasks
```php
t('admin.tasks.setup_org')
t('admin.tasks.manage_users')
t('admin.tasks.manage_roles')
t('store_manager.tasks.add_fixed_asset')
t('store_manager.tasks.process_requisitions')
t('store_manager.tasks.issue_vouchers')
t('dept_head.tasks.approve_requisitions')
t('class_head.tasks.approve_requisitions')
t('user.tasks.request_fixed_asset')
t('user.tasks.request_current_asset')
```

### Navigation & Sidebar
```php
t('sidebar.dashboard')           // Dashboard
t('sidebar.assets')              // Assets
t('sidebar.requisitions')        // Requisitions
t('sidebar.addFixedAsset')       // Add Fixed Asset
t('sidebar.addCurrentAsset')     // Add Current Asset
t('sidebar.requestFixed')        // Request Fixed Asset
t('sidebar.requestCurrent')      // Request Current Asset
t('sidebar.transferAsset')       // Transfer Asset
t('sidebar.returnAsset')         // Return Asset
t('sidebar.manageUsers')         // Manage Users
t('sidebar.manageRoles')         // Manage Roles
t('sidebar.organizations')       // Organizations
t('sidebar.departments')         // Departments
t('sidebar.stores')              // Stores
t('sidebar.announcements')       // Announcements
```

### Action Buttons
```php
t('actions.approve')             // Approve
t('actions.reject')              // Reject
t('actions.save')                // Save
t('actions.submit')              // Submit
t('actions.view')                // View
t('actions.edit')                // Edit
t('actions.delete')              // Delete
t('actions.cancel')              // Cancel
t('actions.back')                // Back
t('actions.add')                 // Add
t('actions.create')              // Create
t('actions.update')              // Update
t('actions.return')              // Return
t('actions.transfer')            // Transfer
t('actions.issue')               // Issue
t('actions.confirm')             // Confirm
```

### Form Labels
```php
t('forms.assetCode')             // Asset Code
t('forms.assetName')             // Asset Name
t('forms.category')              // Category
t('forms.quantity')              // Quantity
t('forms.unit')                  // Unit
t('forms.organization')          // Organization
t('forms.mainDepartment')        // Main Department
t('forms.department')            // Department
t('forms.classUnit')             // Class / Unit
t('forms.status')                // Status
t('forms.requesterName')         // Requester Name
t('forms.priority')              // Priority
t('forms.purpose')               // Purpose / Justification
```

### Table Headers
```php
t('table.id')                    // ID
t('table.name')                  // Name
t('table.code')                  // Code
t('table.type')                  // Type
t('table.status')                // Status
t('table.dateCreated')           // Date Created
t('table.action')                // Action
t('table.requisitionNo')         // Req. No
t('table.requester')             // Requester
t('table.department')            // Department
t('table.asset')                 // Asset
t('table.quantity')              // Qty
t('table.priority')              // Priority
t('table.approvalStatus')        // Approval Status
```

### Status Values
```php
t('status.pending')              // Pending
t('status.approved')             // Approved
t('status.rejected')             // Rejected
t('status.completed')            // Completed
t('status.active')               // Active
t('status.inactive')             // Inactive
t('status.available')            // Available
t('status.assigned')             // Assigned
t('status.returned')             // Returned
```

### Error Messages
```php
t('errors.fieldRequired')        // This field is required
t('errors.invalidFormat')        // Invalid format
t('errors.assetNotFound')        // Asset not found
t('errors.insufficientPermission') // You don't have permission
t('errors.databaseError')        // A database error occurred
t('errors.sessionExpired')       // Your session has expired
t('errors.accessDenied')         // Access Denied
```

### Success Messages
```php
t('success.assetAdded')          // Asset added successfully
t('success.assetUpdated')        // Asset updated successfully
t('success.assetDeleted')        // Asset deleted successfully
t('success.requisitionSubmitted') // Requisition submitted successfully
t('success.requisitionApproved') // Requisition approved successfully
t('success.changesSaved')        // Changes saved successfully
t('success.userCreated')         // User created successfully
```

### Messages
```php
t('messages.success')            // Operation completed successfully
t('messages.error')              // An error occurred. Please try again.
t('messages.adminAccess')        // Administrator Access — Full system access
t('messages.hierarchyRestricted') // Hierarchy Restricted
t('messages.accessDenied')       // Access denied
```

### Common UI Text
```php
t('common.home')                 // Home
t('common.yes')                  // Yes
t('common.no')                   // No
t('common.ok')                   // OK
t('common.all')                  // All
t('common.firstName')            // First Name
t('common.lastName')             // Last Name
t('common.fullName')             // Full Name
```

## Quick Find & Replace Patterns

### In HTML
```html
<!-- BEFORE -->
<h1>Dashboard</h1>
<button>Approve</button>

<!-- AFTER -->
<h1><?= t('page.admin_dashboard_title') ?></h1>
<button><?= t('actions.approve') ?></button>
```

### In PHP
```php
// BEFORE
$error = 'Asset not found';
$message = 'Dashboard';

// AFTER
$error = t('errors.assetNotFound');
$message = t('page.admin_dashboard_title');
```

### For Loops (Table Rows)
```php
// BEFORE
<td><?php echo $row['status']; ?></td>

// AFTER
<td><?php echo t('status.' . strtolower($row['status'])); ?></td>
```

## Testing Your Changes

```bash
# 1. Check PHP syntax
php -l yourfile.php

# 2. Open browser to page
http://localhost/EFPT_AMS/admin/dashboard.php

# 3. Change language (look for language selector)

# 4. Verify text is translated correctly

# 5. Check browser console for JS errors (F12)

# 6. Check PHP error log
tail -f /var/log/apache2/error.log
```

## Troubleshooting

### Text shows as key name (e.g., "page.dashboard_title")
- ✅ Check key exists in en.json
- ✅ Check spelling matches exactly
- ✅ Check file is saved

### Wrong language displayed
- ✅ Check browser language selector
- ✅ Check session with: `var_dump($_SESSION['lang']);`
- ✅ Clear browser cache

### Accented characters show as ?
- ✅ Check file encoding is UTF-8
- ✅ Check meta charset in header.php
- ✅ Check database collation

## File Locations Reference

```
Translation Files:
  assets/i18n/en.json          (English - 300+ keys)
  assets/i18n/am.json          (Amharic - 200+ keys)
  assets/i18n/om.json          (Oromo - 200+ keys)

Core Functions:
  includes/i18n.php            (t() function, load_translations)

Language Switcher:
  assets/components/language-switcher.html

Full Documentation:
  TRANSLATION_GUIDE.md         (Original guide)
  TRANSLATION_IMPLEMENTATION.md (Detailed how-to)
  TRANSLATION_SUMMARY.md       (Project status)
```

## Remember

1. ✅ **Always** add `require_once '../includes/i18n.php';`
2. ✅ **Always** use `t()` for user-facing text
3. ✅ **Never** hardcode English strings in PHP
4. ✅ **Test** in all 3 languages
5. ✅ **Check** PHP syntax after changes
6. ✅ **Commit** changes with clear messages

---

**Happy Translating! 🌍**
Good luck continuing the translation work!
