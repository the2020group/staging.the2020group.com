<?php

/*
 * Template Name: Dashboard - Webinars
 */

get_header(); ?>

		<div class="dash-wrap">

			<div class="dash-page">

	    	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

	    			<div id="dash-main">

	          	<h2><?php the_title(); ?></h2>
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
	          	<?php

	          	  if (isset($_GET['y']) && is_numeric($_GET['y'])) {
                    $selected_year = $_GET['y'];
                }
                $current_year = date('Y');

                $first_year = 2014;


		          	// if no y provided set it to the current year
                if (!isset($_GET['y'])) {
                    //$display_year = $current_year;
                    $selected_year = $current_year;

                }
                else {

                    // if trying to access a year that is too far back set it to the first year
                    if ($selected_year < $first_year) {
                        $selected_year = $first_year;
                    }
                    // future year will be redirected to current year
                    elseif ($selected_year > $current_year) {
                        $selected_year = $current_year;
                    }
                    else {
                        $selected_year = $_GET['y'];
                    }

                }

              ?>

	          	<div class="dash-btns">
		          	<?php for ($i=$current_year; $i>=$first_year; $i--) : ?>

                    <?php

                        $button = 'lsilver';

                        if ( isset($selected_year) && $i==$selected_year ) {
                            $button = 'silver';
                        }

                    ?>

	              <a href="#webinars" data-uri="/dashboard/webinars?y=<?php echo $i;?>" class="dataload <?php echo $button;?> gen-btn"><?php echo $i;?></a>

	              <?php
		              endfor;
	              ?>

		          	<?php

              			$args = array(
											'post_type' => array('pmdwebinars', 'cpdwebinars'),
											'date_query'  => array(
                                            array(
                                                'year' => $selected_year
                                            )
                                     ),
										);


          				$pmdWebinars = new WP_Query( $args );

          				if ( $pmdWebinars->have_posts() ) :

          					while ( $pmdWebinars->have_posts() ) :
          							$pmdWebinars->the_post();
          				?>
	          				<div class="dash-block newsletters">

										<div class="block-copy">
			                <h4><?php the_title();?></h4>
			                <p class="date">Added <?php echo date('d F Y',strtotime(get_the_date()));?></p>
			                <p><?php echo substr(get_the_content(),0,330); ?></p>

                      <?php if (get_field('dashboard_pmd_webinar')) : ?>
                        <a href="<?php echo get_field('dashboard_pmd_webinar'); ?>" target="_blank" class="gen-btn btn-color orange">Stream Webinar</a>
                      <?php endif; ?>

                      <?php if (get_field('dashboard_pmd_webinar_mp4_link')) : ?>
                        <a href="<?php echo get_field('dashboard_pmd_webinar_mp4_link'); ?>" target="_blank" class="gen-btn btn-color orange">Download Webinar</a>
                      <?php endif; ?>

                      <?php if (get_field('dashboard_cpd_webinar')) : ?>
                        <a href="<?php echo get_field('dashboard_cpd_webinar'); ?>" target="_blank" class="gen-btn btn-color orange">Stream Webinar</a>
                      <?php endif; ?>

                      <?php if (get_field('dashboard_webinar_mp4_link')) : ?>
                        <a href="<?php echo get_field('dashboard_webinar_mp4_link'); ?>" target="_blank" class="gen-btn btn-color orange">Download Webinar</a>
                      <?php endif; ?>

                      <?php if (get_field('dashboard_webinar_notes_link')) :?>
                        <a href="<?php echo get_field('dashboard_webinar_notes_link'); ?>" target="_blank" class="gen-btn btn-color orange">Notes</a>
                      <?php endif; ?>

                      <?php if (get_field('dashboard_webinar_slides_link')) :?>
                        <a href="<?php echo get_field('dashboard_webinar_slides_link'); ?>" target="_blank" class="gen-btn btn-color orange">Slides</a>
                      <?php endif; ?>

								</div>
	 						</div>
						<?php

							endwhile;
						endif;

						wp_reset_postdata();

						?>

            <?php else : ?>

            <?php

            // Let's check if this is a child user account
            //$parent_user_id = get_user_meta($user_id,'2020_parent_account',true);

            if (1==2 && $parent_user_id>0) {



                    $args = array(
                      //'user_id'   => $parent_user_id,
                      'post_type' => array('pmdwebinars', 'cpdwebinars'),
                      'date_query'  => array(
                                            array(
                                                'year' => $selected_year
                                            )
                                     ),
                    );


                  $pmdWebinars = new WP_Query( $args );

                  if ( $pmdWebinars->have_posts() ) :

                    while ( $pmdWebinars->have_posts() ) :

                      $pmdWebinars->the_post();

                      $current_product_id = (int)get_the_ID();

                      $assigned = get_assigned_users($current_product_id, '2015', $parent_user_id);
                      // if (count($assigned)>0) {
                      //   foreach ($assigned as $a_id=>$a) {
                      //     if ($a_id == $user_id) {




                    ?>
                    <div class="dash-block newsletters">

                    <div class="block-copy">
                      <h4><?php the_title();?></h4>
                      <p class="date">Added <?php echo date('d F Y',strtotime(get_the_date()));?></p>
                      <p><?php echo substr(get_the_content(),0,330); ?></p>

                      <?php if (get_field('dashboard_pmd_webinar')) : ?>
                        <a href="<?php echo get_field('dashboard_pmd_webinar'); ?>" target="_blank" class="gen-btn btn-color orange">Stream Webinar</a>
                      <?php endif; ?>

                      <?php if (get_field('dashboard_pmd_webinar_mp4_link')) : ?>
                        <a href="<?php echo get_field('dashboard_pmd_webinar_mp4_link'); ?>" target="_blank" class="gen-btn btn-color orange">Download Webinar</a>
                      <?php endif; ?>

                      <?php if (get_field('dashboard_cpd_webinar')) : ?>
                        <a href="<?php echo get_field('dashboard_cpd_webinar'); ?>" target="_blank" class="gen-btn btn-color orange">Stream Webinar</a>
                      <?php endif; ?>

                      <?php if (get_field('dashboard_webinar_mp4_link')) : ?>
                        <a href="<?php echo get_field('dashboard_webinar_mp4_link'); ?>" target="_blank" class="gen-btn btn-color orange">Download Webinar</a>
                      <?php endif; ?>

                      <?php if (get_field('dashboard_webinar_notes_link')) :?>
                        <a href="<?php echo get_field('dashboard_webinar_notes_link'); ?>" target="_blank" class="gen-btn btn-color orange">Notes</a>
                      <?php endif; ?>

                      <?php if (get_field('dashboard_webinar_slides_link')) :?>
                        <a href="<?php echo get_field('dashboard_webinar_slides_link'); ?>" target="_blank" class="gen-btn btn-color orange">Slides</a>
                      <?php endif; ?>

                </div>
              </div>
            <?php


                      //     }
                      //   }
                      // }


              endwhile;
            endif;

            wp_reset_postdata();

            ?>

            <?php
             }
            ?>

          <p><?php printf( __( 'You have no active subscriptions. Find your first subscription in the %sstore%s.', 'woocommerce-subscriptions' ), '<a href="' . get_permalink( woocommerce_get_page_id( 'shop' ) ) . '">', '</a>' ); ?></p>
          <?php endif; ?>

          	</div>

        <?php endwhile; ?>

        <?php endif; ?>

			</div>

    </div>

<?php get_footer();