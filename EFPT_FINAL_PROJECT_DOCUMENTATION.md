# EFPT Final Project Documentation

**Project:** Ethiopian Federal Police Technology Expansion Asset Management System  
**Application name:** EFPT-AMS  
**Version:** 1.0.0  
**Documentation snapshot:** October 12, 2026  
**Primary stack:** PHP 8.x, MySQL/MariaDB, Apache, HTML/CSS/JavaScript  
**Default timezone:** Africa/Addis_Ababa  

> This document describes the application as represented by the current source tree. It is a technical and operational guide, not a claim that a particular production server, database, network, or deployment has been independently certified. Verify environment-specific values before deploying.

## 1. Purpose and scope

EFPT-AMS supports the registration and management of organizational assets through inventory, requests, approvals, issuance, custody, transfer, return, inspection, maintenance, reporting, and disposal. It also provides administrative and operational facilities for managing users, organizational units, stores, asset categories, permissions, notifications, and audit history.

The application is a procedural PHP system with shared include files and role-aware pages. It uses MySQL-compatible storage through `mysqli`, server-rendered HTML, and browser-side JavaScript. There is no framework-managed ORM or separate build pipeline required by the documented local setup.

## 2. Main capabilities

- Register and maintain fixed assets and consumable/current inventory.
- Track stock quantities, asset identifiers, locations, store assignment, custody, and status.
- Submit requisitions and advance them through organizational approvals.
- Generate and print Model 22 issuance vouchers and uniform vouchers.
- Record asset returns, approval decisions, condition, inspection, and maintenance routing.
- Transfer assets between stores or organizational units.
- Manage inspections, maintenance jobs, write-offs, and disposal requests.
- Administer users, organizational hierarchy, roles, module permissions, stores, categories, locations, and announcements.
- Present reports and operational summaries, including PDF and spreadsheet-style exports where implemented.
- Deliver in-app notifications and record significant activity in audit logs.
- Support English, Amharic, and Afaan Oromoo interface translations.
- Support account security features including session expiration, session registry management, password change, and optional two-factor authentication.
- Provide optional HR integration, CCTV inventory/status views, asset maps, and role delegation workflows.

The appearance of a route or menu item does not alone prove that a user has permission to complete an operation. Server-side authorization and configured role/module permissions remain authoritative.

## 3. Technology and runtime

### Server-side

- PHP 8.x; use a version with `mysqli`, `mbstring`, `json`, `openssl`, and `gd` enabled as appropriate for the installed features.
- Apache with PHP support; `mod_rewrite` and `mod_headers` are used by the repository configuration.
- MySQL 8.x or a compatible MariaDB version.
- InnoDB and UTF-8 (`utf8mb4`) are expected for application data.

### Browser-side

- HTML5, CSS, and JavaScript without a required frontend compilation step.
- `assets/css/` contains application styles.
- `assets/js/` contains browser behavior, localization, calendar, table filtering, charts, and assistant-related scripts.
- A service worker (`sw.js`) provides best-effort static asset caching for eligible requests. Dynamic PHP requests are not intended to be served from cache.

### PDF rendering

Some report downloads use `includes/html_pdf_renderer.php`, which searches for an installed Edge, Chrome, or Chromium executable and prints a temporary HTML document to PDF. Set `EFPT_HEADLESS_BROWSER` to the executable path when browser auto-detection is not appropriate. The PHP process needs permission to execute the browser and create temporary files. If no compatible browser is available, individual report routes may use their own fallback or show an error.

## 4. Application layout

