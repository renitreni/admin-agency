# Obfuscation deploy flow

This repository uses two branches:

| Branch | Role |
|--------|------|
| `clean-main` | Readable source of truth. Develop and merge here. |
| `main` | Deploy branch. Contains selective obfuscation. Server webhooks pull from `main`. |

## How publish works

1. Push (or merge) to `clean-main`.
2. GitHub Action [`.github/workflows/obfuscate-publish.yml`](../.github/workflows/obfuscate-publish.yml) runs:
   - **YakPro-po** against `app/Models/` and `app/Filament/Resources/` using [`yakpro-po.cnf`](../yakpro-po.cnf).
   - **[`bin/obfuscate-blade.php`](../bin/obfuscate-blade.php)** against `resources/views/` (comment strip + whitespace minify; YakPro cannot safely parse Blade).
3. The Action force-updates `main` with the same tree as `clean-main`, overlaying only those three areas.

Do **not** merge readable `clean-main` into `main` with a normal PR/merge. That would put clean PHP on the deploy branch. Always promote via pushes to `clean-main` and let the Action publish.

## What is obfuscated

| Path | Tool | Treatment |
|------|------|-----------|
| `app/Models/**/*.php` | YakPro-po | Variables, string literals, control-flow shuffling |
| `app/Filament/Resources/**/*.php` | YakPro-po | Same as models |
| `resources/views/**/*.blade.php` | `bin/obfuscate-blade.php` | Strip Blade/HTML comments; collapse whitespace |

**Not obfuscated** (left readable on `main`):

- Other `app/` PHP (`Providers`, `Services`, `Http`, `Filament/Pages`, `Filament/Tables`, etc.)
- `routes/`, `config/`, `database/`, assets, etc.
- Class / method / property / namespace names (Laravel + Filament need them)

## Local development

```bash
git checkout clean-main
git pull
# work as usual, push to clean-main
```

Point feature-branch PRs at `clean-main`, not `main`.

### Dry-run locally

```bash
# YakPro (clone once next to the repo or wherever you prefer)
php yakpro-po/yakpro-po.php --config-file yakpro-po.cnf -y app/Models -o /tmp/obf-models
php yakpro-po/yakpro-po.php --config-file yakpro-po.cnf -y app/Filament/Resources -o /tmp/obf-resources

# Blade minify
php bin/obfuscate-blade.php resources/views /tmp/obf-views
```

## Branch protection (recommended)

On GitHub → Settings → Branches:

- Protect **`main`**: block direct pushes from humans; allow GitHub Actions to push (or restrict pushes to the Action bot).
- Optionally protect **`clean-main`** with required PR reviews.

## If a deploy breaks after obfuscation

1. Check the latest “Obfuscate and publish to main” workflow run.
2. Prefer fixing logic on `clean-main` and re-pushing.
3. If Models/Resources break, loosen [`yakpro-po.cnf`](../yakpro-po.cnf) (e.g. set `shuffle_stmts` / `obfuscate_string_literal` to `false`) and push again.
4. If a Blade view breaks, compare `resources/views/...` on `clean-main` vs `main`; adjust [`bin/obfuscate-blade.php`](../bin/obfuscate-blade.php) protection patterns if needed.
