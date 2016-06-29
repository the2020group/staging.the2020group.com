<?php

/*
 * Template Name: Regional Conference
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
													$image = get_field('exhibitor_logo');
													$phone = get_field('exhibitor_phone');
													$email = get_field('exhibitor_email');

													// thumbnail
													$size = 'exhibitor-logo-r';
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
					?>					<?php if( have_rows('gallery_items') ): ?>

						<ul class="gallSlides">

						<?php while( have_rows('gallery_items') ): the_row();

							// vars
							$image = get_sub_field('gallery_item');
							$link = get_sub_field('link');

							?>

							<li class="slide">

								<?php if( $link ): ?>
									<a href="<?php echo $link; ?>" rel="fancybox">
								<?php endif; ?>


							<?php $image = wp_get_attachment_image_src(get_field('gallery_item'), 'conf-imagesm'); ?>
							<img src="<?php echo $image[0]; ?>" alt="<?php echo get_the_title(get_field('gallery_item')) ?>" />

								<?php if( $link ): ?>
									</a>
								<?php endif; ?>


							</li>

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

				<div class="exclusive">
					<h3>Exclusive to<br>
						2020 Members</h3>

					<p>Places are free of charge and restricted to one place per partner*</p>

					<a href="<?php echo get_option('home'); ?>/2020-membership-in-practice/" class="gen-btn green icon trophy">Join Now</a>

				</div>
				<!--
				<a href="" class="gen-btn blue icon down-arrow sidebar-btn">Download 2015<br>Training Programme</a>

				<a href="" class="gen-btn blue icon down-arrow sidebar-btn">Download 2014<br>Training Programme</a>
				-->
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
															    <li><a href="<?php the_field('team_telephone'); ?>" target="_blank"><i class="icon-linkedin"></i> LinkedIn profile</a></li>
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

			</div>
		</div>
	</div>

<?php get_footer(); ?>
