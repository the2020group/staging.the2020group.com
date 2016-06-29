<?php

/*
 * Template Name: Dashboard - Audio Downloads New
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

	              <a href="#audio-downloads" data-uri="/dashboard/audio-downloads/?y=<?php echo $i;?>" class="dataload <?php echo $button;?> gen-btn"><?php echo $i;?></a>

	              <?php
		              endfor;
	              ?>

		          	<?php

              			$args = array(
							'post_type' => 'audio',
							'date_query'  => array(
                            		             array(
                                    	            'year' => $selected_year
                                        	    )
                                     		),
										);


          				$audioFiles = new WP_Query( $args );

          				if ( $audioFiles->have_posts() ) :

          					while ( $audioFiles->have_posts() ) :
          							$audioFiles->the_post();
          				?>
	          				<div class="dash-block newsletters">

										<div class="block-copy">
			                <h4><?php the_title();?></h4>
			                <p class="date">Added <?php echo date('d F Y',strtotime(get_the_date()));?></p>
			                <p><?php echo substr(get_the_content(),0,330); ?></p>

			               <?php
			                	$audioFilesLink = get_field('audio_file');

			                	if ($audioFilesLink != '') :
			                	?>
			                		<a href="<?php echo $audioFilesLink; ?>" target="_blank" class="gen-btn btn-color orange">Download</a>
			                	<?php
			                	endif;
			                ?>
								</div>
	 						</div>
						<?php
							endwhile;
						endif;

						wp_reset_postdata();

						?>

<?php else : ?>
          <p><?php printf( __( 'You have no active subscriptions. Find your first subscription in the %sstore%s.', 'woocommerce-subscriptions' ), '<a href="' . get_permalink( woocommerce_get_page_id( 'shop' ) ) . '">', '</a>' ); ?></p>
          <?php endif; ?>
          	</div>

        <?php endwhile; ?>

        <?php endif; ?>

			</div>

    </div>

<?php get_footer();