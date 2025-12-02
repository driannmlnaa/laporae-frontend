<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {

        if (app()->runningInConsole()) {
            $this->createDatabaseIfNotExists();
        }
        Schema::defaultStringLength(191);
    }

    protected function createDatabaseIfNotExists(): void
    {
        $config = config('database.connections.mysql');
        $database = $config['database'] ?? null;

        if (! $database) {
            return;
        }

        try {
            $pdo = new \PDO(
                "mysql:host={$config['host']};port={$config['port']}",
                $config['username'],
                $config['password']
            );

            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
