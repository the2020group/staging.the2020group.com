<?php
	if (class_exists('WC_Subscriptions_Manager')) {
		add_action('activated_subscription', 'iw_handle_activated_subscription', 10, 2);
		add_action('cancelled_subscription', 'iw_handle_cancelled_subscription', 10, 2);
		add_action('subscription_put_on-hold', 'iw_handle_subscription_put_onhold', 10, 2);
		add_action('subscription_expired', 'iw_handle_subscription_expired', 10, 2);
	}

	function iw_handle_activated_subscription($user_id, $sub_key) {
		global $iwpro;
		if(!$iwpro->ia_app_connect()) return;

		// GET PRODUCT ID
		$sub = WC_Subscriptions_Manager::get_subscription($sub_key);
		$product_id = $sub['product_id'];
		$find_id = !empty($sub['variation_id']) ? $sub['variation_id'] : $sub['product_id'];


		// GET CONTACT ID
		$order = new WC_Order( $sub['order_id'] );
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

		// ACTION SET & CAMPAIGN TRIGGER
		$as 	= (int) get_post_meta($product_id, 'infusionsoft_sub_activated', true);
		$cpgoal = get_post_meta($find_id, '_sku', true);

		// RUN ACTIONS
		if(!empty($as)) $iwpro->app->runAS($contactId, $as);
		if(!empty($cpgoal)) $iwpro->app->achieveGoal("woosubactivated", $cpgoal, $contactId);	
		$iwpro->app->achieveGoal("woosubactivated", "any", $contactId);		
	}

	function iw_handle_cancelled_subscription($user_id, $sub_key) {
		global $iwpro;
		if(!$iwpro->ia_app_connect()) return;

		// GET PRODUCT ID
		$sub = WC_Subscriptions_Manager::get_subscription($sub_key);
		$product_id = $sub['product_id'];
		$find_id = !empty($sub['variation_id']) ? $sub['variation_id'] : $sub['product_id'];

		// GET CONTACT ID
		$order = new WC_Order( $sub['order_id'] );
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

		// ACTION SET & CAMPAIGN TRIGGER
		$as 	= (int) get_post_meta($product_id, 'infusionsoft_sub_cancelled', true);
		$cpgoal = get_post_meta($find_id, '_sku', true);

		// RUN ACTIONS
		if(!empty($as)) $iwpro->app->runAS($contactId, $as);
		if(!empty($cpgoal)) $iwpro->app->achieveGoal("woosubcancelled", $cpgoal, $contactId);	
		$iwpro->app->achieveGoal("woosubcancelled", "any", $contactId);			
	}

	function iw_handle_subscription_put_onhold($user_id, $sub_key) {
		global $iwpro;
		if(!$iwpro->ia_app_connect()) return;

		// GET PRODUCT ID
		$sub = WC_Subscriptions_Manager::get_subscription($sub_key);
		$product_id = $sub['product_id'];
		$find_id = !empty($sub['variation_id']) ? $sub['variation_id'] : $sub['product_id'];

		// GET CONTACT ID
		$order = new WC_Order( $sub['order_id'] );
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

		// ACTION SET & CAMPAIGN TRIGGER
		$as 	= (int) get_post_meta($product_id, 'infusionsoft_sub_on-hold', true);
		$cpgoal = get_post_meta($find_id, '_sku', true);

		// RUN ACTIONS
		if(!empty($as)) $iwpro->app->runAS($contactId, $as);
		if(!empty($cpgoal)) $iwpro->app->achieveGoal("woosubsuspended", $cpgoal, $contactId);	
		$iwpro->app->achieveGoal("woosubsuspended", "any", $contactId);			
	}

	function iw_handle_subscription_expired($user_id, $sub_key) {
		global $iwpro;
		if(!$iwpro->ia_app_connect()) return;

		// GET PRODUCT ID
		$sub = WC_Subscriptions_Manager::get_subscription($sub_key);
		$product_id = $sub['product_id'];
		$find_id = !empty($sub['variation_id']) ? $sub['variation_id'] : $sub['product_id'];

		// GET CONTACT ID
		$order = new WC_Order( $sub['order_id'] );
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

		// ACTION SET & CAMPAIGN TRIGGER
		$as 	= (int) get_post_meta($product_id, 'infusionsoft_sub_expired', true);
		$cpgoal = get_post_meta($find_id, '_sku', true);

		// RUN ACTIONS
		if(!empty($as)) $iwpro->app->runAS($contactId, $as);
		if(!empty($cpgoal)) $iwpro->app->achieveGoal("woosubexpired", $cpgoal, $contactId);	
		$iwpro->app->achieveGoal("woosubexpired", "any", $contactId);			
	}

?>