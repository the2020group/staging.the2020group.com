<?php

/*
 * Template Name: Import Child Users
 */

get_header();

set_time_limit(0);


// place file into the theme
// create page using this page template
// location to csv file
// run page from frontend

// best to clear error log as this import will fill it up. In the end there will be a list of errors that have been generated during import

ini_set('auto_detect_line_endings',TRUE);

$start = 1;
$start = $_GET['start'];

$file = fopen(__DIR__.'/library/imports/all_child_live.csv','r');

    $line = 1;
    $master_accounts = array();
    $errors = array();

    while(($cont = fgetcsv($file,9000,',')) !== FALSE) {

        //print_r($cont);

        echo 'record : '.$line.'<br>';

        if (file_exists(__DIR__.'/library/imports/stop.txt')) {
            print_r($errors);

            exit;
        }

        if ($line > 0 && $cont[0] !='' && isset($cont[0])) {


            $user = array();

                // $user['is_contact_id'] = $cont[0];
                // $user['first_name']    = $cont[1];
                // $user['last_name']     = trim($cont[2]);
                // $user['email']         = trim($cont[6]);
                // $user['phone']         = trim(str_replace('(Work)','',$cont[5]));
                // $user['company']       = $cont[3];
                // $user['is_company_id'] = $cont[4];
                // $user['line1']         = $cont[7];
                // $user['line2']         = $cont[8];
                // $user['city']          = $cont[9];
                // $user['county']        = $cont[10];
                // $user['postcode']      = $cont[11];
                // $user['country']       = $cont[12];

                // $user['sm']            = utf8_encode($cont[13]);
                // $user['im']            = utf8_encode($cont[14]);

                if ('new_format'=='new_format') {

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
                    $user['country']       = $cont[12];
                    $user['job_title']     = $cont[13];


                    //$user['sm']            = utf8_encode($cont[13]);
                    //$user['im']            = utf8_encode($cont[14]);

                }






            if ($user['type']=='' && $user['email'] != '' ) {


               $errors[] = 'Contact: '.$user['email'].' Attempted create account.';



                $user_id = username_exists( $user['email'] );

                if ( !$user_id && email_exists($user['email']) == false && $line >= $start) {



                    $random_password = 'password'; //wp_generate_password( $length=12, $include_standard_special_chars=false );
                    $user_id = wp_create_user( $user['email'], $random_password, $user['email'] );




                    if ($user_id == 0) {

                    }
                    else {



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

                        if ($user['job_title']=='Staff') {
                            $user['job_title']='Employee';
                        }

                        add_user_meta($user_id,'2020_account_type',$user['job_title']);


                        $company_users = get_users( array(
                            "meta_key" => "is_company_id",
                            "meta_value" => $user['is_company_id'],
                            "fields" => "ID"
                        ) );

                        $parent_user_id = min($company_users);

                        //add_user_meta($user_id,'2020_parent_account',$master_accounts[$user['is_company_id']]);
                        add_user_meta($user_id,'2020_parent_account',$parent_user_id);



                    }
                }
            }
        }
        $line++;
    }

    fclose($file);
    ini_set('auto_detect_line_endings',FALSE);


get_footer();