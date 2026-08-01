# Shared Hosting Deployment

Upload the contents of the deployment package to public_html/pendataan (or the desired document root).

Required:
- Ensure public/index.php resolves correctly from the subfolder root.
- Ensure public/build/manifest.json exists.
- Ensure public/.htaccess rewrites requests into public/.
- Keep .env configured for the live URL.