| Path | Responsibility |
|---|---|
| `index.php` | Login page, authentication entry, and post-login routing |
| `admin/` | Administrative dashboards, users, roles, permissions, organization setup, asset categories, locations, backups, audit/security tools |
| `store/` | Store dashboard, stock operations, asset creation, requisition fulfillment, Model 22 and uniform vouchers, operational reports |
| `user/` | Regular-user dashboard and approved requisition acceptance |
| `class_head/` | Class-level dashboards, requisition and return approvals |
| `dept_head/` | Department-level dashboards, requisition and return approvals |
| `main_department_head/` | Main-department dashboards, approvals, and review queues |
| `maintenance_head/` | Maintenance oversight, assignments, return inspection review |
| `maintenance_inspection_team/` | Assigned inspection tasks and inspection updates |
| `finance/` | Finance/compliance requisition review |
| `shared/` | Cross-role assets, requisitions, returns, transfers, maintenance, reports, transactions, audit, account, notification, and delegation pages |
| `includes/` | Configuration, authentication, database access, security, shared services, templates, and workflow helpers |
| `database/` | Consolidated schema, migration scripts, and seed SQL |
| `scripts/` | CLI setup, backup, seed, and migration utilities |
| `assets/` | CSS, JavaScript, images, icons, and translation dictionaries |
| `uploads/` | Runtime upload storage; must be writable by the web process and protected against script execution |
| `tests/` | PHPUnit test suite |
| `docs/` | Supplementary project documentation |

The inventory of individual pages is intentionally grouped by responsibility here; use the corresponding directory for the current route list because the source tree evolves.

## 5. User roles and access

The schema currently defines the following role keys:

| Role key | Typical responsibility |
|---|---|
| `admin` | System configuration, user/role administration, governance, reports, and cross-system oversight |
| `org_admin` | Organization-scoped administration and operational review |
| `store_manager` | Inventory, stock operations, fulfillment, vouchers, transfers, and maintenance routing |
| `main_department_head` | Main-department approval and oversight |
| `department_head` | Department approval and oversight |
| `class_head` | Class/unit approval and oversight |
| `maintenance_head` | Maintenance workflow oversight and assignment |
| `maintenance_inspection_team` | Assigned inspections and results |
| `finance_compliance` | Finance/compliance review workflows |
| `regular_user` | Personal requests, issued assets, returns, and account features |

An administrator may also have a super-administrator flag; super-admin status is not a separate `users.role` enum value. Authorization is enforced by login checks, role guards, permission helpers, organizational scopes, and delegation-aware checks. The exact effective access can depend on database permission settings and user hierarchy assignments.

### Access administration principles

1. Assign each account only the role required for its duties.
2. Populate the correct organization, main department, department, class/unit, and store relationships.
3. Review module permissions and active delegations after role changes.
4. Test access with a representative account for each role after configuration.
5. Do not rely on hidden navigation links as an access-control boundary; direct routes must remain protected server-side.

## 6. Core business workflows

### 6.1 Asset requisition and issuance

1. A user creates a request with the requested item, quantity, priority, and purpose.
2. The request is reviewed by the required class, department, and main-department approvers. Applicable steps depend on the requester’s hierarchy and workflow rules.
3. The request reaches the store for fulfillment.
4. The store selects available stock and records issuance using the appropriate voucher workflow.
5. The requester acknowledges receipt when the workflow requires acceptance.
6. Status changes and decisions are available in transaction detail and audit history; notifications may alert participants.

The principal request record is `model20_requisitions`; related requisition items and voucher records hold line-item and issuance details.

### 6.2 Asset returns

1. The custodian submits a return request and records condition/accessory details.
2. The request follows its configured approval and inspection path.
3. A store or maintenance reviewer may assess the asset and route it for maintenance when needed.
4. The system records inspection, acceptance, finalization, and any resulting asset-status changes.

The main transaction is stored in `asset_returns`. Its fields include workflow step, approvals, condition, inspection, and maintenance-routing data. The exact path varies with the return state and configured workflow.

### 6.3 Transfers

1. An authorized user initiates a transfer and supplies the destination and asset details.
2. Required organizational approvals and store review are performed.
3. The receiving party/store confirms the transfer as required.
4. The system records the transfer and updates the relevant custody or location state.

Transfer records are stored in `asset_transfers`; voucher and custody records may provide associated evidence.

### 6.4 Maintenance and inspection

1. A damaged or inspection-required asset is referred into maintenance.
2. The maintenance head reviews the task and assigns an inspection team member when applicable.
3. The assigned team records the inspection result, notes, and date.
4. The maintenance workflow records repair progress and outcome.
5. The asset is returned to service, retained for further work, or routed toward write-off/disposal according to the outcome and authorized decision.

Maintenance jobs and inspection data are represented by maintenance and inspection tables in the master schema. Review the actual record history before finalizing a physical asset action.

