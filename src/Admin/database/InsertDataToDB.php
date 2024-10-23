<?php

function insertDataToDB($data)
{
    global $wpdb;

    // Insert the meta data
    $result = $wpdb->insert(
        $wpdb->prefix."woocommerce_daftra_log", // The table to insert into
        array(
            'type'    => $data['type'], // The ID of the post (product)
            'system_id'   => $data['system_id'], // The meta key (name)
            'woocoomerce_id' => $data['woocommerce_id'], // The meta value
        ),
        array(
            '%s',   // Data format for post_id (integer)
            '%d',   // Data format for meta_key (string)
            '%d'    // Data format for meta_value (string)
        )
    );

    // Check if the insert was successful
    if ($result === false) {
        // Handle error
        return new WP_Error('insert_error', __('Could not insert data.'));
    }

    return $result;
}

function getByEntity($entity, $fields = [])
{
    global $wpdb;
    $fields = empty($fields) ? '*' : implode(',', $fields);

    // Prepare the SQL query
    $table_name = $wpdb->prefix."woocommerce_daftra_log";
    $query = $wpdb->prepare(
        "SELECT $fields FROM $table_name WHERE type = %s",
        $entity
    );

    // Execute the query and get results
    $results = $wpdb->get_results($query, ARRAY_A);

    // Check if results are found
    if (empty($results)) {
        return array(); // Return an empty array if no records found
    }

    return $results; // Return the fetched records
}

function filterEntityBy($entity, $value)
{
    global $wpdb;

    // Prepare the SQL query
    $table_name = $wpdb->prefix."woocommerce_daftra_log";
    $query = $wpdb->prepare(
        "SELECT * FROM `$table_name` WHERE `type` = %s AND `woocoomerce_id` = %s LIMIT 1",
        $entity,
        $value
    );

    // Execute the query and get results
    $results = $wpdb->get_results($query, ARRAY_A);

    // Check if results are found
    if (empty($results)) {
        return array(); // Return an empty array if no records found
    }

    return $results; // Return the fetched records

}