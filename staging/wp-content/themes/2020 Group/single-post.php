<?php get_header(); ?>

<div class="row">
  <div class="small-12 large-8 columns" role="main">

    <?php if (have_posts()) : the_post(); ?>
    <?php
      // Check category permissions to see if user is allowed to access this article
      $cats = get_the_category(get_the_ID());
      $cat_id = $cats[0]->term_taxonomy_id;

      // check if content qualifies for CPD entry
      $post_type = get_post_type();

      // indicator if CPD log needs to be added
      $add_cpd_log = false;
      $cpd_log = array('type'=>'Article','title'=>get_the_title(),'ID'=>get_the_ID());
      // only working for posts atm
      if ($post_type == 'post') {

          // check the CPD field for the post
          $cpd_entry = get_field('content_qualifies_for_cpd_log_entry');
          // if post is set to yes add entry
          if ($cpd_entry == 'yes') {
              $add_cpd_log = true;
          }
          // if set to blank then check what category cpd field says
          elseif (!$cpd_entry || $cpd_entry == '') {
              // get current category
              $cats = get_the_category(get_the_ID());
              // just use the first category
              $cat = $cats[0];
              // get the CPD field of the category
              $cpd_entry = get_field('content_qualifies_for_cpd_log_entry',$cat->taxonomy.'_'.$cat->term_id);
              // if it is set to yes add the log entry
              if ($cpd_entry == 'yes') {
                  $add_cpd_log = true;
              }
          }
      }
      // indicator to see if a new CPD entry has been created for notification
      $new_cpd_log = false;
      // if article qualifies for cpd entry
      if ($add_cpd_log) {
          // try adding a cpd entry and if a new one has been created
          if (add_cpd_log($cpd_log) > 0) {
              // set indicator to true
              $new_cpd_log = true;
          }
      }
    ?>

    <article class="page article-post">
      <header class="article-header">

	      <?php if ( function_exists('yoast_breadcrumb') ) {
				yoast_breadcrumb('<p class="article-breadcrumbs">','</p>');
				} ?>

        <h1><?php the_title(); ?></h1>
        <p class="date"><span>Date Added |</span> <?php the_time('F j, Y'); ?></p>

        <?php include('includes/article-sharing.php'); ?>

				<script>
				window.twttr=(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],t=window.twttr||{};if(d.getElementById(id))return;js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);t._e=[];t.ready=function(f){t._e.push(f);};return t;}(document,"script","twitter-wjs"));
				</script>

        <?php if ($new_cpd_log) : ?>
            <p>New CPD log entry created</p>
        <?php endif; ?>

      </header>

      <?php


        // scenarios - logged in not logged in
        // post has groups /  no groups

        $access = hasUserAccessTo(get_post_meta($post->ID,'groups-groups_read_post'));

        $hasgroups = get_post_meta($post->ID,'groups-groups_read_post');


        if ( !is_user_logged_in() ) :

          if (!is_empty($hasgroups)) :

            ?>

            <section class="entry-content" itemprop="articleBody">

              <?php if ( has_post_thumbnail()) : ?>
                  <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
                      <?php the_post_thumbnail(); ?>
                  </a>
              <?php endif; ?>

              <?php the_excerpt(); ?>

            </section>

          <div class="cta-block side-block text-block texturebg">
            <h2>Not yet a member?</h2>
            <h4>Become a member to download this content</h4>
            <p>Join 1000's of other accountancy professionals. Benefit from our wealth of knowledge, tools, tips and downloads now.</p>
            <a href="<?php echo get_option('home'); ?>/2020-membership-in-practice/" class="gen-btn orange icon trophy">Join Now</a> Already a member? <a href="/login?ref=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">Login now</a>
          </div>

            <?php

            else :

            ?>

            <section class="entry-content" itemprop="articleBody">

            <?php if ( has_post_thumbnail()) : ?>
                <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
                    <?php the_post_thumbnail(); ?>
                </a>
            <?php endif; ?>

            <?php the_content(); ?>

          </section>

          <?php include('includes/article-sharing.php'); ?>

          <?php

          endif;

      else :
      // logged in user ...

        if (!$access) :

          if (in_array('registered', $hasgroups)) :

            // doesn't have correct privlages but the article belongs to registered (and the user is logged in)

            ?>

               <section class="entry-content" itemprop="articleBody">

                <?php if ( has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
                        <?php the_post_thumbnail(); ?>
                    </a>
                <?php endif; ?>

                <?php the_content(); ?>

              </section>

              <?php include('includes/article-sharing.php'); ?>


            <?php

          else :

      ?>


            <section class="entry-content" itemprop="articleBody">

              <?php if ( has_post_thumbnail()) : ?>
                  <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
                      <?php the_post_thumbnail(); ?>
                  </a>
              <?php endif; ?>

              <?php the_excerpt(); ?>

            </section>



          <div class="cta-block side-block text-block texturebg">
            <p>This article is restricted based on your membership type.  Please contact 2020 for further details.</p>
            <a href="<?php echo get_option('home'); ?>/2020-membership-in-practice/" class="gen-btn orange icon trophy">View Membership Options</a>
          </div>



        <?php

          endif;

        else :

        ?>

      <section class="entry-content" itemprop="articleBody">

        <?php if ( has_post_thumbnail()) : ?>
            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
                <?php the_post_thumbnail(); ?>
            </a>
        <?php endif; ?>

        <?php the_content(); ?>

      </section>

      <?php include('includes/article-sharing.php'); ?>

    <?php endif; ?>
    <?php endif; ?>
    </article>

  <?php endif; ?>

  </div>
  <div class="small-12 large-4 columns">
      <ul class="article-sidebar">

						<?php $args = array(
							'child_of'     => 200,
							'date_format'  => get_option('date_format'),
							'depth'        => 0,
							'echo'         => 1,
							'sort_column'  => 'menu_order, post_title',
						        'sort_order'   => '',
							'title_li'     => '<h2>'.__('Article Categories').'</h2>',
							'walker'       => ''
						); ?>
					   <?php wp_list_pages( $args ); ?>

			</ul>

	    <?php  /* get_sidebar(); */  ?>
  </div>

</div>

<?php get_footer();
