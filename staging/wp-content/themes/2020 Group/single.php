<?php get_header(); ?>
	
<div class="row">
	<div class="small-12 medium-8 columns" role="main">
		
		<?php while (have_posts()) : the_post(); ?>
		<?php 
					// check if content qualifies for CPD entry
				$post_type = get_post_type();
				
				// indicator if CPD log needs to be added
				$add_cpd_log = false;
				$cpd_log = array('type'=>'Article','title'=>get_the_title(),'ID'=>get_the_ID());
				echo get_the_ID();
				// only working for posts atm
				if ($post_type == 'post') {
					
					// check the CPD field for the post
					$cpd_entry = get_field('content_qualifies_for_cpd_log_entry');
					print_r($cpd_entry);
					// if post is set to yes add entry
					if ($cpd_entry == 'yes') {
						$add_cpd_log = true;
					}
					// if set to blank then check what category cpd field says
					elseif (!$cpd_entry || $cpd_entry == '') {
						// get current category
  						$cats = get_the_category(get_the_ID()); 

  						// just use the first category
						$cat = $cats[0];

						print_r($cat);

						// get the CPD field of the category
						$cpd_entry = get_field('content_qualifies_for_cpd_log_entry',$cat->taxonomy.'_'.$cat->term_id);

						// if it is set to yes add the log entry
						if ($cpd_entry == 'yes') {
							$add_cpd_log = true;
						}

					}

				}

				if ($add_cpd_log) {
					add_cpd_log($cpd_log);
				}

		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">
			<header class="article-header">
				<h1 itemprop="headline"><?php the_title(); ?></h1>
			</header>
			<section class="entry-content" itemprop="articleBody">
				<?php if ( has_post_thumbnail()) : ?>
						<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >
							<?php the_post_thumbnail(); ?>
						</a>
					<?php endif; ?>
					
					<?php the_content();
				
					endwhile; ?>
			</section>
		</article>
	</div>

	<div class="small-12 medium-4 columns">
		<?php get_sidebar(); ?>
	</div>
</div>

<?php get_footer();
