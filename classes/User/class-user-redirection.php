<?php
/**
 * Redirection of user roles to predefiend pages by settings page.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\User;

use Nfc\Events\Singleton;

/**
 * Class Events_Redirection.
 */
class User_Redirection {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'template_redirect', [ $this, 'user_redirection' ] );
		add_action( 'template_redirect', [ $this, 'pages_redirection' ] );
		add_action( 'wp_footer', [ $this, 'not_logged_in_notice' ] );
		add_filter( 'register_url', [ $this, 'register_url' ] );
		add_action( 'register_new_user', [ $this, 'save_redirection_url_upon_registration' ] );
		add_filter( 'login_redirect', [ $this, 'login_redirect' ], 10, 3 );
	}

	/**
	 * Redirect user to appropriate page when
	 * NFC url param is present.
	 *
	 * @return void
	 */
	public function user_redirection() {
		$post_id    = apply_filters( 'nfc_events_tag_product_id', filter_input( INPUT_GET, 'nfc_product', FILTER_SANITIZE_ENCODED ) );
		$user_pages = get_option( 'nfc_events_setting_user_redirections' );
		$user       = wp_get_current_user();
		$user_role  = isset( $user->roles[0] ) ? $user->roles[0] : null;

		if ( ! $post_id ) {
			return;
		}

		if ( 'publish' !== apply_filters( 'nfc_events_user_redirection_post_status', get_post_status( $post_id ) ) ) {
			/**
			 * Redirect if post doesn't exits or not yet published to the fallback page or home.
			 */
			$url = isset( $user_pages['fallback'] ) && ! empty( $user_pages['fallback'] ) ? get_permalink( $user_pages['fallback'] ) : get_home_url();
		} elseif ( ! $user_role ) {
			/**
			 * Redirect to product if the user is not logged in.
			 */
			$nonce_url = wp_nonce_url(
				preg_match( '/[a-z]/i', $post_id ) ? get_home_url() : get_permalink( $post_id ),
				'nfc_nonce_not_logged_in',
				'nfc_nonce'
			);

			$url = add_query_arg(
				[
					'nfc_not_logged_in' => $post_id,
					'unique'            => filter_input( INPUT_GET, 'unique', FILTER_SANITIZE_ENCODED ),
					'type'              => apply_filters( 'nfc_events_redirection_url_type', '' ),
				],
				$nonce_url
			);
		} elseif ( ! isset( $user_pages[ $user_role ] ) ) {
			/**
			 * Redirect to product if the user role page is not set.
			 */
			$nonce_url = wp_nonce_url(
				get_permalink( $post_id ),
				'nfc_nonce'
			);

			$url = add_query_arg(
				[
					'unique' => filter_input( INPUT_GET, 'unique', FILTER_SANITIZE_ENCODED ),
					'type'   => apply_filters( 'nfc_events_redirection_url_type', '' ),
				],
				$nonce_url
			);
		} else {
			/**
			 * Redirect url to the user role page.
			 */
			$nonce_url = wp_nonce_url(
				get_permalink( $user_pages[ $user_role ] ),
				'nfc_nonce_' . $post_id,
				'nfc_nonce'
			);

			$url = add_query_arg(
				[
					'nfc_product_id' => $post_id,
					'unique'         => filter_input( INPUT_GET, 'unique', FILTER_SANITIZE_ENCODED ),
					'type'           => apply_filters( 'nfc_events_redirection_url_type', '' ),
				],
				$nonce_url
			);
		}

		wp_safe_redirect( $url );

		exit;
	}

	/**
	 * Check if a user can access/view NFC page CPT
	 * and if not redirect to home url.
	 *
	 * @return void
	 */
	public function pages_redirection() {
		if ( ! is_singular( 'nfc_pages' ) ) {
			return;
		}

		$user       = wp_get_current_user();
		$user_role  = isset( $user->roles[0] ) ? $user->roles[0] : null;
		$page_roles = get_post_meta( get_the_ID(), 'nfc_user_roles', true );

		if ( empty( $page_roles ) ) {
			return;
		}

		if ( ( ! $user_role || ! isset( $page_roles[ $user_role ] ) ) && 'administrator' !== $user_role ) {
			wp_safe_redirect( get_home_url() );

			exit;
		}
	}

	/**
	 * Outputs the template for not logged in user notice.
	 *
	 * @return string
	 */
	public function not_logged_in_notice() {
		if ( ! isset( $_GET['nfc_nonce'], $_GET['nfc_not_logged_in'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nfc_nonce'] ) ), 'nfc_nonce_not_logged_in' ) ) {
			return;
		}

		$template_args = [
			'redirection_url' => add_query_arg(
				[
					'nfc_product' => filter_input( INPUT_GET, 'nfc_not_logged_in', FILTER_SANITIZE_ENCODED ),
					'unique'      => filter_input( INPUT_GET, 'unique', FILTER_SANITIZE_ENCODED ),
				],
				get_home_url()
			),
		];

		load_template( NFC_EVENTS_TEMPLATE_PATH . 'nfc-product-not-logged-in-notice.php', false, $template_args );
	}

	/**
	 * Updates register url with redirection url that
	 * we will use for the first login of the new user.
	 *
	 * @param string $registration_url Redirection url.
	 *
	 * @return string
	 */
	public function register_url( $registration_url ) {
		$redirect_to = filter_input( INPUT_GET, 'redirect_to', FILTER_SANITIZE_ENCODED );

		if ( $redirect_to ) {
			$registration_url = $registration_url . '&nfc_redirect_to=' . $redirect_to;
		}

		return $registration_url;
	}

	/**
	 * Saves first time login redirection url onto user meta.
	 *
	 * @param string $user_id User ID.
	 *
	 * @return void
	 */
	public function save_redirection_url_upon_registration( $user_id ) {
		$url_components = isset( $_SERVER['HTTP_REFERER'] ) ? wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) ) : null;
		$parse_query    = $url_components ? wp_parse_str( $url_components['query'], $params ) : null;

		if ( isset( $params['nfc_redirect_to'] ) ) {
			update_user_meta( $user_id, '_nfc_redirect_login', $params['nfc_redirect_to'] );
		}
	}

	/**
	 * Redirect first login to the saved user meta
	 * which is the NFC redirect url which was saved upon registration.
	 *
	 * @param string $redirect_to Redirection URL.
	 * @param string $requested_redirect_to Requested redirect.
	 * @param object $user WP_User.
	 *
	 * @return string
	 */
	public function login_redirect( $redirect_to, $requested_redirect_to, $user ) {
		if ( ! isset( $user->ID ) ) {
			return $redirect_to;
		}

		$nfc_redirect_to = get_user_meta( $user->ID, '_nfc_redirect_login', true );

		if ( $nfc_redirect_to ) {
			update_user_meta( $user->ID, '_nfc_redirect_login', '' );

			return $nfc_redirect_to;
		}

		return $redirect_to;
	}
}