### 6.5 Uniform requests

Uniform items use their own item, size, request, line-item, notification, and voucher records. They share the broader approval and fulfillment principles but are not the same inventory model as fixed assets.

### 6.6 Delegation

Delegation workflows allow specifically authorized work to be handled by an assigned delegate. Activation, acceptance, approval, and revocation/handover states are persisted. Delegated permissions should be time-bounded and reviewed; a delegation does not replace the normal account lifecycle or administrator review.

## 7. Data model overview

The canonical schema and baseline data are in `database/sqlSchemaAll.sql`. It creates or updates the `efpt_ams` database and uses UTF-8-compatible character sets. There are additional SQL/PHP files in `database/migrations/`; apply only migrations required by the version being installed.

Important table groups include:

| Group | Representative tables | Purpose |
|---|---|---|
| Identity and hierarchy | `users`, `organizations`, `main_departments`, `departments`, `classes_units`, `stores` | Accounts and organizational/store structure |
| Inventory | `fixed_assets`, `consumable_assets`, `asset_categories`, `stock_transactions` | Asset identity, current stock, category, and movement |
| Requisition and issuance | `model20_requisitions`, `requisition_items`, `model22_vouchers` | Requests, line items, and issue records |
| Returns and transfer | `asset_returns`, `asset_transfers`, transfer/return voucher records | Asset hand-back and movement workflows |
| Maintenance | `maintenance_records`, maintenance team/inspection tables, `asset_inspections` | Repair, assignment, and condition review |
| Governance and access | `role_permissions`, `role_module_permissions`, delegation-related tables | Role capabilities and delegated work |
| Security and traceability | `audit_logs`, `login_attempts`, `blocked_ips`, `user_login_sessions` | Audit trail, login controls, and session management |
| Messaging | `notifications`, announcement tables | User-facing alerts and announcements |
| Facilities | `buildings`, `floors`, `rooms`, `locations` | Physical location structure |
| Supplemental | uniform tables, `cctv_cameras`, `cctv_status_logs`, document/signature tables | Uniform, CCTV, document, and signature features |

Soft-delete columns are used on selected records; a record removed from the active view may be recoverable from the recycle-bin interface. Permanent deletion is a separate administrative action where supported. Database foreign keys and indexes should be preserved when modifying the schema.

## 8. Local installation with XAMPP

### Prerequisites

- XAMPP or equivalent Apache + PHP + MySQL/MariaDB installation.
- PHP 8.x with `mysqli` and the extensions required by enabled functions.
- A browser and a database account with appropriate privileges.
- Optional: Edge/Chrome/Chromium for browser-rendered PDF reports.

### Installation steps

1. Place the project folder under the Apache document root, for example:

   ```text
   C:\xampp1\htdocs\EFPT_AMS
   ```

