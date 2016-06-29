<?php

/*
Plugin Name: WooCommerce Adobe Connect connector plugin.
Plugin URI: http://relit.ca
Description: WooCommerce Adobe Connect connector plugin.
Version: 999 Custom Version From F10
Author: Relit.Ca
Author URI: http://relit.ca
*/

include('woocommerce-adobeconnect-admin-settings.php');
include('woocommerce-adobeconnect-adobe.php');
$settings = new WooCom_AdobeConnect_Settings( __FILE__ );

define('USERNAME', get_option($settings->settings_base.'login_field'));
define('PASSWORD', get_option($settings->settings_base.'password_field'));
define('BASE_DOMAIN', get_option($settings->settings_base.'domain_field'));
define('FOLDER_ID', get_option($settings->settings_base.'folder_field'));
define('PREFIX', get_option($settings->settings_base.'prefix_field'));
define('NOTIFICATION', (get_option($settings->settings_base.'enable_email_notification_checkbox') == "on" ? 'enable' : null));

function wcac_action($product_id,$user_id) {

    //connect to adobe
	$acc = new AdobeConnectClient_(USERNAME, PASSWORD, BASE, FOLDER);


 /* $parent = get_user_meta($user_id,'2020_parent_account',true);
  if ($parent) {
    $master_account = $parent;
  }
  else {
    $master_account = $user_id;
  }

  // get user details
  $fname= get_user_meta($user_id, 'first_name', true);
  $lname= get_user_meta($user_id, 'last_name', true);
  $user = get_user_by( 'id', $user_id );
  $email= $user->user_email;

  $company = get_user_meta($master_account,'billing_company',true);
  $address1 = get_user_meta($master_account,'billing_address_1',true);
  $address2 = get_user_meta($master_account,'billing_address_2',true);
  $city = get_user_meta($master_account,'billing_city',true);
  $state = get_user_meta($master_account,'billing_state',true);
  $postcode = get_user_meta($master_account,'billing_postcode',true);
  $country = get_user_meta($master_account,'billing_country',true);
  $phone = get_user_meta($master_account,'billing_phone',true);

  if(isset($a_userid['report-bulk-users']['row']['@attributes']['principal-id'])) {
    $principal_id = $a_userid['report-bulk-users']['row']['@attributes']['principal-id'];
  }

  if ($principal_id){
    $passw = get_user_meta($user_id,'ac_pass',true);
  } else {
    $passw = randomPassword();
    update_user_meta($user_id,'ac_pass',$passw);
  }

  $meeting_id = get_post_meta($product_id, 'wcac_meeting', true);
  $acc_result_TEMP = $acc->eventRegister(
    array('sco-id'=>$meeting_id,'login'=>$email,'password'=>$passw,'password-verify'=>$passw,'first-name'=>$fname,'last-name'=>$lname),
    $company,
    $postcode
  );
  error_log(print_r($acc_result_TEMP,1));
*/
    $parent = get_user_meta($user_id,'2020_parent_account',true);
    if ($parent) {
        $master_account = $parent;
    }
    else {
        $master_account = $user_id;
    }

    // get user details
    $fname= get_user_meta($user_id, 'first_name', true);
    $lname= get_user_meta($user_id, 'last_name', true);
    $user = get_user_by( 'id', $user_id );
    $email= $user->user_email;

    $company = get_user_meta($master_account,'billing_company',true);
    $address1 = get_user_meta($master_account,'billing_address_1',true);
    $address2 = get_user_meta($master_account,'billing_address_2',true);
    $city = get_user_meta($master_account,'billing_city',true);
    $state = get_user_meta($master_account,'billing_state',true);
    $postcode = get_user_meta($master_account,'billing_postcode',true);
    $country = get_user_meta($master_account,'billing_country',true);
    $phone = get_user_meta($master_account,'billing_phone',true);

    // see if the user exists on AC
    $a_userid = $acc->getUserByEmail($email);

    // if not create the account
    //if (!array_key_exists_r('principal-id',$a_userid)){
    if (empty($a_userid['principal-list'])){

        $passw = randomPassword();
        $newuserid = $acc->createUser($email, $passw, $fname, $lname, 'guest', PREFIX, NOTIFICATION);

        if (isset($newuserid['principal']['@attributes']['principal-id'])) {
            $u_id = $newuserid['principal']['@attributes']['principal-id'];


                //doesn't work for guests only for users

                //$res = $acc->addDetails($u_id,'x-1337292816',$company);
                //$res = $acc->addDetails($u_id,'x-1337326180',$address1);
                //$res = $acc->addDetails($u_id,'x-1337326183',$address2);
                //$res = $acc->addDetails($u_id,'x-1337278710',$city);
                //$res = $acc->addDetails($u_id,'x-1337278713',$state);
                //$res = $acc->addDetails($u_id,'x-1337278717',$postcode);
                //$res = $acc->addDetails($u_id,'x-1337278718',$country);
                //$res = $acc->addDetails($u_id,'x-1337333935',$phone);



            update_user_meta($user_id,'ac_pass',$passw);

        } else {
            $passw = get_user_meta($user_id,'ac_pass',true);
        }

    }
    else {

        $passw = get_user_meta($user_id,'ac_pass',true);
    }


    $meeting_id = get_post_meta($product_id, 'wcac_meeting', true);

    // works but doesn't trigger email notifications
    $acc->inviteUserToMeeting($meeting_id, $email);

    $acc_result_TEMP = $acc->eventRegister(array('sco-id'=>$meeting_id,'login'=>$email,'password'=>$passw,'password-verify'=>$passw,'first-name'=>$fname,'last-name'=>$lname),
    $company,
    $postcode);


}

