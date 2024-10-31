<?php
/**
 * Bulk actions for events admin side.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Events;

use Nfc\Events\{
	Singleton,
	Helpers\Helpers_Export,
};

/**
 * Class Events_Bulk_Actions.
 */
class Events_Bulk_Actions {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'bulk_actions-edit-nfc_events', [ $this, 'add_events_bulk_actions' ], 10, 1 );
		add_filter( 'handle_bulk_actions-edit-nfc_events', [ $this, 'handle_events_bulk_actions' ], 10, 3 );
		add_action( 'admin_notices', [ $this, 'notices' ] );
	}

	/**
	 * Adds additional bulk actions to events CPT.
	 *
	 * @param array $actions Bulk actions.
	 *
	 * @return array
	 */
	public function add_events_bulk_actions( $actions ) {
		$actions['nfc_export'] = esc_html__( 'Export', 'nfc-events' );

		return $actions;
	}

	/**
	 * Handle events bulk actions.
	 *
	 * @param string $redirect_url Redirection URL.
	 * @param string $action Type of bulk action.
	 * @param array  $post_ids Post IDs.
	 *
	 * @return string
	 */
	public function handle_events_bulk_actions( $redirect_url, $action, $post_ids ) {
		if ( 'nfc_export' === $action ) {
			$redirect_url = add_query_arg( 'nfc_export', implode( ';', $post_ids ), $redirect_url );
		}

		return $redirect_url;
	}

	/**
	 * Admin notices after bulk action.
	 *
	 * @return void
	 */
	public function notices() {
		$nfc_export = filter_input( INPUT_GET, 'nfc_export', FILTER_SANITIZE_ENCODED );

		if ( ! empty( $nfc_export ) ) {
			$event_ids  = explode( ';', $nfc_export );
			$event_data = Helpers_Export::get_events_data_blob_raw( $event_ids );

			if ( ! $event_data ) {
				?>
				<div id="message" class="notice notice-error is-dismissible">
					<p>
						<?php esc_html_e( 'Error during creation of events raw data!', 'nfc-events' ); ?>
					</p>
				</div>
				<?php
			} else {
				?>
				<div id="message" class="notice notice-success is-dismissible">
					<p>
						<?php
						/* translators: %d: number of posts */
						printf( esc_html__( 'Exported %d events.', 'nfc-events' ), (int) count( $event_ids ) );
						?>

						<span class="nfc-admin-bulk-download" data-events-data="<?php echo esc_attr( $event_data ); ?>">
							<?php esc_html_e( 'Download again', 'nfc-events' ); ?>
						</span>
					</p>

					<p>
						<?php esc_html_e( 'To export all events, navigate to NFC Events > Settings > Export.', 'nfc-events' ); ?>
					</p>
				</div>
				<?php
			}
		}
	}
}
