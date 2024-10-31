<?php
/**
 * Post/product feature image.
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

<div class="nfc-product-title">
	<h1>
		<a href="<?php echo esc_url( get_permalink( $args['post_id'] ) ); ?>">
			<?php echo esc_html( $args['title'] ); ?>
		</a>
	</h1>
</div>