//Add the field to order emails
//add_filter('woocommerce_email_order_meta_keys', 'my_woocommerce_email_order_meta_keys');
function my_woocommerce_email_order_meta_keys( $keys ) {
	$keys['Adobe Connect User Note'] = 'ac_user_note';
	$keys['Adobe Connect User Login'] = 'ac_user_login';
	$keys['Adobe Connect User Password'] = 'ac_user_pw';
	$keys['Adobe Connect Link to an event'] = 'ac_user_link';
	return $keys;
}

//see if the user exists
function array_key_exists_r($needle, $haystack){
	if (is_array($haystack) || is_object($haystack))
		$result = array_key_exists($needle, $haystack);
	else
		return false;
    if ($result)
        return $result;
    foreach ($haystack as $v)
    {
        if (is_array($v) || is_object($v))
            $result = array_key_exists_r($needle, $v);
        if ($result)
        return $result;
    }
    return $result;
}

//custom error logging function
function log_error($var){
global $settings;
$directory = trailingslashit($settings->errlog_dir);
$filename = get_option($settings->settings_base.'errorlog_name_field');

	if (get_option($settings->settings_base.'enable_errorlog_checkbox') == 'on') {
		if (get_option($settings->settings_base.'errorlog_name_field') && get_option($settings->settings_base.'errorlog_name_field') != ''){
			// error_log(date("Y-m-d H:i:s").': '.$var."\r\n", 3, $directory.$filename);
		} else {
			//error_log(date("Y-m-d H:i:s").': '.$var."\r\n", 3, $directory.'error_log.txt');
		}
	}
}

//Custom Tabs for Product Display. Compatible with WooCommerce 2.0+ only! Outputs an extra tab to the default set of info tabs on the single product page.
function wcac_custom_tab_options_tab() {
	global $settings; ?>
	<li class="linked_product_options linked_product_tab"><a href="#custom_tab_data"><?php echo get_option($settings->settings_base.'WC_tab_name_field'); ?></a></li><?php
}
if (get_option($settings->settings_base.'enable_WC_checkbox') == 'on')
add_action('woocommerce_product_write_panel_tabs', 'wcac_custom_tab_options_tab');

