# CRM Installer

Minimal Laravel 11 skeleton prepared for shared hosting on LiteSpeed/cPanel.

## Deployment on cPanel + LiteSpeed

1. Upload the project files to your cPanel account, usually under `~/`.
2. Run `composer install` from the terminal or cPanel's Composer feature.
3. Copy `.env.example` to `.env` and fill in database and SMTP credentials.
4. Generate the application key with `php artisan key:generate`.
5. Point the domain's document root to `public_html/public`.
6. Ensure `storage` and `bootstrap/cache` directories are writable.
7. Run database migrations and seeds: `php artisan migrate --seed`.
8. Optionally set up a cron job for the scheduler: `* * * * * php /home/USER/public_html/artisan schedule:run`.
