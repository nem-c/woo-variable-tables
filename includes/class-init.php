<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://cimba.blog/
 * @since      0.9.0
 *
 * @package    RFD\Woo_Variable_Tables
 * @subpackage RFD\Woo_Variable_Tables\Includes
 */

namespace RFD\Woo_Variable_Tables;

use RFD\Core\I18n;
use RFD\Core\Abstracts\Init as Abstract_Init;
use RFD\Woo_Variable_Tables\Meta_Boxes\Product_Cat_Term_Meta_Box;

/**
 * Class Init
 */
class Init extends Abstract_Init {

	/**
	 * Meta boxes to be registered.
	 *
	 * @var array
	 */
	protected $meta_boxes = array(
		'\RFD\Woo_Variable_Tables\Meta_Boxes\Product_Cat_Term_Meta_Box',
	);

	/**
	 * Define core variables.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 *
	 * @since    0.9.0
	 */
	public function __construct() {
		if ( defined( 'RFD_TABLE_VARIABLE_PRODUCTS_VERSION' ) ) {
			$this->version = RFD_WOO_VARIABLE_TABLES_VERSION;
		} else {
			$this->version = '0.9.0';
		}
		$this->plugin_name = RFD_WOO_VARIABLE_TABLES_PLUGIN;
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Dom_Woo_Customize_Login_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since 0.9.0
	 * @access protected
	 */
	protected function set_locale(): void {
		I18n::init(
			$this->loader,
			array(
				'domain'          => 'rfd-woo-variable-tables',
				'plugin_rel_path' => RFD_WOO_VARIABLE_TABLES_PLUGIN_DIR . 'languages' . DIRECTORY_SEPARATOR,
			)
		);
	}
}
