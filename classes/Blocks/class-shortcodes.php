<?php
/**
 * Shortcodes general setup.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Blocks;

use Nfc\Events\{
	Singleton,
	Helpers\Helpers_User,
};

/**
 * Class Shortcodes.
 */
class Shortcodes {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_shortcode( 'nfc_product_image', [ $this, 'product_image' ] );
		add_shortcode( 'nfc_product_title', [ $this, 'product_title' ] );
		add_shortcode( 'nfc_product_event', [ $this, 'event_form' ] );
		add_shortcode( 'nfc_events_log', [ $this, 'events_log' ] );
	}

	/**
	 * Displays post/product feature image.
	 *
	 * @return html
	 */
	public function product_image() {
		$product_id  = ( isset( $_GET['nfc_nonce'], $_GET['nfc_product_id'] ) && wp_verify_nonce( sanitize_key( $_GET['nfc_nonce'] ), 'nfc_nonce_' . sanitize_text_field( wp_unslash( $_GET['nfc_product_id'] ) ) ) ) ? sanitize_text_field( wp_unslash( $_GET['nfc_product_id'] ) ) : '';
		$product_img = apply_filters( 'nfc_events_product_image', get_the_post_thumbnail_url( $product_id, 'woocommerce_single' ), $product_id );
		$product_img = $product_img ? $product_img : NFC_EVENTS_URL . 'assets/images/placeholder.png';

		ob_start();

		$template_args = [
			'post_id' => $product_id,
			'image'   => $product_img,
		];

		load_template( NFC_EVENTS_TEMPLATE_PATH . 'nfc-product-image.php', false, $template_args );

		return ob_get_clean();
	}

	/**
	 * Displays post/product title.
	 *
	 * @return html
	 */
	public function product_title() {
		$product_id    = ( isset( $_GET['nfc_nonce'], $_GET['nfc_product_id'] ) && wp_verify_nonce( sanitize_key( $_GET['nfc_nonce'] ), 'nfc_nonce_' . sanitize_text_field( wp_unslash( $_GET['nfc_product_id'] ) ) ) ) ? sanitize_text_field( wp_unslash( $_GET['nfc_product_id'] ) ) : '';
		$product_title = $product_id ? apply_filters( 'nfc_events_product_title', get_the_title( $product_id ), $product_id ) : ( ! is_page() ? esc_html__( 'Product title', 'nfc-events' ) : '' );

		ob_start();

		$template_args = [
			'post_id' => $product_id,
			'title'   => $product_title,
		];

		load_template( NFC_EVENTS_TEMPLATE_PATH . 'nfc-product-title.php', false, $template_args );

		return ob_get_clean();
	}

	/**
	 * Displays form for choosing post/product NFC event.
	 *
	 * @param array $attributes Attributes.
	 *
	 * @return html
	 */
	public function event_form( $attributes = null ) {
		if ( ! is_singular( 'nfc_pages' ) || ( Helpers_User::is_user_admin() && ! isset( $_GET['nfc_nonce'], $_GET['nfc_product_id'] ) ) ) { // This is a check for Gutenberg, we display content within block editor.
			$user_role = null;

			if ( isset( $attributes['user_role'] ) && ! empty( $attributes['user_role'] ) ) {
				$user_role = $attributes['user_role'];
			}

			ob_start();

			$template_args = [
				'post_id'        => '',
				'unique'         => '',
				'event_statuses' => Helpers_User::get_event_statuses( null, $user_role ),
			];

			load_template( NFC_EVENTS_TEMPLATE_PATH . 'nfc-product-event-form.php', false, $template_args );

			return ob_get_clean();
		}

		$nfc_product_id = filter_input( INPUT_GET, 'nfc_product_id', FILTER_SANITIZE_ENCODED );

		if ( ! isset( $_GET['nfc_nonce'], $_GET['nfc_product_id'] ) || ! wp_verify_nonce( sanitize_key( $_GET['nfc_nonce'] ), 'nfc_nonce_' . sanitize_text_field( wp_unslash( $_GET['nfc_product_id'] ) ) ) ) {
			ob_start();

			?>
			<h3><?php esc_html_e( 'Error! Nonce failed or no post ID passed!', 'nfc-events' ); ?></h3>
			<?php

			return ob_get_clean();
		}

		if ( false === apply_filters( 'nfc_events_user_redirection_post_status', get_post_status( $nfc_product_id ) ) ) {
			ob_start();

			?>
			<h3><?php esc_html_e( 'Error! Wrong post ID!', 'nfc-events' ); ?></h3>
			<?php

			return ob_get_clean();
		}

		ob_start();

		$template_args = [
			'post_id'        => $nfc_product_id,
			'unique'         => filter_input( INPUT_GET, 'unique', FILTER_SANITIZE_ENCODED ),
			'event_statuses' => Helpers_User::get_event_statuses(),
		];

		load_template( NFC_EVENTS_TEMPLATE_PATH . 'nfc-product-event-form.php', false, $template_args );

		return ob_get_clean();
	}

	/**
	 * Displays event posts query loop.
	 *
	 * @return html
	 */
	public function events_log() {
		if ( ! is_user_logged_in() ) {
			ob_start();

			?>
			<h2><?php esc_html_e( 'You need to be logged in to view events log!', 'nfc-events' ); ?></h2>
			<?php

			return ob_get_clean();
		}

		$paged = ( filter_input( INPUT_GET, 'nfc_page', FILTER_SANITIZE_ENCODED ) ) ? filter_input( INPUT_GET, 'nfc_page', FILTER_SANITIZE_ENCODED ) : 1;

		$args = [
			'post_type'      => 'nfc_events',
			'post_status'    => 'publish',
			'posts_per_page' => 10,
			'paged'          => $paged,
			'post__in'       => [],
			'meta_query'     => [],
		];

		$product_ids = filter_input( INPUT_GET, 'nfc_product_ids', FILTER_SANITIZE_ENCODED );

		if ( $product_ids ) {
			array_push(
				$args['meta_query'],
				[
					'key'     => '_nfc_post_id',
					'value'   => $product_ids,
					'compare' => 'IN',
				]
			);
		}

		if ( filter_input( INPUT_GET, 'nfc_status', FILTER_SANITIZE_ENCODED ) ) {
			array_push(
				$args['meta_query'],
				[
					'key'     => '_nfc_event_status_id',
					'value'   => filter_input( INPUT_GET, 'nfc_status', FILTER_SANITIZE_ENCODED ),
					'compare' => '=',
				]
			);
		}

		if ( 'history' !== filter_input( INPUT_GET, 'nfc_current', FILTER_SANITIZE_ENCODED ) ) {
			global $wpdb;

			/**
			 * Select post ids of the DISTINCT meta values of the latests events.
			 */
			$event_ids = $wpdb->get_col(
				"SELECT MAX(post_id)
				FROM wp_postmeta
				WHERE meta_key = '_nfc_post_id'
				GROUP BY meta_value"
			);

			if ( $event_ids ) {
				$args['post__in'] = $event_ids;
			}
		}

		$events_log = new \WP_Query( $args );

		$statuses = get_posts(
			[
				'post_type'   => 'nfc_event_statuses',
				'numberposts' => -1,
			]
		);

		ob_start();

		load_template( NFC_EVENTS_TEMPLATE_PATH . 'nfc-events-log-filters.php', false, [ 'statuses' => $statuses ] );

		$template_args = [
			'query' => $events_log,
			'paged' => $paged,
		];

		load_template( NFC_EVENTS_TEMPLATE_PATH . 'nfc-events-log-loop.php', false, $template_args );

		return ob_get_clean();
	}
}
