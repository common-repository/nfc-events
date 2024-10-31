<?php
/**
 * Patterns library.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Blocks;

use Nfc\Events\Singleton;

/**
 * Class Patterns.
 */
class Patterns {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'set_event_form' ] );
	}

	/**
	 * Registers block patterns category and patterns.
	 *
	 * @return void
	 */
	public function set_event_form() {
		if ( ! function_exists( 'register_block_pattern_category' ) ) {
			return;
		}

		register_block_pattern_category(
			'nfc-events',
			[ 'label' => esc_html__( 'NFC Events', 'nfc-events' ) ]
		);

		//phpcs:disable WordPressVIPMinimum.Security.Mustache.OutputNotation
		register_block_pattern(
			'nfc-events/set-event-form',
			[
				'title'      => esc_html__( 'Event Form Pattern', 'nfc-events' ),
				'categories' => [ 'nfc-events' ],
				'content'    => '<!-- wp:columns {"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}}}} -->
				<div class="wp-block-columns" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:column {"width":"46.5%"} -->
				<div class="wp-block-column" style="flex-basis:46.5%"><!-- wp:nfc-events/product-image /-->

				<!-- wp:nfc-events/restricted-content {"users":[{"value":"test_warehouse","label":"Test warehouse"}]} -->
				<div class="wp-block-nfc-events-restricted-content"><!-- wp:buttons -->
				<div class="wp-block-buttons"><!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="https://nfce.local/nfc_pages/event-log/" target="_blank" rel="noreferrer noopener">See event log</a></div>
				<!-- /wp:button --></div>
				<!-- /wp:buttons --></div>
				<!-- /wp:nfc-events/restricted-content --></div>
				<!-- /wp:column -->

				<!-- wp:column {"width":""} -->
				<div class="wp-block-column"></div>
				<!-- /wp:column -->

				<!-- wp:column {"width":"46.5%"} -->
				<div class="wp-block-column" style="flex-basis:46.5%"><!-- wp:nfc-events/product-title /-->

				<!-- wp:nfc-events/product-event /--></div>
				<!-- /wp:column --></div>
				<!-- /wp:columns -->',
			]
		);
	}
}
