# 📊 EFPT_AMS - System Flows & Workflows

**Visual guides to understand how the system works**

---

## 🎯 Main Asset Lifecycle Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                   COMPLETE ASSET LIFECYCLE                       │
└─────────────────────────────────────────────────────────────────┘

   1. ASSET ARRIVES       2. STORE MANAGER       3. USER REQUESTS
   AT ORGANIZATION           REGISTERS ASSET        FROM STORE
         │                        │                      │
         │                        ↓                      ↓
         └─────────────→ System tracks asset ←─────────────┘
                                  │
                                  ↓
                        ┌─────────────────┐
                        │  ASSET IN       │
                        │  INVENTORY      │
                        └─────────────────┘
                                  │
                    ┌─────────────┴─────────────┐
                    ↓                           ↓
              REQUEST MADE                ASSET USED
                    │                           │
                    ↓                           ↓
            APPROVAL CHAIN                ASSET TRANSFER
            (3-5 days)                     (2-3 days)
                    │                           │
                    ↓                           ↓
            ┌──────────────┐           ┌──────────────┐
            │ ASSET        │           │ ASSET        │
            │ ISSUED       │           │ TRANSFERRED  │
            └──────────────┘           └──────────────┘
                    │                           │
                    └─────────────┬─────────────┘
                                  ↓
                        ┌─────────────────┐
                        │  ASSET USED BY  │
                        │  DEPARTMENT     │
                        └─────────────────┘
                                  │
                    ┌─────────────┴─────────────┐
                    ↓                           ↓
              RETURN REQUEST            DAMAGED/LOST
                    │                           │
                    ↓                           ↓
            ┌──────────────┐         ┌──────────────┐
            │ ASSET        │         │ ASSET        │
            │ RETURNED     │         │ DISPOSED     │
            └──────────────┘         └──────────────┘
                    │                           │
                    └─────────────┬─────────────┘
                                  ↓
                        ┌─────────────────┐
                        │  BACK IN        │
                        │  INVENTORY      │
                        └─────────────────┘
```

---

## 📝 Workflow 1: Asset Requisition (REQUEST → APPROVAL → ISSUANCE)

```
┌──────────────────────────────────────────────────────────────────┐
│              ASSET REQUISITION WORKFLOW (Model 20)               │
└──────────────────────────────────────────────────────────────────┘

STEP 1: USER INITIATES REQUEST
┌─────────────────────────────────────┐
│ 👤 Regular User                     │
│ - Login to system                   │
│ - Click "Request Asset"             │
│ - Select asset type                 │
│ - Fill in details:                  │
│   • What: Laptop                    │
│   • Why: Financial reports          │
│   • Specs: Windows, 16GB RAM        │
│ - Submit request                    │
└─────────────────────────────────────┘
         ↓
    ✉️ REQUEST CREATED
    Status: "Pending Class Head"
         │
         ↓

STEP 2: CLASS HEAD REVIEWS & APPROVES
┌─────────────────────────────────────┐
│ 📋 Class Head                       │
│ - Login to system                   │
│ - Go to "Pending Approvals"         │
│ - See request with:                 │
│   • Who requested                   │
│   • What they need                  │
│   • Reason & specs                  │
│ - Review notes:                     │
│   ✅ APPROVE (Yes, forward)         │
│   ❌ REJECT (No, deny)              │
└─────────────────────────────────────┘
         │
    ┌────┴────┐
    ↓         ↓
  ✅YES      ❌NO
    │         │
    ↓         ↓
FORWARD   REQUEST DENIED
TO DEPT   (User notified)
HEAD      
    │
    ↓

STEP 3: DEPT HEAD REVIEWS & APPROVES
┌─────────────────────────────────────┐
│ 👔 Department Head                  │
│ - Get forwarded request             │
│ - Review approver chain             │
│ - Check budget (optional)           │
│ - Approve or reject                 │
│ - Add comments if needed            │
└─────────────────────────────────────┘
         │
    ┌────┴────┐
    ↓         ↓
  ✅YES      ❌NO
    │         │
    ↓         ↓
FORWARD   REQUEST DENIED
TO STORE
    │
    ↓

STEP 4: STORE MANAGER ISSUES ASSET
┌─────────────────────────────────────┐
│ 🏪 Store Manager                    │
│ - See approved requisition          │
│ - Find asset in inventory           │
│ - Generate voucher (Model 22)       │
│ - Print with asset details          │
│ - Give to user with asset           │
│ - Record in system                  │
└─────────────────────────────────────┘
         ↓
    ✅ ASSET ISSUED
    User receives laptop
         │
         ↓

