<?php

/**
 * Class Tapuz_Delivery
 *
 * This is the plugin core class
 *
 */
class Tapuz_Delivery {

	public $tapuz_admin_view;

	public $tapuz_ajax;

	public function __construct() {
		$this->load_dependencies();
		$this->tapuz_admin_view = new Tapuz_admin_view();
		$this->tapuz_ajax = new Tapuz_ajax();
	}
	/**
	 * Load dependencies
	 * @since 1.0.0
	 *
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tapuz-admin-view.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/tapuz-woocommerce-settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-tapuz-ajax.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'labels/tapuz-create-label.php';

	}

	/**
	 * Set the admin WordPress hooks
	 * @since 1.0.0
	 *
	 */
	private function set_admin_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this->tapuz_admin_view, 'enqueue_styles' ));
		add_action( 'admin_enqueue_scripts',  array( $this->tapuz_admin_view, 'enqueue_scripts' ));
		add_action( 'admin_menu', 'register_tapuz_menu' );
		add_action( 'add_meta_boxes', array( $this->tapuz_admin_view, 'meta_boxes' ));
		add_action( 'wp_ajax_tapuz_open_new_order', array( $this->tapuz_ajax, 'tapuz_open_new_order' ));
		add_action( 'wp_ajax_tapuz_get_order_details', array( $this->tapuz_ajax, 'tapuz_get_order_details' ));
		add_action( 'wp_ajax_tapuz_change_order_status', array( $this->tapuz_ajax, 'tapuz_change_order_status' ));
		add_action( 'wp_ajax_tapuz_reopen_ship', array( $this->tapuz_ajax, 'tapuz_reopen_ship' ));
		add_action('plugins_loaded', array($this,'tapuz_plugins_loaded'));
		add_action( 'manage_shop_order_posts_custom_column' , array( $this->tapuz_admin_view, 'tapuz_admin_column' ), 3,2 );
	}

	/**
	 * Set the admin WordPress filters
	 * @since 1.1
	 *
	 */
	private function set_admin_filters() {
		add_filter( 'manage_shop_order_posts_columns',  array($this->tapuz_admin_view,'tapuz_admin_column_head'),50 );
	}

	/**
	 * Init method after class is created
	 * @since 1.0.0
	 *
	 */
	public function run() {
		load_plugin_textdomain( PLUGIN_NAME, null, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages');
		$this->set_admin_hooks();
		$this->set_admin_filters();
	}

	/**
	 * Listen to GET request for labels
	 * @since 1.0.0
	 *
	 */
	public function tapuz_plugins_loaded() {
		global $pagenow;
		if ($pagenow =='post.php' &&  isset($_GET['tapuz_pdf']) && $_GET['tapuz_pdf']=='create') {
			if (!wp_verify_nonce($_GET['tapuz_label_wpnonce'], 'tapuz_create_label' ) ) die( 'Failed security check' );
			$ship_data = get_post_meta( $_GET['order_id'], '_tapuz_ship_data' );
			$ship_data[0]['collect_company'] = get_option('tapuz_collect_company_name');
			$ship_data[0]['collect_street'] = get_option('tapuz_collect_street_name');
			$ship_data[0]['collect_street_number'] = get_option('tapuz_collect_street_number');
			$ship_data[0]['collect_city'] = get_option('tapuz_collect_city_name');

			$pdf = new Tapuz_create_label($ship_data);
			$tapuz_paper_size = get_option('tapuz_paper_size');
			if ($tapuz_paper_size == '1904'){
				$pdf->create_dymo_label();
			} elseif ($tapuz_paper_size == 'A4-logo' ){
				$tapuz_logo_url_db = get_option( 'tapuz_logo_url' );
				if (empty($tapuz_logo_url_db)){
					$pdf->create_a4_label();
				} else {
					$pdf->create_a4_label_logo(get_option('tapuz_logo_url'));
				}
			} elseif ($tapuz_paper_size == 'A4'){
				$pdf->create_a4_label();
			}
			exit();
		}
	}
}
