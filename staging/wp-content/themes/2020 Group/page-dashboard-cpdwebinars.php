<?php 

/*
 * Template Name: Dashboard - Audio Downloads
 */

get_header(); ?>

		<div class="dash-wrap">
			
			<div class="dash-page">
    		
	    	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	    	
	    			<div id="dash-main">
        
	          	<h2><?php the_title(); ?></h2>
	          	
	          	<div class="dash-btns">
	              <a href="#audio-downloads" data-uri="/dashboard/audio-downloads" class="dataload gen-btn btn-color silver">2014</a>
	          	
		          	<?php

              			$args = array(
											'post_type' => 'newsletter'
										);

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
		           
          	
          	</div>
        
        <?php endwhile; ?>
    
        <?php endif; ?>
        
			</div>
	    
    </div>

<?php get_footer();