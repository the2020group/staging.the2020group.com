<?php get_header(); ?>

	<div class="row">
	
		<div class="small-12 large-8 columns" role="main">

				<article class="page post-not-found">

					<header class="article-header">

						<h1><?php _e( 'Nothing found' ); ?></h1>

					</header>

					<section class="entry-content">

						<p><?php _e( 'Nothing was not found, please search again.' ); ?></p>

					</section>

					<section class="search-form">

						<p><?php get_search_form(); ?></p>

					</section>

				</article>
			
		</div>
		
		<div class="small-12 large-4 columns">
		
			<?php get_sidebar(); ?>
				
		</div>

	</div>

<?php get_footer(); ?>