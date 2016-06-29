<?php


// START submit the correct nominal codes
add_filter('woocommerce_xero_line_item_account_code','custom_xero_codes',10,2);
function custom_xero_codes($account_code,$item) {
  global $wpdb;

  $sql = 'SELECT post_id FROM '.$wpdb->postmeta.' WHERE meta_value="'.$item->get_item_code().'" LIMIT 1';
  $result = $wpdb->get_results($sql);

  $product = get_post($result[0]->post_id);

  if (is_numeric($product->post_parent) && $product->post_parent > 0) {
    $xero_code = get_post_meta($product->post_parent,'xero_code',true);
  }
  else {
    $xero_code = get_post_meta($result[0]->post_id,'xero_code',true);
  }
  
  if ($xero_code!==false && !empty($xero_code)){
    return $xero_code;
  }
  else {
    return $account_code;
  }
  
}

// END submit the correct nominal codes

  add_action ('woocommerce_thankyou', 'order_pending_payment');
  function myfunction($order_id) {
    $order = new WC_Order($order_id);

    //print_r($order);
  }


// Remove woocommerce styles
add_filter( 'woocommerce_enqueue_styles', 'jk_dequeue_styles' );
function jk_dequeue_styles( $enqueue_styles ) {
  unset( $enqueue_styles['woocommerce-general'] );  // Remove the gloss
  unset( $enqueue_styles['woocommerce-layout'] );   // Remove the layout
  unset( $enqueue_styles['woocommerce-smallscreen'] );  // Remove the smallscreen optimisation
  return $enqueue_styles;
}


//
add_action('pre_get_posts', 'order_by_soonest');

function order_by_soonest( $query ) {


  // validate
  if( is_admin() )
  {
    return $query;
  }


  // project example
  if( isset($query->query['product_cat']) && substr($query->query['product_cat'],0,8) == 'webinars' )
  {
    if($query->query_vars['product_cat'] != 'how-to-be-successful-as-a-sole-practitioner') {

      $query->set('orderby', 'meta_value_num');
      $query->set('meta_key', 'date');
      if((is_tax('product_cat',101)) || (term_is_ancestor_of( 101, get_queried_object()->term_id, 'product_cat' ))) {
        $query->set('order', 'DESC');
      } else if((is_tax('product_cat',30)) ) {
        $query->set('orderby', 'menu_order');
      } else {
       $query->set('order', 'ASC');
      }
    }
  }

  // always return
  return $query;

}


// Register Sidebar for product pages
$sidebarProductArgs = array(
  'name'          => __( 'Product Pages', '2020 Group' ),
  'id'            => 'sidebar-products',
  'description'   => '',
  'class'         => '',
  'before_widget' => '<div id="%1$s" class="widget %2$s">',
  'after_widget'  => '</div>',
  'before_title'  => '<h2 class="widgettitle">',
  'after_title'   => '</h2>' );

register_sidebar( $sidebarProductArgs );


// when an order is set to processing check if the order is for purchasing
// a membership and if so mark the order as completed.
add_filter( 'woocommerce_payment_complete_order_status', 'subscription_order_payment_complete_order_status', 10, 2 );

function subscription_order_payment_complete_order_status( $order_status, $order_id ) {
  $order = new WC_Order( $order_id );

  if ( 'processing' == $order_status &&
       ( 'on-hold' == $order->status || 'pending' == $order->status || 'failed' == $order->status ) ) {

    $subscription_order = null;

    if ( count( $order->get_items() ) > 0 ) {

      foreach( $order->get_items() as $item ) {

        if ( 'line_item' == $item['type'] ) {

          $_product = $order->get_product_from_item( $item );

          if ( !$_product->product_type =='subscription_variation' ) {
            // once we've found one non-subscription product we know we're done, break out of the loop
            $subscription_order = false;

            wc_clear_notices();

            wc_add_notice ('Important! Please note that confirmation of your event(s) will not be issued until you allocate the names of the delegate(s) attending by visiting <a href="/dashboard/#fndtn-my-purchases">My2020Dashboard – My Purchases</a>.', 'notice');


            break;
          } else {
            $subscription_order = true;
            //error_log('subs set');
          }

        }
      }
    }

    // subscription order, mark as completed
    if ( $subscription_order ) {
      return 'completed';
    }
  }

  // non-subscription order, return original status
  return $order_status;
}



add_action('woocommerce_order_status_completed','assign_user_automatically');

function assign_user_automatically($order_id) {

global $woocommerce, $woocommerce_bundles, $product;

$order = new WC_Order( $order_id );

//if any item in the cart has parent product for who set

$assigned_products = array();

$count = 1;

  if ( count( $order->get_items() ) > 0 ) {

    foreach( $order->get_items() as $item ) {
      //error_log(print_r($item['item_meta']['This booking is'], 1));
      if (isset($item['item_meta']['This booking is'][0]) && trim($item['item_meta']['This booking is'][0])==trim('For me')) {

        $product_cats = wp_get_post_terms( $item['product_id'], 'product_cat' );

        foreach ($product_cats as $cat) {
          if ($cat->name == 'Webinars') {

              $date = get_field('date',$item['product_id']);
              //error_log('product id : '.$item['product_id']);
              //error_log('date : '.$date);
              $date_n= substr($date,0,4).'-'.substr($date,-4,2).'-'.substr($date,-2);
              //error_log('date_n : '.$date_n);

              $data = array('product_id'=>$item['product_id'],'booking_year'=>date('Y'),'event_date'=>$date_n,'user'=>$order->user_id,'order_id'=> $order_id);

              $bundled_items = get_post_meta( $item['product_id'], '_bundle_data', true );

              if ($bundled_items) {
                foreach ($bundled_items as $bundle_item) {

                  $date = get_field('date',$bundle_item['product_id']);
                  //error_log('product id : '.$item['product_id']);
                  //error_log('date : '.$date);
                  $date_n= substr($date,0,4).'-'.substr($date,-4,2).'-'.substr($date,-2);

                  $data = array('product_id'=>$bundle_item['product_id'],'booking_year'=>date('Y'),'event_date'=>$date_n,'user'=>$order->user_id,'order_id'=> $order_id);
                  //error_log(print_r($data,1));
                  assign_users_to_webinar($data);

                  $assigned_products[] = $bundle_item['product_id'];

                  $count++;
                }
                unset($bundled_items);
              } else {
                assign_users_to_webinar($data);
                break;
              }
            }

          if ($cat->name == 'Conferences' || $cat->name == 'Annual Conferences') {

              $date = get_field('date',$item['product_id']);


              $date_n= substr($date,0,4).'-'.substr($date,-4,2).'-'.substr($date,-2);

              if ( $item['variation_id']==0 || empty($item['variation_id']) ) {
                $p_id = $item['product_id'];
              }
              else {
                $p_id = $item['variation_id'];
              }

              $data = array('product_id'=>$p_id,'booking_year'=>date('Y'),'event_date'=>$date_n,'user'=>$order->user_id,'order_id'=> $order_id);

              $bundled_items = get_post_meta( $item['product_id'], '_bundle_data', true );

              if ($bundled_items) {
                // foreach ($bundled_items as $bundle_item) {
                //   $date = get_field('date',$bundle_item['product_id']);
                //   $date_n= substr($date,0,4).'-'.substr($date,-4,2).'-'.substr($date,-2);
                //   $data = array('product_id'=>$bundle_item['product_id'],'booking_year'=>date('Y'),'event_date'=>$date_n);
                //   assign_users_to_conference($data);
                //   break;
                // }
              } else {
                assign_users_to_conference($data);
                break;
              }
            }

          if ($cat->name == 'Focus Groups') {

              $date = get_field('date',$item['product_id']);

              //error_log(print_r($item,1));
              $date_n= substr($date,0,4).'-'.substr($date,-4,2).'-'.substr($date,-2);

              $data = array('product_id'=>$item['product_id'],'booking_year'=>date('Y'),'user'=>$order->user_id,'event_date'=>$date_n,'order_id'=> $order_id);
              //error_log(print_r($data,1));

              $bundled_items = get_post_meta( $item['product_id'], '_bundle_data', true );

              //error_log(print_r($bundled_items,1));
              if ($bundled_items) {
                foreach ($bundled_items as $bundle_item) {
                  $date = get_field('date',$bundle_item['product_id']);
                  $date_n= substr($date,0,4).'-'.substr($date,-4,2).'-'.substr($date,-2);
                  $data = array('product_id'=>$bundle_item['product_id'],'booking_year'=>date('Y'),'event_date'=>$date_n,'user'=>$order->user_id,'order_id'=> $order_id);
                  assign_users_to_focusgroup($data);
                  break;
                }
              } else {
                assign_users_to_focusgroup($data);
                break;
              }
            }

            if ($cat->name == 'Workshops') {

              $date = get_field('date',$item['product_id']);

              //error_log(print_r($item,1));
              $date_n= substr($date,0,4).'-'.substr($date,-4,2).'-'.substr($date,-2);

              $data = array('product_id'=>$item['product_id'],'booking_year'=>date('Y'),'event_date'=>$date_n,'order_id'=> $order_id);
              //error_log(print_r($data,1));

              $bundled_items = get_post_meta( $item['product_id'], '_bundle_data', true );

              //error_log(print_r($bundled_items,1));
              if ($bundled_items) {
                foreach ($bundled_items as $bundle_item) {
                  $date = get_field('date',$bundle_item['product_id']);
                  $date_n= substr($date,0,4).'-'.substr($date,-4,2).'-'.substr($date,-2);
                  $data = array('product_id'=>$bundle_item['product_id'],'booking_year'=>date('Y'),'event_date'=>$date_n,'user'=>$order->user_id,'order_id'=> $order_id);
                  assign_users_to_workshop($data);
                  break;
                }
              } else {
                assign_users_to_workshop($data);
                break;
              }
            }
          }
          //break;

        }

    }

  }


}

