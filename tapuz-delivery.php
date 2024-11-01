<?php

/**
 * The plugin bootstrap file
 *
 * @link              http://hatammy.com
 * @since             1.0.0
 * @package           Tapuz_Delivery
 *
 * @wordpress-plugin
 * Plugin Name:       Tapuz Delivery
 * Plugin URI:        http://hatammy.com
 * Description:       A plugin for Tapuz Delivery orders from within WooCommerce.
 * Version:           1.1.1
 * Author:            HATAMMY digital marketing
 * Author URI:        http://hatammy.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tapuz-delivery
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-tapuz-delivery.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
define('PLUGIN_NAME', 'tapuz-delivery');
define('PLUGIN_VERSION', '1.1.1');
//Don't forget to put / in the end of the URL!
define('TAPUZ_DEFAULT_URL', 'http://62.0.106.40/baldarws_c/Service.asmx/');
define('TAPUZ_GET_ALL', 'GetCustomerRecords');
define('TAPUZ_GET_BY_ID', 'ListDeliveryDetails');
define('TAPUZ_SAVE_NEW', 'SaveData');
define('TAPUZ_CHANGE_STATUS', 'UpdateDeliveryStatus');

register_activation_hook( __FILE__, 'activate_tapuz_delivery' );

/**
 * store the plugin install date
 */
function activate_tapuz_delivery() {
	$tapuz_install_date_db = get_option( 'tapuz_install_date' );
	if(empty( $tapuz_install_date_db ) ) {
		add_option('tapuz_install_date', date("d-m-Y") );
	} else {
		update_option('tapuz_install_date', date("d-m-Y"));
	}
}

/**
 * Run plugin
 */
function run_tapuz_delivery() {
	$plugin = new Tapuz_Delivery();
	$plugin->run();
}

/**
 * Check if WooCommerce is installed
 */
if (in_array('woocommerce/woocommerce.php', get_option('active_plugins'))) {
	run_tapuz_delivery();
} else {
	add_action( 'admin_notices', 'tapuz_woo_error_notice' );
}

/**
 * Add link to plugin list
 */
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'tapuz_action_links' );

function tapuz_action_links( $links ) {
	$links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=tapuz-delivery-options') ) .'">'.__( 'Settings', 'tapuz-delivery' ).'</a>';
	$links[] = '<a href="http://www.hatammy.com/?utm_source=wordpress&utm_medium=plugins&utm_campaign=tapuz-plugin" target="_blank">'.__( 'Who is HATAMMY', 'tapuz-delivery' ).'</a>';
	$links[] = '<a href="http://www.hatammy.com/plugins/tapuz-delivery/?utm_source=wordpress&utm_medium=plugins&utm_campaign=tapuz-plugin" target="_blank">'.__( 'Installation instructions', 'tapuz-delivery' ).'</a>';
	return $links;
}

/**
 * Error message if WooCommerce is not installed
 */
function tapuz_woo_error_notice() {
	?>
	<div class="error notice">
		<p><?php _e( 'WooCommerce is not active. Please activate plugin before using Tapuz Delivery plugin.', 'tapuz-delivery' ); ?></p>
	</div>
	<?php
}



