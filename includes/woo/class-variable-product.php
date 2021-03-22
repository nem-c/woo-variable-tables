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

namespace RFD\Woo_Variable_Table\Woo;

use RFD\Core\Loader;
use \WP_Post;
use \WC_Product;
use \WP_Term;

/**
 * Class Init
 */
class Variable_Product {

	/**
	 * Rules defined in _woo_variable_table_settings
	 *
	 * @var array
	 */
	protected $rules = array();

	/**
	 * Simple static init method.
	 *
	 * @param Loader $loader Loader instance.
	 * @param int $priority Priority.
	 *
	 * @return Variable_Product
	 */
	public static function init( Loader $loader, $priority = 10 ): Variable_Product {
		$instance = new static();

		$loader->add_action( 'wp_after_insert_post', $instance, 'execute', $priority, 4 );

		return $instance;
	}

	/**
	 * Product type change method if created product matches terms.
	 *
	 * @param int $product_id Product ID.
	 * @param WP_Post $product_post WC_Product instance.
	 * @param bool $update Whether this is an existing post being updated.
	 * @param null|WP_Post $post_before Null for new posts, the WP_Post object prior to the update for updated posts.
	 */
	public function execute( int $product_id, WP_Post $product_post, bool $update, ?WP_Post $post_before ): void {
		if ( true === $update ) {
			return;
		}
		if ( false === $this->is_accepted_status( $product_post->post_status ) ) {
			return;
		}
		$product    = wc_get_product( $product_post );
		$term_rules = $this->load_rules( get_the_terms( $product_id, 'product_cat' ) );

		/**
		 * If product is already of same product type; skip.
		 */
		if ( true === $product->is_type( $term_rules['type'] ) ) {
			return;
		}
		$this->convert( $product_id );
	}

	/**
	 * Load rules for product terms.
	 * It takes first selected category where rules are defined if product has multiple categories.
	 *
	 * @param WP_Term[] $terms Terms array.
	 *
	 * @return array
	 */
	protected function load_rules( array $terms ): array {
		foreach ( $terms as $term ) {
			$term_rules = get_term_meta( $term->term_id, '_woo_variable_table_settings', true );
			if ( false === empty( $term_rules ) && false === empty( $term_rules['type'] ) ) {
				$this->rules = $term_rules;

				return $term_rules;
			}
		}

		return array();
	}

	protected function convert( int $product_id ) {
		$method = sprintf( 'convert_to_' . $this->rules['type'] );
		if ( method_exists( $this, $method ) ) {
			call_user_func( array( $this, $method, $product_id ) );
		}
	}

	protected function convert_to_variable() {

	}

	protected function is_accepted_status( string $post_status ): bool {
		return in_array( $post_status, array( 'publish', 'draft' ), true );
	}

}
