<?php
/**
 * Admin settings page and its content.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Admin;

use Nfc\Events\{
	Singleton,
	Helpers\Helpers,
	Helpers\Helpers_User,
};

/**
 * Class Admin_Settings.
 */
class Admin_Settings {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'settings_page' ] );
		add_action( 'admin_init', [ $this, 'settings_init' ] );
	}

	/**
	 * Adds a top-level menu page and its submenu pages.
	 *
	 * @return void
	 */
	public function settings_page() {
		add_menu_page(
			esc_html__( 'NFC Events', 'nfc-events' ),
			esc_html__( 'NFC Events', 'nfc-events' ),
			'manage_options',
			'nfc-events-admin',
			'',
			'dashicons-schedule',
			56
		);

		add_submenu_page(
			'nfc-events-admin',
			esc_html__( 'Settings', 'nfc-events' ),
			esc_html__( 'Settings', 'nfc-events' ),
			'manage_options',
			'nfc-events-admin-settings',
			[ $this, 'settings_page_contents' ],
			50,
		);
	}

	/**
	 * Settings page header and navigation.
	 *
	 * @return void
	 */
	public function settings_page_contents() {
		$current_tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_ENCODED );
		?>
			<div class="nfc-events-settings-header">
				<div class="nfc-event-settings-header-title">
					<h1><?php esc_html_e( 'NFC Events', 'nfc-events' ); ?></h1>
				</div>

				<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
					<?php
					$tabs = [
						'redirections' => esc_html__( 'Redirections', 'nfc-events' ),
						'roles'        => esc_html__( 'Roles', 'nfc-events' ),
						'export'       => esc_html__( 'Export', 'nfc-events' ),
					];

					$tabs = apply_filters( 'nfc_events_settings_tabs', $tabs );

					foreach ( $tabs as $tab => $tab_name ) {
						$class = ( $current_tab === $tab ) ? 'nav-tab-active' : ( ! $current_tab && array_key_first( $tabs ) === $tab ? 'nav-tab-active' : '' );
						?>
							<a href="?page=<?php echo esc_attr( filter_input( INPUT_GET, 'page', FILTER_SANITIZE_ENCODED ) ); ?>&tab=<?php echo esc_attr( $tab ); ?>" class="nav-tab <?php echo esc_attr( $class ); ?>">
								<?php echo esc_html( $tab_name ); ?>
							</a>
						<?php
					}
					?>
				</nav>
			</div>

			<form method="POST" action="options.php">
				<?php
				if ( ! $current_tab ) {
					settings_fields( 'nfc_events_settings_tab_' . array_key_first( $tabs ) );
					do_settings_sections( 'nfc_events_settings_tab_' . array_key_first( $tabs ) );
					submit_button();
				}

				foreach ( (array) $tabs as $tab => $tab_name ) {
					if ( $current_tab === $tab ) {
						settings_fields( 'nfc_events_settings_tab_' . $tab );
						do_settings_sections( 'nfc_events_settings_tab_' . $tab );

						if ( 'export' !== $tab ) {
							submit_button();
						}
					}
				}
				?>
			</form>
		<?php
	}

	/**
	 * Settings form elements.
	 *
	 * @return void
	 */
	public function settings_init() {
		/**
		 * User page settings.
		 */
		add_settings_section(
			'nfc_events_settings_user_section',
			esc_html__( 'User Redirection', 'nfc-events' ),
			[ $this, 'redirection_section' ],
			'nfc_events_settings_tab_redirections'
		);

		add_settings_field(
			'nfc_events_setting_user_redirections',
			esc_html__( 'User pages', 'nfc-events' ),
			[ $this, 'user_redirections_field' ],
			'nfc_events_settings_tab_redirections',
			'nfc_events_settings_user_section'
		);

		register_setting( 'nfc_events_settings_tab_redirections', 'nfc_events_setting_user_redirections' );

		/**
		 * User roles settings.
		 */
		add_settings_section(
			'nfc_events_settings_user_roles_section',
			esc_html__( 'User Roles', 'nfc-events' ),
			[ $this, 'roles_section' ],
			'nfc_events_settings_tab_roles'
		);

		add_settings_field(
			'nfc_events_setting_user_roles',
			esc_html__( 'Add NFC user roles', 'nfc-events' ),
			[ $this, 'user_roles_field' ],
			'nfc_events_settings_tab_roles',
			'nfc_events_settings_user_roles_section'
		);

		register_setting( 'nfc_events_settings_tab_roles', 'nfc_events_setting_user_roles' );

		/**
		 * Export settings.
		 */

		add_settings_section(
			'nfc_events_settings_export_section',
			esc_html__( 'Export', 'nfc-events' ),
			[ $this, 'export_section' ],
			'nfc_events_settings_tab_export'
		);

		add_settings_field(
			'nfc_events_setting_export',
			esc_html__( 'Export tag URLs', 'nfc-events' ),
			[ $this, 'export_field' ],
			'nfc_events_settings_tab_export',
			'nfc_events_settings_export_section'
		);

		add_settings_field(
			'nfc_events_setting_events_export',
			esc_html__( 'Export events', 'nfc-events' ),
			[ $this, 'export_events_field' ],
			'nfc_events_settings_tab_export',
			'nfc_events_settings_export_section'
		);
	}

	/**
	 * Redirection section.
	 *
	 * @return void
	 */
	public function redirection_section() {
		esc_html_e( 'Select pages for available user roles. Logged in user with these roles will be redirected to their selected pages.', 'nfc-events' );
	}

	/**
	 * User redirection setting field.
	 *
	 * @return void
	 */
	public function user_redirections_field() {
		$user_roles = Helpers_User::get_all_roles();

		if ( ! $user_roles ) {
			return;
		}

		$template_args = [
			'user_roles'    => $user_roles,
			'pages'         => get_posts(
				[
					'post_type'   => 'nfc_pages',
					'numberposts' => -1,
				]
			),
			'setting_value' => get_option( 'nfc_events_setting_user_redirections' ),
		];

		load_template( NFC_EVENTS_TEMPLATE_PATH . 'nfc-settings-user-redirections.php', false, $template_args );
	}

	/**
	 * Redirection section.
	 *
	 * @return void
	 */
	public function roles_section() {
		esc_html_e( 'Create, archive or remove custom user roles.', 'nfc-events' );
	}

	/**
	 * User roles settings field.
	 *
	 * @return void
	 */
	public function user_roles_field() {
		$user_roles = Helpers_User::get_all_default_roles( true );

		if ( ! $user_roles ) {
			return;
		}

		$saved_roles = get_option( 'nfc_events_setting_user_roles' );

		?>
		<div class="nfc-user-roles-labels">
			<p><?php esc_html_e( 'User role name', 'nfc-events' ); ?></p>
			<p><?php esc_html_e( 'Set capability', 'nfc-events' ); ?></p>
		</div>

		<div class="nfc-user-roles">
			<div class="nfc-user-roles-fields">
				<?php
				if ( $saved_roles ) {
					foreach ( (array) $saved_roles as $role_key => $values ) {
						$this->user_role_single( $user_roles, $role_key, $values );
					}
				} else {
					$this->user_role_single( $user_roles, 0 );
				}
				?>
			</div>

			<div class="nfc-user-roles-fields-info">
				<p>
					<?php
						printf(
							/* translators: %1$s: wp user caps doc url, %2$s: woo user caps doc url */
							esc_html__( 'The second column field is for you to set what capabilities the new user role will inherit. The capabilities to chose from are from WordPress and WooCommerce. To learn more about user capabilites please take a look at: %1$s %2$s', 'nfc-events' ),
							'<a href="https://wordpress.org/documentation/article/roles-and-capabilities/">https://wordpress.org/documentation/article/roles-and-capabilities/</a>',
							'<a href="https://woocommerce.com/document/roles-capabilities/">https://woocommerce.com/document/roles-capabilities/</a>'
						);
					?>
				</p>
			</div>

			<div class="nfc-user-roles-add button button-primary">
				<?php esc_html_e( 'Create role +', 'nfc-events' ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Separated single template for user role setting field group.
	 *
	 * @param array  $user_roles Available user roles.
	 * @param string $role_key Input name key.
	 * @param array  $values Previously saved values [name, cap].
	 *
	 * @return void
	 */
	public function user_role_single( $user_roles, $role_key, $values = null ) {
		$is_archived = ( isset( $values['archived'] ) && 'on' === $values['archived'] ) ? true : false;
		?>
		<div class="nfc-user-roles-field <?php echo esc_attr( $is_archived ? '--archived' : '' ); ?>">
			<input type="text" name="nfc_events_setting_user_roles[<?php echo esc_attr( $role_key ); ?>][name]" data-role-key="<?php echo esc_attr( (int) $role_key ); ?>" value="<?php echo esc_attr( isset( $values['name'] ) ? $values['name'] : '' ); ?>" placeholder="<?php esc_attr_e( 'Name', 'nfc-events' ); ?>"/>

			<select name="nfc_events_setting_user_roles[<?php echo esc_attr( $role_key ); ?>][cap]" data-role-key="<?php echo esc_attr( (int) $role_key ); ?>" title="<?php esc_attr_e( 'Select user capabilities', 'nfc-events' ); ?>">
				<option value="" class="option-placeholder">
					<?php esc_html_e( 'Select capability', 'nfc-events' ); ?>
				</option>

				<?php
				foreach ( (array) $user_roles as $user_slug => $user_role ) {
					$selected = ( isset( $values['cap'] ) && $values['cap'] === $user_slug ) ? 'selected' : '';
					?>
					<option value="<?php echo esc_attr( $user_slug ); ?>" <?php echo esc_attr( $selected ); ?>>
						<?php echo esc_html( $user_role['name'] ); ?>
					</option>
					<?php
				}
				?>
			</select>

			<span class="nfc-user-roles-archive">
				<label for="nfc_events_archive_role_<?php echo esc_attr( $role_key ); ?>">
					<span class="button"><?php esc_html_e( 'Archive', 'nfc-events' ); ?></span>
					<span class="button"><?php esc_html_e( 'Unarchive', 'nfc-events' ); ?></span>
				</label>

				<input type="checkbox" name="nfc_events_setting_user_roles[<?php echo esc_attr( $role_key ); ?>][archived]" id="nfc_events_archive_role_<?php echo esc_attr( $role_key ); ?>" data-role-key="<?php echo esc_attr( (int) $role_key ); ?>" <?php checked( $is_archived, true ); ?>/>
			</span>

			<span class="nfc-user-roles-remove">
				<?php esc_html_e( 'Delete', 'nfc-events' ); ?>
			</span>
		</div>
		<?php
	}

	/**
	 * Redirection section.
	 *
	 * @return void
	 */
	public function export_section() {
		esc_html_e( 'Easily export all NFC tag URLs or all of your event posts into CSV files.', 'nfc-events' );
	}

	/**
	 * Export CSV settings field.
	 *
	 * @return void
	 */
	public function export_field() {
		global $wp_post_types;

		if ( ! $wp_post_types ) {
			esc_html_e( 'You have no post types, export is disabled right now!', 'nfc-events' );

			return;
		}

		$template_args = [
			'post_types'  => $wp_post_types,
			'query_param' => Helpers::get_external_tag_url_param(),
			'token'       => $this::admin_token(),
		];

		load_template( NFC_EVENTS_TEMPLATE_PATH . 'nfc-settings-export-tag-urls.php', false, $template_args );
	}

	/**
	 * Export CSV settings field.
	 *
	 * @return void
	 */
	public function export_events_field() {
		$events = get_posts(
			[
				'post_type'   => 'nfc_events',
				'numberposts' => -1,
			]
		);

		if ( ! $events ) {
			esc_html_e( 'Currently you have no events, export is disabled!', 'nfc-events' );

			return;
		}

		$template_args = [
			'type'  => 'nfc_events',
			'count' => count( $events ),
			'token' => $this::admin_token(),
		];

		load_template( NFC_EVENTS_TEMPLATE_PATH . 'nfc-settings-export-events.php', false, $template_args );
	}

	/**
	 * Returns admin uuid token. Creates it if doesn't exist or was expired.
	 *
	 * @return string
	 */
	public static function admin_token() {
		$token = get_transient( 'nfc_admin_token' );

		if ( ! $token ) {
			$token = wp_generate_uuid4();

			set_transient( 'nfc_admin_token', $token, 14400 ); // Expiration 4 hours.
		}

		return $token;
	}
}
