<?php
/**
 * Register custom post type NFC Statuses and sets options.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Statuses;

use Nfc\Events\Singleton;

/**
 * Class Statuses_Register.
 */
class Statuses_Register {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'init', [ $this, 'register_statuses_post_type' ], 20 );
	}

	/**
	 * Registers CPT NFC Statuses.
	 *
	 * @return void
	 */
	public function register_statuses_post_type() {
		$labels = array(
			'name'               => _x( 'Event Statuses', 'Post Type General Name', 'nfc-events' ),
			'singular_name'      => _x( 'Event Status', 'Post Type Singular Name', 'nfc-events' ),
			'menu_name'          => esc_html__( 'Event Statuses', 'nfc-events' ),
			'parent_item_colon'  => esc_html__( 'Parent Event Status', 'nfc-events' ),
			'all_items'          => esc_html__( 'Statuses', 'nfc-events' ),
			'view_item'          => esc_html__( 'View Event Statuses', 'nfc-events' ),
			'add_new_item'       => esc_html__( 'Add New Event Status', 'nfc-events' ),
			'add_new'            => esc_html__( 'Add New', 'nfc-events' ),
			'edit_item'          => esc_html__( 'Edit Event Status', 'nfc-events' ),
			'update_item'        => esc_html__( 'Update Event Status', 'nfc-events' ),
			'search_items'       => esc_html__( 'Search Event Status', 'nfc-events' ),
			'not_found'          => esc_html__( 'Not Found', 'nfc-events' ),
			'not_found_in_trash' => esc_html__( 'Not found in Trash', 'nfc-events' ),
		);

		$args = array(
			'label'               => 'nfc-event-statuses',
			'description'         => esc_html__( 'This is where events statuses are created.', 'nfc-events' ),
			'labels'              => $labels,
			'supports'            => [
				'title',
			],
			'public'              => false,
			'show_ui'             => true,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_in_menu'        => 'nfc-events-admin',
			'hierarchical'        => false,
			'show_in_nav_menus'   => false,
			'rewrite'             => false,
			'query_var'           => false,
			'has_archive'         => false,
			'show_in_rest'        => true,
		);

		register_post_type( 'nfc_event_statuses', apply_filters( 'nfc_events_register_event_statuses_cpt_args', $args, $labels ) );
	}
}
