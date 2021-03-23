<?php
/**
 * Variations table dokan template.
 *
 * phpcs:ignoreFile
 *
 * @var WC_Product[] $product_variations
 * @var array $term_rules
 * @var string $nonce_field
 * @var array $stock_statuses
 *
 */
?>
<div class="dokan-variations-table dokan-edit-row dokan-clearfix">
    <div class="dokan-section-heading" data-togglehandler="dokan_other_options">
        <h2>
            <i class="fa fa-table" aria-hidden="true"></i>
			<?php esc_html_e( 'Variations', 'rfd-woo-variable-table' ); ?>
        </h2>
        <p><?php esc_html_e( 'Manage product variations', 'rfd-woo-variable-table' ); ?></p>
        <a href="#" class="dokan-section-toggle">
            <i class="fa fa-sort-desc fa-flip-vertical" aria-hidden="true"></i>
        </a>
        <div class="dokan-clearfix"></div>
    </div>

    <div class="dokan-section-content" style="overflow-x:auto;">
        <table class="table" style="width: max-content">
            <thead>
            <tr>
                <td style="width: 20rem; text-align: left">
					<?php esc_html_e( 'Product Variation', 'rfd-woo-variable-table' ); ?>
                </td>
				<?php foreach ( $term_rules['fields'] as $field ): ?>

					<?php switch ( $field ):
						case 'regular_price': ?>
                            <td style="width: 10rem; text-align: center">
								<?php esc_html_e( 'Regular Price', 'rfd-woo-variable-table' ); ?>
                            </td>
							<?php break; ?>
						<?php case 'sale_price': ?>
                            <td style="width: 10rem; text-align: center">
								<?php esc_html_e( 'Sale Price', 'rfd-woo-variable-table' ); ?>
                            </td>
							<?php break; ?>
						<?php case 'sku': ?>
                            <td style="width: 10rem; text-align: center">
								<?php esc_html_e( 'SKU', 'rfd-woo-variable-table' ); ?>
                            </td>
							<?php break; ?>
						<?php case 'manage_stock': ?>
                            <td style="width: 8rem; text-align: center">
								<?php esc_html_e( 'Manage Stock', 'rfd-woo-variable-table' ); ?>
                            </td>
							<?php break; ?>
						<?php case 'stock_quantity': ?>
                            <td style="width: 10rem; text-align: center">
								<?php esc_html_e( 'Stock Quantity', 'rfd-woo-variable-table' ); ?>
                            </td>
							<?php break; ?>
						<?php case 'stock_status': ?>
                            <td style="width: 10rem; text-align: center">
								<?php esc_html_e( 'Stock Status', 'rfd-woo-variable-table' ); ?>
                            </td>
							<?php break; ?>
						<?php endswitch; ?>
				<?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
			<?php foreach ( $product_variations as $product_variation ): ?>
                <tr>
                    <th><?php echo $product_variation->get_name() ?></th>
					<?php foreach ( $term_rules['fields'] as $field ): ?>
                        <td style="text-align: center">
							<?php switch ( $field ):
								case 'regular_price': ?>
                                    <input type="number" class="dokan-form-control"
                                           name="woo_variable_table_data[<?php echo $product_variation->get_id() ?>][regular_price]"
                                           value="<?php echo $product_variation->get_regular_price() ?>"
                                           step="0.01"/>
									<?php break; ?>

								<?php case 'sale_price': ?>
                                    <input type="number" class="dokan-form-control"
                                           name="woo_variable_table_data[<?php echo $product_variation->get_id() ?>][sale_price]"
                                           value="<?php echo $product_variation->get_sale_price() ?>"
                                           step="0.01"/>
									<?php break; ?>

								<?php case 'sku': ?>
                                    <input type="text" class="dokan-form-control"
                                           name="woo_variable_table_data[<?php echo $product_variation->get_id() ?>][sku]"
                                           value="<?php echo $product_variation->get_sku() ?>"/>
									<?php break; ?>
								<?php case 'manage_stock': ?>

                                    <input type="checkbox" class="dokan-form-control"
                                           name="woo_variable_table_data[<?php echo $product_variation->get_id() ?>][manage_stock]"
										<?php echo ( true === $product_variation->get_manage_stock() ) ? 'checked="checked"' : ''; ?>
                                           value="1"/>
									<?php break; ?>

								<?php case 'stock_quantity': ?>
                                    <input type="number" class="dokan-form-control"
                                           name="woo_variable_table_data[<?php echo $product_variation->get_id() ?>][stock_quantity]"
                                           value="<?php echo $product_variation->get_stock_quantity() ?>"
										<?php echo ( false === $product_variation->get_manage_stock() ) ? 'disabled="disabled"' : ''; ?>
                                           step="1"/>
									<?php break; ?>

								<?php case 'stock_status': ?>
                                    <select name="woo_variable_table_data[<?php echo $product_variation->get_id() ?>][stock_status]"
										<?php echo ( false === $product_variation->get_manage_stock() ) ? 'disabled="disabled"' : ''; ?>
                                            class="dokan-form-control">
										<?php foreach ( $stock_statuses as $stock_status_name => $stock_status_label ): ?>
                                            <option value="<?php echo $stock_status_name; ?>"
												<?php echo ( $stock_status_name === $product_variation->get_stock_status() ) ? 'selected="selected"' : ''; ?>>
												<?php echo $stock_status_label; ?>
                                            </option>
										<?php endforeach; ?>
                                    </select>
									<?php break; ?>

								<?php endswitch; ?>
                        </td>
					<?php endforeach ?>
                </tr>
			<?php endforeach; ?>
            </tbody>
        </table>
    </div>
	<?php echo $nonce_field; ?>
</div>