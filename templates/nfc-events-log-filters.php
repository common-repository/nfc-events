<?php
/**
 * Events log loop template.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<div class="nfc-events-log-filters">
	<form action="" method="GET">
		<select class="nfc-product-search" name="nfc_product_ids[]" data-nonce="<?php echo esc_attr( wp_create_nonce( 'search-products' ) ); ?>" multiple data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'nfc-events' ); ?>">
			<option value="ase"></option>
			<?php
			$product_ids = filter_input( INPUT_GET, 'nfc_product_ids', FILTER_SANITIZE_ENCODED );

			if ( $product_ids ) {
				foreach ( (array) $product_ids as $product_id ) {
					?>
					<option value="<?php echo esc_attr( $product_id ); ?>" selected>
						<?php echo esc_html( get_the_title( $product_id ) ); ?>
					</option>
					<?php
				}
			}
			?>
		</select>

		<select name="nfc_current" title="<?php echo esc_attr__( 'Current or history', 'nfc-events' ); ?>">
			<?php
			$current_selected = filter_input( INPUT_GET, 'nfc_current', FILTER_SANITIZE_ENCODED ) === 'history' ? 'selected' : '';
			?>

			<option><?php esc_html_e( 'Current events', 'nfc-events' ); ?></option>
			<option value="history" <?php echo esc_attr( $current_selected ); ?>><?php esc_html_e( 'History', 'nfc-events' ); ?></option>
		</select>

		<select name="nfc_status">
			<option value=""><?php esc_html_e( 'All statuses', 'nfc-events' ); ?></option>

			<?php
			foreach ( (array) $args['statuses'] as $nfc_status ) {
				$selected = (int) filter_input( INPUT_GET, 'nfc_status' ) === (int) $nfc_status->ID ? 'selected' : '';
				?>
				<option value="<?php echo esc_attr( $nfc_status->ID ); ?>" <?php echo esc_attr( $selected ); ?>>
					<?php echo esc_html( $nfc_status->post_title ); ?>
				</option>
				<?php
			}
			?>
		</select>

		<input type="submit" class="button" value="<?php echo esc_attr__( 'Filter', 'nfc-events' ); ?>">
	</form>
</div>
