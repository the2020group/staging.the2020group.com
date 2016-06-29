<?php

include_once('infusionsoft-functions.php');

add_filter( 'option_active_plugins', 'enable_plugins_selectively' );

function enable_plugins_selectively( $plugins ) {
  $current_page = add_query_arg( array() );

    if(strstr($current_page, 'dashboard')) {
        $userid = get_current_user_id();
        if($userid == 45) {
            //if (array_key_exists('transposh-translation-filter-for-wordpress/transposh.php', $plugins) ) {
               unset( $plugins[ array_search('transposh-translation-filter-for-wordpress/transposh.php', $plugins) ] );
            //}
        }
    }
  return $plugins;
}

// bcc admins into order complete customer email.
add_filter( 'woocommerce_email_headers', 'add_bcc_to_wc_admin_new_order', 10, 3 );
function add_bcc_to_wc_admin_new_order( $headers = '', $id = '', $wc_email = array() ) {
    if ( $id == 'customer_completed_order' ) {
        $headers .= "Bcc:   clair.doyle@the2020group.com,moira.lewis@the2020group.com\r\n";
    }
    return $headers;
}

/* Manual Subscription Renewal 'workarounds' */

add_action('template_redirect','create_manual_subscription');

function create_manual_subscription() {
  if(isset($_GET['ord'])) {
    global $woocommerce;

    $orderid = (int)$_GET['ord'];
    $order = new WC_Order($orderid);
    $orderuserid = $order->get_user_id();
    $userid = get_current_user_id();

    if($userid === $orderuserid) {

      $woocommerce->cart->empty_cart();

      foreach ($order->get_items() as $item) {

        $variationarr = array(
                          'partners' => $item['pa_partners'],
                          'international-membership' => $item['pa_international-membership']
                        );

        $woocommerce->cart->add_to_cart($item['product_id'], 1, $item['variation_id'], $variationarr);
        $signupfee = get_post_meta($item['variation_id'], '_subscription_sign_up_fee', true);
        if(is_numeric($signupfee) && $signupfee > 0) {
          $coupon_code = 'renew_'.$signupfee;
          $coupon = new WC_Coupon($coupon_code);

          if(!$coupon->exists) {
            create_subscription_coupon($coupon_code, $signupfee);
          }

          $woocommerce->cart->add_discount($coupon_code);
        }
      }
    }
    wp_redirect('/basket');
  }
}

function create_subscription_coupon($coupon_code, $amount) {
  $discount_type = 'fixed_cart'; // Type: fixed_cart, percent, fixed_product, percent_product

  $coupon = array(
    'post_title' => $coupon_code,
    'post_content' => '',
    'post_status' => 'publish',
    'post_author' => 1,
    'post_type'   => 'shop_coupon'
  );

  $new_coupon_id = wp_insert_post( $coupon );

  // Add meta
  update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
  update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
  update_post_meta( $new_coupon_id, 'individual_use', 'no' );
  update_post_meta( $new_coupon_id, 'product_ids', '' );
  update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
  update_post_meta( $new_coupon_id, 'usage_limit', '' );
  update_post_meta( $new_coupon_id, 'expiry_date', '' );
  update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
  update_post_meta( $new_coupon_id, 'free_shipping', 'no' );
}

add_filter('wcs_view_subscription_actions','limit_frontend_actions');
function limit_frontend_actions($actions,$subscription = NULL) {
    $not_allowed_action = array('cancel','suspend','reactivate');
    foreach ($not_allowed_action as $naa) {
        if(isset($actions[$naa])) {
            unset($actions[$naa]);
        }
    }

    return $actions;
}

/* END Manual Subscription Renewal 'workarounds' */


// Change wordpress emails from wordpress@the20202group.com

add_filter('wp_mail_from','yoursite_wp_mail_from');
function yoursite_wp_mail_from($content_type) {
 return 'admin@the2020group.com';
}
add_filter('wp_mail_from_name','yoursite_wp_mail_from_name');
function yoursite_wp_mail_from_name($name) {
 return '2020 Innovation';
}


//Add user roles to menu
add_action('init', 'menu_excerpt__add_menu_field');
function menu_excerpt__add_menu_field() {
    if (!is_callable('bh_add_custom_menu_fields'))
            return;

    bh_add_custom_menu_fields(array('user_types' => array(
        'description' => 'User Types',
        'type' => 'checkbox',
        'options' => array(
                array('value'=>'1', 'description'=>'Registered Users'),
                array('value'=>'3', 'description'=>'Partner in Practice'),
                array('value'=>'6', 'description'=>'Staff Member in Practice'),
                array('value'=>'2', 'description'=>'An individual in Practice'),
                array('value'=>'5', 'description'=>'An individual in Industry'),
            )
        )
    ));
}