STEP 5: USER RECEIVES & CONFIRMS
┌─────────────────────────────────────┐
│ 👤 Regular User                     │
│ - Receive asset from store          │
│ - Sign voucher confirming receipt   │
│ - Upload signature to system        │
│ - Asset now assigned to user        │
└─────────────────────────────────────┘
         ↓
    ✅ COMPLETE
    Laptop now in user's name
    Ready to use

TIMELINE: 2-5 business days
AUDIT TRAIL: Every step logged & traceable
```

---

## 🏪 Workflow 2: Store Manager - Adding Assets to System

```
┌──────────────────────────────────────────────────────────────────┐
│          STORE MANAGER: REGISTER NEW ASSET IN SYSTEM             │
└──────────────────────────────────────────────────────────────────┘

TRIGGER: New equipment arrives at store

STEP 1: OPEN ADD ASSET PAGE
┌────────────────────────────────────────┐
│ 🏪 Store Manager                       │
│ - Login to system                      │
│ - Click "Add Fixed Asset" OR           │
│         "Add Current Asset"            │
│ - See form with fields                 │
└────────────────────────────────────────┘
         ↓

STEP 2: ENTER BASIC INFORMATION
┌────────────────────────────────────────┐
│ 📝 Asset Details Section               │
│                                        │
│ Asset Name: Dell XPS 15 Laptop        │
│ ↓ [Auto-generates code below]         │
│                                        │
│ Asset Code:  [FA-DELL-XPS]  [Gen]     │
│            ← Auto-generated from name │
│            ← User can edit/regenerate │
│ (Or type manual: FA-CUSTOM-001)       │
└────────────────────────────────────────┘
         ↓

STEP 3: SELECT CATEGORY & CONDITION
┌────────────────────────────────────────┐
│ 📋 Category Section                    │
│                                        │
│ Category:      [Computers]            │
│ Sub-Category:  [Laptops]              │
│                                        │
│ Condition:     [New]                  │
│ Quantity:      [1]                    │
│ Unit:          [Pieces]               │
└────────────────────────────────────────┘
         ↓

STEP 4: SELECT ORGANIZATION & STORE
┌────────────────────────────────────────┐
│ 🏢 Organization & Store Section       │
│                                        │
│ Organization:  [Head Office]          │
│                                        │
│ Main Dept:     [IT Department]        │
│                                        │
│ Select Store:  [Technology Store] ▼   │
│                        │               │
│                        ↓               │
│        🏪 STORE METADATA AUTO-FILLS:  │
│        ┌──────────────────────────┐   │
│        │ Store Code: STR-TEC-STO  │   │
│        │ Location: Abadina        │   │
│        │ Phys Code: (filled)      │   │
│        └──────────────────────────┘   │
│                                        │
│ These auto-populate from store config │
└────────────────────────────────────────┘
         ↓

STEP 5: ENTER COST & CONDITION
┌────────────────────────────────────────┐
│ 💵 Cost & Acquisition                  │
│                                        │
│ Purchased Date: [2026-08-17]          │
│ Purchased Cost: [$1,200]            │
│ Physical Condition: [New]             │
│ Location in Store: [Room 101]         │
└────────────────────────────────────────┘
         ↓

STEP 6: VERIFY & SAVE
┌────────────────────────────────────────┐
│ ✅ System Checks:                      │
│                                        │
│ ✓ All required fields filled          │
│ ✓ Asset code not duplicate            │
│ ✓ Valid organization & store          │
│ ✓ Date format correct                 │
│                                        │
│ [SAVE ASSET]                          │
└────────────────────────────────────────┘
         ↓
         ↓ Database validation
         ↓
    ✅ SUCCESS!
┌─────────────────────────────────────────┐
│ ✓ "Asset added successfully"            │
│                                         │
│ Asset Details:                          │
│ • Code: FA-DELL-XPS                    │
│ • Name: Dell XPS 15 Laptop             │
│ • Value: $1,200                        │
│ • Status: Ready for requisition        │
│                                         │
│ Next: Users can now request this asset │
└─────────────────────────────────────────┘

KEY FEATURES:
✨ Auto-code generation from asset name
✨ Store metadata auto-fills from location
✨ Duplicate code detection
✨ Audit trail created
✨ Instant availability for requisition
```

---

## 🔄 Workflow 3: Asset Transfer Between Departments

```
┌──────────────────────────────────────────────────────────────────┐
│           ASSET TRANSFER: MOVE BETWEEN DEPARTMENTS               │
└──────────────────────────────────────────────────────────────────┘

SCENARIO: Laptop needs to move from IT Dept → HR Dept

