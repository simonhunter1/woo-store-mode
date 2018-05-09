<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://remedyone.com
 * @since      1.0.0
 *
 * @package    Rm_Woo_Store_Mode
 * @subpackage Rm_Woo_Store_Mode/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Rm_Woo_Store_Mode
 * @subpackage Rm_Woo_Store_Mode/includes
 * @author     Simon Hunter <simon@remedyone.come>
 */
class Rm_Woo_Store_Mode {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rm_Woo_Store_Mode_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'RWSM_PLUGIN_VERSION' ) ) {
			$this->version = RWSM_PLUGIN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = RWSM_PLUGIN_NAME;

		// Load Dependencies 
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Rm_Woo_Store_Mode_Loader. Orchestrates the hooks of the plugin.
	 * - Rm_Woo_Store_Mode_i18n. Defines internationalization functionality.
	 * - Rm_Woo_Store_Mode_Admin. Defines all hooks for the admin area.
	 * - Rm_Woo_Store_Mode_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rm-woo-store-mode-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rm-woo-store-mode-i18n.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/functions.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-rm-woo-store-mode-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-rm-woo-store-mode-public.php';
		
		$this->loader = new Rm_Woo_Store_Mode_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Rm_Woo_Store_Mode_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Rm_Woo_Store_Mode_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Rm_Woo_Store_Mode_Admin( $this->get_plugin_name(), $this->get_version() );

		require_once(ABSPATH . 'wp-admin/includes/screen.php');

		// Load Admin  Styles / Scripts
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, array(
			'enqueue_styles',
			'enqueue_scripts'
			) );

		// Add Settings Page
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'settings_page' );

		$this->loader->add_action('init', $plugin_admin, array(
			'register_modes', // Register Taxonomy for Modes 
			'save_options' // Save Plugin Options
			) );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Rm_Woo_Store_Mode_Public( $this->get_plugin_name(), $this->get_version() );

		// Enqueue Scripts
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, array(
			'enqueue_styles',
			'enqueue_scripts'
			) );

		$this->loader->add_action( 'woocommerce_after_shop_loop_item', $plugin_public, 'rm_mode_after_shop_loop_item' );

		$this->loader->add_action( 'pre_get_posts', $plugin_public, 'current_mode_products' );

		// Change Checkout Content
		$this->loader->add_filter( 'the_content', $plugin_public, 'checkout' );

		// Store Mode Shortcode 
		$this->loader->add_shortcode( 'wsm-store-mode-change', $plugin_public, 'shortcode' );

		// Display Mode Filter
		$this->loader->add_action('woocommerce_before_shop_loop', $plugin_public, 'filter', 30 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Rm_Woo_Store_Mode_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}


}
