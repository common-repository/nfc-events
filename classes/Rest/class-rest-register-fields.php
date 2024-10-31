<?php
/**
 * Rest endpoint returning all active user roles.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Rest;

use Nfc\Events\{
	Singleton,
	Helpers\Helpers_User,
};

/**
 * Class Rest_Register_Fields.
 */
class Rest_Register_Fields {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Rest
	 *
	 * @var mixed
	 */
	private $rest;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_rest_fields' ], 10, 1 );
	}

	/**
	 * Register rest fields.
	 *
	 * @return void
	 */
	public function register_rest_fields() {
		register_rest_field(
			'nfc_events',
			'event_meta',
			[
				'get_callback' => [ $this, 'event_meta' ],
			]
		);

		register_rest_field(
			'product',
			'nfc_product_meta',
			[
				'get_callback' => [ $this, 'product_meta' ],
			]
		);
	}

	/**
	 * Rest field callback for additional events meta data.
	 *
	 * @param object $post WP_Post.
	 *
	 * @return array
	 */
	public function event_meta( $post ) {
		if ( ! current_user_can( 'edit_others_posts' ) && ! Helpers_User::admin_export() ) {
			return [
				'status' => 401,
				'error'  => esc_html__( 'You are not allowed to see this data', 'nfc-events' ),
			];
		}

		$event_id  = $post['id'];
		$user_id   = get_post_meta( $event_id, '_nfc_user_id', true );
		$user      = get_userdata( $user_id );
		$status_id = get_post_meta( $event_id, '_nfc_event_status_id', true );
		$status    = get_post( $status_id );
		$post_id   = get_post_meta( $event_id, '_nfc_post_id', true );

		return [
			'product_id'      => $post_id,
			'product_name'    => apply_filters( 'nfc_events_rest_product_name', get_the_title( $post_id ), $post_id, $event_id ),
			'event_date'      => get_the_date( get_option( 'date_format' ), $event_id ),
			'event_status_id' => $status_id,
			'event_status'    => $status->post_title,
			'user_id'         => $user_id,
			'user_email'      => '',
			'user_name'       => $user->display_name,
			'user_role'       => $user->roles[0],
			'user_full_name'  => $user->first_name . ' ' . $user->last_name,
			'unique'          => get_post_meta( $event_id, '_nfc_unique', true ),
		];
	}

	/**
	 * Rest field callback for additional product meta data.
	 *
	 * @param object $post WP_Post.
	 *
	 * @return array
	 */
	public function product_meta( $post ) {
		if ( ! current_user_can( 'edit_others_posts' ) && ! Helpers_User::admin_export() ) {
			return [
				'status' => 401,
				'error'  => esc_html__( 'You are not allowed to see this data', 'nfc-events' ),
			];
		}

		$product_id = $post['id'];

		$product    = wc_get_product( $product_id );
		$att_values = [];

		if ( $product && $product->is_type( 'variable' ) ) {
			$variation_ids = $product->get_children();

			if ( ! $variation_ids ) {
				return;
			}

			$variations_meta = [];

			foreach ( $variation_ids as $variation_id ) {
				$total_stock = get_post_meta( $variation_id, '_nfc_events_product_total_stock', true );
				$variation   = wc_get_product( $variation_id );
				$attributes  = $variation->get_attributes();

				if ( $attributes ) {
					foreach ( $attributes as $key => $attribute ) {
						$att_values[ $key ] = explode( ', ', $variation->get_attribute( $key ) );
					}
				}

				array_push(
					$variations_meta,
					[
						'id'          => $variation_id,
						'name'        => get_the_title( $variation_id ),
						'total_stock' => $total_stock ? $total_stock : 1,
						'attributes'  => $att_values,
					]
				);
			}

			return [
				'variations' => $variations_meta,
			];
		}

		$total_stock = get_post_meta( $product_id, '_nfc_events_product_total_stock', true );
		$attributes  = $product->get_attributes();

		if ( $attributes ) {
			foreach ( $attributes as $key => $attribute ) {
				$att_values[ $key ] = explode( ', ', $product->get_attribute( $key ) );
			}
		}

		return [
			'total_stock' => $total_stock ? $total_stock : 1,
			'attributes'  => $att_values,
		];
	}
}
