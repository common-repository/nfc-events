<?php
/**
 * Events related AJAX callbacks.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Events;

use Nfc\Events\{
	Singleton,
	Helpers\Helpers,
};

/**
 * Class Events_Ajax.
 */
class Events_Ajax {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_set_product_event', [ $this, 'set_product_event' ], 10 );
	}

	/**
	 * Ajax response for setting up a post event.
	 *
	 * @return void JSON
	 */
	public function set_product_event() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_key( $_POST['nonce'] ) : null;

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {

			echo wp_json_encode(
				[
					'success' => false,
					'result'  => esc_html__( 'No nonce!', 'nfc-events' ),
				],
			);

			wp_die();
		}

		$event_id = isset( $_POST['form_data']['nfc_events_event'] ) ? sanitize_key( $_POST['form_data']['nfc_events_event'] ) : null;
		$note     = isset( $_POST['form_data']['nfc_events_note'] ) ? sanitize_key( $_POST['form_data']['nfc_events_note'] ) : null;

		if ( ! $event_id ) {
			echo wp_json_encode(
				[
					'success' => false,
					'result'  => esc_html__( 'Select one of the events.', 'nfc-events' ),
				],
			);

			wp_die();
		}

		$set_product_event = Helpers::set_product_event(
			isset( $_POST['form_data']['nfc_events_product_id'] ) ? sanitize_key( $_POST['form_data']['nfc_events_product_id'] ) : null,
			isset( $_POST['form_data']['nfc_events_product_unique'] ) ? sanitize_key( $_POST['form_data']['nfc_events_product_unique'] ) : null,
			$event_id,
			$note,
			isset( $_FILES['nfc_events_attachment_images'] ) ? sanitize_file_name( $_FILES['nfc_events_attachment_images'] ) : null,
		);

		if ( ! $set_product_event ) {
			echo wp_json_encode(
				[
					'success' => false,
					'result'  => esc_html__( 'Something went wrong! Please try later again.', 'nfc-events' ),
				],
			);

			wp_die();
		}

		if ( isset( $set_product_event['error'] ) ) {
			echo wp_json_encode(
				[
					'success' => false,
					'result'  => $set_product_event['error'],
				],
			);

			wp_die();
		}

		echo wp_json_encode(
			[
				'success' => true,
				'result'  => esc_html__( 'Thank you, the event is received!', 'nfc-events' ),
			],
		);

		wp_die();
	}
}
