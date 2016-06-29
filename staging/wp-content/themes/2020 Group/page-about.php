<?php 
    
/*
 * Template Name: About
 */

get_header(); ?>
    
    <div class="row">
    
        <div class="small-12 medium-8 columns" role="main">
            
            <?php if (have_posts()) : the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">

							<header class="article-header about-header">
								
								<?php if ( function_exists('yoast_breadcrumb') ) {
											yoast_breadcrumb('<p class="about-breadcrumbs">','</p>');
								} ?>
            
                <h1><?php the_title(); ?></h1>
                <?php if ($new_cpd_log) : ?>
                    <p>New CPD log entry created</p>
                <?php endif; ?>
                    
              </header>
              
              <section class="entry-content" itemprop="articleBody">
	              
	             <?php if ( has_post_thumbnail()) : ?>
                  
                  <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
                  
                      <?php the_post_thumbnail(); ?>
                  
                  </a>
              
									<?php endif; ?>
              
									<?php the_content();
          
              endif; ?>
              
              </section>

			</article>
    
        </div>
    
        <div class="small-12 medium-4 columns">
	        
	          <?php get_sidebar(); ?>
            
        </div>

    </div>

<?php get_footer();
