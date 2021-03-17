<?php
/**
 * Main file for additional functions
 *
 * @link       https://cimba.blog/
 * @since      0.9.0
 *
 * @package    RFD\Woo_Variable_Tables
 */

if ( false === function_exists( 'rfd_woo_variable_tables_version' ) ) {
	/**
	 * Get plugin version
	 *
	 * @return string
	 */
	function rfd_woo_variable_tables_version(): string {
		return RFD_WOO_VARIABLE_TABLES_VERSION;
	}
}
