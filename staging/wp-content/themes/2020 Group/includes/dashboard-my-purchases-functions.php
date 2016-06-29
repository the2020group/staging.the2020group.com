<?php

//Update user IWPRO tag
function updateIwproTag($user,$product_id) {
	global $iwpro;

	if ( $iwpro->ia_app_connect() ) {
		$userDetails = get_user_by('id', $user);
     	$email = $userDetails->user_email;

	 	$contact = $iwpro->app->dsFind('Contact',5,0,'Email',$email,array('Id'));
	 	$contact = $contact[0];

	 	if ($contact['Id'] != null && $contact['Id'] != 0 && $contact != false){
	 		$contactId = (int) $contact['Id'];

            $produc = get_post($product_id);
            if ($produc->post_parent != 0 && !empty($produc->post_parent)) {
                $product_id = $produc->post_parent;
            }
	 		$tag = (int) get_post_meta($product_id, 'infusionsoft_tag', true);

	 		if(!empty($tag) && $tag > 0) {
                $iwpro->app->grpAssign($contactId, $tag);
            }
	 	}

	}
}

function assign_users ($type,$data) {

    $user_id = get_current_user_id();
    $c_user_id = get_current_user_id();
    $ajax = false;
    if ( $data === false || empty($data) ) {
        $ajax = true;
    }
    else {

        if(is_admin()) {
            $order    = new WC_Order( $data['order_id'] );
            $user_id  = $order->get_user_id();
            $order_id = $data['order_id'];
        }
    }
    if (isset($data['user'])) {
        $user_id = $data['user'];
    }

    if (is_array($data)) {
        $product_id = (int)$data['product_id'];
    }
    else {
        $product_id = (int)$_POST['product_id'];
    }

    if ($product_id==0) {
        error_log(print_r($data,1));
        error_log('Assign Users Error: No Product ID');
        exit;
    }

    if (is_array($data)) {
        $event_date = $data['event_date'];
    }
    else {
        $event_date = $_POST['event_date'];
    }

    if ($event_date=='') {
        error_log('Assign Users Error: No Event Date');
        exit;
    }

    if (is_array($data)) {
        $booking_year = (int)$data['booking_year'];
    }
    else {
        $booking_year = (int)$_POST['booking_year'];
    }

    if ($booking_year==0) {
        error_log('Assign Users Error: Booking year = 0');
        exit;
    }

    if (is_array($data)) {
        $users[$data['order_id']] = array($user_id);
    }
    else {
        $users = $_POST['users'];
    }

    if (!is_array($users)) {
        error_log('Assign Users Error: Users is not an array');
        exit;
    }

    foreach ($users as $order_id => $user_list) {

        foreach ($user_list as $variant_id => $var_users) {

            $assigned = get_assigned_users($product_id, $order_id,  $booking_year, $user_id);

            if (is_array($var_users)) {
                foreach ($var_users as $user) {

                    if ($user != '') {
                        $user = (int)$user;

                        if ($user>0) {
                            if (!isset($assigned[$user])) {



                                //Update user tag
                                updateIwproTag($user,$product_id);

                                $result = update_user_meta($user,'event_'.$variant_id,$event_date.'|'.$booking_year);

                                update_user_meta($user,'event_'.$variant_id.'_order_id',$order_id);

                                $data = array('date'=>$event_date.' 00:00:00','ID'=>$product_id,'type'=>$type);

                                $cpd_entry = get_field('content_qualifies_for_cpd_log_entry',$product_id);
                                if ($cpd_entry == 'yes') {

                                    add_cpd_log($data,$user);

                                }

                                if ($type == 'Webinar') {
                                    wcac_action($product_id,$user);
                                }

                            }
                        }
                    }
                }
            }
            else {
                $user = $var_users;
                if ($user>0) {
                    if (!isset($assigned[$user])) {
                        //Update user tag
                        updateIwproTag($user,$product_id);

                        update_user_meta($user,'event_'.$product_id,$event_date.'|'.$booking_year);
                        update_user_meta($user,'event_'.$product_id.'_order_id',$order_id);

                        $data = array('date'=>$event_date.' 00:00:00','ID'=>$variant_id,'type'=>'Conference');
                        $cpd_entry = get_field('content_qualifies_for_cpd_log_entry',$variant_id);
                        if ($cpd_entry == 'yes') {
                            add_cpd_log($data,$user);
                        }

                        if ($type == 'Webinar') {
                            wcac_action($product_id,$user);
                        }
                    }
                }
            }
        }
    }

    // if ajax call stop here.
    if ($ajax ) {
        echo 'true';
        exit;
    }
}