//Check user role against navigation items
add_filter( 'wp_nav_menu_objects', 'filter_user_main_menu', 10, 2 );
function filter_user_main_menu($items, $args) {

    global $current_user;
    global $wpdb;

    if($args->menu == 'Main Menu') {

        if ( is_user_logged_in() ) {
            $validGroups = array();
            $groups_table = _groups_get_tablename( 'group' );
            if ( $groups = $wpdb->get_results( "SELECT * FROM $groups_table ORDER BY name" ) ) {
                foreach( $groups as $group ) {
                    $is_member = Groups_User_Group::read( $current_user->data->ID, $group->group_id ) ? true : false;
                    if($is_member) $validGroups[] = $group->group_id;
                }
            }

            foreach($items as $itemKey => $item) {
                unset($show);

               $user_types = get_post_meta($item->ID, '_menu_item_user_types', true);
               if(!empty($user_types)) {
                   $show = 0;
                   foreach($user_types as $user_type) {
                       if(in_array($user_type, $validGroups)) {
                           $show = 1;
                       }
                   }
               }
               elseif($item->post_parent != '0') {
                   $parent = $item->menu_item_parent;
                   while($parent != '0') {
                        $parentObj = get_post($parent);
                        $user_types = get_post_meta($parent, '_menu_item_user_types', true);
                        if(!empty($user_types)) {
                            $show = 0;
                            foreach($user_types as $user_type) {
                                if(in_array($user_type, $validGroups)) {
                                    $show = 1;
                                    break;
                                }
                            }
                        }
                        $parent = $parentObj->post_parent;
                    }
               }

               if(isset($show) && !$show) {
                   unset($items[$itemKey]);
               }
            }
        }
    }
    return $items;
}

function custom_rewrite_basic() {
  add_rewrite_rule('^international-members-directory/([^/]*)/?', 'index.php?page_id=856', 'top');
  add_rewrite_rule('^international-members-directory/([^/]*)/([^/]*)/?', 'index.php?page_id=856', 'top');
}
add_action('init', 'custom_rewrite_basic');


// include cpd function
require_once('cpd-functions.php');
require_once('newsletter-functions.php');
require_once('devtools-functions.php');
require_once('front-end-user-functions.php');
require_once('woocommerce-functions.php');
require_once('custom-admin-functions.php');
require_once('custom-sidebar-functions.php');
require_once('dashboard-my-purchases-functions.php');
require_once('csv-export.php');
function scripts_and_styles() {

  //only effect front-end of your website
	if (!is_admin() && $_SERVER['SCRIPT_NAME'] != '/wp-login.php') {


//     if ( WP_DEBUG || SCRIPT_DEBUG ) {

      // Scripts
      wp_register_script( 'geoip', '//geoip.first10.co.uk', array(), '1.0.0', false );
      wp_register_script( 'foundmain', get_template_directory_uri() . '/library/js/min/foundation.min.js', array(), null, false );
      wp_register_script( 'stellarscript', get_template_directory_uri() . '/library/js/libs/min/jquery.stellar.min.js', array(),  'jquery',  false );
      wp_register_script( 'foundeq', get_template_directory_uri() . '/library/js/libs/foundation/foundation.equalizer.js', array(), null, true );
      wp_register_script( 'owlscript', get_stylesheet_directory_uri() . '/library/js/libs/min/owl.carousel.min.js', array(), null, true );
      wp_register_script( 'fancyscript', get_template_directory_uri() . '/library/fancybox/jquery.fancybox.js', array(), 'jquery', true );
      wp_register_script( 'fileuploadscript', get_stylesheet_directory_uri() . '/library/js/min/jquery-fileupload.min.js', array(), null, true );
      wp_register_script( 'allscripts', get_stylesheet_directory_uri() . '/library/js/scripts.js', array(), null, true );

      wp_enqueue_script( 'geoip' );
      wp_enqueue_script( 'foundmain' );
      wp_enqueue_script( 'stellarscript' );
      wp_enqueue_script( 'foundeq' );
      wp_enqueue_script( 'owlscript' );
      wp_enqueue_script( 'fancyscript' );
      wp_enqueue_script( 'fileuploadscript' );
      wp_enqueue_script( 'allscripts' );

/*
    } else {
      wp_register_script('2020minjs', get_template_directory_uri() . '/library/js/min/2020scripts.min.js', array('jquery'), '1.0', true );
      wp_enqueue_script( '2020minjs');
    }
*/

		// register main stylesheet
		wp_register_style( 'misc_stylesheet', get_stylesheet_directory_uri() . '/library/css/pages/_misc.css', array(), '', 'all' );
		wp_register_style( 'stylesheet', get_stylesheet_directory_uri() . '/library/css/style.css', array(), '', 'all' );
		wp_enqueue_style( 'misc_stylesheet' );
		wp_enqueue_style( 'stylesheet' );


    // load js files only on specific pages
    if(is_page()){
      global $wp_query;
      $template_name = get_post_meta( $wp_query->post->ID, '_wp_page_template', true );

      // load js file for managing the dashboard area only if one of the following page templates are being used.
      if($template_name == 'page-dashboard-personal-details.php' ||
         $template_name == 'page-dashboard-my-cpd-record.php'||
         $template_name == 'page-dashboard-tabs.php' ||
         $template_name == 'page-dashboard-my-purchases.php' ){
         wp_enqueue_script('my_third_script', get_template_directory_uri() .'/library/js/dashboard.js');
      }
      if($template_name == 'page-team.php'){
         wp_enqueue_script('teamjs', get_template_directory_uri() .'/library/js/team.js');
      }
    }


	}
}


// Enqueue base scripts and styles
add_action('wp_enqueue_scripts', 'scripts_and_styles', 999);
add_filter( 'woocommerce_product_tabs', 'woo_rename_tabs', 98 );
function woo_rename_tabs( $tabs ) {

	if ( is_product()) {
		global $product;

		$cats = wc_get_product_terms($product->id,'product_cat');

		$cat_list = array();

		foreach ($cats as $cat) {

			$cat_list[] = $cat->slug;
		}

		if (in_array('webinars', $cat_list) || in_array('acca-webinars', $cat_list)) {

			$tabs['description']['title'] = __( 'Content' );		// Rename the description tab
		}
		return $tabs;

	}
}


