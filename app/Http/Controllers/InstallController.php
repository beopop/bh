<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class InstallController extends Controller
{
    public function show()
    {
        if (file_exists(storage_path('installed.lock'))) {
            return redirect('/login');
        }

        $extensions = ['pdo', 'openssl', 'mbstring', 'curl'];
        $missing = array_filter($extensions, fn ($ext) => !extension_loaded($ext));

        return view('install', [
            'missing' => $missing,
        ]);
    }

    public function store(Request $request)
    {
        if (file_exists(storage_path('installed.lock'))) {
            return redirect('/login');
        }

        $data = $request->validate([
            'db_host' => 'required',
            'db_port' => 'required',
            'db_database' => 'required',
            'db_username' => 'required',
            'db_password' => 'nullable',
            'smtp_host' => 'required',
            'smtp_port' => 'required',
            'smtp_username' => 'required',
            'smtp_password' => 'required',
            'admin_email' => 'required|email',
            'admin_password' => 'required|min:6',
        ]);

        $env = [
            'APP_NAME' => 'Laravel',
            'APP_ENV' => 'production',
            'APP_KEY' => 'base64:' . base64_encode(random_bytes(32)),
            'APP_DEBUG' => 'false',
            'APP_URL' => url('/'),
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $data['db_host'],
            'DB_PORT' => $data['db_port'],
            'DB_DATABASE' => $data['db_database'],
            'DB_USERNAME' => $data['db_username'],
            'DB_PASSWORD' => $data['db_password'],
            'MAIL_MAILER' => 'smtp',
            'MAIL_HOST' => $data['smtp_host'],
            'MAIL_PORT' => $data['smtp_port'],
            'MAIL_USERNAME' => $data['smtp_username'],
            'MAIL_PASSWORD' => $data['smtp_password'],
        ];

        $envContent = '';
        foreach ($env as $key => $value) {
            $envContent .= $key . '="' . $value . ""\n";
        }

        file_put_contents(base_path('.env'), $envContent);
        Artisan::call('config:clear');

        Config::set('database.connections.mysql.host', $data['db_host']);
        Config::set('database.connections.mysql.port', $data['db_port']);
        Config::set('database.connections.mysql.database', $data['db_database']);
        Config::set('database.connections.mysql.username', $data['db_username']);
        Config::set('database.connections.mysql.password', $data['db_password']);

        Config::set('mail.mailers.smtp.host', $data['smtp_host']);
        Config::set('mail.mailers.smtp.port', $data['smtp_port']);
        Config::set('mail.mailers.smtp.username', $data['smtp_username']);
        Config::set('mail.mailers.smtp.password', $data['smtp_password']);

        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            return back()->withErrors(['db' => $e->getMessage()]);
        }

        try {
            Mail::raw('Installation test', function ($message) use ($data) {
                $message->to($data['admin_email']);
            });
        } catch (\Exception $e) {
            return back()->withErrors(['smtp' => $e->getMessage()]);
        }

        Artisan::call('migrate', ['--seed' => true]);

        User::create([
            'name' => 'Admin',
            'email' => $data['admin_email'],
            'password' => Hash::make($data['admin_password']),
            'is_admin' => true,
        ]);

        touch(storage_path('installed.lock'));

        return redirect('/login');
    }
}
