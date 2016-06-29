<?php

add_action('woocommerce_checkout_process' , 'ia_woocommerce_checkout_process',10, 1);

function ia_woocommerce_checkout_process() {
	global $iwpro;

	if($_POST['payment_method'] != 'infusionsoft') {
		if(!$iwpro->ia_app_connect()) return;


		$returnFields 	= array('Id');	
		$shiptobilling 	= (int) ia_get_post('shiptobilling');
		
		// GET COUNTRY
		$email			= ia_get_post('billing_email');
		$contact 		= $iwpro->app->dsFind('Contact',5,0,'Email',$email,$returnFields); 
		$contact 		= $contact[0];
		
		$firstName		= ia_get_post('billing_first_name');
		$lastName		= ia_get_post('billing_last_name');
		$phone			= ia_get_post('billing_phone');
		
		$b_address1		= ia_get_post('billing_address_1');
		$b_address2		= ia_get_post('billing_address_2');
		$b_city			= ia_get_post('billing_city');
		$b_state		= ia_get_post('billing_state');
		$b_country		= iw_to_country(ia_get_post('billing_country'));
		$b_zip			= ia_get_post('billing_postcode');
		$b_company		= ia_get_post('billing_company');
		
		$s_address1		= $shiptobilling ?	$b_address1 : ia_get_post('shipping_address_1');
		$s_address2		= $shiptobilling ? 	$b_address2	: ia_get_post('shipping_address_2');
		$s_city			= $shiptobilling ? 	$b_city		: ia_get_post('shipping_city');
		$s_state		= $shiptobilling ? 	$b_state	: ia_get_post('shipping_state');
		$s_country		= $shiptobilling ? 	$b_country	: iw_to_country(ia_get_post('shipping_country'));
		$s_zip			= $shiptobilling ? 	$b_zip		: ia_get_post('shipping_postcode');
		
		// Company Selector
		$compId = 0;
		if(!empty($b_company)) {
			$company 		= $iwpro->app->dsFind('Company',5,0,'Company',$b_company,array('Id')); 
			$company 		= $company[0];
			
			if ($company['Id'] != null && $company['Id'] != 0 && $company != false){							
				$compId = $company['Id'];						
			} else {
				$companyinfo = array('Company' => $b_company);
				$compId = $iwpro->app->dsAdd("Company", $companyinfo);
			}
		}
		
		// CONTACT INFO
		$contactinfo = array(
			'FirstName' 		=> stripslashes($firstName),
			'LastName' 			=> stripslashes($lastName),
			'Phone1' 			=> stripslashes($phone),
			'StreetAddress1' 	=> stripslashes($b_address1),
			'StreetAddress2' 	=> stripslashes($b_address2),
			'City' 				=> stripslashes($b_city),
			'State' 			=> stripslashes($b_state),
			'Country' 			=> stripslashes($b_country),
			'PostalCode' 		=> stripslashes($b_zip),
			'Address2Street1' 	=> stripslashes($s_address1),
			'Address2Street2' 	=> stripslashes($s_address2),
			'City2' 			=> stripslashes($s_city),
			'State2' 			=> stripslashes($s_state),
			'Country2' 			=> stripslashes($s_country),
			'PostalCode2' 		=> $s_zip,
			'Leadsource' 		=> $_SESSION['leadsource'],
			'Company'			=> stripslashes($b_company),
			'CompanyID'			=> $compId,
			'ContactType'		=> 'Customer'
		);
			
	
		// GET CONTACT ID
		if ($contact['Id'] != null && $contact['Id'] != 0 && $contact != false){
			   $contactId = (int) $contact['Id']; 
			   if($iwpro->overwriteBD != "yes") $contactId = $iwpro->app->updateCon($contactId, $contactinfo);
		} else {
			$contactinfo['Email'] = $email;
			$contactId  = $iwpro->app->addCon($contactinfo);
			$iwpro->app->optIn($email,"API: User Purchased from Shop");
		}

		// CREATE REFERRAL: CHECK AFFILIATE													
		$is_aff = (int) $_COOKIE['is_aff'];				
		if( empty($is_aff) ) {					
			if(!empty( $_COOKIE['is_affcode'])) {						
				$returnFields 	= array('Id');						
				$affiliate 		= $iwpro->app->dsFind('Affiliate',1,0,'AffCode', $_COOKIE['is_affcode'], $returnFields);								
				$affiliate		= $affiliate[0];						
				$is_aff 		= (int) $affiliate['Id'];									
			}							
		}							

		if( !empty($is_aff) ) {
			$iwpro->app->dsAdd('Referral', array(			
				'ContactId'   => $contactId,				
				'AffiliateId' => $is_aff,				
				'IPAddress'   => $_SERVER['REMOTE_ADDR'],		
				'Type'	  	  => 0,
				'DateSet'	  => date("Y-m-d")
				)					
			);								
		}				
	}		
	
	if(isset($contactId)) $_SESSION['ia_contactId']  = $contactId;	
}

function ia_get_post($name) {
	if(isset($_POST[$name])) {
		return $_POST[$name];
	}
	return NULL;
}		

?>