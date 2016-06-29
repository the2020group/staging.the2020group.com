<?php 

/*
 * Template Name: Dashboard - My Recommendations
 */

get_header(); ?>

		<div class="dash-wrap">
    
    <div class="row collapse">
    
        <div class="small-1 medium-1 columns" role="main" style="background: #000; color: #fff">
            <?php get_sidebar('dashboard'); ?>
        </div>
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <div class="small-11 medium-11 columns" role="main">
	        
	        	<div id="dash-main">
            
            <h2><?php the_title(); ?></h2>

            <?php 
                // get all products the user has bought
                $user_id = get_current_user_id();
                $products = get_all_product_ids_ordered_by_user($user_id,'completed');
            
                //echo $user_id;
                //print_r($products);

                 $q2 = new WP_query(array('post__not_in' => $products, 'order' => 'RAND', 'post_type' => 'product', 'showposts' => 6 ));

                 $q3 = new WP_query(array(
                    'post__not_in' => $products, 
                    'post_type' => array( 'product'),
                    'order' => 'ASC', 
                    'orderby' => 'date',
                    'date_query' => array(
                        array(
                            'after'     => getdate(),
                            'inclusive' => true,
                        )
                    )
                 ));

                 $q2 = $q3;

                if($q2->have_posts()) :
                    while($q2->have_posts()) : $q2->the_post();
                ?>      
                    <div class="dash-block">
                        
                        <div class="block-copy">
                            
                            <div class="copy-content">
                                <h4><?php the_title(); ?></h4>
                                <p><?php print_excerpt(350); ?></p>
                                <a href="<?php the_permalink(); ?>" class="gen-btn orange">View</a>
                            </div>
                                
                        </div>
                        
                    </div>
            <?php       
                    endwhile;
                endif;
            ?>

            
	        	</div>
    
        </div>
        <?php endwhile; ?>
        <?php endif; ?>
    </div>
    
		</div>

<?php get_footer();