<?php
/**
 * Meta boxes for custom post type NFC Pages.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Pages;

use Nfc\Events\{
	Singleton,
	Helpers\Helpers_User,
};

/**
 * Class Statuses_Meta_Boxes.
 */
class Pages_Meta_Boxes {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'add_meta_boxes', [ $this, 'meta_boxes' ] );
		add_action( 'save_post', [ $this, 'save_meta_data' ] );
	}

	/**
	 * Adds meta boxes to CPT Events.
	 *
	 * @return void
	 */
	public function meta_boxes() {
		add_meta_box(
			'nfc-pages-user-roles',
			esc_html__( 'Allowed user roles', 'nfc-events' ),
			[ $this, 'user_roles_callback' ],
			'nfc_pages',
			'side'
		);
	}

	/**
	 * User roles meta box callback.
	 *
	 * @param object $post WP_Post.
	 *
	 * @return void
	 */
	public function user_roles_callback( $post ) {
		$user_roles = Helpers_User::get_all_nfc_roles( true );

		$template_args = [
			'user_roles'       => $user_roles,
			'saved_user_roles' => get_post_meta( $post->ID, 'nfc_user_roles', true ),
		];

		load_template( NFC_EVENTS_TEMPLATE_PATH . 'nfc-events-user-roles.php', false, $template_args );

		/**
		 * Nonce.
		 */
		wp_nonce_field( 'nfc_pages_user_roles_nonce', 'nfc_pages_user_roles_nonce' );
	}

	/**
	 * Saves post data.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return void
	 */
	public function save_meta_data( $post_id ) {
		if ( ! isset( $_POST['nfc_pages_user_roles_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nfc_pages_user_roles_nonce'] ) ), 'nfc_pages_user_roles_nonce' ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
			return;
		}

 		//phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		update_post_meta( $post_id, 'nfc_user_roles', isset( $_POST['nfc_user_roles'] ) ? sanitize_key( $_POST['nfc_user_roles'] ) : '' );
	}
}
