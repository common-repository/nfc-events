<?php
/**
 * Meta boxes for posts that have relations with created events.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Products;

use Nfc\Events\Singleton;

/**
 * Class Products_Meta_Boxes.
 */
class Products_Meta_Boxes {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Events log
	 *
	 * @var object
	 */
	private $events_log;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'add_meta_boxes', [ $this, 'meta_boxes' ] );
	}

	/**
	 * Adds meta boxes to all posts except nfc related ones.
	 *
	 * @return void
	 */
	public function meta_boxes() {
		global $post;

		if ( ! isset( $post->post_type ) ) {
			return;
		}

		if ( in_array( $post->post_type, [ 'nfc_events', 'nfc_event_statuses', 'nfc_pages' ], true ) ) {
			return;
		}

		$paged = filter_input( INPUT_GET, 'nfc_page', FILTER_SANITIZE_ENCODED ) ? filter_input( INPUT_GET, 'nfc_page', FILTER_SANITIZE_ENCODED ) : 1;

		$this->events_log = new \WP_Query(
			[
				'post_type'      => 'nfc_events',
				'post_status'    => 'publish',
				'posts_per_page' => 6,
				'meta_key'       => '_nfc_post_id',
				'meta_value'     => $post->ID,
				'paged'          => $paged,
			]
		);

		if ( ! $this->events_log ) {
			return;
		}

		add_meta_box(
			'nfc-events-log',
			esc_html__( 'Events Log', 'nfc-events' ),
			[ $this, 'post_events_log' ],
			$post->post_type
		);
	}

	/**
	 * Event posts query loop.
	 *
	 * @param object $post WP_Post.
	 *
	 * @return void
	 */
	public function post_events_log( $post ) {
		$template_args = [
			'query' => $this->events_log,
			'paged' => filter_input( INPUT_GET, 'nfc_page', FILTER_SANITIZE_ENCODED ) ? filter_input( INPUT_GET, 'nfc_page', FILTER_SANITIZE_ENCODED ) : 1,
		];

		load_template( NFC_EVENTS_TEMPLATE_PATH . 'nfc-events-log-loop.php', false, $template_args );
	}
}
