<?php 

/*
 * Template Name: Dashboard old
 */

get_header(); ?>
    
    <div class="row">
    
        <div class="small-12 medium-8 columns" role="main">

            <?php

                global $current_user;

                global $_subscription_details, $_subscription_product;

                get_currentuserinfo();

                
                echo '<h3>Personal Details</h3>';
                echo '<p>Name: '.$current_user->display_name.'</p>';
                echo '<p>Email Address: '.$current_user->user_email.'</p>';
                echo '<p>Password: **********</p>';
                echo '<p>Account type: '.$_subscription_product->post->post_title.'</p>';
                
                //echo '<h3>Personal Details</h3>';
                //echo '<p>Subscribed as: '.$_subscription_product->post->post_title.'</p>';
                //echo '<p>Subscription ends: '.$_subscription_details[key($_subscription_details)]['expiry_date'].'</p>';
                //echo '<p>Subscription status: '.$_subscription_details[key($_subscription_details)]['status'].'</p>';
            ?>


            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">
                
                <header class="article-header">
                    
                    <h1>
                        
                        <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
                    
                    </h1>
                
                </header>
                
                <section class="entry-content">
                    
                    <?php if ( has_post_thumbnail()) : ?>
                        
                        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
                        
                            <?php the_post_thumbnail(); ?>
                        
                        </a>
                    
                    <?php endif; ?>
                    
                    <?php the_excerpt(); ?>
                
                </section>
                
                <footer class="article-footer">
                    
                    <p>This is the article footer content</p>
                
                </footer>

            </article>
            
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
        
        <div class="small-12 medium-4 columns">
        
            <?php get_sidebar(); ?>
            
        </div>

    </div>

<?php get_footer();