<?php
/**
 * Enqueue admin stylesheets and scripts.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Enqueue;

use Nfc\Events\Singleton;

/**
 * Class Enqueue_Admin.
 */
class Enqueue_Admin {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin' ] );
	}

	/**
	 * Enqueue admin styles and scripts.
	 *
	 * @return void
	 */
	public function enqueue_admin() {
		wp_enqueue_style( 'nfc-events', NFC_EVENTS_URL . 'build/admin-style.css', [], NFC_EVENTS_VERSION );
		wp_enqueue_script( 'nfc-events', NFC_EVENTS_URL . 'build/admin-script.js', [ 'jquery' ], NFC_EVENTS_VERSION, false, true );

		wp_localize_script(
			'nfc-events',
			'ajax',
			[
				'url'   => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'ajax-nonce' ),
			]
		);
	}
}
