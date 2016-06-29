<?php

/*
 * Template Name: Dashboard - Previous Newsletters
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

					$types = array('client-enews','fca','innovator','client_newsletter','tax-newsletter');
		                if (isset($_GET['t']) && in_array($_GET['t'], $types) ) {
		                    $selected_type = $_GET['t'];
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

						<a href="#previous-newsletters" data-uri="/dashboard/previous-newsletters?y=<?php echo $i;?>" class="dataload gen-btn btn-color <?php echo $button;?>"><?php echo $i;?></a>

					<?php endfor; ?>
				</div>


				<div class="dash-btns">
	                <a href="#previous-newsletters" data-uri="/dashboard/previous-newsletters?<?php if (isset($selected_year)) {echo 'y='.$selected_year.'&';}?>" class="dataload gen-btn <?php if (!in_array($_GET['t'],$types)) { echo ' silver '; }?>">All</a>
	                <a href="#previous-newsletters" data-uri="/dashboard/previous-newsletters?<?php if (isset($selected_year)) {echo 'y='.$selected_year.'&';}?>t=client_newsletter" class="dataload gen-btn <?php if ($_GET['t'] =='client_newsletter') { echo ' silver ';} ?>">Quarterly Client</a>
	                <a href="#previous-newsletters" data-uri="/dashboard/previous-newsletters?<?php if (isset($selected_year)) {echo 'y='.$selected_year.'&';}?>t=client-enews" class="dataload gen-btn <?php if ($_GET['t'] =='client-enews') { echo ' silver ';} ?>">Client ENews</a>
	                <a href="#previous-newsletters" data-uri="/dashboard/previous-newsletters?<?php if (isset($selected_year)) {echo 'y='.$selected_year.'&';}?>t=fca" class="dataload gen-btn <?php if ($_GET['t'] =='fca') { echo ' silver ';} ?>">FCA</a>
	                <a href="#previous-newsletters" data-uri="/dashboard/previous-newsletters?<?php if (isset($selected_year)) {echo 'y='.$selected_year.'&';}?>t=innovator" class="dataload gen-btn <?php if ($_GET['t'] =='innovator') { echo ' silver ';} ?>">Innovator</a>
	                <a href="#previous-newsletters" data-uri="/dashboard/previous-newsletters?<?php if (isset($selected_year)) {echo 'y='.$selected_year.'&';}?>t=tax-newsletter" class="dataload gen-btn <?php if ($_GET['t'] =='tax-newsletter') { echo ' silver ';} ?>">Tax</a>
				</div>





		          	<?php

              			$args = array(
							'post_type' => 'newsletter',
							'date_query'  => array(
                            		             array(
                                    	            'year' => $selected_year
                                        	    )
                                     		),
										);

              			if (isset($_GET['t']) && in_array($_GET['t'], $types) ) {
		                    $args['tax_query'] = array(
		                                            array(  'taxonomy' => 'newscat',
		                                                    'field'    => 'slug',
		                                                    'terms'    => $_GET['t']
		                                                )
		                                      );
		                }

          				$newsletters = new WP_Query( $args );

          				if ( $newsletters->have_posts() ) :

          					while ( $newsletters->have_posts() ) :
          							$newsletters->the_post();
          				?>
	          				<div class="dash-block newsletters">

										<div class="block-copy">
			                <h4><?php the_title();?></h4>
			                <p class="date">Added <?php echo date('d F Y',strtotime(get_the_date()));?></p>
			                <p><?php echo substr(get_the_content(),0,330); ?></p>

			                <?php
			                	$link = get_field('newsletter_link');

			                	if ($link != '') :
			                	?>
			                		<a href="<?php echo $link; ?>" target="_blank" class="gen-btn btn-color orange">View</a>
			                	<?php
			                	endif;
			                ?>

			                <?php
			                	$downloadlink = get_field('newsletter_download');

			                	if ($downloadlink != '') :
			                	?>
			                		<a href="<?php echo $downloadlink; ?>" target="_blank" class="gen-btn btn-color orange">Download</a>
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