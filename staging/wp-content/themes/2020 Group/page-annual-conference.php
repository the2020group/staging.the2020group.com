<?php

/*
 * Template Name: Generic Conference
 */


	get_header(); ?>

	<div class="row">

		<div class="small-12 large-8 columns" role="main">

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">

				<header class="article-header">

					<h2 itemprop="headline"><?php the_title(); ?></h2>

				</header>

				<section class="entry-content conference-page" itemprop="articleBody">
					<?php the_content(); ?>

					<?php
						//Exhibitors
						$exhibitors_title = get_field('exhibitor_headline');
						$exhibitors_intro = get_field('exhibitor_section_intro');

						$exhibitor_objects = get_field('exhibitor');

						if(strlen($exhibitors_title)):
							echo '<h3>'.$exhibitors_title.'</h3>';
							echo $exhibitors_intro;


					?>

					<div class="exhibitors-block">
						<div class="exhibitor-content">
							<div class="row">

							<?php
								if( $exhibitor_objects ):
							?>
								<div class="small-12 columns">
						      <div id="exhibitor-block">
							      <div class="row collapse" data-equalizer>
								      <?php $counter = 0; ?>

								     	<?php foreach( $exhibitor_objects as $post): // variable must be called $post (IMPORTANT) ?>
					        			<?php setup_postdata($post);

								      		$name = get_the_title();
													$phone = get_field('exhibitor_phone');
													$email = get_field('exhibitor_email');

													$image = wp_get_attachment_image_src(get_field('exhibitor_logo'), 'exhibitor-logo-r');
									       			$width = $image['sizes'][ $size ];
									        		$height = $image['sizes'][ $size ];



													?>

													<div class="small-6 medium-3 columns end">

														<a href="#exhibitor-content-<?php echo $counter; ?>" class="exhibitors-inline exhibitor-logo" data-equalizer-watch>

														 <?php $image = wp_get_attachment_image_src(get_field('exhibitor_logo'), 'exhibitor-logo-r'); ?>
														 <img src="<?php echo $image[0]; ?>" alt="<?php echo get_the_title(get_field('exhibitor_logo')) ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" />

														</a>

														<div id="exhibitor-content-<?php echo $counter; ?>" class="exhibitor-popup" style="display: none;">

														<?php $image = wp_get_attachment_image_src(get_field('exhibitor_logo'), 'exhibitor-logo-r'); ?>
														 <img src="<?php echo $image[0]; ?>" alt="<?php echo get_the_title(get_field('exhibitor_logo')) ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" />

															<p><?php echo the_content(); ?></p>
															<p><?php echo 'T: '.$phone; ?></p>
															<p><?php echo '<a href="mailto:'.$email.'" class="gen-link">'.$email.'</a>'; ?></p>
															<?php if(get_field('exhibitor_website')) : ?><p><a href="http://<?php the_field('exhibitor_website'); ?>" target="_blank" class="gen-link"><?php the_field('exhibitor_website'); ?></a></p><?php endif; ?>
														 </div>

													</div>

													<?php $counter++; ?>

											<?php endforeach; ?>

								  		<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>

							      </div>
						      </div>
					     	</div>

							<?php
								endif;
							?>

							</div>
						</div>
					</div>
					<?php
						endif; //End exhibitors block
						$highlights_title = get_field('conference_highlights_headline');
						$highlights_intro = get_field('conference_highlights_intro_content');

						if(strlen($highlights_title)):
							echo '<h4>'.$highlights_title.'</h4>';
							echo wpautop($highlights_intro);
						endif;


					?>

					<?php if( have_rows('gallery_items') ): ?>

					 	<ul class="gallSlides">

			      	<?php $counter = 0; ?>

			      	<?php while( have_rows('gallery_items') ): the_row();

				      	$image = wp_get_attachment_image_src(get_sub_field('gallery_item'), 'conf-imagesm');
				      	$image2 = wp_get_attachment_image_src(get_sub_field('gallery_item'), 'conf-image');

								?>

								<li class="slide">

									<a href="#gallery-<?php echo $counter; ?>" class="fancybox-inline" rel="gallery1">
										<img src="<?php echo $image[0]; ?>" alt="<?php echo get_the_title(get_sub_field('gallery_item')) ?>" />

									</a>
									<div id="gallery-<?php echo $counter; ?>" style="display: none;">

										<img src="<?php echo $image2[0]; ?>" alt="<?php echo get_the_title(get_sub_field('gallery_item')) ?>" />

									</div>

								</li>

								<?php $counter++; ?>

						<?php endwhile; ?>

						</ul>

			  		<?php endif; ?>

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

			<div class="conference-sidebar">

				<?php if(get_field('guest_speaker_image')): ?>

				<?php

					$image = wp_get_attachment_image_src(get_field('guest_speaker_image'), 'sidepanel-image');

				?>

				<div class="guest">

					<h3><?php the_field('guest_speaker_title'); ?></h3>

					<img src="<?php echo $image[0]; ?>" alt="<?php echo get_the_title(get_field('guest_speaker_image')) ?>" />

				</div>

				<?php endif; ?>

				<?php

					$post_objects = get_field('speaker_list_selection');

					if( $post_objects ): ?>

				<div class="speakers">
					<p><?php the_field('speakers_title'); ?></p>

						<ul class="speakersList">
					    <?php foreach( $post_objects as $post): // variable must be called $post (IMPORTANT) ?>
					        <?php setup_postdata($post);

						        $thumb_src = null;
										if ( has_post_thumbnail($post->ID) ) {
											$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'teamimage' );
											$thumb_src = $src[0];
										}

					        ?>
					        <li class="slide">
										<a href="#speaker-profile-<?php the_ID(); ?>" class="fancybox-inline">
											<?php the_title(); ?>
										</a>

										<div id="speaker-profile-<?php the_ID(); ?>" style="display: none; ">
								    	<div class="member-block-pop">
										    <div class="row collapse">
											    <div class="small-12 medium-6 columns">

											    <?php if ( $thumb_src ): ?>
														<img src="<?php echo $thumb_src; ?>" alt="<?php the_title(); ?>" class="profile-image">
													<?php endif; ?>

											    </div>
													  <div class="small-12 medium-6 columns">

													    <div class="team-pop-wrap">
														    <h3><?php the_title(); ?></h3>

														    <?php if ( $profile = get_field('team_title') ): ?>
														    	<h4><?php the_field('team_title'); ?></h4>
														    <?php endif; ?>

																<?php if ( $profile = get_field('team_telephone') ): ?>
														    <ul class="iconListing">
															  <?php endif; ?>

															    <?php if ( $profile = get_field('team_telephone') ): ?>
															    <li><a href="tel:<?php the_field('team_telephone'); ?>"><i class="icon-telephone icon"></i> <?php the_field('team_telephone'); ?></a></li>
															    <?php endif; ?>

															    <?php if ( $profile = get_field('team_email') ): ?>
															    <li><a href="mailto:<?php echo antispambot( get_field('team_email') ); ?>"><i class="icon-letter icon"></i> Email <?php the_title(); ?></a></li>
															    <?php endif; ?>

															    <?php if ( $profile = get_field('team_linkedin') ): ?>
															    <li><a href="<?php the_field('team_linkedin'); ?>" target="_blank"><i class="icon-linkedin icon"></i> LinkedIn profile</a></li>
															    <?php endif; ?>

														    <?php if ( $profile = get_field('team_telephone') ): ?>
														    </ul>
														     <?php endif; ?>

															  <?php the_content(); ?>
														  </div>
														</div>
									    		</div>
									    	</div>
									    </div>
									</li>
					    <?php endforeach; ?>
					    </ul>
					    <?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
				</div>

				<?php endif; ?>

				<?php if( get_field('product_download') ): ?>
					<a href="<?php the_field('product_download'); ?>" class="side-block download-btn" target="_blank">
						Download <?php the_field('product_download_name'); ?>
					</a>
				<?php endif; ?>


				<?php if(get_field('venue_information')): ?>

				<div class="venue">
					<div class="venue-inner">
						<h3><?php the_field('venue_title'); ?></h3>

						<?php the_field('venue_information'); ?>
					</div>
				</div>

				<?php endif; ?>

				<?php if(get_field('previous_winners_gallery')): ?>
				<div class="previous">
					<div class="previous-inner">

						<h3><?php the_field('previous_winners_title'); ?></h3>
						<?php if( have_rows('previous_winners_gallery') ): ?>

					 	<ul class="winnersSlides">

			      	<?php $counter = 0; ?>

			      	<?php while( have_rows('previous_winners_gallery') ): the_row();

				      	$image = wp_get_attachment_image_src(get_sub_field('winners_image'), 'conf-imagesm');
				      	$image2 = wp_get_attachment_image_src(get_sub_field('winners_image'), 'conf-image');

								?>

								<li class="slide">

									<a href="#prevgallery-<?php echo $counter; ?>" class="fancybox-inline" rel="gallery2">

									 <img src="<?php echo $image[0]; ?>" alt="<?php echo get_the_title(get_field('winners_image')) ?>" >

									</a>
									<div id="prevgallery-<?php echo $counter; ?>" style="display: none;">

										<img src="<?php echo $image2[0]; ?>" alt="<?php echo get_the_title(get_field('winners_image')) ?>" >

									</div>

								</li>

								<?php $counter++; ?>

						<?php endwhile; ?>

						</ul>

			  		<?php endif; ?>


						<?php the_field('previous_winners_text'); ?>
					</div>
				</div>
				<?php endif; ?>

				<?php include_once('includes/block-testimonials.php'); ?>
			</div>
		</div>
	</div>

<?php get_footer(); ?>
