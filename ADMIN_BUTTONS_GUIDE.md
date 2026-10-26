# Admin Action Buttons - Complete Implementation Guide

## Overview

This guide ensures all admin management pages have consistent, fully-functional action buttons following the pattern established in `admin/manage_users.php`.

## Standard Button Pattern

Every admin management table should include these action buttons:

### 1. **Edit Button** ✏️
- Opens a modal form for editing the record
- Populates form fields with current values
- Submits via POST with `action=edit`

### 2. **Deactivate/Activate Button** 🔘
- Toggles status between active/inactive
- Submits via POST with `action=toggle`
- Button text changes based on current status

### 3. **Delete Button** 🗑️
- Deletes the record permanently
- Shows confirmation dialog before deletion
- Submits via POST with `action=delete`

### 4. **Role-Specific Buttons** (where applicable)
- Reset Password (for users)
- Manage Permissions (for roles)
- View Details (for complex records)

---

## Complete Implementation Template

### Part 1: PHP Backend Handlers

```php
<?php
// ... includes and auth ...

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ── ADD ──────────────────────────────────────────
    if ($action === 'add') {
        $id = (int)$_POST['id'] ?? 0;
        $name = trim($_POST['name'] ?? '');
        $status = $_POST['status'] ?? 'active';
        
        if (!$name) {
            $error = 'Name is required.';
        } else {
            $res = db_execute($conn,
                "INSERT INTO table_name (name, status) VALUES (?, ?)",
                'ss',
                [$name, $status]);
            
            if ($res && $res['affected'] > 0) {
                log_audit_event($conn, $user['id'], $user['role'], 
                    'create_record', "Created {$name}", $res['insert_id']);
                $success = "Record created successfully.";
            } else {
                $error = 'Database error or duplicate name.';
            }
        }
    }

    // ── EDIT ─────────────────────────────────────────
    if ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $status = $_POST['status'] ?? 'active';
        
        if (!$id || !$name) {
            $error = 'ID and name required.';
        } else {
            db_execute($conn,
                "UPDATE table_name SET name=?, status=? WHERE id=?",
                'ssi',
                [$name, $status, $id]);
            
            log_audit_event($conn, $user['id'], $user['role'],
                'update_record', "Updated {$name}", $id);
            $success = "Record updated successfully.";
        }
    }

    // ── TOGGLE STATUS ────────────────────────────────
    if ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        $current_status = $_POST['status'] ?? 'active';
        $new_status = $current_status === 'active' ? 'inactive' : 'active';
        
        if (!$id) {
            $error = 'Invalid record ID.';
        } else {
            db_execute($conn,
                "UPDATE table_name SET status=? WHERE id=?",
                'si',
                [$new_status, $id]);
            
            log_audit_event($conn, $user['id'], $user['role'],
                'toggle_status', "Set status to {$new_status}", $id);
            $success = "Status updated to <strong>{$new_status}</strong>.";
        }
    }

    // ── DELETE ───────────────────────────────────────
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        
        if (!$id) {
            $error = 'Invalid record ID.';
        } else {
            db_execute($conn, "DELETE FROM table_name WHERE id=?", 'i', [$id]);
            
            log_audit_event($conn, $user['id'], $user['role'],
                'delete_record', "Deleted record", $id);
            $success = "Record deleted successfully.";
        }
    }
}
?>
```

### Part 2: Table with Action Buttons

```html
<div class="content-card">
    <h2>Records List</h2>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($records && $records->num_rows > 0): 
                $i = 1;
                while ($record = $records->fetch_assoc()): 
            ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= esc($record['name']) ?></td>
                    <td>
                        <span class="badge badge-<?= $record['status'] === 'active' ? 'success' : 'secondary' ?>">
                            <?= ucfirst($record['status']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="action-group">
                            <!-- EDIT BUTTON -->
                            <button class="btn btn-sm btn-secondary"
                                onclick="openEditModal(
                                    <?= (int)$record['id'] ?>,
                                    <?= json_encode($record['name']) ?>,
                                    <?= json_encode($record['status']) ?>
                                )">
                                ✏️ Edit
                            </button>

                            <!-- TOGGLE STATUS BUTTON -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?= (int)$record['id'] ?>">
                                <input type="hidden" name="status" value="<?= esc($record['status']) ?>">
                                <button type="submit" 
                                    class="btn btn-sm btn-<?= $record['status'] === 'active' ? 'warning' : 'success' ?>">
                                    <?= $record['status'] === 'active' ? '🔘 Deactivate' : '✅ Activate' ?>
                                </button>
                            </form>

                            <!-- DELETE BUTTON -->
                            <form method="POST" style="display:inline;" 
                                  onsubmit="return confirm('Delete &quot;<?= addslashes($record['name']) ?>&quot;? This cannot be undone.')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$record['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">
                                    🗑️ Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr>
                    <td colspan="4" class="text-center" style="padding:20px;opacity:0.6;">
                        No records found.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
```

### Part 3: Edit Modal

```html
<!-- ═══════════════════════════════════════════════════════════════
     EDIT MODAL
═══════════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="editModal">
    <div class="modal-box" style="max-width:500px;">
        <div class="modal-header">
            <h3>✏️ Edit Record</h3>
            <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">
            
            <div class="modal-body">
                <div class="form-group-inner">
                    <label>Name <span style="color:#f88;">*</span></label>
                    <input type="text" name="name" id="edit_name" 
                           class="form-control" required>
                </div>
                
                <div class="form-group-inner">
                    <label>Status</label>
                    <select name="status" id="edit_status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" 
                        onclick="closeModal('editModal')">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    💾 Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
```

