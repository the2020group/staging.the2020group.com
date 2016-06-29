<?php

	$user_id = get_current_user_id();

	if ($user_id > 0) {
		$lbc = get_user_meta($user_id,'_lbc',true);

		if(!isset($_COOKIE['lc'])) {

				$c = '';

				if ($lbc == 'ukonly') {
					$c = 'uk';
				}
				elseif ($lbc == 'nonuk') {
					$c = 'nuk';
				}
				elseif ($lbc == 'all') {
					$c = 'all';
				}
		    setcookie('lc', $c, time()+86400*31, '/');
		    $_COOKIE['lc'] = $c;
		}


	}

?><!doctype html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

<head>
<meta charset="utf-8">

<title><?php wp_title('-'); ?></title>

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
<script src="<?php echo get_template_directory_uri(); ?>/library/js/libs/modernizr.custom.min.js"></script>

<script src="http://geoip.first10.co.uk/"></script>

<script src="//use.typekit.net/nab6gby.js"></script>
<script>try{Typekit.load();}catch(e){}</script>

<?php wp_head(); ?>

<!--[if lt IE 9]>
  <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <script src="<?php echo get_template_directory_uri(); ?>/library/js/libs/min/respond.min.js"></script>
  <link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/library/css/ie.css" type="text/css" media="screen">
<![endif]-->

<?php

	// get id of current user


    // if the user is logged in
    if ($user_id > 0) :
    	// get all active subscriptions
    	global $_subscription_details, $ma_id;

    	$ma_id = $user_id;
			$parent_user_id = get_user_meta($user_id,'2020_parent_account',true);
			;
      $is_child_user=false;

      if ($parent_user_id>0) {
      	$ma_id = $parent_user_id;
      	$child_user_id = $user_id;
      	$user_id = $parent_user_id;
      	$is_child_user = true;
      }

    	$_ini_subscription_details = wcs_get_users_subscriptions( $user_id );

    

    	foreach ($_ini_subscription_details as $k => $v) {

    		$old_or_new = true;

    		if ( !is_object($_ini_subscription_details[$k]->order)) {
    			$_subs_order_id = $k;
    			$_subs_order = new WC_Subscription($_subs_order_id);
    			//print_r(get_class_methods($_subs_order));exit;
    		//	print_r($_subs_order);exit;
    			$old_or_new = false;
    		}
    		else {
    			$_subs_order_id = $_ini_subscription_details[$k]->order->id;
    			$_subs_order = new WC_Order($_subs_order_id);
    		}
    		
	    	
	    	$_subs_order_items = $_subs_order->get_items();

	    	$_subscription_key = WC_Subscriptions_Manager::get_subscription_key( $_subs_order_id, $_subs_order_items[key($_subs_order_items)]['product_id']);

	    	if ($old_or_new) {
	    		$_subscription_details[$_subscription_key] = WC_Subscriptions_Manager::get_subscription($_subscription_key);
	    	}
	    	else {

	    		
	    		//print_r($_subs_order->completed_payments);exit;
	    		$_subscription_details[$_subscription_key] = array('order_id'=>$k,
	    																											 'product_id'=>$_subs_order_items[key($_subs_order_items)]['product_id'],
	    																											 'variation_id'=>$_subs_order_items[key($_subs_order_items)]['variation_id'],
	    																											 'status' => $_subs_order->status,
	    																											 'period' => $_subs_order->billing_period,
	    																											 'interval' => $_subs_order->billing_interval,
	    																											 'length' => '',
	    																											 'start_date' => $_subs_order->get_date('start_date'),
	    																											 'expiry_date' => $_subs_order->get_date('next_payment_date'),
	    																											 'end_date' => $_subs_order->get_date('end_date'),
	    																											 'trial_expiry_date' => $_subs_order->get_date('trial_end_date'),
	    																											 'failed_payments' => '',
	    																											 'completed_payments' => '',
	    																											 'suspension_count' => $_subs_order->suspension_count ? $_subs_order->suspension_count : 0,
				    																								 'last_payment_date' => $_subs_order->get_date('last_payment_date'),
	    																											 );

	    		//print_r($_subs_order);
	    	}
    	}


    	//print_r($_subscription_details);exit;


		// create a new product factory to get subscription product details.
		$_pf = new WC_Product_Factory();

		// get the product details
		global $_subscription_product;
		if (is_array($_subscription_details) && isset($_subscription_details[key($_subscription_details)]['product_id'])) {
			$_subscription_product = $_pf->get_product($_subscription_details[key($_subscription_details)]['product_id']);
		}

	endif;
?>
</head>
<body <?php body_class(); ?>>

	<div id="search-popout">
		<div class="search-content">
			<div class="row">
				<div class="small-12 large-6 large-offset-3 columns">
					<?php get_search_form(); ?>
					<span class="search-close"></span>
				</div>
			</div>
		</div>
	</div>

	<div id="outer-wrap" class="">
		<!-- <div class="small-12 columns"> -->
		<?php if (is_front_page() ) : ?>

		<?php else : ?>

			<header class="page-header">
        <a href="#" class="toggle"><span></span></a>
				<div class="row">
					<div class="small-12 medium-2 large-3 columns">
						<div class="main-logo">
							<a href="<?php echo home_url(); ?>" rel="nofollow"><img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/general/2020-innovation.png"  alt="" class="logo"></a>
						</div>
					</div>
					<div class="small-12 medium-10 large-9 columns">
						<div class="utilities">
							<div class="header-menu">
								<div class="search-box">
									<span class="icon-search search"></span>
								</div>
								<?php include_once('includes/block-header-user.php'); ?>
			        </div>
			        <div class="header-sec-menu">

                <dl class="header-content-filter">
                  <dt><a href="#"><span>Content Filter</span></a></dt>
                  <dd>
                    <ul class="menudrop">
                      <li <?php if (isset($_GET['lc']) && $_GET['lc']=='uk') { echo 'class="active"';}?>><a href="?lc=uk" class="location-filter" data-filter="uk">UK Only</a></li>
                      <li <?php if (isset($_GET['lc']) && $_GET['lc']=='nuk') { echo 'class="active"';}?>><a href="?lc=nuk" class="location-filter" data-filter="nuk">Rest of World</a></li>
                      <li <?php if (!isset($_GET['lc']) || ( isset($_GET['lc']) && $_GET['lc']!='uk'  && $_GET['lc']!='nuk' )) { echo 'class="active"'; }?>><a href="?" class="location-filter" data-filter="all">Everything</a></li>
                    </ul>
                  </dd>
                </dl>

			        	<?php dynamic_sidebar('transposh-area'); ?>
			        	<?php echo do_shortcode('[aelia_currency_selector_widget title="Select Currency" widget_type="buttons"]'); ?>
				        <?php
				            $defaults = array(
				                'theme_location'  => 'header',
				                'menu'            => 'header secondary menu',
				                'container'       => '',
				                'echo'            => true,
				                'fallback_cb'     => 'wp_page_menu',
				                'before'          => '',
				                'after'           => '',
				                'link_before'     => '',
				                'link_after'      => '',
				                'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
				                'depth'           => 1,
				                'walker'          => ''
				            );
										wp_nav_menu( $defaults );
				        ?>
				        </div>
						</div>
					</div>
				</div>
			</header>
			<div class="row">
				<div class="small-12 columns">
					<div class="page-nav-wrap">
						<?php wp_nav_menu( array( 'menu' => 'Main Menu', 'container_class' => 'main-menu' ) ); ?>
					</div>
				</div>
			</div>

			<?php endif; ?>
