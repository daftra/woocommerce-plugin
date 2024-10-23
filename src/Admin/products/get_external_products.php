<?php

use Automattic\WooCommerce\Client;

function ajax_get_external_products() {
    $products = get_external_products();

    if ($products === false) {
        wp_send_json_error('Failed to fetch external orders.');
    } else {
        wp_send_json_success($products);
    }
}

// Function to get data from the external API
function get_external_products() {
    $base_url = GET_PRODUCTS_URL;

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
    $data = json_decode($body, true);


    $logged_products = getByEntity('product', ['id', 'system_id']);
    $logged_products = array_map(function($product){
        return $product['system_id'];
    }, $logged_products);

    foreach ($data['data'] as $product) {
        $product = $product['Product'];

        if (!in_array($product->id, $logged_products)){
            create_woocommerce_product($product, $logged_products);
        }
    }

    return $data;
}

// Register the AJAX action for logged-in users
add_action('wp_ajax_get_external_products', 'ajax_get_external_products');
