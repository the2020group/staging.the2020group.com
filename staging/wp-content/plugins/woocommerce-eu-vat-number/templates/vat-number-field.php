<div id="woocommerce_eu_vat_number">
	<?php
		woocommerce_form_field(
			'vat_number',
			array(
				'type'        => 'text',
				'class'       => array(
					'vat-number',
					'update_totals_on_change',
					'address-field form-row-wide'
				),
				'label'       => $label ? $label : __( "VAT Number", 'Default Field Label', 'woocommerce-eu-vat-number' ),
				'placeholder' => _x( 'VAT Number', 'Field Placeholder', 'woocommerce-eu-vat-number' ),
				'description' => wp_kses_post( $description ),
				'default'     => get_user_meta( get_current_user_id(), 'vat_number', true )
			)
		);
	?>
</div>