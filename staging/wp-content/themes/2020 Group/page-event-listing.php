<?php 

/*
 * Template Name: Event Listing
 */

get_header(); 
global $post;

?>
    <div class="row">
        <div class="small-12 columns" role="main">

            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                
                <section class="entry-content">
                  
                  <header class="article-header">
                      <h1><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
                  </header>
                    
                  <?php if ( has_post_thumbnail()) : ?>
                      <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
                          <?php the_post_thumbnail(); ?>
                      </a>
                  <?php endif; ?>
                    
                  <?php the_content(); ?>
                  
                  <ul class="products small-block-grid-1 medium-block-grid-2 large-block-grid-4" data-equalizer>

                  <?php
                      $products = get_posts(array( 'post_type' => 'product', 'showposts' => 10,'product_cat'=>$post->post_name )); 
                      // query_posts( array( 'post_type' => 'product', 'showposts' => 10,'product_cat'=>$post->post_name ) );
                      // if ( have_posts() ) : while ( have_posts() ) : the_post(); 
                      foreach($products as $product) :
                      
                      $productLink = get_permalink( $product->ID );
                      $productTitle = $product->post_title;

                  ?>
                    <li class="product">
                      <div class="panel-block">
                        <div class="inner" data-equalizer-watch>
                          <h3><a href="<?php echo $productLink; ?>"><?php echo $productTitle; ?></a></h3>
                          
                          <a href="<?php echo $productLink; ?>" class="gen-btn btn-color orange">View</a>
                        </div>

                        <?php print_r($product); ?>
                        
                      </div>
                    </li>

                  <?php // endwhile; endif; wp_reset_query();
                    endforeach;
                  ?>
                  
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