2. Start Apache and MySQL/MariaDB using the local service manager.
3. Create the application database and import the consolidated schema. For example, from a MySQL command prompt:

   ```sql
   CREATE DATABASE efpt_ams CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

   Then import `database/sqlSchemaAll.sql` using phpMyAdmin or the MySQL client.
4. Configure database values using the environment configuration supported by `includes/config.php` (the project-root `.env` file is preferred for local configuration):

   ```dotenv
   DB_HOST=localhost
   DB_USER=efpt_app
   DB_PASS=replace_with_a_strong_secret
   DB_NAME=efpt_ams
   ```

   Use the actual host name/IP and credentials for the environment. For a remote database, ensure the server is reachable, the database account is allowed from the web server’s source address, and firewall rules permit the MySQL port. Never commit real credentials.
5. Verify the configured database is reachable before signing in.
6. Open:

   ```text
   http://localhost/EFPT_AMS/
   ```

7. Create or provision the initial administrator using the approved setup process. Do not rely on demo credentials copied from older documentation. Set `DEFAULT_ADMIN_USERNAME` and `DEFAULT_ADMIN_PASSWORD_HASH` securely in the deployment environment as appropriate.
8. Sign in and immediately verify the administrator account, password-change policy, organizational records, user hierarchy, stores, and role permissions.

### Remote MySQL/MariaDB checklist

- `DB_HOST` resolves from the Apache/PHP host, not from the end-user workstation.
- The database listens on the configured interface and port.
- Firewall rules allow only the required application-server connection.
- The database account uses a strong password and a restricted host grant; avoid remote root access.
- MySQL/MariaDB privileges are limited to the application database.
- Backups are stored securely and are not publicly downloadable.

## 9. Environment configuration

Configuration is loaded primarily from `includes/config.php`, which reads a `.env` file if present. Environment constants may also be supplied by the PHP/server environment.

| Setting | Meaning | Operational guidance |
|---|---|---|
| `DB_HOST` | Database host | Local host or administrator-approved database server |
| `DB_USER` / `DB_PASS` | Database credentials | Use a dedicated least-privilege application account |
| `DB_NAME` | Database name | Normally `efpt_ams` |
| `SITE_URL` | Base site URL | Defaults from request host and `/EFPT_AMS`; configure explicitly behind proxies or at a custom domain |
| `ENABLE_SCHEMA_MIGRATIONS` | Controls optional automatic schema bootstrap | Defaults to `false`; keep off in production unless performing a reviewed development operation |
| `SESSION_TIMEOUT` | Session idle timeout in seconds | Defaults to 3600 seconds |
| `MAX_LOGIN_ATTEMPTS` | Login lockout threshold | Defaults to 4 |
| `LOGIN_LOCKOUT_MINUTES` | Lockout period | Defaults to 5 minutes |
| `HR_INTEGRATION_API_KEY` | Optional HR integration secret | Store outside source control; leave unset when integration is not used |
| `EFPT_HEADLESS_BROWSER` | Optional Chromium browser path for PDF | Set to a known executable if auto-detection is unsuitable |

Keep `.env` out of source control, restrict file permissions, and do not place production secrets in documentation, logs, screenshots, or tickets.

## 10. Docker development option

The repository includes `Dockerfile` and `docker-compose.yml`. The compose file describes the PHP/Apache application, MySQL, phpMyAdmin, and Redis services and exposes the app on port 8080 by default.

1. Review the compose environment values before starting. Replace development-only database credentials; do not use the checked-in example secrets for a production deployment.
2. From the project root run:

   ```bash
   docker compose up -d --build
   ```

3. Open `http://localhost:8080`.
4. Check service status and logs:

   ```bash
   docker compose ps
   docker compose logs app
   docker compose logs db
   ```

5. Stop development services with:

   ```bash
   docker compose down
   ```

The MySQL volume is persistent. Removing volumes can erase database data; do not use volume-removal commands unless the data loss is intentional and backed up. The included Docker configuration is a development starting point and requires production security review.

## 11. Migrations, schema changes, and seeds

- The consolidated schema is `database/sqlSchemaAll.sql`.
- Incremental migration files are under `database/migrations/`; some are SQL and some PHP.
- `ENABLE_SCHEMA_MIGRATIONS` defaults to `false`.
- `includes/db_connect.php` contains guarded compatibility handling for delegation workflow columns. This is not a general migration runner.
- `scripts/run_migration_efp.php` runs the specific initial-data SQL file it names; it is not a universal runner for every migration in the folder.
- Seed scripts such as `scripts/seed_sample_data.php`, `scripts/seed_sample_assets.php`, and `scripts/seed_sample_announcements.php` insert sample data. Review their behavior before running against non-development data.

Before applying a migration:

1. Back up the database and verify that the backup can be read.
2. Read the entire migration and identify whether it is safe to re-run.
3. Test it against a copy of the target database.
4. Apply it with a database account that has only the required privileges.
5. Verify affected tables, records, workflows, and application logs.
6. Record which migration was applied and when.

Do not enable general runtime schema changes in production as a substitute for a controlled migration plan.

## 12. Security and privacy

The source includes several security controls. Production configuration and operational practices determine their effectiveness.

