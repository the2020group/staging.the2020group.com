<?php

/*
 * Template Name: Login Page
 */
?>
<?php

    if ( is_user_logged_in() ) { 
        wp_redirect('/dashboard/');exit;
    } 

?>
<?php get_header(); ?>
    
    <div class="row">
    
        <div class="small-12 large-6 large-offset-3 columns" role="main">
            
            <?php if (have_posts()) : the_post(); ?>
            
              <?php the_content();
          
              endif; ?>
    
        </div>
    
    </div>

<?php get_footer();
