<?php

namespace App\Database;

use App\Database\Migration\CreateMigrationsTable;
use App\Database\Migration\CreateWorkflowHistoryTable;

class MigrationRunner
{
    protected $migrations = [];

    public function __construct()
    {
        // Aquí registras todas tus migraciones
        $this->migrations = [
            CreateMigrationsTable::class,
            CreateWorkflowHistoryTable::class,
            // Puedes agregar más migraciones aquí
            // CreateProductsTable::class,
            // CreateOrdersTable::class,
        ];
    }

    public function run()
    {
        foreach ($this->migrations as $migrationClass) {
            $migration = new $migrationClass();

            if (method_exists($migration, 'up')) {
                $migration->up();
            }
        }
    }

    public function rollback()
    {
        // Opción para hacer rollback
        foreach (array_reverse($this->migrations) as $migrationClass) {
            $migration = new $migrationClass();

            if (method_exists($migration, 'down')) {
                $migration->down();
            }
        }
    }
}
