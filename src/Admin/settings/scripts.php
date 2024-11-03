<?php

// Enqueue admin styles and scripts
add_action('admin_enqueue_scripts', 'enqueue_scripts');

function enqueue_scripts() {
    wp_enqueue_style('my-custom-plugin-admin-css', dirname(dirname(plugin_dir_url(__DIR__))) . '/assets/admin-style.css', [], '1.0.0');
    wp_enqueue_script('my-custom-plugin-admin-js', dirname(dirname(plugin_dir_url(__DIR__))) . '/assets/admin-script.js', array('jquery'), '1.0', true);
}