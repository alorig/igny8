<?php

defined('ABSPATH') || exit;



function igny8_install() {

    global $wpdb;

    $table_name = $wpdb->prefix . 'igny8_data';

    $charset_collate = $wpdb->get_charset_collate();



    $sql = "CREATE TABLE $table_name (

        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

        post_id BIGINT UNSIGNED NOT NULL,

        data_type VARCHAR(50) NOT NULL,

        data JSON NOT NULL,

        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

        INDEX (post_id),

        INDEX (data_type)

    ) $charset_collate;";



    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta($sql);

}

