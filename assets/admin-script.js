jQuery(document).ready(function($) {



    $('#get_products_to_woocommerce').on('click', function(e) {
        $(".loader").css('display', 'flex');
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: 'GET',
            data: {
                action: 'get_external_products',
            },
            success: function(response) {
                if (response.success) {
                    $(".loader").css('display', 'none');
                    $("#products-result").html('<p>Done Successfully!</p>');
                } else {
                    $(".loader").css('display', 'none');
                    $('#products-result').html('<p>Error: ' + response.data + '</p>');
                }
            },
            error: function (e) {
                $(".loader").css('display', 'none');
                $('#products-result').html('<p>Error: Something went wrong</p>');
            }
        });
    });

    $('#sync_products_to_daftra').on('click', function(e) {
        $(".loader").css('display', 'flex');
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: 'GET',
            data: {
                action: 'sync_products_to_daftra',
            },
            success: function(response) {
                if (response.success) {
                    $(".loader").css('display', 'none');
                    $("#products-result").html('<p>Done Successfully!</p>');
                } else {
                    $(".loader").css('display', 'none');
                    $('#products-result').html('<p>Error: ' + response.data + '</p>');
                }
            },
            error: function (e) {
                $(".loader").css('display', 'none');
                $('#products-result').html('<p>Error: Something went wrong</p>');
            }

        });
    });

    $('#sync-orders-button').on('click', function(e) {
        $(".loader").css('display', 'flex');
        e.preventDefault();

        $.ajax({
            url: ajaxurl,
            type: 'GET',
            data: {
                action: 'get_woocommerce_orders',
            },
            success: function(response) {
                if (response.success) {
                    $(".loader").css('display', 'none');
                    $("#orders-result").html('<p>Done Successfully!</p>');
                } else {
                    $(".loader").css('display', 'none');
                    $('#orders-result').html('<p>Error: ' + response.data + '</p>');
                }
            },
            error: function (e) {
                $(".loader").css('display', 'none');
                $('#products-result').html('<p>Error: Something went wrong</p>');
            }

        });
    });

    $('.sidebar-link').on('click', function(e) {
        e.preventDefault();
        $('.content-section').hide();
        $($(this).attr('href')).show();
    });

    $('.content-section').hide();
    $('#products_daftra').show();
});