- **Authentication and authorization:** protected pages use login and role checks; module permissions and hierarchy scopes further constrain work.
- **Prepared database statements:** use `db_query()` and `db_execute()` with bound values for database input.
- **HTML output:** use the project escaping helper `esc()` when rendering untrusted text.
- **CSRF protection:** include `csrf_field()` in forms and verify POST requests using the CSRF helper.
- **Sessions:** secure session settings include HttpOnly, SameSite, strict mode, timeout handling, session-ID regeneration, and login-session revocation support.
- **Password controls:** password hashing, login lockout/rate controls, and password-history helpers are present. Apply the current organization password policy.
- **Security headers:** CSP, frame protections, MIME-sniffing protections, referrer policy, and related headers are configured in shared security code and `.htaccess`.
- **Audit:** important activities are recorded in `audit_logs`; access to audit records should be restricted.
- **Uploads:** limit allowed formats and sizes; ensure the web server cannot execute uploaded PHP or other scripts. Restrict direct access to sensitive upload content as required by policy.
- **Secrets:** keep credentials, integration keys, and signing material out of the repository. Rotate any secret that has ever been committed or shared.
- **Transport:** use HTTPS outside local development, especially when login credentials, signatures, personnel records, or asset accountability data are transmitted.

No checklist can guarantee that a deployed instance is secure. Review the full deployment, permissions, network exposure, dependency versions, backup practices, and applicable institutional privacy rules.

## 13. Day-to-day administration

### New user onboarding

1. Create the account through the approved administrative page or import process.
2. Assign the correct role and organizational hierarchy.
3. Confirm active/inactive state, identifiers, store assignment, and required profile fields.
4. Require a secure initial password change if applicable.
5. Test login and verify that the user sees only authorized modules and records.

### Offboarding and access review

1. Disable the account or follow the approved account-retention process.
2. Revoke active sessions and delegations as appropriate.
3. Reassign any pending approval, custody, maintenance, or store responsibility.
4. Preserve required audit evidence; do not delete records needed for accountability.

### Inventory control

- Record assets using the correct fixed or consumable model.
- Verify asset code, serial number (where applicable), category, quantity, cost, organization, store, and current condition.
- Record every issue, return, transfer, adjustment, write-off, and disposal through its authorized workflow.
- Reconcile physical stock with system quantities routinely.

### Account sessions

Users can access their own account/session controls from **My Account** where the session registry is available. If an older database is missing the `user_login_sessions` table, apply the migration `database/migrations/add_user_login_sessions.sql` following the migration procedure above; the application may also create the table on demand in supported flows.

## 14. Backup, restore, and continuity

### Backup policy

Use a scheduled database backup and separately back up required uploaded files. Keep at least one encrypted, access-restricted copy outside the application server. Define retention, recovery point, and recovery time objectives with the system owner.

Example logical backup (adapt account and output location):

```bash
mysqldump --single-transaction --routines --triggers -u <DB_USER> -p efpt_ams > efpt_ams_backup.sql
```

The admin interface includes backup-related pages/scripts, but verify destination, permissions, retention, encryption, and restore capability before relying on them operationally.

### Restore verification

1. Restore to an isolated test database/server.
2. Confirm table counts and representative records.
3. Sign in with a test account and exercise read-only workflows.
4. Verify uploads and any linked documents are present.
5. Document restore duration and any missing dependencies.

Do not test restoration over the live database.

## 15. Performance notes

Performance depends on hardware, data volume, network latency, database indexes, PHP configuration, and browser behavior.

The October 12, 2026 local work included:

- Reduced the redundant database charset-setting round-trip in shared connection initialization while preserving UTF-8 and the selected connection collation.
- Combined nine independent store-dashboard inventory count/value queries into one query. In a local comparison the returned values matched, and measured average database time was about 10.81 ms separately versus 0.88 ms combined for that probe. This is a local query benchmark, not a production load guarantee.
- Added cache-first handling for versioned static asset URLs in the service worker while keeping dynamic PHP routes network-only.
- Earlier work improved browser-rendered report downloads by returning after a complete PDF is written rather than waiting for slow browser shutdown.

Other operational recommendations:

- Monitor database and Apache/PHP error logs for slow queries and repeated failures.
- Keep the database indexes in the master schema and reviewed migrations.
- Avoid running source scans, large exports, or maintenance scripts during peak use unless capacity is known.
- Test with realistic record volumes before enabling new dashboards or reports.
- Run load tests in a safe test environment before claiming a concurrent-user capacity.

