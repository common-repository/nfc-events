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

defined( 'ABSPATH' ) || die();

/**
 * Class Blocks_Product_Event.
 */
class Blocks_Product_Event extends Blocks {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Block name
	 *
	 * @var string
	 */
	protected $blockname = 'product-event';

	/**
	 * Render callback
	 *
	 * @var bool
	 */
	protected $render_callback = true;

	/**
	 * Attributes
	 *
	 * @var array
	 */
	protected $attributes = [
		'users' => [
			'type' => 'object',
		],
	];

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
		$wrapper_attributes = get_block_wrapper_attributes();

		ob_start();

		$user_role = isset( $block_attributes['users'] ) ? $block_attributes['users']['value'] : '';

		echo do_shortcode( '[nfc_product_event user_role="' . $user_role . '"]' );

		echo wp_kses_post( $content );

		$template = ob_get_clean();

		return sprintf(
			'<div %1$s>%2$s</div>',
			$wrapper_attributes,
			$template,
		);
	}
}
