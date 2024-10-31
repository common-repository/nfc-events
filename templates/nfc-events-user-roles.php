<?php
/**
 * Admin NFC Event post editor allowed user roles meta box content.
 *
 * @param array $user_roles Array of avaialable user roles.
 * @param array $saved_user_roles Array of saved user roles.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

foreach ( (array) $args['user_roles'] as $key => $user_role ) {
	$saved_user_role = isset( $args['saved_user_roles'][ $key ] ) ? $args['saved_user_roles'][ $key ] : null;

	?>
	<label for="nfc_user_roles_<?php echo esc_attr( $key ); ?>" class="nfc-event-user-role">
		<input type="checkbox" id="nfc_user_roles_<?php echo esc_attr( $key ); ?>" name="nfc_user_roles[<?php echo esc_attr( $key ); ?>]" <?php checked( $saved_user_role, 'on', true ); ?>/>
		<?php echo esc_html( $user_role['name'] ); ?>
	</label>
	<?php
}

?>
<p>
	<small>
		<?php esc_html_e( 'Note: If no user roles are checked, all user roles will be allowed to view this page.', 'nfc-events' ); ?>
	</small>
</p>
<?php
