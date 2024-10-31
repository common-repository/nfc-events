<?php
/**
 * Adds custom user roles based from setting user roles fields.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\User;

use Nfc\Events\{
	Singleton,
	Helpers\Helpers_User,
};

/**
 * Class User_Roles.
 */
class User_Roles {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'add_roles' ], 10 );
		add_action( 'admin_init', [ $this, 'remove_roles' ], 11 );
	}

	/**
	 * Adds user roles based on settings user roles fields.
	 *
	 * @return void
	 */
	public function add_roles() {
		$roles = get_option( 'nfc_events_setting_user_roles' );

		if ( ! $roles ) {
			return;
		}

		foreach ( (array) $roles as $role ) {
			if ( ! isset( $role['name'], $role['cap'] ) || empty( $role['name'] ) || empty( $role['cap'] ) || isset( $role['archived'] ) ) {
				continue;
			}

			$caps = get_role( $role['cap'] )->capabilities;

			if ( ! $caps ) {
				continue;
			}

			$caps = $caps + [ 'nfc_user_role' => true ];

			add_role(
				strtolower( str_replace( ' ', '_', $role['name'] ) ),
				$role['name'],
				$caps
			);
		}
	}

	/**
	 * Removes user roles based on settings user roles fields.
	 *
	 * @return void
	 */
	public function remove_roles() {
		$custom_roles = get_option( 'nfc_events_setting_user_roles' );

		if ( ! $custom_roles ) {
			return;
		}

		$user_roles = Helpers_User::get_all_roles( true );

		foreach ( (array) $user_roles as $user_role_slug => $user_role ) {
			$in_custom_roles = array_search( $user_role['name'], array_column( $custom_roles, 'name' ), true );

			if ( isset( $user_role['capabilities']['nfc_user_role'] ) && true === $user_role['capabilities']['nfc_user_role'] && ( false === $in_custom_roles || isset( $custom_roles[ $in_custom_roles ]['archived'] ) ) ) {
				remove_role( $user_role_slug );
			}
		}
	}
}
