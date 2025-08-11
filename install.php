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

if (!file_exists('.env') && file_exists('.env.example')) {
    if (!copy('.env.example', '.env')) {
        fwrite(STDERR, "Failed to create .env file\n");
        exit(1);
    }
    echo ".env file created from .env.example\n";
}

run('composer install --no-interaction --prefer-dist');
run('php artisan key:generate --ansi');

echo "Installation complete.\n";
