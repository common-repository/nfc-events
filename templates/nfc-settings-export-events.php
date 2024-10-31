<?php
/**
 * Admin settings export field template.
 *
 * @param array $post_types Array of all available post types.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

//phpcs:disable Generic.Arrays.DisallowShortArraySyntax
?>

<div class="nfc-file-export-events">
	<p>
		<strong>
			<?php esc_html_e( 'Number of events:', 'nfc-events' ); ?>

			<?php echo esc_html( $args['count'] ); ?>
		</strong>
	</p>

	<div class="button button-primary nfc-export-events-submit" data-post-type="<?php echo esc_attr( $args['type'] ); ?>" data-token="<?php echo esc_attr( $args['token'] ); ?>">
		<span><?php esc_html_e( 'Export Events', 'nfc-events' ); ?></span>
		<span><?php esc_html_e( 'Done!', 'nfc-events' ); ?></span>
	</div>

	<div class="nfc-export-success-msg">
		<?php esc_html_e( 'Export is complete. File is automatically downloaded!', 'nfc-events' ); ?>
	</div>

	<div class="nfc-export-events-response"></div>
</div>
