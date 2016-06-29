<div id="main-sidebar" class="sidebar" role="complementary">

  <?php if(!has_term(97,'product_cat')) : ?>
      <?php if (!is_user_logged_in()) : ?>

        <?php if( (has_term(102, 'product_cat')) || (has_term(101, 'product_cat')) ) : ?>

        <?php else : ?>
          <div class="cta-block side-block text-block texturebg">
            <h4>Not yet a member?</h4>
            <!-- <?php if( (has_term(93, 'product_cat')) || (has_term(95, 'product_cat'))  || (has_term(100, 'product_cat'))  || (has_term(109, 'product_cat'))  || (has_term(30, 'product_cat'))  || (has_term(99, 'product_cat'))  || (has_term(94, 'product_cat'))  ) : ?>
              <p>Become a 2020 member to access some 2020 webinars as part of your subscription and others at discounted rates</p>
              <p>Join 1,000's of other progressive accountants and tax professionals worldwide who benefit from our innovative solutions.</p>
            <?php else : ?>
              <p>Join 1000's of other accountancy professionals. Benefit from our wealth of knowledge, tools, tips and downloads now.</p>
            <?php endif; ?> -->
            <p>Join 1000's of other accountancy professionals. Benefit from our wealth of knowledge, tools, tips and downloads now.</p>
            <a href="<?php echo get_option('home'); ?>/2020-membership-in-practice/" class="gen-btn green icon trophy">Join Now</a>
            <p class="already-member">Already a member? <a href="/login?ref=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="white-link">Login now</a></p>
          </div>

        <?php endif; ?>

      <?php endif; ?>

    <?php endif; ?>

  <?php if (is_product()) : ?>


  <?php endif; ?>

  <?php //Output speakers for webinars ?>
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
</div>



  <?php //END Output for speakers on webinars ?>
  <?php dynamic_sidebar( 'sidebar-products' ); ?>

	<?php
		/**
		 * woocommerce_after_single_product_summary hook
		 *
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_product_sidebar' );
	?>

<?php
  $terms = wp_get_post_terms( $post->ID, 'product_cat' ); foreach ( $terms as $term ) $categories[] = $term->term_id;
  if ( in_array( 92, $categories ) ) { ?>
    <div class="side-block text-block dbluebg">
        <h4>Money back guarantee</h4>
        <p>Our products come with a full 28 day money back guarantee if you are not completely satisfied with the product.</p>
    </div>

<?php  };

?>

  <?php if( get_field('product_download') ): ?>
  <a href="<?php the_field('product_download'); ?>" class="side-block download-btn" target="_blank">
		Download <?php the_field('product_download_name'); ?>
	</a>
	<?php endif; ?>

  <?php include_once('includes/block-testimonials.php'); ?>


<!--
  <div class="side-block text-block">
  	<h4>Top 10 ways to improve take up rates</h4>
  	<p>This top ten guide provides tips on how to improve fee protection take up rates</p>
  	<a href="" class="gen-btn blue arrow">Find out more</a>
  </div>
-->

<!--
  <a href="" class="side-block download-btn">
  	Download a pdf brochure <br>for 2020 Tax Protection
  </a>
-->

</div>