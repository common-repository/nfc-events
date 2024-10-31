<?php
/**
 * Main class.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events;

use Nfc\Events\{
	Helpers\Helpers,
	Admin\Admin_Links,
	Admin\Admin_Settings,
	Admin\Admin_Filters,
	Enqueue\Enqueue_Admin,
	Enqueue\Enqueue_Front,
	Events\Events_Register,
	Events\Events_Columns,
	Events\Events_Meta_Boxes,
	Events\Events_Ajax,
	Events\Events_Bulk_Actions,
	Pages\Pages_Register,
	Pages\Pages_Meta_Boxes,
	Statuses\Statuses_Register,
	Statuses\Statuses_Meta_Boxes,
	Blocks\Shortcodes,
	Blocks\Blocks,
	Blocks\Blocks_Product_Image,
	Blocks\Blocks_Product_Title,
	Blocks\Blocks_Product_Event,
	Blocks\Blocks_Events_Log,
	Blocks\Blocks_Restricted_Content,
	Blocks\Patterns,
	Resources\Resources_Admin,
	Resources\Resources_Ajax,
	User\User_Roles,
	User\User_Redirection,
	Export\Export_CSV,
	Products\Products_Meta_Boxes,
	Products\Products_Fields,
	Rest\Rest_User_Roles,
	Rest\Rest_Register_Fields,
};

/**
 * Final class Main.
 */
final class Main {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		spl_autoload_register( [ $this, 'autoload' ] );

		Helpers::instance();
		Admin_Links::instance();
		Admin_Settings::instance();
		Admin_Filters::instance();
		Enqueue_Admin::instance();
		Enqueue_Front::instance();
		Events_Register::instance();
		Events_Columns::instance();
		Events_Meta_Boxes::instance();
		Events_Ajax::instance();
		Events_Bulk_Actions::instance();
		Pages_Register::instance();
		Pages_Meta_Boxes::instance();
		Statuses_Register::instance();
		Statuses_Meta_Boxes::instance();
		Shortcodes::instance();
		Blocks::instance();
		Blocks_Product_Image::instance();
		Blocks_Product_Title::instance();
		Blocks_Product_Event::instance();
		Blocks_Events_Log::instance();
		Blocks_Restricted_Content::instance();
		Patterns::instance();
		Resources_Admin::instance();
		Resources_Ajax::instance();
		User_Roles::instance();
		User_Redirection::instance();
		Export_CSV::instance();
		Products_Meta_Boxes::instance();
		Products_Fields::instance();
		Rest_User_Roles::instance();
		Rest_Register_Fields::instance();
	}

	/**
	 * Autoloader callback.
	 *
	 * @param string $class Class name.
	 *
	 * @return void
	 */
	private function autoload( $class ) {
		if ( strpos( $class, 'Nfc\Events\\' ) === false ) {
			return;
		}

		$file_name = str_replace( [ 'Nfc\Events\\', '\\' ], [ '', '/' ], $class );
		$chunks    = explode( '/', $file_name );
		$file_name = array_pop( $chunks );
		$file_name = 'class-' . strtolower( $file_name ) . '.php';
		$chunks[]  = $file_name;
		$path      = 'classes/' . str_replace( '_', '-', implode( '/', $chunks ) );

		require_once NFC_EVENTS_PATH . $path;
	}
}