//Custom Tab Options. Provides the input fields and add/remove buttons for custom tabs on the single product page.
function wcac_custom_tab_options() {
        global $post, $meeting_link;
        $custom_tab_options = array(
                'title' => get_post_meta($post->ID, 'wcac_custom_tab_title', true),
                'content' => get_post_meta($post->ID, 'wcac_custom_tab_content', true),
				'wcac_meeting' => get_post_meta($post->ID, 'wcac_meeting', true),
				'wcac_custom_tab_link_to_ac_enabled' => get_post_meta($post->ID, 'wcac_custom_tab_link_to_ac_enabled', true),
        );
?>
        <div id="custom_tab_data" class="panel woocommerce_options_panel">
				<div class="options_group">
                        <p class="form-field">
                                <?php woocommerce_wp_checkbox( array( 'id' => 'wcac_custom_tab_enabled', 'label' => __('Enable Additional Tab?', 'wcac_plugin_text'), 'description' => __('Enable this option to enable the tab on the frontend.', 'wcac_plugin_text') ) ); ?>
                        </p>
                </div>

                <div class="options_group custom_tab_options">
                        <p class="form-field">
                                <label><?php _e('Tab Title:', 'wcac_plugin_text'); ?></label>
                                <input type="text" size="5" name="wcac_custom_tab_title" value="<?php echo @$custom_tab_options['title']; ?>" placeholder="<?php _e('Enter your tab title', 'wcac_plugin_text'); ?>" />
                        </p>

                        <p class="form-field">
                                <label><?php _e('Tab content', 'wcac_plugin_text'); ?></label>
                                <textarea class="theEditor" rows="10" cols="40" name="wcac_custom_tab_content" placeholder="<?php _e('Enter your custom tab content', 'wcac_plugin_text'); ?>"><?php echo @$custom_tab_options['content']; ?></textarea>
                        </p>

						<p class="form-field">
								<label><?php _e('Select a meeting:', 'wcac_plugin_text'); ?></label>
								<?php

								//connect to adobe
								$acc = new AdobeConnectClient_(USERNAME, PASSWORD, BASE, FOLDER);
								//$m_result = $acc->getAllAvailableMeetings();
                                $m_result = $acc->getAllContent();

								echo '<select id="wcac_meeting" name="wcac_meeting">
										<option value=""></option>';
							    if (isset($m_result) && count($m_result)>0) {
    								foreach ($m_result as $key => $value){
    									//this works
    									//log_error('Meeting ID: '.$m_result[$key]['@attributes']['sco-id'].' Meeting name: '.$m_result[$key]['name'].' Meeting URL: '.$m_result[$key]['url'].'Ends: '.$m_result[$key]['date-end'].' Description: '.$m_result[$key]['description']);
                                        $meeting_link = BASE_DOMAIN.$m_result[$key]['url-path'];
                                        $sco_id = $m_result[$key]['sco-id'];
    									if (isset($custom_tab_options['wcac_meeting'])) {

    										if($custom_tab_options['wcac_meeting'] == $sco_id){
    											$selected = 'selected="selected"';
    											$meeting_link = BASE_DOMAIN.$m_result[$key]['url-path'];
    											//log_error($meeting_link);
    										} else {
    											$selected = '';
    									   }
    								    }

    								    if ($m_result[$key]['@attributes']['sco-id'])
    								        echo '<option value="'.$sco_id.'" '.$selected.'>'.$m_result[$key]['name'].' (link: '.$m_result[$key]['url-path'].')</option>';
    								}
                                }
								echo '</select>';
								echo '<input type="hidden" class="input-hidden" name="wcac_meeting_link" id="wcac_meeting_link" value="'.$meeting_link.'" />';
								?>
						</p>
						<p class="form-field">
                                <?php woocommerce_wp_checkbox( array( 'id' => 'wcac_custom_tab_link_to_ac_enabled', 'label' => __('Enable Link to a meeting?', 'wcac_plugin_text'), 'description' => __('Enable this option to enable the link to your meeting selected above on the tab on the frontend.', 'wcac_plugin_text') ) ); ?>
                        </p>
        </div>
        </div>
<?php
}
if (get_option($settings->settings_base.'enable_WC_checkbox') == 'on')
add_action('woocommerce_product_write_panels', 'wcac_custom_tab_options');

