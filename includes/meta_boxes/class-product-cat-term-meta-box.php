<?php
/**
 * WooCommerce Product Category Term Meta Box
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package RFD\Woo_Variable_Table
 * @subpackage RFD\Woo_Variable_Table\Meta_Boxes
 */

namespace RFD\Woo_Variable_Table\Meta_Boxes;

use RFD\Core\Abstracts\Admin\Meta_Boxes\Term_Meta_Box;
use RFD\Core\View;

/**
 * Class Product_Cat_Term_Meta_Box
 *
 * @package RFD\Woo_Variable_Table\Meta_Boxes
 */
class Product_Cat_Term_Meta_Box extends Term_Meta_Box {

	/**
	 * Term meta box ID
	 *
	 * @var string
	 */
	protected $id = 'product-cat-term-meta-box';

	/**
	 * Term taxonomy
	 *
	 * @var string
	 */
	protected $taxonomy = 'product_cat';

	/**
	 * Nonce name to be used when running actions.
	 *
	 * @var string
	 */
	protected $nonce_name = '_product_cat_term_save_nonce';

	/**
	 * Render user meta box.
	 *
	 * @param mixed $term Term object.
	 */
	public function render( $term ): void {
		global $pagenow;

		$product_types      = wc_get_product_types();
		$product_attributes = array();
		$product_fields     = array(
			'regular_price'  => __( 'Regular Price', 'woocommerce' ),
			'sale_price'     => __( 'Sale Price', 'woocommerce' ),
			'sku'            => __( 'SKU', 'woocommerce' ),
			'manage_stock'   => __( 'Manage Stock', 'woocommerce' ),
			'stock_quantity' => __( 'Stock Quantity', 'woocommerce' ),
			'stock_status'   => __( 'Stock Status', 'woocommerce' ),
		);
		foreach ( wc_get_attribute_taxonomies() as $attribute ) {
			$product_attributes[ $attribute->attribute_name ] = $attribute->attribute_label;
		}

		$selected_product_type       = '';
		$selected_product_attributes = array();
		$selected_product_fields     = array();

		$template = 'admin/meta-boxes/product-cat-term-create-meta-box.php';

		if ( 'term.php' === $pagenow ) {
			$selected                    = get_term_meta( $term->term_id, '_woo_variable_table_settings', true );
			$selected_product_type       = $selected['type'] ?? '';
			$selected_product_attributes = $selected['attributes'] ?? array();
			$selected_product_fields     = $selected['fields'] ?? array();

			$template = 'admin/meta-boxes/product-cat-term-update-meta-box.php';
		}

		$nonce_field = $this->nonce_field();

		View::render_template(
			$template,
			compact(
				'nonce_field',
				'product_types',
				'product_attributes',
				'product_fields',
				'selected_product_type',
				'selected_product_attributes',
				'selected_product_fields'
			)
		);
	}

	/**
	 * Save user meta data.
	 *
	 * @param int $term_id Term ID.
	 * @param int $tt_id Term-taxonomy ID.
	 * @param string $taxonomy Taxonomy.
	 * @param bool $update Update (true) or create (false).
	 *
	 * @return bool
	 */
	public function save( int $term_id, int $tt_id, string $taxonomy, bool $update ): bool {

		// if taxonomy is not product-cat - skip.
		if ( 'product_cat' !== $taxonomy ) {
			return false;
		}

		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		$product_type = sanitize_text_field( wp_unslash( $_POST['default_product_type'] ?? '' ) );
		//phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$product_attributes = $_POST['default_product_attributes'] ?? array();
		array_walk( $product_attributes, 'sanitize_text_field' );
		//phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$product_fields = $_POST['product_fields'] ?? array();
		array_walk( $product_fields, 'sanitize_text_field' );

		update_term_meta(
			$term_id,
			'_woo_variable_table_settings',
			array(
				'type'       => $product_type,
				'attributes' => $product_attributes,
				'fields'     => $product_fields,
			)
		);

		return true;
	}
}
