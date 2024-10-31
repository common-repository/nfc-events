<?php
/**
 * Register custom post type NFC Events and sets options.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Events;

use Nfc\Events\Singleton;

/**
 * Class Events_Register.
 */
class Events_Register {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'init', [ $this, 'register_events_post_type' ], 10 );
	}

	/**
	 * Registers CPT Events.
	 *
	 * @return void
	 */
	public function register_events_post_type() {
		$labels = array(
			'name'               => _x( 'Events', 'Post Type General Name', 'nfc-events' ),
			'singular_name'      => _x( 'Event', 'Post Type Singular Name', 'nfc-events' ),
			'menu_name'          => esc_html__( 'Events', 'nfc-events' ),
			'parent_item_colon'  => esc_html__( 'Parent Event', 'nfc-events' ),
			'all_items'          => esc_html__( 'Events', 'nfc-events' ),
			'view_item'          => esc_html__( 'View Event', 'nfc-events' ),
			'add_new_item'       => esc_html__( 'Add New Event', 'nfc-events' ),
			'add_new'            => esc_html__( 'Add New', 'nfc-events' ),
			'edit_item'          => esc_html__( 'Edit Event', 'nfc-events' ),
			'update_item'        => esc_html__( 'Update Event', 'nfc-events' ),
			'search_items'       => esc_html__( 'Search Event', 'nfc-events' ),
			'not_found'          => esc_html__( 'Not Found', 'nfc-events' ),
			'not_found_in_trash' => esc_html__( 'Not found in Trash', 'nfc-events' ),
		);

		$args = array(
			'label'               => 'nfc_events',
			'description'         => esc_html__( 'This is where post events are stored.', 'nfc-events' ),
			'labels'              => $labels,
			'supports'            => [
				'title',
			],
			'public'              => false,
			'show_ui'             => true,
			'capability_type'     => 'post',
			'capabilities'        => [
				'create_posts' => false,
			],
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

		register_post_type( 'nfc_events', apply_filters( 'nfc_events_register_events_cpt_args', $args, $labels ) );
	}
}
