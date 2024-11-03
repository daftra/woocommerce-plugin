<?php
// Register settings and add settings page
add_action('admin_init', 'woocommerce_order_sync_settings_init');

function woocommerce_order_sync_settings_init() {
    register_setting('woocommerce_order_sync_settings', 'woocommerce_order_sync_base_url');
    register_setting('woocommerce_order_sync_settings', 'woocommerce_order_sync_api_key');
    register_setting('woocommerce_order_sync_settings', 'woocommerce_order_sync_default_client');

    add_settings_section(
        'woocommerce_order_sync_settings_section',
        __('Daftra WooCommerce Order Settings', 'woocommerce-plugin-sync'),
        'woocommerce_order_sync_settings_section_callback',
        'woocommerce_order_sync'
    );

    add_settings_field(
        'woocommerce_order_sync_base_url',
        __('Base URL', 'woocommerce-plugin-sync'),
        'woocommerce_order_sync_base_url_render',
        'woocommerce_order_sync',
        'woocommerce_order_sync_settings_section'
    );

    add_settings_field(
        'woocommerce_order_sync_api_key',
        __('API KEY', 'woocommerce-plugin-sync'),
        'woocommerce_order_sync_api_key_render',
        'woocommerce_order_sync',
        'woocommerce_order_sync_settings_section'
    );

    add_settings_field(
        'woocommerce_order_sync_default_client',
        'Choose Default Client',
        'woocommerce_order_sync_default_client_render',
        'woocommerce_order_sync',
        'woocommerce_order_sync_settings_section'
    );
}

function my_custom_settings_section_callback() {
    echo 'Select an option from the dropdown.';
}

function woocommerce_order_sync_settings_section_callback() {
    echo esc_html(__('Enter the base URL for the external system.', 'woocommerce-plugin-sync'));
}
function woocommerce_order_sync_base_url_render() {
    $base_url = get_option('woocommerce_order_sync_base_url', '');
    ?>
    <input type="text" name="woocommerce_order_sync_base_url" value="<?php echo esc_attr($base_url); ?>" size="50">
    <?php
}
function woocommerce_order_sync_api_key_render() {
    $api_key = get_option('woocommerce_order_sync_api_key', '');
    ?>
    <input type="text" name="woocommerce_order_sync_api_key" value="<?php echo esc_attr($api_key); ?>" size="50">
    <?php
}
function woocommerce_order_sync_default_client_render() {
    $options = get_option('woocommerce_order_sync_default_client');
    $dropdown_options = clients_dropdown_options();
    ?>
    <select name="woocommerce_order_sync_default_client">
        <?php foreach ($dropdown_options as $key => $label) { ?>
            <option value="<?php echo esc_attr($key); ?>" <?php selected($options, $key); ?>>
                <?php echo esc_html($label); ?>
            </option>
        <?php } ?>
    </select>
    <?php
}

function clients_dropdown_options() {
    $base_url = GET_CLIENTS_URL;

    if (!$base_url) {
        return false;
    }
    // This function should call the external app's API to get the dropdown options
    $response = wp_remote_get($base_url, [
        'headers' => [
            'APIKEY' => get_option('woocommerce_order_sync_api_key')
        ]
    ]);

    if (is_wp_error($response)) {
        return [];
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (empty($data) || !is_array($data)) {
        return [];
    }

    $options[] = "choose Default Client";
    foreach ($data['data'] as $item) {
        $options[$item['Client']['id']] = $item['Client']['business_name'];
    }

    return $options;
}

function sync_orders_plugin_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <div id="my-custom-plugin-container">
            <div id="my-custom-plugin-sidebar">
                <h2>Actions</h2>
                <ul>
                    <li><a href="#products_daftra" class="sidebar-link">Get Products To Woocommerce</a></li>
                    <li><a href="#products_woocommerce" class="sidebar-link">Sync Products To Daftra</a></li>
                    <li><a href="#section1" class="sidebar-link">Sync Orders</a></li>
                </ul>
            </div>
            <div id="my-custom-plugin-content">
                <div id="products_daftra" class="content-section">
                    <h2>Get Products To Woocommerce:</h2>
                    <button id="get_products_to_woocommerce" class="button button-primary">Get Products To Woocommerce</button>
                    <div id="products-result">
                        <div class="loader"></div>
                    </div>
                </div>
                <div id="products_woocommerce" class="content-section">
                    <h2>Sync Products To Daftra:</h2>
                    <button id="sync_products_to_daftra" class="button button-primary">Sync Products To Daftra</button>
                    <div id="products-result">
                        <div class="loader"></div>
                    </div>
                </div>
                <div id="section1" class="content-section">
                    <h2>Sync Orders:</h2>
                    <button id="sync-orders-button" class="button button-primary">Sync Orders</button>
                    <div id="orders-result">
                        <div class="loader"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <?php
}


