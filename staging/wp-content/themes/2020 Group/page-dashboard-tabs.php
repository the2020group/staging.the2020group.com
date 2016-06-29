<?php

/*
 * Template Name: Dashboard - Tabs
 */

get_header();

if ($current_user->ID==0) {
	wp_redirect('/login?ref='.urlencode($_SERVER['REQUEST_URI']));
	exit;
}

?>
		<script>
			jQuery(document).ready(function($){
				// dashboard nav
				var href  = location.href;
				var split = href.split("#");
				var page  = '';


				if (split[1]!=null) {
					page = split[1];
					page = page.replace('fndtn-','');
					parent_div = page;
				}
				else {
					page = 'my-details';
					parent_div = 'my-personal-details';
				}

				$('dd.'+parent_div).addClass('active');
				$('#'+parent_div).load('/dashboard/'+page + ' #dash-main', function(response, status, xhr) {

				 	if ( status == "error" ) {
				  		$( "#error" ).html( msg + xhr.status + " " + xhr.statusText );
				    	//contentWrapper.css('background-color', 'transparent');
				  	}
				  	if ( status == "success" ) {
						//contentWrapper.css('background-color', '#f00');
				  	}
				  	$(document).foundation('equalizer', 'reflow');

					if($('#profilePicture').length>0) {
						$('#profilePicture').fileUpload({
							before : function() {
								$('.profile-image').addClass('loading');
							},
							success : function(data) {
								$('.profile-image .user-img').attr('src',data.image);
							},
							complete : function() {
								$('.profile-image').removeClass('loading');
							}
						});
					}
				});

			});
		</script>

		<?php
			$_i_subscription_details = wcs_get_users_subscriptions( $user_id );
            //error_log(print_r($_ini_subscription_details[key($_ini_subscription_details)]->order->id,1));
            $subs_order_id = $_i_subscription_details[key($_i_subscription_details)]->order->id;
            $subs_order = new WC_Order($subs_order_id);
            $subs_order_items = $subs_order->get_items();
            $subscription_key = WC_Subscriptions_Manager::get_subscription_key( $subs_order_id, $subs_order_items[key($subs_order_items)]['product_id']);
            $subscriptions = WC_Subscriptions_Manager::get_subscription($subscription_key);

			//print_r($subscriptions);

			$user_id = get_current_user_id();
			$m_id = $user_id;
			$parent_user_id = get_user_meta($user_id,'2020_parent_account',true);

      $is_child_user=false;

      if ($parent_user_id>0) {
      	$m_id= $parent_user_id;
      	$child_user_id = $user_id;
      	$user_id = $parent_user_id;
      	$is_child_user = true;
      }

      $subscriptions = $_subscription_details;

			$all_actions = array();
			$membership_id = array();
			?>



		<div class="dash-wrap">

	    <div class="row">

	        <div class="small-12 columns" role="main">

		        <div class="tab-wrap">

            <?php if(!empty($subscriptions)) {
            		reset($subscriptions);
                  	$key = key($subscriptions);
                  }
            ?>

				        	<dl id="mainTabs" class="tabs vertical" data-tab data-options="deep_linking:true; scroll_to_content:false;">
							    <dd class="my-personal-details"><a href="#my-personal-details" data-uri="/dashboard/my-details" class="dataload" title="My details"><span class="icon-user"></span></a></dd>
							    <?php if ( ! empty( $subscriptions ) && $subscriptions[$key]['status'] === 'active' ) : ?>
							    	<dd class="dash-webinars"><a href="#webinars" data-uri="/dashboard/webinars" class="dataload" title="Webinar Archive"><span class="icon-dash-webinars"></span></a></dd>
							    <?php endif; ?>
							    <dd class="my-purchases"><a href="#my-purchases" data-uri="/dashboard/my-purchases" class="dataload" title="My purchases"><span class="icon-dashbasket"></span></a></dd>
							    <dd class="my-cpd-record"><a href="#my-cpd-record" data-uri="/dashboard/my-cpd-record" class="dataload" title="My CPD Record"><span class="icon-records"></span></a></dd>
							    <dd class="my-upcoming-events"><a href="#my-upcoming-events" data-uri="/dashboard/my-upcoming-events" class="dataload" title="My Upcoming Events"><span class="icon-events"></span></a></dd>
							    <?php

							    if ( in_array(45, $membership_id ) ||  in_array(2043, $membership_id ) ) : ?>

								<?php elseif ( !empty( $subscriptions ) ) : ?>

										<?php
										$has_active_subs = false;

										if (isset($subscriptions) && is_array($subscriptions)) {
											foreach ($subscriptions as $subs) {
												if ($subs['status']=='active') {
													$has_active_subs = true;
													break;
												}
											}
										}
											if ($has_active_subs) {
											?>
							    	<dd class="practice-development-tools"><a href="#practice-development-tools" data-uri="/dashboard/practice-development-tools" class="dataload" title="Practice Development Tools"><span class="icon-tools"></span></a></dd>
							    	<dd class="previous-newsletters"><a href="#previous-newsletters" data-uri="/dashboard/previous-newsletters" class="dataload" title="Previous Newsletters"><span class="icon-newsletters"></span></a></dd>
							    	<dd class="audio-downloads"><a href="#audio-downloads" data-uri="/dashboard/audio-downloads" class="dataload" title="Audio Downloads"><span class="icon-audio"></span></a></dd>
							    	<?php } ?>
							    <?php endif; ?>
							    <dd class="subscriptions"><a href="#subscriptions" data-uri="/dashboard/subscriptions" class="dataload" title="My Subscriptions"><span class="icon-letter"></span></a></dd>
							  	<?php if (1==2) { ?>
							    	<dd class="your-recommendations"><a href="#your-recommendations" data-uri="/dashboard/your-recommendations" class="dataload" title="Your Recommendations"><span class="icon-recommended"></span></a></dd>
							    <?php } ?>
							</dl>

							  <div id="mainTabContent" class="tabs-content">

								  <div class="content active" id="my-personal-details"><img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/general/ajax-loader.gif" class="" alt=""></div>

								  <div class="content" id="my-purchases"><img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/general/ajax-loader.gif" class="" alt=""></div>

								  <div class="content" id="my-cpd-record"><img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/general/ajax-loader.gif" class="" alt=""></div>

								  <div class="content" id="my-upcoming-events"><img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/general/ajax-loader.gif" class="" alt=""></div>

								  <div class="content" id="your-recommendations"><img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/general/ajax-loader.gif" class="" alt=""></div>

								  <div class="content" id="practice-development-tools"><img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/general/ajax-loader.gif" class="" alt=""></div>

								  <div class="content" id="previous-newsletters"><img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/general/ajax-loader.gif" class="" alt=""></div>

								  <div class="content" id="audio-downloads"><img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/general/ajax-loader.gif" class="" alt=""></div>

								  <div class="content" id="webinars"><img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/general/ajax-loader.gif" class="" alt=""></div>

								  <div class="content" id="subscriptions"><img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/general/ajax-loader.gif" class="" alt=""></div>

							  </div>

						</div>

		      </div>

	    </div>

    </div>

<?php get_footer();
