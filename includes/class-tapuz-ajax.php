<?php

/**
 * Class tapuz_ajax
 *
 * This is used to handle all ajax requests form admin meta box
 */

class Tapuz_ajax {

	private $tapuz_web_service;

	public function __construct() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tapuz-web-service.php';
		$this->tapuz_web_service = new Tapuz_web_service();
	}

	/**
	 * Open new Tapuz delivery and add it to order DB
	 * @since 1.0.0
	 *
	 */
	public function tapuz_open_new_order() {
		$retrieved_nonce = $_REQUEST['tapuz_wpnonce'];
		if (!wp_verify_nonce($retrieved_nonce, 'submit_tapuz_open_ship' ) ) die( 'Failed security check' );
		global $woocommerce;
		$order_id = $_REQUEST['tapuz_order_id'];
		//$order = new WC_Order( $order_id );
		$order = wc_get_order( $order_id );
		$tapuz_collect = "";
		if ($_REQUEST['tapuz_collect'] != 'NO') {
			$tapuz_collect = $_REQUEST['tapuz_collect'];
		};
		$shipping_details = array();
		$billing_details = array();
		$shipping_details = $order->get_address('shipping');
		$billing_details = $order->get_address('billing');
		$customer_note = $order->customer_message;
		$ship_data = array();
		$ship_data['street'] = $shipping_details['address_1'].$shipping_details['address_2'];
		$ship_data['number'] = "";
		$ship_data['city'] = $shipping_details['city'];
		$ship_data['company'] = $shipping_details['company'];
		$ship_data['note'] = $customer_note;
		$ship_data['urgent'] = $_REQUEST['tapuz_urgent'];
		$ship_data['motor'] = $_REQUEST['tapuz_motor'];
		$ship_data['packages'] = $_REQUEST['tapuz_packages'];
		$ship_data['return'] = $_REQUEST['tapuz_return'];
		$ship_data['woo_id'] = $order_id;
		$ship_data['extra_note']= "";
		$ship_data['contact_name'] = $shipping_details['first_name'] .' '. $shipping_details['last_name'];
		$ship_data['contact_phone'] = $billing_details['phone'];
		$ship_data['contact_mail'] = $billing_details['email'];
		$ship_data['exaction_date'] = $_REQUEST['tapuz_exaction_date'];
		$ship_data['collect'] = $tapuz_collect;
		$ship_data['delivery_time'] =  date("d-m-Y g-i-s");
		$response = $this->tapuz_web_service->create_ship($ship_data);
		switch ($response[0]) {
			case "-100":
				echo '-100';
				die();
			case "-999":
				echo '-999';
				die();
			case "-201":
				_e( 'Error - please check WooCommerce shipping Details.', 'tapuz-delivery' );
				die();
			case "-203":
				_e( 'Error - please check Tapuz settings -> collect from address.', 'tapuz-delivery' );
				die();
			case "-204":
				_e( 'Error - please check Tapuz settings -> collect from address.', 'tapuz-delivery' );
				die();
			case "-205":
				_e( 'Error - please check Tapuz settings -> collect from address.', 'tapuz-delivery' );
				die();
			case "-206":
				_e( 'Error - please check WooCommerce shipping street name.', 'tapuz-delivery' );
				die();
			case "-207":
				_e( 'Error - please check WooCommerce shipping house number.', 'tapuz-delivery' );
				die();
			case "-208":
				_e( 'Error - please check WooCommerce shipping city name.', 'tapuz-delivery' );
				die();
			case "-209":
				_e( 'Error - please check Tapuz settings -> collect from address.', 'tapuz-delivery' );
				die();
			case "-210":
				_e( 'Error - please check Tapuz settings -> collect from address.', 'tapuz-delivery' );
				die();
			case "-219":
				_e( 'Error - please check Tapuz settings -> customer code.', 'tapuz-delivery' );
				die();
			case "-221":
				_e( 'Error - please check WooCommerce shipping note.', 'tapuz-delivery' );
				die();
			case "-222":
				_e( 'Error - please check shipping packages number.', 'tapuz-delivery' );
				die();
			case "-223":
				_e( 'Error - please check Tapuz settings -> collect from address.', 'tapuz-delivery' );
				die();
			case "-224":
				_e( 'Error - please check Tapuz settings -> collect from address.', 'tapuz-delivery' );
				die();
			case "-225":
				_e( 'Error - please check WooCommerce shipping costumer name.', 'tapuz-delivery' );
				die();
			case "-226":
				_e( 'Error - please check WooCommerce shipping costumer phone number.', 'tapuz-delivery' );
				die();
			case "-227":
				_e( 'Error - please check WooCommerce shipping costumer email.', 'tapuz-delivery' );
				die();
			case "-228":
				_e( 'Error - please check shipping delivery date.', 'tapuz-delivery' );
				die();
			case "-229":
				_e( 'Error - please check shipping collect amount.', 'tapuz-delivery' );
				die();
			default:
				$ship_data['delivery_number'] = $response[0];
				add_post_meta( $order_id, '_tapuz_ship_data', $ship_data  );
				print_r($response[0]) ;
				die();
		}
	}

	/**
	 * Get Tapuz delivery status
	 * @since 1.0.0
	 *
	 */
	public function tapuz_get_order_details() {
		$retrieved_nonce = $_REQUEST['tapuz_get_wpnonce'];
		if (!wp_verify_nonce($retrieved_nonce, 'submit_tapuz_get_ship' ) ) die( 'Failed security check' );
		$order_id = $_REQUEST['tapuz_order_id'];
		$tapuz_ship_status = $this->tapuz_web_service->get_ship_status($order_id);
		$tapuz_ship_status_arr = (array)$tapuz_ship_status['ListDeliveryDetails'];
		echo json_encode($tapuz_ship_status_arr);
		die();
	}

	/**
	 * Change Tapuz delivery status
	 * @since 1.0.0
	 */
	public function tapuz_change_order_status() {
		$retrieved_nonce = $_REQUEST['tapuz_change_wpnonce'];
		if (!wp_verify_nonce($retrieved_nonce, 'submit_tapuz_change_ship' ) ) die( 'Failed security check' );
		$ship_id = $_REQUEST['tapuz_ship_id'];
		$tapuz_ship_status = $this->tapuz_web_service->change_ship_status($ship_id);
		if ($tapuz_ship_status[0] == '-100') {
			echo '-100'	;
			die();
		} elseif ($tapuz_ship_status[0] == '-999' ) {
			echo '-999'	;
			die();
		} else {
			print_r($tapuz_ship_status[0]) ;
			die();
		}
	}

	/**
	 * Reopen Ship
	 * @since 1.0.0
	 */
	public function tapuz_reopen_ship() {
		$retrieved_nonce = $_REQUEST['tapuz_reopen_wpnonce'];
		if (!wp_verify_nonce($retrieved_nonce, 'tapuz_reopen_ship' ) ) die( 'Failed security check' );
		delete_post_meta($_REQUEST['tapuz_woo_order_id'], '_tapuz_ship_data');
		die();
	}


}