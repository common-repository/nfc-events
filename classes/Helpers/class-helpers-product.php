<?php
/**
 * Helpers methods all regarding products.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Helpers;

use Nfc\Events\Singleton;

/**
 * Class Helpers_Product.
 */
class Helpers_Product extends Helpers {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Returns list of all product events as name - count.
	 *
	 * @param string $product_id Post ID.
	 *
	 * @return array
	 */
	public static function get_product_events_count_list( $product_id ) {
		if ( ! $product_id ) {
			return;
		}

		global $wpdb;

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$query = $wpdb->get_col(
			$wpdb->prepare( // TODO We should optimize this, think it can be simpler perhaps.
				"SELECT
					rel2.meta_value
				FROM {$wpdb->posts} AS posts
					LEFT JOIN {$wpdb->postmeta} AS rel ON
						posts.ID = rel.post_id
					LEFT JOIN {$wpdb->postmeta} AS rel2 ON
						posts.ID = rel2.post_id
				WHERE
					posts.post_type = 'nfc_events' AND
					posts.post_status = 'publish' AND
					rel.meta_key = '_nfc_post_id' AND
					rel.meta_value = %d AND
					rel2.meta_key = '_nfc_event_status_id' AND
					rel2.meta_value > 0
				ORDER BY posts.ID DESC",
				$product_id
			)
		);

		$statuses = array_count_values( $query );

		foreach ( $statuses as $status_id => $count ) {
			$status_name = get_the_title( $status_id );

			if ( ! $status_name ) { // Status might get deleted so this is why this is checked here.
				continue;
			}
			?>

			<span>
				<?php echo esc_html( $status_name . ': ' . $count ); ?>
			</span>

			<?php
		}
	}
}
