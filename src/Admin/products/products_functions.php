<?php

use Automattic\WooCommerce\Client;

function handleProduct($products)
{
    $final_products = [];
    $system_products = getDaftraProducts();
    $system_products = array_map(function($product){
        return [$product['Product']['id'] => $product['Product']['product_code']."-".$product['Product']['name']];
    }, $system_products['data']);

    $system_products_names = array_map('array_values', $system_products);
    $system_products_names = array_merge(...$system_products);

    foreach ($products as $product) {
        $search_value = "WOC".$product->id."-".$product->name;
        if (in_array($search_value, $system_products_names)){
            $final_products[] = (array) $product;
        } else {
            $daftra_response = create_daftra_product($product);
            $final_products[] = getDaftraSingleProduct($daftra_response['id']);
        }
    }
    return $final_products;
}

function getDaftraSingleProduct($id)
{
    $base_url = sprintf(GET_SPECIFIC_PRODUCT_DATA, $id);

    $response = wp_remote_get($base_url, [
        'headers' => [
            'APIKEY' => get_option('woocommerce_order_sync_api_key')
        ],
    ]);

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true)['data']['Product'];
}

function getDaftraProducts()
{
    $base_url = GET_PRODUCTS_URL;

    $response = wp_remote_get($base_url, [
        'headers' => [
            'APIKEY' => get_option('woocommerce_order_sync_api_key')
        ],
    ]);

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
}