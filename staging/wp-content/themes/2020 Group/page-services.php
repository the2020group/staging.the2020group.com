<?php 

/*
 * Template Name: Services
 */

get_header(); ?>
    
    <div class="row">
    
        <div class="small-12 medium-8 columns" role="main">

            <?php  

                $args = array ('child_of'=>get_the_ID(),
                               'title_li'=> '',
                               'sort_column'=>'menu_order' );
                wp_list_pages( $args );

            ?>
        </div>
        
        <div class="small-12 medium-4 columns">
        
            <?php get_sidebar(); ?>
            
        </div>

    </div>

<?php get_footer();