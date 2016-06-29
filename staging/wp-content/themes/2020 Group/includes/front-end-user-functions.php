<?php
//
// User request development tool file to be downloaded (does not reveal the original file location and checks user permissions etc.)
//
add_action('wp_ajax_download_file', 'download_file');
function download_file() {

	header_remove();

    global $current_user;
	$error = 1;

    // make sure user is logged in
	if (isset($current_user->ID) && $current_user->ID > 0 && isset($_GET['file_id']) && is_numeric($_GET['file_id'])) {

		$post_id = intval($_GET['file_id']);
		$file = get_field('file', $post_id);

		if(isset($file['url'])) {
                    $filename_parts = explode('/',$file['url']);
                    $filename = end($filename_parts);
                    $fileUrl = explode('wp-content/',$file['url']); //Split the file url, we only want it from uploads/...
                    $enviroUrl = explode('themes',dirname(__FILE__)); //Work out the enviroment absolute path and split it as we only want it up to ...wp-content/
                    $fileUrl = $enviroUrl[0].$fileUrl[1]; //Join the 2 urls and supply it to the readfile function

                    if(file_exists($fileUrl)) {
                        unset($error);
                        header('Content-Type: application/octet-stream'); //Force file to be downloaded
                        header("Content-disposition: attachment; filename=\"{$filename}\"");
                        echo readfile($fileUrl);
                        exit;
                    }
		}
	}

	if(isset($error)) {
		//If the user doesn't have access to a file redirect them to the login page
		header('Location: /login/'); exit;
	}
}


//
// User upload new profile image
//
add_action('wp_ajax_update_profile_image', 'update_profile_image');
function update_profile_image() {
	if(isset($_POST['image'])) {
		$data = array('success' => 'Form was submitted', 'image' => $_POST['image']);
		echo json_encode($data);
	}
	else {
		global $wpdb;
		$current_user = wp_get_current_user();

		$image = $_FILES[0];
		$allowed_file_types = array('jpg' =>'image/jpg','jpeg' =>'image/jpeg', 'gif' => 'image/gif', 'png' => 'image/png');

		$overrides = array('test_form' => false, 'mimes' => $allowed_file_types);
		$file = wp_handle_upload($image, $overrides);

		if ($file && !isset( $file['error'] ) ) {
			$image = wp_get_image_editor( $file['file'] );
			if ( ! is_wp_error( $image ) ){
				$size = getimagesize( $file['file'] ); // $size[0] = width, $size[1] = height

				if ( $size[0] > 120 || $size[1] > 120 ){ // if the width or height is larger than the large-size
					$image->resize( 120, 120, true ); // resize the image
					$final_image = $image->save( $file['file'] ); // save the resized image
				}
			}
			update_user_meta($current_user->ID,'user_profile_image',$file['url']); //store the profile image as user meta so it can be referenced later
			$data = array('success' => 'Form was submitted', 'image' => $file['url']);
			echo json_encode($data);
		}
		else {
			echo 'error';
		}
	}
	exit;
}


//
// User permission and access functions
//
function get_group_capabilities_for_user( $user_id = null) {
  if ($user_id == null) {
    $user_id = get_current_user_id();
  }

  global $wpdb;

  $sql = 'SELECT gc.capability FROM '.$wpdb->prefix.'groups_user_group gug
              INNER JOIN '.$wpdb->prefix.'groups_group_capability ggc ON gug.group_id = ggc.group_id
              INNER JOIN '.$wpdb->prefix.'groups_capability gc ON ggc.capability_id = gc.capability_id
              WHERE gug.user_id=%d';

  $capabilities = $wpdb->get_col( $wpdb->prepare($sql, $user_id) );

  return $capabilities;
}


