<?php 
    
/*
 * Template Name: Team Innovation
 */
?> 

<?php get_header(); ?>
    
    <div class="row">
    
        <div class="small-12 columns" role="main">
            
            <div class="team-wrap">
            
            <?php  

                $args = array ('child_of'=>get_the_ID(),
                               'title_li'=> '',
                               'sort_column'=>'menu_order' );
                wp_list_pages( $args );

            ?>
            <?php if (have_posts()) : the_post(); ?>
            
                <header>
                    
                    <h2><?php the_title(); ?></h2>
                    <?php if ($new_cpd_log) : ?>
                        <p>New CPD log entry created</p>
                    <?php endif; ?>
                    
                </header>

                <?php if ( has_post_thumbnail()) : ?>
                    
                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
                    
                        <?php the_post_thumbnail(); ?>
                    
                    </a>
                
                <?php endif; ?>
                
                <?php the_content();
            
              endif; ?>
              
          <header class="team-header">
          	<h1><?php the_title(); ?>	<img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/team/innovation.png"  alt="" class="team-logo">	</h1>
					</header>
					
					<div class="team-content">
	          
	          <div>
						
							<?php 
	
							$image = get_field('team_image');
							
							if( !empty($image) ): 
							
								// vars
								$alt = $image['alt'];
								
								// thumbnail
								$size = 'teamimage';
								$thumb = $image['sizes'][ $size ];
								$width = $image['sizes'][ $size . '-width' ];
								$height = $image['sizes'][ $size . '-height' ];
							
							?>
							
							<img src="<?php echo $thumb; ?>" alt="<?php echo $alt; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" />
								
							<?php endif; ?>
						
						</div>
						
					</div>
    
        </div>
    
        </div>
        
        </div>

<?php get_footer();
