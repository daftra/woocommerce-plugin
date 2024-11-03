<?php

use Automattic\WooCommerce\Client;

function create_woocommerce_product($productData, $orders_ids) {
    $consumer_key = API_KEY; // Replace with your Consumer Key
    $consumer_secret = API_SECRET; // Replace with your Consumer Secret
    $store_url = get_site_url(); // Your store URL

    $woocommerce = new Client(
        $store_url,
        $consumer_key,
        $consumer_secret,
        [
            'version' => 'wc/v3',
        ]
    );

    $product_categories = [];
    if (!empty($productData['ProductCategory'])){
        foreach ($productData['ProductCategory'] as $category) {
            $check = search_category($category['name']);
            if(!$check && $category['parent_id'] == null){
                $product_categories[] = create_woocommerce_category($category);
            } elseif(!$check && $category['parent_id'] != null) {
                $parent_data = get_parent_category_from_daftra($category['parent_id']);
                $parent_check = search_category($parent_data['data']['Category']['name']);
                if (!$parent_check){
                    create_woocommerce_category($parent_data['data']['Category']);
                }
                $product_categories[] = create_woocommerce_sub_category($parent_check, $category);
            } else {
                $product_categories[] = $check;
            }
        }
    }
    $product_categories = array_values($product_categories);

    if (isset($productData['ProductMasterImage'])){
        $image = ['src' => $productData['ProductMasterImage']['file_full_path']];
    } else {
        $image = [];
    }

    $data = [
        'name' => $productData['name'],
        'slug' => "daftra-product-".$productData['id'],
        'sku' => "#DAFTRA".$productData['product_code'],
        'type' => 'simple',
        'regular_price' => number_format($productData['unit_price'], 2, '.', ''),
        'description' => $productData['name'],
        'short_description' => $productData['name'],
        'categories' => array_map(function($id) {
            return ['id' => $id];
        }, $product_categories),
        'images' => [$image],
        'meta_data' => [
            [
                'key' => 'daftra-system-id',
                'value' => $productData['id']
            ]
        ]
    ];

    try {
        if (!in_array($productData['id'], $orders_ids)) {
            $product = $woocommerce->post('products', $data);
            $data = ['type' => 'product', 'system_id' => $productData['id'], 'woocommerce_id' => $product->id];
            insertDataToDB($data);
        }
        return true;
//        echo 'Product created successfully.';
    } catch (Exception $e) {
        echo 'Error: ' . esc_html($e->getMessage());
    }
}