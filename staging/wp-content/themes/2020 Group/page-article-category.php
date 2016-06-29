<?php 

/*
 * Template Name: Article Category
 */

get_header(); ?>
    
    
    
    <div class="row">
    
        <div class="small-12 large-8 columns">
	        
	        <div class="article-list">
		        
		        <header class="article-head">
	        
		        <?php if ( function_exists('yoast_breadcrumb') ) {
									yoast_breadcrumb('<p class="article-breadcrumbs">','</p>');
						} ?>
									
						<h1><?php the_title(); ?></h1>
							
		        </header>
		        
		        <?php $content_category = get_field('content_category'); ?>
		        <?php 
	
	
	            /*
							$current_category = get_category($content_category->term_id);
	            
	
	            $capabilities = get_group_capabilities_for_user(); 
	
	            $capabilities = array_filter($capabilities);
	
	            $cat_caps = explode(',', get_field('capabilities',$current_category->taxonomy.'_'.$current_category->term_id));
	
	            $cat_caps = array_filter($cat_caps);
	            $access = false;
	
	            foreach ($capabilities as $user_cap) {
	
	                if (in_array($user_cap, $cat_caps)) {
	                    $access = true;
	                    break;
	                }
	
	            }
	
	            if (!$access) {
	                wp_redirect('/no-access-rights');exit;
	            }
	          */
	
	        	?>
	        	
            <?php $content_category = get_field('content_category'); ?>
            <?php query_posts( array ( 'category_name' => $content_category->slug, 'posts_per_page' => -1 ) ); ?>
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            
            <article id="post-<?php the_ID(); ?>" <?php post_class('article-list-item'); ?> role="article">
	            
	            	<header class="article-header">
                    
                  <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                  
                  <p class="date"><span>Date Added |</span> <?php the_time('F j, Y'); ?></p>
              
              	</header>
	            
		            <section class="entry-content">
			            
			            <?php if ( has_post_thumbnail()) : ?>
                      
                    <div class="archive-thumbnail-wrap">
                      <div class="archive-thumbnail">
                        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="article-archive-thumbnail">
                          <?php the_post_thumbnail('thumbnail'); ?>
                      	</a>
                      </div>
                      <div class="archive-text">
                        <?php the_excerpt(); ?>
                      </div>
                    </div>
                        
                    <?php else : ?>
                    <?php the_excerpt(); ?>
                    
                    <?php endif; ?>
                
                </section>

            </article>
            
            <?php endwhile; ?>
            
            <?php else : ?>
            
            <article class="post-not-found page">
                
                <header class="article-header">
                    
                    <h1><?php _e( 'Nothing Found!' ); ?></h1>
                
                </header>
                
                <section class="entry-content">
                    
                    <p><?php _e( 'Please check what you are looking for' ); ?></p>
                
                </section>
            
            </article>
            
            <?php endif; ?>
            <?php wp_reset_query(); ?>
            
		      </div>
        
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
	        
	        <?php /* get_sidebar(); */ ?>
	        
        </div>
        
    </div>

<?php get_footer();