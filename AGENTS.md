# AGENTS.md — NicerApp-WebOS-6.0.z

Guidance for AI coding agents working on this repository.

## Project overview

NicerApp WebOS is a browser-based 2D (+ evolving 3D) WebOS / CMS. Pages use photo, tiled image, YouTube/iframe, WebGL or canvas backgrounds with semi-transparent content and apps layered on top. Server-side stack is PHP on Linux; primary data store is Apache CouchDB (JSON documents). Optional SQL (MySQL/PostgreSQL via ADOdb patterns) exists but CouchDB is the default.

Current version (see `code/NicerAppWebOS/version.json`): **v6.0.0-beta-5.0.0**.

Live site: https://nicer.app  
Repo: https://github.com/rene-ajm-veerman/NicerApp-WebOS-6.0.z

Guiding principle: **never expose database servers directly to browsers**. All data access is browser → jQuery AJAX → specific PHP endpoints → DB libraries → response (SUCCESS / FAILED / data only in non-debug mode).

## Architecture

### Request flow
1. Web server (Apache behind optional Nginx TLS terminator) serves the document root.
2. `boot.php` loads domain settings, starts session, wires error handling, loads DB connectors (CouchDB via Sag + uDB abstraction), instantiates global `$naWebOS`.
3. `index.php` / app entry points and AJAX scripts under `businessLogic/ajax/` and app folders handle the request.
4. Client JS (site-*.js, theme editor, app modules) talks only to PHP AJAX endpoints.

### Key runtime objects
- `$naWebOS` — main application object (`businessLogic/class.NicerAppWebOS.php`), constructed in two phases (`new` + `__construct2()`).
- Domain settings from `domains/<domain>/domainConfig/settings.json` (and `naLAN.json`).
- CouchDB connections via `businessLogic/databases/uDB-2.0.0/` plugins and Sag.
- Session holds CouchDB auth cookie / login name when present.

### Directory map (important paths)

```
/
├── README.md
├── 3rd-party/                    # git submodule
├── domains/                      # per-domain config & content roots
├── siteMedia/                    # backgrounds & media (large assets often external)
└── code/
    └── NicerAppWebOS/
        ├── boot.php              # bootstrap — do not change casually
        ├── index.php
        ├── functions.php
        ├── version.json
        ├── COMPATIBILITY.txt
        ├── apps/
        │   └── NicerAppWebOS/    # applications, userInterfaces, APIs
        ├── businessLogic/
        │   ├── class.NicerAppWebOS.php
        │   ├── class.NicerAppWebOS.errorHandler.php
        │   ├── class.NicerAppWebOS.log.php
        │   ├── class.screenshots.php
        │   ├── ajax/             # AJAX endpoints
        │   ├── databases/uDB-2.0.0/   # DB API + CouchDB plugins + Sag
        │   ├── composer.json     # crawler-detect, predis
        │   ├── package.json      # puppeteer for screenshots
        │   └── nodeScreenshot.js
        ├── scripts.install/      # install helpers, latest.sh
        ├── scripts.maintenance/  # workers, backups, htaccess builders
        ├── themes/
        └── site-6.*.js / na.*.js
```

Document root in production is typically something like `/var/www/NicerAppWebOS-v6.y.z` (or the path recorded in domain `settings.json` `phpServers[0].rootPath` + `relativePathUnderRootPath`).

## Setup, build, run, test

There is **no traditional compile step**. “Build” = install system packages, CouchDB, Composer/npm deps, Apache/Nginx config, permissions, and domain settings.

### Fresh install (Linux, recommended)

From the project README / `scripts.install/latest.sh` pattern:

```bash
apt update
apt install -y curl
curl https://nicer.app/NicerAppWebOS/scripts.install/latest.sh | /usr/bin/sh
```

`latest.sh` (repo copy) roughly:
- upgrades packages
- installs Apache, PHP modules, Composer, ImageMagick, npm, WordNet, etc.
- enables `headers` + `rewrite`
- adds CouchDB apt repo and installs CouchDB
- creates `/var/www/NicerAppWebOS-v6.y.z` and unpacks a release zip

Re-install / refresh after previous install:

```bash
cd ~/Downloads
rm -rf /var/www/NicerAppWebOS-v6.y.z   # careful
curl https://nicer.app/NicerAppWebOS/scripts.install/latest.sh | /usr/bin/sh
```

### PHP / Node dependencies (inside businessLogic)

```bash
cd code/NicerAppWebOS/businessLogic
composer install
npm install
```

