<?php
/**
 * Enqueue frontend scripts and stylesheets.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Enqueue;

use Nfc\Events\Singleton;

/**
 * Class Enqueue_Front.
 */
class Enqueue_Front {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );
	}

	/**
	 * Enqueue styles and scripts.
	 *
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_style( 'nfc-events', NFC_EVENTS_URL . 'build/main-style.css', [], NFC_EVENTS_VERSION );
		wp_enqueue_script( 'nfc-events', NFC_EVENTS_URL . 'build/main-script.js', [ 'jquery' ], NFC_EVENTS_VERSION, false );

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
