<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://remedyone.com
 * @since      1.0.0
 *
 * @package    Rm_Woo_Store_Mode
 * @subpackage Rm_Woo_Store_Mode/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rm_Woo_Store_Mode
 * @subpackage Rm_Woo_Store_Mode/admin
 * @author     Simon Hunter <simon@remedyone.come>
 */
class Rm_Woo_Store_Mode_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		
		// Prevent Loading Scripts on other Admin Pages
		if( ! is_woo_store_options_page() ) {
			return; 
		}

		wp_enqueue_style( 'jquery-ui', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rm-woo-store-mode-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
		// Prevent Loading Scripts on other Admin Pages
		if( ! is_woo_store_options_page() ) {
			return; 
		}

		wp_enqueue_script( 'wp-util' );
		wp_enqueue_script( 'jquery-ui-timepicker-addon', plugin_dir_url( __FILE__ ) . 'js/jquery-ui-timepicker-addon.js', array( 'jquery-ui-datepicker', 'jquery-ui-slider' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rm-woo-store-mode-admin.js', array( 'wp-util' ), $this->version, true );

	}

	/**
	 * Register Page Menu 
	 *
	 * @since 	1.0.0
	 */

	public function settings_page() {
		add_submenu_page( 
			'woocommerce', 
			'Woo Store Mode', 
			'Woo Store Mode', 
			'manage_options', 
			$this->plugin_name,
			array( $this, 'store_menu_callback' )
		);
	}

	/**
	 * Callback to show Woo Store Menu contents. 
	 *
	 * @since 	1.0.0
	 */

	public function store_menu_callback() {
		
		$modes = self::get_modes();
		$options = rm_get_options();
		$page_id = 'woocommerce_page_'.$this->plugin_name;
		$plugin_name = $this->plugin_name;
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/admin/partials/rm-woo-store-mode-admin-display.php';
	}

	/**
	 * Create a Modes Taxonomy 
	 */
	public function register_modes() {
		
		$labels = array(
			'name'					=> _x( 'Modes', 'Modes', 'rm-woo-store-mode' ),
			'singular_name'			=> _x( 'Mode', 'Taxonomy Mode', 'rm-woo-store-mode' ),
			'search_items'			=> __( 'Search Mode', 'rm-woo-store-mode' ),
			'popular_items'			=> __( 'Popular Mode', 'rm-woo-store-mode' ),
			'all_items'				=> __( 'All Mode', 'rm-woo-store-mode' ),
			'parent_item'			=> __( 'Parent Mode', 'rm-woo-store-mode' ),
			'parent_item_colon'		=> __( 'Parent Mode', 'rm-woo-store-mode' ),
			'edit_item'				=> __( 'Edit Mode', 'rm-woo-store-mode' ),
			'update_item'			=> __( 'Update Mode', 'rm-woo-store-mode' ),
			'add_new_item'			=> __( 'Add New Mode', 'rm-woo-store-mode' ),
			'new_item_name'			=> __( 'New Mode Name', 'rm-woo-store-mode' ),
			'add_or_remove_items'	=> __( 'Add or remove Mode', 'rm-woo-store-mode' ),
			'choose_from_most_used'	=> __( 'Choose from most used rm-woo-store-mode', 'rm-woo-store-mode' ),
			'menu_name'				=> __( 'Mode', 'rm-woo-store-mode' ),
		);
	
		$args = array(
			'labels'            => apply_filters( 'rm_mode_labels', $labels ),
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'hierarchical'      => false,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => true,
			'rewrite'           => true,
			'query_var'         => true,
			'capabilities'      => array(),
		);
	
		register_taxonomy( 'rm-mode', array( 'product' ), $args );
		
	}

	public function save_options() {
		if( ! isset( $_POST['save_rm_woo_store_options'] ) ) {
			return;
		}

		if( 'Save' != $_POST['save_rm_woo_store_options'] ) {
			return;
		}

		if( ! wp_verify_nonce( $_POST['_wpnonce'], 'rm_woo_store_nonce' ) ) {
			die();
		}

		$default_modes = self::default_modes();
		$posted_data = $_POST;
		$options = rm_array_merge_recursive_distinct( $default_modes, $posted_data );

		update_option( $this->plugin_name . '-options', $options );
	}

	public static function default_modes() {
		$defaults = array();
		foreach( (array) self::get_modes() as $mode ) {
			$defaults['modes'][ $mode->slug ]['open'] = '00:00';
			$defaults['modes'][ $mode->slug ]['close'] = '00:00';
			$defaults['modes'][ $mode->slug ]['status'] = null;
		}
		return $defaults; 
	}

	public static function get_modes() {
		return get_terms( array('taxonomy' => 'rm-mode', 'hide_empty' => false ) );
	}

}


