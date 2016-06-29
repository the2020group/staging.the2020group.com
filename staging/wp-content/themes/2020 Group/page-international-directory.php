<?php

/*
 * Template Name: International Directory
 */

get_header(); ?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>


<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'home-hero' );?>
<div id="country-scroll" data-stellar-background-ratio="0.05" style="background-image: url('<?php echo $thumb['0'];?>'); background-position: 0% 14px;">
  <?php the_field('international_welcome'); ?>
</div>

<a id="int-members-list">
<div class="row">
	<div class="small-12 columns" role="main">
		<header class="members-header">
      <h1><span><?php the_title(); ?></span></h1>
    </header>

		<?php

			$url = $_SERVER["REQUEST_URI"] ;
			$url = substr($url,1);
			if (substr($url,-1)=='/') {

				$url = substr($url,0,-1);

			}
			$parts = explode('/',$url);

		?>
    <div class="member-firms">
	    <p class="int-breadcrumbs">
				<span prefix="v: http://rdf.data-vocabulary.org/#">
					<span typeof="v:Breadcrumb"><a href="http://the2020group.local" rel="v:url" property="v:title">Home</a></span> »
					<span typeof="v:Breadcrumb"><?php
						if (!isset($parts[1])): ?>
	 						<span class="breadcrumb_last" property="v:title">International Members Directory</span>
	 						<?php else: ?>
		 						<a href="/international-members-directory/" rel="v:url" property="v:title">International Members Directory</a>
		 					<?php endif;
						?></span>

						<?php

						if (isset($parts[1])) :
							$tag = get_term_by('slug', $parts[1], 'location');
							?> »
							<?php if (!isset($parts[2])) : ?>
								<span typeof="v:Breadcrumb"><span class="breadcrumb_last" property="v:title"><?php echo $tag->name;?></span></span>
							<?php else: ?>
								<a href="/international-members-directory/<?php echo $tag->slug;?>/" rel="v:url" property="v:title"><?php echo $tag->name;?></a>
							<?php endif; ?>

						<?php
							endif;
						?>

						<?php

						if (isset($parts[2])) :
							$tag = get_term_by('slug', $parts[2], 'location');
							?>
					» <span typeof="v:Breadcrumb"><span class="breadcrumb_last" property="v:title"><?php echo $tag->name;?></span></span>
					<?php
						endif;
					?>
				</span>
			</p>
			<?php
		  	/* if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p class="int-breadcrumbs">','</p>');
			} */
			?>
	    <div class="row">
	      <div class="small-12 large-3 columns">
		      <div class="continent-list">
		        <h3>Continents</h3>
		        <?php

			        $args = array('parent'=>0,'orderby'=>'name');
							$continents = get_terms( 'location', $args );

			        echo '<ul class="continent-btns">';

			        foreach ($continents as $continent){
								echo '<li';
								if ($continent->slug == $parts[1]) {
									echo ' class="selected" ';

								}
								echo '><a href="/international-members-directory/'.$continent->slug.'/#int-members-list" >'.$continent->name.'</a></li>';
			        }
			        echo '</ul>';

			        /*
    		  		$args = array(

						  'taxonomy' 				=> 'location',
						  'hide_empty' 			=> 0,
						  'depth' 					=>1,
						  'orderby'     		=> 'name',
						  'title_li'    		=> __( '' ),
						);
						?>
						<ul class="continent-btns">
						<?php
						wp_list_categories($args);
						?>
						</ul>
						*/
						?>
		      </div>
	      </div>

	      <?php if (!isset($parts[1])) : ?>

	      <div class="small-12 medium-9 columns">
						<div class="member-copy">
		  				<?php the_content(); ?>
	  				</div>
	  		</div>

	  		<?php endif; ?>

	      <?php if (isset($parts[1])) : ?>

				<div class="small-12 large-3 columns end">
					<div class="continent-list countries">
		        <h3>Countries</h3>
		        <?php

			        //print_r($parts);

			      $tag = get_term_by('slug', $parts[1], 'location');

			      $taxonomy_name = 'location';
						$termchildren = get_term_children( $tag->term_id, $taxonomy_name );
												

						foreach ( $termchildren as $child ) {
							$term = get_term_by( 'id', $child, $taxonomy_name );
							$newtermchildren[$child] =$term->slug;
						}
						asort($newtermchildren);

						echo '<ul class="continent-btns">';
						
						foreach ( $newtermchildren as $child => $value) {
							//print_r($child);
							$term = get_term_by( 'id', $child, $taxonomy_name );
							//print_r($term);
							echo '<li';
								if ($term->slug == $parts[2]) {
									echo ' class="selected" ';

								}
								echo '><a href="/international-members-directory/'.$parts[1].'/'.$term->slug.'/#int-members-list">' . $term->name . '</a></li>';
						
						}
						echo '</ul>';

			      /*

						 $current_term = get_term_by( 'slug', $url[1], get_query_var( 'taxonomy' ) );
						 $args = array(
						    'child_of' => $current_term->term_id,
						    'taxonomy' => $current_term->taxonomy,
								'hide_empty' => 0,
								'hierarchical' => true,
								'depth'  => 1,
								'title_li' => ''
						    );
						?>
						<ul class="continent-btns">
						<?php
						wp_list_categories($args);
						?>
						</ul>

						*/
						?>
		      </div>
	  		</div>
				<?php if (isset($parts[2])) : ?>
	  		<div class="small-12 large-6 columns">

		  		<?php

			  		$country = get_term_by('slug', $parts[2], 'location');

			  		$args = array (
			  			'post_type'=>'directory',
			  			'posts_per_page' => '-1',
			  			'orderby' => 'title',
			  			'order' => 'asc',
						'tax_query' => array(
								array(
									'taxonomy' => 'location',
									'field'    => 'slug',
									'terms'    => $parts[2],
								),
							)
							);

			  		$listing_query = new WP_Query( $args );

		  		?>
					<div class="country-detail">
						<h3><?php echo $country->name;?></h3>

						<?php

						if ( $listing_query->have_posts() ) :

							while ( $listing_query->have_posts() ) :
								$listing_query->the_post();
						?>
						<div class="country-info">



							<?php

							$image = get_field('company_logo');

							if( !empty($image) ):

								// vars
								$title = $image['title'];
								$alt = $image['alt'];

								// thumbnail
								$size = 'exhibitor-logo';
								$thumb = $image['sizes'][ $size ];
								$width = $image['sizes'][ $size . '-width' ];
								$height = $image['sizes'][ $size . '-height' ];

							?>

							<img src="<?php echo $thumb; ?>" alt="<?php echo $alt; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" />
							<?php endif; ?>
							<h4><?php the_title(); ?></h4>
							<div class="row">
								<div class="small-12 large-6 columns">
									<ul class="address">
                                        <li><?php if(the_field('address')) : ?><?php the_field('address'); ?><?php endif; ?></li>
                                        <li><?php if(the_field('postcode')) : ?><li><?php the_field('postcode'); ?><?php endif; ?></li>
                                        <li><?php if(the_field('country')) : ?><li><?php the_field('country'); ?><?php endif; ?></li>
									</ul>

									<ul class="contacts">
										<li>Contact: <?php the_field('contact_name'); ?></li>
                                        <?php if(get_field('email')) : ?><li class="linkwrap"><a href="mailto:<?php the_field('email'); ?>"><?php the_field('email'); ?></a></li><?php endif; ?>
                                        <?php if(get_field('telephone')) : ?><li>T. <?php the_field('telephone'); ?></li><?php endif; ?>
                                        <?php if(get_field('web_link')) : ?><li class="linkwrap"><a href="http://<?php the_field('web_link'); ?>" target="_blank"><?php the_field('web_link'); ?></a></li><?php endif; ?>
                                        <?php if(get_field('fax')): ?><li>F. <?php the_field('fax'); ?></li><?php endif; ?>
                                    </ul>

								</div>
								<div class="small-12 large-6 columns">
									<?php if(get_field('specialisms')) : ?>
										<p><strong>Specialisms</strong></p>
										<?php the_field('specialisms'); ?>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<?php
							endwhile;
							endif;
							wp_reset_postdata();
							?>
					</div>
	  		</div>
	  		<?php endif;?>

	  		<?php endif; ?>

	    </div>
    </div>
  </div>
