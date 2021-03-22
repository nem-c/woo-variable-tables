<?php
/**
 * Product Category Variable Table Meta Box when in Create context
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package    RFD\Woo_Variable_Table
 *
 * @var string $nonce_field
 * @var array $product_types
 * @var array $product_attributes
 * @var array $product_fields
 */

use RFD\Core\Input;

// @codingStandardsIgnoreStart
?>
<?php echo $nonce_field; ?>
<div class="form-field">
    <label for="woo_variable_tables_default_product_type">
		<?php _e( 'Default product type:', 'rfd-woo-variable-table' ); ?>
    </label>
	<?php echo Input::render(
		array(
			'id'          => 'woo_variable_tables_default_product_type',
			'field_name'  => 'default_product_type',
			'field_value' => '',
			'type'        => 'select',
			'title'       => __( 'Default product type:', 'rfd-woo-variable-table' ),
			'description' => '',
			'options'     => $product_types,
		)
	); ?>
</div>
<div id="woo_variable_tables_variable_product_options">
    <div class="form-field">
        <label for="woo_variable_tables_auto_generated_attributes"></label>
		<?php echo Input::render(
			array(
				'id'          => 'woo_variable_tables_default_product_type',
				'field_name'  => 'default_product_attributes',
				'field_value' => '',
				'type'        => 'checkbox_group',
				'title'       => __( 'Default product attributes:', 'rfd-woo-variable-table' ),
				'description' => __( 'Variations will be automatically generated for selected attributes on product creation', 'rfd-woo-variable-table' ),
				'options'     => $product_attributes,
				'multiple'    => true,
			)
		); ?>
    </div>
    <div class="form-field">
        <label for="woo_variable_tables_product_fields"></label>
		<?php echo Input::render(
			array(
				'id'          => 'woo_variable_tables_product_fields',
				'field_name'  => 'product_fields',
				'field_value' => '',
				'type'        => 'checkbox_group',
				'title'       => __( 'Shown product fields:', 'rfd-woo-variable-table' ),
				'description' => __( 'Fields displayed in table as column', 'rfd-woo-variable-table' ),
				'options'     => $product_fields,
				'multiple'    => true,
			)
		); ?>
    </div>
</div>