add_action( 'woocommerce_order_status_completed', 'assign_checkout_banner', 10 );
function assign_checkout_banner($order_id) {
  // If we're in the admin panel, we don't want to display these banners at all.
  // This function actually checks if the user is an admin at all - so this function
  // won't fire for admin users.
  if (current_user_can( 'manage_options')) { return; }

  $order = new WC_Order($order_id);


  // No need to display any banners if the order has no items.
  if (count($order->get_items()) === 0) { return; }


  $messages = array(
    'standard' => 'Please visit <a href="/dashboard/#fndtn-my-purchases">My2020Dashboard – My Purchases</a> to access the details of your purchase.',
    'subscription' => 'Please visit <a href="/dashboard/">My2020Dashboard</a> to access all of the 2020 Membership Benefits.',
    'for-colleagues' => 'Important! Please note that confirmation of your event(s) will not be issued until you allocate the names of the delegate(s) attending by visiting <a href="/dashboard/#fndtn-my-purchases">My2020Dashboard – My Purchases</a>.'
  );

  foreach ($order->get_items() as $item) {
    $product = $order->get_product_from_item($item);
    $booking = $item['item_meta']['This booking is'][0];

    if (isset($booking)) {
      if (trim($booking) == trim('For me')) {
        // this is a booking, and is for ne
        wc_clear_notices();
        wc_add_notice($messages['standard'], 'success');
        break;
      } else {
        // this is a booking, and is for a colleague
        wc_clear_notices();
        wc_add_notice($messages['for-colleagues'], 'success');
        break;
      }
    } elseif ($product->product_type =='subscription_variation' || $product->product_type =='subscription') {
      // this is a new subscription
      wc_clear_notices();
      wc_add_notice($messages['subscription'], 'success');
      break;
    } else {
      // this is a standard order
      wc_clear_notices();
      wc_add_notice($messages['standard'], 'success');

      break;
    }
  }
}

/*
* Set custom product placeholder image for woocommerce
*
**/
add_action( 'init', 'custom_fix_thumbnail' );

function custom_fix_thumbnail() {
  add_filter('woocommerce_placeholder_img_src', 'custom_woocommerce_placeholder_img_src');

  function custom_woocommerce_placeholder_img_src( $src ) {
  $src = get_template_directory_uri() . '/library/images/general/product-placeholder.png';

  return $src;
  }
}


/*
* Single Product Pages - Move product images (if available) to top of sidebar)
*
**/
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
add_action( 'woocommerce_single_product_images', 'woocommerce_show_product_images', 5 );

/* Remove product image in product loop - eg: for archive pages */
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );

/* Move Related products and upsells to the sidebar */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

add_action( 'woocommerce_product_sidebar', 'woocommerce_output_related_products', 10);
add_action( 'woocommerce_product_sidebar', 'woocommerce_upsell_display', 15);


/*
* Function to show an individual product variation's price
*
**/
function get_product_variation_price($variation_id) {
  global $woocommerce; // Don't forget this!
  $product = new WC_Product_Variation($variation_id);
  //return $product->product_custom_fields['_price'][0]; // No longer works in new version of WooCommerce
  return $product->get_price_html(); // Works. Use this if you want the formatted price
  //return $product->get_price(); // Works. Use this if you want unformatted price
}

function get_product_variation_price_no_currency($variation_id) {
  global $woocommerce; // Don't forget this!
  $product = new WC_Product_Variation($variation_id);
  //return $product->product_custom_fields['_price'][0]; // No longer works in new version of WooCommerce
  //return $product->get_price_html(); // Works. Use this if you want the formatted price
  return $product->get_price(); // Works. Use this if you want unformatted price
}

function get_membership_variation_price($variation_id) {
  global $woocommerce; // Don't forget this!
  return number_format(WC_Subscriptions_Product::get_price( $variation_id )+WC_Subscriptions_Product::get_sign_up_fee( $variation_id ),2);
}

// get currency and apply speicific style
function get_currency_css_class() {

 $current_currency = get_woocommerce_currency();

 return 'currency-'.strtolower($current_currency);

}

// usage  [convert]399[/convert]
function convert_price( $atts, $price = 1 ) {
  return '<span>' . get_woocommerce_currency_symbol().current_ex_rate($price,0) . '</span>';
}
add_shortcode( 'convert', 'convert_price' );


function current_ex_rate($price,$dec=2) {

  $rates = get_option( 'wc_aelia_currency_switcher' );

  $current_currency = get_woocommerce_currency();

  $current_rate = $rates['exchange_rates'][$current_currency]['rate'];

  if ($current_rate==''){$current_rate=1;}

  $converted_price = $current_rate * $price;

//  error_log ($price.' x '.$current_rate.'='.$converted_price. ' FOR  '.$current_currency);

  $converted_price = number_format($converted_price,$dec);
  if ($converted_price==0) {
    $converted_price = 'Free';
  }
  return $converted_price;

}

/*
* Function to show an individual product variation's price
*
**/
// Show trailing zeros on prices, default is to hide it.
// add_filter( 'woocommerce_price_trim_zeros', 'wc_hide_trailing_zeros', 10, 1 );
// function wc_hide_trailing_zeros( $trim ) {
//     // set to false to show trailing zeros
//     return false;
// }


/**
 * Only display minimum price for WooCommerce variable products
 **/
/*
add_filter('woocommerce_variable_price_html', 'custom_variation_price', 10, 2);

function custom_variation_price( $price, $product ) {

     $price = '';


     $price .= woocommerce_price($product->get_price());

     return $price;
}
*/



  // Wrap the breadcrumbs so it's in the grid
/*
  function my_woocommerce_breadcrumbs() {
    return array(
      'delimiter'   => ' &#47 ',
      'wrap_before' => '<div class="row"><div class="small-12 column"><nav class="woocommerce-breadcrumb" ' . ( is_single() ? 'itemprop="breadcrumb"' : '' ) . '>',
      'wrap_after'  => '</nav></div></div>',
      'before'      => '',
      'after'       => '',
      'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
    );
  }

  add_filter( 'woocommerce_breadcrumb_defaults', 'my_woocommerce_breadcrumbs' );
*/

/** Get list of sub categories by parent ID
 **/

function wc_subcats_from_parentcat_by_ID($parent_cat_ID, $class = NULL, $customtitle = NULL) {
  $args = array(
     'hierarchical' => 1,
     'show_option_none' => '',
     'hide_empty' => 0,
     'parent' => $parent_cat_ID,
     'taxonomy' => 'product_cat'
  );
  $subcats = get_categories($args);
    echo '<ul class="wooc_sclist">';
      foreach ($subcats as $sc) {
        $link = get_term_link( $sc->slug, $sc->taxonomy );
          echo '<li ';
          if($class != NULL) {
            echo 'class="'. $class .'" ';
          };
          echo '><a href="'. $link .'">';
          if($customtitle != NULL) {
            $term_fid = 'product_cat_' . $sc->term_id;
            if(get_field($customtitle, $term_fid)) {
              the_field($customtitle, $term_fid);
              //echo 'custom title param not null and field has content';
            }
            else {
              echo $sc->name;
              //echo 'custom title param not null but has NO content';
            };
          } else {
            echo $sc->name;
            //echo 'custom title param null';
          };
          echo '</a></li>';
      }
    echo '</ul>';
}

function wc_subcats_from_parentcat_by_ID_option($parent_cat_ID, $class = NULL, $customtitle = NULL) {



  $args = array(
     'hierarchical' => 1,
     'show_option_none' => '',
     'hide_empty' => 0,
     'parent' => $parent_cat_ID,
     'taxonomy' => 'product_cat',
     'order_by' => 'menu_order'
  );
  $currenturl = get_url();


  $subcats = get_categories($args);
    //echo '<select>';
  $count = 0;

      if($parent_cat_ID == 97 && $subcats) {
        $link = get_term_link( $parent_cat_ID, 'product_cat' );
        echo '<option value="'. $link .'">Select Events</option>';
      }

      foreach ($subcats as $sc) {
        $link = get_term_link( $sc->slug, $sc->taxonomy );
          echo '<option value="'. $link .'" ';
          if($class != NULL) {
            echo 'class="'. $class .'" ';
          };
          //error_log($count.'--'.$parent_cat_ID);
          if($currenturl === $link ) {
            echo 'selected="selected"';
          };

          if (is_plugin_active('location-based-content/location-based-content.php') ) {
            $termMeta = get_option($sc->term_id.'-lbc' );
          }
          else {
            $termMeta = '';
          }

          echo ' data-lbc="'.$termMeta.'" ';

          echo '>';
          if($customtitle != NULL) {
            $term_fid = 'product_cat_' . $sc->term_id;
            if(get_field($customtitle, $term_fid)) {
              the_field($customtitle, $term_fid);
              //echo 'custom title param not null and field has content';
            }
            else {
              echo $sc->name;
              //echo 'custom title param not null but has NO content';
            };
          } else {
            echo $sc->name;
            //echo 'custom title param null';
          };
          echo '</option>';
          $count++;
      }
    //echo '</select>';
}

/** Get list of sub categories by parent name
 **/
function wc_subcats_from_parentcat_by_name($parent_cat_NAME) {
  $IDbyNAME = get_term_by('name', $parent_cat_NAME, 'product_cat');
  $product_cat_ID = $IDbyNAME->term_id;
    $args = array(
       'hierarchical' => 1,
       'show_option_none' => '',
       'hide_empty' => 0,
       'parent' => $product_cat_ID,
       'taxonomy' => 'product_cat'
    );
  $subcats = get_categories($args);
    echo '<ul class="wooc_sclist">';
      foreach ($subcats as $sc) {
        $link = get_term_link( $sc->slug, $sc->taxonomy );
          echo '<li><a href="'. $link .'">'.$sc->name.'</a></li>';
      }
    echo '</ul>';
}

