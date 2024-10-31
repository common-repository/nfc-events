<?php
/**
 * Helpers methods misc.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Helpers;

use Nfc\Events\Singleton;

/**
 * Class Helpers.
 */
class Helpers {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Inserts new event post.
	 *
	 * @param int    $post_id Product ID.
	 * @param int    $unique Not sure what this is (comment from Krokedil).
	 * @param int    $event_status_id Event status post ID.
	 * @param string $note Textarea string.
	 * @param array  $files Files.
	 *
	 * @return int
	 */
	public static function set_product_event( $post_id, $unique = 1, $event_status_id, $note = '', $files = null ) {
		if ( ! $post_id ) {
			return false;
		}

		$post_title = apply_filters( 'nfc_events_set_product_event_title', get_the_title( $post_id ) );

		$cancel = apply_filters(
			'nfc_events_prevent_product_event_insert',
			[
				'cancel' => false,
				'notice' => esc_html__( 'Event with this status cannot be created at this moment.', 'nfc-events' ),
			],
			$post_id,
			$unique,
			$event_status_id,
			get_current_user_id()
		);

		if ( true === $cancel['cancel'] ) {
			return [
				'error' => $cancel['notice'],
			];
		}

		if ( $files ) {
			$files_count = count( $files['name'] );
			$urls        = [];
			$extension   = [
				'image/png',
				'image/jpeg',
				'image/jpg',
			];

			for ( $i = 0; $i < $files_count; $i++ ) {
				if ( in_array( $files['type'][ $i ], $extension, true ) ) {
					if ( 10 < number_format( $files['size'][ $i ] / 1048576, 2 ) ) {
						return [
							/* translators: %s: Size of the file */
							'error' => sprintf( esc_html__( 'Image %s is too large!', 'nfc-events' ), $files['name'][ $i ] ),
						];
					}

					$upload_dir = wp_upload_dir();
					$nfc_dir    = $upload_dir['basedir'] . '/nfc';
					$nfc_url    = $upload_dir['baseurl'] . '/nfc/';

					if ( ! file_exists( $nfc_dir ) ) {
						wp_mkdir_p( $nfc_dir );
					}

					$name = $files['name'][ $i ];

					$file_name = pathinfo( $name, PATHINFO_FILENAME ) . '-' . strtolower( $post_title ) . '-ID-' . $post_id . '.' . pathinfo( $name, PATHINFO_EXTENSION );
					$file_name = wp_unique_filename( $nfc_dir, $file_name );
					$upload    = wp_handle_upload( $files['tmp_name'][ $i ], array( 'test_form' => false ) );

					if ( ! $upload ) {
						return [
							/* translators: %s: Size of the file */
							'error' => sprintf( esc_html__( 'Error uploading image: %s', 'nfc-events' ), $name ),
						];
					}

					$urls[] = [
						'name' => $file_name,
						'url'  => $nfc_url . $file_name,
					];
				}
			}
		}

		$event_id = wp_insert_post(
			[
				'post_type'    => 'nfc_events',
				'post_title'   => $post_title,
				'post_content' => $note,
				'post_status'  => 'publish',
			]
		);

		if ( ! $event_id ) {
			return [
				/* translators: %s: Size of the file */
				'error' => sprintf( esc_html__( 'Event post could not be inserted!', 'nfc-events' ), $files['name'][ $i ] ),
			];
		}

		update_post_meta( $event_id, '_nfc_post_id', $post_id );
		update_post_meta( $event_id, '_nfc_unique', $unique );
		update_post_meta( $event_id, '_nfc_event_status_id', $event_status_id );
		update_post_meta( $event_id, '_nfc_user_id', get_current_user_id() );
		update_post_meta( $event_id, '_nfc_files', $urls );

		update_post_meta( $event_id, '_nfc_additionals', apply_filters( 'nfc_events_set_product_event_created', [], $event_id, $post_id, $unique, $event_status_id, get_current_user_id(), $urls ) );

		return $event_id;
	}

	/**
	 * Returns external NFC tag url query paramater.
	 *
	 * @return string
	 */
	public static function get_external_tag_url_param() {
		return '?nfc_product=';
	}

	/**
	 * Creates CSV file and appens tag urls into it.
	 *
	 * @param string $post_type Post type.
	 *
	 * @return string
	 */
	public static function get_tag_urls( $post_type ) {
		if ( ! $post_type ) {
			return false;
		}

		$file_name   = 'nfc-tag-urls-' . strtotime( gmdate( 'd M Y H:i:s' ) ) . '.csv';
		$uploads_dir = wp_get_upload_dir();
		$file        = $uploads_dir['basedir'] . '/' . $file_name;

		if ( ! file_exists( $file ) ) {
			file_put_contents( $file, '' ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
		}

		$handle = fopen( $file, 'w' ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
		fputcsv( $handle, [ 'NFC Tag URLs' ] ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite
		fclose( $handle ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
		ob_flush();

		$per_page = 50;
		$offset   = 0;
		$counter  = 0;

		do {
			$args = [
				'post_type'      => $post_type,
				'post_status'    => 'publish',
				'posts_per_page' => $per_page,
				'offset'         => $offset,
			];

			$loop = new \WP_Query( $args );

			if ( $loop->have_posts() ) {
				while ( $loop->have_posts() ) :
					$loop->the_post();
					++$offset;

					$handle = fopen( $file, 'a' ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen

					if ( false === $handle ) {
						continue;
					}

					$url = [
						get_permalink( get_the_id() ) . self::get_external_tag_url_param() . get_the_id(),
					];

					fputcsv( $handle, $url ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite
					fclose( $handle ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
					ob_flush();

					++$counter;
				endwhile;
			}
		} while ( $loop->post_count );

		return $uploads_dir['baseurl'] . '/' . $file_name;
	}

	/**
	 * Returns all event posts related to specific product ID.
	 *
	 * @param int $product_id Post ID.
	 *
	 * @return array
	 */
	public static function get_event_log( $product_id ) {
		$query = new \WP_Query(
			[
				'post_type'  => 'nfc_events',
				'meta_key'   => '_nfc_post_id',
				'meta_value' => $product_id,
			]
		);

		if ( $query->have_posts() ) {
			return $query->posts;
		}

		return false;
	}

	/**
	 * Deletes NFC resource file. (uploads/nfc/)
	 *
	 * @param string $directory Directory of file.
	 * @param string $file File.
	 *
	 * @return bool
	 */
	public static function delete_resource( $directory, $file ) {
		if ( ! $directory || ! $file ) {
			return;
		}

		$delete = wp_delete_file_from_directory( $file, $directory );

		return $delete;
	}
}