STEP 1: CURRENT USER INITIATES TRANSFER
┌───────────────────────────────────────┐
│ 👤 Employee in IT Dept                │
│ - Click "Transfer Asset"              │
│ - Select asset: Dell Laptop           │
│ - Current Dept: IT (auto-filled)      │
│ - New Dept: HR (select)               │
│ - Reason: "Reorganization"            │
│ - Notes: "Update antivirus first"     │
│ - Submit                              │
└───────────────────────────────────────┘
         ↓
    📤 TRANSFER REQUEST SENT
         │
         ↓

STEP 2: RELEASING DEPT HEAD APPROVES
┌───────────────────────────────────────┐
│ 👔 IT Department Head                 │
│ - See transfer request                │
│ - Review asset details                │
│ - Approve release: YES                │
│ - Add comment (optional)              │
│ - Click "Approve Release"             │
└───────────────────────────────────────┘
         ↓
    ✅ RELEASED FROM IT
         │
         ↓

STEP 3: RECEIVING DEPT HEAD APPROVES
┌───────────────────────────────────────┐
│ 👔 HR Department Head                 │
│ - See incoming transfer               │
│ - Review asset & condition notes      │
│ - Approve acceptance: YES             │
│ - Click "Approve Reception"           │
└───────────────────────────────────────┘
         ↓
    ✅ ACCEPTED BY HR
         │
         ↓

STEP 4: SYSTEM UPDATES OWNERSHIP
┌───────────────────────────────────────┐
│ ✅ ASSET TRANSFERRED                  │
│                                       │
│ Previous Owner: IT Department         │
│ New Owner: HR Department              │
│ Transfer Date: 2026-08-17             │
│ Status: Active in HR                  │
│                                       │
│ Audit Log Entry Created               │
│ Both depts can see in records         │
└───────────────────────────────────────┘

TIMELINE: 1-3 business days
AUDIT TRAIL: Fully traceable
```

---

## 📥 Workflow 4: Asset Return to Store

```
┌──────────────────────────────────────────────────────────────────┐
│            ASSET RETURN: BACK TO STORE/DISPOSAL                  │
└──────────────────────────────────────────────────────────────────┘

SCENARIO: User finished project, returns laptop

STEP 1: USER INITIATES RETURN
┌──────────────────────────────────────┐
│ 👤 Employee with Asset               │
│ - Click "Return Asset"               │
│ - Select: Dell Laptop                │
│ - Reason: "Project completed"        │
│ - Condition: Select option:          │
│   • New (looks brand new)            │
│   • Good (normal wear & tear)        │
│   • Fair (some damage)               │
│   • Poor (heavy damage)              │
│   • Damaged (broken)                 │
│ - Notes: "Works perfectly"           │
│ - Submit return request              │
└──────────────────────────────────────┘
         ↓
    📋 RETURN REQUEST SUBMITTED
         │
         ↓

STEP 2: DEPARTMENT HEAD APPROVES
┌──────────────────────────────────────┐
│ 👔 Department Head                   │
│ - See return request                 │
│ - Review condition report            │
│ - Approve: YES/NO                    │
│ - Add notes (e.g., "Approved")      │
└──────────────────────────────────────┘
         │
    ┌────┴────┐
    ↓         ↓
  ✅YES      ❌NO
    │         │
    ↓         ↓
FORWARD   RETURN REJECTED
TO STORE  (Asset stays with user)
    │
    ↓

STEP 3: STORE MANAGER INSPECTS
┌──────────────────────────────────────┐
│ 🏪 Store Manager                     │
│ - Receive asset from user            │
│ - Physical inspection:               │
│   ✓ Check condition                  │
│   ✓ Test functionality               │
│   ✓ Check for damage                 │
│   ✓ Verify serial number             │
│ - Update condition if needed         │
│ - Accept or reject                   │
└──────────────────────────────────────┘
         │
    ┌────┴────┐
    ↓         ↓
  ✅OK      ❌DAMAGED
    │         │
    ↓         ↓
BACK IN    DISPOSAL
INVENTORY  PROCESS
    │         │
    ↓         ↓
Ready for  Write-off &
next user  Scrap
         
TIMELINE: 2-3 business days
AUDIT TRAIL: Condition documented
```

---

## 📊 Workflow 5: Approval Chain (Authorization Levels)

```
REQUEST TRAVELS UP THE CHAIN
┌────────────────────────────────────────────────────────────┐
│                                                            │
│  LEVEL 1: CLASS HEAD (If applicable)                      │
│  └─ Reviews basic need                                    │
│     └─ Approves or rejects                                │
│        └─ Forwards to next level                          │
│           │                                               │
│           ↓                                               │
│  LEVEL 2: DEPARTMENT HEAD                                 │
│  └─ Reviews department budget                            │
│     └─ Checks appropriateness                            │
│        └─ Approves or rejects                            │
│           └─ Forwards to store                           │
│              │                                            │
│              ↓                                            │
│  LEVEL 3: STORE MANAGER                                  │
│  └─ Checks inventory availability                        │
│     └─ Issues asset or backlogs                          │
│        └─ User receives                                  │
│           │                                               │
│           ↓                                               │
│  ✅ COMPLETE - ASSET IN USER'S HAND                     │
│                                                            │
└────────────────────────────────────────────────────────────┘