// Cart - Basket Text

function wpa_change_my_basket_text( $translated_text, $text, $domain ){
    if( $domain == 'woothemes' && $translated_text == 'Cart:' )
        $translated_text = 'Basket:';

    return $translated_text;
}
add_filter( 'gettext', 'wpa_change_my_basket_text', 10, 3 );


function fixed_pricing($product,$convert=true) {


$pricing_rules = get_post_meta( $product->id, '_pricing_rules', true );
$price=false;

  if(is_user_logged_in() && check_user_group()) {
    //error_log('f 1 '.$product->id);
    if($pricing_rules) {
      //error_log('f 2 ');
        foreach ($pricing_rules as $rule) {
          if(isset($rule['conditions'][1]['args']['applies_to']) && $rule['conditions'][1]['args']['applies_to'] == 'groups' && $price==false) {
            //error_log('f 3 ');
            //error_log(print_r($rule,1));
            $price = $rule['rules'][1]['amount'];
            $found=true;
          }
        }
    } else {
      //error_log('f 4 ');
      $price = $product->get_price();
    }
  } else {
    //error_log('f 5 ');
    $price = $product->get_price();
    $convert=false;
  }

  if ($price == 0) {
    return '<span class="price">Free!</span>';
  } else {
    $price = number_format($price,2);
    if ($convert) {
      $price = get_woocommerce_currency_symbol().current_ex_rate($price);
    } else {
      $price = get_woocommerce_currency_symbol().($price);
    }
    return '<span class="price">'.$price.'</span>';
  }


}


function check_user_group($price_rule=null) {
// Check what groups the current user is part of

$user_id = get_current_user_id();
$user = new Groups_User( $user_id );
$groups = $user->__get( 'groups' );

$found=false;
$price_rule = array(2,3,4,5,6);

 foreach ($groups as $group) {

    $user_group_id=$group->group->group_id;

    if (is_array($price_rule)) {
      if (in_array($user_group_id,$price_rule)) {
        $found = true;
      }
    }
  }
 return $found;
}



/** Show dynamic price on product page
 **/

add_filter( 'woocommerce_available_variation', 'f10_wc_available_variation',1 );
function f10_wc_available_variation( $variations ) {

//error_log('1'.$variations['price_html'] );

global $woocommerce, $product, $post;

$isacca = false;

$categories = get_the_terms ( $product->ID, 'product_cat');

// check if product is in ACCA
foreach ($categories as $category) {
  if ($category->term_id == 84 || $category->parent==84) {
    $isacca = true;
  } else {
    $isacca = false;
  }
}

$key = 'variation_id';
$val = $variations[$key];

$pricing_rules = get_post_meta( $post->ID, '_pricing_rules', true );
$found = false;
$product_has_date_variants = false;
$subscriber = false;

if(is_user_logged_in() && check_user_group() ) {

    if($pricing_rules) :

    foreach ($pricing_rules as $rule) {

      $var_arrays = $rule['variation_rules']['args']['variations'];

      if(!is_array($var_arrays)) {
        $var_arrays = array($var_arrays);
      }

      $key = 'variation_id';
      $val = $variations[$key];
      $today = date('Y-m-d');

      if($found == false) {

        if(isset($rule['conditions'][1]['args']['applies_to']) && $rule['conditions'][1]['args']['applies_to'] == 'groups' ) {

          if(in_array($val, $var_arrays)) {

            //error_log('VARS  :'.$val.'--'.print_r($var_arrays,1));
            $datefrom = $rule['date_from'];
            $dateto = $rule['date_to'];

            //error_log($datefrom.'-->>'.$dateto);

            if ($datefrom !='' || $dateto != '') {
              $product_has_date_variants = true;


              if ($dateto != '' && $datefrom == '') {
                if($today < $dateto) {
                  //error_log(print_r('before date to',1));
                  if($rule['rules'][1]['from'] != '' && $rule['rules'][1]['from'] <= 1 ) {
                  //error_log(print_r('has both to and from',1));
                  $dynprice = $rule['rules'][1]['amount'];
                  $found = true;
                  //error_log(print_r($dynprice,1));
                  } elseif ($rule['rules'][1]['from'] != '') {

                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                    //error_log(print_r($dynprice,1));

                  } elseif ($rule['rules'][1]['to'] != '') {

                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                    //error_log(print_r($dynprice,1));

                  } else {

                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                    //error_log(print_r($dynprice,1));
                  }

                }

              } elseif($datefrom != '') {
                if($today > $datefrom) {
                  if($dateto != '') {
                    if($today < $dateto) {
                      //error_log(print_r('has before date and date from',1));
                      if($rule['rules'][1]['from'] != '' && $rule['rules'][1]['from'] <= 1 ) {
                        //error_log(print_r('has both to and from',1));
                        $dynprice = $rule['rules'][1]['amount'];
                        $found = true;
                        //error_log(print_r($dynprice,1));
                      } elseif ($rule['rules'][1]['from'] != '') {

                        $dynprice = $rule['rules'][1]['amount'];
                        $found = true;
                        //error_log(print_r($dynprice,1));

                      } elseif ($rule['rules'][1]['to'] != '') {

                        $dynprice = $rule['rules'][1]['amount'];
                        $found = true;
                        //error_log(print_r($dynprice,1));

                      } else {

                        $dynprice = $rule['rules'][1]['amount'];
                        $found = true;
                        //error_log(print_r($dynprice,1));
                      }
                    } else {

                    //error_log(print_r('after date from',1));

                    if($rule['rules'][1]['from'] != '' && $rule['rules'][1]['from'] <= 1 ) {

                      $dynprice = $rule['rules'][1]['amount'];
                      $found = true;
                      //error_log(print_r($dynprice,1));
                    } elseif ($rule['rules'][1]['from'] != '') {

                      $dynprice = $rule['rules'][1]['amount'];
                      $found = true;
                      //error_log(print_r($dynprice,1));

                    } elseif ($rule['rules'][1]['to'] != '') {

                      $dynprice = $rule['rules'][1]['amount'];
                      $found = true;
                      //error_log(print_r($dynprice,1));

                    } else {

                      $dynprice = $rule['rules'][1]['amount'];
                      $found = true;
                      //error_log(print_r($dynprice,1));
                    }
                  }
                } else {

                  if($rule['rules'][1]['from'] != '' && $rule['rules'][1]['from'] <= 1 ) {
                    //error_log(print_r('has both to and from',1));
                    //error_log('RULES : '.$dateto.'=='.$datefrom);
                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                    //error_log(print_r($dynprice,1));
                  } elseif ($rule['rules'][1]['from'] != '') {

                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                   // error_log(print_r($dynprice,1));

                  } elseif ($rule['rules'][1]['to'] != '') {

                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                    //error_log(print_r($dynprice,1));

                  } else {

                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                    //error_log(print_r($dynprice,1));
                  }
                }
              }
            } else {
              // no dates ?

            }
          } else {
             // NOT in var Arrays
          }
        } else {
          // has simple variants for single products

          $dynprice = $rule['rules'][1]['amount'];

          // $qtyfrom = $rule['rule'][1]['from'];
          // $qtyto = $rule['rule'][1]['to'];

          // if ($qtyfrom > 1) {
          //   $dynprice = $rule['rules'][1]['amount'];
          // }

        }
      }
    }
  }


    // If we have variants but no date fields
    foreach ($pricing_rules as $rule) {

      if(isset($rule['conditions'][1]['args']['applies_to']) && $rule['conditions'][1]['args']['applies_to'] == 'groups' && $found==false) {

        $var_arrays = $rule['variation_rules']['args']['variations'][0];

        if($val == $rule['variation_rules']['args']['variations'][0]) {

          $datefrom = $rule['date_from'];
          $dateto = $rule['date_to'];

          if ($dateto =='' && $datefrom =='') {
            $dynprice = $rule['rules'][1]['amount'];
            //$dynprice = current_ex_rate($dynprice);
            $found=true;

          }
        }
      }
    }


    if ((int)$dynprice > 0) {
      $dynprice = get_woocommerce_currency_symbol().current_ex_rate($dynprice);
    } else {
      $dynprice = 'Free';
    }

    $variations['price_html'] = '<span class="price">'.$dynprice.'</span>';

  endif;

} else {   // NOT logged in

  if($pricing_rules) :

    //error_log(print_r($pricing_rules,1));

    foreach ($pricing_rules as $rule) {

      $var_arrays = $rule['variation_rules']['args']['variations'];

      if(!is_array($var_arrays)) {
        $var_arrays = array($var_arrays);
      }

      $key = 'variation_id';
      $val = $variations[$key];
      $today = date('Y-m-d');

      if($found == false) {

        if(isset($rule['conditions'][1]['args']['applies_to']) && $rule['conditions'][1]['args']['applies_to'] == 'everyone') {

          if(in_array($val, $var_arrays)) {

            //error_log('VARS  :'.$val.'--'.print_r($var_arrays,1));
            $datefrom = $rule['date_from'];
            $dateto = $rule['date_to'];

            //error_log($datefrom.'-->>'.$dateto);

            if ($datefrom !='' || $dateto != '') {
              $product_has_date_variants = true;


              if ($dateto != '' && $datefrom == '') {
                if($today < $dateto) {
                  //error_log(print_r('before date to',1));
                  if($rule['rules'][1]['from'] != '' && $rule['rules'][1]['from'] <= 1 ) {
                  //error_log(print_r('has both to and from',1));
                  $dynprice = $rule['rules'][1]['amount'];
                  $found = true;
                  //error_log(print_r($dynprice,1));
                  } elseif ($rule['rules'][1]['from'] != '') {

                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                    //error_log(print_r($dynprice,1));

                  } elseif ($rule['rules'][1]['to'] != '') {

                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                    //error_log(print_r($dynprice,1));

                  } else {

                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                    //error_log(print_r($dynprice,1));
                  }

                }

              } elseif($datefrom != '') {
                if($today > $datefrom) {
                  if($dateto != '') {
                    if($today < $dateto) {
                      //error_log(print_r('has before date and date from',1));
                      if($rule['rules'][1]['from'] != '' && $rule['rules'][1]['from'] <= 1 ) {
                        //error_log(print_r('has both to and from',1));
                        $dynprice = $rule['rules'][1]['amount'];
                        $found = true;
                        //error_log(print_r($dynprice,1));
                      } elseif ($rule['rules'][1]['from'] != '') {

                        $dynprice = $rule['rules'][1]['amount'];
                        $found = true;
                        //error_log(print_r($dynprice,1));

                      } elseif ($rule['rules'][1]['to'] != '') {

                        $dynprice = $rule['rules'][1]['amount'];
                        $found = true;
                        //error_log(print_r($dynprice,1));

                      } else {

                        $dynprice = $rule['rules'][1]['amount'];
                        $found = true;
                        //error_log(print_r($dynprice,1));
                      }
                    } else {

                    //error_log(print_r('after date from',1));

                    if($rule['rules'][1]['from'] != '' && $rule['rules'][1]['from'] <= 1 ) {

                      $dynprice = $rule['rules'][1]['amount'];
                      $found = true;
                      //error_log(print_r($dynprice,1));
                    } elseif ($rule['rules'][1]['from'] != '') {

                      $dynprice = $rule['rules'][1]['amount'];
                      $found = true;
                      //error_log(print_r($dynprice,1));

                    } elseif ($rule['rules'][1]['to'] != '') {

                      $dynprice = $rule['rules'][1]['amount'];
                      $found = true;
                      //error_log(print_r($dynprice,1));

                    } else {

                      $dynprice = $rule['rules'][1]['amount'];
                      $found = true;
                      //error_log(print_r($dynprice,1));
                    }
                  }
                } else {

                  if($rule['rules'][1]['from'] != '' && $rule['rules'][1]['from'] <= 1 ) {
                    //error_log(print_r('has both to and from',1));
                    //error_log('RULES : '.$dateto.'=='.$datefrom);
                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                    //error_log(print_r($dynprice,1));
                  } elseif ($rule['rules'][1]['from'] != '') {

                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                   // error_log(print_r($dynprice,1));

                  } elseif ($rule['rules'][1]['to'] != '') {

                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                    //error_log(print_r($dynprice,1));

                  } else {

                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                    //error_log(print_r($dynprice,1));
                  }
                }
              }
            } else {
              // no dates ?

            }
          } else {
             // NOT in var Arrays
          }
        } else {
          // has simple variants for single products

          $dynprice = $rule['rules'][1]['amount'];

          // $qtyfrom = $rule['rule'][1]['from'];
          // $qtyto = $rule['rule'][1]['to'];

          // if ($qtyfrom > 1) {
          //   $dynprice = $rule['rules'][1]['amount'];
          // }

        }
      }
    }
  }


    // If we have variants but no date fields
    foreach ($pricing_rules as $rule) {

      if(isset($rule['conditions'][1]['args']['applies_to']) && $rule['conditions'][1]['args']['applies_to'] == 'everyone' && $found==false) {

        $var_arrays = $rule['variation_rules']['args']['variations'][0];

        if($val == $rule['variation_rules']['args']['variations'][0]) {

          $datefrom = $rule['date_from'];
          $dateto = $rule['date_to'];

          if ($dateto =='' && $datefrom =='') {
            $dynprice = $rule['rules'][1]['amount'];
            //$dynprice = current_ex_rate($dynprice);
            $found=true;

          }
        }
      }
    }


    if ((int)$dynprice > 0) {
      $dynprice = get_woocommerce_currency_symbol().current_ex_rate($dynprice);
    } else {
      $dynprice = 'Free';
    }

    $variations['price_html'] = '<span class="price">'.$dynprice.'</span>';

  endif;

}

//error_log('2'.$variations['price_html'] );
return $variations;

}