add_action('wp_ajax_assign_users_to_conference', 'assign_users_to_conference');
function assign_users_to_conference($data = false) {

    $type = 'Conference';

    assign_users($type,$data);

}

add_action('wp_ajax_assign_users_to_webinar', 'assign_users_to_webinar');
function assign_users_to_webinar($data = false) {

    $type = 'Webinar';

    assign_users($type,$data);

}

add_action('wp_ajax_assign_users_to_workshop', 'assign_users_to_workshop');
function assign_users_to_workshop($data = false) {

    $type = 'Workshop';

    assign_users($type,$data);

}

add_action('wp_ajax_assign_users_to_focusgroup', 'assign_users_to_focusgroup');
function assign_users_to_focusgroup($data = false) {

    $type = 'Focus Group';

    assign_users($type,$data);

}

function get_order_ids_for_user($user_id,$year) {

    $args = array (
                    'fields'      => 'ids',
                    'post_type'   => 'shop_order',
                    'post_status' => 'complete',
                    'author'      => $user_id,
                    'year'        => $year
                  );

    $query = new WP_Query($args);
    //print_r($query);
}

function get_assigned_users($product_id, $order_id, $year, $userid) {



    $userids = array ();

    $parent = get_user_meta($userid,'2020_parent_account',true);

    if ($parent) {
        $userid = $parent;
    }

    $children = getChildUserAccounts($userid);
    $userids[] = $userid;
    foreach ( $children as $child) {
        $userids[] = $child->ID;
    }

    $userids = array_unique($userids);

    $u_args = array( 'include' => $userids,
                     'meta_query' => array(
                                            array(
                                                'key'     => 'event_'.$product_id,
                                                'value'   => '|'.$year,
                                                'compare' => 'LIKE'
                                            ),
                                            array(
                                                'key'     => 'event_'.$product_id.'_order_id',
                                                'value'   => $order_id,
                                                'compare' => '='
                                            ),
                                            'relation' => 'AND',
                                        )
                    );

    $user_query = new WP_User_Query($u_args);
    $assigned = array();

    if ( ! empty( $user_query->results ) ) {
        foreach ( $user_query->results as $user ) {
            $assigned[$user->ID] = get_user_meta($user->ID,'first_name',true).' '.get_user_meta($user->ID,'last_name',true);
        }
    }

    return $assigned;
}

function get_assigned_users_conf($product_id, $order_id, $year, $userid) {

    $userids = array ();


    $parent = get_user_meta($userid,'2020_parent_account',true);

    if ($parent) {
        $userid = $parent;
    }

    $children = getChildUserAccounts($userid);
    $userids[] = $userid;
    foreach ( $children as $child) {
        $userids[] = $child->ID;
    }

    $u_args = array( 'include' => $userids,
                    'meta_query' => array(
                                            array(
                                                'key'     => 'event_'.$product_id,
                                                'value'   => '|'.$year,
                                                'compare' => 'LIKE'
                                            ),
                                            array(
                                                'key'     => 'event_'.$product_id.'_order_id',
                                                'value'   => $order_id,
                                                'compare' => '=='
                                            ),
                                            'relation' => 'AND',
                                        )
                    );

    $user_query = new WP_User_Query($u_args);

    $assigned = array();

    if ( ! empty( $user_query->results ) ) {
        foreach ( $user_query->results as $user ) {
            $variation = get_user_meta($user->ID,'event_'.$product_id.'_meal',true);
            if($variation) {
                $assigned[get_user_meta($user->ID,'event_'.$product_id.'_meal',true)][$user->ID] = get_user_meta($user->ID,'first_name',true).' '.get_user_meta($user->ID,'last_name',true);
            } else {
                $assigned[$user->ID] = get_user_meta($user->ID,'first_name',true).' '.get_user_meta($user->ID,'last_name',true);
            }

        }
    }

    return $assigned;
}