REJECTION AT ANY LEVEL:
- Request stops
- User gets notification with reason
- Can resubmit after addressing feedback
```

---

## 🔐 Role-Based Dashboard Access

```
EACH ROLE SEES DIFFERENT DASHBOARD

┌─────────────────────────────────────────────┐
│ 👑 SYSTEM ADMIN                             │
├─────────────────────────────────────────────┤
│ ✓ System Configuration                      │
│ ✓ Manage Users & Roles                      │
│ ✓ All Reports (System-wide)                │
│ ✓ View all assets                          │
│ ✓ View all requisitions                    │
│ ✓ Permission Management                    │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ 🏪 STORE MANAGER                            │
├─────────────────────────────────────────────┤
│ ✓ Add/Register Assets                      │
│ ✓ View Inventory                           │
│ ✓ Issue Approved Requisitions              │
│ ✓ Generate Vouchers (Model 22)             │
│ ✓ Track Stock Levels                       │
│ ✓ Store-specific Reports                  │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ 👔 DEPARTMENT HEAD / CLASS HEAD             │
├─────────────────────────────────────────────┤
│ ✓ View Pending Approvals                   │
│ ✓ Approve/Reject Requests                  │
│ ✓ View Department Assets                   │
│ ✓ Transfer Assets                          │
│ ✓ Department Reports                       │
│ ✓ Approve Returns                          │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ 👤 REGULAR USER                             │
├─────────────────────────────────────────────┤
│ ✓ My Assets (assigned to me)               │
│ ✓ Request Asset                            │
│ ✓ Submit Return Request                    │
│ ✓ View My Requisitions                     │
│ ✓ Download Asset Documents                 │
└─────────────────────────────────────────────┘
```

---

## 💾 Data Flow: From Request to Storage

```
USER REQUEST
    ↓
Web Form Submitted
(HTTP POST)
    ↓
PHP Backend
- Validates input
- Checks permissions
- Verifies CSRF token
    ↓
Prepared SQL Query
- Uses ? placeholders
- Prevents SQL injection
    ↓
MySQL Database
- Stores data
- Updates tables
- Creates audit log
    ↓
Response to User
- Success message
- Redirects to dashboard
    ↓
✅ Complete
Data safely stored with audit trail
```

---

## 🔄 Asset Status Transitions

```
ASSET LIFECYCLE STATES

NEW (In Inventory)
    ↓ [Requisition approved]
ASSIGNED (With User)
    ↓ [Transfer requested]
IN TRANSITION (Moving)
    ↓ [Transfer complete]
REASSIGNED (New User/Dept)
    ↓ [Can repeat or return]
IN TRANSITION / RETURN REQUEST
    ↓ [Condition inspected]
RETURNED (Back in Store)
    ↓ [Next options:]
    ├─ Ready for new requisition (GOOD CONDITION)
    │  └─ Back to NEW status
    └─ Disposal (DAMAGED/BROKEN)
       └─ DISPOSED (Write-off)

Each transition logged with:
- Who made change
- When (date & time)
- Why (reason code)
- From/To (locations/users)
```

---

## 🎯 Key Decision Points

```
REQUEST APPROVAL DECISION TREE

┌─ Does request have all required info?
│  ├─ YES → Continue
│  └─ NO → Return to requester (Fix & resubmit)
│
├─ Is requesting user authorized?
│  ├─ YES → Continue
│  └─ NO → Reject (Access denied)
│
├─ Does asset exist in inventory?
│  ├─ YES → Continue
│  └─ NO → Backlog (Wait for stock)
│
├─ Is budget available (if applicable)?
│  ├─ YES → Continue
│  └─ NO → Reject or defer
│
├─ Is requisition reasonable?
│  ├─ YES → Approve
│  └─ NO → Reject (Provide reason)
│
└─ → ISSUE ASSET TO USER
```

---

## 📱 Mobile-Friendly Design

```
RESPONSIVE ACROSS DEVICES

Desktop (1920px wide)
├─ Full layout
├─ Side navigation visible
└─ All features accessible

Tablet (768px wide)
├─ Adapted layout
├─ Touch-friendly buttons
└─ Readable on smaller screen

Mobile (360px+ wide)
├─ Single column
├─ Collapsible menu
└─ Essential functions only
```

---

**EFPT_AMS System Flows** | *Complete documentation for asset lifecycle*
