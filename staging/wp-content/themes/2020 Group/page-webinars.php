<?php

/*
 * Template Name: Webinar Listings
 */

get_header(); 
global $post;

?>
    <div class="row">
        <div class="small-12 columns" role="main">

            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                <section class="entry-content">

                  <div class="product-filter">

                    <div class="row">
                      <div class="small-12 medium-6 columns">
                        <p>Filter Results</p>
                      </div>
                      <div class="small-12 medium-6 columns text-right">
                        <p>Filter Results</p>
                      </div>
                    </div>

                    <form>
                      <div class="row">
                        <div class="small-12 medium-4 columns">
                          <select>
                            <option value="date">By Date</option>
                            <option value="mostpopular">Most Popular</option>
                          </select>
                        </div>
                        <div class="small-12 medium-4 columns">
                          <select>
                            <option>Type</option>
                            <option value="type">Some other Type</option>
                          </select>
                        </div>
                        <div class="small-12 medium-4 columns">
                          <select>
                            <option>Topic</option>
                            <option value="someothertopic">Some other Topic</option>
                          </select>
                        </div>
                      </div>
                    </form>

                  </div>

                  <header class="article-header">
                    <h2><?php the_title(); ?></h2>
                  </header>

                  <?php the_content(); ?>

                  <ul class="products product-list small-block-grid-1 medium-block-grid-2 large-block-grid-4" data-equalizer>

                  <?php
                    // Query the product loop to get related webinars
                    $args = array( 'post_type' => 'product', 'showposts' => 10, 'product_cat'=>$post->post_name );
                    $products = get_posts( $args );
                    foreach($products as $product) :

                      // Set the content vars of the returned matching products
                      $productLink = get_permalink( $product->ID );
                      $productTitle = $product->post_title;
                      $eventDate = get_field('date', $product->ID);
                      $eventStartTime = get_field('start_time', $product->ID);
                      $eventEndTime = get_field('end_time', $product->ID);
                      $productExcerpt = $product->post_excerpt;
                  ?>

                    <li class="product">
                      <div class="panel-block">
                        <div class="inner" data-equalizer-watch>
                          <h3><a href="<?php echo $productLink; ?>"><?php echo $productTitle; ?></a></h3>

                          <?php if ($eventStartTime) : ?>
                            <p class="event-date"><span>Date:</span> <?php echo $eventDate; ?></p>
                          <?php endif; ?>

                          <?php if ($eventStartTime) : ?>
                            <p class="event-time"><?php echo $eventStartTime; ?> 
                              <?php if ($eventEndTime) : ?>
                              - <?php echo $eventEndTime; ?>
                              <?php endif; ?>
                            </p>
                          <?php endif; ?>

                          <div class="excerpt">
                            <p><?php echo $productExcerpt; ?></p>
                          </div>

                          <a href="<?php echo $productLink; ?>" class="gen-btn btn-color orange arrow">View</a>
                        </div>

                        <?php // print_r($product); ?>

                      </div>
                    </li>

                  <?php endforeach; ?>

                  </ul>

                </section>


            <?php endwhile; ?>

            <?php else : ?>

            <article class="post-not-found">

                <header class="not-found-header">
                    <h1><?php _e( 'Nothing Found!' ); ?></h1>
                </header>

                <section class="not-found-content">
                    <p><?php _e( 'Please check what you are looking for' ); ?></p>
                </section>

            </article>

            <?php endif; ?>

            <div class="below-nav">
                <?php posts_nav_link(' - ', '&laquo; Prev', 'Next &raquo;'); ?>
            </div>

        </div>

    </div>

<?php get_footer();