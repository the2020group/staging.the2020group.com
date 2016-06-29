<?php get_header(); ?>


<div class="row">
	<div class="small-12 columns">
		<h1 class="testimonials-header"><?php _e( 'Testimonials' ); ?></h1>
		
		<ul class="testimonial-wrap">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<li <?php post_class(); ?>>
				
	
						<div class="testimonial-detail">
							<p>"<?php echo get_the_content(); ?>"</p>
							<h2><?php the_title(); ?></h2>
						</div>
						
			</li>
    <?php endwhile; ?>
    
    <?php do_action( 'woocommerce_after_shop_loop' ); ?>
    
    <?php endif; ?>
</div>
		
	</div>
</div>



<?php get_footer();
