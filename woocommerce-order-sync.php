<?php

/*
Plugin Name: Daftra Sync
Description: Sync WooCommerce orders with Daftra system.
Version: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Author: Daftra
*/


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . "/config/endpoints.php";
require_once __DIR__ . "/src/autoload.php";
// Include WooCommerce REST API
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

register_activation_hook(__FILE__, 'woocommerce_order_sync_activate');
register_activation_hook(__FILE__, 'create_default_client_to_daftra');