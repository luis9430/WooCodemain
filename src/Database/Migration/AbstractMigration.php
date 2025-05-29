<?php
namespace App\Database\Migration;

abstract class AbstractMigration
{
    abstract public function up();
    abstract public function down();

    public function getName(): string {
        return static::class;
    }
}
