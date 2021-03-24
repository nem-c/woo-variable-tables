<?php
/**
 * Modifies newly created product to different type if needed.
 * Based on values select for product category for created product.
 *
 * @link       https://cimba.blog/
 * @since      0.9.0
 *
 * @package    RFD\Woo_Variable_Table
 * @subpackage RFD\Woo_Variable_Table\Includes
 */

namespace RFD\Woo_Variable_Table\Dokan;

use RFD\Core\Loader;
use RFD\Core\Logger;
use RFD\Core\View;
use RFD\Woo_Variable_Table\Woo\Variable_Product;
use \WP_Post;
use \WC_Product;
use \WC_Data_Exception;

/**
 * Class Variable_Product
 */
class Variations_Table {
	/**
	 * Processing is hooking into multiple hooks on same script execution.
	 * For this it is mandatory to have unique instance for whole page.
	 *
	 * @var Variations_Table
	 */
	public static $instance;

	/**
	 * Simple static init method.
	 *
	 * @param Loader $loader Loader instance.
	 * @param int $priority Priority.
	 *
	 * @return Variations_Table
	 */
	public static function init( Loader $loader, $priority = 10 ): Variations_Table {
		if ( true === empty( self::$instance ) ) {
			self::$instance = new static();
		}

		$loader->add_action( 'dokan_product_edit_after_main', self::$instance, 'render', 25, 2 );
		$loader->add_action( 'dokan_product_updated', self::$instance, 'maybe_save', $priority, 2 );

		return self::$instance;
	}

	/**
	 * Render table.
	 *
	 * @param WP_Post $product_post WP_Post object.
	 * @param int $product_id Product ID.
	 */
	public function render( WP_Post $product_post, int $product_id ): void {
		$product            = wc_get_product( $product_id );
		$product_variations = $this->get_product_variations( $product_id );
		$product_cat_terms  = get_the_terms( $product_id, 'product_cat' );
		$term_rules         = woo_variable_table_load_rules( $product_cat_terms );
		$stock_statuses     = wc_get_product_stock_status_options();

		$nonce_field = $this->nonce_field();

		View::render_template(
			'dokan/variations-table.php',
			compact(
				'nonce_field',
				'product',
				'product_id',
				'product_variations',
				'term_rules',
				'stock_statuses'
			)
		);
	}

	/**
	 * Check should save method be executed or not.
	 *
	 * @param int $product_id Product ID.
	 * @param array $postdata Post data array.
	 */
	public function maybe_save( int $product_id, array $postdata ): void { //phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
		if ( false === is_user_logged_in() ) {
			return;
		}

		if ( false === dokan_is_user_seller( get_current_user_id() ) ) {
			return;
		}

		if ( false === dokan_is_product_author( $product_id ) ) {
			// product doesn't belong to seller.
			return;
		}

		if ( false === wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['woo_variable_table'] ?? '' ) ), 'save_table' ) ) {
			return;
		}

		$this->save( $product_id, $postdata );
	}

	/**
	 * Save table data.
	 *
	 * @param int $product_id Product ID.
	 * @param array $postdata Post Data array.
	 */
	protected function save( int $product_id, array $postdata ) { //phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded,Generic.Metrics.NestingLevel.MaxExceeded
		$table_data = $postdata['woo_variable_table_data'] ?? array();

		if ( 'simple' === $postdata['product_type'] ) {
			// delete update key in this case.
			delete_post_meta( $product_id, '_woo_variable_table_updated', true );
			// restore variations.
			$this->restore_variations( $product_id, $postdata );
		}

		foreach ( $table_data as $product_variation_id => $data ) {
			$product_variation = wc_get_product( $product_variation_id );
			if ( $product_id !== $product_variation->get_parent_id() ) {
				// if product doesn't belong to parent product - skip.
				continue;
			}

			// set sku if set.
			if ( true === isset( $data['sku'] ) ) {
				try {
					$product_variation->set_sku( $data['sku'] );
				} catch ( WC_Data_Exception $exception ) {
					Logger::log( $exception->getMessage() );
				}
			}
			// set or unset manage stock based on existence of data.
			if ( true === isset( $data['manage_stock'] ) && 1 === intval( $data['manage_stock'] ) ) {
				$product_variation->set_manage_stock( true );
			} else {
				$product_variation->set_manage_stock( false );
			}
			// set stock quantity if set.
			if ( true === isset( $data['stock_quantity'] ) ) {
				$product_variation->set_stock_quantity( intval( $data['stock_quantity'] ) );
			}
			// set stock status if set.
			if ( true === isset( $data['stock_status'] ) ) {
				$product_variation->set_stock_status( intval( $data['stock_status'] ) );
			}

			$product_variation->save();

			$this->save_prices(
				$product_variation,
				$data['regular_price'] ?? false,
				$data['sale_price'] ?? false
			);
		}
	}

	/**
	 * Save product prices.
	 *
	 * @param WC_Product $product WC_Product object.
	 * @param mixed $regular_price Regular price value.
	 * @param mixed $sale_price Sale price value.
	 */
	protected function save_prices( WC_Product $product, $regular_price, $sale_price ): void {  //phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
		$no_regular_price = false;
		$no_sale_price    = false;

		if ( false === is_numeric( $regular_price ) ) {
			$product->set_regular_price( false );
			$product->set_sale_price( false );
			$product->set_price( false );

			$no_regular_price = true;
		}
		if ( false === is_numeric( $sale_price ) ) {
			$product->set_sale_price( false );

			$no_sale_price = true;
		}

		if ( false === $no_regular_price ) {
			$product->set_regular_price( floatval( $regular_price ) );
			$product->set_price( floatval( $regular_price ) );
		}

		if ( false === $no_sale_price ) {
			$product->set_sale_price( floatval( $sale_price ) );
			$product->set_price( floatval( $sale_price ) );
		}

		$product->save();
	}

	/**
	 * Restore variable product details if needed.
	 *
	 * @param int $product_id Product ID.
	 * @param array $postdata Post data array.
	 */
	protected function restore_variations( int $product_id, array $postdata ) {

		$convertor = new Variable_Product();
		$convertor->execute( $product_id, $postdata );
	}

	/**
	 * Get all product variations.
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return WC_Product[]
	 */
	protected function get_product_variations( int $product_id ): array {
		return wc_get_products(
			array(
				'status'  => array( 'publish' ),
				'type'    => 'variation',
				'parent'  => $product_id,
				'limit'   => - 1,
				'orderby' => array(
					'menu_order' => 'ASC',
					'ID'         => 'DESC',
				),
				'return'  => 'objects',
			)
		);
	}

	/**
	 * Renders nonce field
	 *
	 * @return string
	 */
	public function nonce_field(): string {
		return wp_nonce_field( 'save_table', 'woo_variable_table', true, false );
	}
}
