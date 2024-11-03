<?php

use Automattic\WooCommerce\Client;

function ajax_sync_products_to_daftra() {
    $orders = sync_products_to_daftra();

    if ($orders === false) {
        wp_send_json_error('Failed to fetch external orders.');
    } else {
        wp_send_json_success($orders);
    }
}

// Function to get data from the external API
function sync_products_to_daftra() {
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

    $products = (array) $woocommerce->get('products');
    $logged_products = getByEntity('product', ['system_id', 'id', 'woocoomerce_id']);
    $logged_products = array_map(function($product){
        return $product['woocoomerce_id'];
    }, $logged_products);

    $products_to_send = array_map(function($product) use($logged_products){
        if (!in_array($product->id, $logged_products)){
            return $product;
        }
    }, $products);

    foreach (array_filter($products_to_send) as $item) {
        create_daftra_product($item);
    }
}

// Register the AJAX action for logged-in users
add_action('wp_ajax_sync_products_to_daftra', 'ajax_sync_products_to_daftra');

function create_daftra_product($data)
{
    $base_url = CREATE_PRODUCT_URL;
    $categories = [];
    $tags = [];

    if (!empty($data->categories)){
        foreach ($data->categories as $category){
            $categories[] = $category->name;
        }
    }

    if (!empty($data->tags)){
        foreach ($data->tags as $tag){
            $tags[] = $tag->name;
        }
    }

    $product_data = [
        'Product' => [
            'staff_id' => 1,
            'name' => $data->name,
            'description' => $data->description,
            'unit_price' => $data->regular_price,
            'tax1' => 0,
            'tax2' => 0,
            'supplier_id' => $data->id,
            'brand' => "",
            'category' => implode(",", $categories),
            'tags' => implode(",", $tags),
            'buy_price' => $data->price,
            'product_code' => "WOC".$data->id,
            'track_stock' => 1,
            'stock_balance' => $data->stock_quantity,
            'low_stock_thershold' => 5,
            'status' => 0,
            'type' => 1,
            'availabe_online' => true,
        ]
    ];


    $response = wp_remote_post($base_url, [
        'headers' => [
            'APIKEY' => get_option('woocommerce_order_sync_api_key'),
            'content-type' => 'application/json'
        ],
        'body' => wp_json_encode($product_data)
    ]);
    $body = wp_remote_retrieve_body($response);
    $response_array = json_decode($body, true);
    insertDataToDB(['system_id' => $response_array['id'], 'type' => 'product', 'woocommerce_id' => $data->id]);
    return $response_array;
}
