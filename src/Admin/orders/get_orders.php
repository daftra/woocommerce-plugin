<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Automattic\WooCommerce\Client;

require_once __DIR__ . "/../taxes/taxes_functions.php";
require_once __DIR__ . "/../shipping/shipping_functions.php";
require_once __DIR__ . "/../products/products_functions.php";
require_once __DIR__ . "/../clients/clients_functions.php";
require_once __DIR__ . "/create_order.php";

function ajax_get_woocommerce_orders()
{
    $orders = get_woocommerce_orders();

    if ($orders === false) {
        wp_send_json_error('Failed to fetch external orders.');
    } else {
        wp_send_json_success($orders);
    }
}
function get_woocommerce_orders()
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

    $orders = $woocommerce->get('orders');

    $logged_orders = getByEntity('order', ['id', 'woocoomerce_id', 'system_id']);
    $logged_orders = array_map(function($product){
        return $product['woocoomerce_id'];
    }, $logged_orders);

    $orders_to_send = array_filter(array_map(function($order) use($logged_orders){
        if (!in_array($order->id, $logged_orders)){
            return $order;
        }
    }, $orders));


    if (!empty($orders_to_send)){
        return send_orders_to_daftra($orders_to_send);
    } else {
        return true;
    }
}

function send_orders_to_daftra($orders) :bool
{
    foreach ($orders as $order)
    {
        if (!empty($order->line_items)){
            $products = handleProduct($order->line_items);
            $tax = (int) handleTaxes($order);
            $shipping = handleShipping($order);
            $client = handleClient($order->customer_id);
            $system_id = create_daftra_order($tax, $shipping, $client, (array) $order);
            $data = ['type' => 'sub_category', 'woocommerce_id' => $order->id, 'system_id' => $system_id];
            insertDataToDB($data);
        } else {
            return false;
        }
    }
    return true;
}

add_action('wp_ajax_get_woocommerce_orders', 'ajax_get_woocommerce_orders');