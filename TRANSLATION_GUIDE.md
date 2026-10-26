# 🌍 EFPT_AMS - Multi-Language Translation Guide

**Complete guide to implementing and using translations in the system**

---

## 📚 Table of Contents

1. [Quick Start](#quick-start)
2. [How the System Works](#how-the-system-works)
3. [Using Translations in PHP](#using-translations-in-php)
4. [Using Translations in HTML/Forms](#using-translations-in-htmlforms)
5. [Adding New Translations](#adding-new-translations)
6. [Language Switcher](#language-switcher)
7. [Supported Languages](#supported-languages)
8. [Translation File Structure](#translation-file-structure)
9. [Best Practices](#best-practices)

---

## 🚀 Quick Start

### For PHP Files

```php
<?php
// At the top of your PHP file, include the i18n system
require_once __DIR__ . '/includes/i18n.php';

// Use translations with the t() function
echo t('nav.dashboard');           // Output: "Dashboard"
echo t('actions.save');            // Output: "Save"
echo t('messages.success');        // Output: "Operation completed successfully"
?>
```

### For HTML Forms

```html
<label><?php echo t('forms.assetName'); ?></label>
<input type="text" placeholder="<?php echo t('forms.assetName'); ?>" name="assetName">

<button><?php echo t('actions.save'); ?></button>
```

### For Status Displays

```php
<span class="badge"><?php echo t('status.' . strtolower($asset['status'])); ?></span>
```

---

## 🔧 How the System Works

### Architecture

```
User Browser
    ↓
Header/Navigation includes language switcher
    ↓
User clicks language option
    ↓
PHP sets $_SESSION['lang'] = 'am' (or 'en', 'om')
    ↓
Page loads/refreshes
    ↓
includes/i18n.php loads appropriate JSON file
    ↓
Translation keys return translated strings
    ↓
Pages display in selected language
```

### File Structure

```
EFPT_AMS/
├── includes/
│   └── i18n.php                    # Translation system (functions)
├── assets/
│   ├── i18n/
│   │   ├── en.json                 # English translations
│   │   ├── am.json                 # Amharic translations
│   │   └── om.json                 # Oromo translations
│   └── components/
│       └── language-switcher.html  # UI component
└── [All PHP files use t() function]
```

---

## 💻 Using Translations in PHP

### Basic Usage

```php
<?php
require_once 'includes/i18n.php';

// Simple key lookup
$dashboard_label = t('nav.dashboard');

// With parameters for dynamic content
$welcome_msg = t('headers.welcomeBack', ['name' => 'Ahmed']);
// Returns: "Welcome back, Ahmed!"

// Fallback if key doesn't exist
$missing = t('nonexistent.key');  // Returns: "nonexistent.key" (the key itself)
?>
```

### In Page Title

```php
<?php
$page_title = t('page.view_model20_title');
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $page_title; ?> - EFPT_AMS</title>
</head>
```

### In Forms

```php
<form method="POST">
    <div class="form-group">
        <label><?php echo t('forms.assetCode'); ?> <span class="required">*</span></label>
        <input type="text" name="asset_code" required>
        <small><?php echo t('messages.required'); ?></small>
    </div>

    <div class="form-group">
        <label><?php echo t('forms.assetName'); ?> <span class="required">*</span></label>
        <input type="text" name="asset_name" required>
    </div>

    <div class="form-group">
        <label><?php echo t('forms.category'); ?></label>
        <select name="category">
            <option><?php echo t('forms.selectOption'); ?></option>
            <option>Computers</option>
            <option>Furniture</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">
        <?php echo t('actions.submit'); ?>
    </button>
</form>
```

### In Conditional Messages

```php
<?php
if ($operation_success) {
    $message = t('success.assetAdded');
    // Returns: "Asset added successfully"
} else {
    $message = t('errors.databaseError');
    // Returns: "A database error occurred. Please contact support."
}
?>
<div class="alert alert-<?php echo ($operation_success ? 'success' : 'danger'); ?>">
    <?php echo $message; ?>
</div>
```

### In Loops (Tables/Lists)

```php
<?php
$assets = [
    ['id' => 1, 'name' => 'Laptop', 'status' => 'active'],
    ['id' => 2, 'name' => 'Desk', 'status' => 'pending'],
    ['id' => 3, 'name' => 'Chair', 'status' => 'inactive']
];
?>

<table>
    <thead>
        <tr>
            <th><?php echo t('table.id'); ?></th>
            <th><?php echo t('table.name'); ?></th>
            <th><?php echo t('table.status'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($assets as $asset): ?>
        <tr>
            <td><?php echo $asset['id']; ?></td>
            <td><?php echo $asset['name']; ?></td>
            <td>
                <span class="badge badge-<?php echo $asset['status']; ?>">
                    <?php echo t('status.' . strtolower($asset['status'])); ?>
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
```

---

## 🎨 Using Translations in HTML/Forms

### Simple Labels

```html
<label><?php echo t('forms.assetCode'); ?></label>
<!-- Output: "Asset Code" (or translated equivalent) -->
```

### Placeholders

```html
<input type="text" 
       placeholder="<?php echo t('common.search_placeholder'); ?>"
       name="search">
```

### Button Text

```html
<button type="submit" class="btn btn-primary">
    <?php echo t('actions.save'); ?>
</button>

<a href="#" class="btn btn-secondary">
    <?php echo t('actions.cancel'); ?>
</a>
```

### Dropdown Options

```html
<select name="organization">
    <option value=""><?php echo t('forms.selectOption'); ?></option>
    <option value="head_office"><?php echo t('organization.headOffice'); ?></option>
    <option value="branch"><?php echo t('organization.branch'); ?></option>
</select>
```

### Alerts & Messages

```html
<?php if ($error): ?>
<div class="alert alert-danger" role="alert">
    <strong><?php echo t('messages.error'); ?>:</strong>
    <?php echo $error_message; ?>
</div>
<?php endif; ?>

<?php if ($success): ?>
<div class="alert alert-success" role="alert">
    <?php echo t('success.changesSaved'); ?>
</div>
<?php endif; ?>
```

### Form Validation Messages

```html
<?php if (!empty($_POST) && $validation_errors): ?>
<div class="alert alert-warning">
    <strong><?php echo t('messages.warning'); ?></strong>
    <ul>
        <?php foreach ($validation_errors as $field => $error): ?>
        <li>
            <strong><?php echo t('forms.' . $field); ?>:</strong>
            <?php echo t('errors.' . $error); ?>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
```

---

## ➕ Adding New Translations

### Step 1: Identify the new key

```php
// Before (hardcoded)
echo "Add New Asset";

// After (translation key: pages.addNewAsset)
echo t('pages.addNewAsset');
```

### Step 2: Add to en.json

```json
{
  "pages": {
    "addNewAsset": "Add New Asset",
    "editAsset": "Edit Asset"
  }
}
```

### Step 3: Add to am.json (Amharic)

```json
{
  "pages": {
    "addNewAsset": "አዲስ ንብረት ያክሉ",
    "editAsset": "ንብረት ያርትዑ"
  }
}
```

### Step 4: Add to om.json (Oromo)

```json
{
  "pages": {
    "addNewAsset": "Qabeenya Haaraa Dabaluu",
    "editAsset": "Qabeenya Gulaaluu"
  }
}
```

### Step 5: Use in PHP

```php
echo t('pages.addNewAsset');  // Automatically uses current language
```

---

## 🌐 Language Switcher

### Integration

1. **Include in header.php:**
   ```php
   <!-- In header.php, after <header> opening tag -->
   <?php include 'assets/components/language-switcher.html'; ?>
   ```

2. **Handle language change:**
   ```php
   // In your main index.php or header.php
   if ($_GET['action'] === 'set_language' && isset($_GET['lang'])) {
       set_locale($_GET['lang']);
   }
   ```

### How It Works

1. User clicks language button in switcher
2. JavaScript sends request: `?action=set_language&lang=am`
3. PHP calls `set_locale('am')`
4. Language saved to session and database
5. Page reloads with new language

### Customization

```html
<!-- Add more language options in language-switcher.html -->
<button class="lang-option" data-lang="fr" onclick="changeLanguage('fr')">
    <span class="language-flag">🇫🇷</span>
    Français (French)
</button>
```

Then create `assets/i18n/fr.json` with French translations.

---

## 📋 Supported Languages

| Language | Code | Status | Native Name |
|----------|------|--------|-------------|
| English | `en` | ✅ Complete | English |
| Amharic | `am` | ✅ Complete | አማርኛ |
| Oromo | `om` | ✅ Complete | Afaan Oromoo |
| French | `fr` | 🔄 Coming | Français |
| Arabic | `ar` | 🔄 Coming | العربية |
| Tigrinya | `ti` | 🔄 Coming | ትግርኛ |

---

## 📁 Translation File Structure

### Key Naming Convention

```
{category}.{subcategory}.{key}

Examples:
- nav.dashboard
- actions.approve
- messages.success
- forms.assetCode
- errors.fieldRequired
- status.pending
- roles.admin
- workflow.requisitionTitle
```

### JSON Format Example

```json
{
  "category": {
    "subcategory": {
      "key": "Translated text"
    },
    "simpleKey": "Value"
  },
  "anotherCategory": {
    "key": "Value with {parameter} support"
  }
}
```

### Valid Keys by Category

```
✓ nav.*              - Navigation menu items
✓ actions.*          - Buttons and actions (Save, Delete, etc.)
✓ messages.*         - System messages (Success, Error, Warning)
✓ headers.*          - Page headers and titles
✓ sidebar.*          - Sidebar menu items
✓ forms.*            - Form labels and fields
✓ roles.*            - User role names
✓ workflow.*         - Process workflow steps
✓ table.*            - Table column headers
✓ common.*           - Common UI elements
✓ status.*           - Status values (Pending, Approved, etc.)
✓ errors.*           - Error messages
✓ success.*          - Success messages
```

---

## ✅ Best Practices

### 1. Use Translation Keys Consistently

```php
// ✓ Good
echo t('actions.save');

// ✗ Bad
echo "Save";
```

### 2. Use Namespaced Keys

```php
// ✓ Good - specific and organized
t('workflow.requisitionTitle')
t('workflow.selectAsset')

// ✗ Bad - too generic
t('title')
t('select')
```

### 3. Use Parameters for Dynamic Content

```php
// ✓ Good
$msg = t('headers.welcomeBack', ['name' => $user_name]);

// ✗ Bad
echo t('headers.welcome') . ' ' . $user_name;
```

### 4. Handle Missing Translations

```php
// System returns key name if not found
$text = t('nonexistent.key');  // Returns: "nonexistent.key"

// Use fallback in code
if (strpos($text, '.') !== false) {
    echo "Translation missing for: " . $text;
}
```

### 5. Organize Translations by Feature

```json
{
  "fixedAsset": {
    "forms": { ... },
    "messages": { ... },
    "errors": { ... }
  },
  "currentAsset": {
    "forms": { ... },
    "messages": { ... }
  }
}
```

### 6. Test All Languages

```php
// Debug: Show current language
echo "Current language: " . get_locale();

// Debug: Load specific language file
$translations = load_translations('am');
print_r(array_keys($translations));
```

### 7. Keep Translations Concise

```json
// ✓ Good - clear and concise
"assetCode": "Asset Code"

// ✗ Bad - too wordy
"assetCode": "Please enter the unique code for the asset in the system"
```

### 8. Maintain Consistency Across Languages

```json
// en.json
"status.approved": "Approved"

// am.json
"status.approved": "ተቀባይ"  // Should be single word like English

// ✓ Consistency maintained in structure and meaning
```

---

## 🔍 Debugging

### Check Current Language

```php
<?php
echo get_locale();  // Output: 'en' or 'am' or 'om'
?>
```

### List All Available Languages

```php
<?php
$locales = available_locales();
// Output: Array ( [0] => en [1] => am [2] => om )
?>
```

### Manually Set Language

```php
<?php
set_locale('am');  // Switch to Amharic
echo t('nav.dashboard');  // Now shows Amharic translation
?>
```

### Load Specific Translation File

```php
<?php
$amharic = load_translations('am');
echo $amharic['nav']['dashboard'];  // Direct access
?>
```

---

## 📊 Example: Complete Form with Translations

```php
<?php
require_once 'includes/i18n.php';

$page_title = t('page.request_fixed_title');
$asset_submitted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and save
    $asset_submitted = true;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $page_title; ?></title>
</head>
<body>
    <h1><?php echo $page_title; ?></h1>

    <?php if ($asset_submitted): ?>
        <div class="alert alert-success">
            <?php echo t('success.requisitionSubmitted'); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label><?php echo t('forms.assetName'); ?> <span class="required">*</span></label>
            <input type="text" name="asset_name" required 
                   placeholder="<?php echo t('forms.assetName'); ?>">
        </div>

        <div class="form-group">
            <label><?php echo t('forms.category'); ?></label>
            <select name="category">
                <option value=""><?php echo t('forms.selectOption'); ?></option>
                <option value="computers">Computers</option>
                <option value="furniture">Furniture</option>
            </select>
        </div>

        <div class="form-group">
            <label><?php echo t('forms.reason'); ?> <span class="required">*</span></label>
            <textarea name="reason" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            <?php echo t('actions.submit'); ?>
        </button>
        
        <a href="dashboard.php" class="btn btn-secondary">
            <?php echo t('actions.cancel'); ?>
        </a>
    </form>
</body>
</html>
```

---

## 🎯 Next Steps

1. ✅ Review existing files and add `<?php echo t('key'); ?>` calls
2. ✅ Test each language by switching via language switcher
3. ✅ Add missing translations to JSON files
4. ✅ Update error messages to use translation keys
5. ✅ Add support for more languages by creating new JSON files
6. ✅ Document any new translation keys in this guide

---

**Translation System Ready!** 🌍

All pages now support:
- ✅ English (en)
- ✅ Amharic (am)
- ✅ Oromo (om)
- 🔄 Easy to add French, Arabic, Tigrinya, etc.

Start using `t()` function in all your PHP files! 🚀