// custom query for testimonials archive page

function wpd_testimonials_query( $query ){
    if( ! is_admin()
        && $query->is_post_type_archive( 'testimonials' )
        && $query->is_main_query() ){
            $query->set( 'posts_per_page', 10 );
    }
}
add_action( 'pre_get_posts', 'wpd_testimonials_query' );


function my_login_logo_one() {
?>
<style type="text/css">

body.login{
	background: #edf3f3;
}

body.login div#login{
	width: 500px;
}

body.login div#login h1 a {
 background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/library/images/general/2020-innovation.png);
 background-size: contain;
 width: 183px;
 margin-bottom: 20px;
}

body.login div#login #loginform{
	box-shadow: none;
	border-radius: 3px;
	border-bottom: 2px solid #d9d9d9;
}

body.login div#login #loginform .submit .button{
	background: #653653;
	border: none;
	box-shadow: none;
	height: 38px;
	padding: 0 18px 2px;
	font-weight: 700;
	font-size: 14px;
	transition: all, 0.2s, ease-in-out;
}

body.login div#login #loginform .submit .button:hover{
	background: #ac626f;
	cursor: pointer;
}

body.login div#login #nav a:hover,
body.login div#login #backtoblog a:hover{
	color: #653653;
}

</style>
 <?php
} add_action( 'login_enqueue_scripts', 'my_login_logo_one' );


add_filter('single_add_to_cart_text', 'woo_custom_cart_button_text');
add_filter('add_to_cart_text', 'woo_custom_cart_button_text');
function woo_custom_cart_button_text() {
	return __('Add to basket', 'woocommerce');
}

//wc_add_notice( __( 'Cart updated.', 'woocommerce' ) );
add_filter('gettext', 'woo_custom_cart_update_text', 10, 3);
function woo_custom_cart_update_text($translation, $text, $domain) {
    if ($domain == 'woocommerce') {
        if ($text == 'Cart updated.') {
            $translation = 'Basket updated.';
        }
    }
    return $translation;
}

add_filter( 'woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text' );    // 2.1 +
add_filter( 'woocommerce_product_add_to_cart_text', 'woo_custom_cart_button_text' );    // 2.1 +

// make sure we can use ajax on frontend
add_action('wp_head','ajaxurl');
function ajaxurl() {
?>
  <script type="text/javascript">var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';</script>
<?php
}

// create widget area in header for transposh
add_action( 'widgets_init', 'header_widgets_init' );
function header_widgets_init() {
    register_sidebar( array(
        'name' => 'Transposh Area',
        'id' => 'transposh-area',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => '',
    ) );
}

// if user registered, check if infusion soft contact id and company id have been set
// if not set them now.
add_action('user_register','infusionsoft_integration',10,1);
function infusionsoft_integration($user_id) {
    global $iwpro;
    $iwpro->ia_app_connect();



    $is_contact_id = get_user_meta($user_id,'is_contact_id',true);
    $is_company_id = get_user_meta($user_id,'is_company_id',true);
    $user_data = get_userdata($user_id);
    $email = $user_data->user_email;
    if (empty($is_contact_id)) {
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
        if(is_numeric($contactId)) {
            update_usermeta($user_id,'is_contact_id',$contactId);
        }


    }

    if (empty($is_company_id)) {

        $b_company = stripslashes(get_user_meta($user_id,'billing_company',true));
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

        if(is_numeric($compId)) {
            update_usermeta($user_id,'is_company_id',$compId);
        }


    }

}


add_shortcode('regional_cpd_conferences', 'shortcode_regional_cpd_conferences_function');

function shortcode_regional_cpd_conferences_function() {

    $html = '<div class="conf-tab-wrap">';


    $args = array(
        'posts_per_page' => 20,
        'product_cat' => 'regional-cpd-conferences',
        'post_type' => 'product',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'meta_query' => array (
                                array(
                                        'key' => 'date',
                                        'value' => date('Ymd'),
                                        'compare' => '>='
                                )
                        )
    );

    $the_query = get_posts( $args );

    if (count($the_query)>0) :

        $html .= '<table class="general-table reg-conf-tab" border="0" width="100%" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td>Date</td>
                            <td>Time</td>
                            <td>Venue</td>
                            <td>Location</td>
                            <td>Speaker</td>
                            <td>Register</td>
                        </tr>';

        foreach ($the_query as $conf ) :


            $cpd_id = $conf->ID;

            $eventDate = get_field('date', $cpd_id);
            $eventStartTime = get_field('start_time', $cpd_id);
            $eventEndTime = get_field('end_time', $cpd_id);


            $date_part['year'] = substr($eventDate,0,4);
            $date_part['month'] = substr($eventDate,4,2);
            $date_part['day'] = substr($eventDate,6,2);

            $time = $eventStartTime;
            if ($eventEndTime) {
                $time .= ' - '.$eventEndTime;
            }

            $venue = get_field('venue', $cpd_id);
            $location = get_field('location', $cpd_id);
            $speaker = get_field('speaker', $cpd_id);

            $html .= '          <tr>
                                <td>'.date('j<\s\u\p>S</\s\u\p> F Y', mktime(0, 0, 0, $date_part['month'], $date_part['day'], $date_part['year'])).'</td>
                                <td>'.$time.'</td>
                                <td>'.$venue.'</td>
                                <td>'.$location.'</td>
                                <td>'.$speaker.'</td>
                                <td><a class="buy" href="'.get_permalink($cpd_id).'">Register</a></td>
                            </tr>';
        endforeach;

        $html .= '      </tbody>
                </table>';

    else :

        $html .='<p>There are no Regional CPD Conferences scheduled at the moment.</p>';

    endif;

    $html .= '</div>';

    wp_reset_query();

    return $html;
}

