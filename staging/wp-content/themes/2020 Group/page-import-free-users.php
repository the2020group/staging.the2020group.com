<?php

/*
 * Template Name: Import Free Users
 */

get_header();

set_time_limit(0);


// place file into the theme
// create page using this page template
// location to csv file
// run page from frontend

// best to clear error log as this import will fill it up. In the end there will be a list of errors that have been generated during import


ini_set('auto_detect_line_endings',TRUE);

$file = fopen(__DIR__.'/library/imports/Complimentary-Members-NoTag.csv','r');

    $line = 1;
    $master_accounts = array();
    $errors = array();

    while(($cont = fgetcsv($file,9000,',')) !== FALSE) {

        //print_r($cont);

        if (file_exists(__DIR__.'/library/imports/stop.txt')) {
            print_r($errors);

            exit;
        }

        if ($line > 0 && $cont[0] !='' && isset($cont[0])&& $cont[6]!='') {

            $user = array();

            $user['is_contact_id'] = $cont[0];
            $user['first_name']    = $cont[1];
            $user['last_name']     = trim($cont[2]);
            $user['company']       = $cont[3];
            $user['is_company_id'] = $cont[4];
            $user['phone']         = trim(str_replace('(Work)','',$cont[5]));
            $user['email']         = trim($cont[6]);
            $user['line1']         = $cont[7];
            $user['line2']         = $cont[8];
            $user['city']          = $cont[9];
            $user['county']        = $cont[10];
            $user['postcode']      = $cont[11];

            $continue = true;

            if (1==1 || trim($user['is_company_id'])=='8312') :

            $tags = $cont[12];

            $tags = explode(', ',$tags);

            foreach ($tags as $tag) {
                $parts = explode(' -> ',$tag);
                $user['tags'][trim($parts[0])][] = trim($parts[1]);
            }

            $continue = true;
            $v_id = 0;
            $p_id = 48;
            if (in_array('020108 Mem - 1 Partner',$user['tags']['02 Behavioural Tags']) ) {
                $v_id = 2034;
            }
            elseif (in_array('020109 Mem - 2-5 Partners',$user['tags']['02 Behavioural Tags'])) {
                $v_id = 49;
            }
            elseif (in_array('020110 Mem - 6-9 Partners',$user['tags']['02 Behavioural Tags'])) {
                $v_id = 50;
            }
            elseif (in_array('020111 Mem - 10-14 Partners',$user['tags']['02 Behavioural Tags'])) {
                $v_id = 51;
            }
            elseif (in_array('020103 Mem - Individual Membership (Practice)', $user['tags']['02 Behavioural Tags'])) {
                $p_id  = 45;
            }
            elseif (in_array('020103 Mem - Individual Membership (Industry)', $user['tags']['02 Behavioural Tags'])) {
                $p_id  = 2043;
            }
            else {
                error_log('oops not sure what membership : '.print_r($user,1));
                $continue = false;
                $error[] = 'Contact: '.$user['email'].' - Problem: Cannot determine what membership. Possibly missing tags';
            }

            if ($continue) {
                $user_id = username_exists( $user['email'] );
                if ( !$user_id && email_exists($user['email']) == false ) {

                    error_log($line.':'.$user['is_contact_id'].' - '.$user['is_company_id']);

                    $random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
                    $user_id = wp_create_user( $user['email'], $random_password, $user['email'] );

                    if ($user_id == 0) {
                        error_log( 'failed to create user:'.print_r($user,1));
                    }
                    else {
                        error_log('new user created '.$user_id);
                        add_user_meta($user_id,'billing_company',$user['company']);
                        add_user_meta($user_id,'billing_phone',$user['phone']);
                        add_user_meta($user_id,'billing_address_1',$user['line1']);
                        add_user_meta($user_id,'billing_address_2',$user['line2']);
                        add_user_meta($user_id,'billing_city',$user['city']);
                        add_user_meta($user_id,'billing_state',$user['county']);
                        add_user_meta($user_id,'billing_postcode',$user['postcode']);
                        add_user_meta($user_id,'billing_country',$user['country']);
                        add_user_meta($user_id,'first_name',$user['first_name']);
                        add_user_meta($user_id,'last_name',$user['last_name']);
                        add_user_meta($user_id,'is_company_id',$user['is_company_id']);
                        add_user_meta($user_id,'is_contact_id',$user['is_contact_id']);

                        $type = '';
                        $group = '';

                        if (in_array('010411 Pos - Partner',$user['tags']['01 Demographic Tags']) ||
                            in_array('010409 Pos - Managing Partner',$user['tags']['01 Demographic Tags'])
                           ) {

                            $type = 'Partner';
                            //if ($user['sm']!='£0.00' ) {
                                $group = 'Owner';
                            //}
                        }
                        else if (in_array('010102 Cty - Accountant in Industry',$user['tags']['01 Demographic Tags'])) {
                            $type = 'Partner';
                            $group = 'individual_in_industry';
                            //if ($user['sm']!='£0.00' ) {
                                $group = 'Owner';
                            //}
                        }
                        else if (in_array('010101 Cty - Accountant in Practice',$user['tags']['01 Demographic Tags'])  ) {
                            $type = 'Employee';
                            $group = 'individual_in_practice';
                            //if ($user['sm']!='£0.00' ) {
                                $group = 'Owner';
                                $type='Partner';
                            //}
                        }
                        else {
                            if (in_array('010401 Pos - Accountancy',$user['tags']['01 Demographic Tags']) ||
                                in_array('010402 Pos - Administration',$user['tags']['01 Demographic Tags']) ||
                                in_array('010403 Pos - Bookkeeping',$user['tags']['01 Demographic Tags']) ||
                                in_array('010404 Pos - Business Advisory',$user['tags']['01 Demographic Tags']) ||
                                in_array('010405 Pos - Corporate Finance',$user['tags']['01 Demographic Tags']) ||
                                in_array('010406 Pos - Forensic Accounting',$user['tags']['01 Demographic Tags']) ||
                                in_array('010407 Pos - FSA',$user['tags']['01 Demographic Tags']) ||
                                in_array('010408 Pos - IT',$user['tags']['01 Demographic Tags']) ||
                                in_array('010410 Pos - Miscellaneous',$user['tags']['01 Demographic Tags']) ||
                                in_array('010412 Pos - Practice Manager',$user['tags']['01 Demographic Tags']) ||
                                in_array('010414 Pos - Sales/Marketing',$user['tags']['01 Demographic Tags']) ||
                                in_array('010415 Pos - Tax',$user['tags']['01 Demographic Tags']) ||
                                in_array('010416 Pos - Tax Partner',$user['tags']['01 Demographic Tags']) ||
                                in_array('010417 Pos - Training',$user['tags']['01 Demographic Tags']) ||
                                in_array('010418 Pos - Wealth Management',$user['tags']['01 Demographic Tags']) ||
                                in_array('010413 Pos - Sole Practitioner',$user['tags']['01 Demographic Tags'])
                               )  {
                                // if ($user['sm']=='£0.00' ) {
                                //     $type = 'Employee';
                                //     $group = 'Staff';
                                // }
                                // else {
                                    $type = 'Partner';
                                    $group = 'Owner';
                                //}
                            }
                        }

                        error_log('type:'.$type);

                        // error_log('sm: '.$user['sm']);
                        // error_log('im: '.$user['im']);


                                if ($type == 'Partner') {

                                    $order = wc_create_order(array('customer_id'=>$user_id));
                                     $address = array(
                                        'first_name' => $user['first_name'],
                                        'last_name'  => $user['last_name'],
                                        'company'    => $user['company'],
                                        'email'      => $user['email'],
                                        'phone'      => $user['phone'],
                                        'address_1'  => $user['line1'],
                                        'address_2'  => $user['line2'],
                                        'city'       => $user['city'],
                                        'state'      => $user['county'],
                                        'postcode'   => $user['postcode'],
                                        'country'    => $user['country']
                                    );

                                    $order->set_address( $address, 'billing' );
                                    $order->set_address( $address, 'shipping' );



                                    if ($p_id > 0) {
                                        $item = $order->add_product( wc_get_product( $v_id ), 1 ); //(get_product with id and next is for quantity)
                                        $values['data'] = array();
                                        $values['product_id'] = $p_id;
                                        $values['variation_id'] = $v_id;

                                        WC_Subscriptions_Checkout::add_order_item_meta($item, $values);
                                        $order->calculate_totals();
                                        $order->payment_complete();

                                        $sub_key = WC_Subscriptions_Manager::get_subscription_key( $order->id, $p_id );


                                        if (in_array('020113 Mem - January',$user['tags']['02 Behavioural Tags'])) {
                                            $start_date ='2015-01-01 00:00:01';
                                            $expiry_date = '2016-01-31 23:59:59';
                                            $next_payment = $expiry_date;
                                        }
                                        elseif (in_array('020114 Mem - February',$user['tags']['02 Behavioural Tags'])) {
                                            $start_date ='2015-02-01 00:00:01';
                                            $expiry_date = '2016-02-28 23:59:59';
                                            $next_payment = $expiry_date;
                                        }
                                        elseif (in_array('020115 Mem - March',$user['tags']['02 Behavioural Tags'])) {
                                            $start_date ='2015-03-01 00:00:01';
                                            $expiry_date = '2016-03-31 23:59:59';
                                            $next_payment = $expiry_date;
                                        }
                                        elseif (in_array('020116 Mem - April',$user['tags']['02 Behavioural Tags'])) {
                                            $start_date ='2015-04-01 00:00:01';
                                            $expiry_date = '2016-04-30 23:59:59';
                                            $next_payment = $expiry_date;
                                        }
                                        elseif (in_array('020117 Mem - May',$user['tags']['02 Behavioural Tags'])) {
                                            $start_date ='2015-05-01 00:00:01';
                                            $expiry_date = '2016-05-31 23:59:59';
                                            $next_payment = $expiry_date;
                                        }
                                        elseif (in_array('020118 Mem - June',$user['tags']['02 Behavioural Tags'])) {
                                            $start_date ='2015-06-01 00:00:01';
                                            $expiry_date = '2016-06-30 23:59:59';
                                            $next_payment = $expiry_date;
                                        }
                                        elseif (in_array('020119 Mem - July',$user['tags']['02 Behavioural Tags'])) {
                                            $start_date ='2015-07-01 00:00:01';
                                            $expiry_date = '2016-07-31 23:59:59';
                                            $next_payment = $expiry_date;
                                        }
                                        elseif (in_array('020120 Mem - August',$user['tags']['02 Behavioural Tags'])) {
                                            $start_date ='2015-08-01 00:00:01';
                                            $expiry_date = '2016-08-31 23:59:59';
                                            $next_payment = $expiry_date;
                                        }
                                        elseif (in_array('020121 Mem - September',$user['tags']['02 Behavioural Tags'])) {
                                            $start_date ='2014-09-01 00:00:01';
                                            $expiry_date = '2015-09-30 23:59:59';
                                            $next_payment = $expiry_date;
                                        }
                                        elseif (in_array('020122 Mem - October',$user['tags']['02 Behavioural Tags'])) {
                                            $start_date ='2014-10-01 00:00:01';
                                            $expiry_date = '2015-10-31 23:59:59';
                                            $next_payment = $expiry_date;
                                        }
                                        elseif (in_array('020123 Mem - November',$user['tags']['02 Behavioural Tags'])) {
                                            $start_date ='2014-11-01 00:00:01';
                                            $expiry_date = '2015-11-30 23:59:59';
                                            $next_payment = $expiry_date;
                                        }
                                        elseif (in_array('020124 Mem - December',$user['tags']['02 Behavioural Tags'])) {
                                            $start_date ='2014-12-01 00:00:01';
                                            $expiry_date = '2015-12-31 23:59:59';
                                            $next_payment = $expiry_date;
                                        }

                                        $new_subscription_details = array(
                                                                        'start_date' => $start_date,
                                                                        'expiry_date' => $expiry_date
                                                                    );


                                        WC_Subscriptions_Manager::update_subscription( $sub_key, $new_subscription_details );

                                        WC_Subscriptions_Manager::set_next_payment_date( $sub_key, $user_id, $next_payment ) ;

                                        error_log('sm order created: '.$order->id);
                                    }
                                }
                                else {
                                    error_log('no sm order created');
                                }



                        if ($type != '') {


                            add_user_meta($user_id,'2020_account_type',$type);







                            if ($user['sm']!='') {
                                error_log('setting master account');
                                $master_accounts[$user['is_company_id']] = $user_id;

                            }
                            else {

                                 if (isset($master_accounts[$user['is_company_id']])) {
                                     error_log('found master');
                                     // change to the parent account user id NOT is_ id

                                    $company_users = get_users( array(
                                        "meta_key" => "is_company_id",
                                        "meta_value" => $user['is_company_id'],
                                        "fields" => "ID"
                                    ) );

                                    $parent_user_id = min($company_users);

                                    //add_user_meta($user_id,'2020_parent_account',$master_accounts[$user['is_company_id']]);
                                    add_user_meta($user_id,'2020_parent_account',$parent_user_id);

                                }
                                else {
                                    error_log('no master account: '.print_r($user,1));
                                }
                            }
                        }
                    }
                }
            }
            endif;
        }

        $line++;
        error_log('HERE ? '.$line );
        //error_log(print_r($cont,1));
    }

    fclose($file);
    ini_set('auto_detect_line_endings',FALSE);
    error_log('Done');
    error_log('Found Errors');
    error_log(print_r($errors,1));


get_footer();