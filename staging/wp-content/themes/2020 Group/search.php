<?php get_header(); ?>
	
	<div class="row">
	
		<div class="small-12 large-8 columns" role="main">
			
			<div class="search-results-page">
		
			<h1 class="archive-title"><span><?php _e( 'Search Results for:', '%s' ); ?></span> <?php echo esc_attr(get_search_query()); ?></h1>

				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class('search-result'); ?> role="article">

								<header class="article-header">

									<h3 class="search-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>

								</header>

								<section class="entry-content">
										<?php the_excerpt( '<span class="read-more">' . __( 'Read more &raquo;' ) . '</span>' ); ?>
								</section>

								<footer class="article-footer">
                  <?php 
                  $list = get_the_category_list(',');
                  if (($list)) {
	                  printf( __( 'Filed under: %1$s' ), get_the_category_list(',') ); 
    			  }
    			  ?>
                </footer>

							</article>

				<?php endwhile; ?>

								

				<?php else : ?>

						<article id="post-not-found" class="hentry cf">
	
							<header class="article-header">
								<h3><?php _e( 'Post not found!' ); ?></h3>
							</header>
							
							<section class="entry-content">
								<p><?php _e( 'Try searching again' ); ?></p>
								<?php get_search_form(); ?>
							</section>
							
						</article>

				<?php endif; ?>
				
			</div>

		</div>

		<div class="small-12 large-4 columns" role="main">
		
			<?php get_sidebar(); ?>
			
		</div>				

	</div>

<?php get_footer(); ?>