add_shortcode('annual_conferences', 'shortcode_annual_conferences_function');

function shortcode_annual_conferences_function( $atts ) {

    $today = date('Y-m-d');

    $atts = shortcode_atts(
        array(
            'id' => '0'
        ), $atts, 'annual_conferences' );

    $html = '<div class="conf-tab-wrap">';

    if ($atts['id'] > 0) :

        $product = new WC_Product( $atts['id'] );

        $dat = get_metadata('post', $atts['id']);

        $prizing_rules = unserialize($dat['_pricing_rules'][0]);

        $product_attr = unserialize($dat['_product_attributes'][0]);

        $including_parts = explode('|',$product_attr['type']['value']);


        $types = array();

        foreach ($including_parts as $part) {
            $types[trim(esc_attr( sanitize_title( $part ) ))] = trim($part);
        }

        $future  = array();
        $current = array();

        $future_date = array();

        foreach ($prizing_rules as $rule) {

            if ($rule['date_from']!='') {
                $future[$rule['date_from']][] = $rule;

                $future_date[] = $rule['date_from'];
            }
            else {
                $current[] = $rule;
            }
        }

        $lines = array();

        foreach ($current as $cur) {
            $lines[$cur['variation_rules']['args']['variations'][0]][]['price'] = $cur['rules'][1]['amount'];
        }

        $html .= '<div class="conf-tab-wrap">
                <table class="general-table ann-conf-tab" border="0" width="100%" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                <td></td>
                <td> Member Price</td>
                <td>Non Member Price</td>
                <td></td>
                </tr>';

                foreach ($lines as $k=>$line) {

                    $at = get_post_meta($k,'attribute_type',true);

                    $l = '';

                    if ($today < $future_date[0] ) {
                        $l = '<a class="buy" href="'.get_permalink($atts['id']).'?attribute_type='.$at.'">Buy</a>';
                    }


                    $html .='
                        <tr>
                        <td>'.$types[$at].'</td>
                        <td>&pound;'.get_woocommerce_currency_symbol().current_ex_rate($line[1]['price']).'</td>
                        <td>&pound;'.get_woocommerce_currency_symbol().current_ex_rate($line[0]['price']).'</td>
                        <td>'.$l.'</td>
                        </tr>
                        ';
                }

        sort($future_date);

        $future_date = array_unique($future_date);

        foreach ($future_date as $fd) {

            $html .='
                <tr>
                <td colspan="4" style="text-align: left;">If booking after '.date('j<\s\u\p>S</\s\u\p> F Y',strtotime($fd)).':</td>

                </tr>';


            $flines = array();

            foreach ($future[$fd] as $fur) {

                $flines[$fur['variation_rules']['args']['variations'][0]][]['price'] = get_woocommerce_currency_symbol().current_ex_rate($fur['rules'][1]['amount']);

            }

            foreach ($flines as $k=>$fline) {

                $at = get_post_meta($k,'attribute_type',true);

                $li='';

                if ($l=='') {
                    $li = '<a class="buy" href="'.get_permalink($atts['id']).'?attribute_type='.$at.'">Buy</a>';
                }



                $html .='
                    <tr>
                    <td>'.$types[$at].'</td>
                    <td>&pound;'.get_woocommerce_currency_symbol().current_ex_rate($fline[1]['price']).'</td>
                    <td>&pound;'.get_woocommerce_currency_symbol().current_ex_rate($fline[0]['price']).'</td>
                    <td>'.$li.'</td>
                    </tr>
                ';
            }

        }



        $html .='
        </tbody>
        </table>
        </div>';

    else :

        $html .='<p>There are no Regional CPD Conferences scheduled at the moment.</p>';

    endif;

    $html .= '</div>';

    wp_reset_query();

    return $html;
}




add_shortcode('annual_conference', 'shortcode_annual_conference_function');

