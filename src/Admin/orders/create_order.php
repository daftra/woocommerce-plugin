<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$payment_statuses = [
    'completed' => 2,
    'pending' => 0,
    'processing' => 0,
    'on-hold' => 0,
    'cancelled' => 0,
    'refunded' => 0,
    'failed' => 0,
    'draft' => 0
];
define('payment_statuses', $payment_statuses);

$invoice_payment_statuses = [
    'completed' => 1,
    'pending' => 2,
    'processing' => 2,
    'on-hold' => 2,
    'cancelled' => 2,
    'refunded' => 2,
    'failed' => 2,
    'draft' => 2
];
define('invoice_payment_statuses', $invoice_payment_statuses);



function create_daftra_order($tax, $shipping, $client, $orderData)
{
    $base_url = CREATE_ORDERS_URL;

    $payload = preparePayload($tax, $shipping, $client, $orderData);

    $response = wp_remote_post($base_url, [
        'headers' => [
            'APIKEY' => get_option('woocommerce_order_sync_api_key'),
            'content-type' => 'application/json',
            'accept' => 'application/json'
        ],
        'body' => wp_json_encode($payload)
    ]);

    $body = wp_remote_retrieve_body($response);
    return json_decode($body, true)['id'];

}

function preparePayload($tax, $shipping, $client, $orderData)
{
    $siteInfo = getSiteInfo();
    $invoice['payment_status'] = payment_statuses[$orderData['status']];
    if ($orderData['status'] == 'draft') {
        $invoice['draft'] = 1;
    } else {
        $invoice['draft'] = 0;
    }
    $invoice['site_id'] = $siteInfo['data']['Site']['id'];
    $invoice['staff_id'] = 0;
    $invoice['store_id'] = 1;
    $invoice['type'] = 0;
    $invoice['is_offline'] = 1;
    $invoice['issued'] = 0;
    $invoice['deposit'] = 0;
    $invoice['deposit_type'] = 1;
    $invoice['due_after'] = 0;
    $invoice['date_format'] = 0;
    $invoice['language_id'] = $siteInfo['data']['Site']['language_code'];
    $invoice['client_id'] = $client['id'];
    $invoice['currency_code'] = $orderData['currency'];
    $invoice['client_business_name'] = $client['business_name'];
    $invoice['client_country_code'] = $client['country_code'];
    $invoice['date'] = gmdate('Y-m-d');
    $invoice['shipping_amount'] = $shipping['fees'];
    $invoice['shipping_option_id'] = $shipping['id'];
    $invoice['shipping_tax_id'] = $tax;
    $invoice['issue_date'] = gmdate('Y-m-d');
    $invoice['summary_subtotal'] = $orderData['total'];
    $invoice['summary_discount'] = $orderData['discount_total'];
    $invoice['summary_total'] = $orderData['total'];
    $invoiceCustomField = [
        [
            'label' => 'WooCommerce_id',
            'value' => $orderData['id']
        ]
    ];
    if ($invoice['payment_status'] == 2) {
        $invoice['summary_paid'] = $orderData['total'];
        $invoice['summary_unpaid'] = 0;
    } else {
        $invoice['summary_unpaid'] = $orderData['total'];
        $invoice['summary_paid'] = 0;
    }
    $invoice['created'] = str_replace('T', ' ', $orderData['date_created']);
    $invoice['modified'] = str_replace('T', ' ', $orderData['date_modified']);
    $invoice['required_terms_file'] = 0;
    $invoice['invoice_layout_id'] = 0;
    $invoice['branch_id'] = $tax;
    $invoice['group_price_id'] = 0;
    $invoiceItems = [];
    foreach ($orderData['line_items'] as $line_item){
        $line_item = (array) $line_item;
        $lineItem['item'] = $line_item['name'];
        $lineItem['description'] = $line_item['name'] . " IS on woocommerce with ID ". $line_item['product_id'];
        $lineItem['unit_price'] = $line_item['total'];
        $lineItem['quantity'] = $line_item['quantity'];
        $lineItem['tax1'] = $tax;
        $lineItem['summary_tax1'] = floatval($line_item['total_tax']);
        $lineItem['product_id'] = $line_item['product_id'];
        $invoiceItems[] = $lineItem;
    }

    $invoicePayment['payment_method'] = $orderData['payment_method_title'];
    $invoicePayment['status'] = invoice_payment_statuses[$orderData['status']];
    $invoicePayment['amount'] = $orderData['total'];
    $invoicePayment['transaction_id'] = $orderData['transaction_id'];
    $invoicePayment['treasury_id'] = "1";
    $invoicePayment['staff_id'] = "1";
    $invoicePayment['date'] = str_replace('T', ' ', $orderData['date_created']);

    return [
        'Invoice' => $invoice,
        'InvoiceItem' => $invoiceItems,
        'Payment' => [$invoicePayment],
        'InvoiceCustomField' =>$invoiceCustomField
    ];
}