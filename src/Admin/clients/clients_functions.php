<?php

use Automattic\WooCommerce\Client;

function handleClient($client_id)
{
    // there is client in the invoice
    if ($client_id) {
        $daftraClients = getDaftraClients();

        $system_clients = array_map(function($client){
            return [$client['Client']['id'] => $client['Client']['email']];
        }, $daftraClients['data']);

        $system_clients_emails = array_map('array_values', $system_clients);
        $system_clients_emails = array_merge(...$system_clients);

        $client_data = getSpecificClient($client_id);

        if (in_array($client_data->email, $system_clients_emails)){
            return (array) $client_data;
        } else {
            $client_id = create_client_to_daftra($client_data);
            return getDaftraSingleClient($client_id);
        }
    }
    // use default client
    else {
        return getDaftraSingleClient(get_option('woocommerce_order_sync_default_client'));
    }
}

function getSpecificClient($client_id)
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

    return $woocommerce->get('customers/'.$client_id);
}

function getDaftraClients()
{
    $base_url = GET_CLIENTS_URL;

    $response = wp_remote_get($base_url, [
        'headers' => [
            'APIKEY' => get_option('woocommerce_order_sync_api_key')
        ],
    ]);

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
}


function create_client_to_daftra($data)
{
    $base_url = CREATE_CLIENT_URL;

    $client_data = [
        'Client' => [
            'is_offline' => true,
            'staff_id' => 0,
            'business_name' => $data->username,
            'first_name' => $data->first_name,
            'last_name' => $data->last_name,
            'email' => $data->email,
            'city' => $data->city,
            'state' => $data->state,
            'phone1' => $data->phone,
            'country_code' => $data->country,
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
    return json_decode($body, true)['id'];
}

function getDaftraSingleClient($id)
{
    $base_url = sprintf(GET_SPECIFIC_CLIENT_DATA, $id);

    $response = wp_remote_get($base_url, [
        'headers' => [
            'APIKEY' => get_option('woocommerce_order_sync_api_key')
        ],
    ]);

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true)['data']['Client'];
}
