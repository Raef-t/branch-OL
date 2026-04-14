# Migration Consolidation Guide

Use this when the same table is spread across many migration files in:
- `database/migrations`
- `Modules/*/database/migrations`

The command `migrations:consolidate` merges `up()` statements per table in chronological order, so newer edits are applied last.

## 1) Preview only (recommended first)

```bash
php artisan migrations:consolidate
```

This prints a table summary and does not write files.

## 2) Generate one consolidated migration file per table

```bash
php artisan migrations:consolidate --write
```

Generated files go to:
- `database/migrations_consolidated`

## 3) Limit consolidation to specific tables

```bash
php artisan migrations:consolidate --table=students --table=users --write
```

## 4) Optional: archive old migration files

```bash
php artisan migrations:consolidate --write --archive
```

Old files are moved to:
- `database/migrations_archive/...`

## Important Notes

- This is intended for schema cleanup and fresh migration flows.
- If your database already contains a migration history, plan the transition carefully.
- To make consolidated files the active source for fresh environments, either:
  - run migrate with `--path=database/migrations_consolidated`, or
  - generate into your active migration path and archive old files.