function get_assigned_events($user,$month,$year) {

    global $wpdb;

    $sql = $wpdb->prepare( 'SELECT * FROM '.$wpdb->prefix.'usermeta WHERE user_id=%d AND meta_value LIKE %s',$user,$year.'-'.$month.'%');

    $events = $wpdb->get_results($sql );

    return $events;

}

function get_todays_events($datetocheck) {

    global $wpdb;
    /* $datecheck = $year . $month . $day;
    $sql = $wpdb->prepare( 'SELECT * FROM '.$wpdb->prefix.'postmeta WHERE meta_key=%d AND meta_value LIKE %s','date',$datecheck);
    $events = $wpdb->get_results($sql );

    return $events;*/

    $events = array();//event ids
    $datecheck = $datetocheck;
    $args = array(
                'numberposts'     => -1,
                'meta_key'        => 'date',
                'meta_value'      => $datecheck,
                'post_type'       => 'product',
                'post_status'     => 'publish',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'term_id',
                        'terms'    => array( 97),
                        'operator' => 'NOT IN',
                    ),
                ),
            );

    $posts = get_posts($args);

    //get the post ids as event ids
    $events = wp_list_pluck( $posts, 'ID' );

    return $events;


}


function get_assigned_products ($user_id,$order_id) {
    global $wpdb;

    $order = new WC_Order($order_id);
    $sql = 'SELECT um.meta_key as product_key FROM '.$wpdb->usermeta.' um
                WHERE um.user_id    = %d
                    AND um.meta_key LIKE %s
                    AND um.meta_value = %d';
    $result = $wpdb->get_col( $wpdb->prepare( $sql, $user_id, 'event_%_order_id', $order_id ) );



    foreach ($result as $k=>$v) {
        $result[$k] = str_replace('_order_id','',$v);
        $result[$k] = str_replace('event_','',$result[$k]);
    }

    $products = array();

    foreach ($order->get_items() as $key => $lineItem) {

        if (in_array($lineItem['product_id'],$result) || in_array($lineItem['variation_id'],$result)) {
            $products[] = $lineItem;
        }
    }

    return $products;
}

function get_products_user_has_been_assigned_to($user_id,$year) {
    global $wpdb;

    $sql = ' SELECT p.ID AS order_id FROM '.$wpdb->usermeta.' um
                INNER JOIN '.$wpdb->posts.' p ON p.ID = um.meta_value
                INNER JOIN '.$wpdb->postmeta.' pm ON p.ID = pm.post_id
                WHERE um.user_id = %d
                    AND p.post_status = "wc-completed"
                    AND p.post_type   = "shop_order"
                    AND year(p.post_date) = %d
                    AND pm.meta_key   = "_customer_user"
                    AND pm.meta_value != %d
                    AND um.meta_key   LIKE %s ';

    $result = $wpdb->get_col( $wpdb->prepare( $sql, $user_id,$year, $user_id, 'event_%_order_id' ) );

    return $result;
}


// get all order years for
// - assigned to a product by the buyer
// - placed the order
function get_all_order_years($user_id) {
    global $wpdb;

    // sql for getting all assigned orders or placed orders
    $sql = '( SELECT  EXTRACT(YEAR from p.post_date) AS order_year FROM '.$wpdb->usermeta.' um
                INNER JOIN '.$wpdb->posts.' p ON p.ID = um.meta_value
                WHERE um.user_id = %d
                    AND p.post_status = "wc-completed"
                    AND p.post_type = "shop_order"
                    AND um.meta_key LIKE %s )

             UNION

            ( SELECT  EXTRACT(YEAR from p2.post_date) AS order_year FROM '.$wpdb->posts.' p2
                INNER JOIN '.$wpdb->postmeta.' m ON m.post_id = p2.ID
                    WHERE m.meta_key = "_customer_user"
                        AND m.meta_value = %d
                        AND p2.post_status = "wc-completed"
                        AND p2.post_type = "shop_order" )

            ORDER BY order_year DESC;
            ';

    // get only the order years. we don't care about the rest here.
    $result = $wpdb->get_col( $wpdb->prepare( $sql, $user_id, 'event_%_order_id', $user_id ) );

    return $result;
}


