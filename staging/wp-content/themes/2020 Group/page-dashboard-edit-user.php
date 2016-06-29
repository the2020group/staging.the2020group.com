<!doctype html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

<head>
<meta charset="utf-8">

<title><?php bloginfo('name'); ?> | <?php is_home() ? bloginfo('description') : wp_title(''); ?></title>

<meta name="HandheldFriendly" content="True">
<meta name="MobileOptimized" content="320">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/library/images/apple-icon-touch.png">
<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
<!--[if IE]>
	<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
<![endif]-->
<?php // set /favicon.ico for IE10 win ?>
<meta name="msapplication-TileColor" content="#d3492f">
<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/library/images/win8-tile-icon.png">

<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

<script src="//use.typekit.net/nab6gby.js"></script>
<script>try{Typekit.load();}catch(e){}</script>

<?php wp_head(); ?>
<?php

	// get id of current user
    $user_id = get_current_user_id();

    // if the user is logged in
    if ($user_id > 0) :
    	// get all active subscriptions
    	global $_subscription_details;
        $_ini_subscription_details = wcs_get_users_subscriptions( $user_id );

        foreach ($_ini_subscription_details as $k => $v) {
            $_subs_order_id = $_ini_subscription_details[$k]->order->id;
            $_subs_order = new WC_Order($_subs_order_id);
            $_subs_order_items = $_subs_order->get_items();
            $_subscription_key = WC_Subscriptions_Manager::get_subscription_key( $_subs_order_id, $_subs_order_items[key($_subs_order_items)]['product_id']);
            $_subscription_details[$_subscription_key] = WC_Subscriptions_Manager::get_subscription($_subscription_key);
        }
		// create a new product factory to get subscription product details.
		$_pf = new WC_Product_Factory();

		// get the product details
		global $_subscription_product;
		$_subscription_product = $_pf->get_product($_subscription_details[key($_subscription_details)]['product_id']);

	endif;
?>
<?php

/*
 * Template Name: Dashboard - Personal Details Edit User
 */

$current_user = wp_get_current_user();
$parent_account = get_user_meta($current_user->ID,'2020_parent_account',true);
$type = get_user_meta($current_user->ID,'2020_account_type',true);

$edit_user_id = (int)$_GET['id'];
$child_account = get_user_meta($edit_user_id,'2020_parent_account',true);

if ($current_user->ID != $child_account) {
    wp_logout();
    wp_redirect('/login');
}

if (isset($_POST['save'])) {

    $first_name     = trim(strip_tags($_POST['first_name']));
    $last_name      = trim(strip_tags($_POST['last_name']));

    $company_name   = trim(strip_tags($_POST['company_name']));

    $account_type   = trim(strip_tags($_POST['account_type']));
    $account_status = trim(strip_tags($_POST['account_status']));

    if ($account_type == null) {
        $account_type = 'standard';
    }

    update_user_meta($edit_user_id,'first_name',$first_name);
    update_user_meta($edit_user_id,'last_name',$last_name);

    update_user_meta($edit_user_id,'billing_company',$company_name);

    update_user_meta($edit_user_id,'2020_account_type',$account_type);
    if ($account_status=='disabled') {
        update_user_meta($edit_user_id,'2020_account_status','true');
    }
    else {
        delete_user_meta($edit_user_id,'2020_account_status');
    }
    manageChildUsersInInfusionsoft($edit_user_id);
    ?>

    <script>
    jQuery(document).ready(function() {

        parent.location.reload();

        parent.jQuery.fancybox.close();

     });
    </script>
    <?php
    exit;
}

/* get_header(); */ ?>

