<?php
/**
 * Admin resources page and its content.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Resources;

use Nfc\Events\Singleton;

/**
 * Class Resources_Admin.
 */
class Resources_Admin {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'admin_menu', [ $this, 'settings_page' ] );
		add_filter( 'admin_init', [ $this, 'settings_init' ] );
	}

	/**
	 * Register a resources menu page.
	 *
	 * @return void
	 */
	public function settings_page() {
		add_submenu_page(
			'nfc-events-admin',
			esc_html__( 'Resources', 'nfc-events' ),
			esc_html__( 'Resources', 'nfc-events' ),
			'manage_options',
			'nfc-events-admin-resources',
			[ $this, 'resources_page_contents' ],
			3,
		);
	}

	/**
	 * Resources page callback.
	 *
	 * @return void
	 */
	public function resources_page_contents() {
		?>
			<div class="nfc-events-settings-header">
				<div class="nfc-event-settings-header-title">
					<h1><?php esc_html_e( 'Resources', 'nfc-events' ); ?></h1>
				</div>
			</div>

			<div class="nfc-events-resources-content">
				<?php
				do_settings_sections( 'nfc_events_resources_tab_redirections' );
				?>
			</div>
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
			'',
			[ $this, 'redirection_section' ],
			'nfc_events_resources_tab_redirections'
		);
	}

	/**
	 * Redirection section.
	 *
	 * @return void
	 */
	public function redirection_section() {
		$uploads_dir = wp_get_upload_dir();
		$nfc_dir     = $uploads_dir['basedir'] . '/nfc/';
		$perpage     = 24;
		$page        = ( filter_input( INPUT_GET, 'nfc_page', FILTER_SANITIZE_ENCODED ) ) ? filter_input( INPUT_GET, 'nfc_page', FILTER_SANITIZE_ENCODED ) : 1;
		$offset      = ( $page - 1 ) * $perpage;
		$extensions  = [
			'jpg',
			'jpeg',
			'png',
		];

		$extension_pattern = implode( ',', $extensions );
		$extension_pattern = filter_input( INPUT_GET, 'type', FILTER_SANITIZE_ENCODED ) ? filter_input( INPUT_GET, 'type', FILTER_SANITIZE_ENCODED ) : $extension_pattern;

		if ( filter_input( INPUT_GET, 'product', FILTER_SANITIZE_ENCODED ) ) {
			$files = glob( $nfc_dir . '*-ID-' . filter_input( INPUT_GET, 'product', FILTER_SANITIZE_ENCODED ) . '.{' . $extension_pattern . '}', GLOB_BRACE );

			if ( ! $files ) {
				$files = glob( $nfc_dir . '*-' . filter_input( INPUT_GET, 'product', FILTER_SANITIZE_ENCODED ) . '-*.{' . $extension_pattern . '}', GLOB_BRACE );
			}
		} else {
			$files = glob( $nfc_dir . '*.{' . $extension_pattern . '}', GLOB_BRACE );
		}

		$total_files = count( $files );
		$total_pages = ceil( $total_files / $perpage );
		$files       = array_slice( $files, $offset, $perpage );
		$offset_to   = $total_files < ( $offset + $perpage ) ? $total_files : $offset + $perpage;
		?>

		<div class="nfc-events-resources-filter">
			<form action="<?php echo esc_url( html_entity_decode( get_pagenum_link() ) ); ?>" method="GET">
				<input type="text" name="product" placeholder="<?php echo esc_attr__( 'Product name or ID', 'nfc-events' ); ?>" value="<?php echo esc_attr( filter_input( INPUT_GET, 'product', FILTER_SANITIZE_ENCODED ) ); ?>"/>

				<select name="type" style="margin:-3px 8px 0 5px;">
					<option value=""><?php esc_html_e( 'All types', 'nfc-events' ); ?></option>

					<?php
					foreach ( (array) $extensions as $extension ) {
						$selected = filter_input( INPUT_GET, 'type', FILTER_SANITIZE_ENCODED ) === $extension ? 'selected' : '';
						?>
						<option value="<?php echo esc_attr( $extension ); ?>" <?php echo esc_attr( $selected ); ?>>
							<?php echo esc_html( strtoupper( $extension ) ); ?>
						</option>
						<?php
					}
					?>
				</select>

				<input type="hidden" name="page" value="<?php echo esc_attr( filter_input( INPUT_GET, 'page', FILTER_SANITIZE_ENCODED ) ); ?>">
				<input type="hidden" name="nfc_page" value="<?php echo esc_attr( filter_input( INPUT_GET, 'nfc_page', FILTER_SANITIZE_ENCODED ) ); ?>">
				<input type="submit" class="button" value="<?php echo esc_attr__( 'Filter', 'nfc-events' ); ?>"/>
			</form>
		</div>

		<?php
		if ( ! $files ) {
			esc_html_e( 'You have no files currently.', 'nfc-events' );

			return;
		}

		esc_html_e( 'Here you can find all the files related to your events. Click on item to view/download or click the X button to delete the item.', 'nfc-events' );
		?>

		<div class="nfc-events-resources-count">
			<p>
				<strong>
					<?php
					printf(
						/* translators: %1$s: offset from, %2$s: offset to, %3$s: number of total files */
						esc_html__( 'Showing: %1$s-%2$s of %3$s files', 'nfc-events' ),
						esc_html( $offset ),
						esc_html( $offset_to ),
						esc_html( $total_files )
					);
					?>
				</strong>
			</p>
		</div>

		<div class="nfc-events-resources">
			<?php
			foreach ( (array) $files as $file ) {
				$file_name = basename( $file );
				$file_url  = $uploads_dir['baseurl'] . '/nfc/' . $file_name;
				?>
					<div class="nfc-events-resource">
						<a href="<?php echo esc_url( $file_url ); ?>" title="<?php echo esc_attr( $file_name ); ?>" download>
							<span class="nfc-events-resources-preview" style="background-image:url('<?php echo esc_url( $file_url ); ?>');"></span>
							<span class="nfc-events-resources-preview-title"><?php echo esc_html( $file_name ); ?></span>
						</a>

						<span class="nfc-events-resources-delete" data-directory="<?php echo esc_attr( $nfc_dir ); ?>" data-file="<?php echo esc_attr( $file ); ?>"></span>
					</div>
				<?php
			}
			?>
		</div>

		<div class="nfc-events-resources-pagination">
			<?php
			echo wp_kses_post(
				paginate_links(
					[
						'base'    => add_query_arg( 'nfc_page', '%#%' ),
						'total'   => $total_pages,
						'current' => $page,
					]
				)
			);
			?>
		</div>
		<?php
	}
}
