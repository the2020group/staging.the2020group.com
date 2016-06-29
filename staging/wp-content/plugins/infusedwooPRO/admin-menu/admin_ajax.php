<?php

add_action( 'wp_ajax_ia_admin_apicreds', 'ia_admin_apicreds' );
add_action( 'wp_ajax_iw_fetch_products', 'iw_fetch_products' );
add_action( 'wp_ajax_iw_process_products', 'iw_process_products' );
add_action( 'wp_ajax_iw_count_orders', 'iw_count_orders' );
add_action( 'wp_ajax_iw_process_orders', 'iw_process_orders' );
add_action( 'wp_ajax_iw_save_orders', 'iw_save_orders' );
add_action( 'wp_ajax_iw_advanced_tracking', 'iw_advanced_tracking' );
add_action( 'wp_ajax_iw_enable_regtoifs', 'iw_enable_regtoifs' );

add_action( 'wp_ajax_iw_cf_save_group', 'iw_cf_save_group');
add_action( 'wp_ajax_iw_cf_load_fields', 'iw_cf_load_fields');
add_action( 'wp_ajax_iw_cf_del_group', 'iw_cf_del_group');
add_action( 'wp_ajax_iw_cf_load_group', 'iw_cf_load_group');
add_action( 'wp_ajax_iw_cf_reposition_groups', 'iw_cf_reposition_groups');
add_action( 'wp_ajax_iw_cf_save_field', 'iw_cf_save_field');
add_action( 'wp_ajax_iw_cf_load_field', 'iw_cf_load_field');
add_action( 'wp_ajax_iw_cf_del_field', 'iw_cf_del_field');
add_action( 'wp_ajax_iw_cf_reposition_fields', 'iw_cf_reposition_fields');

add_action( 'wp_ajax_iw_ty_save_ov', 'iw_ty_save_ov');
add_action( 'wp_ajax_iw_ty_load_ovs', 'iw_ty_load_ovs');
add_action( 'wp_ajax_iw_ty_load_ov', 'iw_ty_load_ov');
add_action( 'wp_ajax_iw_ty_reposition_ovs', 'iw_ty_reposition_ovs');
add_action( 'wp_ajax_iw_ty_del_ov', 'iw_ty_del_ov');

function ia_admin_apicreds() {
	global $iwpro;
	if(!isset($_GET['app']) || !isset($_GET['api'])) {
		die("Empty App Name or API Key");
	}

	if(class_exists('iaSDK')) {
		$testapp = new iaSDK;
		
		$testapp->configCon($_GET['app'], $_GET['api']);
		$checker = $testapp->dsGetSetting('Contact', 'optiontypes');
		
		//VALIDATE CREDENTIALS
		$pos = strrpos($checker, "ERROR");

		if ($pos === false)  {
			if(isset($iwpro->settings)) {
				$settings = $iwpro->settings;
			} else {
				$settings = array();
			}

			$settings['enabled'] = "yes";
			$settings['machinename'] = $_GET['app'];
			$settings['apikey'] = $_GET['api'];


			update_option( $iwpro->plugin_id . $iwpro->id . '_settings', $settings );
			$iwpro->ia_app_connect();
			die("ok");					
		} else {
			die($checker);	
		}
	} else {
		die("API File / Conflict Error.");
	}		
}










function iw_fetch_iproducts() {
	global $woocommerce;
  	global $iwpro;
  	global $wpdb;

  	if(!$iwpro->ia_app_connect()) {
  		echo "Error connecting to Infusionsoft.";
  		return;
  	}


  	// GET ALL PRODUCT CATEGORIES
  	$prodcats_t = $iwpro->app->dsFind('ProductCategory',1000,0,'Id','%',array('Id','CategoryDisplayName'));
  	
  	$prodcats = array();
  	foreach($prodcats_t as $pc) {
  		$prodcats[$pc['Id']] = $pc['CategoryDisplayName'];
  	}  

  	$pincats = array();
  	$bucket = array();
  	$i = 0;

  	do {
			$bucket = $iwpro->app->dsFind('ProductCategoryAssign',1000,$i,'ProductCategoryId','%', array('ProductId','ProductCategoryId'));
			if(is_array($bucket)) $pincats = array_merge($pincats, $bucket);
			$i++;
		} while(count($bucket) == 1000); 

  	// GET PRODUCTS:
  	$q = array(
				'Id',
				'Sku',
				'ProductName',
				'ProductPrice',
				'ShortDescription',
				'Taxable',
				'Weight',
				'Description',
				'TopHTML',
				'BottomHTML',
				'InventoryLimit',
				'Shippable',
				'LargeImage'
			);

  	$allproducts = array();
  	$bucket = array();
  	$i = 0;

	do {
		$bucket = $iwpro->app->dsFind('Product',1000,$i,'Id','%', $q);
		if(is_array($bucket)) $allproducts = array_merge($allproducts, $bucket);
		$i++;
	} while(count($bucket) == 1000); 


	if(isset($_GET['options']['step2']) && $_GET['options']['step2'] == 'cat') {
  		$catlist = $_GET['options']['step2further'];

  		$incl = array();

  		if(is_array($pincats) && count($pincats) > 0) {
	  		foreach($catlist as $c) {
  				foreach($pincats as $p) 
  					if($p['ProductCategoryId'] == trim($c)) $incl[] = $p['ProductId'];
	  		}
  		}

  		$products = array();
  		foreach($allproducts as $a) {
  			if(in_array($a['Id'], $incl)) $products[] = $a;
  		}
  	} else if(isset($_GET['options']['step2']) && $_GET['options']['step2'] == 'id') {
  		$products = array();

  		$allowedids =  iw_split_entry($_GET['options']['step2further']);

  		foreach($allproducts as $p) {
  			if(in_array($p['Id'], $allowedids)) {
  				$products[] = $p;
  			}
  		}

  	} else {
  		$products = $allproducts;
  	}

  	foreach($products as $k => $v) {
		if(isset($products[$k]['LargeImage'])) $products[$k]['LargeImage'] = base64_encode($products[$k]['LargeImage']);
	}

	$result = array(
		"products" => $products,
		"prodcats" => $prodcats,
		"pincats" => $pincats
		);

	if(!isset($_GET['callback'])) echo json_encode($result);
	else echo "{$_GET['callback']}(" . json_encode($result) . ")";
	
	die();
}

