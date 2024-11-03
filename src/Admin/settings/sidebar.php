<?php
add_action('admin_menu', 'woocommerce_order_sync_settings_page');

function woocommerce_order_sync_settings_page() {
    add_options_page(
        'Daftra Commerce',
        'Daftra Commerce',
        'manage_options',
        'Daftra Commerce',
        'woocommerce_order_sync_settings_page_html'
    );
    add_menu_page(
        'Daftra Commerce',     // Page title
        'Daftra Commerce',     // Menu title
        'manage_options',       // Capability
        'woocommerce-orders-sync',     // Menu slug
        'sync_orders_plugin_page',// Callback function
        'dashicons-admin-generic', // Icon
        6                       // Position
    );
}

function woocommerce_order_sync_settings_page_html() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('woocommerce_order_sync_settings');
            do_settings_sections('woocommerce_order_sync');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}

// Add settings link on plugin page
function woocommerce_order_sync_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=Daftra Commerce">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'woocommerce_order_sync_settings_link');
