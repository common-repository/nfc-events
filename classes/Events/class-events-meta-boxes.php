<?php
/**
 * Meta boxes for CPT Events.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Events;

use Nfc\Events\{
	Singleton,
	Helpers\Helpers_User,
};

/**
 * Class Events_Meta_Boxes.
 */
class Events_Meta_Boxes {
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
			'nfc-event-data',
			esc_html__( 'Event Data', 'nfc-events' ),
			[ $this, 'event_data' ],
			'nfc_events'
		);
	}

	/**
	 * Single event post admin data.
	 *
	 * @param object $post WP_Post.
	 *
	 * @return void
	 */
	public function event_data( $post ) {
		$event_id  = $post->ID;
		$user_id   = get_post_meta( $post->ID, '_nfc_user_id', true );
		$user      = get_user_by( 'id', $user_id );
		$status_id = get_post_meta( $event_id, '_nfc_event_status_id', true );
		$post_id   = get_post_meta( $event_id, '_nfc_post_id', true );
		$files     = get_post_meta( $event_id, '_nfc_files', true );
		$unique    = get_post_meta( $event_id, '_nfc_unique', true );
		?>
		<h3><?php echo esc_html__( 'Event #', 'nfc-events' ) . esc_html( $event_id ); ?></h3>

		<div class="panel nfc-event-data">
			<div class="nfc-event-data-general">
				<h3><?php esc_html_e( 'General', 'nfc-events' ); ?></h3>

				<p>
					<strong><?php esc_html_e( 'Date created:', 'nfc-events' ); ?></strong>
					<?php
					echo wp_kses_post( get_the_date( get_option( 'date_format' ), $post ) . '<br>' . get_the_time( get_option( 'time_format' ), $post ) );
					?>
				</p>

				<p>
					<strong><?php esc_html_e( 'Event status:', 'nfc-events' ); ?></strong>

					<?php
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
					?>
				</p>

				<p>
					<strong><?php esc_html_e( 'Product:', 'nfc-events' ); ?></strong>

					<a href="<?php echo esc_url( apply_filters( 'nfc_events_post_event_metabox_title_url', get_permalink( $post_id ), $event_id, $post_id ) ); ?>" target="_blank">
						<?php echo esc_html( apply_filters( 'nfc_events_post_event_metabox_title', get_the_title( $post_id ), $event_id, $post_id ) ); ?>
					</a>

					<span>
						<?php apply_filters( 'nfc_events_post_event_metabox_unique', $unique, $event_id, $post_id ) === $unique ? esc_html_e( 'Unique:', 'nfc-events' ) : ''; ?>
						<?php echo esc_html( apply_filters( 'nfc_events_post_event_metabox_unique', $unique, $event_id, $post_id ) ); ?>
					</span>
				</p>
			</div>

			<div class="nfc-event-data-user">
				<h3><?php esc_html_e( 'User data', 'nfc-events' ); ?></h3>

				<?php
				if ( $user ) {
					?>
					<p>
						<span><?php echo esc_html( $user->display_name ); ?></span>
						<span><?php echo esc_html__( 'User ID:', 'nfc-events' ) . ' ' . esc_html( $user_id ); ?></span>
					</p>

					<p>
						<strong><?php esc_html_e( 'Role:', 'nfc-events' ); ?></strong>
						<?php echo esc_html( ucfirst( $user->roles[0] ) ); ?>
					</p>

					<p>
						<strong><?php esc_html_e( 'Email:', 'nfc-events' ); ?></strong>
						<?php echo esc_html( $user->user_email ); ?>
					</p>
					<?php
				}
				?>
			</div>

			<div class="nfc-event-data-note">
				<h3><?php esc_html_e( 'Comment', 'nfc-events' ); ?></h3>

				<p>
					<?php
					$content = get_the_content( $post );

					if ( $content ) {
						?>
						<span><?php echo esc_html( $content ); ?></span>
						<?php
					} else {
						esc_html_e( 'No comment', 'nfc-events' );
					}
					?>
				</p>
			</div>

			<div class="nfc-event-files">
				<h3><?php esc_html_e( 'Files', 'nfc-events' ); ?></h3>

				<p>
					<?php
					if ( $files ) {
						foreach ( (array) $files as $file ) {
							$file_url  = isset( $file['url'] ) ? $file['url'] : null;
							$file_name = isset( $file['name'] ) ? $file['name'] : null;
							$exist     = wp_remote_request( $file_url, [ 'sslverify' => false ] );

							if ( ! $file_url || ( isset( $exist['response']['code'] ) && 404 === $exist['response']['code'] ) ) {
								continue;
							}
							?>

							<a href="<?php echo esc_url( $file_url ); ?>" download style="background-image:url('<?php echo esc_url( $file_url ); ?>');" title="<?php echo esc_html( $file_name ); ?>"></a>
							<?php

							$has_images = true;
						}
					} else {
						esc_html_e( 'No files were uploaded.', 'nfc-events' );
					}
					?>
				</p>

				<?php
				if ( isset( $has_images ) ) {
					?>
					<span><?php esc_html_e( 'Click on the image to download the file.', 'nfc-events' ); ?></span>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Saves post data.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return void
	 */
	public function save_meta_data( $post_id ) {}
}
