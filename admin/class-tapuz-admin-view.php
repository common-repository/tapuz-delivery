<?php

/**
 * Class Tapuz_admin_view
 * This is used to display Tapuz delivery box on admin area
 *
 * @since 1.0.0
 *
 */
class Tapuz_admin_view {
	/**
	 * Enqueue Scripts and Localize Script
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts($hook) {
		global $post_type;
		if ( ('post.php' == $hook || 'post-new.php' == $hook) && $post_type == 'shop_order'  ) {
			wp_register_script(
				'tapuz-delivery-admin',
				plugins_url( 'admin/js/tapuz-delivery-admin.js', dirname( __FILE__ ) ),
				array('jquery'),
				PLUGIN_VERSION,
				true
			);
			wp_localize_script( 'tapuz-delivery-admin', 'tapuz_delivery', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'tapuz_ajax_nonce' => wp_create_nonce( "submit_tapuz_open_ship" ),
				'tapuz_ajax_get_nonce' => wp_create_nonce( "submit_tapuz_get_ship" ),
				'tapuz_ajax_change_nonce' => wp_create_nonce( "submit_tapuz_change_ship" ),
				'tapuz_ajax_reopen_nonce' => wp_create_nonce( "tapuz_reopen_ship" ),
				'tapuz_ajax_loader' => plugins_url( 'admin/img/reload.gif', dirname( __FILE__ ) ),
				'tapuz_err_message' => __( 'We are experiencing a communication error. Please try again later.', 'tapuz-delivery' ),
				'tapuz_cancel_ship' => __( 'Cancel shipment', 'tapuz-delivery' ),
				'tapuz_cancel_ship_ok' => __( 'Shipment canceled successfully', 'tapuz-delivery' ),
				'tapuz_reopen_ship' => __( 'Reopen shipment', 'tapuz-delivery' ),
				'tapuz_status_1' => __( 'Open', 'tapuz-delivery' ),
				'tapuz_status_2' => __( 'Delivery man on his way', 'tapuz-delivery' ),
				'tapuz_status_3' => __( 'Delivered', 'tapuz-delivery' ),
				'tapuz_status_4' => __( 'Collected from customer', 'tapuz-delivery' ),
				'tapuz_status_5' => __( 'Back from costumer', 'tapuz-delivery' ),
				'tapuz_status_7' => __( 'Approved', 'tapuz-delivery' ),
				'tapuz_status_8' => __( 'Canceled', 'tapuz-delivery' ),
				'tapuz_status_9' => __( 'Second delivery man ', 'tapuz-delivery' ),
				'tapuz_status_12' => __( 'On hold', 'tapuz-delivery' ),
				'tapuz_err_message_code' => __( 'Error fetching data from Tapuz. Please check your API Settings on Settings -> Tapuz Delivery', 'tapuz-delivery' ),
				'tapuz_err_message_open_code' => __( 'Error opening shipment in Tapuz. Please check your API Settings on Settings -> Tapuz Delivery', 'tapuz-delivery' )
			));
			wp_enqueue_script( 'tapuz-delivery-admin' );
		}

	}
	/**
	 * Enqueue styles
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles($hook) {
		global $post_type;
		if ( ('post.php' == $hook || 'post-new.php' == $hook) && $post_type == 'shop_order'  ) {
			wp_register_style(
				'tapuz-delivery-admin',
				plugins_url( 'admin/css/tapuz-delivery-admin.css', dirname( __FILE__ ) ),
				null,
				PLUGIN_VERSION
			);
			wp_enqueue_style( 'tapuz-delivery-admin' );
		} elseif ('edit.php' == $hook && $post_type == 'shop_order' ) {
			wp_register_style(
				'tapuz-delivery-table-admin',
				plugins_url( 'admin/css/tapuz-delivery-table-admin.css', dirname( __FILE__ ) ),
				null,
				PLUGIN_VERSION
			);
			wp_enqueue_style( 'tapuz-delivery-table-admin' );
		}
	}
	/**
	 * Register Meta Boxes
	 *
	 * @since 1.0.0
	 */
	public function meta_boxes() {
		add_meta_box(
			'tapuz-delivery',
			__( 'Tapuz Delivery', 'tapuz-delivery' ),
			array( $this, 'tapuz_meta_box_side' ),
			'shop_order',
			'side',
			'high'
		);
	}
	/**
	 * Meta box DOM
	 *
	 * @since 1.0.0
	 */
	public function tapuz_meta_box_side() {
		global $woocommerce, $post;
		$order = wc_get_order( $post->ID );
		if (function_exists ( 'wc_seq_order_number_pro' )){
			$order_id = $post->ID;
		} else {
			$order_id = $order->get_order_number();
		}
		$order_date = new DateTime($order -> order_date);
		$active_date = new DateTime(get_option( 'tapuz_install_date' ));
		$tapuz_label_nonce = wp_create_nonce( "tapuz_create_label" );
		$tapuz_label_query = 'post.php?tapuz_pdf=create&tapuz_label_wpnonce='.$tapuz_label_nonce.'&order_id='.$order_id;
		$tapuz_order_id_db = get_post_meta( $order_id, '_tapuz_ship_data' );
		if (!empty($tapuz_order_id_db)) {
			$tapuz_delivery_number_arry = get_post_meta( $order_id, '_tapuz_ship_data');
			$tapuz_delivery_number = $tapuz_delivery_number_arry[0];
			$tapuz_delivery_time = explode(" ", $tapuz_delivery_number['delivery_time']);
			?>
			<div class="tapuz-wrapper">
				<div id="tapuz_ship_exists" data-order="<?php echo $order_id ?>">
					<h4><?php _e( 'Tapuz shipment details: ', 'tapuz-delivery' ); ?></h4>
					<p><?php _e( 'Shipment number:', 'tapuz-delivery' ); ?> <span class="tapuz_delivery_id"><?php echo $tapuz_delivery_number['delivery_number']; ?></span></p>
					<div class="tapuz_exist_details">
						<p class="tapuz_ship_open"><?php _e( 'Receiver name: ', 'tapuz-delivery' ); ?><span class="tapuz_receiver_name"></span></p>
						<p><?php _e( 'Delivery time:', 'tapuz-delivery' ); ?><span class="tapuz_delivery_time"><?php echo $tapuz_delivery_time[0]; ?></span></p>
						<p><?php _e( 'Delivery status:', 'tapuz-delivery' ); ?><span class="tapuz_delivery_status"></span></p>
						<p class="tapuz_ship_open"><?php _e( 'Shipped on:', 'tapuz-delivery' ); ?><span class="tapuz_shipped_on"></span></p>
						<div class="tapuz-button-container">	
							<a class="tapuz-button tapuz-print-button" target="_blank" data-order="<?php echo $order_id ?>"
						        href="<?php echo $tapuz_label_query ?>"><?php _e( 'Print label', 'tapuz-delivery' ); ?></a>
						</div>
					</div>
				</div>
				<div class="tapuz-powered-by">
					<span>Powered by </span><a target="_blank" href="http://www.hatammy.com/?utm_source=wordpress&utm_medium=plugins&utm_campaign=tapuz-plugin"><img src="<?php echo plugins_url( 'admin/img/hatammy_logo.png', dirname( __FILE__ ) )?>"></a>
				</div>
			</div>

			<?php
		} else {
			?>
				<div class="tapuz-wrapper">
					<div id="tapuz_open_ship">
						<p><?php if ($active_date >= $order_date) { _e( 'Notice: This plugin was installed after this order was made. Please check with Tapuz if you already opened this delivery. ', 'tapuz-delivery' );}  ?></p>
					<div id="tapuz_checkbox">
						<input id="tapuz_urgent" type="checkbox" name="tapuz_urgent" value="urgent"><?php _e( 'Urgent', 'tapuz-delivery' ); ?>
						<input id="tapuz_return" type="checkbox" name="tapuz_return" value="return"><?php _e( 'Return', 'tapuz-delivery' ); ?><br>
					</div>
					<span><?php _e( 'Collect amount: ', 'tapuz-delivery' ); ?></span><input id="tapuz_collect" type="text" name="tapuz_collect" value="<?php _e( 'NO', 'tapuz-delivery' ); ?>"><br>
					<span><?php _e( 'Delivery type:', 'tapuz-delivery' ); ?></span>
					<select id="tapuz_motor">
						<option value="1"><?php _e( 'Scooter', 'tapuz-delivery' ); ?></option>
						<option value="2"><?php _e( 'Car', 'tapuz-delivery' ); ?></option>
					</select><br>
					<span><?php _e( 'Packages:', 'tapuz-delivery' ); ?></span><input id="tapuz_packages" type="number" name="packages" value="1"><br>
					<span><?php _e( 'Deliver on:', 'tapuz-delivery' ); ?></span><input id="tapuz_exaction_date" type="date" name="date" value="<?php echo date('Y-m-d') ?>"><br>
					<div class="tapuz-button-container">
						<button class="tapuz-button tapuz-open-button" data-order="<?php echo $order_id ?>">
							<?php _e( 'Open shipment', 'tapuz-delivery' ); ?></button>
					</div>
				</div>
				<div class="tapuz-success-ship">
					<p><?php _e( 'Shipment number:', 'tapuz-delivery' ); ?><span class="tapuz-success-ship-number"></span></p>
					<div class="tapuz-button-container">
						<a class="tapuz-button tapuz-print-button" target="_blank" data-order="<?php echo $order_id ?>"
						   href="<?php echo $tapuz_label_query ?>"><?php _e( 'Print label', 'tapuz-delivery' ); ?></a>
					</div>
				</div>
				<div class="tapuz-powered-by">
					<span>Powered by </span><a target="_blank" href="http://www.hatammy.com/?utm_source=wordpress&utm_medium=plugins&utm_campaign=tapuz-plugin"><img src="<?php echo plugins_url( 'admin/img/hatammy_logo.png', dirname( __FILE__ ) )?>"></a>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Shop orders columns head
	 *
	 * @since 1.1
	 */
	public function tapuz_admin_column_head($columns){
		$columns['tapuz_delivery_column'] = __( 'Tapuz Delivery', 'tapuz-delivery' );
		return $columns;
	}

	/**
	 * Shop orders columns content
	 *
	 * @since 1.1
	 */
	public function tapuz_admin_column($column, $post_id) {
		$order = wc_get_order( $post_id );
		if (function_exists ( 'wc_seq_order_number_pro' )){
			$order_id = $post_id;
		} else {
			$order_id = $order->get_order_number();
		}
		$tapuz_order_meta = get_post_meta( $order_id, '_tapuz_ship_data' );
		$tapuz_label_nonce = wp_create_nonce( "tapuz_create_label" );
		$tapuz_label_query = 'post.php?tapuz_pdf=create&tapuz_label_wpnonce='.$tapuz_label_nonce.'&order_id='.$order_id;
		if ( $column == 'tapuz_delivery_column' ) {
			if ($tapuz_order_meta) {
				?>
				<div class="tapuz-table-deliv-num">
					<?php _e( 'Delivery number:', 'tapuz-delivery' );?>
					<span><?php echo $tapuz_order_meta[0]['delivery_number'] ?></span>
				</div>
				<div class="tapuz-button-container">
					<a class="tapuz-button tapuz-print-button" target="_blank" data-order="<?php echo $order_id ?>"
				        href="<?php echo $tapuz_label_query ?>"><?php _e( 'Print label', 'tapuz-delivery' ); ?></a>
				</div>
				<?php
			} else {
				?>
				<div class="tapuz-table-deliv-not">
					<?php _e( 'No delivery to display', 'tapuz-delivery' ); ?>
				</div>
				<?php
			}

		}
	}
}
?>