<?php get_header(); ?>

	<div class="row">
	
		<div class="small-12 columns" role="main">

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">

				<header class="article-header">

					<h2 itemprop="headline"><?php the_title(); ?></h2>

				</header>

				<section class="entry-content" itemprop="articleBody">

					<?php the_content(); ?>

				</section>

			</article>

			<?php endwhile; else : ?>

			<article class="post-not-found">
			
				<header class="not-found-header">
					
					<h2><?php _e( 'Nothing Found!' ); ?></h2>
				
				</header>
				
				<section class="not-found-content">
					
					<p><?php _e( 'Please check what you are looking for.' ); ?></p>
				
				</section>
			
			</article>

			<?php endif; ?>

		</div>

	</div>

<?php get_footer(); ?>
