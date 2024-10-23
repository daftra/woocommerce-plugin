<?php
/**
 * Plugin Name: Order Status Webhook
 * Description: Triggers a function when WooCommerce order status changes to "completed".
 * Version: 1.0
 * Author: Your Name
 */

// Ensure WooCommerce is active
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    // Hook to order status completed
    add_action('woocommerce_order_status_completed', 'update_daftra_order', 10, 1);

    function update_daftra_order($order_id) {
        // Add your custom code here
        // For example, send data to a webhook:

        $data = ['Invoice' => ['payment_status' => 2]];

        if (!empty($data)){
            $webhook_url = sprintf(UPDATE_ORDER_WEBHOOK, $data[0]['system_id']);
            // Send data using wp_remote_post
            $response = wp_remote_request($webhook_url, [
                'headers'   => [
                    'Content-Type' => 'application/json',
                    'APIKEY' => get_option('woocommerce_order_sync_api_key')
                ],
                'body'      => json_encode($data),
                'method'    => 'PUT'
            ]);

            // Check for errors or handle response
            if (is_wp_error($response)) {
                // Handle error
                $error_message = $response->get_error_message();
                error_log('Webhook failed: ' . $error_message);
            } else {
                $body = wp_remote_retrieve_body($response);
                $res = json_decode($body, true);

                error_log(json_encode($res));
            }
        } else {
            error_log("Order is not synced to daftra yet!");
        }
    }
}
