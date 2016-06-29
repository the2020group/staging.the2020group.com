<?php

/*
 * Template Name: Membership
 */

get_header();


// Experimenting with pulling woocommerce pricing in for individual products
global $woocommerce;

// Set up product info for each variation
$productIndividual = new WC_Product_Variable(45);
$productPartners = new WC_Product_Variable(48);


/*
echo '<h3>Individual product attrs</h3>';
print_r($productIndividual->get_available_variations());
echo '<br /><br />';
echo '<h3>Partners product attrs</h3>';
print_r($productPartners->get_available_variations());
*/



?>
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'home-hero' );?>
	<div id="membership" role="main">
    <div class="hero" style="background-image: url('<?php echo $thumb['0'];?>'); ">
      <div class="hero-content">
	      <div class="intro">
	        <h1>2020 Membership</h1>

	        <p>A leading organisation helping progressive accountants and tax professionals worldwide.</p>
	      </div>

	      <div class="row">
	      	<div class="small-12 medium-6 columns medium-offset-3" id="mem-links">
	      		<div class="options">
			        <p>View membership options<br>
			        	<span>from <?php echo get_woocommerce_currency_symbol().(get_membership_variation_price(45) );//echo get_product_variation_price(68); ?> per year</span>
			    	</p>
			    </div>
	      		<div class="options">
			        <p><a href="#2020-benefits">View membership benefits</a><br>
			        	<span>&nbsp;</span>
			    	</p>
			    </div>
	      	</div>
	      </div>
      </div>
    </div>

  	<section class="pricing" id="pricing-options">
	  	<div class="row" data-equalizer>
	  	  <div class="small-12 medium-6 large-2 large-offset-1 columns">
	      	<div class="pricing-block" data-equalizer-watch>
	      		<div class="member-title">Individual Membership</div>
	        	<div class="price-title"><div>Individual<br/>in Practice</div></div>
	        	<div class="price-content">
		        	<p class="<?php echo get_currency_css_class(); ?>"><?php echo get_woocommerce_currency_symbol().(get_membership_variation_price(45) ); ?></p>
		        	<p class="year">/Year</p>
	        	</div>
	        	<div class="actions">
  	        	<a href="/product/individual-membership/?attribute_type=individual" class="gen-btn green icon right-arrow">Learn More</a>
	        	</div>
	        </div>
		    </div>
	    	<div class="small-12 medium-6 large-2 columns">
	      	<div class="pricing-block" data-equalizer-watch>
	      		<div class="member-title">Premium Membership</div>
	        	<div class="price-title"><div>Sole<br>Practitioner</div></div>
	        	<div class="price-content">
		        	<p class="<?php echo get_currency_css_class(); ?>"><?php echo get_woocommerce_currency_symbol().(get_membership_variation_price(2034) ); ?></p>
		        	<p class="year">/Year</p>
	        	</div>
	        	<div class="actions">
  	        	<a href="/product/standard/?attribute_pa_partners=sole-practitioner" class="gen-btn green icon right-arrow">Learn More</a>
	        	</div>
	      	</div>
	    	</div>
	    	<div class="small-12 medium-4 large-2 columns">
	      	<div class="pricing-block" data-equalizer-watch>
	      		<div class="member-title">Premium Membership</div>
	        	<div class="price-title"><div><span class="number">2 - 5</span><br>Partners</div></div>
	        	<div class="price-content">
		        	<p class="<?php echo get_currency_css_class(); ?>"><?php echo get_woocommerce_currency_symbol().(get_membership_variation_price(49) ); ?></p>
		        	<p class="year">/Year</p>
	        	</div>
	        	<div class="actions">
  	        	<a href="/product/standard/?attribute_pa_partners=2-5-partners" class="gen-btn green icon right-arrow">Learn More</a>
	        	</div>
	      	</div>
	    	</div>
	    	<div class="small-12 medium-4 large-2 columns" data-equalizer-watch>
	      	<div class="pricing-block" data-equalizer-watch>
	      		<div class="member-title">Premium Membership</div>
	        	<div class="price-title"><div><span class="number">6 - 9</span><br>Partners</div></div>
	        	<div class="price-content">
		        	<p class="<?php echo get_currency_css_class(); ?>"><?php echo get_woocommerce_currency_symbol().(get_membership_variation_price(50) ); ?></p>
		        	<p class="year">/Year</p>
	        	</div>
	        	<div class="actions">
  	        	<a href="/product/standard/?attribute_pa_partners=6-9-partners" class="gen-btn green icon right-arrow">Learn More</a>
	        	</div>
	      	</div>
	    	</div>
	    	<div class="small-12 medium-4 large-2 end columns">
		    <div class="pricing-block" data-equalizer-watch>
		    	<div class="member-title">Premium Membership</div>
	      		<div class="price-title"><div><span class="number">10 - 15</span><br>Partners</div></div>
	        	<div class="price-content">
		        	<p class="<?php echo get_currency_css_class(); ?>"><?php echo get_woocommerce_currency_symbol().(get_membership_variation_price(51) ); ?></p>
		        	<p class="year">/Year</p>
	        	</div>
	        	<div class="actions">
  	        	<a href="/product/standard/?attribute_pa_partners=10-15-partners" class="gen-btn green icon right-arrow">Learn More</a>
	        	</div>
	      	</div>
	    	</div>
	    </div>

	    <div class="row">
	    	<div class="small-12 medium-10 medium-offset-1 columns">
			    <p>For 15+ Partners and national multi-office firms, price on application. Please <a href="/contact" class="gen-link">Contact Us</a>
			    <span class="excludes">Price excludes VAT</span></p>


			    
	    	</div>
	    </div>

  	</section>

  	<section class="benefits" id="2020-benefits">
  		<div class="row">
	  		<div class="small-12 columns">
		      <h3>2020 members receive all of these great benefits</h3>

        	<div class="row" data-equalizer>

	        	<?php if( have_rows('membership_receive_benefits') ): ?>

		      		<?php while( have_rows('membership_receive_benefits') ): the_row(); ?>

		      		<div class="small-12 large-4 columns">
		      			<div class="benefit-block benefit01" data-equalizer-watch>

			      		<h4><?php the_sub_field('benefit_title'); ?></h4>

								<?php

								if( have_rows('benefit_list') ): ?>
									<ul class="benefit-list">
									<?php

									while( have_rows('benefit_list') ): the_row();

										?>
										<li><?php the_sub_field('benefit_list_item'); ?></li>

									<?php endwhile; ?>
									</ul>
								<?php endif; ?>

			      		</div>
		      		</div>

							<?php endwhile; ?>
						<?php endif; ?>

				</div>
		  </div>
  	</section>

  	<section class="whyjoin">
		  <div class="row">
      	<div class="small-12 large-5 large-offset-1 columns">
	      	<?php the_field('membership_why_join_us'); ?>
				</div>
      	<div class="small-12 large-5 end columns">
	      	<div class="embed-container">
	      		<?php the_field('membership_why_join_video'); ?>
	      	</div>
    		</div>
    	</div>
    </section>

  	<div id="receive">

      	<h4>2020 Members receive:</h4>

      	<div class="membersCarousel">
	      	<?php if( have_rows('membership_receive') ): ?>
							<?php while( have_rows('membership_receive') ): the_row();

								// vars
								$receiveimage = get_sub_field('receive_image');
								$receivecontent = get_sub_field('receive_text');
								$receivelink = get_sub_field('receive_image_link');

								?>
								<div>
									<div class="row">
								  	<div class="small-12 large-5 large-offset-1 columns">

										<?php if( $receivelink ): ?>
											<a href="<?php echo $receivelink; ?>">
										<?php endif; ?>

											<img src="<?php echo $receiveimage['url']; ?>" alt="<?php echo $receiveimage['alt'] ?>" class="webinar-img" />

										<?php if( $receivelink ): ?>
											</a>
										<?php endif; ?>

										</div>
										<div class="small-12 large-5 end columns">
											<div class="carousel-content">
												<?php echo $receivecontent; ?>
											</div>
										</div>
									</div>
								</div>
							<?php endwhile; ?>
					<?php endif; ?>

		    </div>

      </div>

  	<div class="also">
		<h4>2020 Members also receive:</h4>
    	<p>(Not all included with Individual Memberships)</p>
    	<div class="row" data-equalizer>
      	<?php if( have_rows('membership_also_receive') ): ?>
      		<?php while( have_rows('membership_also_receive') ): the_row();
				$content = get_sub_field('also_receive_content'); ?>
				<div class="small-12 large-4 columns">
					<div class="also-block" data-equalizer-watch>
	    		   		<span class="icon-check"></span>
						<?php echo $content; ?>
					</div>
				</div>
			<?php endwhile; ?>
		<?php endif; ?>
      	</div>
    	<div class="row">
    		<a class="gen-btn green small-12 large-4 columns large-offset-4" id="become" href="#pricing-options">I would like to become a 2020 Member</a>
    	</div>
    </div>
    
		<div class="benchmark-tool">
    	<?php include('includes/block-benchmark-tool.php'); ?>
		</div>

  	<?php include_once('includes/block-testimonials.php'); ?>

</div>

	<?php endwhile; else : ?>

<?php endif; ?>

<?php get_footer();