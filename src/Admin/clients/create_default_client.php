<?php

function create_default_client_to_daftra()
{
    $base_url = CREATE_CLIENT_URL;

    $client_data = [
        'Client' => [
            'is_offline' => true,
            'staff_id' => 0,
            'business_name' => "Woocommerce Default",
            'first_name' => "Woocommerce",
            'last_name' => "Default",
            'email' => "",
            'city' => "",
            'state' => "",
            'phone1' => "",
            'country_code' => "",
            'default_currency_code' => '',
            'type' => 2,
            'credit_limit' => 0,
            'credit_period' => 0
        ]
    ];

    $response = wp_remote_post($base_url, [
        'headers' => [
            'APIKEY' => get_option('woocommerce_order_sync_api_key'),
            'content-type' => 'application/json',
            'accept' => 'application/json'
        ],
        'body' => json_encode($client_data)
    ]);

    $body = wp_remote_retrieve_body($response);
    $client_id = json_decode($body, true)['id'];
    update_option( 'woocommerce_order_sync_default_client', $client_id);

    return $client_id;
}