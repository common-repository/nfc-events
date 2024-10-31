<?php
/**
 * Meta boxes for CPT Statuses.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Statuses;

use Nfc\Events\{
	Singleton,
	Helpers\Helpers_User,
};

/**
 * Class Statuses_Meta_Boxes.
 */
class Statuses_Meta_Boxes {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'add_meta_boxes', [ $this, 'meta_boxes' ] );
		add_action( 'save_post', [ $this, 'save_meta_data' ] );
	}

	/**
	 * Adds meta boxes to CPT Events.
	 *
	 * @return void
	 */
	public function meta_boxes() {
		add_meta_box(
			'nfc-event-status-user-roles',
			esc_html__( 'Allowed user roles', 'nfc-events' ),
			[ $this, 'user_roles_callback' ],
			'nfc_event_statuses'
		);

		add_meta_box(
			'nfc-event-status-status-color',
			esc_html__( 'Status colour', 'nfc-events' ),
			[ $this, 'status_color' ],
			'nfc_event_statuses'
		);

		add_meta_box(
			'nfc-event-status-set-default',
			esc_html__( 'Set as default status', 'nfc-events' ),
			[ $this, 'set_default' ],
			'nfc_event_statuses'
		);
	}

	/**
	 * User roles meta box callback.
	 *
	 * @param object $post WP_Post.
	 *
	 * @return void
	 */
	public function user_roles_callback( $post ) {
		$user_roles = Helpers_User::get_all_roles( true );

		$template_args = [
			'user_roles'       => $user_roles,
			'saved_user_roles' => get_post_meta( $post->ID, 'nfc_user_roles', true ),
		];

		load_template( NFC_EVENTS_TEMPLATE_PATH . 'nfc-events-user-roles.php', false, $template_args );

		/**
		 * Nonce.
		 */
		wp_nonce_field( 'nfc_event_status_nonce', 'nfc_event_status_nonce' );
	}

	/**
	 * User roles meta box callback.
	 *
	 * @param object $post WP_Post.
	 *
	 * @return void
	 */
	public function status_color( $post ) {
		$value = get_post_meta( $post->ID, 'nfc_event_status_color', true );
		$value = $value ? $value : '#e5e5e5';

		?>
		<label for="nfc_event_status_color" class="nfc-event-user-role">
			<?php esc_html_e( 'Status color', 'nfc-events' ); ?>
			<input type="color" id="nfc_event_status_color" name="nfc_event_status_color" value="<?php echo esc_attr( $value ); ?>"/>
			<p class="description">
				<small>
					<?php esc_html_e( 'This option is for administrator to better differentiate statuses within Dashboard > NFC > Events.', 'nfc-events' ); ?>
				</small>
			</p>
		</label>
		<?php
	}

	/**
	 * Set as default status for the frontend preselected status.
	 *
	 * @param object $post WP_Post.
	 *
	 * @return void
	 */
	public function set_default( $post ) {
		$value = get_option( 'nfc_event_status_default' );
		$value = $post->post_name === $value ? 'on' : null;

		?>
		<label for="nfc_event_status_default" class="nfc-event-user-role">
			<input type="checkbox" id="nfc_event_status_default" name="nfc_event_status_default" <?php checked( $value, 'on', true ); ?>/>
			<?php esc_html_e( 'Set as default', 'nfc-events' ); ?>

			<p>
				<small>
					<?php esc_html_e( 'Checking the option will make this status as default one so in case if a product doesnt have any events yet this status will be pre-selected on the event form block.', 'nfc-events' ); ?>
				</small>
				<br />
				<small>
					<?php esc_html_e( 'Also marking this status as default one will make any previous default status unchecked.', 'nfc-events' ); ?>
				</small>
			</p>
		</label>
		<?php
	}

	/**
	 * Saves post data.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return void
	 */
	public function save_meta_data( $post_id ) {
		if ( ! isset( $_POST['nfc_event_status_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nfc_event_status_nonce'] ) ), 'nfc_event_status_nonce' ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
			return;
		}

 		//phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		update_post_meta( $post_id, 'nfc_user_roles', isset( $_POST['nfc_user_roles'] ) ? sanitize_key( $_POST['nfc_user_roles'] ) : '' );
		update_post_meta( $post_id, 'nfc_event_status_color', isset( $_POST['nfc_event_status_color'] ) ? sanitize_key( $_POST['nfc_event_status_color'] ) : '' );

		if ( isset( $_POST['nfc_event_status_default'], $_POST['post_name'] ) && 'on' === sanitize_key( $_POST['nfc_event_status_default'] ) ) {
			update_option( 'nfc_event_status_default', sanitize_key( $_POST['post_name'] ) );
		}
	}
}
