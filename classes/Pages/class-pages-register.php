<?php
/**
 * Register CPT NFC Pages and sets options.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Pages;

use Nfc\Events\Singleton;

/**
 * Class Pages_Register.
 */
class Pages_Register {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'init', [ $this, 'register_pages_post_type' ], 30 );
	}

	/**
	 * Registers CPT NFC Pages.
	 *
	 * @return void
	 */
	public function register_pages_post_type() {
		$labels = array(
			'name'               => _x( 'Pages', 'Post Type General Name', 'nfc-events' ),
			'singular_name'      => _x( 'Page', 'Post Type Singular Name', 'nfc-events' ),
			'menu_name'          => esc_html__( 'Pages', 'nfc-events' ),
			'parent_item_colon'  => esc_html__( 'Parent Page', 'nfc-events' ),
			'all_items'          => esc_html__( 'Pages', 'nfc-events' ),
			'view_item'          => esc_html__( 'View Page', 'nfc-events' ),
			'add_new_item'       => esc_html__( 'Add New Page', 'nfc-events' ),
			'add_new'            => esc_html__( 'Add New', 'nfc-events' ),
			'edit_item'          => esc_html__( 'Edit Page', 'nfc-events' ),
			'update_item'        => esc_html__( 'Update Page', 'nfc-events' ),
			'search_items'       => esc_html__( 'Search Page', 'nfc-events' ),
			'not_found'          => esc_html__( 'Not Found', 'nfc-events' ),
			'not_found_in_trash' => esc_html__( 'Not found in Trash', 'nfc-events' ),
		);

		$args = array(
			'label'               => 'nfc_pages',
			'description'         => esc_html__( 'This is where NFC pages are stored.', 'nfc-events' ),
			'labels'              => $labels,
			'public'              => true,
			'show_ui'             => true,
			'capability_type'     => 'post',
			'publicly_queryable'  => true,
			'exclude_from_search' => true,
			'show_in_menu'        => 'nfc-events-admin',
			'has_archive'         => false,
			'show_in_rest'        => true,
		);

		register_post_type( 'nfc_pages', apply_filters( 'nfc_events_register_pages_cpt_args', $args, $labels ) );
	}
}
