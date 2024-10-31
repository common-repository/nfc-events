<?php
/**
 * CPT columns.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Events;

use Nfc\Events\Singleton;

/**
 * Class Events_Columns.
 */
class Events_Columns {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'manage_edit-nfc_events_columns', [ $this, 'add_event_status_column' ] );
		add_action( 'manage_nfc_events_posts_custom_column', [ $this, 'event_status_column_content' ], 10, 2 );
	}

	/**
	 * Add events table column/s.
	 *
	 * @param array $columns Table columns.
	 *
	 * @return array
	 */
	public function add_event_status_column( $columns ) {
		$columns['event_status'] = esc_html__( 'Status', 'nfc-events' );
		$columns['event_unique'] = esc_html__( 'Unique', 'nfc-events' );

		return $columns;
	}

	/**
	 * Displays event status on events admin table.
	 *
	 * @param string $column Column slug.
	 * @param int    $event_id Post ID.
	 *
	 * @return void
	 */
	public function event_status_column_content( $column, $event_id ) {
		if ( 'event_status' === $column ) {
			$status_id = get_post_meta( $event_id, '_nfc_event_status_id', true );

			if ( $status_id ) {
				$status_name  = get_the_title( $status_id );
				$status_color = get_post_meta( $status_id, 'nfc_event_status_color', true );

				?>
				<div class="nfc-event-status" style="background-color:<?php echo esc_attr( $status_color ); ?>">
					<span style="color:<?php echo esc_attr( $status_color ); ?>">
						<?php echo esc_html( $status_name ); ?>
					</span>
				</div>
				<?php
			}
		}

		if ( 'event_unique' === $column ) {
			$unique = get_post_meta( $event_id, '_nfc_unique', true );

			if ( $unique ) {
				?>
				<div class="nfc-event-unique">
					<?php echo esc_html( $unique ); ?>
				</div>
				<?php
			}
		}
	}
}
