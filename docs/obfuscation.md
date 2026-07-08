# Obfuscation deploy flow

This repository uses two branches:

| Branch | Role |
|--------|------|
| `clean-main` | Readable source of truth. Develop and merge here. |
| `main` | Deploy branch. Contains obfuscated `app/` PHP. Server webhooks pull from `main`. |

## How publish works

1. Push (or merge) to `clean-main`.
2. GitHub Action [`.github/workflows/obfuscate-publish.yml`](../.github/workflows/obfuscate-publish.yml) runs YakPro-po against `app/` using [`yakpro-po.cnf`](../yakpro-po.cnf).
3. The Action force-updates `main` with the same tree as `clean-main`, but with obfuscated `app/`.

Do **not** merge readable `clean-main` into `main` with a normal PR/merge. That would put clean PHP on the deploy branch. Always promote via pushes to `clean-main` and let the Action publish.

## What is obfuscated

- **Yes:** `app/**/*.php` (variables, string literals, control-flow shuffling).
- **No:** class/method/property/namespace names (Laravel + Filament need them), and all other paths (`routes/`, `config/`, views, etc.).

## Local development

```bash
git checkout clean-main
git pull
# work as usual, push to clean-main
```

Point feature-branch PRs at `clean-main`, not `main`.

## Branch protection (recommended)

On GitHub → Settings → Branches:

- Protect **`main`**: block direct pushes from humans; allow GitHub Actions to push (or restrict pushes to the Action bot).
- Optionally protect **`clean-main`** with required PR reviews.

## If a deploy breaks after obfuscation

1. Check the latest “Obfuscate and publish to main” workflow run.
2. Prefer fixing logic on `clean-main` and re-pushing.
3. If needed, loosen [`yakpro-po.cnf`](../yakpro-po.cnf) (e.g. set `shuffle_stmts` / `obfuscate_string_literal` to `false`) and push again.