The local application probe observed a healthy database connection and a fast login response at the time measured; this should not be interpreted as a service-level objective.

## 16. Testing and verification

The repository uses PHPUnit 10.5 configuration in `phpunit.xml`.

Run tests from the project root:

```powershell
& 'C:\xampp1\php\php.exe' 'phpunit.phar' tests
```

Check a changed PHP file:

```powershell
& 'C:\xampp1\php\php.exe' -l 'includes\changed_file.php'
```

The full project suite passed locally on October 12, 2026: **89 tests, 426 assertions**. This documents that test run; it does not certify untested user workflows or production behavior.

For every release, verify at minimum:

- PHP syntax and automated tests.
- Database connection and schema compatibility.
- Login, role access, and logout.
- Representative requisition, issue, return, transfer, and maintenance flows.
- Report download and printing.
- Backup and restore on a non-production copy.
- Browser console, PHP error log, Apache error log, and database error log.

## 17. Troubleshooting

### “Service temporarily unavailable” / database cannot connect

1. Confirm MySQL/MariaDB is running.
2. Check `.env` or server environment values for `DB_HOST`, `DB_USER`, `DB_PASS`, and `DB_NAME`.
3. From the PHP/Apache host, verify DNS/routing and the configured database port.
4. For a remote database, check firewall rules, bind/listen settings, and the database user's allowed source host.
5. Inspect the PHP error log for the connection exception; the user-facing error intentionally omits credentials and raw database details.

### Login loops, expired session, or forced password change

- Confirm browser cookies are enabled and that HTTP/HTTPS and hostnames are consistent.
- Check the configured `SITE_URL`, session timeout, server time, and HTTPS proxy configuration.
- Confirm the account is active and not soft-deleted.
- Revoke stale sessions or follow the administrator account recovery process.

### Missing module or action

- Verify the account's role and organizational scope.
- Review configured module permissions and active delegations.
- Confirm the record is in a state accepted by the workflow.
- Check server logs for database/schema errors; a visible button is not proof of authorization or a pending task.

### Missing session-management feature

- Confirm the `user_login_sessions` table exists.
- If needed, apply `database/migrations/add_user_login_sessions.sql` against a verified backup.

### Slow pages or report download

- Measure the page response separately from browser-rendered reports.
- Test database connection/query latency from the web host.
- Inspect expensive queries, record volume, missing indexes, and error logs.
- For Chromium PDF output, confirm `proc_open` is enabled, the browser executable exists, and the PHP user can create/delete temporary files.
- Do not terminate unrelated Edge/Chrome processes to troubleshoot a renderer.

### Service-worker stale assets

- Confirm the browser has the current service-worker script and the expected cache version.
- Reload after deployment and verify the asset URL's version query string changes when content changes.
- Dynamic PHP pages should continue to use network responses.

## 18. Release and change-management checklist

1. Review code and database migrations.
2. Back up production data and uploads.
3. Test the change on a staging copy.
4. Apply schema changes before deploying code that requires them.
5. Deploy code and static assets.
6. Confirm the service worker and asset versions update as expected.
7. Run smoke tests using representative roles.
8. Monitor logs and key workflows after release.
9. Keep a rollback plan that includes both code and schema/data considerations.

## 19. Related project references

- `README.md` — project introduction and existing feature overview.
- `DEPLOY.md` — Ubuntu/LAMP deployment instructions; validate each command against the target server.
- `database/sqlSchemaAll.sql` — consolidated schema and baseline data.
- `database/migrations/` — incremental migration files.
- `SYSTEM_FLOWS.md` — workflow diagrams and supplementary explanations.
- `PROJECT_STRUCTURE.md` — older directory inventory; the live source tree is authoritative.
- `phpunit.xml` and `tests/` — automated test configuration and cases.

Older project documents may contain stale sample credentials, old test counts, file lists, or status claims. Treat the current source, configuration, schema, and approved operational policy as authoritative.

## 20. Document maintenance

Update this document when roles, approval stages, schema, configuration, deployment procedures, integrations, or security controls change. Re-check commands and table names against the current repository before distributing a revised copy. Keep operational secrets and real personal or asset data out of documentation.
