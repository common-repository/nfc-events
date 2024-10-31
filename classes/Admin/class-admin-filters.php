<?php
/**
 * Admin CPT filtering options.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Admin;

use Nfc\Events\Singleton;

/**
 * Class Admin_Filters.
 */
class Admin_Filters {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'restrict_manage_posts', [ $this, 'filter_status' ], 10, 1 );
		add_action( 'parse_query', [ $this, 'parse_query' ], 10, 1 );
	}

	/**
	 * Adds status filter for nfc_events CPT admin table list.
	 *
	 * @param string $post_type CPT.
	 *
	 * @return void
	 */
	public function filter_status( $post_type ) {
		if ( 'nfc_events' !== $post_type ) {
			return;
		}

		global $wpdb;

		$post_status = "AND post_status != 'auto-draft'";

		if ( ! filter_input( INPUT_GET, 'post_status', FILTER_SANITIZE_ENCODED ) || 'trash' !== filter_input( INPUT_GET, 'post_status', FILTER_SANITIZE_ENCODED ) ) {
			$post_status .= " AND post_status != 'trash'";
		} elseif ( ! filter_input( INPUT_GET, 'post_status', FILTER_SANITIZE_ENCODED ) ) {
			$post_status = $wpdb->prepare( ' AND post_status = %s', filter_input( INPUT_GET, 'post_status', FILTER_SANITIZE_ENCODED ) );
		}

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$statuses = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT DISTINCT meta_value 
				FROM wp_postmeta 
				LEFT JOIN wp_posts
				ON wp_postmeta.post_id = wp_posts.ID
				WHERE meta_key = '_nfc_event_status_id'
				$post_status
				AND post_type = %s
				ORDER BY meta_value",
				$post_type
			)
		);
		?>

		<select name="nfc_status_filter">
			<option value=""><?php esc_html_e( 'All statuses', 'nfc-events' ); ?></option>

			<?php
			foreach ( (array) $statuses as $status ) {
				$status_post = get_post( $status->meta_value );
				$selected    = (int) filter_input( INPUT_GET, 'nfc_status_filter', FILTER_SANITIZE_ENCODED ) === (int) $status->meta_value ? 'selected' : '';
				?>
				<option value="<?php echo esc_attr( $status->meta_value ); ?>" <?php echo esc_attr( $selected ); ?>>
					<?php echo esc_html( $status_post->post_title ); ?>
				</option>
				<?php
			}
			?>
		</select>
		<?php
	}

	/**
	 * Parse admin filtering query.
	 *
	 * @param object $query WP_Query.
	 *
	 * @return void
	 */
	public function parse_query( $query ) {
		global $pagenow;

		if ( 'nfc_events' === filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_ENCODED ) && is_admin() && 'edit.php' === $pagenow && ! empty( filter_input( INPUT_GET, 'nfc_status_filter', FILTER_SANITIZE_ENCODED ) ) ) {
			$query->query_vars['meta_key']   = '_nfc_event_status_id';
			$query->query_vars['meta_value'] = filter_input( INPUT_GET, 'nfc_status_filter', FILTER_SANITIZE_ENCODED );
		}
	}
}
