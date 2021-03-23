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
use \WC_Product_Attribute;
use \WC_Product_Factory;
use \WP_Term;

/**
 * Class Variable_Product
 */
class Variable_Product {

	/**
	 * Rules defined in _woo_variable_table_settings
	 *
	 * @var array
	 */
	protected $rules = array();

	/**
	 * Product ID in processing.
	 *
	 * @var int
	 */
	protected $product_id = 0;

	/**
	 * Was post just published.
	 * Detected using transition_post_status hook.
	 *
	 * @var bool
	 */
	protected $product_published = false;

	/**
	 * Processing is hooking into multiple hooks on same script execution.
	 * For this it is mandatory to have unique instance for whole page.
	 *
	 * @var Variable_Product
	 */
	public static $instance;

	/**
	 * Simple static init method.
	 *
	 * @param Loader $loader Loader instance.
	 * @param int $priority Priority.
	 *
	 * @return Variable_Product
	 */
	public static function init( Loader $loader, $priority = 10 ): Variable_Product {
		if ( true === empty( self::$instance ) ) {
			self::$instance = new static();
		}

		if ( true === is_admin() ) {
			self::init_admin( $loader, $priority );
		} else {
			self::init_frontend( $loader, $priority );
		}

		return self::$instance;
	}

	/**
	 * Simple static init method for admin only.
	 *
	 * @param Loader $loader Loader instance.
	 * @param int $priority Priority.
	 */
	public static function init_admin( Loader $loader, $priority = 10 ) {
		$loader->add_action( 'transition_post_status', self::$instance, 'detect_transition', $priority, 3 );
		// priority has to be higher than already set by WooCommerce in WC_Meta_Box_Product_Data::save.
		$loader->add_action( 'woocommerce_process_product_meta', self::$instance, 'execute', 99, 2 );
	}

	/**
	 * Simple static init method for admin only.
	 * Frontend currently supports dokan only.
	 *
	 * @param Loader $loader Loader instance.
	 * @param int $priority Priority.
	 */
	public static function init_frontend( Loader $loader, $priority = 10 ) {
		$loader->add_action( 'dokan_new_product_added', self::$instance, 'execute', 99, 2 );
	}

	/**
	 * Transition to execute based on new and old status.
	 *
	 * @param string $new_status New post status string.
	 * @param string $old_status Old post status string.
	 * @param WP_Post $post Post object.
	 */
	public function detect_transition( string $new_status, string $old_status, WP_Post $post ): void {
		// skip for same status and non product posts.
		if ( 'product' !== $post->post_type || $old_status === $new_status ) {
			return;
		}
		// if new status is not publish skip.
		if ( 'publish' !== $new_status ) {
			return;
		}

		$this->product_published = true;
	}

	/**
	 * Product type change method if created product matches terms.
	 *
	 * $product_data can be WP_Post or array of data (Dokan).
	 *
	 * @param int $product_id Product ID.
	 * @param WP_Post|array $product_data WC_Product instance.
	 */
	public function execute( int $product_id, $product_data ): void { //phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
		$this->product_id = $product_id;
		$update           = (bool) get_post_meta( $product_id, '_woo_variable_table_updated', true );

		if ( true === $update ) {
			return;
		}

		$product_post = get_post( $product_id );
		if ( false === $this->is_accepted_status( $product_post->post_status ) ) {
			return;
		}

		$product           = wc_get_product( $product_post );
		$product_cat_terms = get_the_terms( $product_id, 'product_cat' );
		$term_rules        = woo_variable_table_load_rules( $product_cat_terms );

		if ( true === empty( $term_rules ) ) {
			return;
		}

		/**
		 * If product is already of same product type; skip.
		 */
		if ( true === $product->is_type( $term_rules['type'] ) ) {
			return;
		}

		$this->rules = $term_rules;
		$this->convert();
	}

	/**
	 * Run convert method from product type to new one.
	 */
	protected function convert() {
		$method = sprintf( 'convert_to_' . $this->rules['type'] );
		if ( method_exists( $this, $method ) ) {
			call_user_func( array( $this, $method ) );
		}
	}

	/**
	 * Converts given product to variable product type.
	 */
	protected function convert_to_variable() {
		wp_set_object_terms( $this->product_id, 'variable', 'product_type' );

		$attributes    = $this->make_product_attributes_for_rules();
		$product_class = WC_Product_Factory::get_product_classname( $this->product_id, 'variable' );
		$product       = new $product_class( $this->product_id );

		$product->set_attributes( $attributes );

		$product->save();

		$this->create_all_variations( $this->product_id );

		// this prevents same product to be altered 2 times even when status is changed.
		update_post_meta( $this->product_id, '_woo_variable_table_updated', true );
	}

