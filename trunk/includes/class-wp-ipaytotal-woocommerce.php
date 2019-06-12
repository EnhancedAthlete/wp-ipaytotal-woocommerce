<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://ipaytotal.com/
 * @since      3.0.0
 *
 * @package    WP_IPayTotal_WooCommerce
 * @subpackage WP_IPayTotal_WooCommerce/includes
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
 * @since      3.0.0
 * @package    WP_IPayTotal_WooCommerce
 * @subpackage WP_IPayTotal_WooCommerce/includes
 * @author     iPayTotal, BrianHenryIE <support@ipaytotal.com>
 */
class WP_IPayTotal_WooCommerce {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    3.0.0
	 * @access   protected
	 * @var      WP_IPayTotal_WooCommerce_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    3.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The path to the root folder of the plugin.
	 *
	 * @since    3.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The filesystem path to the root of this plugin.
	 */
	protected $plugin_root_dir;

	/**
	 * The plugin's root path and filename. Use in place of `plugin_basename` which returns the path of the current file.
	 *
	 * @see plugin_basename()
	 *
	 * @since    3.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The plugin's root path and filename.
	 */
	protected $plugin_basename;

	/**
	 * The current version of the plugin.
	 *
	 * @since    3.0.0
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
	 * @since    3.0.0
	 */
	public function __construct() {
		if ( defined( 'WP_IPAYTOTAL_WOOCOMMERCE_VERSION' ) ) {
			$this->version = WP_IPAYTOTAL_WOOCOMMERCE_VERSION;
		} else {
			$this->version = '3.0.0';
		}
		$this->plugin_name = 'wp-ipaytotal-woocommerce';

		$this->plugin_root_dir = dirname( dirname( __FILE__ ) );

		$this->plugin_basename = ltrim( $this->plugin_root_dir . '/' . $this->plugin_name . '.php', '/' );

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
	 * - WP_IPayTotal_WooCommerce_Loader. Orchestrates the hooks of the plugin.
	 * - WP_IPayTotal_WooCommerce_I18n. Defines internationalization functionality.
	 * - WP_IPayTotal_WooCommerce_Admin. Defines all hooks for the admin area.
	 * - WP_IPayTotal_WooCommerce_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    3.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-ipaytotal-woocommerce-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-ipaytotal-woocommerce-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-ipaytotal-woocommerce-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-ipaytotal-woocommerce-plugins-page.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-ipaytotal-woocommerce-public.php';

		$this->loader = new WP_IPayTotal_WooCommerce_Loader();

	}

	/**
	 * A publicly accessible reference to the i18n object, e.g. for removing hooks.
	 *
	 * @var WP_IPayTotal_WooCommerce_I18n The i18n object.
	 */
	public $i18n;

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WP_IPayTotal_WooCommerce_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    3.0.0
	 * @access   private
	 */
	private function set_locale() {

		$this->i18n = new WP_IPayTotal_WooCommerce_I18n();

		$this->loader->add_action( 'plugins_loaded', $this->i18n, 'load_plugin_textdomain' );

	}

	/**
	 * A publicly accessible reference to the plugins page object, e.g. for removing hooks.
	 *
	 * @var WP_IPayTotal_WooCommerce_Plugins_Page The object used for the plugins page.
	 */
	public $plugins_page;

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WP_IPayTotal_WooCommerce_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->plugins_page = new WP_IPayTotal_WooCommerce_Plugins_Page( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter( 'plugin_action_links_' . $this->plugin_basename, $this->plugins_page, 'plugin_action_links' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new WP_IPayTotal_WooCommerce_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    3.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     3.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     3.0.0
	 * @return    WP_IPayTotal_WooCommerce_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     3.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
