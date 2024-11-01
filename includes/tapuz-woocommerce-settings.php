<?php
/**
 * Create a sub menu in WordPress settings menu
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function register_tapuz_menu() {

	add_options_page(
		__( 'Tapuz Delivery', 'tapuz-delivery' ),
		__( 'Tapuz Delivery', 'tapuz-delivery' ),
		'manage_options',
		'tapuz-delivery-options',
		'tapuz_plugin_options'
	);
}

function tapuz_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'tapuz-delivery' ) );
	}
	if ( isset( $_POST['tapuz_fields_submitted'] ) && $_POST['tapuz_fields_submitted'] == 'submitted' ) {
		$retrieved_nonce = $_REQUEST['_wpnonce'];
		if (!wp_verify_nonce($retrieved_nonce, 'submit_tapuz_settings' ) ) die( 'Failed security check' );
		foreach ( $_POST as $key => $value ) {
			if ($key == 'tapuz_service_url') {
				if (empty($value)) {
					update_option( $key, TAPUZ_DEFAULT_URL);
				} elseif (substr($value, -1) != '/'){
					$with_slash = $value .'/';
					update_option( $key, $with_slash );
				} else {
					update_option( $key, $value );
				}
			}elseif ( get_option( $key ) != $value ) {
				update_option( $key, $value );

			} else {
				add_option( $key, $value, '', 'no' );
			}
		}
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php _e( 'Your settings have been saved.', 'tapuz-delivery' ); ?></p>
		</div>
	<?php } ?>
	<div class="wrap">
	<h1><?php _e( 'Tapuz Delivery Integration Settings', 'tapuz-delivery' ) ?></h1>
	<form class="tapuz-setting-form" method="POST">
		<h2><?php _e( 'API Settings:', 'tapuz-delivery' ) ?></h2>
		<?php wp_nonce_field('submit_tapuz_settings'); ?>
		<span><?php _e( 'Service URL: ', 'tapuz-delivery' ) ?></span>
			<input type="url" name="tapuz_service_url" style="width: 40%; max-width: 470px;" value="<?php if (!get_option( 'tapuz_service_url' )){echo TAPUZ_DEFAULT_URL; } else { echo get_option( 'tapuz_service_url' ); } ?>"><br><br>
		<span><?php _e( 'Customer code: ', 'tapuz-delivery' ) ?></span>
			<input type="text" name="tapuz_customer_code" value="<?php echo get_option('tapuz_customer_code')?>"><br><br>
		<span><?php _e( 'Username: ', 'tapuz-delivery' ) ?></span>
			<input type="text" name="tapuz_username" value="<?php echo get_option('tapuz_username')?>"><br><br>
		<span><?php _e( 'Password: ', 'tapuz-delivery' ) ?></span>
			<input type="password" name="tapuz_password" value="<?php echo get_option('tapuz_password')?>"><br><br>
		<input type="hidden" name="tapuz_fields_submitted" value="submitted">
		<h2><?php _e( 'Shipping Label Settings:', 'tapuz-delivery' ) ?></h2>
		<span><?php _e( 'Label size: ', 'tapuz-delivery' ) ?></span>
		<select name="tapuz_paper_size" id="tapuz_paper_size">
			<option value="A4" <?php if (get_option('tapuz_paper_size') == 'A4') echo 'selected';?> >A4</option>
			<option value="1904" <?php if (get_option('tapuz_paper_size') == '1904') echo 'selected';?>>DYMO 99014</option>
			<option value="A4-logo" <?php if (get_option('tapuz_paper_size') == 'A4-logo') echo 'selected';?>>A4 with logo</option>
		</select><br><br>
		<div id="tapuz_logo_url" style="display: none">
		<span><?php _e( 'Logo URL: ', 'tapuz-delivery' ) ?></span>
			<input id="tapuz_logo_input" type="url" name="tapuz_logo_url" style="width: 40%; max-width: 470px;" value="<?php  echo get_option( 'tapuz_logo_url' ) ?>"><br><br>
		</div>
		<h2><?php _e( 'Delivery Settings - collect from address:', 'tapuz-delivery' ) ?></h2>
		<span><?php _e( 'Street name: ', 'tapuz-delivery' ) ?></span>
			<input type="text" name="tapuz_collect_street_name" value="<?php echo get_option('tapuz_collect_street_name')?>">
		<span><?php _e( 'House number: ', 'tapuz-delivery' ) ?></span>
			<input type="text" name="tapuz_collect_street_number" value="<?php echo get_option('tapuz_collect_street_number')?>"><br><br>
		<span><?php _e( 'City name: ', 'tapuz-delivery' ) ?></span>
			<input type="text" name="tapuz_collect_city_name" value="<?php echo get_option('tapuz_collect_city_name')?>"><br><br>
		<span><?php _e( 'Company name: ', 'tapuz-delivery' ) ?></span>
			<input type="text" name="tapuz_collect_company_name" style="width: 30%; max-width: 370px;" value="<?php echo get_option('tapuz_collect_company_name')?>"><br><br>
		<?php submit_button(__( 'submit', 'tapuz-delivery'  ), 'primary');?>
		<div style="float: left; direction: ltr">
			<span style="font-size: 20px;">Powered by </span><a  target="_blank" href="http://www.hatammy.com/?utm_source=wordpress&utm_medium=plugins&utm_campaign=tapuz-plugin"><img style="height: 35px; vertical-align: middle;" src="<?php echo plugins_url( 'admin/img/hatammy_logo.png', dirname( __FILE__ ) )?>"></a>
			<span style="font-size: 20px;">Digital Marketing.</span>
		</div>
		</div>
		<script type="text/javascript">
			window.onload = function() {
				var eSelect = document.getElementById('tapuz_paper_size');
				var optOtherReason = document.getElementById('tapuz_logo_url');
				if(eSelect.selectedIndex === 2) {
					optOtherReason.style.display = 'block';
				} else {
					optOtherReason.style.display = 'none';
				}
				eSelect.onchange = function() {
					if(eSelect.selectedIndex === 2) {
						optOtherReason.style.display = 'block';
					} else {
						document.getElementById('tapuz_logo_input').value = "";
						optOtherReason.style.display = 'none';
					}
				}
			}
		</script>
		<?php

}
