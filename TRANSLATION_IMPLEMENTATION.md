# EFPT AMS Translation Implementation Guide

## Overview

This guide documents how to implement full multi-language support across all EFPT AMS pages using the expanded translation system.

## Current Status

✅ **Completed:**
- Comprehensive English translation file (en.json) with 300+ keys
- Structured translations for all roles (admin, org_admin, store_manager, dept_head, class_head, user)
- Dashboard translations for each role
- Translation infrastructure (i18n.php, language switcher)
- Sample implementations (admin/dashboard.php, store/dashboard.php)

⏳ **In Progress:**
- Translating pages to use t() function instead of hardcoded strings

## Translation File Structure

### Location
- `assets/i18n/en.json` - English (English)
- `assets/i18n/am.json` - Amharic (አማርኛ)
- `assets/i18n/om.json` - Oromo

### Key Categories

```
actions.*          - Buttons/Actions (Approve, Reject, Save, etc.)
messages.*         - System messages (Success, Error, Info, etc.)
headers.*          - Page titles and headers
sidebar.*          - Sidebar navigation menu
forms.*            - Form labels and placeholders
roles.*            - Role names and descriptions
page.*             - Page-specific titles
dashboard.*        - Dashboard content by role
admin.*            - System admin role info and tasks
org_admin.*        - Organization admin role info and tasks
store_manager.*    - Store manager role info and tasks
dept_head.*        - Department head role info and tasks
class_head.*       - Class head role info and tasks
user.*             - Regular user role info and tasks
workflow.*         - Workflow steps and statuses
table.*            - Table headers and labels
status.*           - Status values
errors.*           - Error messages
success.*          - Success messages
common.*           - Common UI text (Yes, No, Home, etc.)
```

## Implementation Steps for Each Page

### Step 1: Add i18n Include
Add this line after the auth.php include:
```php
require_once '../includes/i18n.php';
```

### Step 2: Update Page Title
**Before:**
```php
$page_title = 'Admin Dashboard';
```

**After:**
```php
$page_title = t('page.admin_dashboard_title');
```

### Step 3: Replace Hardcoded Strings
**Before:**
```html
<h1>Admin Dashboard</h1>
<button>Approve</button>
<span>Active Users</span>
```

**After:**
```html
<h1><?= t('dashboard.admin.title') ?></h1>
<button><?= t('actions.approve') ?></button>
<span><?= t('dashboard.admin.stats.activeUsers') ?></span>
```

### Step 4: Update Error/Success Messages
**Before:**
```php
$error = 'Asset not found';
$success = 'Asset added successfully';
```

**After:**
```php
$error = t('errors.assetNotFound');
$success = t('success.assetAdded');
```

## Pages to Update (Priority Order)

### Tier 1: Dashboard Pages (Critical)
These pages are entry points for each role:
- [x] `admin/dashboard.php` - ✅ Updated
- [ ] `store/dashboard.php` - Partially updated
- [ ] `dept_head/dashboard.php`
- [ ] `class_head/dashboard.php`
- [ ] `user/dashboard.php`
- [ ] `admin/super_admin_dashboard.php`
- [ ] `main_department_head/dashboard.php`

### Tier 2: Form Pages (High Priority)
These are user-facing forms:
- [ ] `shared/request_current.php` - Current Asset Request
- [ ] `shared/request_fixed.php` - Fixed Asset Request
- [ ] `store/add_current_asset.php` - Add Current Asset
- [ ] `store/add_fixed_asset.php` - Add Fixed Asset
- [ ] `admin/manage_users.php` - Manage Users
- [ ] `admin/organizations.php` - Organizations
- [ ] `admin/departments.php` - Departments

### Tier 3: View/Approval Pages (Medium Priority)
- [ ] `shared/view_model20.php` - View Requisitions
- [ ] `dept_head/approve_requisitions.php`
- [ ] `class_head/approve_requisitions.php`
- [ ] `shared/view_current_asset.php`
- [ ] `shared/view_fixed_asset.php`
- [ ] `shared/returns.php`
- [ ] `shared/transfer_asset.php`

### Tier 4: Admin Management Pages (Standard Priority)
- [ ] `admin/manage_roles.php`
- [ ] `admin/role_permissions.php`
- [ ] `admin/announcements.php`
- [ ] `admin/stores.php`
- [ ] `admin/asset_categories.php`
- [ ] `admin/settings.php`

### Tier 5: Shared Pages (Lower Priority)
- [ ] `shared/reports.php`
- [ ] `shared/documents.php`
- [ ] `shared/accountability.php`
- [ ] `shared/inspections.php`
- [ ] `shared/signatures.php`
- [ ] `shared/disposal_requests.php`

## Quick Reference: Common Replacements

