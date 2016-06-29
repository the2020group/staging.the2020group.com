<?php 

/*
 * Template Name: Dashboard - CPD Webinars
 */

get_header(); ?>

		<div class="dash-wrap">
			
			<div class="dash-page">
    		
	    	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	    	
	    			<div id="dash-main">
        
	          	<h2><?php the_title(); ?></h2>
	          	
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
		          	
	              <a href="#cpd-webinars" data-uri="/dashboard/cpd-webinars/?y=<?php echo $i;?>" class="dataload <?php echo $button;?> gen-btn"><?php echo $i;?></a>
	              
	              <?php 
		              endfor;
	              ?>
	              
		          	<?php

              			$args = array(
											'post_type' => 'cpdwebinars',
											'date_query'  => array(
                                            array(
                                                'year' => $selected_year
                                            )
                                     ),
										);
										
										
          				$cpdWebinars = new WP_Query( $args );

          				if ( $cpdWebinars->have_posts() ) :

          					while ( $cpdWebinars->have_posts() ) :
          							$cpdWebinars->the_post();
          				?>
	          				<div class="dash-block newsletters">
										
										<div class="block-copy">
			                <h4><?php the_title();?></h4>
			                <p class="date">Added <?php echo date('d F Y',strtotime(get_the_date()));?></p>
			                <p><?php echo substr(get_the_content(),0,330); ?></p>
                      
                      <?php if (get_field('dashboard_cpd_webinar')) : ?>
                        <a href="<?php echo get_field('dashboard_cpd_webinar'); ?>" target="_blank" class="gen-btn btn-color orange">Stream</a>
                      <?php endif; ?>

                      <?php if (get_field('dashboard_cpd_webinar_mp4_link')) : ?>
                        <a href="<?php echo get_field('dashboard_cpd_webinar_mp4_link'); ?>" target="_blank" class="gen-btn btn-color orange">Download</a>
                      <?php endif; ?>
			                	
                      <?php if (get_field('dashboard_webinar_notes_link')) :?>
                        <a href="<?php echo get_field('dashboard_webinar_notes_link'); ?>" target="_blank" class="gen-btn btn-color orange">Download Notes</a>
                      <?php endif; ?>
                      
                      <?php if (get_field('dashboard_webinar_slides_link')) :?>
                        <a href="<?php echo get_field('dashboard_webinar_slides_link'); ?>" target="_blank" class="gen-btn btn-color orange">Download Slides</a>
                      <?php endif; ?>

								</div>
	 						</div>
						<?php
							endwhile;
						endif;

						wp_reset_postdata();

						?>
		           
          	
          	</div>
        
        <?php endwhile; ?>
    
        <?php endif; ?>
        
			</div>
	    
    </div>

<?php get_footer();