function shortcode_annual_conference_function( $atts ) {

    $today = date('Y-m-d');

    $atts = shortcode_atts(
        array(
            'id' => '0'
        ), $atts, 'annual_conferences' );

    $html = '<div class="conf-tab-wrap">';

    if ($atts['id'] > 0) :

        $product = new WC_Product( $atts['id'] );

        $dat = get_metadata('post', $atts['id']);



        $prizing_rules = unserialize($dat['_pricing_rules'][0]);

        $lines = array();

        foreach ($prizing_rules as $cur) {

            $line = array ('price'  => $cur['rules'][1]['amount'],
                           'qty'    => $cur['rules'][1]['from'],
                           'date'   => $cur['date_to'],
                           'target' => $cur['conditions'][1]['args']['applies_to']);

            // bulk price
            if (isset($cur['rules'][1]['from']) && $cur['rules'][1]['from'] > 1) {
              if ($cur['conditions'][1]['args']['applies_to'] == 'everyone') {
                $lines[$cur['variation_rules']['args']['variations'][0]]['bulk']['everyone'] = $line;
              }
              else {
                $lines[$cur['variation_rules']['args']['variations'][0]]['bulk']['member'] = $line;
              }
            }
            // early bird
            elseif ($cur['date_to'] != '') {
              if ($cur['conditions'][1]['args']['applies_to'] == 'everyone') {
                $lines[$cur['variation_rules']['args']['variations'][0]]['early_bird']['everyone'] = $line;
              }
              else {
                $lines[$cur['variation_rules']['args']['variations'][0]]['early_bird']['member'] = $line;
              }
            }
            // standard
            else {
              if ($cur['conditions'][1]['args']['applies_to'] == 'everyone') {
                $lines[$cur['variation_rules']['args']['variations'][0]]['standard']['everyone'] = $line;
              }
              else {
                $lines[$cur['variation_rules']['args']['variations'][0]]['standard']['member'] = $line;
              }
            }


        }




        $html .= '<div class="conf-tab-wrap">
                <table class="general-table ann-conf-tab" border="0" width="100%" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                <td></td>
                <td>Member Price</td>
                <td>Non Member Price</td>
                <td>Register</td>
                </tr>';


				$keys = array();

                foreach ($lines as $k=>$line) {
                	$keys[]= $k;
				}

                foreach ($lines[$keys[0]] as $k=>$line) {

                    $at = get_post_meta($k,'attribute_type',true);

                    if ($k == 'early_bird') {
                      $title = '<strong>Early bird discount</strong>
                      				If booking on or before '.date('j<\s\u\p>S</\s\u\p> F Y',strtotime($lines[$keys[0]]['early_bird']['everyone']['date'])).'<br />';

                    }
                    else if ($k == 'bulk') {
                      $title = '<strong>Multi-participant discount</strong>
                              If booking '.$lines[$keys[0]]['bulk']['everyone']['qty'].' or more delegates from the same firm (all names must be supplied):<br />';
                    }
                    else {
                      if(isset($lines[$keys[0]]['early_bird']['everyone']['date'])) {
                        $title = 'If booking after '.date('j<\s\u\p>S</\s\u\p> F Y',strtotime($lines[$keys[0]]['early_bird']['everyone']['date'])).'<br />';
                      } else {
                        $title = 'Standard booking:<br />';
                      }
                    }

					$title .= 'Basic 8 hour conference rate, including lunch';
                    $title .= '<br />Basic 8 hour conference rate, including lunch, dinner &amp; wine';
                    $title .= '<br />Basic 8 hour conference rate, including lunch, dinner &amp; wine plus Friday half day';

                    $html .='
                        <tr>
                        <td>'.$title.'</td>
                        <td><br />'.get_woocommerce_currency_symbol().current_ex_rate($lines[$keys[0]][$k]['member']['price']).'<br />'.get_woocommerce_currency_symbol().current_ex_rate($lines[$keys[1]][$k]['member']['price']).'<br />'.get_woocommerce_currency_symbol().current_ex_rate($lines[$keys[2]][$k]['member']['price']).'</td>
                        <td><br />'.get_woocommerce_currency_symbol().current_ex_rate($lines[$keys[0]][$k]['everyone']['price']).'<br />'.get_woocommerce_currency_symbol().current_ex_rate($lines[$keys[1]][$k]['everyone']['price']).'<br />'.get_woocommerce_currency_symbol().current_ex_rate($lines[$keys[2]][$k]['everyone']['price']).'</td>
                        <td><a class="buy" href="'.get_permalink($atts['id']).'?attribute_type='.$at.'">Register</a></td>
                        </tr>
                        ';
                }

        $html .='
        </tbody>
        </table>
        </div>';

    else :

        $html .='<p>There are no conferences scheduled at the moment.</p>';

    endif;

    $html .= '</div>';

    wp_reset_query();

    return $html;
}


add_shortcode('conference', 'shortcode_conference_function');

function shortcode_conference_function( $atts ) {

    $today = date('Y-m-d');

    $atts = shortcode_atts(
        array(
            'id' => '0'
        ), $atts, 'annual_conferences' );

    $html = '<div class="conf-tab-wrap">';

    if ($atts['id'] > 0) :

        $product = new WC_Product( $atts['id'] );

        $dat = get_metadata('post', $atts['id']);



        $prizing_rules = unserialize($dat['_pricing_rules'][0]);

        $lines = array();

        foreach ($prizing_rules as $cur) {

            $line = array ('price'  => $cur['rules'][1]['amount'],
                           'qty'    => $cur['rules'][1]['from'],
                           'date'   => $cur['date_to'],
                           'target' => $cur['conditions'][1]['args']['applies_to']);

            // bulk price
            if ($line['qty'] > 1) {
              if ($line['target'] == 'everyone') {
                $lines['bulk']['everyone'] = $line;
              }
              else {
                $lines['bulk']['member'] = $line;
              }
            }
            // early bird
            elseif ($line['date'] != '') {
              if ($line['target'] == 'everyone') {
                $lines['early_bird']['everyone'] = $line;
              }
              else {
                $lines['early_bird']['member'] = $line;
              }
            }
            // standard
            else {
              if ($line['target'] == 'everyone') {
                $lines['standard']['everyone'] = $line;
              }
              else {
                $lines['standard']['member'] = $line;
              }
            }
        }

        $html .= '<div class="conf-tab-wrap">
                <table class="general-table ann-conf-tab" border="0" width="100%" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                <td></td>
                <td>Member Price</td>
                <td>Non Member Price</td>
                <td>Register</td>
                </tr>';

                foreach ($lines as $k=>$line) {



                    $at = get_post_meta($k,'attribute_type',true);

                    if ($k == 'early_bird') {
                      $title = '<strong>Early bird discount</strong><br />
                                If booking on or before '.date('j<\s\u\p>S</\s\u\p> F Y',strtotime($lines['early_bird']['everyone']['date']));
                    }
                    else if ($k == 'bulk') {
                      $title = '<strong>Multi-participant discount</strong><br />
                              If booking '.$lines['bulk']['everyone']['qty'].' or more delegates from the same firm (all names must be supplied):';
                    }
                    else {
                        if(in_array('early_bird', $lines)) :
                            $title = 'If booking after '.date('j<\s\u\p>S</\s\u\p> F Y',strtotime($lines['early_bird']['everyone']['date']));
                        else :
                            $title = 'Standard';
                        endif;

                    }


                    $html .='
                        <tr>
                        <td>'.$title.'</td>
                        <td>'.get_woocommerce_currency_symbol().current_ex_rate($lines[$k]['member']['price']).' + VAT </td>
                        <td>'.get_woocommerce_currency_symbol().current_ex_rate($lines[$k]['everyone']['price']).' + VAT </td>
                        <td><a class="buy" href="'.get_permalink($atts['id']).'?attribute_type='.$at.'">Register</a></td>
                        </tr>
                        ';
                }

        $html .='
        </tbody>
        </table>
        </div>';

    else :

        $html .='<p>There are no conferences scheduled at the moment.</p>';

    endif;

    $html .= '</div>';

    wp_reset_query();

    return $html;
}


