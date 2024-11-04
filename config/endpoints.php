<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$system_prefix = get_option('woocommerce_order_sync_base_url') .'/api2/';
$laravel_system_prefix = get_option('woocommerce_order_sync_base_url') . "/v2/api/";

define("GET_PRODUCTS_URL", $system_prefix . 'products');
define("GET_CATEGORIES_URL", $system_prefix . 'categories');
define("GET_PARENT_CATEGORY_URL", $system_prefix . 'product_categories/%s');
define("CREATE_ORDERS_URL", $system_prefix . 'invoices');
define("GET_CLIENTS_URL", $system_prefix . 'clients');
define("CREATE_CLIENT_URL", $system_prefix."clients");
define("CREATE_PRODUCT_URL", $system_prefix."products.json");
define("UPDATE_ORDER_WEBHOOK", $system_prefix."invoices/%s");
define("GET_SITE_INFO_URL", $system_prefix."site_info");

define("GET_TAXES_URL", $system_prefix."taxes");
define("CREATE_TAX_URL", $system_prefix."taxes");

define("GET_SHIPPING_URL", $laravel_system_prefix."entity/shipping_option/list/1?per_page=50");
define("GET__SINGLE_SHIPPING_URL", $laravel_system_prefix."entity/shipping_option/%s/show");
define("CREATE_SHIPPING_URL", $laravel_system_prefix."local_entities_data/shipping_option");

define("GET_SPECIFIC_PRODUCT_DATA", $system_prefix."products/%s");
define("GET_SPECIFIC_CLIENT_DATA", $system_prefix."clients/%s");