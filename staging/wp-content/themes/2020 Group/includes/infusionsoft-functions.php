<?php

if(is_admin()) {
	add_action( 'profile_update', 'update_user_on_infusionsoft', 10, 2 );
}

function update_user_on_infusionsoft($user_id, $old_data='') {
	global $iwpro;
  if ( $iwpro->ia_app_connect() ) {

		$user_data = get_userdata($user_id);
    
    $email = $user_data->user_email;
    
    $contact = $iwpro->app->dsFind('Contact',5,0,'Email',$email,array('Id'));
    $contact = $contact[0];

    if ($contact['Id'] != null && $contact['Id'] != 0 && $contact != false){
      $contactId = (int) $contact['Id'];
    } 
    else {
      $contactinfo    = array();
      $contactinfo['Email'] = $email;
      $contactId  = $iwpro->app->addCon($contactinfo);
  }

    $b_company = stripslashes(get_user_meta($user_id,'billing_company',true));

    $company        = $iwpro->app->dsFind('Company',5,0,'Company',$b_company,array('Id'));
    $company        = $company[0];

    if ($company['Id'] != null && $company['Id'] != 0 && $company != false){
      $compId = $company['Id'];
    } 
    else {
      $companyinfo = array('Company' => $b_company);
      $compId = $iwpro->app->dsAdd("Company", $companyinfo);
    }

    $contactinfo = array(
      'FirstName'         => stripslashes(get_user_meta($user_id,'first_name',true)),
      'LastName'          => stripslashes(get_user_meta($user_id,'last_name',true)),
      'StreetAddress1'    => stripslashes(get_user_meta($user_id,'billing_address_1',true)),
      'StreetAddress2'    => stripslashes(get_user_meta($user_id,'billing_address_2',true)),
      'City'              => stripslashes(get_user_meta($user_id,'billing_city',true)),
      'State'             => stripslashes(get_user_meta($user_id,'billing_state',true)),
      'Country'           => stripslashes(get_user_meta($user_id,'billing_country',true)),
      'PostalCode'        => stripslashes(get_user_meta($user_id,'billing_postcode',true)),
      'Phone1'            => stripslashes(get_user_meta($user_id,'billing_phone',true)),
      'CompanyID'         => $compId,
      'Company'           => $b_company,
      'ContactType'       => 'Customer'
  );
    
    $iwpro->app->dsUpdate("Contact",$contactId,$contactinfo);
  }

}