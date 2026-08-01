# Laravel 12 Vite Deployment - Configuration Summary (Updated)

## Overview

This document explains the Vite build and deployment considerations for this project. It avoids hardcoded subfolder paths and recommends serving compiled assets from `public/build/`.

## Key Fixes

1. Ensure Vite outputs a `manifest.json` in `public/build/`.
2. Use a production `base` of `/build/` (or your hosting subpath) in `vite.config.js`.
3. Ensure webserver routes requests to the project's `public/` directory so asset URLs resolve correctly.

## Files Modified / Notes

- `vite.config.js`: set `manifest: 'manifest.json'`, `outDir: 'public/build'`, and configure `base` for production (example `/build/`).
- `package.json`: ensure build script handles any post-build relocation if necessary.
- `public/.htaccess`: ensure asset rewrite rules point to `/build/` (no legacy `/nobar` paths).

## Build Output Structure

```
public/build/
├── manifest.json
├── assets/
└── ...
```

## How It Works on Shared Hosting (example)

1. User visits: `https://your-host/pendataan` (or your configured host path)
2. Webserver routes to the project's `public/` directory
3. `public/index.php` loads Laravel app
4. Laravel's Vite macro finds `/public/build/manifest.json`
5. Manifest maps assets to `/build/assets/`
6. Browser requests `/build/assets/app-xxxxx.js`

## Build Command

```bash
npm run build
```

This executes your build pipeline (e.g., `vite build`) and any post-build scripts you have configured.

## Verification Checklist

- `public/build/manifest.json` exists
- `public/build/assets/` contains compiled CSS/JS
- `vite.config.js` configured for production `base` (/build/ or your chosen path)
- Webserver routes requests to the `public/` directory
- Blade templates use `@vite()` macro

## Deployment Instructions (example)

1. Extract your hosting package to the desired document root (e.g., `public_html/pendataan`)
2. Ensure `public/` is the document root or route requests to it
3. If building on the server: run `npm install` and `npm run build`
4. Application ready at: `https://your-host/pendataan`

## Troubleshooting

If manifest not found after deployment:
1. Verify `public/build/manifest.json` exists
2. If using pre-built assets, ensure the manifest is at `public/build/manifest.json`
3. Check that Blade calls to `@vite()` resolve to the expected manifest location

---

This document no longer references legacy `/nobar` paths. Update hosting-specific values (domain, document root) to match your environment.