function getChildUserAccounts($user_id, $type=NULL) {

  global $wpdb;

  //If a type is specified we will only get the active users for that type
  if(!is_null($type)) {
	$results = $wpdb->get_results($wpdb->prepare('SELECT `u`.`ID`, `u`.`user_email`,
						`um_first`.`meta_value` as `first_name`,
						`um_last`.`meta_value` as `last_name`,
						`um_acc`.`meta_value` as `account_type`
				  FROM `wp_users` `u`
				  INNER JOIN `wp_usermeta` `um` ON `u`.`ID`=`um`.`user_id`
				  INNER JOIN `wp_usermeta` `um_first` ON `u`.`ID`=`um_first`.`user_id`
				  INNER JOIN `wp_usermeta` `um_last` ON `u`.`ID`=`um_last`.`user_id`
				  INNER JOIN `wp_usermeta` `um_acc` ON `u`.`ID`=`um_acc`.`user_id`
				  WHERE `um`.`meta_key`="2020_parent_account" AND
						  `um`.`meta_value`=%d AND
						  `um_acc`.`meta_value`=%s AND
						  `um_acc`.`meta_key`="2020_account_type" AND
						  `um_first`.`meta_key`="first_name" AND
						  `um_last`.`meta_key`="last_name"
						  AND (SELECT `meta_value` FROM `wp_usermeta` WHERE `user_id` = `u`.`ID` AND `meta_key` = "2020_account_status" LIMIT 1) IS NULL',$user_id,$type));
  }
  else {
	$results = $wpdb->get_results($wpdb->prepare('SELECT `u`.`ID`, `u`.`user_email`,
						`um_first`.`meta_value` as `first_name`,
						`um_last`.`meta_value` as `last_name`,
						`um_acc`.`meta_value` as `account_type`
				  FROM `wp_users` `u`
				  INNER JOIN `wp_usermeta` `um` ON `u`.`ID`=`um`.`user_id`
				  INNER JOIN `wp_usermeta` `um_first` ON `u`.`ID`=`um_first`.`user_id`
				  INNER JOIN `wp_usermeta` `um_last` ON `u`.`ID`=`um_last`.`user_id`
				  INNER JOIN `wp_usermeta` `um_acc` ON `u`.`ID`=`um_acc`.`user_id`
				  WHERE `um`.`meta_key`="2020_parent_account" AND
						  `um`.`meta_value`=%d AND
						  `um_acc`.`meta_key`="2020_account_type" AND
						  `um_first`.`meta_key`="first_name" AND
						  `um_last`.`meta_key`="last_name"',$user_id));
  }

  return $results;

}

// update user from front end from dashboard page - My Personal Details
// TODO: proper data validation, also check if the user is the main account holder or a sub account in which case no address or phone should be editable, feedback.
add_action('wp_ajax_update_user', 'updateUser');
function updateUser() {
  $user_id = get_current_user_id();

  if ($user_id==0) {
    exit;
  }

  $new_details = array();

  $new_details['ID'] = $user_id;

  $first_name = $_POST['change-first-name'];
  $last_name = $_POST['change-last-name'];
  $company_name = $_POST['change-company'];

  $display_name = $first_name.' '.$last_name;
  update_user_meta($user_id,'first_name',$first_name);
  update_user_meta($user_id,'last_name',$last_name);

  $new_details['display_name'] = $display_name;

  update_user_meta($user_id,'billing_first_name',$first_name);
  update_user_meta($user_id,'billing_last_name',$last_name);
  update_user_meta($user_id,'billing_company',$company_name);

  update_user_meta($user_id,'shipping_first_name',$first_name);
  update_user_meta($user_id,'shipping_last_name',$last_name);

  $email = $_POST['change-email'];
  update_user_meta($user_id,'billing_email',$email);

  $new_details['user_email'] = $email;

  $phone = $_POST['change-phone'];
  update_user_meta($user_id,'billing_phone',$phone);

  $pass = $_POST['change-password'];

  if ($pass != '') {
    $new_details['user_pass'] = $pass;
  }

  $line1 = $_POST['change-line-1'];
  update_user_meta($user_id,'billing_address_1',$line1);
  update_user_meta($user_id,'shipping_address_1',$line1);

  $line2 = $_POST['change-line-2'];
  update_user_meta($user_id,'billing_address_2',$line2);
  update_user_meta($user_id,'shipping_address_2',$line2);

  $city = $_POST['change-city'];
  update_user_meta($user_id,'billing_city',$city);
  update_user_meta($user_id,'shipping_city',$city);

  $state = $_POST['change-county'];
  update_user_meta($user_id,'billing_state',$state);
  update_user_meta($user_id,'shipping_state',$state);

  $postcode = $_POST['change-postcode'];
  update_user_meta($user_id,'billing_postcode',$postcode);
  update_user_meta($user_id,'shipping_postcode',$postcode);


  wp_update_user($new_details);

  // merge conflict. this was on staging
  //do_action('woocommerce_customer_save_address', $user_id, true);
  // this came from 86-triangle-fix
  update_user_on_infusionsoft($user_id);

  $user = get_user_by( 'id', $user_id );

  if( $user ) {
      wp_set_current_user( $user_id, $user->user_login );
      wp_set_auth_cookie( $user_id );
      do_action( 'wp_login', $user->user_login );
  }


  exit;
}

//
// CREATE A NEW SUB USER
//
add_action('wp_ajax_create_user', 'createUser');
function createUser() {



  $parent_user_id = get_current_user_id();

  if ($parent_user_id==0) {
    exit;
  }

  if ($parent_user_id > 0 ) {
	if($_POST['account_type'] == 'partner') {
		$allowedPartners = getAllowedNumberOfPartners($parent_user_id);
		if($allowedPartners == '0') {
			echo 'nopartners';
			exit;
		}
	}

    $email_address = $_POST['new_email'];
    $first_name    = $_POST['new_first_name'];
    $last_name     = $_POST['new_last_name'];
    $type          = $_POST['account_type'];

    if($type == null) {
       $type = 'Standard';
    }

    if( username_exists( $email_address ) == null) {

      // Generate the password and create the user
      $password = wp_generate_password( 12, false );
      $user_id = wp_create_user( $email_address, $password, $email_address );

      // Set the nickname
      wp_update_user(
        array(
          'ID'          =>    $user_id,
          'nickname'    =>    $email_address
        )
      );

      // Set the role
      $user = new WP_User( $user_id );
      $user->set_role( 'subscriber' );

      $userWelcomeSubject = 'Welcome!';
      $userWelcomeContent1 = file_get_contents(get_theme_root().'/2020 Group/includes/emails/email-header.php');
      $userWelcomeContent1 .= '<p>Thank you for registering on the 2020 website.  You will find your new password details below.<p>';
      $userWelcomeContent2 = '<p>To login to the site visit <a href="http://www.the2020group.com/login/">www.the2020group.com/login/</a>, enter your email address and your new password details in the appropriate fields. Once you have successfully logged in you can change your password in My2020Dashboard.</p>
                            <p>If you require further assistance please call the main 2020 office on +44(0) 121 314 2020 or email <a href="mailto:admin@the2020group.com">admin@the2020group.com</a>.</p>
                            <p>Best wishes<br />2020 Innovation</p>';
      $userWelcomeFooter = file_get_contents(get_theme_root().'/2020 Group/includes/emails/email-footer.php');

      $headers = array('Content-Type: text/html; charset=UTF-8');

      // Email the user
      wp_mail( $email_address, $userWelcomeSubject, $userWelcomeContent1 . '<p>Email Address: <strong>' . $email_address . '</strong><br />Password: <strong>' . $password . '</strong></p>' . $userWelcomeContent2 . $userWelcomeFooter, $headers );

      update_user_meta($user_id,'2020_parent_account',$parent_user_id);
      update_user_meta($user_id,'2020_account_type',$type);
      update_user_meta($user_id,'first_name',$first_name);
      update_user_meta($user_id,'last_name',$last_name);

      switch ($type) {

        case 'Partner':
          Groups_User_Group::create( array( 'user_id' => $user_id, 'group_id' => 3) );
          break;
        case 'Employee':
          Groups_User_Group::create( array( 'user_id' => $user_id, 'group_id' => 2 ) );
          break;
        default:
          Groups_User_Group::create( array( 'user_id' => $user_id, 'group_id' => 9 ) );
          break;

      }

      // update data on infuseionsoft for this child user.
      manageChildUsersInInfusionsoft($user_id);

      echo 'true';

    } // end if
    else {
      echo 'false';
    }
    exit;
  }
}

add_action('wp_ajax_international', 'manageDirectoryListing');
function manageDirectoryListing() {
  $user_id = get_current_user_id();

  if ($user_id==0) {
    exit;
  }

  $contact_name = $_POST['change-intl_contact_name'];
  $company_name = $_POST['change-intl_company_name'];
  $address      = $_POST['change-intl_address'];
  $postcode     = $_POST['change-intl_postcode'];
  $country      = $_POST['change-intl_country'];
  $phone        = $_POST['change-intl_phone'];
  $fax          = $_POST['change-intl_fax'];
  $email        = $_POST['change-intl_email'];
  $web          = $_POST['change-intl_web'];
  $cat          = $_POST['change-intl_cat'];

  $intl_continent   = $_POST['intl_continent'];
  $intl_country     = $_POST['intl_country'];
  $intl_specialisms = $_POST['intl_specialisms'];

  $entry = get_posts(array('post_type'=>'directory','author'=>$user_id));

  if (count($entry)==0) {
    $new = true;
  }
  else {
    $new = false;
  }

  if ($new) {
    // add cpd entry
    $post_id = wp_insert_post(
                                array(
                                  'comment_status' => 'closed',
                                  'ping_status'    => 'closed',
                                  'post_author'    => $user_id,
                                  'post_title'     => $company_name,
                                  'post_status'    => 'publish',
                                  'post_type'      => 'directory'
                                )
                              );


  }
  else {

    wp_update_post(array('ID'=>$entry[0]->ID,'post_title'=>$company_name));

    $post_id = $entry[0]->ID;

  }


  if(!empty($intl_continent)) {
	wp_set_post_terms( $post_id, array($intl_continent,$intl_country), 'location');
  }
  else {
	wp_set_post_terms( $post_id, '', 'location');
  }

  if(!empty($intl_specialisms)) {
	wp_set_post_terms( $post_id, $intl_specialisms, 'directory_cat');
	$intl_specialismsHtml = '<ul>';
	foreach($intl_specialisms as $intl_specialism) {
		$intl_specialism = get_term($intl_specialism, 'directory_cat');
		$intl_specialismsHtml .= '<li>'.$intl_specialism->name.'</li>';
	}
	$intl_specialismsHtml .= '</ul>';
	update_field('field_54d0d5df99e5a',$intl_specialismsHtml,$post_id);
  }
  else {
	wp_set_post_terms( $post_id, '', 'directory_cat');
	update_field('field_54d0d5df99e5a','',$post_id);
  }

  update_field('field_5464d7da1d53f',$contact_name,$post_id);
  update_field('field_5464d85e1d541',$address,$post_id);
  update_field('field_5464d8731d542',$postcode,$post_id);
  update_field('field_5465f559bd27d',$country,$post_id);
  update_field('field_5464d8861d543',$phone,$post_id);
  update_field('field_5464d8901d544',$fax,$post_id);
  update_field('field_5464d8a31d545',$email,$post_id);
  update_field('field_5464d8b31d546',$web,$post_id);

  exit;
}


function user_has_international_subscription($user_id) {
    return WC_Subscriptions_Manager::user_has_subscription( (int)$user_id, 313, 'active' );
}

function getAllowedNumberOfPartners($user_id) {
	$partners = 0;
  global $_subscription_details;

	//How many partners are you allowed?
	$subscriptions = $_subscription_details;
	foreach($subscriptions as $subscription) {
		if($subscription['product_id'] == 48) {
			$variation = new WC_Product_Variation($subscription['variation_id']);
			$variationAttr = $variation->get_variation_attributes();
			if(isset($variationAttr['attribute_pa_partners'])) {
				$partners = explode('-part',$variationAttr['attribute_pa_partners']);
				$partners = explode('-',$partners[0]);
				$partners = end($partners);
			}
			break;
		}
	}

	if($partners !== 0) {
		//How many partners in use?
		$results = getChildUserAccounts($user_id, 'Partner');
		if(!empty($results)) {
			$usedPartners = count($results);
			$partners = $partners-$usedPartners;
			if($partners<0) $partners = 0;
		}
	}
	else {
		//How many partners in use?
		$results = getChildUserAccounts($user_id, 'Partner');
		if(empty($results)) $partners = 1;
	}

	return $partners;
}

// if you are child user check that your account hasn't been disabled or the master account subscription hasn't expired
function check_child_account_status($user) {

    $user = (int)$user;
    //check if the user account is a child user
    $parent_user = get_user_meta($user,'2020_parent_account',true);

    if ($parent_user!='') {

      // check if child account has been locked
      $account_status = get_user_meta($user,'2020_account_status',true);

      if ($account_status=='true') {
        // if so then
        return false;
      }

    }
    return true;
}

// When a user's group changes, check that they are still allowed to have
// children, and deactivate the children if not.
add_action('groups_created_user_group','remove_ineligible_children');
function remove_ineligible_children($user_id) {
  if(!eligible_for_children($user_id)) {
    $children = getChildUserAccounts($user_id);

    foreach($children as $child) {
      update_user_meta($child->ID, '2020_account_status', 'true');
    }

    return true;
  } else {
    return false;
  }
}

function eligible_for_children($user_id) {
  $user = new Groups_User($user_id);
  $groups = $user->__get('groups');

  $group_blacklist = array(5,2,9);
  $group_ids = array();
  if (is_array($groups)) {
    foreach($groups as $g) {
      $group_ids[] = (int) $g->group->group_id;
    }
  }

  if(array_intersect($group_ids, $group_blacklist) != null) {
    // User is not allowed to have child users.
    return false;
  } else {
    return true;
  }
}


//
// REDIRECT AFTER LOGIN
//

add_filter('woocommerce_login_redirect', 'custom_login_redirect',10,2);
function custom_login_redirect( $redirect_to,$user ) {

  if(isset($_POST['_wp_http_referer'])) {
    $_GET['ref'] = $_POST['_wp_http_referer'];

    if ( strstr($_GET['ref'],'ref=')) {
      $parts = explode('ref=', $_GET['ref']);
      $part = explode('&',$parts[1]);
      $_GET['ref'] = urldecode($part[0]);
    }
  }

  if (check_child_account_status($user->ID)) {
    if(isset($_GET['ref'])) {
      return ($_GET['ref']);
    }
    else {
      return '/dashboard/';
    }
  }
  else {
    return '/logout';
  }
}

add_action('wp_logout','go_home');
function go_home(){
  wp_redirect( '/login' );
  exit();
}

// will create or update child user accounts on infusion soft
function manageChildUsersInInfusionsoft($user_id) {

  global $iwpro;

  // check if infusedwoo is connected
  if ( $iwpro->ia_app_connect() ) {

      // get master user account for user id
      $master_account = get_user_meta($user_id,'2020_parent_account',true);

      // if  there is no masteruseraccount then we don't need to do anything.
      if (!empty($master_account)) {

          // get the users data
          $user_data = get_userdata($user_id);

          // find the contact by email on infusionsoft
          $email = $user_data->user_email;
          $contact = $iwpro->app->dsFind('Contact',5,0,'Email',$email,array('Id'));
          $contact = $contact[0];

          // if contact was found store contact id
          if ($contact['Id'] != null && $contact['Id'] != 0 && $contact != false){
              $contactId = (int) $contact['Id'];
          }

          // if contact wasn't found add the user to infusion soft ( only email address )
          else {
              $contactinfo    = array();
              $contactinfo['Email'] = $email;
              $contactId  = $iwpro->app->addCon($contactinfo);
          }

          // look for billing company of master user accouns
          $b_company = stripslashes(get_user_meta($master_account,'billing_company',true));

          // find the company by name
          $company        = $iwpro->app->dsFind('Company',5,0,'Company',$b_company,array('Id'));
          $company        = $company[0];

          // if company exists remember company id
          if ($company['Id'] != null && $company['Id'] != 0 && $company != false){
              $compId = $company['Id'];
          }
          // if company isn't available create if from name
          else {
              $companyinfo = array('Company' => $b_company);
              $compId = $iwpro->app->dsAdd("Company", $companyinfo);
          }

          // create array of userdetails to be sent to infusionsoft
          $contactinfo = array(
              'FirstName'         => stripslashes(get_user_meta($user_id,'first_name',true)),
              'LastName'          => stripslashes(get_user_meta($user_id,'last_name',true)),
              'StreetAddress1'    => stripslashes(get_user_meta($master_account,'billing_address_1',true)),
              'StreetAddress2'    => stripslashes(get_user_meta($master_account,'billing_address_2',true)),
              'City'              => stripslashes(get_user_meta($master_account,'billing_city',true)),
              'State'             => stripslashes(get_user_meta($master_account,'billing_state',true)),
              'Country'           => stripslashes(get_user_meta($master_account,'billing_country',true)),
              'PostalCode'        => stripslashes(get_user_meta($master_account,'billing_postcode',true)),
              'CompanyID'         => $compId,
              'Company'           => $b_company,
              'ContactType'       => 'Customer'
          );

          // if infusedwoo has been set to overwrite details update details in infusionsoft
          if($iwpro->overwriteBD != "yes") {
              $iwpro->app->dsUpdate("Contact",$contactId,$contactinfo);
          }
      }
  }
}

//ADDED BY LOL----------------/////////

//make XML from multi array
function convertArrayToXML($array, $root, $element) {

    $output = new SimpleXMLElement("<{$root}></{$root}>");

    foreach ($array as $key => $value) {
      array_to_xml($array, $output, $element);
    }

    return $output->asXML();
}

function array_to_xml($array, $xml, $baseChild = 'child') {
  foreach ($array as $key => $value) {
    if (is_numeric($key)) {
      // We are a an element without a key name - use the base child instead.
      $child = $xml->addChild($baseChild);
      array_to_xml($value, $child);
    } elseif (is_array($value)) {
      $child = $xml->addChild($key);
      array_to_xml($value, $child);
    } else {
      // We're a string - add this with no further processing.
      $xml->{$key} = $value;
    }
  }
}

//ensure XML valid
function xml_entities($text, $charset = 'UTF-8') {
    // encode html characters that are also invalid in xml
    $text = htmlentities($text, ENT_COMPAT, $charset, false);

    // XML character entity array from Wiki
    $arr_xml_special_char = array("&quot;", "&amp;", "&apos;", "&lt;", "&gt;");

    // Building the regex string to exclude all strings with xml special char
    $arr_xml_special_char_regex = "(?";
    foreach ($arr_xml_special_char as $key => $value) {
        $arr_xml_special_char_regex .= "(?!$value)";
    }
    $arr_xml_special_char_regex .= ")";

    $pattern = "/$arr_xml_special_char_regex&([a-zA-Z0-9]+;)/";

    $replacement = '&amp;${1}';
    return preg_replace($pattern, $replacement, $text);
}

//get details of users who have updated contact in dashboard details
//also returns total users
function get_updated_users($count=FALSE){

	//select users where meta key 'last_updated' is within last 2 hours
	$args = array(
	'meta_key' => 'last_updated',
    'meta_query' => array(
        array(
            'key' => 'last_updated',
            'value' => date('Y-m-d', strtotime('-2 hours')) ,
            'compare' => '>=',
            'type' => 'DATE'
        )
    ));

	$wp_user_query = new WP_User_Query( $args );

	if($count){

		$total_users = $wp_user_query->get_total();

		return $total_users;

	} else {

		$users = $wp_user_query->get_results();

		return $users;
	}
}


//prepare user details to pass to xero via API
function get_contact_details(){

	$users = get_updated_users();

	if (!empty($users)) {
		foreach ($users as $user){

			$user_meta = get_user_meta($user->ID);
      $contact_id = stripslashes(get_user_meta($user->ID,'xero_contact_id',true));

      if(!$contact_id) {
        $contact_id = add_xero_contact_id($user->ID, $user->email);
      }

			//create multi array
			$users_contact_info[$user->ID] = array(
        'ContactID' => stripslashes(get_user_meta($user->ID,'xero_contact_id',true)),
        'Name' => stripslashes(get_user_meta($user->ID,'billing_company',true)),
        'FirstName' => stripslashes(get_user_meta($user->ID,'first_name',true)),
        'LastName' => stripslashes(get_user_meta($user->ID,'billing_last_name',true)),
        'EmailAddress'=> stripslashes(get_user_meta($user->ID,'billing_email',true)),
        'Addresses' => array(
          'Address'	=> array(
            'AddressType' => 'POBOX',
            'AddressLine1' => stripslashes(get_user_meta($user->ID,'billing_address_1',true)),
            'AddressLine2' => stripslashes(get_user_meta($user->ID,'billing_address_2',true)),
            'City' => stripslashes(get_user_meta($user->ID,'billing_city',true)),
            'Region' => stripslashes(get_user_meta($user->ID,'billing_state',true)),
            'PostalCode' => stripslashes(get_user_meta($user->ID,'billing_postcode',true)),
            'Country' => stripslashes(get_user_meta($user->ID,'billing_country',true))
          ),
        ),
        'Phones' => array(
          'Phone' => array(
            'PhoneNumber' => stripslashes(get_user_meta($user->ID,'billing_phone',true))
          )
        )
      );
		}

		return $users_contact_info;
	} else {
		echo 'No user details have been updated within last 24 hours.';
	}
}


//add date / time to user meta data when contact details are updated
add_action( 'updated_user_meta', 'record_updated_user_data' );
function record_updated_user_data(){

	$user_id = get_current_user_id();

	$datetime = date('Y-m-d H:i:s');

	$update = update_user_meta( $user_id, 'last_updated', $datetime );

	return $update;
}


// add Xero ContactID and add to meta data when new user registers
//add_action( 'woocommerce_order_status_processing', 'add_new_user_xero_contact_id' );
function add_new_user_xero_contact_id(){

		$user_id = get_current_user_id();
		$user_info = get_userdata($user_id);
		$user_email = $user_info->user_email;
		$update_contactID = add_xero_contact_id($user_id, $user_email);

		return $update_contactID;

}

//get contactID from xero API for user and add to meta data
function add_xero_contact_id($user_id, $user_email){
	//get xero contact id for user email
  include_once ('class-xero-contacts.php');
  $xero_api = new Xero_Update_Contact_Details();
	$contact_id = (string) $xero_api->getContactID($user_email);

	if($contact_id !=''){
		//add xero contact id to user meta data
		$update_contactID = update_user_meta( $user_id, 'xero_contact_id', $contact_id );
		if($update_contactID){
			return $contact_id;
		}
  }
}

// Make post_contacts_to_xero run once an hour.
if (!wp_next_scheduled('update_xero_contacts')) {
	wp_schedule_event(time(), 'hourly', 'update_xero_contacts');
}
add_action('update_xero_contacts', 'post_contacts_to_xero');

//post contact details to xero API, to be executed via daily cron job
function post_contacts_to_xero(){

    include ('class-xero-contacts.php');

    $total_users = get_updated_users(TRUE);
    $batch_limit = 50;
    for ($i = 0; $i < $total_users; $i += $batch_limit) {
      $users_contact_info = get_contact_details();

      //convert multi array to XML
      $contacts_xml = convertArrayToXML($users_contact_info, 'contacts', 'contact');

      //pass to API class
      $xeroAPI = new Xero_Update_Contact_Details();
      $xeroAPI->updateContacts($contacts_xml);
    }
}

//get contactID from xero and add to meta data for all users
//only needs running once - to run just visit:  http://www.the2020group.com/?getcontactids=1
//add_action( 'init', 'add_xero_contact_ids' );
function add_xero_contact_ids(){
	if(isset($_GET['getcontactids'])){
		//connect to xero API
		include_once ('class-xero-contacts.php');

		$startTime = time();

		$batch = 100;

		$total_users = count_users();

		$user_args = array(
		'number'  => $batch,
		'offset' => $i,
		'meta_key' => 'xero_contact_id',
		'meta_compare' => 'NOT EXISTS' );

		$count = 0;
		for ($i = 0; $i < $total_users; $i += $batch) {
			$all_users =  get_users($user_args);

			foreach ($all_users as $user){
				$user_id = $user->ID;
				$user_email = $user->user_email;

				$update_contactID = add_xero_contact_id($user_id, $user_email);

		  }
			sleep(1);  //use this to stop it going over 60 requests/min rate limit.
			$count++;
		}

		$endTime = time();
		echo "Total time to generate results: ".($endTime - $startTime)." seconds.\n";
	}
}

function validate_direct_debit() {
  $debitGateway = new WC_SmartDebit_Gateway;

  $current_user = wp_get_current_user();

  $user_id = $current_user->ID;

  list($account_holder, $account_number, $sort_code) = $debitGateway->validatePostedSmartDebitFields();

  if ( wc_notice_count('error') > 0 ) {
    ob_start();
    wc_print_notices();
    $messages = ob_get_clean();

    echo '<!--WC_START-->' . json_encode(
      array(
        'result'	=> 'failure',
        'messages' 	=> isset( $messages ) ? $messages : '',
        'refresh' 	=> false,
        'reload'    => false
      )
    ) . '<!--WC_END-->';

    exit;
  }

  $args = array(
    'variable_ddi[reference_number]'     => rand(0,9999999) . $debitGateway->getInfusionSoftReference($user_id),
    'variable_ddi[sort_code]'            => $sort_code,
    'variable_ddi[account_number]'       => $account_number,
    'variable_ddi[account_name]'         => $account_holder,
    'variable_ddi[first_name]'           => $_POST['billing_first_name'],
    'variable_ddi[last_name]'            => $_POST['billing_last_name'],
    'variable_ddi[address_1]'            => $_POST['billing_address_1'],
    'variable_ddi[address_2]'            => $_POST['billing_address_2'],
    'variable_ddi[town]'                 => $_POST['billing_city'],
    'variable_ddi[county]'               => $_POST['billing_state'],
    'variable_ddi[postcode]'             => $_POST['billing_postcode'],
    'variable_ddi[country]'              => $_POST['billing_country'],
    'variable_ddi[service_user][pslid]'  => $debitGateway->get_option('api_pslid'),
    'variable_ddi[frequency_type]'       => 'M',
    'variable_ddi[company_name]'         => $_POST['billing_company'],
    'variable_ddi[email_address]'        => $_POST['billing_email'],
    'variable_ddi[default_amount]'       => $payment_plan[1][1]*100,
    'variable_ddi[first_amount]'         => $payment_plan[1][0]*100
  );

  $valid = $debitGateway->makeCall('/api/ddi/variable/validate', $args);

  die($valid);
}

add_action('wp_ajax_validate_ddi', 'validate_direct_debit');
add_action('wp_ajax_nopriv_validate_ddi', 'validate_direct_debit');


//
// Ajax login for Benchmark tool
//
//user login
add_action('wp_ajax_benchmark_login', 'benchmark_login');
add_action('wp_ajax_nopriv_benchmark_login', 'benchmark_login');
function benchmark_login() {

  $response = array();

  if(isset($_POST['email']) && $_POST['email'] != '' && isset($_POST['password'])  && $_POST['password'] != '') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user = get_user_by('email', $email);

    if(!empty($user)) {

      //Log the user in
      $user = wp_signon(array('user_login' => $user->data->user_login, 'user_password' => $password));
      if(is_wp_error($user)) {
        $response['status'] = 'error';
        $response['message'] = 'Login was unsuccessful. Please check your details and try again.';
      }
      else {
        wp_set_current_user( $user->ID );
        $response['status'] = 'success';
        $response['firstname'] = get_user_meta($user->ID,'first_name',true);
        $response['lastname'] = get_user_meta($user->ID,'last_name',true);
        $response['email'] = $_POST['email'];
        $response['company'] = get_user_meta($user->ID,'billing_company',true);
        $response['address'] = get_user_meta($user->ID,'billing_address_1',true);
        $response['city'] = get_user_meta($user->ID,'billing_city',true);
        $response['postcode'] = get_user_meta($user->ID,'billing_postcode',true);
        $response['message'] = '';
      }
    }
    else {
      $response['status'] = 'error';
      $response['message'] = 'Login was unsuccessful. Please check your details and try again.';
    }
  } else {
    $response['status'] = 'error';
    $response['message'] = 'Login was unsuccessful. Please check your details and try again.';
  }

  echo json_encode($response);
  exit;
}