### Page Titles
```php
t('page.admin_dashboard_title')
t('page.store_dashboard_title')
t('page.request_current_title')
t('page.request_fixed_title')
```

### Role-Based Content
```php
// Get role name
t('roles.admin') // "System Administrator"
t('roles.store_manager') // "Store Manager"

// Get role tasks
t('admin.tasks.setup_org')
t('admin.tasks.manage_users')
t('store_manager.tasks.add_fixed_asset')
t('dept_head.tasks.approve_requisitions')
```

### Common Actions
```php
t('actions.approve')
t('actions.reject')
t('actions.save')
t('actions.submit')
t('actions.view')
t('actions.edit')
t('actions.delete')
```

### Common Messages
```php
t('messages.adminAccess')
t('messages.hierarchyRestricted')
t('messages.accessDenied')

t('success.assetAdded')
t('success.requisitionSubmitted')
t('success.changesSaved')

t('errors.fieldRequired')
t('errors.assetNotFound')
t('errors.insufficientPermission')
```

### Table Headers
```php
t('table.id')
t('table.name')
t('table.code')
t('table.status')
t('table.action')
t('table.requisitionNo')
t('table.requester')
t('table.approvalStatus')
```

## Automation Script (Optional)

For developers who want to automate parts of the translation process:

```bash
# Find all hardcoded English strings that might need translation
grep -r "Dashboard\|requisition\|approval\|asset" admin/ store/ shared/ --include="*.php" | grep -v t('
```

## Testing Translation Changes

### 1. Verify English (en)
- Change language to English
- Check all text displays correctly
- Verify t() fallback works for missing keys

### 2. Verify Amharic (am)
- Change language to Amharic
- Check dashboard displays in Amharic
- Check role descriptions appear in Amharic

### 3. Verify Oromo (om)
- Change language to Oromo
- Check dashboard displays in Oromo
- Check workflow messages appear in Oromo

### 4. Database Persistence
- Change language to Amharic
- Reload page - should stay in Amharic
- Login/logout - should retain language preference

## Adding New Translation Keys

When adding a new feature or page:

1. **Define the key** in a logical category:
   ```json
   {
     "myFeature": {
       "title": "My Feature Title",
       "description": "My Feature Description"
     }
   }
   ```

2. **Add to all language files**:
   - en.json
   - am.json
   - om.json

3. **Use in PHP**:
   ```php
   t('myFeature.title')
   t('myFeature.description')
   ```

## Performance Considerations

- Translations are loaded once per request (cached in static variable)
- No database queries for translations (all in JSON files)
- Minimal performance impact (<1ms per t() call)
- Language preference stored in session for current request

## Extending to New Languages

To add a new language (e.g., French - fr):

1. **Create** `assets/i18n/fr.json`
2. **Copy structure** from `en.json`
3. **Translate all keys** to French
4. **Update** `includes/i18n.php`:
   ```php
   function available_locales() {
       return ['en', 'am', 'om', 'fr'];
   }
   ```
5. **Add to language switcher** `assets/components/language-switcher.html`

## Common Pitfalls to Avoid

❌ **Don't:**
- Hardcode strings in HTML: `<h1>Dashboard</h1>`
- Concatenate translations: `t('page.title') . ' - ' . t('subtitle')`
- Nest function calls: `echo t(strtolower($role) . '.title')`
- Store translations in database variables

✅ **Do:**
- Use translation keys: `<h1><?= t('page.dashboard_title') ?></h1>`
- Pre-build complete strings in JSON
- Use consistent key naming patterns
- Cache translations at application level

## Troubleshooting

### Missing Translation Key
**Issue:** `t()` returns the key name instead of translated text
**Solution:** Check that the key exists in en.json - English is the fallback

### Wrong Language Displayed
**Issue:** Always shows English despite selecting another language
**Solution:** 
- Check session variable: `var_dump($_SESSION['lang']);`
- Check database `language` column in users table
- Verify language file exists at correct path

### Accented Characters Not Displaying
**Issue:** Amharic/Oromo characters show as ?
**Solution:**
- Verify file encoding is UTF-8
- Check `<meta charset="UTF-8">` in header.php
- Verify database collation is utf8mb4_unicode_ci

## Next Steps

1. **Week 1:** Update all dashboard pages
2. **Week 2:** Update form pages (request, add asset)
3. **Week 3:** Update approval pages
4. **Week 4:** Complete remaining pages
5. **Week 5:** QA and testing across all languages

## Support

For questions about translation implementation:
- Check existing pages: admin/dashboard.php, store/dashboard.php
- Review en.json for available keys
- Refer to includes/i18n.php for t() function documentation

---

**Last Updated:** 2026-08-17
**Status:** Active Implementation
**Coverage:** Dashboard pages updated, remaining pages in queue
