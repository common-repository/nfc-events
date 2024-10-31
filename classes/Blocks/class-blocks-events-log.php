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
 * Class Blocks_Events_Log.
 */
class Blocks_Events_Log extends Blocks {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Render callback
	 *
	 * @var bool
	 */
	protected $render_callback = true;

	/**
	 * Block name
	 *
	 * @var string
	 */
	protected $blockname = 'events-log';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_block_type' ] );
	}

	/**
	 * Server render callback.
	 *
	 * @param array  $block_attributes Block attributes.
	 * @param string $content Content.
	 *
	 * @return string
	 */
	public function render_callback( $block_attributes, $content ) {
		$wrapper_attributes = get_block_wrapper_attributes();

		ob_start();

		echo do_shortcode( '[nfc_events_log]' );

		$template = ob_get_clean();

		return sprintf(
			'<div %1$s>%2$s</div>',
			$wrapper_attributes,
			$template,
		);
	}
}
