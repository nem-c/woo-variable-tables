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
 * @package    RFD\Woo_Variable_Table
 * @subpackage RFD\Woo_Variable_Table\Includes
 */

namespace RFD\Woo_Variable_Table;

use RFD\Core\I18n;
use RFD\Core\Abstracts\Init as Abstract_Init;
use RFD\Woo_Variable_Table\Dokan\Variations_Table;
use RFD\Woo_Variable_Table\Woo\Variable_Product;

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
		'\RFD\Woo_Variable_Table\Meta_Boxes\Product_Cat_Term_Meta_Box',
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
			$this->version = RFD_WOO_VARIABLE_TABLE_VERSION;
		} else {
			$this->version = '0.9.0';
		}
		$this->plugin_name = RFD_WOO_VARIABLE_TABLE_PLUGIN;
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
				'domain'          => 'rfd-woo-variable-table',
				'plugin_rel_path' => RFD_WOO_VARIABLE_TABLE_PLUGIN_DIR . 'languages' . DIRECTORY_SEPARATOR,
			)
		);
	}

	/**
	 * Prepare hooks for both admin and frontend.
	 */
	protected function prepare_general(): void {
		Variable_Product::init( $this->loader );
	}

	/**
	 * Prepare hooks for frontend only
	 */
	protected function prepare_frontend(): void {
		Variations_Table::init( $this->loader, 25 );
	}


}
