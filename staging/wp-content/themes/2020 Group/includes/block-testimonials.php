<?php

//  global $post;
	$categories = array();
	if (is_object($post)) {
	  $categoryTerms = wp_get_post_terms( $post->ID, 'product_cat' );
	  foreach ( $categoryTerms as $categoryTerm ) {
	  	$categories[] = $categoryTerm->slug;
	  };
	}

//print_r($categoryTerms);

  if( get_field('show_testimonials') ) : ?>

	<div class="side-block quotes-block">
		<div class="quotes">
			<div class="quoteCarousel">

				<?php
					$curatedTestimonials = get_field('select_testimonials');

					// If admin has chosen some specific testimonials on this page
					if($curatedTestimonials) : ?>

				    <?php foreach( $curatedTestimonials as $singleCurated) : ?>

				        <?php setup_postdata($singleCurated); ?>
								<?php $affiliationLink = get_field('testimonial_link', $singleCurated->ID); ?>
								<?php $testimonialTitle = get_the_title($singleCurated->ID); ?>

								<div class="testimonialItem">
						      <div class="row">
						        <div class="small-10 small-offset-1 columns">
						        	<?php the_content(); ?>
						          <p class="affiliateLink">
					              <?php if($affiliationLink) : ?>
				                  <a href="<?php echo $affiliationLink; ?>" target="_blank" rel="nofollow">
														<strong><?php echo $testimonialTitle; ?></strong>
				                  </a>
				                <?php else : ?>
				                  <strong><?php echo $testimonialTitle; ?></strong>
				                <?php endif; ?>
						          </p>
						        </div>
						      </div>
						    </div>

				    <?php endforeach; ?>

				    <?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>

					<?php else :
          // admin has not chosen any specific testimonials - but 'show testimonials' textbox is ticked
          ?>

				    <?php

				    $testimonial_args = array(
				  		'post_type' => 'testimonials',
				  		'posts_per_page' => 5,
				  		'post_status' => 'publish',
				  		'orderby' => 'rand',
				  		'tag__not_in' => array(111)
				    );

				    $testimonials = get_posts( $testimonial_args );
				    foreach ( $testimonials as $testimonial ) :
				    setup_postdata( $testimonial );

				    $testimonialContent = get_the_content();
				    $affiliation = get_field('testimonial_byline', $testimonial->ID);
				    $affiliationLink = get_field('testimonial_link', $testimonial->ID);

				    ?>

				    <div class="testimonialItem">
				      <div class="row">
				        <div class="small-10 small-offset-1 columns">
				        	<p class="ita"><?php echo $testimonialContent; ?></p>
				          <p><strong><?php echo get_the_title($testimonial->ID); ?>
				            <?php
				              if($affiliation) : ?>,&nbsp;
				                <?php if($affiliationLink) : ?>
				                  <a href="<?php echo $affiliationLink; ?>" target="_blank" rel="nofollow" class="affiliateLink"><?php echo $affiliation; ?></a>
				                <?php else : ?>
				                  <?php echo $affiliation; ?>
				                <?php endif;
				              endif; ?>
				          </strong></p>
				        </div>
				      </div>
				    </div>

				    <?php endforeach;
				    wp_reset_postdata(); ?>

					<?php endif; ?>


			</div>
	  </div>
	</div>

<?php endif; ?>

<?php
 if(!is_array($categories)) {
 	$categories = array($categories);
 }

 if ( (in_array( 'webinars', $categories )) ||  (in_array( 'acca-webinars', $categories ))) :
  // If we're looking at webinars - show the ones tagged a 'webinar' in the main testimonial view instead of the above

	//print_r($categories);

  if( !get_field('show_testimonials') ) : ?>


  <div class="side-block quotes-block">
  	<div class="quotes">
  		<div class="quoteCarousel">
            <?php
  			    $testimonial_args = array(
  			  		'post_type' => 'testimonials',
  			  		'posts_per_page' => 10,
  			  		'post_status' => 'publish',
  			  		'orderby' => 'rand',
              		'tag' => 'webinar'
  			    );

  			    $testimonials = get_posts( $testimonial_args );
  			    foreach ( $testimonials as $testimonial ) :
  			    setup_postdata( $testimonial );

  			    $testimonialContent = get_the_content();
  			    $affiliation = get_field('testimonial_byline', $testimonial->ID);
  			    $affiliationLink = get_field('testimonial_link', $testimonial->ID);

  			    ?>

  			    <div class="testimonialItem">
  			      <div class="row">
  			        <div class="small-10 small-offset-1 columns">
  			        	<p class="ita"><?php echo $testimonialContent; ?></p>
  			          <p><strong><?php echo get_the_title($testimonial->ID); ?>
  			            <?php
  			              if($affiliation) : ?>,&nbsp;
  			                <?php if($affiliationLink) : ?>
  			                  <a href="<?php echo $affiliationLink; ?>" target="_blank" rel="nofollow" class="affiliateLink"><?php echo $affiliation; ?></a>
  			                <?php else : ?>
  			                  <?php echo $affiliation; ?>
  			                <?php endif;
  			              endif; ?>
  			          </strong></p>
  			        </div>
  			      </div>
  			    </div>

  			    <?php endforeach;
  			    wp_reset_postdata(); ?>

  		</div>
    </div>
  </div>

<?php endif;
endif; ?>