// get all orders for a specific user
function get_all_user_orders($user_id,$status='completed',$return_orders=false,$year=false) {
    if(!$user_id || (!$return_orders && !$year)) {
        return false;
    }

    $orders = array();

    $args = array(
                'numberposts'     => -1,
                'meta_key'        => '_customer_user',
                'meta_value'      => $user_id,
                'post_type'       => 'shop_order',
                'post_status'     => 'publish',
                'tax_query'=>array(
                        array(
                            'taxonomy'  =>'shop_order_status',
                            'field'     => 'slug',
                            'terms'     =>$status
                            )
                )
            );

    if ($year) {
        $args['year'] = (int)$year;
    }

    $posts = get_posts($args);

    if ($return_orders) {
        return ($posts);
    }

    //get the post ids as order ids
    $orders = wp_list_pluck( $posts, 'ID' );

    return $orders;
}


    function get_all_products_ordered_by_user($user_id=false,$status='completed',$year=false) {

    if(!$user_id || !$year) {
        return false;
    }
    $year = (int)$year;

    $orders = array();
    $orders = get_all_user_orders($user_id,$status,false, $year);

    if(empty($orders)) {
        return false;
    }

    global $wpdb;

    $order_list                 = '('.join(',', $orders).')';
    $query_select_order_items   = "SELECT order_item_id as id
                                        FROM {$wpdb->prefix}woocommerce_order_items
                                        WHERE order_id IN {$order_list}";

    $query_select_product_ids   = "SELECT im.meta_value as product_id, i.order_id
                                        FROM {$wpdb->prefix}woocommerce_order_itemmeta im, wp_woocommerce_order_items i
                                        WHERE im.order_item_id = i.order_item_id
                                            AND im.meta_key=%s
                                            AND im.order_item_id IN ($query_select_order_items)";

    $result = $wpdb->get_results($wpdb->prepare($query_select_product_ids,'_product_id'));

    //print_r($orders);
    //echo '<hr />';


	$product_ids = array();
	$orderArr = array();

	if(!empty($result)) {
		foreach($result as $prodArr) {
			$product_ids[] = $prodArr->product_id;
			$orderArr[$prodArr->product_id] = $prodArr->order_id;
		}
	}


    /*

    To get product type:
    if($product->is_type(''))

    */


    //$product_ids = array_unique($product_ids);


    $meta_query = array(
        array(
            'key' => 'date',
            'compare' => 'IN'//,
            //'orderby' => 'meta_value_num ASC'
        )
    );



    $products_with_dates = new WP_Query( array( 'post_type' => 'product', 'post__in' => $product_ids , 'posts_per_page'=>-1
        , 'meta_query' => $meta_query, 'orderby' => 'meta_value_num ASC'
    ) );

    $meta_query = array(
        'relation' => 'OR',
        array(
            'key' => 'date',
            'value' => array(''),
            'compare' => 'NOT IN'
        ),
        array(
            'key' => '_sku',
            'compare' => 'IN'
        )
    );

    $products_without_dates = new WP_Query( array( 'post_type' => 'product', 'post__in' => $product_ids , 'posts_per_page'=>-1
        , 'meta_query' => $meta_query
    ) );

    foreach ($products_without_dates->posts as $k => $product) {
        foreach ($products_with_dates->posts as $product_2) {
            if ($product->ID == $product_2->ID) {
                unset($products_without_dates->posts[$k]);
            }
        }
    }

    $products = new WP_Query;

    $products->posts = array_merge($products_with_dates->posts,$products_without_dates->posts);
    $products->post_count = count($products->posts);

	if(!empty($products->posts)) {
		foreach($products->posts as $key => $product) {
			$products->posts[$key]->order_id = $orderArr[$product->ID];
            $thedate = get_field('date',$product->ID);
		}
	}

    return $products;
}



