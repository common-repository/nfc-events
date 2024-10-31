<?php
/**
 * Admin plugins list page actions.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Admin;

use Nfc\Events\Singleton;

/**
 * Class Admin_Links.
 */
class Admin_Links {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'plugin_action_links_' . NFC_EVENTS_BASENAME, [ $this, 'plugin_action_links' ], 10, 1 );
	}

	/**
	 * Adds plugin action links within admin plugins page.
	 *
	 * @param array $links Action links.
	 *
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		$settings_url = admin_url( 'admin.php?page=nfc-events-admin-settings' );
		$docs_url     = 'https://www.notion.so/maksimer/User-Documentation-ca46aaab9f6b46688aad07d75cade470';

		$links[] = '<a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Settings', 'nfc-events' ) . '</a>';
		$links[] = '<a href="' . esc_url( $docs_url ) . '" target="_blank">' . esc_html__( 'Documentation', 'nfc-events' ) . '</a>';

		return $links;
	}
}
