<?php
/**
 * Plugin Name:       NFC Events
 * Description:       Create unique NFC product tag URLs, custom user roles, NFC pages and blocks, allow specific users to create events by scanning NFC tags, manage all events and more.
 * Author:            Science Park Borås
 * Author URI:        https://maksimer.com
 * Text Domain:       nfc-events
 * Domain Path:       /languages
 * Version:           1.0.0
 * Copyright:         © 2024 Science Park Borås.
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package         Nfc\Events;
 */

namespace Nfc\Events;

defined( 'ABSPATH' ) || die();

define( 'NFC_EVENTS_VERSION', '1.0.0' );
define( 'NFC_EVENTS_PATH', plugin_dir_path( __FILE__ ) );
define( 'NFC_EVENTS_URL', plugin_dir_url( __FILE__ ) );
define( 'NFC_EVENTS_TEMPLATE_PATH', plugin_dir_path( __FILE__ ) . 'templates/' );
define( 'NFC_EVENTS_BASENAME', plugin_basename( __FILE__ ) );
define( 'NFC_EVENTS_BLOCKS_BUILD_PATH', plugin_dir_path( __FILE__ ) . 'build/blocks/' );
define( 'NFC_EVENTS_BLOCKS_BUILD_URL', plugin_dir_url( __FILE__ ) . 'build/blocks/' );

/**
 * Plugin textdomain.
 */
load_plugin_textdomain( 'nfc-events', false, dirname( NFC_EVENTS_BASENAME ) . '/languages' );

/**
 * Require the main class.
 */
if ( ! class_exists( 'Main', false ) ) {
	require_once NFC_EVENTS_PATH . 'classes/class-singleton.php';
	require_once NFC_EVENTS_PATH . 'classes/class-main.php';
}

/**
 * Returns the main instance.
 *
 * @return Main
 */
function nfc_events() {
	return Main::instance();
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\nfc_events' );


/**
 * Activation plugin hook. Flush of the permalinks.
 */
function nfc_events_activate() {
	flush_rewrite_rules(); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\\nfc_events_activate' );
