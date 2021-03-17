<?php
/**
 * WooCommerce Product Category Term Meta Box
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package RFD\Woo_Variable_Tables
 * @subpackage RFD\Woo_Variable_Tables\Meta_Boxes
 */

namespace RFD\Woo_Variable_Tables\Meta_Boxes;

use RFD\Core\Abstracts\Admin\Meta_Boxes\Term_Meta_Box;

/**
 * Class Product_Cat_Term_Meta_Box
 *
 * @package RFD\Woo_Variable_Tables\Meta_Boxes
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
	protected $nonce_name = '';

	/**
	 * Nonce save action name
	 *
	 * @var string
	 */
	protected $nonce_action = '';

	/**
	 * Render user meta box.
	 *
	 * @param mixed $term Term object.
	 */
	public function render( $term ): void {
		echo '123';
	}

	/**
	 * Save user meta data.
	 *
	 * @param int $term_id Term ID.
	 *
	 * @return bool
	 */
	public function save( int $term_id ): bool {

		return true;
	}
}
