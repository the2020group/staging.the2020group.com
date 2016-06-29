<?php get_header(); ?>

	<div class="row">

		<div class="small-12 large-8 columns" role="main">

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">

				<header class="article-header">

					<h1 itemprop="headline"><?php the_title(); ?>

					<?php if ( $post->post_parent == '196' ) { ?>


					<?php

					$image = get_field('services_logo');

					if( !empty($image) ):

						// vars
						$alt = $image['alt'];

						// thumbnail
						$size = 'services-logo';
						$thumb = $image['sizes'][ $size ];
						$width = $image['sizes'][ $size . '-width' ];
						$height = $image['sizes'][ $size . '-height' ];

						 ?>

						<img src="<?php echo $thumb; ?>" alt="<?php echo $alt; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" class="title-logo" />

					<?php endif; ?>

					<?php } ?>
					</h1>

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

		<div class="small-12 large-4 columns">

			<?php get_sidebar(); ?>

		</div>

	</div>

<?php get_footer(); ?>