// Process meta. Processes the custom tab options when a post is saved
function process_product_meta_custom_tab( $post_id ) {
        update_post_meta( $post_id, 'wcac_custom_tab_enabled', ( isset($_POST['wcac_custom_tab_enabled']) && $_POST['wcac_custom_tab_enabled'] ) ? 'yes' : 'no' );
        update_post_meta( $post_id, 'wcac_custom_tab_title', $_POST['wcac_custom_tab_title']);
        update_post_meta( $post_id, 'wcac_custom_tab_content', $_POST['wcac_custom_tab_content']);
		update_post_meta( $post_id, 'wcac_meeting', $_POST['wcac_meeting']);
		update_post_meta( $post_id, 'wcac_custom_tab_link_to_ac_enabled', ( isset($_POST['wcac_custom_tab_link_to_ac_enabled']) && $_POST['wcac_custom_tab_link_to_ac_enabled'] ) ? 'yes' : 'no' );
		update_post_meta( $post_id, 'wcac_meeting_link', $_POST['wcac_meeting']);
}

if (get_option($settings->settings_base.'enable_WC_checkbox') == 'on')
add_action('woocommerce_process_product_meta', 'process_product_meta_custom_tab', 10, 2);

//Display Tab. Display Custom Tab on Frontend of Website for WooCommerce 2.0
if (get_option($settings->settings_base.'enable_WC_checkbox') == 'on')
add_filter( 'woocommerce_product_tabs', 'wcac_woocommerce_product_custom_tab' );
function wcac_woocommerce_product_custom_tab( $tabs ) {
	global $post, $product;
	$custom_tab_options = array(
			'enabled' => get_post_meta($post->ID, 'wcac_custom_tab_enabled', true),
			'title' => get_post_meta($post->ID, 'wcac_custom_tab_title', true),
			'content' => get_post_meta($post->ID, 'wcac_custom_tab_content', true),
			'wcac_meeting_link' => get_post_meta($post->ID, 'wcac_meeting_link', true),
			'wcac_custom_tab_link_to_ac_enabled' => get_post_meta($post->ID, 'wcac_custom_tab_link_to_ac_enabled', true),
	);
	if ( $custom_tab_options['enabled'] != 'no' ){
			$tabs['custom-tab-first'] = array(
					'title'    => $custom_tab_options['title'],
					'priority' => 25,
					'callback' => 'wcac_custom_product_tabs_panel_content',
					'wcac_custom_tab_link_to_ac_enabled' => $custom_tab_options['wcac_custom_tab_link_to_ac_enabled'],
					'wcac_meeting_link' => $custom_tab_options['wcac_meeting_link'],
					'content'  => $custom_tab_options['content']
			);
	}
	return $tabs;
}

//Render the custom product tab panel content for the callback 'wcac_custom_product_tabs_panel_content'
function wcac_custom_product_tabs_panel_content( $key, $custom_tab_options ) {
		echo '<h2>' . $custom_tab_options['title'] . '</h2>';
		if($custom_tab_options['wcac_custom_tab_link_to_ac_enabled']=='yes')
			echo '<p> Link to this event: <a href="' . $custom_tab_options['wcac_meeting_link'] . '" target="_blank">Click Here To Open</a></p>';
		echo $custom_tab_options['content'];
}

function randomPassword(){
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}
