<?php
/**
 * Post/product notice for not logged in user when gets redirected from nfc product/scanned url.
 *
 * @param string $redirection_url Redirection URL after successful login.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

//phpcs:disable Generic.Arrays.DisallowShortArraySyntax
?>

<div class="nfc-product-notice">
	<div class="nfc-product-notice-container">
		<h3><?php esc_html_e( 'Sign In', 'nfc-events' ); ?></h3>

		<p>
			<?php esc_html_e( 'Please login and you will be automatically redirected to NFC event page.', 'nfc-events' ); ?>
		</p>

		<a class="button btn nfc-login-button" href="<?php echo esc_url( wp_login_url( $args['redirection_url'] ) ); ?>">
			<?php esc_html_e( 'Sign in to your account', 'nfc-events' ); ?>
		</a>

		<div class="nfc-product-notice-close"></div>
	</div>
</div>
