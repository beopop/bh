<?php
$root = realpath(__DIR__.'/..');
$installedFlag = $root.'/storage/installed';

if (file_exists($installedFlag)) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $envPath = $root.'/.env';
    if (!file_exists($envPath)) {
        copy($root.'/.env.example', $envPath);
    }
    $env = file_get_contents($envPath);
    $replacements = [
        'DB_HOST' => $_POST['db_host'] ?? '127.0.0.1',
        'DB_DATABASE' => $_POST['db_name'] ?? '',
        'DB_USERNAME' => $_POST['db_user'] ?? '',
        'DB_PASSWORD' => $_POST['db_pass'] ?? '',
        'MAIL_HOST' => $_POST['mail_host'] ?? '',
        'MAIL_PORT' => $_POST['mail_port'] ?? '',
        'MAIL_USERNAME' => $_POST['mail_user'] ?? '',
        'MAIL_PASSWORD' => $_POST['mail_pass'] ?? '',
        'MAIL_ENCRYPTION' => $_POST['mail_encryption'] ?? ''
    ];
    foreach ($replacements as $key => $value) {
        $env = preg_replace("/^{$key}=.*$/m", $key.'='.$value, $env);
    }
    file_put_contents($envPath, $env);

    chdir($root);
    $commands = [
        'composer install --no-interaction --prefer-dist',
        'php artisan key:generate',
        'php artisan migrate --seed'
    ];
    foreach ($commands as $cmd) {
        echo '<pre>' . htmlspecialchars($cmd) . "\n";
        passthru($cmd, $status);
        echo '</pre>';
        if ($status !== 0) {
            die('Command failed: '.$cmd);
        }
    }

    file_put_contents($installedFlag, 'installed');
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Installation</title>
</head>
<body>
    <h1>Application Setup</h1>
    <form method="post">
        <h2>Database</h2>
        <label>Host <input type="text" name="db_host" value="127.0.0.1"></label><br>
        <label>Name <input type="text" name="db_name"></label><br>
        <label>User <input type="text" name="db_user"></label><br>
        <label>Password <input type="password" name="db_pass"></label><br>
        <h2>Mail</h2>
        <label>Host <input type="text" name="mail_host"></label><br>
        <label>Port <input type="text" name="mail_port" value="587"></label><br>
        <label>Username <input type="text" name="mail_user"></label><br>
        <label>Password <input type="password" name="mail_pass"></label><br>
        <label>Encryption <input type="text" name="mail_encryption" value="tls"></label><br>
        <button type="submit">Install</button>
    </form>
</body>
</html>
