<?php
/**
 * Math-related additional functions.
 *
 * @link       https://cimba.blog/
 * @since      0.9.0
 *
 * @package    RFD\Woo_Variable_Table
 */

if ( false === function_exists( 'wc_cartesian_product_of_attributes_terms' ) ) {
	/**
	 * Create cartesian product of all given attributes.
	 *
	 * @param array $attributes List of attributes with tags.
	 *
	 * @return array
	 */
	function wc_cartesian_product_of_attributes_terms( array $attributes ): array { //phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh, Generic.Metrics.NestingLevel.MaxExceeded
		$result = array( array() );

		foreach ( $attributes as $attribute_name => $attribute_values ) {
			// If a sub-array is empty, it doesn't affect the cartesian product.
			if ( true === empty( $attribute_values ) ) {
				continue;
			}

			$append = array();

			foreach ( $result as $attribute ) {
				foreach ( $attribute_values as $attribute_value ) {
					$attribute[ $attribute_name ] = $attribute_value;

					$append[] = $attribute;
				}
			}

			$result = $append;
		}

		return $result;
	}
}
