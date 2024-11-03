<?php

use Automattic\WooCommerce\Client;

function create_woocommerce_category($categoryData) {
    $consumer_key = API_KEY; // Replace with your Consumer Key
    $consumer_secret = API_SECRET; // Replace with your Consumer Secret
    $store_url = get_site_url(); // Your store URL

    $woocommerce = new Client(
        $store_url,
        $consumer_key,
        $consumer_secret,
        [
            'version' => 'wc/v3',
        ]
    );

    $data = [
        'name' => $categoryData['name'],
        'slug' => "category-".$categoryData['id'],
        'image' => []
    ];

    try {
        $response =  $woocommerce->post('products/categories', $data);
        $data = ['type' => 'category', 'woocommerce_id' => $response->id, 'system_id' => $categoryData['id']];
        insertDataToDB($data);
        return $response;
    } catch (Exception $e) {
        echo 'Error: ' . esc_html($e->getMessage());
    }
}

function create_woocommerce_sub_category($parent_id, $child_data) {
    $consumer_key = API_KEY; // Replace with your Consumer Key
    $consumer_secret = API_SECRET; // Replace with your Consumer Secret
    $store_url = get_site_url(); // Your store URL

    $woocommerce = new Client(
        $store_url,
        $consumer_key,
        $consumer_secret,
        [
            'version' => 'wc/v3',
        ]
    );

    $data = [
        'name' => $child_data['name'],
        'slug' => "category-".$child_data['id'],
        'image' => [],
        'parent' => $parent_id
    ];

    try {
        $response = $woocommerce->post('products/categories', $data);
        $data = ['type' => 'sub_category', 'woocommerce_id' => $response->id, 'system_id' => $child_data['id']];
        insertDataToDB($data);
        return $response;
    } catch (Exception $e) {
        return esc_html($e->getMessage());
    }
}


// Register the AJAX action for logged-in users
add_action('wp_ajax_get_external_categories', 'ajax_get_external_categories');


function search_category($name)
{
    $consumer_key = API_KEY; // Replace with your Consumer Key
    $consumer_secret = API_SECRET; // Replace with your Consumer Secret
    $store_url = get_site_url(); // Your store URL

    $woocommerce = new Client(
        $store_url,
        $consumer_key,
        $consumer_secret,
        [
            'version' => 'wc/v3',
        ]
    );

    $category = $woocommerce->get('products/categories', ['search' => $name]);
    return $category[0]->id;
}

function get_parent_category_from_daftra($parent_id)
{
    $base_url = sprintf(GET_PARENT_CATEGORY_URL, $parent_id);

    if (!$base_url) {
        return false;
    }

    $response = wp_remote_get($base_url, [
        'headers' => [
            'APIKEY' => get_option('woocommerce_order_sync_api_key')
        ]
    ]);

    if (is_wp_error($response)) {
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
}
