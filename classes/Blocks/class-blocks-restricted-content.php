<?php
/**
 * Register block and set server render callback.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Blocks;

use Nfc\Events\{
	Singleton,
	Blocks\Blocks,
};
use Nfc\Events\Helpers\Helpers_User;

defined( 'ABSPATH' ) || die();

/**
 * Class Blocks_Restricted_Content.
 */
class Blocks_Restricted_Content extends Blocks {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Block name
	 *
	 * @var string
	 */
	protected $blockname = 'restricted-content';

	/**
	 * Render callback
	 *
	 * @var bool
	 */
	protected $render_callback = true;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_block_type' ] );
	}

	/**
	 * Server render callback with a check for allowed user roles to view the innerBlocks content.
	 *
	 * @param array  $block_attributes Block attributes.
	 * @param string $content Content.
	 *
	 * @return string
	 */
	public function render_callback( $block_attributes, $content ) {
		$allowed_roles = isset( $block_attributes['users'] ) ? $block_attributes['users'] : null;

		if ( $allowed_roles && ! is_user_logged_in() ) {
			$content = '';
		}

		$current_user_role = Helpers_User::get_user_role( get_current_user_id() );

		if ( 'administrator' !== $current_user_role && isset( $current_user_role ) && false === array_search( $current_user_role, array_column( $allowed_roles, 'value' ), true ) ) {
			$content = '';
		}

		$wrapper_attributes = get_block_wrapper_attributes();

		ob_start();

		echo wp_kses_post( $content );

		$template = ob_get_clean();

		return sprintf(
			'<div %1$s>%2$s</div>',
			$wrapper_attributes,
			$template,
		);
	}
}
