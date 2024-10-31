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

<div class="nfc-file-export nfc-tags-export">
	<select name="nfc_export_post_type" data-token="<?php echo esc_attr( $args['token'] ); ?>" data-query-param="<?php echo esc_attr( $args['query_param'] ); ?>" title="<?php echo esc_attr__( 'Select export post type', 'nfc-events' ); ?>">
		<option value="">
			<?php esc_html_e( 'Select post type', 'nfc-events' ); ?>
		</option>
		<?php
		foreach ( (array) $args['post_types'] as $post_type_option ) {
			if ( false === $post_type_option->show_in_rest || ( 'product' !== $post_type_option->name && 'post' !== $post_type_option->name && 'page' !== $post_type_option->name ) ) {
				continue;
			}

			$rest_base = $post_type_option->rest_base ? $post_type_option->rest_base : $post_type_option->name;
			?>
			<option value="<?php echo esc_attr( $rest_base ); ?>">
				<?php echo esc_html( $post_type_option->label ); ?>
			</option>
			<?php
		}
		?>
	</select>

	<div class="button button-primary nfc-export-submit">
		<span><?php esc_html_e( 'Start Export', 'nfc-events' ); ?></span>
		<span><?php esc_html_e( 'Done!', 'nfc-events' ); ?></span>
	</div>


	<div class="nfc-export-success-msg">
		<?php esc_html_e( 'Export is complete. File is automatically downloaded!', 'nfc-events' ); ?>
	</div>

	<div class="nfc-export-response"></div>
</div>
