<?php

// ORDER COMPLETE HOOKS

add_action('woocommerce_order_status_on-hold_to_completed', 'ia_woocommerce_payment_complete', 10, 1);
add_action('woocommerce_order_status_pending_to_completed', 'ia_woocommerce_payment_complete', 10, 1);
add_action('woocommerce_order_status_failed_to_completed', 'ia_woocommerce_payment_complete', 10, 1);					
add_action('woocommerce_order_status_on-hold_to_processing', 'ia_woocommerce_payment_complete', 10, 1);
add_action('woocommerce_order_status_pending_to_processing', 'ia_woocommerce_payment_complete', 10, 1);
add_action('woocommerce_order_status_failed_to_processing', 'ia_woocommerce_payment_complete', 10, 1);

function ia_woocommerce_payment_complete( $order_id ) {
	global $woocommerce;
	global $iwpro;

	$order = new WC_Order( $order_id );	
	if(!$iwpro->ia_app_connect()) {
		$apperrormsg = $iwpro->settings['apperrormsg'];
		$order->add_order_note("CRITICAL: Not sent to infusionsoft due to {$apperrormsg}");	
		return;
	}		

	$email = $order->billing_email;
	$contact = $iwpro->app->dsFind('Contact',5,0,'Email',$email,array('Id'));
		$contact = $contact[0];	
	
	if ($contact['Id'] != null && $contact['Id'] != 0 && $contact != false){
		$contactId = (int) $contact['Id']; 
	} else {				
		$contactinfo	= array();	
		$contactinfo['Email'] = $email;
		$contactId  = $iwpro->app->addCon($contactinfo);
	}			
	
	$products = $order->get_items(); 
	
	$as = (int) $iwpro->success_as;
	$iwpro->app->runAS($contactId, $as);
	
	$saveOrders = $iwpro->settings['saveOrders'];
	
	$payment_method = $order->payment_method;
	
	if($saveOrders == "yes" && $payment_method != "infusionsoft") {

		// MAKE SURE BILLING AND SHIPPING ADDRESS IS CORRECT
		// Company Selector
		$compId = 0;
		$b_company = stripslashes($order->billing_company);
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

		$contactinfo = array(
			'FirstName'			=> stripslashes($order->billing_first_name),
			'LastName'			=> stripslashes($order->billing_last_name),
			'StreetAddress1' 	=> stripslashes($order->billing_address_1),
			'StreetAddress2' 	=> stripslashes($order->billing_address_2),
			'City' 				=> stripslashes($order->billing_city),
			'State' 			=> stripslashes($order->billing_state),
			'Country' 			=> stripslashes(iw_to_country($order->billing_country)),
			'PostalCode' 		=> stripslashes($order->billing_postcode),
			'Address2Street1' 	=> stripslashes($order->shipping_address_1),
			'Address2Street2' 	=> stripslashes($order->shipping_address_2),
			'City2' 			=> stripslashes($order->shipping_city),
			'State2' 			=> stripslashes($order->shipping_state),
			'Country2' 			=> stripslashes(iw_to_country($order->shipping_country)),
			'PostalCode2' 		=> $order->shipping_postcode,
			'CompanyID'			=> $compId,
			'Phone1'			=> $order->billing_phone,
			'Company'			=> $b_company,
			'ContactType'		=> 'Customer'
		);

		if($iwpro->overwriteBD != "yes") $iwpro->app->dsUpdate("Contact",$contactId,$contactinfo);
		
		if($payment_method != "infusionsoft") {
			// CHECK AFFILIATE			
					
			$returnFields = array('AffiliateId');
			$referrals = $iwpro->app->dsFind('Referral',1000,0,'ContactId',(int) $contactId,$returnFields);
			$num = count($referrals);
			if($num > 0  && is_array($referrals)) $is_aff = $referrals[$num-1]['AffiliateId'];
			else $is_aff = 0;	

			// BREAK IF INVOICE ALREADY CREATED
			$ifs_inv = get_post_meta($order_id, 'infusionsoft_invoice_id', true );
			if($ifs_inv > 0) {
				return true;
			}
			
			// CREATE INVOICE
			
			$orderDate = date('Ymd\TH:i:s', current_time('timestamp'));
			$inv_id = (int) $iwpro->app->blankOrder($contactId,"Woocommerce Order # {$order_id}",$orderDate,0,$is_aff);
			update_post_meta($order_id, 'infusionsoft_invoice_id', $inv_id);
			$calc_totals = 0;
			
			$products = $order->get_items(); 
			// PRODUCT LINE


			foreach($products as $product) {
				
				
					

				$sku = "";
				$id  =  (int) $product['product_id'];
				$vid =  (int) $product['variation_id'];				
				
				$pid     = (int) get_post_meta($id, 'infusionsoft_product', true);
				
				if($vid != 0)   $sku = get_post_meta($vid, '_sku', true);
				if(empty($sku)) $sku = get_post_meta($id, '_sku', true);
				$sdesc = '';


				if( empty($pid) ) {
					if(!empty($sku)) {
						$ifsproduct = $iwpro->app->dsFind('Product',1,0,'Sku',$sku, array('Id'));
						$ifsproduct = $ifsproduct[0];
						if(!empty($ifsproduct)) $pid = (int) $ifsproduct['Id'];
						else if($iwpro->settings['addsku'] == "yes") {
							$productname  = get_the_title($product['product_id']);
							$productprice = $product['line_total'];								
							$newproduct = array('ProductName' 	=> $productname,
												'ProductPrice'  => $productprice,
												'Sku'     		=> $sku);
							$pid = (int) $iwpro->app->dsAdd("Product", $newproduct);
						} else $pid = 0;
					} else $pid = 0;						
				} 
		
				$qty 	= (int) $product['qty'];
				$price 	= ((float) $product['line_total']) / ((float) $product['qty']);
				
				$tag    = (int) get_post_meta($id, 'infusionsoft_tag', 	true);
				$email  = (int) get_post_meta($id, 'infusionsoft_email', 	true);
				$action = (int) get_post_meta($id, 'infusionsoft_action', 	true);				
				
				if ( !f10_exclude_product($product) ) {
					if(!empty($tag)) 	$iwpro->app->grpAssign($contactId, $tag);	
				}

				if(!empty($action)) $iwpro->app->runAS($contactId, $action);
				if(!empty($email))	$iwpro->app->sendTemplate(array($contactId), $email);

				if(!empty($sku) && preg_match("/^[A-Za-z0-9]+$/", $sku)) $iwpro->app->achieveGoal("woopurchase", $sku, $contactId);		
				
				$iwpro->app->addOrderItem($inv_id, $pid, 4, $price, $qty, $product['name'], $sdesc);
				$calc_totals += $qty * $price;		
			}

			$iwpro->app->achieveGoal("woopurchase", "any", $contactId);			
			
			// TAX LINE
			$tax = (float) $order->get_total_tax();
			if($tax > 0.0) {
				$iwpro->app->addOrderItem($inv_id, 0, 2, $tax, 1, 'Tax','');
				$calc_totals += $tax;
			}
			
			// SHIPPING LINE
			$s_method = (string) $order->get_shipping_method();  
			$s_total  = (float)  $order->get_total_shipping();
			if($s_total > 0.0) {
				$iwpro->app->addOrderItem($inv_id, 0, 1, $s_total, 1, $s_method,$s_method);
				$calc_totals += $s_total;
			}

			//coupon line
			$discount = (float) ($calc_totals - $order->get_total());
			if ( round($discount,2) > 0.00  ) {
			  $iwpro->app->addOrderItem($inv_id, 0, 7, -$discount, 1, 'Discount', 'Woocommerce Shop Coupon Code');
			  $calc_totals -= $discount;		  
			} 
			
			$method = $order->payment_method_title;
			
			$totals = (float) $iwpro->app->amtOwed($inv_id);
			$iwpro->app->manualPmt($inv_id, $totals, $orderDate, $method, "Woocommerce Checkout",false);
			
			//Add Order Notes				
			$jobid  = $iwpro->app->dsLoad("Invoice",$inv_id, array("JobId"));
			$jobid  = (int) $jobid['JobId'];
			$iwpro->app->dsUpdate("Job",$jobid, array("JobNotes" => $order->customer_note, 'OrderType' => 'Online'));
			update_post_meta($order_id, 'infusionsoft_order_id', $jobid);
			$appname = isset($iwpro->machine_name) ? $iwpro->machine_name : "";
			update_post_meta($order_id, 'infusionsoft_view_order', "https://$appname.infusionsoft.com/Job/manageJob.jsp?view=edit&ID=$jobid");
				
		}				
	} else {
		foreach($products as $product) {
			if ( !f10_exclude_product($product) ) {
					
				$sku = "";
				$id  =  (int) $product['product_id'];
				$vid =  (int) $product['variation_id'];	

				if($vid != 0)   $sku = get_post_meta($vid, '_sku', true);
				if(empty($sku)) $sku = get_post_meta($id, '_sku', true);
			
				$tag    = (int) get_post_meta($id, 'infusionsoft_tag', 	true);
				$email  = (int) get_post_meta($id, 'infusionsoft_email', 	true);
				$action = (int) get_post_meta($id, 'infusionsoft_action', 	true);				

				if(!empty($action)) $iwpro->app->runAS($contactId, $action);
				if(!empty($tag)) 	$iwpro->app->grpAssign($contactId, $tag);	
				if(!empty($email))	$iwpro->app->sendTemplate(array($contactId), $email);

				if(!empty($sku) && preg_match("/^[A-Za-z0-9]+$/", $sku)) $iwpro->app->achieveGoal("woopurchase", $sku, $contactId);			
			}
		}

		$iwpro->app->achieveGoal("woopurchase", "any", $contactId);			
	}

	
	// TRIGGER GOAL IF COUPON CODE IS USED:
	$used_coup = $order->get_used_coupons();
	
	if (function_exists('ia_save_cart')) ia_save_cart($email, "");

	if(is_array($used_coup)) { 
		foreach($used_coup as $c)
			$iwpro->app->achieveGoal("woocoupon", $c, $contactId);	
	}

	do_action( 'infusedwoo_payment_complete', $order_id );
}


function f10_exclude_product($product) {

	$cats = get_the_terms( $product['product_id'], 'product_cat' );

	$exclude_cats = array('Webinars','Conferences','Workshops','Focus Groups');

	foreach ($cats as $cat) {
		if ( in_array($cat->name, $exclude_cats) ) {
			return true;
		}
	}

	return false;

}




?>