function get_all_product_ids_ordered_by_user($user_id=false,$status='completed',$year=false) {

    if(!$user_id || !$year) {
        return false;
    }
    $year = (int)$year;

    $orders = array();
    $orders = get_all_user_orders($user_id,$status,false, $year);

    if(empty($orders)) {
        return false;
    }

    global $wpdb;

    $order_list                 = '('.join(',', $orders).')';
    $query_select_order_items   =  "SELECT order_item_id as id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id IN {$order_list}";
    $query_select_product_ids   = "SELECT meta_value as product_id FROM {$wpdb->prefix}woocommerce_order_itemmeta
                                    WHERE meta_key=%s AND order_item_id IN ($query_select_order_items)";
    $product_ids = $wpdb->get_col($wpdb->prepare($query_select_product_ids,'_product_id'));

    return $product_ids;
}

// get all years in which a customer has ordered something
function get_order_years($user_id) {
    $orders = get_all_user_orders($user_id,'completed',true);
    $order_years = array();

    foreach ($orders as $order) {
        $year = substr($order->post_date,0,4);

        if (!in_array($year, $order_years)) {
            array_push($order_years,$year);
        }
    }

    sort($order_years);
    return array_reverse($order_years);
}


function get_webinar_seats($product_id,$user_id,$year) {

    if(!$user_id || !$year) {
        return false;
    }
    $year = (int)$year;

    $orders = array();
    $orders = get_all_user_orders($user_id,'completed',false, $year);

    if(empty($orders)) {
        return false;
    }
    global $wpdb;

    $total_seats = 0;

    $ret_seats = array();

    foreach ($orders as $order) {

        $order_details = new WC_Order( $order);

        $items = $order_details->get_items();

        foreach ($items as $item) {

            $term_list = wp_get_post_terms($item['item_meta']['_product_id'][0],'product_cat',array('fields'=>'ids'));

            if ( in_array(10, $term_list) === true || in_array(97, $term_list) === true  ) {

                $qty = $item['item_meta']['_qty'][0];
                $seats = $item['item_meta']['delegate-places'][0];

                if($seats) {

                    if ($item['item_meta']['_product_id'][0] == $product_id) {

                        if (is_numeric($seats) && is_numeric($qty)) {
                            $total_seats += ($seats*$qty);
                            $ret_seats[$order] = ($seats*$qty);
                        }
                        else {
                            $parts = explode('-',$seats);

                            $total_seats += ((int)$parts[1]*$qty);
                            $ret_seats[$order] = ((int)$parts[1]*$qty);
                        }
                    }
                } else {
                    if ($item['item_meta']['_product_id'][0] == $product_id) {
                        $total_seats += $qty;
                        $ret_seats[$order] = $qty;
                    }
                    else {
                        //$total_seats = $qty;
                    }

                }

            }

        }

    }

    return $ret_seats;

}

function get_conference_seats($product_id,$user_id,$year) {

    if(!$user_id || !$year) {
        return false;
    }
    $year = (int)$year;

    $orders = array();
    $orders = get_all_user_orders($user_id,'completed',false, $year);

    if(empty($orders)) {
        return false;
    }

    global $wpdb;

    $total_seats = array();

    foreach ($orders as $order) {

        $order_details = new WC_Order( $order);

        $items = $order_details->get_items();

        foreach ($items as $item) {

            $term_list = wp_get_post_terms($item['item_meta']['_product_id'][0],'product_cat',array('fields'=>'ids'));

            if ( in_array(11, $term_list) === true) {

                $qty = $item['item_meta']['_qty'][0];
                $package = $item['item_meta']['conference-package'][0];

                if($package) {

                    if ($item['item_meta']['_product_id'][0] == $product_id || $item['item_meta']['_variation_id'][0] == $product_id) {
                        if(!isset($ret_seats[$order][$package])) {
                            $total_seats[$package] = 0;
                            $ret_seats[$order][$package] = 0;
                        }
                        $total_seats[$package] += $qty;
                        $ret_seats[$order][$package] += $qty;
                    }
                } else {
                    if ($item['item_meta']['_product_id'][0] == $product_id || $item['item_meta']['_variation_id'][0] == $product_id) {
                        $ret_seats[$order] = $qty;
                    }

                }

            }

        }

    }

    error_log(print_r($ret_seats,1));

    return $ret_seats;

}