function shortcode_products_table_by_category_function($atts) {

    $a = shortcode_atts( array(
        'cat' => NULL,
        //'bar' => 'something else',
    ), $atts );

    $html = '<div class="conf-tab-wrap">';


    $args = array(
        'posts_per_page' => -1,
        'product_cat' => $a['cat'],
        'post_type' => 'product',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'meta_query' => array (
                                array(
                                        'key' => 'date',
                                        'value' => date('Ymd'),
                                        'compare' => '>='
                                )
                        )
    );

    $the_query = get_posts( $args );

    if (count($the_query)>0) :

        $html .= '<table class="general-table reg-conf-tab" border="0" width="100%" cellspacing="0" cellpadding="0">
                    <tbody>
                        <tr>
                            <td>Date</td>
                            <td>Time</td>
                            <td>Venue</td>
                            <td>Location</td>
                            <td>Register</td>
                        </tr>';

        foreach ($the_query as $conf ) :


            $cpd_id = $conf->ID;

            $eventDate = get_field('date', $cpd_id);
            $eventStartTime = get_field('start_time', $cpd_id);
            $eventEndTime = get_field('end_time', $cpd_id);


            $date_part['year'] = substr($eventDate,0,4);
            $date_part['month'] = substr($eventDate,4,2);
            $date_part['day'] = substr($eventDate,6,2);

            $time = $eventStartTime;
            if ($eventEndTime) {
                $time .= ' - '.$eventEndTime;
            }

            $location = get_field('location', $cpd_id);
            $venue = get_field('venue', $cpd_id);
            $speaker = get_field('speaker', $cpd_id);

            $html .= '          <tr>
                                <td>'.date('j<\s\u\p>S</\s\u\p> F Y', mktime(0, 0, 0, $date_part['month'], $date_part['day'], $date_part['year'])).'</td>
                                <td>'.$time.'</td>
                                <td>'.$venue.'</td>
                                <td>'.$location.'</td>
                                <td><a class="buy" href="'.get_permalink($cpd_id).'">Register</a></td>
                            </tr>';
        endforeach;

        $html .= '      </tbody>
                </table>';

    else :

        $html .='<p>There are no events scheduled at the moment.</p>';

    endif;

    $html .= '</div>';

    wp_reset_query();

    return $html;
}

add_shortcode('products_by_category', 'shortcode_products_table_by_category_function');




/**********
*
* General Group and Capability Check Functions
* Pass the group ID into the function that want to check the user has access to or not.
* Pass an array of ID's in to check against multiple groups - eg: check_groups_user_capabilities(array(4,5,7));
*
*/

function check_groups_user_capabilities($groupID) {

  if(!is_array($groupID)) {

    // Check if the option 'all' has been passed through - if so, get all subscription id's
    if($groupID == 'all'){
      global $wpdb;
      $ids = $wpdb->get_results('SELECT group_id FROM '. $wpdb->prefix .'groups_group');

      $groupID = array();
      foreach($ids as $id) {
        // Exclude id 1 which is a simple 'registered' group
        if($id->group_id > 1) {
          $groupID[] = $id->group_id;
        }
      }

    } else {
      $groupID = array($groupID);
    }
  }

  require_once( ABSPATH . 'wp-includes/pluggable.php' );

  foreach ($groupID as $group) {

    // Is this user in an owner group
    if ( Groups_User_Group::read( get_current_user_id() , $group )) {
      return true;
    }
  }

  return false;
}




function hasUserAccessTo($groups = array()) {

  global $current_user;

    if (count($groups)==0 ) {
      return true;
    }

    foreach ($groups as $key => &$value) {
      $value = strtolower(str_replace(' ','_',$value));
    }

    if ($current_user->ID == 0) {
      return false;
    }


    global $wpdb;

    $all_groups = $wpdb->get_results('SELECT * FROM '. $wpdb->prefix .'groups_group');

    $group = array();

    if (count($all_groups) > 0) {
      foreach ($all_groups as $id) {
        if ($id->name !='Registered') {
          $group[$id->group_id] = strtolower(str_replace(' ','_',$id->name));
        }
      }
    }

    $user_groups = $wpdb->get_results($wpdb->prepare('SELECT * FROM '. $wpdb->prefix .'groups_user_group WHERE user_id=%d',$current_user->ID));

    if (count($user_groups)>0) {
      foreach ($user_groups as $user_group) {
        if (isset($group[$user_group->group_id])) {
          if (in_array($group[$user_group->group_id],$groups)) {
            return true;
          }
        }
      }
      return false;
    } else {
      return false;
    }
}

