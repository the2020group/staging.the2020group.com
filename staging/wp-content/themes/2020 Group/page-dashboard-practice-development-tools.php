<?php

/*
 * Template Name: Dashboard - Practice Development Tools
 */

get_header(); ?>

		<div class="dash-wrap">

	    <div class="row collapse">

	        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

	        <div class="small-12 medium-11 columns" role="main">

		        	<div id="dash-main">

		            <h2><?php the_title(); ?></h2>

		            <!--
<div class="dash-btns">
		              <a href="" class="gen-btn btn-color silver">2014</a>
		            </div>
-->

		          	<div class="pdt-content">

			          	<dl id="pdtTabs" class="tabs" data-tab>
			          		<?php

 								$products = array();
                $subscriptions = array();

                foreach ($_subscription_details as $sub) {
                  if (isset($sub['status']) && $sub['status'] === 'active') {
                    array_push($subscriptions,$sub);
                  }
                }
     

								?>
			          		<?php if ( ! empty( $subscriptions ) ) : ?>
									  <dd class="active"><a href="#strategy-profit-improvement">Strategy & Profit Improvement</a></dd>
									  <dd><a href="#practice-management">Practice Management</a></dd>
									  <dd><a href="#marketing-your-practice">Marketing Your Practice</a></dd>
									  <dd><a href="#adding-value-to-your-clients">Adding Value to Your Clients</a></dd>
									</dl>

									<div id="pdtTabContent" class="tabs-content">

										<?php

											$cats = array('strategy-profit-improvement',
														  'practice-management',
														  'marketing-your-practice',
														  'adding-value-to-your-clients');
											$active = 'active';
										foreach ($cats as $cat) :

											?>


									  <div class="content <?php echo $active;?>" id="<?php echo $cat;?>">

										<?php

				              			$args = array(
											'post_type' => 'devtools',
										    'tools_cat' => $cat
										);

				          				$tools = new WP_Query( $args );

				          				if ( $tools->have_posts() ) :

				          					while ( $tools->have_posts() ) :
				          							$tools->the_post();

				          							$file = get_field('file');
				          				?>
											    <div class="pdtTool">
											    	<a href="<?php echo admin_url('admin-ajax.php'); ?>?action=download_file&file_id=<?php the_id();?>" download="<?php the_title();?>">
													    <p><?php the_title();?></p>
													    <p class="date">Updated <?php echo date('d F Y',strtotime(get_the_modified_date()));?></p>
													</a>
											    </div>

									    	<?php
											endwhile;
										endif;

										wp_reset_postdata();
										$active = '';
										?>
									  </div>
									  <?php
									  	endforeach;
									  ?>

									</div>
								<?php else : ?>
									<p><?php printf( __( 'You have no active subscriptions. Find your first subscription in the %sstore%s.', 'woocommerce-subscriptions' ), '<a href="' . get_permalink( woocommerce_get_page_id( 'shop' ) ) . '">', '</a>' ); ?></p>
								<?php endif; ?>

		          	</div>
		       		</div>
		      </div>

	        <?php endwhile; ?>

	        <?php endif; ?>

	    </div>

		</div>

<?php get_footer();