<?php

defined('ABSPATH') || exit;



/**

 * Insert a row into wp_igny8_data.

 *

 * @param int $post_id

 * @param string $type e.g. 'prompt_log', 'cache', 'input_history'

 * @param array $data Associative array to store as JSON

 * @return int|false Insert ID or false on failure

 */

function igny8_db_insert($post_id, $type, $data) {

    global $wpdb;

    $table = $wpdb->prefix . 'igny8_data';



    return $wpdb->insert($table, [

        'post_id'   => $post_id,

        'data_type' => $type,

        'data'      => wp_json_encode($data),

        'created_at' => current_time('mysql'),

        'updated_at' => current_time('mysql'),

    ]);

}



/**

 * Get records by post_id and data_type.

 *

 * @param int $post_id

 * @param string $type

 * @return array

 */

function igny8_db_get_by_type($post_id, $type) {

    global $wpdb;

    $table = $wpdb->prefix . 'igny8_data';



    $rows = $wpdb->get_results($wpdb->prepare(

        "SELECT * FROM $table WHERE post_id = %d AND data_type = %s ORDER BY updated_at DESC",

        $post_id, $type

    ), ARRAY_A);



    foreach ($rows as &$row) {

        $row['data'] = json_decode($row['data'], true);

    }



    return $rows;

}



/**

 * Delete all Igny8 rows for a post (if needed).

 *

 * @param int $post_id

 * @return int Rows deleted

 */

function igny8_db_delete_post_data($post_id) {

    global $wpdb;

    $table = $wpdb->prefix . 'igny8_data';

    return $wpdb->delete($table, ['post_id' => $post_id]);

}

