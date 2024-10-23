<?php

function getAllSystemShipping()
{
    $base_url = GET_SHIPPING_URL;

    $response = wp_remote_get($base_url, [
        'headers' => [
            'APIKEY' => get_option('woocommerce_order_sync_api_key')
        ],
    ]);

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
}

function handleShipping($order)
{
    if (isset($order->shipping_lines) && !empty($order->shipping_lines)){
        $system_shipping_options = getAllSystemShipping();
        $system_shipping_options = array_map(function($shipping){
            return [$shipping['id'] => $shipping['name']."-".$shipping['fees']];
        }, $system_shipping_options['data']);

        $system_shipping_names = array_map('array_values', $system_shipping_options);
        $system_shipping_names = array_merge(...$system_shipping_names);

        foreach ($order->shipping_lines as $wc_shipping){
            $value = fmod($wc_shipping->total, 1) == 0 ? (int) $wc_shipping->total : $wc_shipping->total;
            $search_value = $wc_shipping->method_title."-".$value;
            if (in_array($search_value, $system_shipping_names)){
                $indexes = array_map(function($subArray) use ($search_value) {
                    return array_search($search_value, $subArray);
                }, $system_shipping_options);

                $index = array_search(true, array_map(function($item) {
                    return $item !== false;
                }, $indexes));

                return getDaftraSingleShipping($indexes[$index]);
            } else {
                return createShippingOption($wc_shipping);
            }
        }
    }
}

function createShippingOption($shippingOption)
{
    $base_url = CREATE_SHIPPING_URL;

    $taxBody = [
        'name' => $shippingOption->method_title,
        'fees' => $shippingOption->total,
        'status' => 1,
        'tax_id' => 1
    ];

    $response = wp_remote_post($base_url, [
        'headers' => [
            'APIKEY' => get_option('woocommerce_order_sync_api_key'),
            'content-type' => 'application/json'
        ],
        'body' => json_encode($taxBody)
    ]);

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
}

function getDaftraSingleShipping($shipping_id)
{
    $base_url = sprintf(GET__SINGLE_SHIPPING_URL, $shipping_id);

    $response = wp_remote_get($base_url, [
        'headers' => [
            'APIKEY' => get_option('woocommerce_order_sync_api_key')
        ],
    ]);

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);

}