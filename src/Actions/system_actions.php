<?php

function checkExist($entity, $entity_id)
{
    $base_url = $entity."CHECK_URL";
}

function getEntityData($entity)
{

}

function getSiteInfo()
{
    $base_url = GET_SITE_INFO_URL;

    $response = wp_remote_get($base_url, [
        'headers' => [
            'APIKEY' => get_option('woocommerce_order_sync_api_key')
        ],
    ]);

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
}