### Part 4: JavaScript Functions

```javascript
<script>
// Generic modal functions (should be in footer.php globally)
function openModal(id) { 
    document.getElementById(id).classList.add('open'); 
}

function closeModal(id) { 
    document.getElementById(id).classList.remove('open'); 
}

// Edit modal population
function openEditModal(id, name, status) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    
    var statusEl = document.getElementById('edit_status');
    if (statusEl) {
        Array.from(statusEl.options).forEach(function(opt) {
            opt.selected = (opt.value === status);
        });
    }
    
    openModal('editModal');
}
</script>
```

---

## Pages Implementation Status

| Page | Edit | Deactivate | Delete | Status |
|------|------|-----------|---------|--------|
| manage_users.php | ✅ Modal | ✅ Toggle | ✅ Form | ✓ Complete |
| manage_roles.php | ❌ Missing | ✅ Toggle | ✅ Form | ⚠️ Partial |
| organizations.php | ✅ Modal | ✅ Toggle | ✅ Form | ✓ Complete |
| departments.php | ❌ Missing | ✅ Toggle | ✅ Form | ⚠️ Partial |
| main_departments.php | ❌ Missing | ✅ Toggle | ✅ Form | ⚠️ Partial |
| classes_units.php | ❌ Missing | ✅ Toggle | ✅ Form | ⚠️ Partial |
| stores.php | ❌ Missing | ✅ Toggle | ✅ Form | ⚠️ Partial |
| asset_categories.php | ❌ Missing | ✅ Toggle | ✅ Form | ⚠️ Partial |
| locations.php | ❌ Missing | ✅ Toggle | ✅ Form | ⚠️ Partial |

---

## Button Styling CSS

These styles should already exist in your CSS files:

```css
/* Action buttons container */
.action-group {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    align-items: center;
}

/* Button base styles */
.btn {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 4px;
    border: 1px solid;
    cursor: pointer;
    font-size: 0.85rem;
    transition: all 0.2s;
    white-space: nowrap;
}

.btn-sm {
    padding: 4px 8px;
    font-size: 0.75rem;
}

/* Button variants */
.btn-secondary {
    background: rgba(100,160,200,0.15);
    border-color: rgba(100,160,200,0.3);
    color: #9dd0ff;
}

.btn-secondary:hover {
    background: rgba(100,160,200,0.25);
    border-color: rgba(100,160,200,0.5);
}

.btn-warning {
    background: rgba(255,168,0,0.2);
    border-color: rgba(255,168,0,0.4);
    color: #ffa800;
}

.btn-warning:hover {
    background: rgba(255,168,0,0.35);
    border-color: rgba(255,168,0,0.6);
}

.btn-danger {
    background: rgba(220,53,69,0.2);
    border-color: rgba(220,53,69,0.4);
    color: #ff6b6b;
}

.btn-danger:hover {
    background: rgba(220,53,69,0.35);
    border-color: rgba(220,53,69,0.6);
}

.btn-success {
    background: rgba(40,167,69,0.2);
    border-color: rgba(40,167,69,0.4);
    color: #28a745;
}

.btn-success:hover {
    background: rgba(40,167,69,0.35);
    border-color: rgba(40,167,69,0.6);
}

.btn-primary {
    background: rgba(0,123,255,0.2);
    border-color: rgba(0,123,255,0.4);
    color: #007bff;
}

.btn-primary:hover {
    background: rgba(0,123,255,0.35);
    border-color: rgba(0,123,255,0.6);
}
```

---

## Quick Implementation Checklist

For each page that needs button updates:

- [ ] Add POST handlers for: add, edit, toggle, delete
- [ ] Add Edit button with openEditModal() call
- [ ] Add Deactivate/Activate button with POST form
- [ ] Add Delete button with confirmation dialog
- [ ] Add Edit modal HTML with form fields
- [ ] Add JavaScript openEditModal() function
- [ ] Test all buttons work correctly
- [ ] Verify audit logging works
- [ ] Test in all browsers
- [ ] Check responsive design on mobile

---

## Common Issues & Fixes

### Issue: Button not responding
**Fix:** Check that modal functions (openModal, closeModal) are included in footer.php

### Issue: Modal doesn't show
**Fix:** Verify CSS modal-overlay class has `display: flex; position: fixed;`

### Issue: Form not submitting
**Fix:** Ensure form has `method="POST"` and action name in hidden input

### Issue: Confirmation dialog not showing
**Fix:** Check that `onsubmit="return confirm(...)"` is on form, not button

### Issue: Database changes not saved
**Fix:** Verify db_execute() is used with correct type string (s=string, i=int)

---

## Testing Procedure

1. **Create a test record**
   - Click "+ Add" button
   - Fill form and submit
   - Verify record appears in table

2. **Edit the record**
   - Click "Edit" button
   - Change values
   - Click "Save Changes"
   - Verify changes appear in table

3. **Deactivate the record**
   - Click "Deactivate" button
   - Verify button changes to "Activate"
   - Verify status badge changes

4. **Reactivate the record**
   - Click "Activate" button
   - Verify button changes to "Deactivate"

5. **Delete the record**
   - Click "Delete" button
   - Confirm deletion
   - Verify record disappears from table

---

## Next Steps

1. Review current implementation in manage_users.php
2. Add Edit modals to: manage_roles.php, departments.php, main_departments.php, classes_units.php
3. Test all buttons across all pages
4. Add any missing audit logging
5. Update documentation with any page-specific variations

---

**Status:** Ready for implementation
**Priority:** High - Affects all admin management pages
**Estimated Time:** 3-5 hours for all pages
