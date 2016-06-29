<?php

/*
 * Template Name: Team
 */

get_header(); ?>

    <div class="row">

        <div class="small-12 columns" role="main">

          <div class="team-wrap">

          <header class="team-header about-header">
	          <?php if ( function_exists('yoast_breadcrumb') ) {
											yoast_breadcrumb('<p class="about-breadcrumbs team-breadcrumbs">','</p>');
						} ?>

						<div class="row">
          		<div class="small-6 columns">
          			<h1><?php the_title(); ?></h1>
          		</div>
          		<div class="small-6 columns team-logo">

          			<img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/team/innovation.png"  alt="" class="inno-logo">
          			<!--
	          		<img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/team/practice-exchange.png"  alt="" class="prac-logo">
          			<img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/team/tax-protection.png"  alt="" class="prot-logo">-->

          		</div>
						</div>
          </header>

          <div class="tab-set team-content">
	          <dl id="teamTabs" class="tabs" data-tab data-options="deep_linking:false; scroll_to_content:false">
						  <dd class="active"><a href="#innovation" id="inno-btn">2020 Innovation</a></dd>
						  <dd><a href="#practice-exchange" id="prac-btn">2020 Practice Exchange</a></dd>
						  <dd><a href="#tax-protection" id="prot-btn">2020 Tax Protection</a></dd>
						</dl>

						<div id="teamTabContent" class="tabs-content">
					  	<div class="content active" id="innovation">
						    <div class="row" data-equalizer>
							    <?php

										the_post();

										// Get 'team' posts
										$team_posts = get_posts( array(
											'post_type' => 'team',
											'posts_per_page' => -1,
											'order'			=> 'ASC',
											'orderby'		=> 'menu_order',
											'tax_query' => array(
									        array(
									        'taxonomy' => 'division',
									        'field' => 'term_id',
									        'terms' => 81)
									    )
										));

										if ( $team_posts ):
									?>

							    <?php
								    	foreach ( $team_posts as $post ):
											setup_postdata($post);

											$thumb_src = null;
											if ( has_post_thumbnail($post->ID) ) {
												$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'teamimage' );
												$thumb_src = $src[0];
											}
									?>

							    <div class="small-12 medium-4 columns end">
								    <div class="member-block" data-equalizer-watch>
									    <?php if ( $thumb_src ): ?>
												<a href="#innovation-profile-<?php the_ID(); ?>" class="fancybox-inline">
													<img src="<?php echo $thumb_src; ?>" alt="<?php the_title(); ?>, <?php the_field('team_title'); ?>" class="">
												</a>
											<?php endif; ?>

									    <div class="team-detail-wrap">
										    <h3><?php the_title(); ?></h3>
											  <h4><?php the_field('team_title'); ?></h4>

										    <ul class="iconListing">
											    <li><i class="icon-telephone icon"></i> <?php the_field('team_telephone'); ?></li>
											    <li><a href="mailto:<?php echo antispambot( get_field('team_email') ); ?>"><i class="icon-letter icon"></i> Email <?php the_title(); ?></a></li>
											    <?php if ( $profile = get_field('team_profile') ): ?>
											    <li><a href="#innovation-profile-<?php the_ID(); ?>" class="fancybox-inline"><i class="icon-eye icon"></i> View Profile</a></li>
											    <?php endif; ?>
											    <?php if ( $linkedin = get_field('team_linkedin') ): ?>
											    <li><a href="<?php echo $linkedin; ?>" target="_blank"><i class="icon-linkedin icon"></i> LinkedIn profile</a></li>
											    <?php endif; ?>
										    </ul>
									    </div>
							    	</div>
							    	<div id="innovation-profile-<?php the_ID(); ?>" style="display: none; ">
								    	<div class="member-block-pop">
										    <div class="row collapse">
											    <div class="small-12 medium-6 columns">

											    <?php if ( $thumb_src ): ?>
														<img src="<?php echo $thumb_src; ?>" alt="<?php the_title(); ?>, <?php the_field('team_title'); ?>" class="profile-image">
													<?php endif; ?>

										    </div>
												  <div class="small-12 medium-6 columns">

											    <div class="team-pop-wrap">
												    <h3><?php the_title(); ?></h3>
													  <h4><?php the_field('team_title'); ?></h4>

												    <ul class="iconListing">
													    <li><i class="icon-telephone icon"></i> <?php the_field('team_telephone'); ?></li>
													    <li><a href="mailto:<?php echo antispambot( get_field('team_email') ); ?>"><i class="icon-letter icon"></i> Email <?php the_title(); ?></a></li>
													    <?php if ( $linkedin = get_field('team_linkedin') ): ?>
													    <li><a href="<?php echo $linkedin; ?>" target="_blank"><i class="icon-linkedin icon"></i> LinkedIn profile</a></li>
													    <?php endif; ?>
												    </ul>

												    <?php the_content(); ?>

											    </div>
									    	</div>
								    		</div>
								    	</div>
								    </div>
							    </div>

							    <?php endforeach; ?>

									<?php endif;

										wp_reset_query();

									?>

						    </div>
						  </div>
						  <div class="content" id="practice-exchange">
						    <div class="row">

							    <?php

										the_post();

										$team_posts = get_posts( array(
											'post_type' => 'team',
											'posts_per_page' => -1, // Unlimited posts
											'order'			=> 'ASC',
											'tax_query' => array(
									        array(
									        'taxonomy' => 'division',
									        'field' => 'term_id',
									        'terms' => 82)
									    )

										));

										if ( $team_posts ):
									?>

							    <?php
								    	foreach ( $team_posts as $post ):
											setup_postdata($post);

											$thumb_src = null;
											if ( has_post_thumbnail($post->ID) ) {
												$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'teamimage' );
												$thumb_src = $src[0];
											}
									?>

							    <div class="small-6 medium-4 columns end">
								    <div class="member-block">

									    <?php if ( $thumb_src ): ?>
												<a href="#practice-exchange-profile-<?php the_ID(); ?>" class="fancybox-inline">
													<img src="<?php echo $thumb_src; ?>" alt="<?php the_title(); ?>, <?php the_field('team_title'); ?>" class="">
												</a>
											<?php endif; ?>

									    <div class="team-detail-wrap">
										    <h3><?php the_title(); ?></h3>
											  <h4><?php the_field('team_title'); ?></h4>

										    <ul class="iconListing">
											    <li><i class="icon-telephone icon"></i> <?php the_field('team_telephone'); ?></li>
											    <li><a href="mailto:<?php echo antispambot( get_field('team_email') ); ?>"><i class="icon-letter icon"></i> Email <?php the_title(); ?></a></li>
											    <?php if ( $profile = get_field('team_profile') ): ?>
											    <li><a href="#practice-exchange-profile-<?php the_ID(); ?>" class="fancybox-inline"><i class="icon-eye icon"></i> View Profile</a></li>
											    <?php endif; ?>
											    <?php if ( $linkedin = get_field('team_linkedin') ): ?>
											    <li><a href="<?php echo $linkedin; ?>" target="_blank"><i class="icon-linkedin icon"></i> LinkedIn profile</a></li>
											    <?php endif; ?>
										    </ul>
									    </div>
							    	</div>

										<div id="practice-exchange-profile-<?php the_ID(); ?>" style="display: none; ">
								    	<div class="member-block-pop">
										    <div class="row collapse">
											    <div class="small-12 medium-6 columns">

											    <?php if ( $thumb_src ): ?>
														<img src="<?php echo $thumb_src; ?>" alt="<?php the_title(); ?>, <?php the_field('team_title'); ?>" class="profile-image">
													<?php endif; ?>

										    </div>
												  <div class="small-12 medium-6 columns">

											    <div class="team-pop-wrap">
												    <h3><?php the_title(); ?></h3>
													  <h4><?php the_field('team_title'); ?></h4>

												    <ul class="iconListing">
													    <li><i class="icon-telephone icon"></i> <?php the_field('team_telephone'); ?></li>
													    <li><a href="mailto:<?php echo antispambot( get_field('team_email') ); ?>"><i class="icon-letter icon"></i> Email <?php the_title(); ?></a></li>
													    <?php if ( $linkedin = get_field('team_linkedin') ): ?>
													    <li><a href="<?php echo $linkedin; ?>" target="_blank"><i class="icon-linkedin icon"></i> LinkedIn profile</a></li>
													    <?php endif; ?>
												    </ul>

												    <?php the_content(); ?>

											    </div>
									    	</div>
								    		</div>
								    	</div>
								    </div>
							    </div>

									<?php endforeach; ?>

									<?php endif;

										wp_reset_query();

									?>

						    </div>
						  </div>
						  <div class="content" id="tax-protection">
						    <div class="row">

							    <?php

										the_post();

										$team_posts = get_posts( array(
											'post_type' => 'team',
											'posts_per_page' => -1,
											'order'			=> 'ASC',
											'tax_query' => array(
									        array(
									        'taxonomy' => 'division',
									        'field' => 'term_id',
									        'terms' => 83)
									    )

										));

										if ( $team_posts ):
									?>

							    <?php

											foreach ( $team_posts as $post ):
											setup_postdata($post);

											$thumb_src = null;
											if ( has_post_thumbnail($post->ID) ) {
												$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'teamimage' );
												$thumb_src = $src[0];
											}
									?>

							    <div class="small-6 medium-4 columns end">
								    <div class="member-block">
									    <?php if ( $thumb_src ): ?>
												<a href="#tax-protection-profile-<?php the_ID(); ?>" class="fancybox-inline">
													<img src="<?php echo $thumb_src; ?>" alt="<?php the_title(); ?>, <?php the_field('team_title'); ?>" class="">
												</a>
											<?php endif; ?>

									    <div class="team-detail-wrap">
										    <h3><?php the_title(); ?></h3>
											  <h4><?php the_field('team_title'); ?></h4>

										    <ul class="iconListing">
											    <li><i class="icon-telephone icon"></i> <?php the_field('team_telephone'); ?></li>
											    <li><a href="mailto:<?php echo antispambot( get_field('team_email') ); ?>"><i class="icon-letter icon"></i> Email <?php the_title(); ?></a></li>
											    <?php if ( $profile = get_field('team_profile') ): ?>
											    <li><a href="#tax-protection-profile-<?php the_ID(); ?>" class="fancybox-inline"><i class="icon-eye icon"></i> View Profile</a></li>
											    <?php endif; ?>
											    <?php if ( $profile = get_field('team_linkedin') ): ?>
											    <li><a href="<?php echo $linkedin; ?>" target="_blank"><i class="icon-linkedin icon"></i> LinkedIn profile</a></li>
											    <?php endif; ?>
										    </ul>
									    </div>
							    	</div>
							    	<div id="tax-protection-profile-<?php the_ID(); ?>" style="display: none; ">
								    	<div class="member-block-pop">
										    <div class="row collapse">
											    <div class="small-12 medium-6 columns">

											    <?php if ( $thumb_src ): ?>
														<img src="<?php echo $thumb_src; ?>" alt="<?php the_title(); ?>, <?php the_field('team_title'); ?>" class="profile-image">
													<?php endif; ?>

										    </div>
												  <div class="small-12 medium-6 columns">

											    <div class="team-pop-wrap">
												    <h3><?php the_title(); ?></h3>
													  <h4><?php the_field('team_title'); ?></h4>

												    <ul class="iconListing">
													    <li><i class="icon-telephone icon"></i> <?php the_field('team_telephone'); ?></li>
													    <li><a href="mailto:<?php echo antispambot( get_field('team_email') ); ?>"><i class="icon-letter icon"></i> Email <?php the_title(); ?></a></li>
													    <?php if ( $profile = get_field('team_linkedin') ): ?>
													    <li><a href="<?php echo $linkedin; ?>" target="_blank"><i class="icon-linkedin icon"></i> LinkedIn profile</a></li>
													    <?php endif; ?>
												    </ul>

												    <?php the_content(); ?>

											    </div>
									    	</div>
								    		</div>
								    	</div>
								    </div>
							    </div>

									<?php endforeach; ?>

									<?php endif;

										wp_reset_query();

									?>

						    </div>
						  </div>
						</div>

          </div>

        </div>

        </div>

        </div>

<?php get_footer();
