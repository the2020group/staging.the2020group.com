<?php
/*
 * Template Name: Page Archive
 */

get_header(); ?>
    <div class="row">
        <div class="small-12 columns">
	        <div class="archive-wrap">
	        	<header class="article-head">
	        		<?php if ( function_exists('yoast_breadcrumb') ) {
							yoast_breadcrumb('<p class="archive-breadcrumbs">','</p>');
							} ?>
							<h1><?php the_title(); ?></h1>
	        	</header>
	        	<div class="row" data-equalizer>
		        	<?php
							$children = get_pages(array('child_of' => $post->ID, 'sort_order' => 'ASC', 'sort_column' => 'menu_order'));
							$counter = 1;
							foreach ($children as $child) { ?>
							<?php if (	($child->ID!=59) && ($child->ID!=263) 	) :?>
		        	<div class="small-12 medium-6 large-4 columns end">
			        	<div class="archive-block block-0<?php echo $counter; ?>" data-equalizer-watch>
								   <h3><?php echo $child->post_title; ?></h3>
								   <?php if ($child->post_excerpt):?>
								  	<p><?php echo $child->post_excerpt; ?></p>
								   <?php endif; ?>
									 <p><a href="<?php echo get_permalink($child->ID); ?>" class="gen-btn orange icon eye archive-view">View</a></p>
								</div>
		        	</div>
		        	<?php $counter++; ?>
		        	<?php endif; ?>
		        	<?php } ?>
	        	</div>
					</div>
        </div>
    </div>

<?php get_footer();