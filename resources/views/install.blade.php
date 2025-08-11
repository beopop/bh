<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Installer</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <h1>Installation Wizard</h1>
    @if($missing)
        <div class="alert">
            Missing PHP extensions: {{ implode(', ', $missing) }}
        </div>
    @endif
    <form method="POST" action="{{ url('/install') }}">
        @csrf
        <h2>Database</h2>
        <input type="text" name="db_host" placeholder="DB Host" value="{{ old('db_host', '127.0.0.1') }}" required>
        <input type="text" name="db_port" placeholder="DB Port" value="{{ old('db_port', '3306') }}" required>
        <input type="text" name="db_database" placeholder="DB Name" value="{{ old('db_database') }}" required>
        <input type="text" name="db_username" placeholder="DB Username" value="{{ old('db_username') }}" required>
        <input type="password" name="db_password" placeholder="DB Password">
        <h2>SMTP</h2>
        <input type="text" name="smtp_host" placeholder="SMTP Host" value="{{ old('smtp_host') }}" required>
        <input type="text" name="smtp_port" placeholder="SMTP Port" value="{{ old('smtp_port', '587') }}" required>
        <input type="text" name="smtp_username" placeholder="SMTP Username" value="{{ old('smtp_username') }}" required>
        <input type="password" name="smtp_password" placeholder="SMTP Password" required>
        <h2>Admin User</h2>
        <input type="email" name="admin_email" placeholder="Admin Email" value="{{ old('admin_email') }}" required>
        <input type="password" name="admin_password" placeholder="Admin Password" required>
        <button type="submit">Install</button>
    </form>
</body>
</html>