</head>
<body <?php body_class(); ?>>

	<div id="outer-wrap" class="">



    <div class="row">

        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

        <div class="small-12 columns">

            <?php

                $results = $wpdb->get_results($wpdb->prepare('SELECT DISTINCT `u`.`ID`, `u`.`user_email`,
                                                                  `um_first`.`meta_value` as `first_name`,
                                                                  `um_last`.`meta_value` as `last_name`,
                                                                  `um_acc`.`meta_value` as `account_type`,
                                                                  `um_co`.`meta_value` as `user_company`

                                                            FROM `wp_users` `u`

                                                            INNER JOIN `wp_usermeta` `um` ON `u`.`ID`=`um`.`user_id`
                                                            INNER JOIN `wp_usermeta` `um_first` ON `u`.`ID`=`um_first`.`user_id`
                                                            INNER JOIN `wp_usermeta` `um_last` ON `u`.`ID`=`um_last`.`user_id`
                                                            INNER JOIN `wp_usermeta` `um_acc` ON `u`.`ID`=`um_acc`.`user_id`
                                                            INNER JOIN `wp_usermeta` `um_co` ON `u`.`ID`=`um_co`.`user_id`

                                                            WHERE
                                                                    `um`.`user_id`=%d AND
                                                                    `um_acc`.`meta_key`="2020_account_type" AND
                                                                    `um_first`.`meta_key`="first_name" AND
                                                                    `um_last`.`meta_key`="last_name" AND
                                                                    `um_co`.`meta_key`="billing_company"',$edit_user_id));

                ?>


                <div>&nbsp;</div>

                <h3>Edit user</h3>

                <div class="dash-wrap">

                <div class="dash-block pers-det">

                <div class="row">

                    <div class="small-12 columns" role="main">


                        <form action="" method="post" id="new_user">
                            <div class="row collapse">

                                <div class="small-12 medium-4 columns">
                                    <div class="left-col">
																			<p>Name</p>
                                    </div>
                                </div>

                                <div class="small-12 medium-6 columns">
                                    <div class="right-col">
                                        <input type="text" name="first_name" placeholder="First name" value="<?php echo $results[0]->first_name;?>"/>
                                        <input type="text" name="last_name" placeholder="Last name" value="<?php echo $results[0]->last_name;?>" />
                                    </div>
                                </div>
                                <div class="small-12 medium-2 columns">

                                </div>

                            </div>

                           <div class="row collapse">

                                <div class="small-12 medium-4 columns">
                                    <div class="left-col">
                                                                            <p>Company Name</p>
                                    </div>
                                </div>

                                <div class="small-12 medium-6 columns">
                                    <div class="right-col">
                                        <input type="test" name="company_name" value="<?php echo $results[0]->user_company;?>" />
                                    </div>
                                </div>
                                <div class="small-12 medium-2 columns">

                                </div>

                            </div>


                            <div class="row collapse">

                                <div class="small-12 medium-4 columns">
                                    <div class="left-col">
																			<p>Email Address</p>
                                    </div>
                                </div>

                                <div class="small-12 medium-6 columns">
                                    <div class="right-col">
                                    	<input type="email" name="email" value="<?php echo $results[0]->user_email;?>" readonly/>
                                    </div>
                                </div>
                                <div class="small-12 medium-2 columns">

                                </div>

                            </div>

                            <?php if (check_groups_user_capabilities(4)) { ?>
                            <div class="row collapse">

                                <div class="small-12 medium-4 columns">
                                    <div class="left-col">
																			<p>Account type</p>
                                    </div>
                                </div>

                                <div class="small-12 medium-6 columns">
                                  <div class="right-col">
                                    <select name="account_type"><option value="partner" <?php if ($results[0]->account_type == 'partner') { echo 'selected'; } ?>>Partner</option><option value="employee" <?php if ($results[0]->account_type == 'employee') { echo 'selected'; } ?>>Employee</option></select>
                                  </div>
                                </div>
                                <div class="small-2 medium-2 columns" role="main" style="text-align: left;">

                                </div>

                            </div>
                            <?php } ?>

                            <div class="row collapse">

                                <div class="small-12 medium-4 columns">
                                    <div class="left-col">
																			<p>Account Status</p>
                                    </div>
                                </div>
                                <?php $status = get_user_meta($edit_user_id,'2020_account_status',true); ?>
                                <div class="small-12 medium-6 columns">
                                    <div class="right-col">
                                    <select name="account_status"><option value="active" <?php if ($status=='') { echo 'selected'; } ?>>Active</option><option value="disabled" <?php if ($status=='true') { echo 'selected'; } ?>>Disabled</option></select>
                                    </div>
                                </div>
                                <div class="small-12 medium-2 columns">
                                    <button class="gen-btn orange" type="submit" name="save">Save</button>
                                </div>

                            </div>
                            <div class="row collapse">

									<div class="small-12 medium-4 columns">
											&nbsp;
									</div>
									<div class="small-12 medium-3 columns">
                                    &nbsp;
                                </div>
                                <div class="small-2 medium-2 columns">
                                    &nbsp;
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                </div>

                </div>

        </div>
        <?php endwhile; ?>
        <?php endif; ?>
    </div>

<?php wp_footer();
