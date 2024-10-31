<?php
/**
 * Admin settings user pages template.
 *
 * @param array $user_roles Array of all available user roles.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

foreach ( (array) $args['user_roles'] as $key => $user_role ) {
	$value = isset( $args['setting_value'][ $key ] ) ? $args['setting_value'][ $key ] : null;
	?>
	<div class="nfc-events-setting-user-page">
		<span><?php echo esc_html( $user_role['name'] . ':' ); ?></span>

		<select name="nfc_events_setting_user_redirections[<?php echo esc_attr( $key ); ?>]" class="nfc-events-setting-user-page-select">
			<option value=""><?php esc_html_e( 'Select page', 'nfc-events' ); ?></option>

			<?php
			foreach ( (array) $args['pages'] as $nfc_page ) {
				$selected = ( (int) $value === $nfc_page->ID ) ? 'selected' : '';
				?>
				<option value="<?php echo esc_attr( $nfc_page->ID ); ?>" <?php echo esc_attr( $selected ); ?>>
					<?php echo esc_html( $nfc_page->post_title ); ?>
				</option>
				<?php
			}
			?>
		</select>
	</div>
	<?php
}
?>

<div class="nfc-events-setting-user-page">
	<span><?php esc_html_e( 'Fallback:', 'nfc-events' ); ?></span>

	<select name="nfc_events_setting_user_redirections[fallback]" class="nfc-events-setting-user-page-select">
		<option value=""><?php esc_html_e( 'Select page', 'nfc-events' ); ?></option>

		<?php
		foreach ( (array) $args['pages'] as $nfc_page ) {
			$selected = ( (int) ( isset( $args['setting_value']['fallback'] ) ? $args['setting_value']['fallback'] : null ) === $nfc_page->ID ) ? 'selected' : '';
			?>
			<option value="<?php echo esc_attr( $nfc_page->ID ); ?>" <?php echo esc_attr( $selected ); ?>>
				<?php echo esc_html( $nfc_page->post_title ); ?>
			</option>
			<?php
		}
		?>
	</select>

	<p>
		<small>
			<?php esc_html_e( 'Note: User will be redirected to fallback page only when post ID does not exist or post/product is not yet published.', 'nfc-events' ); ?>
		</small>
	</p>
</div>
