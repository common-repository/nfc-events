<?php
/**
 * Rest endpoint returning all active user roles.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Rest;

use Nfc\Events\{
	Singleton,
	Helpers\Helpers_User
};

/**
 * Class Rest_User_Roles.
 */
class Rest_User_Roles {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
	}

	/**
	 * Register the rest route.
	 */
	public function register_rest_routes() {
		register_rest_route(
			'nfc/v1',
			'/roles',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_all_roles' ],
				'permission_callback' => [ $this, 'permission_check' ],
			]
		);

		register_rest_route(
			'nfc/v1',
			'/roles/(?P<id>\d+)',
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_user_role' ],
				'permission_callback' => [ $this, 'permission_check' ],
			]
		);
	}

	/**
	 * Get all user roles route callback.
	 *
	 * @return array
	 */
	public function get_all_roles() {
		return Helpers_User::get_all_nfc_roles( true );
	}

	/**
	 * Get specific user role route callback.
	 *
	 * @param object $request Request.
	 *
	 * @return string
	 */
	public function get_user_role( $request ) {
		$user_id = $request->get_param( 'id' );

		return Helpers_User::get_user_role( $user_id );
	}

	/**
	 * Restrict endpoint to browsers that have the wp-postpass_ cookie.
	 *
	 * @return bool
	 */
	public function permission_check() {
		return current_user_can( 'edit_others_posts' );
	}
}
