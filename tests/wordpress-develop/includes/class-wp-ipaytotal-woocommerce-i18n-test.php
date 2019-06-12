<?php
/**
 * Tests for WP_IPayTotal_WooCommerce_I18n. Tests load_plugin_textdomain.
 *
 * @package wp-ipaytotal-woocommerce
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

/**
 * Class WP_IPayTotal_WooCommerce_I18n_Test
 */
class WP_IPayTotal_WooCommerce_I18n_Test extends WP_UnitTestCase {

	/**
	 * AFAICT, this will fail until a translation has been added.
	 *
	 * @see load_plugin_textdomain()
	 * @see https://gist.github.com/GaryJones/c8259da3a4501fd0648f19beddce0249
	 */
	public function test_wowp_iptwpg_load_plugin_textdomain() {

		$this->markTestSkipped( 'Needs translation.' );

		global $plugin_root_dir;

		$this->assertTrue( file_exists( $plugin_root_dir . '/languages/' ) );

		// Seems to fail because there are no translations to load.
		$this->assertTrue( is_textdomain_loaded( 'wp-ipaytotal-woocommerce' ) );

	}

}
