<?php /* Template Name: Persona landing page */ ?>

<?php
$maxArticles = 10;

get_header(); ?>

	<div class="row">

		<div class="small-12 large-8 columns" role="main">

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class('interest'); ?> role="article">

				<header class="article-header">

					<h1 itemprop="headline"><?php the_title(); ?>

					</h1>

				</header>

				<section class="entry-content" itemprop="articleBody">

					<?php the_content(); ?>

				</section>

			</article>

			<div class="search-results-page">

			<?php
					$term_id = get_field('persona');
					echo $term;

					$term = get_term( $term_id, 'persona' );

				?>

					<?php // Define the query
						$args = array(
							'post_type' => 'post',
							'tax_query' => array(
								array(
									'taxonomy' => 'persona',
									'field'    => 'id',
									'terms'    => $term_id,
								),
							),
						    'posts_per_page' => -1
						);
						$query = new WP_Query( $args );

					if ($query->have_posts()) { ?>

						<h1 class="archive-title persona-title" data-group="<?php echo $term_id; ?>-1">Articles about <?php echo $term->name; ?></h1>
						<div class="hideContent" data-group="<?php echo $term_id; ?>-1">

							<?php
							$count = 0;
							while ( $query->have_posts() ) : $query->the_post(); $count++;

							$class = 'search-result';
							if($maxArticles < $count) {
								$class .= ' hide-article';
							}
							?>

								<article id="post-<?php the_ID(); ?>" <?php post_class($class); ?> role="article">

									<header class="article-header">

										<h3 class="search-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>

									</header>

									<section class="entry-content">
											<?php the_excerpt( '<span class="read-more">' . __( 'Read more &raquo;' ) . '</span>' ); ?>
									</section>

									<footer class="article-footer">

					</footer>

								</article>


							<?php endwhile; ?>

							<?php
							if($maxArticles < $count) {
								echo '<a class="gen-btn orange icon right-arrow showMore" href="" data-shown="10" data-max="'.$count.'">Show more Articles about '.$term->name.'</a><br /><br />';
							}
							?>

						</div>


					<?php } // end of check for query having posts

				// use reset postdata to restore orginal query
				wp_reset_postdata(); ?>




							<?php

							$productcats = get_terms( 'product_cat', array('parent' => 10, 'exclude' => '') );

							foreach ($productcats as $cat) {
								//echo $cat->term_id.'<br/>';

								$args = array(
								    'post_type' => 'product',
								    'tax_query' => array(
										array(
											'taxonomy' => 'persona',
											'field'    => 'slug',
											'terms'    => $term->slug,
										),
										array(
											'taxonomy' => 'product_cat',
											'field'	   => 'id',
											'terms'	   => $cat->term_id,
										),
									),
								    'posts_per_page' => -1
								);
								$query = new WP_Query( $args );

								if ($query->have_posts()) { ?>

									<h1 class="archive-title persona-title" data-group="<?php echo $cat->term_id; ?>"><?php echo $cat->name; ?></h1>
									<div class="hideContent" data-group="<?php echo $cat->term_id; ?>">


									<?php
											$count = 0;
											while ( $query->have_posts() ) : $query->the_post(); $count++;

											$class = 'search-result';
											if($maxArticles < $count) {
												$class .= ' hide-article';
											}
											?>



												<article id="post-<?php the_ID(); ?>" <?php post_class($class); ?> role="article">

													<header class="article-header">

														<h3 class="search-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>



													</header>

													<section class="entry-content">
															<?php the_excerpt( '<span class="read-more">' . __( 'Read more &raquo;' ) . '</span>' ); ?>
													</section>

													<footer class="article-footer">

													</footer>

												</article>


											<?php endwhile; ?>

											<?php
											if($maxArticles < $count) {
												echo '<a class="gen-btn orange icon right-arrow showMore" href="" data-shown="10" data-max="'.$count.'">Show more '.$term->name.' Products &amp; Events</a><br /><br />';
											}
											?>
									</div>

								<?php }



							} ?>

							<?php // use reset postdata to restore orginal query
							wp_reset_postdata(); ?>

							<?php

							$productcats = get_terms( 'product_cat', array('parent' => 0, 'exclude' => '10,14,97') );

							foreach ($productcats as $cat) {
								//echo $cat->term_id.'<br/>';

								$args = array(
								    'post_type' => 'product',
								    'tax_query' => array(
										array(
											'taxonomy' => 'persona',
											'field'    => 'slug',
											'terms'    => $term->slug,
										),
										array(
											'taxonomy' => 'product_cat',
											'field'	   => 'id',
											'terms'	   => $cat->term_id,
										),
									),
								    'posts_per_page' => -1
								);
								$query = new WP_Query( $args );

								if ($query->have_posts()) { ?>

									<h1 class="archive-title persona-title" data-group="<?php echo $cat->term_id; ?>"><?php echo $cat->name; ?></h1>
									<div class="hideContent" data-group="<?php echo $cat->term_id; ?>">


									<?php
											$count = 0;
											while ( $query->have_posts() ) : $query->the_post(); $count++;

											$class = 'search-result';
											if($maxArticles < $count) {
												$class .= ' hide-article';
											}
											?>



												<article id="post-<?php the_ID(); ?>" <?php post_class($class); ?> role="article">

													<header class="article-header">

														<h3 class="search-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>



													</header>

													<section class="entry-content">
															<?php the_excerpt( '<span class="read-more">' . __( 'Read more &raquo;' ) . '</span>' ); ?>
													</section>

													<footer class="article-footer">

													</footer>

												</article>


											<?php endwhile; ?>

											<?php
											if($maxArticles < $count) {
												echo '<a class="gen-btn orange icon right-arrow showMore" href="" data-shown="10" data-max="'.$count.'">Show more '.$term->name.' Products &amp; Events</a><br /><br />';
											}
											?>
									</div>

								<?php }



							} ?>



				<?php // use reset postdata to restore orginal query
				wp_reset_postdata(); ?>

					<?php // Define the query
						$args = array(
						    'post_type' => 'pmdwebinars',
						    'tax_query' => array(
								array(
									'taxonomy' => 'persona',
									'field'    => 'slug',
									'terms'    => $term->slug,
								),
							),
						    'posts_per_page' => -1
						);
						$query = new WP_Query( $args );

					if ($query->have_posts()) { ?>

					    <h1 class="archive-title persona-title" data-group="<?php echo $term_id; ?>-3"><?php echo $term->name; ?> PMD Webinars</h1>
						<div class="hideContent" data-group="<?php echo $term_id; ?>-3">

							<?php
							$count = 0;
							while ( $query->have_posts() ) : $query->the_post(); $count++;

							$class = 'search-result';
							if($maxArticles < $count) {
								$class .= ' hide-article';
							}
							?>

								<article id="post-<?php the_ID(); ?>" <?php post_class($class); ?> role="article">

									<header class="article-header">

										<h3 class="search-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>



									</header>

									<section class="entry-content">
											<?php the_excerpt( '<span class="read-more">' . __( 'Read more &raquo;' ) . '</span>' ); ?>
									</section>

									<footer class="article-footer">

					</footer>

								</article>


							<?php endwhile; ?>

							<?php
							if($maxArticles < $count) {
								echo '<a class="gen-btn orange icon right-arrow showMore" href="" data-shown="10" data-max="'.$count.'">Show more '.$term->name.' PMD Webinars</a><br /><br />';
							}
							?>

						</div>


					<?php } // end of check for query having posts

				// use reset postdata to restore orginal query
				wp_reset_postdata(); ?>

					<?php // Define the query
						$args = array(
						    'post_type' => 'cpdwebinars',
						    'tax_query' => array(
								array(
									'taxonomy' => 'persona',
									'field'    => 'slug',
									'terms'    => $term->slug,
								),
							),
						    'posts_per_page' => -1
						);
						$query = new WP_Query( $args );

					if ($query->have_posts()) { ?>

					    <h1 class="archive-title persona-title" data-group="<?php echo $term_id; ?>-4"><?php echo $term->name; ?> CPD Webinars</h1>
						<div class="hideContent" data-group="<?php echo $term_id; ?>-4">

							<?php
							$count = 0;
							while ( $query->have_posts() ) : $query->the_post(); $count++;

							$class = 'search-result';
							if($maxArticles < $count) {
								$class .= ' hide-article';
							}
							?>

								<article id="post-<?php the_ID(); ?>" <?php post_class($class); ?> role="article">

									<header class="article-header">

										<h3 class="search-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>



									</header>

									<section class="entry-content">
											<?php the_excerpt( '<span class="read-more">' . __( 'Read more &raquo;' ) . '</span>' ); ?>
									</section>

									<footer class="article-footer">

					</footer>

								</article>


							<?php endwhile; ?>

							<?php
							if($maxArticles < $count) {
								echo '<a class="gen-btn orange icon right-arrow showMore" href="" data-shown="10" data-max="'.$count.'">Show more '.$term->name.' CPD Webinars</a><br /><br />';
							}
							?>

						</div>


	0				<?php } // end of check for query having posts

				// use reset postdata to restore orginal query
				wp_reset_postdata(); ?>

					<?php // Define the query
						$args = array(
						    'post_type' => 'page',
						    'tax_query' => array(
								array(
									'taxonomy' => 'persona',
									'field'    => 'slug',
									'terms'    => $term->slug,
								),
							),
						    'posts_per_page' => -1
						);
						$query = new WP_Query( $args );

					if ($query->have_posts()) { ?>

					    <h1 class="archive-title persona-title" data-group="<?php echo $term_id; ?>-5">Useful <?php echo $term->name; ?> Pages</h1>
						<div class="hideContent" data-group="<?php echo $term_id; ?>-5">

							<?php
							$count = 0;
							while ( $query->have_posts() ) : $query->the_post(); $count++;

							$class = 'search-result';
							if($maxArticles < $count) {
								$class .= ' hide-article';
							}
							?>

								<article id="post-<?php the_ID(); ?>" <?php post_class($class); ?> role="article">

									<header class="article-header">

										<h3 class="search-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>



									</header>

									<section class="entry-content">
											<?php the_excerpt( '<span class="read-more">' . __( 'Read more &raquo;' ) . '</span>' ); ?>
									</section>

									<footer class="article-footer">

					</footer>

								</article>


							<?php endwhile; ?>

							<?php
							if($maxArticles < $count) {
								echo '<a class="gen-btn orange icon right-arrow showMore" href="" data-shown="10" data-max="'.$count.'">Show more Useful '.$term->name.' Pages</a><br /><br />';
							}
							?>

						</div>


					<?php } // end of check for query having posts

				// use reset postdata to restore orginal query
				wp_reset_postdata(); ?>

			</div>

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
