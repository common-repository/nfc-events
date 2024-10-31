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

use Nfc\Events\{
	Singleton,
	Helpers\Helpers,
	Helpers\Helpers_Product,
};

if ( ! $args['query']->have_posts() ) {
	?>
	<h3>
		<?php esc_html_e( 'No events found.', 'nfc-events' ); ?>
	</h3>
	<?php

	return;
}

// phpcs:disable Generic.Arrays.DisallowShortArraySyntax
?>

<div class="nfc-events-log">
	<div class="nfc-events-log-header">
		<span><?php esc_html_e( 'ID', 'nfc-events' ); ?></span>
		<span><?php esc_html_e( 'Date created', 'nfc-events' ); ?></span>
		<span><?php esc_html_e( 'Status', 'nfc-events' ); ?></span>
		<span class="nfc-events-log-header-product"><?php esc_html_e( 'Product', 'nfc-events' ); ?></span>
		<span><?php esc_html_e( 'User details', 'nfc-events' ); ?></span>
		<span><?php esc_html_e( 'Comment', 'nfc-events' ); ?></span>
		<span><?php esc_html_e( 'Files', 'nfc-events' ); ?></span>
	</div>
	<?php

	while ( $args['query']->have_posts() ) {
		$args['query']->the_post();

		$event_id   = get_the_ID();
		$user_id    = get_post_meta( $event_id, '_nfc_user_id', true );
		$user       = get_user_by( 'id', $user_id );
		$product_id = get_post_meta( $event_id, '_nfc_post_id', true );
		$status_id  = get_post_meta( $event_id, '_nfc_event_status_id', true );
		$files      = get_post_meta( $event_id, '_nfc_files', true );
		$unique     = get_post_meta( $event_id, '_nfc_unique', true );

		?>
		<div class="nfc-events-log-item">
			<span>
				<h4><?php esc_html_e( 'Event ID:', 'nfc-events' ); ?></h4>
				<?php echo esc_html( $event_id ); ?>
			</span>

			<span>
				<h4><?php esc_html_e( 'Date:', 'nfc-events' ); ?></h4>
				<?php echo wp_kses_post( get_the_date( get_option( 'date_format' ), $post ) . '<br>' . get_the_time( get_option( 'time_format' ), $post ) ); ?>
			</span>

			<span>
				<h4><?php esc_html_e( 'Status:', 'nfc-events' ); ?></h4>

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
			</span>

			<span class="nfc-events-log-item-product">
				<h4><?php esc_html_e( 'Product:', 'nfc-events' ); ?></h4>

				<div>
					<strong><?php esc_html_e( 'Name:', 'nfc-events' ); ?></strong>

					<a href="<?php echo esc_url( apply_filters( 'nfc_events_post_event_metabox_title_url', get_permalink( $product_id ), $event_id, $product_id ) ); ?>" target="_blank">
						<?php echo esc_html( apply_filters( 'nfc_events_post_event_metabox_title', get_the_title( $product_id ), $event_id, $product_id ) ); ?>
					</a>
				</div>

				<div>
					<strong><?php apply_filters( 'nfc_events_post_event_metabox_unique', $unique, $event_id, $product_id ) === $unique ? esc_html_e( 'Unique:', 'nfc-events' ) : ''; ?></strong>

					<span>
						<?php echo esc_html( apply_filters( 'nfc_events_post_event_metabox_unique', $unique, $event_id, $product_id ) ); ?>
					</span>
				</div>

				<div>
					<strong><?php esc_html_e( 'Product ID:', 'nfc-events' ); ?></strong>
					<span><?php echo esc_html( $product_id ); ?></span>
				</div>

				<div class="nfc-events-log-item-product-statuses">
					<strong><?php esc_html_e( 'Status history', 'nfc-events' ); ?></strong>
					<?php echo wp_kses_post( Helpers_Product::get_product_events_count_list( $product_id ) ); ?>
				</div>
			</span>

			<span>
				<h4><?php esc_html_e( 'User:', 'nfc-events' ); ?></h4>

				<?php
				if ( $user ) {
					?>
					<div>
						<strong><?php esc_html_e( 'Name:', 'nfc-events' ); ?></strong>
						<span><?php echo esc_html( $user->display_name ); ?></span>
					</div>

					<div>
						<strong><?php esc_html_e( 'ID:', 'nfc-events' ); ?></strong>
						<span><?php echo esc_html( $user_id ); ?></span>
					</div>

					<div>
						<strong><?php esc_html_e( 'Role:', 'nfc-events' ); ?></strong>
						<?php echo esc_html( ucfirst( $user->roles[0] ) ); ?>
					</div>

					<div>
						<strong><?php esc_html_e( 'Email:', 'nfc-events' ); ?></strong>
						<?php echo esc_html( $user->user_email ); ?>
					</div>
					<?php
				}
				?>
			</span>

			<span>
				<h4><?php esc_html_e( 'Comment:', 'nfc-events' ); ?></h4>

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
			</span>

			<span class="nfc-event-files">
				<h4><?php esc_html_e( 'Files:', 'nfc-events' ); ?></h4>

				<?php
				$has_images = false;

				if ( $files ) {
					foreach ( (array) $files as $file ) {
						$file_url  = isset( $file['url'] ) ? $file['url'] : null;
						$file_name = isset( $file['name'] ) ? $file['name'] : null;
						$exist     = wp_remote_request( $file_url, [ 'sslverify' => false ] );

						if ( ! $file_url || is_wp_error( $exist ) || ( isset( $exist['response']['code'] ) && 404 === $exist['response']['code'] ) ) {
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

				if ( true === $has_images ) {
					?>
					<span><?php esc_html_e( 'Click on the image to download the file.', 'nfc-events' ); ?></span>
					<?php
				}
				?>
			</span>
		</div>
		<?php
	}
	?>
</div>

<div class="nfc-events-log-pagination">
	<?php
	echo wp_kses_post(
		paginate_links(
			[
				'base'    => add_query_arg( 'nfc_page', '%#%' ),
				'total'   => $args['query']->max_num_pages,
				'current' => $args['paged'],
			]
		)
	);
	?>
</div>