</div>

<div class="strat-alli">
  <h2><?php the_field('strategic_alliance_title'); ?></h2>

  <div class="row">
    <div class="small-12 large-5 large-offset-1 columns all-list">
	    <?php the_field('strategic_alliance_left_col'); ?>
	  </div>

    <div class="small-12 large-5 end columns all-list">
	    <?php the_field('strategic_alliance_right_col'); ?>
	  </div>
  </div>
</div>

<?php if(get_field('international_downloads')) : ?>
<div class="row">
  <div class="small-12 large-10 large-offset-1 columns int-resource">
	  <h3><?php the_field('international_resources_title'); ?></h3>

	  <p class="resources">The following resources are free to download:</p>

	  <div class="avail-downloads">
		  <h4><?php the_field('international_resources_country'); ?></h4>

		  	<?php

				if(get_field('international_downloads'))
				{

					echo '<div class="row">';

					while(has_sub_field('international_downloads'))
					{
						$attachment_id = get_sub_field('international_download_item');
						$url = wp_get_attachment_url( $attachment_id );
						$title = get_the_title( $attachment_id );

						echo '<div class="small-12 large-6 columns end"><a href="' . $url . '" class="download">' . $title . '</a></div>';
					}

					echo '</div>';
				}
				?>
			</div>
	  </div>
  </div>
</div>
<?php endif; ?>

<div class="member-say-wrap">
  <div class="row">
	  <div class="small-12 large-10 large-offset-1 columns">
		  <?php the_field('international_testimony'); ?>
		</div>
  </div>
</div>

<?php endwhile; ?>
<?php endif; ?>

<?php get_footer();