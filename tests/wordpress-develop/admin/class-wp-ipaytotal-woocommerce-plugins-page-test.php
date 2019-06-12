<?php
/**
 * Tests for WP_IPayTotal_WooCommerce_Plugins_Page. Tests the settings link is correctly added on plugins.php.
 *
 * @package wp-ipaytotal-woocommerce
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

/**
 * Class WP_IPayTotal_WooCommerce_Plugins_Page
 */
class WP_IPayTotal_WooCommerce_Plugins_Page_Test extends WP_UnitTestCase {

	/**
	 * Verify the content of the action links.
	 *
	 * TODO: The Deactivate link isn't returned when the filter is run in the test, suggesting it's
	 * not being run on plugins.php page as it should.
	 *
	 * phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	 * phpcs:disable WordPress.PHP.NoSilencedErrors.Discouraged
	 */
	public function test_plugin_action_links() {

		$expected_anchor    = get_site_url() . '/wp-admin/admin.php?page=wc-settings&tab=checkout';
		$expected_link_text = 'Settings';

		global $plugin_root_dir;

		$plugin_basename = 'wp-ipaytotal-woocommerce/wp-ipaytotal-woocommerce.php';

		$filter_name = 'plugin_action_links_' . $plugin_basename;

		$this->go_to( '/wp-admin/plugins.php' );

		$plugin_action_links = apply_filters( $filter_name, array() );

		$this->assertGreaterThan( 0, count( $plugin_action_links ), 'The plugin action link was definitely not added.' );

		$first_link = $plugin_action_links[0];

		$dom = new DOMDocument();

		@$dom->loadHtml( mb_convert_encoding( $first_link, 'HTML-ENTITIES', 'UTF-8' ) );

		$nodes = $dom->getElementsByTagName( 'a' );

		$this->assertEquals( 1, $nodes->length );

		$node = $nodes->item( 0 );

		$actual_anchor    = $node->getAttribute( 'href' );
		$actual_link_text = $node->nodeValue;

		$this->assertEquals( $expected_anchor, $actual_anchor );
		$this->assertEquals( $expected_link_text, $actual_link_text );
	}

}