/** Adjust subscription string text
 **/

function my_subs_price_string( $pricestring ) {
    if (is_admin() ) {
      return $pricestring;
    }
    $removethou  = str_replace( '1,', '1', $pricestring );
    $removeyear  = str_replace( 'for 1 year', '|', $removethou );
    $removesignup = str_replace( 'sign-up fee', '', $removeyear );
    $removeanda  = str_replace( 'and a', '', $removesignup );
    $removecomma  = str_replace( ',', '', $removeanda );
    $newpound    = str_replace( '£', '', $removecomma );
    $wioutpound2 = strip_tags($newpound);
    $wioutpound  = preg_replace('/\s+/', '', $wioutpound2);
    $wioutpound  = str_replace(get_woocommerce_currency_symbol(),'',$wioutpound);
    $wioutpound  = array_map('floatval', explode('|', $wioutpound));
    $wioutpound = array_sum($wioutpound);

    if ( is_cart() || is_checkout() || is_ajax() ) {
      $newprice = get_woocommerce_currency_symbol().number_format((float)$wioutpound,2).' for 1 year';
    } else {
      $newprice = get_woocommerce_currency_symbol().number_format((float)$wioutpound,2).' for 1 year';
    }
  return $newprice;

}
add_filter( 'woocommerce_subscriptions_product_price_string', 'my_subs_price_string' );
add_filter( 'woocommerce_subscription_price_string', 'my_subs_price_string' );




add_filter('woocommerce_get_price','price_text');
function price_text($price) {

 if ( !is_user_logged_in() && is_string($price) && $price !== 0 ){
    $update = $price;
    $price = $update;
    //echo $price.'<br>';

    return $price;
  } else {
    return $price;
  }
}

/** Hide Cart Prices on restricted
 **/

add_action('after_setup_theme','activate_filter') ;

function activate_filter(){
  add_filter('woocommerce_get_price_html', 'show_price_logged');
}



function show_price_logged($price){


  if(is_user_logged_in() ){

    /*global $woocommerce, $product, $post;
    $pricing_groups = get_post_meta( $post->ID, '_pricing_rules', true );

    error_log(print_r($pricing_groups,1));

    if($pricing_groups != '') {
      $price = 'Has dynamic pricing ' . $price;
      //error_log(print_r('dynamic here',1));
      error_log(print_r($price,1));
    }*/
  // return $price;

  }
  else
  {
      $restrict = get_field('restrict_product');
      //error_log(print_r($restrict,1));
    if($restrict  == true) {
      remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
      remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
      remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
      remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

      $ctablock = '<div class="cta-block side-block text-block texturebg add-cart-block">
              <h2>Not yet a member?</h2>
              <h4>Become a member to purchase this product</h4>
              <p>Join 1000\'s of other accountancy professionals. Benefit from our wealth of knowledge, tools, tips and downloads now.</p>
              <a href="'. get_option('home') . '/2020-membership-in-practice/" class="gen-btn orange icon trophy">Join Now</a> Already a member? <a href="/login?ref='.urlencode($_SERVER['REQUEST_URI']).'">Login now</a></div>';

     // return $ctablock;



    }
    else {
      //hide all pricing if not free and not logged in


    //  return 'Price !';
    }
  }

return $price;

}

function max_seats_for_current_subscriptions($output = false) {

  $_i_subscription_details = wcs_get_users_subscriptions( $user_id );
  //error_log(print_r($_ini_subscription_details[key($_ini_subscription_details)]->order->id,1));
  $subs_order_id = $_i_subscription_details[key($_i_subscription_details)]->order->id;
  $subs_order = new WC_Order($subs_order_id);
  $subs_order_items = $subs_order->get_items();
  $subscription_key = WC_Subscriptions_Manager::get_subscription_key( $subs_order_id, $subs_order_items[key($subs_order_items)]['product_id']);
  $subs = WC_Subscriptions_Manager::get_subscription($subscription_key);


  $subs = wcs_get_users_subscriptions();

  if($subs) :
  foreach ($subs as $sub) {
    //var_dump($sub);
    if($sub['variation_id'] != '') {
      $meta_values = get_post_meta( $sub['variation_id'], 'attribute_partners', true );
      if($meta_values == '2-5-partners') {
        $maxseats[] = 5;
      }
      elseif($meta_values == '6-9-partners') {
        $maxseats[] = 9;
      }
      elseif($meta_values == '10-15-partners') {
        $maxseats[] = 15;
      }
    }
    else {
      $post_data = get_post($sub['product_id'], ARRAY_A);
      $meta_values = $post_data['post_name'];
      $maxseats[] = 1;
    }
  }
  $maxseats = max($maxseats);
  if($output == true) {
    echo $maxseats;
  } else {
    return $maxseats;
  };
  endif;
  //var_dump($subs);
}

// Simple products
function jk_woocommerce_quantity_input_args( $args, $product ) {

    $qtyrestrict = get_field('restrict_quantity');

    if($qtyrestrict) {
      //$args['input_value']  = 2;  // Starting value
      $args['max_value'] = max_seats_for_current_subscriptions();   // Maximum value
      //$args['min_value']    = 2;    // Minimum value
      //$args['step']     = 2;    // Quantity steps
    }
    return $args;
}
// Variations
function jk_woocommerce_available_variation( $args ) {

  $qtyrestrict = get_field('restrict_quantity');
  if($qtyrestrict) {
    $args['max_qty'] = max_seats_for_current_subscriptions();  // Maximum value (variations)
    //$args['min_qty'] = 2;     // Minimum value (variations)
  }
  return $args;
}


add_filter( 'woocommerce_available_variation', 'jk_woocommerce_available_variation' );
add_filter( 'woocommerce_quantity_input_args', 'jk_woocommerce_quantity_input_args', 10, 2 );


