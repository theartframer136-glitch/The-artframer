# Deployment Pipeline — theartframer.us

This document describes the GitHub → Hostinger pipeline for theartframer.us,
following the hybrid pattern from `nuviolearning.com` + `indiatutorsonline.com`.

## Architecture

```
Local machine  ──git push──▶  GitHub repo  ──GitHub Actions──▶  Hostinger SSH  ──▶  WordPress
                                  │
                                  └─ secrets: HOSTINGER_SSH_KEY/HOST/PORT/USERNAME
```

## Pipeline overview

Three workflows ship with this repo:

| Workflow | Trigger | Purpose |
|---|---|---|
| `deploy.yml` | Push to `main` or manual | rsync theme → purge LiteSpeed + OPcache |
| `purge-cache.yml` | Manual only | Manual cache purge when site looks stale |
| `set-php-ini.yml` | Manual (dry-run → commit) | Write PHP limits via `.user.ini` |
| `run-task.yml` | Manual (dry-run → commit) | Generic WP-CLI runner template |

## Server-side details

| Item | Value |
|---|---|
| SSH host | `72.61.66.116` |
| SSH port | `65002` |
| SSH user | `u979793747_OPuOsKi4J` |
| WP root | `/home/u979793747/websites/OPuOsKi4J/public_html` (auto-discovered) |
| Site URL | https://theartframer.us |
| Server | `us-bos-h5g-node7.hstgr.io` |
| Theme | `postero-child` (child of `postero`) |

## Required GitHub Secrets

| Name | Value |
|---|---|
| `HOSTINGER_SSH_KEY` | Full Ed25519 private key including `-----BEGIN/END-----` |
| `HOSTINGER_HOST` | `72.61.66.116` |
| `HOSTINGER_PORT` | `65002` |
| `HOSTINGER_USERNAME` | `u979793747_OPuOsKi4J` |

## Making theme changes

1. Edit files under `postero-child/` locally
2. **Bump the `Version:` in `style.css`** if you change CSS/JS (cache-busting)
3. `git commit && git push origin main`
4. Watch the Actions tab — deploy completes in ~30 seconds
5. Hard-refresh the site (Ctrl+Shift+R) to bypass browser cache

## Cache layers (in order, all real)

1. **Browser cache** — `?ver=` query string busts via `Version:` bump
2. **LiteSpeed page + object cache** — purged automatically by deploy
3. **PHP OPcache** — reset automatically via HTTP trigger after deploy
4. **Hostinger hcdn edge CDN** — manual purge via hPanel if needed

## Verifying a deploy

```bash
# Check deployed version landed
curl -s https://theartframer.us/wp-content/themes/postero-child/style.css | grep Version

# Check cache headers
curl -sIL https://theartframer.us/ | grep -iE "x-litespeed-cache|x-hcdn-cache-status|age:"

# Bypass cache to see fresh render
curl -s "https://theartframer.us/?cb=$(date +%s)" | head -50
```

## Common server-side tasks (via Actions tab → manual triggers)

- **Cache stuck?** → run `Purge Caches (manual)`
- **Elementor 500 errors?** → run `Set PHP Limits (.user.ini)` (dry-run first)
- **Custom WP-CLI command?** → edit `run-task.yml`, commit, run from Actions tab

## Known issues

### SSH key not persisting (resolved by Hostinger support)

Initial setup on 2026-05-22 hit a Hostinger panel bug where adding an SSH key
via Advanced → Remote access → SSH/SFTP keys showed the key as added but it
wasn't actually written to `authorized_keys` on the server. Resolved by
contacting Hostinger support and having them manually add the key.

## See also

- `ONBOARDING.md` (in the repo owner's other repos) — the original setup guide
  distilled from nuviolearning + indiatutorsonline
- `README.md` — quick reference
