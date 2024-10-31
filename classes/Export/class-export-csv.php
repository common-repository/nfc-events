<?php
/**
 * Creation of CSV file.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Export;

use Nfc\Events\Helpers\Helpers;
use Nfc\Events\Singleton;

/**
 * Class Export_CSV.
 */
class Export_CSV {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_get_tag_urls_csv', [ $this, 'get_tag_urls_csv' ], 10 );
	}

	/**
	 * Ajax response with a CSV file of all
	 * NFC tag urls of a specific post type.
	 *
	 * @return void JSON
	 */
	public function get_tag_urls_csv() {
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

		$post_type = isset( $_POST['postType'] ) ? sanitize_key( $_POST['postType'] ) : null;

		if ( ! $post_type ) {
			echo wp_json_encode(
				[
					'success' => false,
					'result'  => esc_html__( 'Select one of the post types.', 'nfc-events' ),
				],
			);

			wp_die();
		}

		$file_url = Helpers::get_tag_urls( $post_type );

		if ( ! $file_url ) {
			echo wp_json_encode(
				[
					'success' => false,
					'result'  => esc_html__( 'Error, No product with this post type!', 'nfc-events' ),
				],
			);

			wp_die();
		}

		echo wp_json_encode(
			[
				'success' => true,
				'result'  => esc_html__( 'Export complete.', 'nfc-events' ) . ' <a href="' . $file_url . '">' . esc_html__( 'Click here to download the file.', 'nfc-events' ) . '</a>',
			],
		);

		wp_die();
	}
}
