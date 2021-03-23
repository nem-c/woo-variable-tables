<?php
/**
 * Woo-related additional functions.
 *
 * @link       https://cimba.blog/
 * @since      0.9.0
 *
 * @package    RFD\Woo_Variable_Table
 */

if ( false === function_exists( 'woo_variable_table_load_rules' ) ) {
	/**
	 * Load rules for product terms.
	 * It takes first selected category where rules are defined if product has multiple categories.
	 *
	 * @param WP_Term[]|bool $terms Terms array.
	 *
	 * @return array
	 */
	function woo_variable_table_load_rules( $terms ): array {
		if ( true === empty( $terms ) ) {
			$terms = array();
		}
		foreach ( $terms as $term ) {
			$term_rules = get_term_meta( $term->term_id, '_woo_variable_table_settings', true );
			if ( false === empty( $term_rules ) && false === empty( $term_rules['type'] ) ) {
				return $term_rules;
			}
		}

		return array();
	}
}
