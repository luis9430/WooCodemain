<?php
namespace App\Database\Migration;

use App\Database\Migration\AbstractMigration;

class CreateMigrationsTable extends AbstractMigration
{
    public function up() {
        global $wpdb;

        $table = $wpdb->prefix . 'migrations';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            migrated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function down() {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}migrations");
    }
}
