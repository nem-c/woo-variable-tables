<?php
/**
 * Plugin Name: Woo Table Variable Products
 * Plugin URI:  https://cimba.dev/
 * Description: Edit your variable products with ease using table variable products.
 * Version:     0.9.0
 * Author:      Nemanja Cimbaljevic
 * Author URI:  https://codeable.io/developers/nemanja-cimbaljevic/?ref=jjTaE
 * Text Domain: rfd-table-variable-products
 * Domain Path: /languages
 * License:     GPL2
 *
 * @package RFD\Woo_Variable_Tables
 */

namespace RFD\Woo_Variable_Tables;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'RFD_WOO_VARIABLE_TABLES_PLUGIN', 'rfd-table-variable-products' );
define( 'RFD_WOO_VARIABLE_TABLES_VERSION', '0.9.0' );

define( 'RFD_WOO_VARIABLE_TABLES_PLUGIN_DIR', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
define( 'RFD_WOO_VARIABLE_TABLES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define( 'RFD_WOO_VARIABLE_TABLES_ASSETS_URL', RFD_WOO_VARIABLE_TABLES_PLUGIN_URL . 'assets/' );
define( 'RFD_WOO_VARIABLE_TABLES_TEMPLATES_DIR', RFD_WOO_VARIABLE_TABLES_PLUGIN_DIR . 'templates' . DIRECTORY_SEPARATOR );

define( 'RFD_WOO_VARIABLE_TABLES_BLOCKS_DIR', RFD_WOO_VARIABLE_TABLES_PLUGIN_DIR . 'blocks' . DIRECTORY_SEPARATOR );
define( 'RFD_WOO_VARIABLE_TABLES_BLOCKS_URL', RFD_WOO_VARIABLE_TABLES_PLUGIN_URL . 'blocks/' );

require_once RFD_WOO_VARIABLE_TABLES_PLUGIN_DIR . 'core/autoload.php';
require_once RFD_WOO_VARIABLE_TABLES_PLUGIN_DIR . 'functions/functions.php';

/**
 * The code that runs during plugin activation.
 */
$init_plugin = new Init();
( function () use ( $init_plugin ) {
	$init_plugin->prepare()->run();
} )();
