<?php
/**
 * Resources related AJAX callbacks.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Resources;

use Nfc\Events\{
	Singleton,
	Helpers\Helpers,
};

/**
 * Class Resources_Ajax.
 */
class Resources_Ajax {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_delete_resource', [ $this, 'delete_resource' ], 10 );
	}

	/**
	 * Ajax response for deleting an resource.
	 *
	 * @return void JSON
	 */
	public function delete_resource() {
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

		$file      = isset( $_POST['file'] ) ? sanitize_key( $_POST['file'] ) : null;
		$directory = isset( $_POST['directory'] ) ? sanitize_key( $_POST['directory'] ) : null;

		if ( ! $directory || ! $file ) {
			echo wp_json_encode(
				[
					'success' => false,
					'result'  => esc_html__( 'No file path!', 'nfc-events' ),
				],
			);

			wp_die();
		}

		$delete = Helpers::delete_resource( $directory, $file );

		if ( ! $delete ) {
			echo wp_json_encode(
				[
					'success' => false,
					'result'  => esc_html__( 'Something went wrong! Please try later again.', 'nfc-events' ),
				],
			);

			wp_die();
		}

		echo wp_json_encode(
			[
				'success' => true,
				'result'  => $delete,
			],
		);

		wp_die();
	}
}
