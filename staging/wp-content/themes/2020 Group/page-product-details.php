<?php 
    
/*
 * Template Name: Product Details
 */
?> 

<?php get_header(); ?>
    
    <div class="row">
    
        <div class="small-12 medium-8 columns" role="main">
            
            <?php while (have_posts()) : the_post(); ?>
            
                <header>
                    
                    <h2><?php the_title(); ?></h2>
                
                </header>

                    <?php if ( has_post_thumbnail()) : ?>
                        
                        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
                        
                            <?php the_post_thumbnail(); ?>
                        
                        </a>
                    
                    <?php endif; ?>
                    
                    <?php the_content();
                
                    endwhile; ?>
    
        </div>
    
        <div class="small-12 medium-4 columns">
        
            <?php get_sidebar(); ?>
            
        </div>

    </div>

<?php get_footer();
