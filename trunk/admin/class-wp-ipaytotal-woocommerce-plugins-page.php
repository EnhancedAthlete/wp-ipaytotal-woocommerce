<?php
/**
 * The plugin page output of the plugin.
 *
 * @link       https://ipaytotal.com/c
 * @since      3.0.0
 *
 * @package    WP_IPayTotal_WooCommerce
 * @subpackage WP_IPayTotal_WooCommerce/admin
 */

/**
 * This class adds a `Settings` link on the plugins.php page.
 *
 * @package    WP_IPayTotal_WooCommerce
 * @subpackage WP_IPayTotal_WooCommerce/admin
 * @author     iPayTotal, BrianHenryIE <support@ipaytotal.com>
 */
class WP_IPayTotal_WooCommerce_Plugins_Page {

	/**
	 * The ID of this plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    3.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    3.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Add link to settings page in plugins.php list.
	 *
	 * @param array $links The existing plugin links (usually "Deactivate").
	 *
	 * @return array The links to display below the plugin name on plugins.php.
	 */
	public function plugin_action_links( $links ) {

		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout' ) . '">' . __( 'Settings', 'wp-ipaytotal-woocommerce' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

}
