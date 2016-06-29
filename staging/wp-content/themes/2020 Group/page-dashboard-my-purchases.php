<?php

/*
 * Template Name: Dashboard - My Purchases
 */

get_header();

//include some functions that are only used on this page
require_once('includes/dashboard-my-purchases-functions.php');


 ?>

 <div class="dash-wrap">

    <div class="row collapse">

        <div class="small-1 medium-1 columns" role="main" style="background: #000; color: #fff">
            <?php get_sidebar('dashboard'); ?>
        </div>
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

        <div class="small-11 medium-11 columns" role="main">
            <div id="dash-main">

            <h2><?php the_title(); ?></h2>

            <?php

                // TASK  :build year selector
                // STEP 1: get all orders the user has been assigned too event_PRODUCTID_ORDERID or that a user has placed
                // STEP 2: output years

                $user_id = get_current_user_id();
                $assigned_orders = get_all_order_years($user_id);

                if (isset($_GET['y'])) {
                    $year = (int)$_GET['y'];
                }


                ?>
                <div class="dash-btns">
                    <?php

                        foreach ($assigned_orders as $k=>$order_year) :

                            $button = 'silver';

                            if ( (isset($year) && $order_year==$year) || (isset($year) && $year==0 && $k==0)) {
                                $button = 'silver';
                                $year = $order_year;
                            }
                            else {
                                $button = 'lsilver';
                            }

                            ?>
                          <a href="#my-purchases" data-uri="/dashboard/my-purchases?y=<?php echo $order_year;?>" class="dataload gen-btn btn-color <?php echo $button;?> "><?php echo $order_year;?></a>
                      <?php endforeach; ?>

                    </div>
                <?php

                $parent_user_id = get_user_meta($user_id,'2020_parent_account',true);

                $is_child_user = false;

                if ($parent_user_id == '') {

                    $parent_user_id = 0;
                    $p_id = $user_id;

                }
                elseif ($parent_user_id && $parent_user_id>0) {

                  $is_child_user = true;
                  $parent_user_id = $parent_user_id;
                  $p_id = $parent_user_id;
                }


                $has_been_assigned_to = get_products_user_has_been_assigned_to($user_id,$year);

                if (is_array($has_been_assigned_to) && count($has_been_assigned_to)>0) {

                    echo '<div><h3>Purchased and assigned to you :</h3></div>';

                    foreach ($has_been_assigned_to as $assigned_to_order) {

                        $products = get_assigned_products($user_id,$assigned_to_order);

                        foreach ($products as $product) {

                            if (isset($product['product_id'])) {

                                include('includes/dashboard-my-child-purchases-loop.php');

                            }

                        }

                    }

                }

?>

                   <div><h3>Purchased by you :</h3></div>

<?php


                $order_years = get_order_years($user_id);

                if (count($order_years)>0) :
                    if (!isset($_GET['y'])) {
                        $year = $order_years[0];
                    }
                    else {
                        $year = (int)$_GET['y'];
                        if (!in_array($year,$order_years)) {
                            $year = $order_years[0];
                        }
                    }



                        // get all products the user has bought
                        //$user_id = get_current_user_id();
                        $products = get_all_products_ordered_by_user($user_id,'completed',$year);

                        if ($parent_user_id!= '') {

                            $child_user_id = get_current_user_id();

                            $products_child_user = get_all_products_ordered_by_user($child_user_id,'completed',$year);

                        }


                        $children = getChildUserAccounts($p_id);

                        $account_dropdown = '';
                        $account_dropdown .=  '<option value="'.$p_id.'">'.get_user_meta($p_id,'first_name',true).' '.get_user_meta($p_id,'last_name',true).'</option>';
                        foreach ( $children as $child) {
                            $account_dropdown .= '<option value="'.$child->ID.'">'.$child->first_name.' '.$child->last_name.'</option>';
                        }

                        if (count($products->posts)==0 && count($products_child_user->posts)==0 ) : ?>
                            <p>No purchases this year</p>
                        <?php
                        else :

                            $counter = 0;

                            // loop through each product and display the content
                            foreach ($products->posts as $product ) :
                                $counter++;
                                $bundleparent = '';
                                $bundle = get_post_meta( $product->ID, '_bundle_data', true );

                                // check if the custom field has a value
                                if( !empty( $bundle ) ) {

                                    $bundleparent = $product->ID;
                                    //echo('A Bundled Product '.$product->ID);
                                    foreach ($bundle as $subproduct) {
                                        //$product = get_post($subproduct['product_id']);
                                        //print_r($product);
                                        //echo('<br/>A Bundled Sub Product '.$subproduct['product_id']);
                                        //include('includes/dashboard-my-purchases-loop.php');
                                    }
                                    //include('includes/dashboard-my-purchases-loop.php');
                                } else {

                                    /*
                                    if ($is_child_user) {

                                         $assigned = get_assigned_users($product->ID, $year, $user_id);




                                        if (count($assigned)>0) {
                                            foreach ($assigned as $id=>$a) {


                                                //echo '<li>'.$a.'</li>';


                                                if ($id == $user_id) {

                                                    include('includes/dashboard-my-purchases-loop.php');
                                                }

                                            }

                                        }

                                    } else {
                                        include('includes/dashboard-my-purchases-loop.php');
                                    }
                                    */
                                    $user_id = get_current_user_id();

                                    include('includes/dashboard-my-purchases-loop.php');

                                }

                                //include('includes/dashboard-my-purchases-loop.php');

                               ?>

                            <?php  endforeach;

/*
                            $counter = 0;


                            $products = get_all_products_ordered_by_user($child_user_id,'completed',$year);

                            $counter = 0;

                            // flag that this user has urched but can allocate parents children ?
                            $is_sibling = true ;

                            if($products) :
                                // loop through each product and display the content
                                foreach ($products->posts as $product ) :
                                    $counter++;

                                     $temp = get_assigned_users($product->ID, $year, $child_user_id);

                                     //$temp = get_assigned_users($product->ID, $year, $user_id);

                                    include('includes/dashboard-my-purchases-loop.php');


                                endforeach;
                            endif;

*/
                        endif;
                    else  :
                        ?>
                        <p>You haven't purchased anything!</p>
                        <?php
                    endif;


            ?>
                </div>
        </div>
        <?php endwhile; ?>
        <?php endif; ?>
    </div>

        </div>

<?php get_footer();