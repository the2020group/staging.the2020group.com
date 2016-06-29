<?php

// Override the InfusedWoo PRO infusedwoo_customer_edit_address() function
remove_action('woocommerce_customer_save_address', 'infusedwoo_customer_edit_address',10, 2);
add_action('woocommerce_customer_save_address', 'f10_infusedwoo_customer_edit_address',10, 2);

function f10_infusedwoo_customer_edit_address($user_id, $load_address) {
	global $iwpro;

	if($iwpro->autosave_address == "yes") {
		if(!$iwpro->ia_app_connect()) return false;

		$upd = array(
				'FirstName' => get_user_meta( $user_id, 'billing_first_name', true ),
				'LastName' => get_user_meta( $user_id, 'billing_last_name', true ),
				'StreetAddress1' => get_user_meta( $user_id, 'billing_address_1', true ),
				'StreetAddress2' => get_user_meta( $user_id, 'billing_address_2', true ),
				'City' => get_user_meta( $user_id, 'billing_city', true ),
				'State' => get_user_meta( $user_id, 'billing_state', true ),
				'PostalCode' => get_user_meta( $user_id, 'billing_postcode', true ),
				'Address2Street1' => get_user_meta( $user_id, 'shipping_address_1', true ),
				'Address2Street2' => get_user_meta( $user_id, 'shipping_address_2', true ),
				'City2' => get_user_meta( $user_id, 'shipping_city', true ),
				'State2' => get_user_meta( $user_id, 'shipping_state', true ),
				'PostalCode2' => get_user_meta( $user_id, 'shipping_postcode', true ),
				'Email' => get_user_meta( $user_id, 'billing_email', true )
			);

		// FIRST 10 EDIT: Try to update a user by their ID, rather than just adding/updating
    // via e-mail address.
		$contact_id = get_user_meta( $user_id, 'is_contact_id', true );
    if($contact_id) {
      $iwpro->app->updateCon($contact_id, $upd);
    } else {
      $iwpro->app->addWithDupCheck($upd, 'Email');
    }
	}
}