function iw_fetch_wproducts() {
	global $woocommerce;
  	global $iwpro;
  	global $wpdb;

  	$args = array(
			'posts_per_page'   => 999999,
			'orderby'          => 'post_date',
			'order'            => 'ASC',
			'post_type'        => 'product',
			'post_status'      => 'publish',
		);

  	$allwooprods = get_posts( $args );

	if(isset($_GET['options']['step2']) && $_GET['options']['step2'] == 'cat') {
		$wooprods = array();
		$cat_ids = $_GET['options']['step2further'];
		$cat_ids = array_map('intval', $cat_ids);
		$cat_ids = array_unique( $cat_ids );

		foreach($allwooprods as $k => $prod) {
			$terms = wp_get_object_terms( $prod->ID, 'product_cat');
			foreach($terms as $t) {
				if(in_array($t->term_id, $cat_ids)) {
					$wooprods[] = $prod;
					break;
				}
			}
		}
	} else if(isset($_GET['options']['step2']) && $_GET['options']['step2'] == 'id') {
		$wooprods = array();

  		$allowedids = iw_split_entry($_GET['options']['step2further']);

  		foreach($allwooprods as $k => $prod) {
  			if(in_array($prod->ID, $allowedids)) {
  				$wooprods[] = $prod;
  			}
  		}
	} else $wooprods = $allwooprods;

	$result = array(
		"products" => $wooprods,
		);

	if(!isset($_GET['callback'])) echo json_encode($result);
	else echo "{$_GET['callback']}(" . json_encode($result) . ")";
	
	die();
}

function iw_fetch_products() {
	error_reporting(0);
	if(isset($_GET['options']['step1']) && $_GET['options']['step1'] == 'import') {
		iw_fetch_iproducts();
	} else {
		iw_fetch_wproducts();
	}
}








