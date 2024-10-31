<?php
/**
 * Helpers methods all regarding user/users.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Helpers;

use Nfc\Events\Singleton;

/**
 * Class Helpers_Export.
 */
class Helpers_Export extends Helpers {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Returns events data as multi array.
	 *
	 * @param int $event_ids Post IDs.
	 *
	 * @return array
	 */
	public static function get_events_data( $event_ids ) {
		$events_data['header'] = [
			'ID',
			'Event Date',
			'Event Status',
			'Event Status ID',
			'Product ID',
			'Product Name',
			'Product Unique',
			'User Email',
			'User ID',
			'User Name',
			'User Full Name',
			'User Role',
		];

		foreach ( (array) $event_ids as $event_id ) {
			$status_id  = get_post_meta( $event_id, '_nfc_event_status_id', true );
			$product_id = get_post_meta( $event_id, '_nfc_post_id', true );
			$user_id    = get_post_meta( $event_id, '_nfc_user_id', true );
			$user       = get_userdata( $user_id );

			$events_data[ $event_id ] = [
				$event_id,
				get_the_date( get_option( 'date_format' ), $event_id ),
				get_the_title( $status_id ),
				$status_id,
				$product_id,
				get_the_title( $product_id ),
				get_post_meta( $event_id, '_nfc_unique', true ),
				$user->user_email,
				$user_id,
				$user->display_name,
				$user->first_name . ' ' . $user->last_name,
				$user->roles[0],
			];
		}

		return $events_data;
	}

	/**
	 * Transforms multi array of event data into a rows as
	 * strings separated by ";" ready for CSV file creation.
	 *
	 * @param array $event_ids Post IDs.
	 *
	 * @return string
	 */
	public static function get_events_data_blob_raw( $event_ids ) {
		if ( ! is_array( $event_ids ) ) {
			return false;
		}

		$event_data = array_map(
			function ( $array ) {
				return implode( ';', $array );
			},
			self::get_events_data( $event_ids )
		);

		return implode( ";\n", $event_data );
	}
}
