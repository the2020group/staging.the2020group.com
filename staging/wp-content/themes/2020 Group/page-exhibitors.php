<?php

/*
 * Template Name: Exhibitors
 */

get_header(); ?>

		<?php if (have_posts()) : the_post(); ?>

    <div class="row">
      <div class="small-12 medium-8 columns">
        <div class="exhibitors-intro">
        	<?php the_content(); ?>
        </div>
      </div>
      <div class="small-12 medium-3 columns">
        <!-- <a href="#exhibitor-block" class="gen-btn orange icon down-arrow opps-btn">Opportunities for Exhibitors</a> -->
      </div>
    </div>

    <div class="row">
      <div class="small-12 columns">
	      <div id="exhibitor-block">
		      <ul class="row collapse">

			     	<?php if( have_rows('exhibitor') ): ?>

			      	<?php $counter = 0; ?>

			      	<?php while( have_rows('exhibitor') ): the_row();

				      	$name = get_sub_field('exhibitor_name');
								$image = get_sub_field('exhibitor_logo');
								$content = get_sub_field('exhibitor_content');

								// thumbnail
								$size = 'exhibitor-logo-r';
				        $width = $image['sizes'][ $size . '-width' ];
				        $height = $image['sizes'][ $size . '-height' ];

								?>

								<li class="small-6 medium-3 columns end">

									<a href="#exhibitor-content-<?php echo $counter; ?>" class="exhibitors-inline exhibitor-logo">

									 <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" >

									</a>

									<div id="exhibitor-content-<?php echo $counter; ?>" class="exhibitor-popup" style="display: none;">

										<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" >

										<p><?php echo $content; ?></p>

									 </div>

								</li>

								<?php $counter++; ?>

						<?php endwhile; ?>

			  		<?php endif; ?>

		      </ul>
	      </div>
     	</div>
    </div>

    <div id="exhibitor-opps-wrap">
	    <div class="row">
		    <div class="small-12 columns">
			    <div class="exhibitor-opps">
		    	<?php the_field('exhibitor_opportunities'); ?>
			    </div>
		    </div>
	    </div>
	  </div>

    <?php endif; ?>

<?php get_footer();
