# EFPT_AMS — Agent Guidance

## Linting & Testing

### PHP Syntax Check
```bash
php -l <file.php>
```
Or check all PHP files:
```bash
get-childitem -recurse -filter "*.php" | % { php -l $_.FullName }
```

### PHP_CodeSniffer (if installed)
```bash
phpcs --standard=PSR12 --extensions=php includes/ store/ shared/ admin/
```

### PHPUnit Tests (if installed)
```bash
vendor/bin/phpunit tests/
# or
php vendor/bin/phpunit tests/
```

### Database
- Schema: `database/sqlSchemaAll.sql`
- Migrations: `database/migrations/`
- Migration runner: `php scripts/run_migration_efp.php` (if exists)
- `ENABLE_SCHEMA_MIGRATIONS` is set to `false` in `config.php` — toggle to `true` only during development

### Environment
- PHP binary location: `C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe`
- Project root: `C:\laragon\www\EFPT_AMS`
- DB: MySQL via Laragon (host: localhost, user: root, db: efpt_ams)

## Project Conventions
- Procedural PHP, no framework
- Use `db_query()` / `db_execute()` for all DB operations (prepared statements)
- Use `esc()` for all HTML output
- Use `csrf_field()` in forms, `csrf_verify()` in POST handlers
- Role-based access: `has_role('role_name')`, `require_login()`
- Asset type: `'fixed'` or `'current'`/`'consumable'`
- Serial numbers are optional for current assets, typically present for fixed assets
