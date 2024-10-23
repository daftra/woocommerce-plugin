<?php
function woocommerce_order_sync_activate() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'woocommerce_daftra_log';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            type varchar(255) NULL,
            system_id varchar(255) NULL,
            woocoomerce_id varchar(255) NULL,
            created timestamp DEFAULT CURRENT_TIMESTAMP,
            modified timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}