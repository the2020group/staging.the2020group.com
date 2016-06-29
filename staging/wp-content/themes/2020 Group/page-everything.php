<?php 

/*
 * The template for Everything
 */

get_header(); ?>
	
	<div class="row">
	
		<div class="small-12 columns" role="main">

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">
				
				<header class="article-header">
					
					<h1><?php the_title(); ?></h1>
				
				</header>
				
				<section class="entry-content">
					
					<?php the_content(); ?>
				
				</section>
				
			</article>
			
			<?php endwhile; ?>
			
			<?php else : ?>
			
			<article class="post-not-found">
				
				<header class="not-found-header">
					
					<h2><?php _e( 'Nothing Found!' ); ?></h2>
				
				</header>
				
				<section class="not-found-content">
					
					<p><?php _e( 'Please check what you are looking for' ); ?></p>
				
				</section>
			
			</article>
			
			<?php endif; ?>
			
			<div class="below-nav">
				
				<?php posts_nav_link(' - ', '&laquo; Prev', 'Next &raquo;'); ?>
			
			</div>
		
		</div>

	</div>

<?php get_footer();