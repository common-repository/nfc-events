<?php
/**
 * Singleton.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events;

defined( 'ABSPATH' ) || die();

trait Singleton {

	/**
	 * Self.
	 *
	 * @var self
	 */
	private static $instance = null;

	/**
	 * Instanciate the class
	 *
	 * @return self
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