function userHasSubscription($status = null)
{
  global $current_user;

  // If the visitor isn't logged in, just return false - they won't have any subscriptions.
  if ($current_user->ID == 0) {
    return false;
  }

  // Check each subscription...
  foreach (WC_Subscriptions_Manager::get_users_subscriptions( $current_user->ID ) as $sub) {
    // If the subscription is a membership, and it's active, return true.
    if(in_array($sub['product_id'], array(45, 48, 2043))) {
      if( (is_array($status) && in_array($status, $sub['status'])) ||
          ($sub['status'] == $status) ) {
        return true;
      }
    }
  }
  // User doesn't have any active memberships - return false.
  return false;
}

function userHasActiveSubscription()
{
  global $current_user;
  global $_subscription_details;

  // If the visitor isn't logged in, just return false - they won't have any subscriptions.
  if ($current_user->ID == 0) {
    return false;
  }

  // Check each subscription...
  foreach ($_subscription_details as $sub) {
    // If the subscription is a membership, and it's active, return true.
    if( in_array($sub['product_id'], array(45, 48, 2043)) && $sub['status'] == 'active' ) {
      return true;
    }
  }
  // User doesn't have any active memberships - return false.
  return false;
}

/** Get current URL (used for showing/hiding product archive filters)
 **/
function get_url() {
    $url  = isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http';
    $url .= '://' . $_SERVER['SERVER_NAME'];
    $url .= in_array( $_SERVER['SERVER_PORT'], array('80', '443') ) ? '' : ':' . $_SERVER['SERVER_PORT'];
    $url .= $_SERVER['REQUEST_URI'];
    return $url;
}

// Variable & intelligent excerpt length.
function print_excerpt($length) { // Max excerpt length. Length is set in characters
    global $post;
    $text = $post->post_excerpt;
    if ( '' == $text ) {
        $text = get_the_content('');
        $text = apply_filters('the_content', $text);
        $text = str_replace(']]>', ']]>', $text);
    }
    $text = strip_shortcodes($text); // optional, recommended
    $text = strip_tags($text); // use ' $text = strip_tags($text,'&lt;p&gt;&lt;a&gt;'); ' if you want to keep some tags

    $text = substr($text,0,$length);
    $excerpt = reverse_strrchr($text, '.', 1) . '&hellip;';
    if( $excerpt ) {
        echo apply_filters('the_excerpt',$excerpt);
    } else {
        echo apply_filters('the_excerpt',$text);
    }
}

// Returns the portion of haystack which goes until the last occurrence of needle
function reverse_strrchr($haystack, $needle, $trail) {
    return strrpos($haystack, $needle) ? substr($haystack, 0, strrpos($haystack, $needle) + $trail) : false;
}


/** Join taxonomy tables

function tax_search_join( $join )
{
  global $wpdb;
  if( is_search() )
  {
    $join .= "
        INNER JOIN
          {$wpdb->term_relationships} ON {$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id
        INNER JOIN
          {$wpdb->term_taxonomy} ON {$wpdb->term_taxonomy}.term_taxonomy_id = {$wpdb->term_relationships}.term_taxonomy_id
        INNER JOIN
          {$wpdb->terms} ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id
      ";
  }
  return $join;
}
add_filter('posts_join', 'tax_search_join');
 **/

/** Add search_tags to search results
function tax_search_where( $where )
{
  global $wpdb;
  if( is_search() )
  {
    // add the search term to the query
    $where .= " OR
    (
      {$wpdb->term_taxonomy}.taxonomy LIKE 'persona'
      AND
      {$wpdb->terms}.name LIKE ('%".$wpdb->escape( get_query_var('s') )."%')
    ) ";
  }
  return $where;
}
add_filter('posts_where', 'tax_search_where');
**/

/** Group search results by post ID to avoid duplicates

function tax_search_groupby( $groupby )
{
  global $wpdb;
  if( is_search() )
  {
    $groupby = "{$wpdb->posts}.ID";
  }
  return $groupby;
}
add_filter('posts_groupby', 'tax_search_groupby');
 **/

/**
 * Populate a Gravity Forms Name field.
 *
 * @param array        $field  Gravity Forms field object
 * @param string|array $name   Name components, pass either a string or array when the
 *                             nameFormat is 'simple' or an array in all other cases
 */
function eo_get_usermeta($meta_key)
{
  $current_user = wp_get_current_user();
  $ret = (($current_user instanceof WP_User) && (0 != $current_user->ID)) ?
    $current_user->__get($meta_key) : '';

  return $ret;
}

add_filter('gform_field_value_first_name', 'eo_populate_name');
add_filter('gform_field_value_last_name',  'eo_populate_name');

function eo_populate_name($value)
{
  // extract the parameter name from the current filter name
  $param = str_replace('gform_field_value_', '', current_filter());

  // we are interested only in the first_name and last_name parameters
  if ( !in_array($param, array('first_name', 'last_name')) )
    return $value;

  // incidentally, the user meta keys for the first and last name are
  // 'first_name' and 'last_name', the same as the parameter names
  $value = eo_get_usermeta($param);

  return $value;

}

