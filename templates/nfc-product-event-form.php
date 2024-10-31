<?php
/**
 * Form for submitting a task.
 *
 * @param string $post_id Post ID.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

//phpcs:disable Generic.Arrays.DisallowShortArraySyntax
?>

<div class="nfc-product-event">
	<div class="nfc-product-event-details">
		<div>
			<?php
			if ( $args['post_id'] ) {
				if ( function_exists( 'wc_get_product' ) ) {
					$product = wc_get_product( $args['post_id'] );
				}

				if ( isset( $product ) && $product ) {
					echo wp_kses_post( wp_trim_words( $product->get_description(), 11, '...' ) );
				} elseif ( has_excerpt( $args['post_id'] ) ) {
					the_excerpt( $args['post_id'] );
				} else {
					echo wp_kses_post( wp_trim_words( get_the_content( false, false, $args['post_id'] ), 11, '...' ) );
				}
			}
			?>
		</div>

		<form action="" method="POST" class="nfc-product-event-form">
			<div>
				<h4><?php esc_html_e( 'Select status:', 'nfc-events' ); ?></h4>

				<?php
				if ( $args['event_statuses'] ) {
					if ( $args['post_id'] ) {
						$latest_product_event = new \WP_Query(
							[
								'post_type'      => 'nfc_events',
								'post_status'    => 'publish',
								'posts_per_page' => 1,
								'meta_key'       => '_nfc_post_id',
								'meta_value'     => $args['post_id'],
							]
						);

						if ( $latest_product_event->have_posts() ) {
							$latest_status = get_post_meta( $latest_product_event->post->ID, '_nfc_event_status_id', true );
						}
					}

					$default_status = get_option( 'nfc_event_status_default' );

					foreach ( $args['event_statuses'] as $key => $event_status ) {
						$checked = ! isset( $latest_status ) ? ( $default_status === $event_status->post_name ? 'checked' : '' ) : ( (int) $latest_status === $event_status->ID ? 'checked' : '' );
						?>

						<label for="nfc_events_event_<?php echo esc_attr( $event_status->ID ); ?>">
							<input type="radio" id="nfc_events_event_<?php echo esc_attr( $event_status->ID ); ?>" name="nfc_events_event" value="<?php echo esc_attr( $event_status->ID ); ?>" <?php echo esc_attr( $checked ); ?>>

							<span>
								<?php echo esc_html( $event_status->post_title ); ?>

								<span><?php echo wp_kses_post( $event_status->post_excerpt ); ?></span>
							</span>
						</label>

						<?php
					}
				} else {
					?>
					<p><em><?php esc_html_e( 'No statuses currently to choose for your user role.', 'nfc-events' ); ?></em></p>
					<?php
				}
				?>
			</div>

			<div>
				<h4><?php esc_html_e( 'Leave comment:', 'nfc-events' ); ?></h4>

				<textarea name="nfc_events_note" rows="3" cols="50" placeholder="<?php echo esc_attr__( 'Any message for us?', 'nfc-events' ); ?>"></textarea>
			</div>

			<div>
				<h4><?php esc_html_e( 'Upload photo:', 'nfc-events' ); ?></h4>

				<div class="nfc-events-file-upload">
					<p>
						<?php esc_html_e( 'Max 10MB per image', 'nfc-events' ); ?>

						<span><?php esc_html( '(png, jpg, jpeg)' ); ?></span>
					</p>

					<div id="nfc-events-files-names"></div>

					<label for="nfc_events_attachment_images">
						<span><?php esc_html_e( 'Attach images', 'nfc-events' ); ?></span>
					</label>

					<input type="file" name="file_images[]" id="nfc_events_attachment_images" class="nfc-events-attachment" accept="image/png, image/jpeg" multiple="">
				</div>
			</div>

			<input type="hidden" name="nfc_events_product_id" value="<?php echo esc_attr( $args['post_id'] ); ?>"/>
			<input type="hidden" name="nfc_events_product_unique" value="<?php echo esc_attr( $args['unique'] ); ?>"/>
			<div class="nfc-product-event-form-open-submit-modal">
				<?php esc_html_e( 'Submit post event', 'nfc-events' ); ?>
			</div>
			<div class="nfc-product-event-form-modal">
				<div class="nfc-product-event-form-modal-wrap">
					<h4><?php esc_html_e( 'Are you sure you want to submit an event?', 'nfc-events' ); ?></h4>

					<button type="submit" class="nfc-product-event-form-submit"><?php esc_html_e( 'Submit', 'nfc-events' ); ?></button>
					<button type="button" class="nfc-product-event-form-close-submit-modal"><?php esc_html_e( 'Cancel', 'nfc-events' ); ?></button>
				</div>
			</div>
			<div class="nfc-product-event-form-response"></div>
		</form>
	</div>
</div>
