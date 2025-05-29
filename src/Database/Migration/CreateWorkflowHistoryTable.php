<?php
namespace App\Database\Migration;

class CreateWorkflowHistoryTable extends AbstractMigration
{
    public function up()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'workflow_history';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            object_id bigint(20) NOT NULL,
            object_type varchar(100) NOT NULL,
            workflow varchar(100) NOT NULL,
            transition varchar(100) NOT NULL,
            from_state varchar(100) NOT NULL,
            to_state varchar(100) NOT NULL,
            user_id bigint(20) NULL,
            metadata text NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY object_id (object_id),
            KEY workflow (workflow)
        ) $charset_collate;";

     require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    
    }

    public function down()
    {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}workflow_history");
    }
}