function get_workshop_seats($product_id,$user_id,$year) {

     if(!$user_id || !$year) {
        return false;
    }
    $year = (int)$year;

    $orders = array();
    $orders = get_all_user_orders($user_id,'completed',false, $year);

    if(empty($orders)) {
        return false;
    }
    global $wpdb;

    $total_seats = 0;

    $ret_seats = array();

    foreach ($orders as $order) {

        $order_details = new WC_Order( $order);

        $items = $order_details->get_items();

        foreach ($items as $item) {

            $term_list = wp_get_post_terms($item['item_meta']['_product_id'][0],'product_cat',array('fields'=>'ids'));

            if ( in_array(28, $term_list) === true) {
                $qty = $item['item_meta']['_qty'][0];
                    if ($item['item_meta']['_product_id'][0] == $product_id) {
                        $ret_seats[$order] += $qty;
                    }
            }

        }

    }

    return $ret_seats;

}

function get_focusgroup_seats($product_id,$user_id,$year) {

     if(!$user_id || !$year) {
        return false;
    }
    $year = (int)$year;

    $orders = array();
    $orders = get_all_user_orders($user_id,'completed',false, $year);

    if(empty($orders)) {
        return false;
    }
    global $wpdb;

    $total_seats = 0;

    $ret_seats = array();

    foreach ($orders as $order) {

        $order_details = new WC_Order( $order);

        $items = $order_details->get_items();

        foreach ($items as $item) {

            $term_list = wp_get_post_terms($item['item_meta']['_product_id'][0],'product_cat',array('fields'=>'ids'));

            if ( in_array(27, $term_list) === true ) {
                $qty = $item['item_meta']['_qty'][0];
                    if ($item['item_meta']['_product_id'][0] == $product_id) {
                        $ret_seats[$order] += $qty;
                    }
            }


        }

    }

    return $ret_seats;

}

/**
 * has the user bought the product
 */
function has_user_bought($user_id,$product_id){
    $ordered_products=get_all_products_ordered_by_user($user_id);
    if(in_array($product_id, (array)$ordered_products))
    return true;
    return false;
}

function get_webinar_date ($product_id) {

          // Set the content vars of the returned matching products
          //$productLink = get_permalink( $product->ID );
          //$productTitle = $product->post_title;
          $eventDate = get_field('date', $product_id);
          $eventStartTime = get_field('start_time', $product_id);
          $eventEndTime = get_field('end_time', $product_id);


        if ($eventDate) :

          $date_part['year'] = substr($eventDate,0,4);
          $date_part['month'] = substr($eventDate,4,2);
          $date_part['day'] = substr($eventDate,6,2);

          $html = '<p class="event-date"><span>Date:</span> '.date('j<\s\u\p>S</\s\u\p> F Y', mktime(0, 0, 0, $date_part['month'], $date_part['day'], $date_part['year'])).'</p>';



        // if ($eventStartTime) :

        //   $html .='<p class="event-time">'.$eventStartTime;

        //     if ($eventEndTime) {
        //         $html .='-'.$eventEndTime;
        //     }

        //   $html .= '</p>';

        // endif;

        return $html;

        else :

            return '';

        endif;


}

function get_variant_from_order_product($order_id,$productid) {

    $order = new WC_Order($order_id);
    $items = $order->get_items();

    foreach ($items as $item) {

        $product_id = $item['product_id'];

        $product_variation_id = $item['variation_id'];

          // Check if product has variation.
        if ($product_variation_id && $product_id==$productid) {

            return $product_variation_id;

        } else {

            return 0;

        }
    }

}

function get_post_id_from_meta( $meta_key,$meta_value ) {

  global $wpdb;

  $meta_value = sanitize_title($meta_value);

  $pid = $wpdb->get_var( $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %d AND meta_value = %s",  $meta_key, $meta_value) );
  if( $pid != '' ) {
    return $pid;
  } else {
    return false;
  }
}
