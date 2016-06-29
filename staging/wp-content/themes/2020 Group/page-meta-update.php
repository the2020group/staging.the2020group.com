<?php
 /* Template Name: Meta update */
get_header(); ?>

	<div class="row">

		<div class="small-12 columns" role="main">

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">

				<header class="article-header">

					<h1><?php the_title(); ?></h1>

				</header>

				<section class="entry-content">

					<?php the_content(); ?>

				</section>

			</article>


			<?php

				$meta = $wpdb->get_results("SELECT * FROM $wpdb->usermeta WHERE meta_key LIKE 'event_%'");
				$count = $wpdb->get_results("SELECT COUNT(*) FROM $wpdb->usermeta WHERE meta_key LIKE 'event_%'");

				foreach ($meta as $entry) {
					$user_id	= $entry->user_id;
					$key 		= $entry->meta_key;
					$value 		= $entry->meta_value;

					$order_id = str_replace('event_', '', $key);
					$order_year = explode('|', $value)[1];
					$company_id = get_user_meta($user_id, 'is_company_id', true);
					$company_users = $wpdb->get_col('SELECT user_id FROM '.$wpdb->usermeta.' WHERE meta_key = "is_company_id" AND meta_value = "'.$company_id.'"');
					$company_orders = array();

					foreach ($company_users as $company_user) {
						$user_orders = array();
						$user_orders = get_all_user_orders($company_user,'completed',true, false);
						if($user_orders) {
							foreach ($user_orders as $order) {
								//error_log($order->ID);
								$company_orders[] = $order->ID;
							}
						}
					}

					if($company_orders) {
						foreach ($company_orders as $company_order) {
							$query_select_order_items   =  $wpdb->get_col('SELECT order_item_id as id FROM '.$wpdb->prefix.'woocommerce_order_items WHERE order_id = '.$company_order.'');

							$product_ids = array();

							foreach ($query_select_order_items as $order_items) {
								$query_select_product_ids = $wpdb->get_col('SELECT meta_value as product_id FROM '.$wpdb->prefix.'woocommerce_order_itemmeta WHERE meta_key=%s AND order_item_id = '.$order_items.'');

								$product_ids[] = $query_select_product_ids;

							}
							error_log(print_r($product_ids,1));

    						/*$query_select_product_ids   =  $wpdb->get_results('SELECT meta_value as product_id FROM '.$wpdb->prefix.'woocommerce_order_itemmeta WHERE meta_key=%s AND order_item_id IN ("'.$query_select_order_items.'"');
    						error_log(print_r($query_select_product_ids,1));*/
						}
					}

					// Check if user is parent/child
					$parent = get_user_meta($user_id,'2020_parent_account', true);
					if($parent) {
						//error_log('is parent');
					}
					else {
						//error_log('is child');
					}

					exit;
				};
			?>

			<?php endwhile; ?>

			<?php else : ?>

			<article class="post-not-found">

				<header class="not-found-header">

					<h2><?php _e( 'Nothing Found!' ); ?></h2>

				</header>

				<section class="not-found-content">

					<p><?php _e( 'Please check what you are looking for' ); ?></p>

				</section>

			</article>

			<?php endif; ?>

			<div class="below-nav">

				<?php posts_nav_link(' - ', '&laquo; Prev', 'Next &raquo;'); ?>

			</div>

		</div>

	</div>

<?php get_footer();