/*
* Gets the excerpt of a specific post ID or object
* @param - $post - object/int - the ID or object of the post to get the excerpt of
* @param - $length - int - the length of the excerpt in words
* @param - $tags - string - the allowed HTML tags. These will not be stripped out
* @param - $extra - string - text to append to the end of the excerpt
*/
function twenty_excerpt_by_id($post, $length = 10, $tags = '<a><em><strong>', $extra = ' . . .') {

    if(is_int($post)) {
        // get the post object of the passed ID
        $post = get_post($post);
    } elseif(!is_object($post)) {
        return false;
    }

    if(has_excerpt($post->ID)) {
        $the_excerpt = $post->post_excerpt;
        return apply_filters('the_content', $the_excerpt);
    } else {
        $the_excerpt = $post->post_content;
    }

    $the_excerpt = strip_shortcodes(strip_tags($the_excerpt), $tags);
    $the_excerpt = preg_split('/\b/', $the_excerpt, $length * 2+1);
    $excerpt_waste = array_pop($the_excerpt);
    $the_excerpt = implode($the_excerpt);
    $the_excerpt .= $extra;

    return apply_filters('the_content', $the_excerpt);
}


/*
*  DS add custom bundle to cart button from woocommerce bundle template
*/
function add_bundle_to_cart () {

global $woocommerce, $product, $post, $woocommerce_bundles;

do_action( 'woocommerce_before_add_to_cart_form' );

$html = '<form method="post" enctype="multipart/form-data" class="bundle_form" >';

$bundled_items = $product->get_bundled_items();

$attributes = $product->get_available_bundle_variations();

    if ( $product->is_purchasable() ) {


            do_action( 'woocommerce_before_add_to_cart_button' );

                    foreach ( $bundled_items as $bundled_item_id => $bundled_item ) {

                        $bundled_item_id = $bundled_item->item_id;
                        $bundled_product = $bundled_item->product;

                        if ( $bundled_product->product_type == 'variable' ) {

                                foreach ( $attributes[ $bundled_item_id ][0]['attributes'] as $name => $options ) {
                                   $html .= '<input type="hidden" name="bundle_'.$name.'_'.$bundled_item_id.'" class="bundle_'.$name.'_'.$bundled_item_id.'" value="'.$options.'">';
                                }
                            $html .= '<input type="hidden" name="bundle_variation_id_'.$bundled_item_id.'" class="bundle_variation_id_'.$bundled_item_id.'" value="'.$attributes[ $bundled_item_id ][0][ 'variation_id' ].'" />';

                        }
                    }

                $html .= '<input type="hidden" name="add-to-cart" value="'.$product->id.'" />';
                $html .= '<input type="hidden" name="quantity" value="1" />';
                $html .= '<a class="bundle_add_to_cart_button gen-btn orange icon right-arrow" href="">Add to basket</a>';

                do_action( 'woocommerce_after_add_to_cart_button' );

    }

$html .= '</form>';

echo $html;

}

//add_filter( 'the_content', 'geoip_append_content' );

function clear_notices_after_login($redirect) {
    wc_clear_notices();
    return $redirect;
}
add_action('woocommerce_login_redirect', 'clear_notices_after_login');



function benchmark_check_functions($form) {

// error_log(print_r($form,1));
  if ( !is_user_logged_in() ) {
    return $form;
  }

  $user = wp_get_current_user();
  $all_meta_for_user = get_user_meta( $user->ID );
//  print_r( $all_meta_for_user );

  // getting the user data to prepopulate the form if they are logged in
  $email = $user->user_email;
  $first_name = $all_meta_for_user['first_name'][0];
  $last_name = $all_meta_for_user['last_name'][0];
  $billing_company = $all_meta_for_user['billing_company'][0];
  $billing_address1 = $all_meta_for_user['billing_address_1'][0];
  $billing_address2 = $all_meta_for_user['billing_address_2'][0];
  $billing_city = $all_meta_for_user['billing_city'][0];
  $billing_postcode = $all_meta_for_user['billing_postcode'][0];

  //logged in checkbox hidden field
  $field_loggedin   = 34;
  $field_fname      = 36;
  $field_lname      = 37;
  $field_email      = 40;
  $field_company    = 28;
  $field_address1   = 30;
  $field_address2   = 31;
  $field_city       = 32;
  $field_postcode   = 33;

  foreach( $form['fields'] as &$field )  {

    if ( $field->id == $field_loggedin ) {
      // setting the hidden 'is_logged_in' form field to 1 which hides the login button
      $field->defaultValue = 1;

    } elseif ( $field->id == $field_fname ) {
      $field->defaultValue = $first_name;

    } elseif ( $field->id == $field_lname ) {
      $field->defaultValue = $last_name;

    } elseif ( $field->id == $field_email ) {
      $field->defaultValue = $email;

    } elseif ( $field->id == $field_company ) {
      $field->defaultValue = $billing_company;

    } elseif ( $field->id == $field_address1 ) {
      $field->defaultValue = $billing_address1;

    } elseif ( $field->id == $field_address2 ) {
      $field->defaultValue = $billing_address2;

    } elseif ( $field->id == $field_city ) {
      $field->defaultValue = $billing_city;

    } elseif ( $field->id == $field_postcode ) {
      $field->defaultValue = $billing_postcode;
    }

  }

  return $form;

}


add_filter( 'gform_pre_render_9', 'benchmark_check_functions' );

function gather_benchmark_data( $entry, $form ) {

    /*error_log('entry:');
    error_log(print_r($entry,1));
    error_log('form:');
    error_log(print_r($form,1));*/

}

add_action( 'gform_after_submission_9', 'gather_benchmark_data', 10, 2 );