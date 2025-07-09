<?php

defined('WP_UNINSTALL_PLUGIN') || exit;



global $wpdb;



// Drop custom Igny8 table

$table_name = $wpdb->prefix . 'igny8_data';

$wpdb->query("DROP TABLE IF EXISTS $table_name");



// Delete all plugin-specific options

$options = [

    'igny8_api_key',

    'igny8_model',

    'igny8_use_moderation',

    'igny8_input_scope',

    'igny8_detection_prompt',

    'igny8_content_length',

    'igny8_rewrite_prompt',

    'igny8_teaser_text',

    'igny8_fixed_fields_config',

];



foreach ($options as $option) {

    delete_option($option);

}

