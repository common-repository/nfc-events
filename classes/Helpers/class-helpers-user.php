<?php
/**
 * Helpers methods all regarding user/users.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Helpers;

use Nfc\Events\Singleton;

/**
 * Class Helpers_User.
 */
class Helpers_User extends Helpers {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Returns available user roles.
	 *
	 * @param boolean $unset_admin Unset admin role or not.
	 *
	 * @return array
	 */
	public static function get_all_roles( $unset_admin = false ) {
		global $wp_roles;

		if ( ! $wp_roles ) {
			return [];
		}

		$user_roles = $wp_roles->roles;

		if ( $unset_admin && isset( $user_roles['administrator'] ) ) {
			unset( $user_roles['administrator'] );
		}

		return apply_filters( 'nfc_events_available_user_roles', $user_roles );
	}

	/**
	 * Returns available user roles without nfc custom ones.
	 *
	 * @param boolean $unset_admin Unset admin role or not.
	 *
	 * @return array
	 */
	public static function get_all_default_roles( $unset_admin = false ) {
		$user_roles = self::get_all_roles( $unset_admin );

		foreach ( $user_roles as $role_key => $role ) {
			if ( isset( $role['capabilities']['nfc_user_role'] ) && true === $role['capabilities']['nfc_user_role'] ) {
				unset( $user_roles[ $role_key ] );
			}
		}

		return apply_filters( 'nfc_events_available_default_user_roles', $user_roles );
	}

	/**
	 * Returns only custom made nfc available user roles.
	 *
	 * @param boolean $unset_admin Unset admin role or not.
	 *
	 * @return array
	 */
	public static function get_all_nfc_roles( $unset_admin = false ) {
		$user_roles = self::get_all_roles( $unset_admin );

		foreach ( $user_roles as $role_key => $role ) {
			if ( ! $unset_admin && 'administrator' === $role_key ) {
				continue;
			}

			if ( ! isset( $role['capabilities']['nfc_user_role'] ) || true !== $role['capabilities']['nfc_user_role'] ) {
				unset( $user_roles[ $role_key ] );
			}
		}

		return apply_filters( 'nfc_events_available_nfc_user_roles', $user_roles );
	}

	/**
	 * Returns what is a user role.
	 *
	 * @param string $user_id User ID.
	 *
	 * @return string
	 */
	public static function get_user_role( $user_id ) {
		$user = get_userdata( $user_id );

		if ( ! $user ) {
			return esc_html__( 'No such user', 'nfc-events' );
		}

		return apply_filters( 'nfc_events_get_user_role', $user->roles[0] );
	}

	/**
	 * Check if user is an administrator.
	 *
	 * @param string $user_id User ID.
	 *
	 * @return bool
	 */
	public static function is_user_admin( $user_id = null ) {
		$user_id = $user_id ? $user_id : get_current_user_id();

		if ( 'administrator' === self::get_user_role( $user_id ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Returns event statuses posts for specific user role.
	 *
	 * @param object $user WP_User.
	 * @param string $user_role WP user role.
	 *
	 * @return array
	 */
	public static function get_event_statuses( $user = null, $user_role = null ) {
		if ( ! $user_role ) {
			if ( ! $user ) {
				$user = wp_get_current_user();
			}

			$user_role = isset( $user->roles[0] ) ? $user->roles[0] : null;
		}

		$event_statuses = get_posts(
			[
				'post_type'   => 'nfc_event_statuses',
				'numberposts' => -1,
			]
		);

		if ( 'administrator' === $user_role ) {
			return $event_statuses;
		}

		foreach ( (array) $event_statuses as $key => $event_status ) {
			$event_status_roles = get_post_meta( $event_status->ID, 'nfc_user_roles', true );

			if ( ! $event_status_roles || isset( $event_status_roles[ $user_role ] ) ) {
				continue;
			}

			unset( $event_statuses[ $key ] );
		}

		return array_values( $event_statuses ); // Reindex array keys cause we had unset some.
	}

	/**
	 * Checks whether the GET request is admin exporter.
	 *
	 * @return bool
	 */
	public static function admin_export() {
		$token     = filter_input( INPUT_GET, 'token', FILTER_SANITIZE_ENCODED );
		$transient = get_transient( 'nfc_admin_token' );

		if ( ! $token || ! $transient || $token !== $transient ) {
			return false;
		}

		return true;
	}
}
