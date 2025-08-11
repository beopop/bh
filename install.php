#!/usr/bin/env php
<?php

declare(strict_types=1);

function run(string $command): void
{
    echo "Running: {$command}\n";
    passthru($command, $status);
    if ($status !== 0) {
        fwrite(STDERR, "Command failed with exit code {$status}: {$command}\n");
        exit($status);
    }
}

function prompt(string $question, string $default = ''): string
{
    $prompt = $default === '' ? $question . ': ' : sprintf('%s [%s]: ', $question, $default);
    $answer = trim((string) readline($prompt));
    return $answer === '' ? $default : $answer;
}

function updateEnv(array $values): void
{
    $envPath = '.env';
    $contents = file_exists($envPath) ? file_get_contents($envPath) : '';

    foreach ($values as $key => $value) {
        $pattern = '/^' . preg_quote($key, '/') . '=.*/m';
        $replacement = $key . '=' . $value;
        if (preg_match($pattern, $contents)) {
            $contents = preg_replace($pattern, $replacement, $contents);
        } else {
            $contents .= PHP_EOL . $replacement;
        }
    }

    file_put_contents($envPath, $contents);
}

if (!file_exists('.env') && file_exists('.env.example')) {
    if (!copy('.env.example', '.env')) {
        fwrite(STDERR, "Failed to create .env file\n");
        exit(1);
    }
    echo ".env file created from .env.example\n";
}

$config = [];
$config['APP_NAME'] = prompt('Application name', 'Laravel');
$config['APP_URL'] = prompt('Application URL', 'http://localhost');
$config['DB_HOST'] = prompt('Database host', '127.0.0.1');
$config['DB_PORT'] = prompt('Database port', '3306');
$config['DB_DATABASE'] = prompt('Database name', 'laravel');
$config['DB_USERNAME'] = prompt('Database user', 'root');
$config['DB_PASSWORD'] = prompt('Database password');

updateEnv($config);

run('composer install --no-interaction --prefer-dist');
run('php artisan key:generate --ansi');

try {
    $dsn = sprintf('mysql:host=%s;port=%s', $config['DB_HOST'], $config['DB_PORT']);
    $pdo = new PDO($dsn, $config['DB_USERNAME'], $config['DB_PASSWORD']);
    $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . $config['DB_DATABASE'] . '`');
    echo "Database {$config['DB_DATABASE']} created or already exists.\n";
} catch (PDOException $e) {
    fwrite(STDERR, 'Database creation failed: ' . $e->getMessage() . "\n");
    exit(1);
}

run('php artisan migrate --force');

echo "Installation complete.\n";
