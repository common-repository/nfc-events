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

<div class="nfc-product-img">
	<a href="<?php echo esc_url( get_permalink( $args['post_id'] ) ); ?>">
		<img src="<?php echo esc_url( $args['image'] ); ?>"/>
	</a>
</div>
