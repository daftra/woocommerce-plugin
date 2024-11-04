<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


require_once __DIR__ . "/Admin/settings/sidebar.php";
require_once __DIR__ . "/Admin/settings/setting_page.php";
require_once __DIR__ . "/Admin/products/create_product.php";
require_once __DIR__ . "/Admin/products/get_external_products.php";
require_once __DIR__ . "/Admin/settings/scripts.php";
require_once __DIR__ . "/Admin/orders/get_orders.php";
require_once __DIR__ . "/Admin/categories/get_external_categories.php";
require_once __DIR__ . "/Admin/database/create_db_table.php";
require_once __DIR__ . "/Admin/clients/create_default_client.php";
require_once __DIR__ . "/Admin/products/sync_products_to_daftra.php";
require_once __DIR__ . "/Admin/database/InsertDataToDB.php";
require_once __DIR__ . "/Webhooks/order-status-webhook.php";