# The Art Framer — WordPress Site

**Live site:** https://theartframer.us  
**Hosting:** Hostinger Business/Cloud (managed WordPress, server `us-bos-h5g-node7.hstgr.io`)  
**Deploy pipeline:** Push to `main` → GitHub Actions rsync over SSH → Hostinger → LiteSpeed + OPcache purge

Pattern follows the **hybrid approach** from `nuviolearning.com` + `indiatutorsonline.com`:
4 secrets, path auto-discovery, dual cache reset, `set -euo pipefail`.

---

## Repository layout

```
The-artframer/
├── .github/
│   ├── hostinger_deploy(.pub)   # SSH keypair (gitignored)
│   └── workflows/
│       └── deploy.yml           # Push-to-deploy + cache reset
├── postero-child/               # The active WordPress child theme
│   ├── style.css                # Child theme header (Template: postero, Version: 1.2.5)
│   ├── functions.php            # Parent+child enqueue, custom asset loader
│   ├── assets/
│   │   ├── css/custom.css       # Site-specific CSS (cache-busted via filemtime)
│   │   └── js/                  # Site-specific JS (drop custom.js here)
│   └── inc/                     # PHP modules
├── .gitignore
└── README.md
```

---

## Required GitHub Actions secrets

| Secret | Value |
|---|---|
| `HOSTINGER_SSH_KEY` | Full contents of the Ed25519 private key (incl. `-----BEGIN/END-----`) |
| `HOSTINGER_HOST` | `72.61.66.116` |
| `HOSTINGER_PORT` | `65002` |
| `HOSTINGER_USERNAME` | `u979793747_OPuOsKi4J` |

---

## Server paths

| Item | Value |
|---|---|
| SSH host | `72.61.66.116` |
| SSH port | `65002` |
| SSH user | `u979793747_OPuOsKi4J` |
| WP root (expected) | `/home/u979793747/websites/OPuOsKi4J/public_html` |
| Theme target | `<WP_ROOT>/wp-content/themes/postero-child/` |

The workflow probes 3 candidate WP root paths and picks the first writable one,
so the exact path doesn't need to be hardcoded.

---

## Making changes

1. Edit files under `postero-child/` locally
2. **Bump `Version:` in `style.css`** when you change CSS/JS (cache-busting)
3. `git add . && git commit -m "describe change" && git push`
4. GitHub Actions deploys + purges LiteSpeed page cache + resets OPcache
5. Hard-refresh the site (Ctrl+Shift+R) to bypass browser cache

---

## ⚠️ Known issue (as of 2026-05-22)

Hostinger's per-site SSH/SFTP key panel for theartframer.us is **not persisting
keys to `authorized_keys`** on the server. SSH key auth fails with
`Permission denied (publickey)` even after the panel shows the key as added.

**Fingerprint that should be in authorized_keys:** `SHA256:KJ7BZalVWKZIhHThS4KkGNQt8s6RdlQvPUQHT08Ipjk`

**Resolution path:** Hostinger support has been asked to manually add the key.
Once they do, `git push` deploys correctly. The pipeline itself is verified
working — same pattern as nuviolearning.com / indiatutorsonline.com.

---

## Cache layers (all real)

1. **Browser cache** — `?ver=` query string handles this (bump theme Version)
2. **LiteSpeed page cache** — purged by deploy workflow via WP-CLI
3. **PHP OPcache** — reset by deploy workflow via HTTP-triggered temp script
4. **Hostinger hcdn edge CDN** — manual purge via hPanel if HTML still stale