//add_action ('woocommerce_after_add_to_cart_button','show_custom_add_to_basket_content');
function show_custom_add_to_basket_content(){
  echo '<div class="currency-label">';
  the_field('currency_label',5);
  echo '</div>';
}


// Add the function to a specific action hook in WooCommerce, http://docs.woothemes.com/document/hooks/
add_action( 'woocommerce_after_add_to_cart_form', 'show_pricing_rules' );

function show_pricing_rules() {

    global $woocommerce, $product, $post;

    $isacca = false;

    $categories = get_the_terms ( $product->ID, 'product_cat');

    foreach ($categories as $category) {
      if ($category->term_id == 97 || $category->parent==97) {
        $memberslabel = 'ACCA Discount';
        $isacca = true;
      } else {
        $memberslabel = 'Members';
      }
     }

    if( $product->has_child() ) { // this is a variable product

        // get the pricing groups
        $pricing_groups = get_post_meta( $post->ID, '_pricing_rules', true );

        $attributes = $product->get_attributes();

        //print_r ($attributes);

        //echo '<br><br><br>';

        $attname = '';

        $labels = array();

        foreach ( $attributes as $key=>$attribute ) {
                //$attname = $attribute['name'];
                //$attrname = str_replace("-", " ", $attname); // replace dashed with spaces
                //$attname = ucwords($variation_name); // uppercase the first letter of a word
                $attname = 'attribute_'.$key;

                $labels = array_merge($labels,explode('|',$attributes[$key]['value']));

        }


        foreach ($labels as $label) {

          $new_labels[$label] = sanitize_title($label);

        }

        //error_log(print_r($attributes,1));

        //error_log(print_r($out,1));

        // reset variables
        $table_count = 0;

        //error_log(print_r($pricing_groups,1));

        $groups = array();

        $lines = array();
        if (isset($pricing_groups) && is_array($pricing_groups)) {
          foreach ($pricing_groups as $pricing_rules) {

            //error_log(print_r($pricing_rules,1));

            $var_ids = '';
            $cfs = '';
            $attribute_slugs = array();
            $attr_slugs = '';
            $variation_names = '';
            $name = '';

            // get the product variation IDs
            $var_ids = $pricing_rules['variation_rules']['args']['variations'];
            $datefrom = $pricing_rules['date_from'];
            $dateto = $pricing_rules['date_to'];


            if($var_ids) :
            // getting the product variation name(s) that this pricing rule is assigned to
              foreach ($var_ids as $var_id) {
                  $cfs = get_post_custom($var_id); // get all of the custom fields for this product variation
                  $attribute_slugs[] = $cfs[$attname]; // the last entry in the $cfs array contains the slug(s) of the attributes value(s) that this pricing rule is applied to

                  // the key of the last array in $cfs is 'atrribute_pa_sex', 'sex' being the attribute name created by us
                  // thus, the values of this array may also be attained by:
                  // $attribute_slugs[] = $cfs['attribute_pa_sex'];
              };
            endif;

            if($attribute_slugs) :

            $delegates = false;
            //error_log(print_r($attribute_slugs,1));
            // getting the array values from inside the main array
              foreach ($attribute_slugs as $attr_slugs) {

                  // cleaning up the slugs, creating array with clean variation names for this pricing rule
                  foreach ($attr_slugs as $attr_slug) {
                      if($attr_slug != '1' && $attr_slug != '3-9') {
                        $variation_name = str_replace("-", " ", $attr_slug); // replace dashed with spaces
                        $variation_names[] = ucwords($variation_name); // uppercase the first letter of a word
                      }
                      else {
                        $delegates = true;
                        if($attr_slug == '1') {
                          $variation_name = $attr_slug.' Delegate place';
                          $variation_names[] = $variation_name;
                        }
                        else {
                          $variation_name = $attr_slug.' Delegate places';
                          $variation_names[] = $variation_name;
                        }
                      }

                  }
              }
            endif;
            //error_log(print_r($attribute_slugs,1));

            ////////////////////////////////////

            $line = array ('price'  => $pricing_rules['rules'][1]['amount'],
                           'qty'    => $pricing_rules['rules'][1]['from'],
                           'date'   => $pricing_rules['date_to'],
                           'target' => $pricing_rules['conditions'][1]['args']['applies_to'],
                           'name'   => $variation_names[0]
                          );

            //print_r($line);


              // bulk price
              if (isset($pricing_rules['rules'][1]['from']) && $pricing_rules['rules'][1]['from'] > 1) {
                if ($pricing_rules['conditions'][1]['args']['applies_to'] == 'everyone') {
                  $lines[$pricing_rules['variation_rules']['args']['variations'][0]]['bulk']['everyone'] = $line;
                }
                else {
                  $lines[$pricing_rules['variation_rules']['args']['variations'][0]]['bulk']['member'] = $line;
                }
              }
              // early bird
              elseif ($pricing_rules['date_to'] != '') {
                if ($pricing_rules['conditions'][1]['args']['applies_to'] == 'everyone') {
                  $lines[$pricing_rules['variation_rules']['args']['variations'][0]]['early_bird']['everyone'] = $line;
                }
                else {
                  $lines[$pricing_rules['variation_rules']['args']['variations'][0]]['early_bird']['member'] = $line;
                }
              }
              // standard
              else {
                if ($pricing_rules['conditions'][1]['args']['applies_to'] == 'everyone') {
                  $lines[$pricing_rules['variation_rules']['args']['variations'][0]]['standard']['everyone'] = $line;
                }
                else {
                  $lines[$pricing_rules['variation_rules']['args']['variations'][0]]['standard']['member'] = $line;
                }
              }




              /////////////////////////////////////

            $grouptype = $pricing_rules['conditions'][1]['args'];

            foreach ($grouptype as $groupt['applies_to']) {
              if($groupt['applies_to'] == 'everyone') {
                //error_log(print_r('Everyone',1));
              } else {
                //error_log(print_r('Not everyone',1));
              }

              if(is_array($groupt['applies_to'])) {
                $groups[] = 'groups';
              } else {
                $groups[] = $groupt['applies_to'];
              }

              //error_log(print_r($groupt,1));
              //$groups[] = $groupt;

            }

            $groups = array_unique($groups);

            //error_log(print_r($groups,1));

          }
        }


        //error_log(print_r($lines,1));

        ///////////////////////////

        if(!is_user_logged_in() && !$isacca){
          $html .= '<div class="text-left" id="login-prompt">Already a Member? <a href="/login?ref='.urlencode($_SERVER['REQUEST_URI']).'">Login now</a> to access appropriate pricing.</div>';
        }

        $html .= '<div class="conf-tab-wrap">
                <table class="general-table ann-conf-tab" border="0" width="100%" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                <td></td>';
        $html .= '<td>'.$memberslabel.'</td>';
        if (!$isacca) {
          $html .= '<td>Non Members</td></tr>';
        } else {
          $html .= '</tr>';
        }

                $keys = array();

                //print_r($lines);

                foreach ($lines as $k=>$line) {
                  $keys[]= $k;
                }

                if(isset($lines[$keys[0]]) && is_array($lines[$keys[0]])) {
                  foreach ($lines[$keys[0]] as $k=>$line) {


                      $at = get_post_meta($k,'attribute_type',true);

                      if ($k == 'early_bird') {
                        $title = '<strong>Early bird discount</strong>
                                If booking on or before '.date('j<\s\u\p>S</\s\u\p> F Y',strtotime($lines[$keys[0]]['early_bird']['everyone']['date']));

                      }
                      else if ($k == 'bulk') {
                        $title = '<strong>Multi-participant discount</strong>
                                If booking '.$lines[$keys[0]]['bulk']['everyone']['qty'].' or more delegates from the same firm:';
                      }
                      else {
                        if($lines[$keys[0]]['early_bird']['everyone']['date'] == '') {
                          if($lines[$keyi][$k]['member']['name'] == '') {
                            $title = get_the_title($id);
                          } else {
                            $title = $lines[$keyi][$k]['member']['name'];
                          }
                        }
                        else {
                          if($lines[$keys[0]]['early_bird']['everyone']['date'] == '') {
                            if($lines[$keyi][$k]['member']['name'] == '') {
                              $title = get_the_title($id);
                            } else {
                              $title = $lines[$keyi][$k]['member']['name'];
                            }
                          } else {
                            $title = 'If booking after '.date('j<\s\u\p>S</\s\u\p> F Y',strtotime($lines[$keys[0]]['early_bird']['everyone']['date']));
                          }

                        }
                      }
                      $count = 0;
                      foreach ($keys as $keyi) {

                        if ($delegates) {
                          $title .= '<br />'.$lines[$keyi][$k]['member']['name'];

                        } else {
                          foreach ($new_labels as $new=>$old) {
                            //echo $new.' || '.$old.' || '.sanitize_title($lines[$keyi][$k]['member']['name']).'<br>';



                              if(trim($new) != '1' && trim($new) != '3-9') {
                                //echo $new;
                              }
                              else {
                                if(trim($new) == '1') {
                                  $new = '1 Delegate place';
                                }
                                else {
                                  $new_temp = $new.' Delegate places';
                                  $new = $new_temp;
                                }
                              }




                            $count++;

                           if (sanitize_title($lines[$keyi][$k]['member']['name'])==$old) {

                              $licence_name=str_replace('-',' ',get_post_meta($keyi,'attribute_licence-type',true));

                              if ($licence_name) {
                                  $title .= '<br />'.ucfirst($licence_name).' - '.$new;
                              } else {
                                // swap this for correct compare attribute ID ?
                                if (trim($new) !== 'This booking is for me') {
                                  $title .= '<br />'.$new;
                                }
                              }
                            } else {
                              if (strpos($new, 'Delegate')>0 && $count <3){
                              //$title .= '<br />'.$lines[$keyi][$k]['member']['name'];
                                $title .= '<br />'.$new;
                              }
                            }
                          }
                        }
                      }

                      $html .='
                          <tr>
                          <td>'.$title.'</td>';
                          $html_temp = array('member'=>'','everyone'=>'');
                          foreach ($keys as $keyi) {
                            foreach ($html_temp as $html_key=>$html_val) {
                              if($lines[$keyi][$k][$html_key]['price'] == '') {
                                $altprice = $product->get_price();
                                if($altprice) {
                                  $price = get_woocommerce_currency_symbol().current_ex_rate($altprice);
                                }
                                else {
                                  $price = 'N/A';
                                }
                              } else {
                                if((int)$lines[$keyi][$k][$html_key]['price'] > 0) {
                                  $price = $lines[$keyi][$k][$html_key]['price'];
                                  $price = current_ex_rate($price);
                                  $price = get_woocommerce_currency_symbol().$price; //$lines[$keyi][$k][$html_key]['price'];
                                } else {
                                  $price = 'Free';
                                }
                              }

                              $html_temp[$html_key] .= '<br />'.$price;
                            }
                          }

                          $i=1;
                          foreach ($html_temp as $html_temp_text){
                            if ($i==2 && $isacca) {
                            } else {
                              $html .= '<td>'.$html_temp_text.'</td>';
                            }
                            $i++;
                          }

                        $html .='</tr>
                          ';
                  }
                }
                //error_log(print_r($price,1));
                $html .='
        </tbody>
        </table>
        </div>';


        if($pricing_groups) {
          echo $html;
        }

        ////////////////////////////







    } else { // this is a simple product, no variations

        // get all of the the pricing groups
        $id = get_the_id();
        $pricing_groups = get_post_meta( $id, '_pricing_rules', true );

        $bundled_items = get_post_meta( $id, '_bundle_data', true );

        $groups = array();

        $lines = array();

        if($pricing_groups) {

          foreach ($pricing_groups as $pricing_rules) {

            //error_log(print_r($pricing_rules,1));

            $var_ids = '';
            $cfs = '';
            $attribute_slugs = '';
            $attr_slugs = '';
            $variation_names = '';
            $name = '';

            // get the product variation IDs
            //$var_ids = $pricing_rules['variation_rules']['args']['variations'];
            $datefrom = $pricing_rules['date_from'];
            $dateto = $pricing_rules['date_to'];

            /* getting the product variation name(s) that this pricing rule is assigned to
            foreach ($var_ids as $var_id) {
                $cfs = get_post_custom($var_id); // get all of the custom fields for this product variation
                $attribute_slugs[] = end($cfs); // the last entry in the $cfs array contains the slug(s) of the attributes value(s) that this pricing rule is applied to

                // the key of the last array in $cfs is 'atrribute_pa_sex', 'sex' being the attribute name created by us
                // thus, the values of this array may also be attained by:
                // $attribute_slugs[] = $cfs['attribute_pa_sex'];
            } */


            /* getting the array values from inside the main array
            foreach ($attribute_slugs as $attr_slugs) {

                // cleaning up the slugs, creating array with clean variation names for this pricing rule
                foreach ($attr_slugs as $attr_slug) {
                    $variation_name = str_replace("-", " ", $attr_slug); // replace dashed with spaces
                    $variation_names[] = ucwords($variation_name); // uppercase the first letter of a word
                }
            } */
            //error_log(print_r($variation_names,1));

            //error_log(print_r($pricing_rules,1));

            ////////////////////////////////////
            //error_log(print_r('____________________',1));
            //error_log(print_r($pricing_rules['rules'][1]['amount'], 1));
            //error_log(print_r('____________________',1));

            $line = array ('price'  => $pricing_rules['rules'][1]['amount'],
                           'qty'    => $pricing_rules['rules'][1]['from'],
                           'date'   => $pricing_rules['date_to'],
                           'target' => $pricing_rules['conditions'][1]['args']['applies_to'],
                           'name'   => get_the_title($id)
                          );



              // bulk price
              if (isset($pricing_rules['rules'][1]['from']) && $pricing_rules['rules'][1]['from'] > 1) {
                if ($pricing_rules['conditions'][1]['args']['applies_to'] == 'everyone') {
                  $lines[$pricing_rules['variation_rules']['args']['variations'][0]]['bulk']['everyone'] = $line;
                }
                else {
                  $lines[$pricing_rules['variation_rules']['args']['variations'][0]]['bulk']['member'] = $line;
                }
              }
              // early bird
              elseif ($pricing_rules['date_to'] != '') {
                if ($pricing_rules['conditions'][1]['args']['applies_to'] == 'everyone') {
                  $lines[$pricing_rules['variation_rules']['args']['variations'][0]]['early_bird']['everyone'] = $line;
                }
                else {
                  $lines[$pricing_rules['variation_rules']['args']['variations'][0]]['early_bird']['member'] = $line;
                }
              }
              // standard
              else {
                if ($pricing_rules['conditions'][1]['args']['applies_to'] == 'everyone') {
                  $lines[$pricing_rules['variation_rules']['args']['variations'][0]]['standard']['everyone'] = $line;
                }
                else {
                  $lines[$pricing_rules['variation_rules']['args']['variations'][0]]['standard']['member'] = $line;
                }
              }




              /////////////////////////////////////

            $grouptype = $pricing_rules['conditions'][1]['args'];

            foreach ($grouptype as $groupt['applies_to']) {
              if($groupt['applies_to'] == 'everyone') {
                //error_log(print_r('Everyone',1));
              } else {
                //error_log(print_r('Not everyone',1));
              }

              if(is_array($groupt['applies_to'])) {
                $groups[] = 'groups';
              } else {
                $groups[] = $groupt['applies_to'];
              }

              //error_log(print_r($groupt,1));
              //$groups[] = $groupt;

            }

            $groups = array_unique($groups);

            //error_log(print_r($groups,1));

          }
        }


        //error_log(print_r($lines,1));

        ///////////////////////////
        if(!is_user_logged_in()  && !$isacca){
          $html .= '<div class="text-left" id="login-prompt">Already a Member? <a href="/login?ref='.urlencode($_SERVER['REQUEST_URI']).'">Login now</a> to access appropriate pricing.</div>';
        }

        $html .= '<div class="conf-tab-wrap">
                <table class="general-table ann-conf-tab" border="0" width="100%" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                <td></td>';
        $html .='<td>'.$memberslabel.'</td>';
        if (!$isacca) {
          $html .= '<td>Non Members</td></tr>';
        } else {
          $html .= '</tr>';
        }

                $keys = array();

                if($lines) {


                    foreach ($lines as $k=>$line) {
                      $keys[]= $k;
                    }




                    foreach ($lines[$keys[0]] as $k=>$line) {


                        $at = get_post_meta($k,'attribute_type',true);

                        if ($k == 'early_bird') {
                          $title = '<strong>Early bird discount</strong>
                                  If booking on or before '.date('j<\s\u\p>S</\s\u\p> F Y',strtotime($lines[$keys[0]]['early_bird']['everyone']['date']));

                        }
                        else if ($k == 'bulk') {
                          $title = '<strong>Multi-participant discount</strong>
                                  If booking '.$lines[$keys[0]]['bulk']['everyone']['qty'].' or more delegates from the same firm:';
                        }
                        else {
                          if($lines[$keys[0]]['early_bird']['everyone']['date'] == '') {
                            $title = '';
                          }
                          else {
                            if($lines[$keys[0]]['early_bird']['everyone']['date'] == '') {
                              $title = $lines[$keyi][$k]['member']['name'];
                            } else {
                              $title = 'If booking after '.date('j<\s\u\p>S</\s\u\p> F Y',strtotime($lines[$keys[0]]['early_bird']['everyone']['date']));
                            }

                          }

                        }
                        foreach ($keys as $keyi) {

                          if ($bundled_items) {
 //                           $title .= '<br />'.$lines[$keyi][$k]['member']['name'];
                          }

                          if ($delegates||$bundled_items) {
                            $title .= '<br />'.$lines[$keyi][$k]['member']['name'];
                          } else {
                            if (isset($new_labels)) {
                              foreach ($new_labels as $new=>$old) {
                                //echo $new.' || '.$old.' || '.sanitize_title($lines[$keyi][$k]['member']['name']).'<br>';
                                $count++;
                                if (sanitize_title($lines[$keyi][$k]['member']['name'])==$old) {

                                  $licence_name=str_replace('-',' ',get_post_meta($keyi,'attribute_licence-type',true));

                                  if ($licence_name) {
                                      $title .= '<br />'.ucfirst($licence_name).' - '.$new;
                                  } else {
                                    // swap this for correct compare attribute ID ?
                                    if (trim($new) !== 'This booking is for me') {
                                      $title .= '<br />'.$new;
                                    }
                                  }
                                } else {
                                  $title .= '<br />'.$lines[$keyi][$k]['member']['name'];
                                }
                              }
                            } else {
                              $title .= '<br />'.$lines[$keyi][$k]['member']['name'];
                            }
                          }



                        }

                        $html .='
                            <tr>
                            <td>'.$title.'</td>';
                            $html_temp = array('member'=>'','everyone'=>'');
                            foreach ($keys as $keyi) {
                              foreach ($html_temp as $html_key=>$html_val) {
                                //error_log(print_r($html_key,1));
                                if($lines[$keyi][$k][$html_key]['price'] == '')
                                  $html_temp[$html_key] .= '<br/>N/A';
                                elseif((int)$lines[$keyi][$k][$html_key]['price'] > 0) {
                                  $temp_price = current_ex_rate($lines[$keyi][$k][$html_key]['price']);
                                  $html_temp[$html_key] .= '<br />'.get_woocommerce_currency_symbol().$temp_price;//$lines[$keyi][$k][$html_key]['price'];
                                }
                                else {
                                  $html_temp[$html_key] .= '<br />Free';
                                }


                              }
                            }
                            $i=1;
                            foreach ($html_temp as $html_temp_text){

                               if ($i==2 && $isacca) {
                               } else {
                                  $html .= '<td>'.$html_temp_text.'</td>';
                               }
                              $i++;

                            }

                          $html .='</tr>
                            ';
                    }
                }
                $html .='
        </tbody>
        </table>
        </div>';

        if($pricing_groups) {
          echo $html;
        }







    }
}

