/**
 * Tapuz delivery plugin
 * Order page @ admin screen
 * Ajax requests
 */
(function( $ ) {
	'use strict';
	$(document).ready( function($) {
        /**
         * If the ship is already open - get ship details
         */
        if(document.getElementById("tapuz_ship_exists") != null) {
            $(".tapuz-powered-by").hide();
            $("#tapuz_ship_exists").append('<img class="tapuz_loader" src="'+tapuz_delivery.tapuz_ajax_loader+'">');
            var tapuz_ship_id = $(".tapuz_delivery_id").text();
            $.ajax({
                url: tapuz_delivery.ajax_url,
                type: 'POST',
                data: {
                    action : 'tapuz_get_order_details',
                    tapuz_order_id: tapuz_ship_id,
                    tapuz_get_wpnonce: tapuz_delivery.tapuz_ajax_get_nonce
        },
            success: function( response ) {
                var delivery_status = "";
                $(".tapuz_loader").hide();
                $(".tapuz-powered-by").show();
                if (response == 'Communication error') {
                    $("#tapuz_ship_exists").append('<p class="tapuz_error_message">'+tapuz_delivery.tapuz_err_message+'</p>');
                } else {
                    var tapuz_response = JSON.parse(response);
                    if (tapuz_response.StatusCode == "-999") {
                        $(".tapuz_exist_details").replaceWith("<h4>"+ tapuz_delivery.tapuz_err_message_code + "</h4>");
                    } else if (Object.keys(tapuz_response.Records).length == 0) {
                        $("#tapuz_ship_exists").append('<p class="tapuz_error_message">'+tapuz_delivery.tapuz_err_message_code+'</p>');
                    }else if (tapuz_response.StatusCode == "-100") {
                        $(".tapuz_exist_details").replaceWith("<h4>"+ tapuz_delivery.tapuz_err_message_code + "</h4>");
                    } else {
                        var tapuz_status = tapuz_response.Records.Record.DeliveryStatus;
                        switch (tapuz_status) {
                            case "1":
                                delivery_status = tapuz_delivery.tapuz_status_1;
                                break;
                            case "2":
                                delivery_status = tapuz_delivery.tapuz_status_2;
                                break;
                            case "3":
                                delivery_status = tapuz_delivery.tapuz_status_3;
                                break;
                            case "4":
                                delivery_status = tapuz_delivery.tapuz_status_4;
                                break;
                            case "5":
                                delivery_status = tapuz_delivery.tapuz_status_5;
                                break;
                            case "7":
                                delivery_status = tapuz_delivery.tapuz_status_7;
                                break;
                            case "8":
                                delivery_status = tapuz_delivery.tapuz_status_8;
                                break;
                            case "9":
                                delivery_status = tapuz_delivery.tapuz_status_9;
                                break;
                            case "12":
                                delivery_status = tapuz_delivery.tapuz_status_12;
                                break;
                        }
                        $(".tapuz_exist_details").show();

                        if (tapuz_status == 8) {
                            $(".tapuz_ship_open").hide();
                            $(".tapuz-button-container").append('<button class="tapuz-button tapuz-reopen-ship">'+tapuz_delivery.tapuz_reopen_ship+'</button>');
                        } else if (tapuz_status != 1 && tapuz_status != 2){
                            $(".tapuz_receiver_name").html(tapuz_response.Records.Record.Receiver);
                            $(".tapuz_shipped_on").html(tapuz_response.Records.Record.ExeTime);
                        } else {
                            $(".tapuz_ship_open").hide();
                        }
                        $(".tapuz_delivery_status").html(delivery_status);
                        if (tapuz_status == 1){
                            $(".tapuz-button-container").append('<button class="tapuz-button tapuz-cancel-ship">'+tapuz_delivery.tapuz_cancel_ship+'</button>');
                        }
                    }
                }
            },
            error: function () {
                $(".tapuz_loader").hide();
                $(".tapuz-powered-by").show();
                $("#tapuz_ship_exists").append('<p class="tapuz_error_message">'+tapuz_delivery.tapuz_err_message+'</p>');
                }
            });

        }
        /**
         * On click open new Tapuz delivery
         */
		$(document).on("click", ".tapuz-open-button", function(event){
            event.preventDefault();
            $(".tapuz-open-button").hide();
            $(".tapuz-powered-by").hide();
            $("#tapuz_open_ship").append('<img class="tapuz_loader" src="'+tapuz_delivery.tapuz_ajax_loader+'">');
            var tapuz_order_id = $("button.tapuz-open-button").attr("data-order");
            var tapuz_urgent = "1";
            if ($('#tapuz_urgent').attr('checked')){
                tapuz_urgent = "2"
            }
            var tapuz_return = "1";
            if($('#tapuz_return').attr('checked') == "checked"){
                tapuz_return = "2"
            }
            var tapuz_collect = $('#tapuz_collect').val();
            if (isNaN(tapuz_collect)) {
                tapuz_collect = 'NO';
            }
            var tapuz_motor = $('#tapuz_motor').val();
            var tapuz_packages = $('#tapuz_packages').val();
            var tapuz_exaction_date = $('#tapuz_exaction_date').val();
            $.ajax({
                url: tapuz_delivery.ajax_url,
                type: 'POST',
                data: {
                    action : 'tapuz_open_new_order',
                    tapuz_order_id : tapuz_order_id,
                    tapuz_urgent: tapuz_urgent,
                    tapuz_return: tapuz_return,
                    tapuz_collect: tapuz_collect,
                    tapuz_motor: tapuz_motor,
                    tapuz_packages: tapuz_packages,
                    tapuz_exaction_date: tapuz_exaction_date,
                    tapuz_wpnonce: tapuz_delivery.tapuz_ajax_nonce
                },
                success: function( response ) {
                    $(".tapuz_loader").hide();
                    $(".tapuz-powered-by").show();
                    $(".tapuz-open-button").hide();
                    if (response == '-100') {
                        $("#tapuz_open_ship").append('<p class="tapuz_error_message">'+tapuz_delivery.tapuz_err_message_open_code+'</p>');
                    } else if (response == '-999') {
                        $("#tapuz_open_ship").append('<p class="tapuz_error_message">'+tapuz_delivery.tapuz_err_message+'</p>');
                    } else {
                        $("#tapuz_open_ship").hide();
                        $(".tapuz-success-ship").show();
                        $(".tapuz-success-ship-number").html(response);
                    }
                },
                error: function() {
                    $(".tapuz_loader").hide();
                    $(".tapuz-powered-by").show();
                    $("#tapuz_open_ship").append('<p>'+tapuz_delivery.tapuz_err_message+'</p>');
                }
            })
        });
        
        /**
         * On click change Tapuz delivery status
         */
        $(document).on("click", ".tapuz-cancel-ship", function(event){
            event.preventDefault();
            $("#tapuz_ship_exists").hide();
            $(".tapuz-powered-by").hide();
            $(".tapuz-wrapper").append('<img class="tapuz_loader" src="'+tapuz_delivery.tapuz_ajax_loader+'">');
            var tapuz_ship_id = $(".tapuz_delivery_id").text();
            $.ajax({
                url: tapuz_delivery.ajax_url,
                type: 'POST',
                data: {
                    action : 'tapuz_change_order_status',
                    tapuz_ship_id : tapuz_ship_id,
                    tapuz_change_wpnonce: tapuz_delivery.tapuz_ajax_change_nonce
                },
                success: function( response ) {
                    console.log(response);
                    $(".tapuz_loader").hide();
                    if (response == '-100') {
                        $(".tapuz-wrapper").append('<p class="tapuz_error_message">'+tapuz_delivery.tapuz_err_message_open_code+'</p>');
                    } else if (response == '-999') {
                        $(".tapuz-wrapper").append('<p class="tapuz_error_message">'+tapuz_delivery.tapuz_err_message+'</p>');
                    } else if (response == '1') {
                        $(".tapuz-wrapper").append('<p class="tapuz_error_message">'+tapuz_delivery.tapuz_cancel_ship_ok+'</p>');
                    } else {
                        $(".tapuz-wrapper").append('<p class="tapuz_error_message">'+tapuz_delivery.tapuz_err_message+'</p>');
                    }
                },
                error: function() {
                    $(".tapuz_loader").hide();
                    $(".tapuz-wrapper").append('<p>'+tapuz_delivery.tapuz_err_message+'</p>');
                }
            })
        });
        /**
         * On click change Tapuz delivery status
         */
        $(document).on("click", ".tapuz-reopen-ship", function(event){
            event.preventDefault();
            $("#tapuz_ship_exists").hide();
            $(".tapuz-powered-by").hide();
            $(".tapuz-wrapper").append('<img class="tapuz_loader" src="'+tapuz_delivery.tapuz_ajax_loader+'">');
            var tapuz_woo_order_id = $("#tapuz_ship_exists").attr("data-order");
            $.ajax({
                url: tapuz_delivery.ajax_url,
                type: 'POST',
                data: {
                    action : 'tapuz_reopen_ship',
                    tapuz_woo_order_id : tapuz_woo_order_id,
                    tapuz_reopen_wpnonce: tapuz_delivery.tapuz_ajax_reopen_nonce
                },
                success: function( response ) {
                    location.reload();
                },
                error: function() {
                    $(".tapuz_loader").hide();
                    $(".tapuz-powered-by").show();
                    $(".tapuz-wrapper").append('<p>'+tapuz_delivery.tapuz_err_message+'</p>');
                }
            })
        });
        /**
         * Put NIS mark next to the collect box
         */
        $("#tapuz_collect").change(function () {
            if ($(".tapuz_nis").length == 0 ){
                $("#tapuz_collect").after("<span class='tapuz_nis'>&#8362</span>");
            }

        });
	})
})( jQuery );


