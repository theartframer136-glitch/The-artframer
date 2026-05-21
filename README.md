# The Art Framer — WordPress Site

**Live site:** https://theartframer.com  
**Hosting:** Hostinger (WordPress)  
**Deploys:** Push to `main` → auto-deploys to Hostinger via FTP

---

## First-time setup

### 1. Add FTP secrets to GitHub

Go to **GitHub repo → Settings → Secrets and variables → Actions → New repository secret** and add:

| Secret name | Value |
|---|---|
| `FTP_SERVER` | Your Hostinger FTP hostname (e.g. `ftp.theartframer.com`) |
| `FTP_USERNAME` | FTP username from Hostinger hPanel |
| `FTP_PASSWORD` | FTP password from Hostinger hPanel |

> Find these in **Hostinger hPanel → Files → FTP Accounts**.

### 2. Add your WordPress files

Copy your WordPress files into this folder (everything in `public_html/` on Hostinger), then:

```bash
git add .
git commit -m "Initial WordPress site files"
git push origin main
```

The deploy workflow will run automatically and sync files to Hostinger.

---

## What is NOT tracked in git

- `wp-config.php` — contains database credentials, lives only on the server
- `wp-content/uploads/` — media library, managed on the server
- `wp-content/cache/` — generated files

## Branch → environment

| Branch | Environment |
|---|---|
| `main` | Production (theartframer.com) |