// cart - basket JS translation
add_filter( 'wc_add_to_cart_params', 'jscarttobasket', 10, 1 );

function jscarttobasket( $localized ) {
  $localized['i18n_view_cart'] = __( 'View Basket', 'woocommerce' );
  return $localized;
}
add_filter( 'woocommerce_add_message', 'carttobasket', 10, 1 );
add_filter( 'woocommerce_add_error', 'carttobasket', 10, 1 );
add_filter( 'woocommerce_add_notice', 'carttobasket', 10, 1 );

function carttobasket( $message ) {
  $message = str_replace( 'Cart', 'Basket', $message );
  $message = str_replace( 'cart', 'basket', $message );
  $message = str_replace( 'add-to-basket', 'add-to-cart', $message );
  return $message;
}

add_filter( 'woocommerce_add_error', 'isrequired', 10, 1 );
function isrequired( $message ) {
  $message = str_replace( '"This booking is" is a required field', 'There is a missing field below, please select who this booking is for', $message );
  return $message;
}


add_filter( 'woocommerce_get_order_item_totals', 'order_text_update', 10, 1);
function order_text_update($rows, $order=null) {
        foreach($rows as $k => $row) {
          if ($k=='cart_subtotal') {
            $rows[$k]['label']  = 'Subtotal:';
          }
        }
        return $rows;
}





