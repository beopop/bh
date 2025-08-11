# CRM Installer

Minimal Laravel 11 skeleton prepared for shared hosting on LiteSpeed/cPanel.

## Deployment on cPanel + LiteSpeed

1. Upload the project files to your cPanel account, usually under `~/`.
2. Run `php install.php` to install Composer dependencies and generate the application key.
3. Copy `.env.example` to `.env` (the installer creates it if missing) and fill in database and SMTP credentials.
4. Point the domain's document root to `public_html/public`.
5. Ensure `storage` and `bootstrap/cache` directories are writable.
6. Run database migrations and seeds: `php artisan migrate --seed`.
7. Optionally set up a cron job for the scheduler: `* * * * * php /home/USER/public_html/artisan schedule:run`.