Composer requires: `jaybizzle/crawler-detect`, `predis/predis`.  
npm requires: `puppeteer`, `puppeteer-extra`, `puppeteer-extra-plugin-stealth` (used by screenshot workers).

### Apache (local / LAN)

Example vhost pattern from README (adjust DocumentRoot and ServerAdmin):

```apache
<VirtualHost *:80>
    ServerName localhost
    ServerAdmin you@example.com
    DocumentRoot /var/www/NicerAppWebOS-v6.y.z
    <Directory /var/www/NicerAppWebOS-v6.y.z>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

```bash
sudo a2ensite 001-localhost.conf   # or your site file
sudo service apache2 restart
```

Set permissions (path names in README still mention older trees; use the actual install path):

```bash
chmod a+x /path/to/setPermissions.sh   # if present in the tree
/path/to/setPermissions.sh
```

### HTTPS / multi-domain

Nginx in front of Apache on private LAN ports is the documented production pattern (see README for full `nicer.app` / `said.by` examples and Let’s Encrypt cert paths). Do **not** expose CouchDB (default 5984) publicly; PHP on the LAN should talk to it.

CouchDB auth cookie timeout (example from README):

```bash
curl http://admin:PASSWORD@HOST:5984/_node/_local/_config/couch_httpd_auth/timeout -X PUT -d '"604800"'
```

### Background media

Backgrounds live under siteMedia paths such as:

- `.../NicerAppWebOS-siteMedia/backgrounds/landscape`
- `.../backgrounds/tiled`
- `.../backgrounds/iframe/youtube` (`.txt` files containing a single YouTube URL)

They are largely **not** in the git distribution (size).

### Maintenance / workers

Example screenshot queue worker (`scripts.maintenance/processScreenshotQueue.php`):

```php
// loads boot.php + class.screenshots.php, creates indexes,
// enqueues from comments, runs worker with maxJobs / sleepSeconds
```

Run via CLI under the web user after boot can resolve domain settings (set `NA_DOMAIN` if needed).

### “Test”

There is no formal PHPUnit / Jest suite in the tree. Practical checks:

1. Open the site in Chrome after install (`http://localhost` or the configured domain).
2. Login AJAX path (`businessLogic/ajax/ajax_login.php`) and session cookies.
3. Background switching / taskbar UI.
4. Screenshot worker if you use comments screenshots.
5. Confirm CouchDB is reachable only from PHP, not from the public internet.

Debug flags in `boot.php` (`$naDebugStartup`, `$naDebugThemeLoading`, `$naDebugAll`) control error display and verbosity.

## Conventions

- **Language stack**: PHP (server), JavaScript (client + some Node for Puppeteer), CSS, HTML. JSON for config and CouchDB documents.
- **Entry / bootstrap**: Prefer requiring `boot.php` (or the established relative path to it) from AJAX and CLI scripts so `$naWebOS`, sessions, and DB connectors exist.
- **AJAX contract**: Prefer SUCCESS/FAILED (or structured data) responses; keep error handling and sanitization on the PHP side.
- **DB**: Prefer CouchDB through the existing uDB / Sag layer. Do not add direct browser-to-CouchDB calls.
- **Domain isolation**: Domain-specific settings live under `domains/<name>/domainConfig/`. Do not hard-code production hostnames in shared core without going through settings.
- **Permissions**: Production files tend toward owner/group patterns like `www-data` and restrictive modes (see globals in `boot.php`). Respect existing permission helpers.
- **Versioning**: Site JS files are versioned in the filename (`site-6.0.0-beta-*.js`, etc.). Keep version.json and those filenames consistent when bumping.
- **Licensing**: Core is open-source oriented; commercial license text exists but is marked obsolete-for-now. Follow LICENSE files in `code/NicerAppWebOS/`.
- **boot.php**: Explicitly restricted — changes should go through the project owner (see header comment in the file).

## Compatibility

Documented OS targets (`COMPATIBILITY.txt`):

- Ubuntu 20.04
- Kubuntu 20.04 / 22.04 LTS / 24.04 LTS

Browsers: Chrome-focused (Windows / Kubuntu) per `version.json`.

## Notes for agents

- Prefer reading `boot.php`, `class.NicerAppWebOS.php`, and a representative AJAX script before large refactors.
- Domain `settings.json` paths drive `rootPath_na`; broken settings break almost everything.
- Large media and some third-party trees may be incomplete in a bare clone; use the official install path / nicer.app downloads for a runnable tree.
- When adding features, keep the AJAX → PHP → DB isolation and avoid introducing new direct DB exposure.
```