<?php
/**
 * Plugin Name: Woo Table Variable Products
 * Plugin URI:  https://cimba.dev/
 * Description: Edit your variable products with ease using table variable products.
 * Version:     1.0.0
 * Author:      Nemanja Cimbaljevic
 * Author URI:  https://codeable.io/developers/nemanja-cimbaljevic/?ref=jjTaE
 * Text Domain: rfd-woo-variable-table
 * Domain Path: /languages
 * License:     GPL2
 *
 * @package RFD\Woo_Variable_Table
 */

namespace RFD\Woo_Variable_Table;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'RFD_WOO_VARIABLE_TABLE_PLUGIN', 'rfd-woo-variable-table' );
define( 'RFD_WOO_VARIABLE_TABLE_VERSION', '1.0.0' );

define( 'RFD_WOO_VARIABLE_TABLE_PLUGIN_DIR', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
define( 'RFD_WOO_VARIABLE_TABLE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define( 'RFD_WOO_VARIABLE_TABLE_ASSETS_URL', RFD_WOO_VARIABLE_TABLE_PLUGIN_URL . 'assets/' );
define( 'RFD_WOO_VARIABLE_TABLE_TEMPLATES_DIR', RFD_WOO_VARIABLE_TABLE_PLUGIN_DIR . 'templates' . DIRECTORY_SEPARATOR );

define( 'RFD_WOO_VARIABLE_TABLE_BLOCKS_DIR', RFD_WOO_VARIABLE_TABLE_PLUGIN_DIR . 'blocks' . DIRECTORY_SEPARATOR );
define( 'RFD_WOO_VARIABLE_TABLE_BLOCKS_URL', RFD_WOO_VARIABLE_TABLE_PLUGIN_URL . 'blocks/' );

require_once RFD_WOO_VARIABLE_TABLE_PLUGIN_DIR . 'core/autoload.php';
require_once RFD_WOO_VARIABLE_TABLE_PLUGIN_DIR . 'functions/functions.php';

/**
 * The code that runs during plugin activation.
 */
$init_plugin = new Init();

add_action(
	'woocommerce_loaded',
	function () use ( $init_plugin ) {
		$init_plugin->prepare()->run();
	}
);
