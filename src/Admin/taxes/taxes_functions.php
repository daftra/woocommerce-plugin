<?php

require_once __DIR__ . '/../../Actions/system_actions.php';
function getAllSystemTaxes()
{
    $base_url = GET_TAXES_URL;

    $response = wp_remote_get($base_url, [
        'headers' => [
            'APIKEY' => get_option('woocommerce_order_sync_api_key')
        ],
    ]);

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
}

function handleTaxes($order)
{
    if (isset($order->tax_lines) && !empty($order->tax_lines)){
        $system_taxes = getAllSystemTaxes();
        $system_taxes = array_map(function($tax){
            return [$tax['Tax']['id'] => $tax['Tax']['name']."-".$tax['Tax']['value']];
        }, $system_taxes['data']);

        $system_taxes_names = array_map('array_values', $system_taxes);
        $system_taxes_names = array_merge(...$system_taxes);

        foreach ($order->tax_lines as $wc_tax){
            $search_value = $wc_tax->label."-".$wc_tax->rate_percent;
            if (in_array($search_value, $system_taxes_names)){
                $indexes = array_map(function($subArray) use ($search_value) {
                    return array_search($search_value, $subArray);
                }, $system_taxes);

                $index = array_search(true, array_map(function($item) {
                    return $item !== false;
                }, $indexes));
                return $indexes[$index];
            } else {
                $new_tax = createTax($wc_tax);
                return $new_tax['id'];
            }
        }
    }
}

function createTax($taxData)
{
    $base_url = CREATE_TAX_URL;
    $siteData = getSiteInfo();

    $taxBody = [
        'Tax' => [
            'name' => $taxData->label,
            'value' => $taxData->rate_percent,
            'description' => wp_json_encode(['woocommerce_id' => $taxData->id]),
            'site_id' => $siteData['data']['Site']['id'],
            'branch_id' => 1
        ]
    ];

    $response = wp_remote_post($base_url, [
        'headers' => [
            'APIKEY' => get_option('woocommerce_order_sync_api_key'),
            'content-type' => 'application/json'
        ],
        'body' => wp_json_encode($taxBody)
    ]);

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true);
}