<?php
/**
 * Custom fields for products.
 *
 * @package Nfc\Events;
 * @version 1.0.0
 */

namespace Nfc\Events\Products;

use Nfc\Events\Singleton;

/**
 * Class Products_Fields.
 */
class Products_Fields {
	use Singleton;

	//phpcs:disable Generic.Arrays.DisallowShortArraySyntax

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_product_options_inventory_product_data', [ $this, 'product_setting_fields' ] );
		add_action( 'woocommerce_process_product_meta', [ $this, 'save_product_custom_fields' ] );
		add_action( 'woocommerce_product_after_variable_attributes', [ $this, 'variation_setting_fields' ], 10, 3 );
		add_action( 'woocommerce_save_product_variation', [ $this, 'save_variation_setting_fields' ], 10, 2 );
	}

	/**
	 * Displays product custom fields except for variable products.
	 *
	 * @return void
	 */
	public function product_setting_fields() {
		$product = wc_get_product( get_the_ID() );

		if ( $product && $product->is_type( 'variable' ) ) {
			return;
		}
		?>

		<h4 style="color:#000;padding:0 14px;margin:20px 0 5px;">
			<?php esc_html_e( 'NFC Events Settings', 'nfc-events' ); ?>
		</h4>

		<?php
		woocommerce_wp_text_input(
			[
				'id'          => '_nfc_events_product_total_stock',
				'label'       => esc_html__( 'Total amount of products', 'nfc-events' ),
				'description' => esc_attr__( 'Set total amount of your products (all product on stock and those that are currently rented).', 'nfc-events' ),
				'desc_tip'    => true,
				'data_type'   => 'number',
				'value'       => get_post_meta( get_the_ID(), '_nfc_events_product_total_stock', true ),
			]
		);

		/**
		 * Nonce.
		 */
		wp_nonce_field( 'nfc_events_product_fields_nonce', 'nfc_events_product_fields_nonce' );
	}

	/**
	 * Save product custom fields.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return void
	 */
	public function save_product_custom_fields( $post_id ) {
		$product = wc_get_product( get_the_ID() );

		if ( $product && $product->is_type( 'variable' ) ) {
			return;
		}

		if ( ! isset( $_POST['nfc_events_product_fields_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nfc_events_product_fields_nonce'] ), 'nfc_events_product_fields_nonce' ) ) {
			return;
		}

		update_post_meta( $post_id, '_nfc_events_product_total_stock', isset( $_POST['_nfc_events_product_total_stock'] ) ? sanitize_key( $_POST['_nfc_events_product_total_stock'] ) : '' );
	}

	/**
	 * Displays variations custom fields.
	 *
	 * @param int     $loop           Position in the loop.
	 * @param array   $variation_data Variation data.
	 * @param WP_Post $variation      Post data.
	 *
	 * @return void
	 */
	public function variation_setting_fields( $loop, $variation_data, $variation ) {
		$value = get_post_meta( $variation->ID, '_nfc_events_product_total_stock', true );

		?>
		<h3 style="padding:15px 0 0 0!important">
			<?php esc_html_e( 'NFC Events Settings', 'nfc-events' ); ?>
		</h3>

		<p class="form-row form-row-first">
			<label class="tips" data-tip="<?php esc_attr_e( 'Set total amount of your products (all product on stock and those that are currently rented).', 'nfc-events' ); ?>">
				<?php esc_html_e( 'Total amount of products', 'nfc-events' ); ?>
			</label>
			<span class="woocommerce-help-tip"></span>
			<input type="number" class="_nfc_events_product_total_stock" name="_nfc_events_product_total_stock[<?php echo esc_attr( $loop ); ?>]" value="<?php echo esc_attr( $value ); ?>"/>
		</p>
		<?php

		/**
		 * Nonce.
		 */
		wp_nonce_field( 'nfc_events_variation_fields_nonce', 'nfc_events_variation_fields_nonce' );
	}

	/**
	 * Saves variations custom fields values.
	 *
	 * @param int $variation_id variation id.
	 * @param int $loop Loop.
	 *
	 * @return void
	 */
	public function save_variation_setting_fields( $variation_id, $loop ) {
		if ( ! isset( $_POST['nfc_events_variation_fields_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nfc_events_variation_fields_nonce'] ), 'nfc_events_variation_fields_nonce' ) ) {
			return;
		}

		update_post_meta(
			$variation_id,
			'_nfc_events_product_total_stock',
			isset( $_POST['_nfc_events_product_total_stock'][ $loop ] ) ? sanitize_key( $_POST['_nfc_events_product_total_stock'][ $loop ] ) : ''
		);
	}
}