	/**
	 * Check if given posts status is accepted.
	 *
	 * @param string $post_status Post status.
	 *
	 * @return bool
	 */
	protected function is_accepted_status( string $post_status ): bool {
		return in_array( $post_status, array( 'publish', 'draft' ), true );
	}

	/**
	 * Get product variations for product ID.
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return int[]
	 */
	protected function get_product_variation_ids( int $product_id ): array {
		$variation_ids = array();

		$variations = get_posts(
			array(
				'post_parent' => $product_id,
				'post_type'   => 'product_variation',
				'fields'      => 'ids',
				'post_status' => array( 'any', 'trash', 'auto-draft' ),
				'numberposts' => - 1, // phpcs:ignore WordPress.VIP.PostsPerPage.posts_per_page_numberposts
			)
		);

		if ( false === empty( $variations ) ) {
			$variation_ids = wp_parse_id_list( $variations );
		}

		return $variation_ids;
	}

	/**
	 * Delete all defined variations for products
	 *
	 * @param int $product_id Product ID.
	 * @param bool $force_delete Skip Trash.
	 *
	 * @return bool
	 */
	protected function delete_variations( int $product_id, bool $force_delete = true ): bool {
		if ( false === is_numeric( $product_id ) || 0 >= $product_id ) {
			return false;
		}

		$variation_ids = $this->get_product_variation_ids( $product_id );

		foreach ( $variation_ids as $variation_id ) {
			if ( $force_delete ) {
				do_action( 'woocommerce_before_delete_product_variation', $variation_id );
				wp_delete_post( $variation_id, true );
				do_action( 'woocommerce_delete_product_variation', $variation_id );
			} else {
				wp_trash_post( $variation_id );
				do_action( 'woocommerce_trash_product_variation', $variation_id );
			}
		}

		delete_transient( 'wc_product_children_' . $product_id );

		return true;
	}

	/**
	 * Make attributes list for set_attributes.
	 *
	 * @return array
	 */
	protected function make_product_attributes_for_rules(): array {
		$attributes = array();
		foreach ( $this->rules['attributes'] as $key => $attribute ) {

			$product_attribute_name    = 'pa_' . $attribute;
			$product_attribute_options = $this->get_attribute_variations( $attribute, 'ids' );

			$product_attribute = new WC_Product_Attribute();
			$product_attribute->set_id( $key + 1 );
			$product_attribute->set_name( $product_attribute_name );
			$product_attribute->set_options( $product_attribute_options );
			$product_attribute->set_position( $key );
			$product_attribute->set_visible( true );
			$product_attribute->set_variation( true );

			$attributes[ $product_attribute_name ] = $product_attribute;
		}

		return $attributes;
	}

	/**
	 * Create variations for product.
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return bool
	 */
	protected function create_all_variations( int $product_id ): bool {

		$product    = wc_get_product( $product_id );
		$data_store = $product->get_data_store();

		if ( false === is_callable( array( $data_store, 'create_all_product_variations' ) ) ) {
			return false;
		}

		$data_store->create_all_product_variations( $product );
		$data_store->sort_all_product_variations( $product->get_id() );

		return true;
	}

	/**
	 * Get list of attribute terms.
	 *
	 * @param string $attribute Attribute to search for.
	 * @param string $format Fields to return in array. Default 'raw' returns unchanged WP_Term object.
	 *
	 * @return array
	 */
	protected function get_attribute_variations( string $attribute, $format = 'raw' ): array { //phpcs:ignore Generic.Metrics.NestingLevel.MaxExceeded,Generic.Metrics.CyclomaticComplexity.TooHigh
		$attribute_terms = array();

		$terms = get_terms(
			array(
				'taxonomy'   => 'pa_' . $attribute,
				'hide_empty' => false,
			)
		);

		foreach ( $terms as $term ) {
			$attribute_terms[] = $term;
		}

		switch ( $format ) {
			case 'ids':
				$formatted_terms = array_map(
					function ( $row ) {
						return $row->term_id;
					},
					$attribute_terms
				);
				break;
			case 'raw':
			default:
				$formatted_terms = $attribute_terms;
		}

		return $formatted_terms;
	}

}
