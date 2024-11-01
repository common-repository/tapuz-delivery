<?php

/**
 * Fired when the plugin is uninstalled.

 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option('tapuz_service_url');
delete_option('tapuz_customer_code');
delete_option('tapuz_username');
delete_option('tapuz_password');
delete_option('tapuz_paper_size');
delete_option('tapuz_collect_street_name');
delete_option('tapuz_collect_street_number');
delete_option('tapuz_collect_city_name');
delete_option('tapuz_collect_company_name');
delete_option( 'tapuz_logo_url' );