// NOTE that this replaces the subscriptions and product basket functionality - ensure that subs settings allows multiple in basket
add_filter( 'woocommerce_add_to_cart_validation', 'woocommerce_alert_cart_before_add', 1, 1 );
function woocommerce_alert_cart_before_add($passed) {

  // DS default change
  $passed = true;

  global $woocommerce;

  $product_id = (int)$_POST["add-to-cart"];

  if ( WC_Subscriptions_Product::is_subscription( $product_id ) && (WC()->cart->cart_contents) ) {

      foreach( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {

        //anything in cart then messsage and exit
        if ($cart_item['product_id']) {

                $passed = false;
                wc_add_notice( __( '<b>You need to checkout before purchasing a Membership Subscription, otherwise your basket will be emptied. Do you wish to <a href="/basket">checkout</a> or <a href="/checkout/?empty=1">empty your basket to make a new purchase</a></b>? ', 'woocommerce' ), 'notice' );

                break;

        } else {

          $passed = true;

        }

      }

  } else {

    if (WC_Subscriptions_Product::is_subscription( $product_id ) ) {

          $passed =  true;

          wc_add_notice( __( '<b>You need to check out before purchasing anything further from the website. You can continue shopping after you have made your purchase.</b>', 'woocommerce' ), 'notice' );

          add_filter( 'woocommerce_add_to_cart_redirect', 'redirect_ajax_add_to_cart' );

    }

    if ((WC()->cart->cart_contents) ) {

      foreach( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {


         if (WC_Subscriptions_Product::is_subscription( $cart_item['product_id'] ) ) {

          $passed = false;

          wc_add_notice( __( '<b>As you have a Membership Subscription already in your basket, you need to check out before purchasing anything further from the website. You can continue shopping after you have made your Membership Subscription purchase. Do you wish to <a href="/basket">checkout</a> or <a href="/checkout/?empty=1">empty your basket to make a new purchase</a>?</b>', 'woocommerce' ), 'notice' );
          break;

         }

      }

    }

  }

return $passed;

}

function redirect_ajax_add_to_cart() {
  global $woocommerce;

    $data = $woocommerce->cart->get_checkout_url();

    return $data;
}

add_action( 'woocommerce_add_to_cart_validation', 'woocommerce_clear_cart_add', 15 );
function woocommerce_clear_cart_add($passed) {

//error_log($_GET['empty'].'-'.$_GET['add-to-cart'] );



  if ( $_GET['empty'] == 1 &&  isset( $_GET['add-to-cart'] ) ) {

    global $woocommerce;

    $woocommerce->cart->empty_cart();

    if ( isset( $_GET['add-to-cart'] ) ) {

      $product_id = $_GET['add-to-cart'];
      //error_log($product_id);
      $woocommerce->cart->add_to_cart($product_id,1);
    } else {
      add_filter( 'woocommerce_continue_shopping_redirect', 'wc_custom_redirect_continue_shopping' );
    }
  } else {
    return $passed;
  }
}

add_action( 'init', 'woocommerce_clear_cart_url', 15 );
function woocommerce_clear_cart_url() {

global $woocommerce;

  if ( isset($_GET['empty']) && $_GET['empty'] == 1 ) {

    $woocommerce->cart->empty_cart();
    add_filter( 'woocommerce_continue_shopping_redirect', 'wc_custom_redirect_continue_shopping' );

  }

}


// if subs then notify
add_action( 'woocommerce_before_checkout_form', 'subsmsg_add_checkout_notice', 11 );
function subsmsg_add_checkout_notice() {

       //get product categories in carts
      foreach( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {


        if ( WC_Subscriptions_Product::is_subscription( $cart_item['product_id'] ) ) {
            wc_print_notice( __( 'You need to check out before purchasing anything further from the website. You can continue shopping after you have made your purchase.', 'woocommerce' ), 'notice' );
        }
      }

}


/**
 * WooCommerce Cart item quantity was set to 0
 */
add_filter('woocommerce_before_cart_item_quantity_zero', 'f10_before_cart_item_quantity_zero', 10, 1);
function f10_before_cart_item_quantity_zero($cart_item_key)
{

  global $woocommerce;

  //error_log('Cart quantity updated to zero. Cart item key: ' . $cart_item_key);

  $cart = $woocommerce->cart->get_cart();
  foreach ($cart as $key => $value) {
    if (isset($value['bundled_items'])) {

      $bundled_items = $value['bundled_items'];

      foreach ($bundled_items as $bundle_item) {
        //$woocommerce->cart->set_quantity( $bundle_item, 0, false );
        unset( $woocommerce->cart->cart_contents[ $bundle_item ] );
        //error_log('removed child : '.$bundled_item);

      }
    }
  }
}

add_filter( 'loop_shop_per_page', 'acca_loops_shop_per_page' , 20 );
function acca_loops_shop_per_page($cols){
  if ( is_product_category('acca-webinars') ) {
    return 60;
  } else {
    return $cols;
  }
}
//add_action( 'template_redirect', 'f10_remove_product_from_cart' );
function f10_remove_product_from_cart($remove) {

global $woocommerce;

//error_log('get : '.$remove);

    // Run only in the Cart or Checkout Page
    if( is_cart() && isset($_GET['remove_item']) ) {

      $cart_item_key = sanitize_text_field( $_GET['remove_item'] );

      //error_log('Cart remove pressed. Cart item key: ' . $cart_item_key);

      $cart = $woocommerce->cart->get_cart();

      unset( $woocommerce->cart->cart_contents[ $cart_item_key ] );

      foreach ($cart as $key => $value) {
        if ($key == $cart_item_key && isset($value['bundled_items'])) {

          $bundled_items = $value['bundled_items'];

          foreach ($bundled_items as $bundle_item) {
            //$woocommerce->cart->set_quantity( $bundle_item, 0, false );
            unset( $woocommerce->cart->cart_contents[ $bundle_item ] );

            //$woocommerce->cart->
            //error_log('removed child : '.$bundle_item);

          }
        }
      }
      $woocommerce->cart->calculate_totals();
      $referer  = WC()->cart->get_cart_url();
      wp_safe_redirect( $referer );
    }


}


add_filter( 'woocommerce_bacs_accounts', 'f10_woocommerce_bacs_accounts' );
function f10_woocommerce_bacs_accounts($details){
return '' ;
}

add_filter ('woocommerce_bundled_product_add_to_cart','f10_woocommerce_bundled_product_add_to_cart' );
function f10_woocommerce_bundled_product_add_to_cart ($id) {
}

function fixed_pricing_2($product,$convert=false) {

global $woocommerce;

if(is_user_logged_in() && check_user_group() ) {
  $usergroup = 'groups';
} else {
  $usergroup = 'everyone';
}

$found = false;
$product_has_date_variants = false;

$pricing_rules = get_post_meta( $product->id, '_pricing_rules', true );
$price=false;

 if($pricing_rules) :

    //error_log(print_r($pricing_rules,1));

    foreach ($pricing_rules as $rule) {

      $var_arrays = $rule['variation_rules']['args']['variations'];

      if(!is_array($var_arrays)) {
        $var_arrays = array($var_arrays);
      }

      $key = 'variation_id';
      $val = $variations[$key];
      $today = date('Y-m-d');

      if($found == false) {

        if(isset($rule['conditions'][1]['args']['applies_to']) && $rule['conditions'][1]['args']['applies_to'] == $usergroup) {

          if(in_array($val, $var_arrays)) {

            //error_log('VARS  :'.$val.'--'.print_r($var_arrays,1));
            $datefrom = $rule['date_from'];
            $dateto = $rule['date_to'];

            //error_log($datefrom.'-->>'.$dateto);

            if ($datefrom !='' || $dateto != '') {
              $product_has_date_variants = true;


              if ($dateto != '' && $datefrom == '') {
                if($today < $dateto) {
                  //error_log(print_r('before date to',1));
                  if($rule['rules'][1]['from'] != '' && $rule['rules'][1]['from'] <= 1 ) {
                  //error_log(print_r('has both to and from',1));
                  $dynprice = $rule['rules'][1]['amount'];
                  $found = true;
                  //error_log(print_r($dynprice,1));
                  } elseif ($rule['rules'][1]['from'] != '') {

                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                    //error_log(print_r($dynprice,1));

                  } elseif ($rule['rules'][1]['to'] != '') {

                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                    //error_log(print_r($dynprice,1));

                  } else {

                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                    //error_log(print_r($dynprice,1));
                  }

                }

              } elseif($datefrom != '') {
                if($today > $datefrom) {
                  if($dateto != '') {
                    if($today < $dateto) {
                      //error_log(print_r('has before date and date from',1));
                      if($rule['rules'][1]['from'] != '' && $rule['rules'][1]['from'] <= 1 ) {
                        //error_log(print_r('has both to and from',1));
                        $dynprice = $rule['rules'][1]['amount'];
                        $found = true;
                        //error_log(print_r($dynprice,1));
                      } elseif ($rule['rules'][1]['from'] != '') {

                        $dynprice = $rule['rules'][1]['amount'];
                        $found = true;
                        //error_log(print_r($dynprice,1));

                      } elseif ($rule['rules'][1]['to'] != '') {

                        $dynprice = $rule['rules'][1]['amount'];
                        $found = true;
                        //error_log(print_r($dynprice,1));

                      } else {

                        $dynprice = $rule['rules'][1]['amount'];
                        $found = true;
                        //error_log(print_r($dynprice,1));
                      }
                    } else {

                    //error_log(print_r('after date from',1));

                    if($rule['rules'][1]['from'] != '' && $rule['rules'][1]['from'] <= 1 ) {

                      $dynprice = $rule['rules'][1]['amount'];
                      $found = true;
                      //error_log(print_r($dynprice,1));
                    } elseif ($rule['rules'][1]['from'] != '') {

                      $dynprice = $rule['rules'][1]['amount'];
                      $found = true;
                      //error_log(print_r($dynprice,1));

                    } elseif ($rule['rules'][1]['to'] != '') {

                      $dynprice = $rule['rules'][1]['amount'];
                      $found = true;
                      //error_log(print_r($dynprice,1));

                    } else {

                      $dynprice = $rule['rules'][1]['amount'];
                      $found = true;
                      //error_log(print_r($dynprice,1));
                    }
                  }
                } else {

                  if($rule['rules'][1]['from'] != '' && $rule['rules'][1]['from'] <= 1 ) {
                    //error_log(print_r('has both to and from',1));
                    //error_log('RULES : '.$dateto.'=='.$datefrom);
                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                    //error_log(print_r($dynprice,1));
                  } elseif ($rule['rules'][1]['from'] != '') {

                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                   // error_log(print_r($dynprice,1));

                  } elseif ($rule['rules'][1]['to'] != '') {

                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                    //error_log(print_r($dynprice,1));

                  } else {

                    $dynprice = $rule['rules'][1]['amount'];
                    $found = true;
                    //error_log(print_r($dynprice,1));
                  }
                }
              }
            } else {
              // no dates ?

            }
          } else {
             // NOT in var Arrays
          }
        } else {
          // has simple variants for single products

          $dynprice = $rule['rules'][1]['amount'];

          // $qtyfrom = $rule['rule'][1]['from'];
          // $qtyto = $rule['rule'][1]['to'];

          // if ($qtyfrom > 1) {
          //   $dynprice = $rule['rules'][1]['amount'];
          // }

        }
      }
    }
  }


    // If we have variants but no date fields
    foreach ($pricing_rules as $rule) {

      if(isset($rule['conditions'][1]['args']['applies_to']) && $rule['conditions'][1]['args']['applies_to'] == $usergroup && $found==false) {

        $var_arrays = $rule['variation_rules']['args']['variations'][0];

        if($val == $rule['variation_rules']['args']['variations'][0]) {

          $datefrom = $rule['date_from'];
          $dateto = $rule['date_to'];

          if ($dateto =='' && $datefrom =='') {
            $dynprice = $rule['rules'][1]['amount'];
            //$dynprice = current_ex_rate($dynprice);
            $found=true;

          }
        }
      }
    }


    if ((int)$dynprice > 0) {
      $dynprice = get_woocommerce_currency_symbol().current_ex_rate($dynprice);
    } else {
      $dynprice = 'Free';
    }

    $price = '<span class="price">'.$dynprice.'</span>';

  endif;

return $price;

}

add_filter( 'woocommerce_default_address_fields' , 'f10_override_default_address_fields' );
function f10_override_default_address_fields( $address_fields ) {
     $address_fields['company']['required'] = true;
     return $address_fields;
}

add_action( 'woocommerce_thankyou', 'paypal_woocommerce_thankyou', 10 );

function paypal_woocommerce_thankyou ($order_id) {

  if ( $_GET['utm_nooverride'] == 1 && isset( $_GET['utm_nooverride'] ) ) {

    //error_log('POST PAYPAL : '.$order_id);

    global $woocommerce, $woocommerce_bundles, $product;

    $order = new WC_Order( $order_id );
    $output = '' ;


    if ( count( $order->get_items() ) > 0 ) {

      foreach( $order->get_items() as $item ) {

        if (isset($item['item_meta']['This booking is'][0]) && trim($item['item_meta']['This booking is'][0])==trim('For me')) {

            //error_log('booking set - for me');
            $output = 'Please visit <a href="/dashboard/#fndtn-my-purchases">My2020Dashboard – My Purchases</a> to access the details of your purchase.';

        } else {

          if (isset($item['item_meta']['This booking is'][0])) {

             $output = 'Important! Please note that confirmation of your event(s) will not be issued until you allocate the names of the delegate(s) attending by visiting <a href="/dashboard/#fndtn-my-purchases">My2020Dashboard – My Purchases</a>.';

          } else {
             $output = 'Please visit <a href="/dashboard/#fndtn-my-purchases">My2020Dashboard – My Purchases</a> to access the details of your purchase.';

          }


          $_product = $order->get_product_from_item( $item );

          if ( $_product->product_type =='subscription_variation' || $_product->product_type =='subscription' ) {

             $output =  'Please visit <a href="/dashboard/">My2020Dashboard</a> to access all of the 2020 Membership Benefits.';
          }

        }
      }
    }

    print_r( '<div class="woocommerce-message" style="top:93px;position:absolute;">'.$output.'</div>');

  }

}
//woocommerce_after_checkout_form
//add_action ('woocommerce_review_order_after_order_total','check_currency');
add_action ('woocommerce_review_order_before_payment','check_currency');
function check_currency() {

//  if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

    $current_currency = get_woocommerce_currency();

    if ($current_currency == 'ZAR' ) {
      echo '<p>Unfortunately PayPal does not currently accept South African Rand. To continue to pay via Debit/Credit card or PayPal, please change your currency to <a href="/checkout/?aelia_cs_currency=USD">USD</a>, <a href="/checkout/?aelia_cs_currency=GBP">GBP</a> or <a href="/checkout/?aelia_cs_currency=EUR">EUR</a>, alternatively you may continue via Bank Transfer.</p>';
    }
    if ($current_currency == 'INR' ) {
      echo '<p>Unfortunately PayPal does not currently accept Indian Rupee. To continue to pay via Debit/Credit card or PayPal, please change your currency to <a href="/checkout/?aelia_cs_currency=USD">USD</a>, <a href="/checkout/?aelia_cs_currency=GBP">GBP</a> or <a href="/checkout/?aelia_cs_currency=EUR">EUR</a>, alternatively you may continue via Bank Transfer.</p>';
    }
//  }
}

add_filter( 'wpo_wcpdf_order_items_data', 'signupfee_wpo_wcpdf_order_items_data', 10, 2 );
function signupfee_wpo_wcpdf_order_items_data( $data_list, $order ) {

  if (sizeof( $order->get_items() ) > 0 ) {

      foreach( $order->get_items() as $item_id => $item ) {
        $_product  = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
        $item_meta = new WC_Order_Item_Meta( $item['item_meta'], $_product );


        if ( !$_product->product_type =='subscription_variation' ) {

        $subscription_order = false;

        } else {

          $subscription_order = true;

          $signupfee = $_product->subscription_sign_up_fee;

          $data = array();

          // Set the item_id
          $data['item_id'] = 'TEMP';

          // Set the id
          $data['product_id'] = $item['product_id'];
          $data['variation_id'] = $item['variation_id'];

          // Set item name
          $data['name'] = 'Subscription Sign Up Fee';

          // Set item quantity
          $data['quantity'] = 1;

          // Set the line total (=after discount)
          $data['line_total'] = $signupfee;
          $data['single_line_total'] = $signupfee;
          $data['line_tax'] = 0;
          $data['single_line_tax'] = 0;

          $data['line_subtotal'] =  get_woocommerce_currency_symbol().current_ex_rate($signupfee);
          $data['line_subtotal_tax'] = $signupfee;
          $data['ex_price'] = $signupfee;
          $data['price'] = $signupfee;
          $data['order_price'] = get_woocommerce_currency_symbol().current_ex_rate($signupfee); // formatted according to WC settings

          $data['single_price'] = $signupfee;

        }

  if ( $subscription_order && $signupfee >0 ) {

      $data_list[] = $data;

      return $data_list;

  } else {

    return $data_list;

  }
    }
  }
}

function remove_email_fields($fields) {
  if(is_user_logged_in()) {
    unset($fields['billing']['billing_phone']['class']);

    unset($fields['billing']['billing_email']);
    unset($fields['billing']['shipping_email']);
  }

  return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'remove_email_fields' );

/**
 * Change Order Notes Placeholder Text - WooCommerce
 *
 */
function adjust_woocommerce_checkout_fields( $fields ) {
  //error_log(print_r($fields,1));
  $fields['order']['order_comments']['label'] = 'Enter delegate names/order notes here';
  return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'adjust_woocommerce_checkout_fields' );