function iw_import_products() {
	global $woocommerce;
  	global $iwpro;
  	global $wpdb;
  	error_reporting(0);

  	if(!$iwpro->ia_app_connect()) {
  		echo "Error connecting to Infusionsoft.";
  		return;
  	}

  	$products = $_POST['product'];
  	$prodcats = $_POST['prodcats'];
  	$pincats = $_POST['pincats'];

	if(is_array($products) && count($products) > 0) {

  		foreach($products as $product) {
		  	// SEARCH BY SKU:
		  	$sku = isset($product['Sku']) ? $product['Sku'] : "";
		  	$pid = isset($product['Id']) ? $product['Id'] : "";

		  	if(!empty($sku)) {
			  	$product_id = $wpdb->get_var($wpdb->prepare("SELECT posts.ID FROM $wpdb->posts posts
			  		INNER JOIN $wpdb->postmeta postmeta ON posts.ID = postmeta.post_id
			  		WHERE postmeta.meta_key='_sku' AND postmeta.meta_value='%s' AND posts.post_type='product' AND posts.post_status NOT LIKE 'trash' LIMIT 1
			  		", $sku ));
		  	} else {
			  	$product_id = $wpdb->get_var($wpdb->prepare("SELECT posts.ID FROM $wpdb->posts posts
			  		INNER JOIN $wpdb->postmeta postmeta ON posts.ID = postmeta.post_id
			  		WHERE postmeta.meta_key='infusionsoft_product' AND postmeta.meta_value='%s' AND posts.post_type='product' AND posts.post_status NOT LIKE 'trash' LIMIT 1
			  		", $pid ));				  		
		  	}

		  	if(empty($product_id)) {
		  		// if not exist, create new product
				$new_product = array(
				  'post_title'    => $product['ProductName'],
				  'post_status'   => 'publish',
				  'post_type' => 'product'
				);

				// Insert the post into the database
				$product_id = wp_insert_post( $new_product );
		  	} else {
		  		$upd_content = array(
					      'ID'          => $product_id,
					      'post_title' 	=> $product['ProductName']
					  );

		  		wp_update_post( $upd_content );
		  	}

		  	// ------- IMAGE: Still working...
		  	if(isset($product['LargeImage']) && !empty($product['LargeImage']) && $_POST['options']['images'] == 'yes') {
		  		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		  		require_once( ABSPATH . 'wp-admin/includes/file.php' );
				$wp_upload_dir = wp_upload_dir();

		  		$img_path = iw_upload_blob(base64_decode($product['LargeImage']), $product['Id']);
		  		$full_path = $wp_upload_dir['basedir'] . "/" . $img_path;

				$parent_post_id = $product_id;
				$filetype = wp_check_filetype( basename( $full_path ), null );
				

				$attachment = array(
					'guid'           => $wp_upload_dir['url'] . '/' . basename($full_path), 
					'post_mime_type' => $filetype['type'],
					'post_title'     => preg_replace( '/\.[^.]+$/', '', basename($full_path)),
					'post_content'   => '',
					'post_status'    => 'inherit'
				);


				// Insert the attachment.
				$attach_id = wp_insert_attachment( $attachment, $full_path, $parent_post_id );

				// Generate the metadata for the attachment, and update the database record.
				$attach_data = wp_generate_attachment_metadata( $attach_id, $full_path );
				wp_update_attachment_metadata( $attach_id, $attach_data );

				// UPDATE WOO PRODUCT:
				update_post_meta($product_id, '_thumbnail_id', $attach_id);
				update_post_meta($product_id, '_wp_attached_file', $img_path);
		  	}

		  	// update category
		  	$pcats = array();

		  	foreach($pincats as $p) 
		  		if($product['Id'] == $p['ProductId']) $pcats[] = $prodcats[$p['ProductCategoryId']];
		  	
		  	if(count($pcats) > 0) {
		  		wp_set_object_terms( $product_id, $pcats, 'product_cat' );
		  	}

		  	// update meta
		  	update_post_meta($product_id, '_sku', $sku);
		  	if(empty($sku)) update_post_meta($product_id, 'infusionsoft_product', $pid);
		  	update_post_meta($product_id, '_regular_price', $product['ProductPrice']);
		  	update_post_meta($product_id, '_visibility', 'visible');
		  	update_post_meta($product_id, '_price', $product['ProductPrice']);

		  	if(isset($_POST['options']['content']) && !empty($_POST['options']['content'])) {
		  		$contentsrc = $_POST['options']['content'];
		  		if($contentsrc == 'shortdesc') $content = $product['ShortDescription'];
		  		else if($contentsrc == 'desc') $content = $product['Description'];
		  		else if($contentsrc == 'topbottom') $content = $product['TopHTML'] . $product['BottomHTML'];
		  		else if($contentsrc == 'topdescbottom') $content = $product['TopHTML'] . $product['Description'] . $product['BottomHTML'];

		  		if(isset($content)) {
			  		$upd_content = array(
					      'ID'           => $product_id,
					      'post_content' => $content
					  );

			  		wp_update_post( $upd_content );
		  		}

		  	}

		  	if(isset($_POST['options']['shortdesc']) && !empty($_POST['options']['shortdesc'])) {
		  		$shortsrc = $_POST['options']['shortdesc'];
		  		if($shortsrc == 'shortdesc') $short = $product['ShortDescription'];
		  		else if($shortsrc == 'desc') $short = $product['Description'];

		  		if(isset($short)) {
			  		$upd_content = array(
					      'ID'           => $product_id,
					      'post_excerpt' => $short
					  );

			  		wp_update_post( $upd_content );
		  		}
		  	}

		  	if($_POST['options']['virtual'] == 'yes') {
		  		 if($product['Shippable']) 
		  		 	update_post_meta($product_id, '_virtual', 'no');
		  		 else 
		  		 	update_post_meta($product_id, '_virtual', 'yes');
		  		 
		  	}

		  	if($_POST['options']['tax'] == 'yes') {
		  		 if($product['Taxable']) 
		  		 	update_post_meta($product_id, '_tax_status', 'taxable');
		  		 else 
		  		 	update_post_meta($product_id, '_tax_status', '');				  		
		  	}

  		}
  		
  	}


	die("ok");
}

function iw_export_products() {
	global $woocommerce;
  	global $iwpro;
  	global $wpdb;
  	error_reporting(0);

  	if(!$iwpro->ia_app_connect()) {
  		echo "Error connecting to Infusionsoft.";
  		return;
  	}

  	// MAP FIELDS:
	$mapped = array();

	foreach($_POST['options'] as $k => $v) {
		$fld = $v['name'];
		$val = $v['value'];

  		$isfld = (strpos($fld, "export-") !== false && strpos($fld, "-meta") === false);

  		if($isfld) {
  			$ifsfld = str_replace("export-", "", $fld);
  			if(!empty($val)) {
  				$mapped[$ifsfld] = $val;
  			}
  		} 
  	}

  	$wooprods = $_POST['product'];

  	foreach($wooprods as $prod) {
		$addprod = array();
		$ifsid = 0;

		$force_ifsid = (int) get_post_meta($prod['ID'], 'infusionsoft_product', true);

		foreach($mapped as $k => $val) {
			if($val == 'sku') {
				$addprod[$k] = get_post_meta($prod['ID'], '_sku', true);
			} else if($val == 'short') { 
				$addprod[$k] = $prod['post_excerpt'];
			} else if($val == 'content') { 
				$addprod[$k] = $prod['post_content'];
			} else if($val == 'regprice') { 
				$addprod[$k] = get_post_meta($prod['ID'], '_price', true);
			} else if($val == 'saleprice') { 
				$addprod[$k] = get_post_meta($prod['ID'], '_sale_price', true);
			} else if($val == 'title') { 
				$addprod[$k] = $prod['post_title'];
			} else if($val == 'weight') { 
				$addprod[$k] = get_post_meta($prod['ID'], '_weight', true);
			} else if($val == 'virtual') { 
				$v = get_post_meta($prod['ID'], '_virtual', true);
				if($v == 'yes') $v = 0;
				else $v = 1;

				$addprod[$k] = $v;
			} else if($val == 'stock') { 
				$addprod[$k] = get_post_meta($prod['ID'], '_stock', true);
			} else if($val == 'taxstatus') { 
				$v = get_post_meta($prod['ID'], '_tax_status', true);
				if($v == 'taxable') $v = 1;
				else $v = 0;

				$addprod[$k] = $v;
			} else if($val == 'meta') {
				$mkey = $_POST["export-$k-meta"];
				$addprod[$k] = get_post_meta($prod['ID'], $mkey, true);
			} else if($val == 'productimage') {
				$attach_id = get_post_meta($prod['ID'], '_thumbnail_id', true);
				if($attach_id > 0) {
					$attachment = wp_get_attachment_image_src( $attach_id, 'medium' );
					//$base64 = base64_encode(file_get_contents($attachment[0])); 
					$base64 = "BASE64:" . file_get_contents($attachment[0]);
				}
			}
		}

		// search existence of product in Infusionsoft
		if(isset($force_ifsid) && !empty($force_ifsid)) {
			$ifsprod = $iwpro->app->dsLoad('Product',$force_ifsid, array('Id'));

			if(is_array($ifsprod) && count($ifsprod) > 0) {
				$ifsid = $force_ifsid;
			}
		}

		if(!isset($ifsid) || empty($ifsid)) {
			$ifsprod = $iwpro->app->dsFind('Product',1,0,'Sku', $addprod['Sku'], array('Id'));

			if(is_array($ifsprod) && count($ifsprod) > 0) {
				$iprod = $ifsprod[0];
				$ifsid = $iprod['Id'];
			}
		}

		$addprod['Status'] = 1;


		if(isset($ifsid) && $ifsid > 0) {
			$iwpro->app->dsUpdate('Product', $ifsid, $addprod);
		} else {
			$ifsid = $iwpro->app->dsAdd('Product', $addprod);
		}

		if(isset($base64) && !empty($base64)) {
			//echo '<img src="data:image/jpeg;base64,'.$base64.'" />';
			$test = $iwpro->app->dsUpdateWithImage('Product', $ifsid, array('LargeImage' => $base64));
			unset($base64);
		}
  	}

  	die("ok");
}

function iw_process_products() {
	error_reporting(0);
	if(isset($_POST['method']) && $_POST['method'] == 'import') {
		iw_import_products();
	} else {
		iw_export_products();
	}
}

function iw_count_orders() {
	global $iwpro;

	$count = 0;

	if($_POST['options']['step1'] == 'import') {
		if(!$iwpro->ia_app_connect()) return false;

		if($_POST['options']['step2'] == 'all') {
			$count = $iwpro->app->dsCount("Invoice",array("Id" => "%"));
		} else if($_POST['options']['step2'] == 'cat') {
			$count_paid = $iwpro->app->dsCount("Invoice",array("PayStatus" => 1));
			$count_unpaid = $iwpro->app->dsCount("Invoice",array("PayStatus" => 0));

			if(in_array("unpaid", $_POST['options']['step2further'])) {
				$count += $count_unpaid;
			} 

			if(in_array("paid", $_POST['options']['step2further'])) {
				$count += $count_paid;
			} 
		} else {
			$count = count(iw_split_entry($_POST['options']['step2further']));
		}
	} else {
		if($_POST['options']['step2'] == 'all') {
			$count_info = wp_count_posts('shop_order');
			foreach(array_keys( wc_get_order_statuses() ) as $s ) {
				$count += $count_info->$s;
			}
		} else if($_POST['options']['step2'] == 'cat') {
			$count_info = wp_count_posts('shop_order');
			foreach($_POST['options']['step2further'] as $s ) {
				$count += $count_info->$s;
			}
		} else {
			$count = count(iw_split_entry($_POST['options']['step2further']));
		}
		
	}

	echo $count;
	die();
}


function iw_split_entry($vals) {
	$split_entry = explode(",", $vals);
	$split_result = array();

	for($i = 0; $i < count($split_entry); $i++) {
		if(strpos($split_entry[$i], "-") !== false) {
			$ranged_input = explode("-", $split_entry[$i]);
			if((int) trim($ranged_input[1]) < (int) trim($ranged_input[0])) return false;

			for($j = (int) trim($ranged_input[0]); $j <= (int) trim($ranged_input[1]); $j++) {
				$split_result[] = $j;
			}
		} else if((int) trim($split_entry[$i]) > 0) {
			$split_result[] = (int) trim($split_entry[$i]);
		} else {
			return false;
		}
	}
	
	return $split_result;
}






function iw_process_orders() {
	error_reporting(0);
	global $iwpro;
	$options = $_POST['options'];
	if(!$iwpro->ia_app_connect()) die($iwpro->settings['apperrormsg']);

	// Fetch Orders First
	if($options['step1'] == 'import') {
		if($options['step2'] == 'all') {
			$orders = $iwpro->app->dsFind('Invoice', (int) $options['pergroup'], (int) $options['toprocess'], "Id", "%", array(
					'Id',
					'ContactId',
					'JobId',
					'PayStatus',
					'DateCreated',
					'TotalDue',
					'TotalPaid'
				));
		} else if($options['step2'] == 'cat') {

			if(in_array("unpaid", $_POST['options']['step2further']) && in_array("paid", $_POST['options']['step2further'])) {
				$query = "%";
			} else if(in_array("unpaid", $_POST['options']['step2further'])) {
				$query = 0;
			} else {
				$query = 1;
			}
			$orders = $iwpro->app->dsFind('Invoice', (int) $options['pergroup'], (int) $options['toprocess'], "PayStatus", $query, array(
					'Id',
					'ContactId',
					'JobId',
					'PayStatus',
					'DateCreated',
					'TotalDue',
					'TotalPaid'
				));
		} else {
			$order_ids = iw_split_entry($_POST['options']['step2further']);

			$orders = array();
			for($i = (int) $options['toprocess']*(int) $options['pergroup']; $i < ((int) $options['toprocess'] + 1)*(int) $options['pergroup']; $i++) {
				$order = $iwpro->app->dsLoad("Job", (int) $order_ids[$i], array(
						'ContactId',
						'Id',
						'DateCreated',
					));

				if(is_array($order) && count($order) > 0) $orders[] = $order;
			}
		}

		if(count($orders) > 0) {
			foreach($orders as $order) {
				$jobid = isset($order['JobId']) ? (int) $order['JobId'] : (int) $order['Id'];
				$status = str_replace("wc-", "", $options['step3']);

				// Check first if order already exists
				$args = array(
				    'meta_query' => array(
				        array(
				            'key' => 'infusionsoft_order_id',
				            'value' => $jobid
				        )
				    ),
				    'post_type' => 'shop_order',
				    'posts_per_page' => 1
				);
				$check = get_posts($args);

				if(count($check) > 0) continue;

				// Create New Order:
				$new_order = wc_create_order(array('status' => $status));

				// Save Billing and Shipping Information
				$ifscontact = $iwpro->app->loadCon($order['ContactId'], array(
					'FirstName',
					'LastName',
					'Email',
					'StreetAddress1',
					'StreetAddress2',
					'City',
					'State',
					'Country',
					'PostalCode',
					'Address2Street1',
					'Address2Street2',
					'City2',
					'State2',
					'Country2',
					'PostalCode2',
					'Phone1',
					'Company'
					));

				$new_order->set_address(array(
					'email'			=> isset($ifscontact['Email']) ? $ifscontact['Email'] : "",
					'first_name' 	=> isset($ifscontact['FirstName']) ? $ifscontact['FirstName'] : "",
					'last_name' 	=> isset($ifscontact['LastName']) ? $ifscontact['LastName'] : "",
					'address_1' 	=> isset($ifscontact['StreetAddress1']) ? $ifscontact['StreetAddress1'] : "",
					'address_2' 	=> isset($ifscontact['StreetAddress2']) ? $ifscontact['StreetAddress2'] : "",
					'city' 			=> isset($ifscontact['City']) ? $ifscontact['City'] : "",
					'state' 		=> isset($ifscontact['State']) ? $ifscontact['State'] : "",
					'country' 		=> isset($ifscontact['Country']) ? iw_to_country_code($ifscontact['Country']) : "",
					'postcode' 		=> isset($ifscontact['PostalCode']) ? $ifscontact['PostalCode'] : "",
					'phone' 		=> isset($ifscontact['Phone1']) ? $ifscontact['Phone1'] : "",
					'company' 		=> isset($ifscontact['Company']) ? $ifscontact['Company'] : ""
					), 'billing');

				$new_order->set_address(array(
					'first_name' 	=> isset($ifscontact['FirstName']) ? $ifscontact['FirstName'] : "",
					'last_name' 	=> isset($ifscontact['LastName']) ? $ifscontact['LastName'] : "",
					'address_1' 	=> isset($ifscontact['Address2Street1']) ? $ifscontact['Address2Street1'] : "",
					'address_2' 	=> isset($ifscontact['Address2Street2']) ? $ifscontact['Address2Street2'] : "",
					'city' 			=> isset($ifscontact['City2']) ? $ifscontact['City2'] : "",
					'state' 		=> isset($ifscontact['State2']) ? $ifscontact['State2'] : "",
					'country' 		=> isset($ifscontact['Country2']) ? iw_to_country_code($ifscontact['Country2']) : "",
					'postcode' 		=> isset($ifscontact['PostalCode2']) ? $ifscontact['PostalCode2'] : ""
					), 'shipping');

				// Add Order Items:
				$items = $iwpro->app->dsFind('OrderItem',1000,0,'OrderId',$jobid, array(
						'ItemName',
						'ItemType',
						'Qty',
						'ProductId',
						'PPU',
						'CPU'
					));

				foreach($items as $item) {
					if($item['ItemType'] == 1 || $item['ItemType'] == 14) {
						$shipping_fee = new WC_Shipping_Rate;
						$shipping_fee->label = $item['ItemName'];
						$shipping_fee->cost = $item['PPU'];

						$new_order->add_shipping($shipping_fee);
					} else if($item['PPU'] < 0) {
						$new_order->add_coupon( $item['ItemName'], -$item['PPU']);
					} else if($item['ItemType'] == 2) {
						$tax = new WC_Shipping_Rate;
						$tax->label = "TAX: " . $item['ItemName'];
						$tax->cost = $item['PPU'];
						$tax->taxable = false;

						$new_order->add_fee( $tax );
					} else if(isset($item['ProductId']) && $item['ProductId'] > 0) {
						$wcprodid = 0;

						// search by product id:
						$args = array(
						    'meta_query' => array(
						        array(
						            'key' => 'infusionsoft_product',
						            'value' => $item['ProductId']
						        )
						    ),
						    'post_type' => 'product',
						    'posts_per_page' => 1
						);
						$wcprod = get_posts($args);

						if(count($wcprod) > 0) $wcprodid = $wcprod[0]->ID;
						else {
							$ifsprod = $iwpro->app->dsLoad('Product',$item['ProductId'],array('Sku'));
							if(isset($ifsprod['Sku'])) {
								$args = array(
								    'meta_query' => array(
								        array(
								            'key' => '_sku',
								            'value' => $ifsprod['Sku']
								        )
								    ),
								    'post_type' => 'product',
								    'posts_per_page' => 1
								);
								$wcprod = get_posts($args);
								if(count($wcprod) > 0) $wcprodid = $wcprod[0]->ID;
							}
						}

						if($wcprodid > 0) {
							$wcprod_to_add = new WC_Product($wcprodid);
							$new_order->add_product( $wcprod_to_add, $item['Qty'] );
						} else {
							$custom = new WC_Shipping_Rate;
							$custom->label =  $item['ItemName'];
							$custom->cost = $item['PPU']*$item['Qty'];
							$custom->taxable = false;

							$new_order->add_fee( $custom );
						}
					} else {
						$custom = new WC_Shipping_Rate;
						$custom->label =  $item['ItemName'];
						$custom->cost = $item['PPU']*$item['Qty'];
						$custom->taxable = false;

						$new_order->add_fee( $custom );
					}
				}

				//Add Order Notes				
				$new_order->add_order_note("Imported From Infusionsoft.");
				update_post_meta($new_order->id, 'infusionsoft_order_id', $jobid);
				update_post_meta($new_order->id, 'infusionsoft_invoice_id', $order['Id']);
				$appname = isset($iwpro->machine_name) ? $iwpro->machine_name : "";
				update_post_meta($new_order->id, 'infusionsoft_view_order', "https://$appname.infusionsoft.com/Job/manageJob.jsp?view=edit&ID=$jobid");
			}
		}
	} else {
		if($options['step2'] == 'all') {
			$orders = get_posts( array(
			        'post_type'   => 'shop_order',
			        'post_status' => array_keys( wc_get_order_statuses() )
			));

		} else if($options['step2'] == 'cat') {
			$orders = get_posts( array(
			        'post_type'   => 'shop_order',
			        'post_status' => $options['step2further']
			));
		} else {
			$orders = get_posts( array(
			        'post_type'   => 'shop_order',
			        'post_status' => array_keys( wc_get_order_statuses() ),
			        'post__in' => iw_split_entry($_POST['options']['step2further'])
			));
		}

		if(is_array($orders) && count($orders) > 0) {
			foreach($orders as $order) {
				// BREAK IF INVOICE ALREADY CREATED

				$ifs_inv = get_post_meta($order->ID, 'infusionsoft_invoice_id', true );
				if($ifs_inv > 0) {
					continue;
				}
				$wcorder = new WC_Order( $order->ID );
				
				$email = $wcorder->billing_email;
				$payment_method = $wcorder->payment_method;
				$contact = $iwpro->app->dsFind('Contact',5,0,'Email',$email,array('Id'));
					$contact = $contact[0];	
				
				if ($contact['Id'] != null && $contact['Id'] != 0 && $contact != false){
					$contactId = (int) $contact['Id']; 
				} else {				
					$contactinfo = array(
						'FirstName'			=> stripslashes($wcorder->billing_first_name),
						'LastName'			=> stripslashes($wcorder->billing_last_name),
						'StreetAddress1' 	=> stripslashes($wcorder->billing_address_1),
						'StreetAddress2' 	=> stripslashes($wcorder->billing_address_2),
						'City' 				=> stripslashes($wcorder->billing_city),
						'State' 			=> stripslashes($wcorder->billing_state),
						'Country' 			=> stripslashes(iw_to_country($wcorder->billing_country)),
						'PostalCode' 		=> stripslashes($wcorder->billing_postcode),
						'Address2Street1' 	=> stripslashes($wcorder->shipping_address_1),
						'Address2Street2' 	=> stripslashes($wcorder->shipping_address_2),
						'City2' 			=> stripslashes($wcorder->shipping_city),
						'State2' 			=> stripslashes($wcorder->shipping_state),
						'Country2' 			=> stripslashes(iw_to_country($wcorder->shipping_country)),
						'PostalCode2' 		=> $wcorder->shipping_postcode,
						'Phone1'			=> $wcorder->billing_phone,
						'Company'			=> $b_company,
						'Email'				=> $email
					);
					$contactId  = $iwpro->app->addCon($contactinfo);
				}

				$products = $wcorder->get_items(); 

				// CREATE INVOICE
				$orderDate = date('Ymd\TH:i:s', strtotime($order->post_date_gmt));
				$inv_id = (int) $iwpro->app->blankOrder($contactId,"Woocommerce Order # {$order->ID}",$orderDate,0,0);
				update_post_meta($order->ID, 'infusionsoft_invoice_id', $inv_id);
				$calc_totals = 0;
				
				$products = $wcorder->get_items(); 

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

					$iwpro->app->addOrderItem($inv_id, $pid, 4, $price, $qty, $product['name'], $sdesc);
					$calc_totals += $qty * $price;		
				}

				// TAX LINE
				$tax = (float) $wcorder->get_total_tax();
				if($tax > 0.0) {
					$iwpro->app->addOrderItem($inv_id, 0, 2, $tax, 1, 'Tax','');
					$calc_totals += $tax;
				}
				
				// SHIPPING LINE
				$s_method = (string) $wcorder->get_shipping_method();  
				$s_total  = (float)  $wcorder->get_total_shipping();
				if($s_total > 0.0) {
					$iwpro->app->addOrderItem($inv_id, 0, 1, $s_total, 1, $s_method,$s_method);
					$calc_totals += $s_total;
				}

				//coupon line
				$discount = (float) ($calc_totals - $wcorder->get_total());
				if ( round($discount,2) > 0.00  ) {
				  $iwpro->app->addOrderItem($inv_id, 0, 7, -$discount, 1, 'Discount', 'Woocommerce Shop Coupon Code');
				  $calc_totals -= $discount;		  
				} 

				$method = $wcorder->payment_method_title;
				$totals = (float) $iwpro->app->amtOwed($inv_id);
				$status = $wcorder->get_status();

				if($status == 'processing' || $status == 'completed') {
					$iwpro->app->manualPmt($inv_id, $totals, $orderDate, $method, "Woocommerce Checkout",false);
				}

				$jobid  = $iwpro->app->dsLoad("Invoice",$inv_id, array("JobId"));
				$jobid  = (int) $jobid['JobId'];
				update_post_meta($order->ID, 'infusionsoft_order_id', $jobid);

				$as = (int) $options['step3'];
				if($as > 0) $iwpro->app->runAS($contactId, $as);
				$wcorder->add_order_note('Exported Order to Infusionsoft via Order Export Wizard.');
			}
		}
	}
	die("ok");
}

function iw_save_orders() {
	global $iwpro;

	if(isset($iwpro->settings)) {
		$settings = $iwpro->settings;
	} else {
		$settings = array();
	}

	$settings['saveOrders'] = $_GET['enable'] == 'true' ? "yes" : "no";


	update_option( $iwpro->plugin_id . $iwpro->id . '_settings', $settings );
}

function iw_enable_regtoifs() {
	global $iwpro;

	if(isset($iwpro->settings)) {
		$settings = $iwpro->settings;
	} else {
		$settings = array();
	}

	$settings['regtoifs'] = $_GET['enable'] == 'true' ? "yes" : "no";


	update_option( $iwpro->plugin_id . $iwpro->id . '_settings', $settings );
}

function iw_advanced_tracking() {
	global $iwpro;

	if(isset($iwpro->settings)) {
		$settings = $iwpro->settings;
	} else {
		$settings = array();
	}

	$settings['advancedTracking'] = $_GET['enable'] == 'true' ? "yes" : "no";


	update_option( $iwpro->plugin_id . $iwpro->id . '_settings', $settings );
}

function iw_cf_save_group() {
	$iw_cf_groups = get_option('iw_cf_groups');
	if(empty($iw_cf_groups)) $iw_cf_groups = array();

	if(isset($_POST['id']) && !empty($_POST['id'])) {
		if(count($iw_cf_groups) > 0) {
			foreach($iw_cf_groups as $k => $grp) {
				if($k == $_POST['id']) {
					$_POST['order'] = isset($iw_cf_groups[$_POST['id']]['order']) ? $iw_cf_groups[$_POST['id']]['order'] : $iw_cf_groups[$_POST['id']]['order'];
					$iw_cf_groups[$k] = $_POST;
				}
			}
		} else {
			$_POST['order'] = isset($iw_cf_groups[$_POST['id']]) ? $iw_cf_groups[$_POST['id']]['order'] : $iw_cf_groups[$_POST['id']]['order'];
			$iw_cf_groups[$_POST['id']] = $_POST; 
		}
	} else {
		if(count($iw_cf_groups) > 0) {
			$new_id = max(array_keys($iw_cf_groups)) + 1;
			$_POST['id'] = $new_id;
			$_POST['order'] = $new_id;
			$iw_cf_groups[$new_id] = $_POST; 
		} else {
			$_POST['id'] = 1;
			$_POST['order'] = 1;
			$iw_cf_groups[1] = $_POST; 
		}
	}

	update_option( 'iw_cf_groups', $iw_cf_groups );
	die("ok");
}

function iw_cf_load_fields() {
	$iw_cf_groups = get_option('iw_cf_groups');
	$iw_cf_fields = get_option('iw_cf_fields');

	// group fields
	$grouped_fields = array();
	if(is_array($iw_cf_fields) && count($iw_cf_fields) > 0) {
		foreach($iw_cf_fields as $k => $f) {
			$grouped_fields[$f['grpid']][$f['order']] = $f;
		} 
	}

	if(empty($iw_cf_groups)) die("");
	$sorted_cf_groups = array();

	foreach($iw_cf_groups as $k => $v) {
		$sorted_cf_groups[$iw_cf_groups[$k]['order']] = $v;
	}
	ksort($sorted_cf_groups);
	foreach($sorted_cf_groups as $k => $grp) {
		?>
		<li class="iw_cf_group" grpid="<?php echo $grp['id']; ?>">
			<span class="iw_cf_group_name"><?php echo strlen($grp['name']) > 35 ? substr($grp['name'], 0, 34) . "..." : $grp['name']; ?>
				<span class="controls">
					<i class="fa fa-plus grp-add" grpid="<?php echo $grp['id']; ?>" title="Add new custom field"></i>
					<i class="fa fa-pencil grp-edit" grpid="<?php echo $grp['id']; ?>" title="Group Settings"></i>
					<i class="fa fa-times grp-delete" grpid="<?php echo $grp['id']; ?>" title="Delete Group"></i>
				</span>
			</span>
			<?php 
				$grp_fields = isset($grouped_fields[$grp['id']]) ? $grouped_fields[$grp['id']] : array();

				if(count($grp_fields) > 0) {
					ksort($grp_fields);
					?>
					<ul class="iw_cf_fields"> 
					<?php foreach($grp_fields as $field) {
					?>
					<li class="iw_cf_field" fieldid="<?php echo $field['id']; ?>"><?php echo strlen($field['name']) > 35 ? substr($field['name'], 0, 34) . "..." : $field['name']; ?>
						<span class="controls">
							<i class="fa fa-pencil field-edit" title="Edit Custom Field" fieldid="<?php echo $field['id']; ?>"></i>
							<i class="fa fa-times field-delete" title="Delete Custom Field" fieldid="<?php echo $field['id']; ?>"></i>
						</span>
					</li>
					<?php } ?>
					</ul>
				<?php } ?>
		</li>
		<?php
	}

	die();
}

function iw_cf_del_group() {
	if(isset($_POST['grpid']) && $_POST['grpid'] >0) {
		$iw_cf_groups = get_option('iw_cf_groups');
		$iw_cf_fields = get_option('iw_cf_fields');

		if(empty($iw_cf_groups)) die("");

		if(in_array($_POST['grpid'], array_keys($iw_cf_groups)))
			unset($iw_cf_groups[$_POST['grpid']]);

		foreach($iw_cf_fields as $k => $v) {
			if($v['grpid'] == $_POST['grpid']) unset($iw_cf_fields[$k]);
		}

		update_option( 'iw_cf_groups', $iw_cf_groups );
		update_option( 'iw_cf_fields', $iw_cf_fields );
		die();
	}
}

function iw_cf_del_field() {
	if(isset($_POST['fieldid']) && $_POST['fieldid'] >0) {
		$iw_cf_fields = get_option('iw_cf_fields');
		if(empty($iw_cf_fields)) die("");

		if(in_array($_POST['fieldid'], array_keys($iw_cf_fields)))
			unset($iw_cf_fields[$_POST['fieldid']]);

		update_option( 'iw_cf_fields', $iw_cf_fields );
		die();
	}
}


function iw_cf_load_group() {
	if(isset($_GET['grpid']) && $_GET['grpid'] >0) {
		$iw_cf_groups = get_option('iw_cf_groups');
		if(empty($iw_cf_groups)) die("");

		if(in_array($_GET['grpid'], array_keys($iw_cf_groups))) {
			$result = $iw_cf_groups[$_GET['grpid']];
			if(!isset($_GET['callback'])) echo json_encode($result);
			else echo "{$_GET['callback']}(" . json_encode($result) . ")";
		}

		die();
	}	
}

function iw_cf_reposition_groups() {
	if(isset($_POST['position'])) {
		$iw_cf_groups = get_option('iw_cf_groups');
		$i = 0;

		foreach($_POST['position'] as $v) {
			$iw_cf_groups[$v]['order'] = $i++;
		}		

		update_option( 'iw_cf_groups', $iw_cf_groups );
	}	

	die();
}

function iw_cf_save_field() {	
	$iw_cf_fields = get_option('iw_cf_fields');
	if(empty($iw_cf_fields)) $iw_cf_fields = array();

	$_POST['name'] = stripslashes($_POST['name']);
	if(isset($_POST['further']) && !is_array($_POST['further'])) $_POST['further'] == stripslashes($_POST['further']);

	if(isset($_POST['options']) && is_array($_POST['options'])) {
		foreach($_POST['options'] as $k => $v) {
			$_POST['options'][$k] = stripslashes($v);
		}
	}

	if(isset($_POST['id']) && !empty($_POST['id'])) {
		if(count($iw_cf_fields) > 0) {
			foreach($iw_cf_fields as $k => $grp) {
				if($k == $_POST['id']) {
					$_POST['order'] = isset($iw_cf_fields[$_POST['id']]['order']) ? $iw_cf_fields[$_POST['id']]['order'] : $iw_cf_fields[$_POST['id']]['order'];
					$iw_cf_fields[$k] = $_POST;
				}
			}
		} else {
			$_POST['order'] = isset($iw_cf_fields[$_POST['id']]) ? $iw_cf_fields[$_POST['id']]['order'] : $iw_cf_fields[$_POST['id']]['order'];
			$iw_cf_fields[$_POST['id']] = $_POST; 
		}
	} else {
		if(count($iw_cf_fields) > 0) {
			$new_id = max(array_keys($iw_cf_fields)) + 1;
			$_POST['id'] = $new_id;
			$_POST['order'] = $new_id;
			$iw_cf_fields[$new_id] = $_POST; 
		} else {
			$_POST['id'] = 1;
			$_POST['order'] = 1;
			$iw_cf_fields[1] = $_POST; 
		}
	}

	update_option( 'iw_cf_fields', $iw_cf_fields );

	die("ok");

}

function iw_cf_load_field() {
	if(isset($_GET['fieldid']) && $_GET['fieldid'] >0) {
		$iw_cf_fields = get_option('iw_cf_fields');
		if(empty($iw_cf_fields)) die("");

		if(in_array($_GET['fieldid'], array_keys($iw_cf_fields))) {
			$result = $iw_cf_fields[$_GET['fieldid']];
			if(!isset($_GET['callback'])) echo json_encode($result);
			else echo "{$_GET['callback']}(" . json_encode($result) . ")";
		}

		die();
	}	
}

function iw_cf_reposition_fields() {
	if(isset($_POST['position'])) {
		$iw_cf_fields = get_option('iw_cf_fields');
		$i = 0;

		foreach($_POST['position'] as $v) {
			$iw_cf_fields[$v]['order'] = $i++;
		}		

		update_option( 'iw_cf_fields', $iw_cf_fields );
	}	

	die();
}




function iw_ty_save_ov() {
	$iw_ty_ovs = get_option('iw_ty_ovs');
	if(empty($iw_ty_ovs)) $iw_ty_ovs = array();

	if(isset($_POST['id']) && !empty($_POST['id'])) {
		if(count($iw_ty_ovs) > 0) {
			foreach($iw_ty_ovs as $k => $ov) {
				if($k == $_POST['id']) {
					$_POST['order'] = isset($iw_ty_ovs[$_POST['id']]['order']) ? $iw_ty_ovs[$_POST['id']]['order'] : $iw_ty_ovs[$_POST['id']]['order'];
					$iw_ty_ovs[$k] = $_POST;
				}
			}
		} else {
			$_POST['order'] = isset($iw_ty_ovs[$_POST['id']]) ? $iw_ty_ovs[$_POST['id']]['order'] : $iw_ty_ovs[$_POST['id']]['order'];
			$iw_ty_ovs[$_POST['id']] = $_POST; 
		}
	} else {
		if(count($iw_ty_ovs) > 0) {
			$new_id = max(array_keys($iw_ty_ovs)) + 1;
			$_POST['id'] = $new_id;
			$_POST['order'] = $new_id;
			$iw_ty_ovs[$new_id] = $_POST; 
		} else {
			$_POST['id'] = 1;
			$_POST['order'] = 1;
			$iw_ty_ovs[1] = $_POST; 
		}
	}

	update_option( 'iw_ty_ovs', $iw_ty_ovs );
	die("ok");
}


function iw_ty_load_ovs() {
	$iw_ty_ovs = get_option('iw_ty_ovs');

	if(empty($iw_ty_ovs)) die("");
	$sorted_ovs = array();

	foreach($iw_ty_ovs as $k => $v) {
		$sorted_ovs[$iw_ty_ovs[$k]['order']] = $v;
	}
	ksort($sorted_ovs);
	foreach($sorted_ovs as $k => $ov) {
		?>
		<li class="iw_ty_ov_li" ovid="<?php echo $ov['id']; ?>">
			<span class="iw_ty_ov_name"><?php echo strlen($ov['name']) > 35 ? substr($ov['name'], 0, 34) . "..." : $ov['name']; ?>
				<span class="controls">
					<i class="fa fa-pencil ov-edit" ovid="<?php echo $ov['id']; ?>" title="Override Settings"></i>
					<i class="fa fa-times ov-delete" ovid="<?php echo $ov['id']; ?>" title="Delete Override"></i>
				</span>
			</span>
		</li>
		<?php
	}

	die();
}

function iw_ty_load_ov() {
	if(isset($_GET['ovid']) && $_GET['ovid'] >0) {
		$iw_ty_ovs = get_option('iw_ty_ovs');
		if(empty($iw_ty_ovs)) die("");

		if(in_array($_GET['ovid'], array_keys($iw_ty_ovs))) {
			$result = $iw_ty_ovs[$_GET['ovid']];
			if(!isset($_GET['callback'])) echo json_encode($result);
			else echo "{$_GET['callback']}(" . json_encode($result) . ")";
		}

		die();
	}	
}

function iw_ty_reposition_ovs() {
	if(isset($_POST['position'])) {
		$iw_ty_ovs = get_option('iw_ty_ovs');
		$i = 0;

		foreach($_POST['position'] as $v) {
			$iw_ty_ovs[$v]['order'] = $i++;
		}		

		update_option( 'iw_ty_ovs', $iw_ty_ovs );
	}	

	die();
}

function iw_ty_del_ov() {
	if(isset($_POST['ovid']) && $_POST['ovid'] >0) {
		$iw_ty_ovs = get_option('iw_ty_ovs');

		if(empty($iw_ty_ovs)) die("");

		if(in_array($_POST['ovid'], array_keys($iw_ty_ovs)))
			unset($iw_ty_ovs[$_POST['ovid']]);

		update_option( 'iw_ty_ovs', $iw_ty_ovs );
		die();
	}
}
