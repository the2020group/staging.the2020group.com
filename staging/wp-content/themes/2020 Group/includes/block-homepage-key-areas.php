<div class="row collapse">
	<div class="small-12 columns">

			<div class="row">
				<div class="small-12 columns">
			    <div class="key-header">
				    <h3>2020 Innovation helping you with...</h3>
				    <!-- <a href="" class="gen-btn silver view-btn">View all</a> -->
			    </div>
				</div>
			</div>

			<div class="five-col-row" data-equalizer>

					<?php

						// check if the repeater field has rows of data
						if( have_rows('key_area_block') ):

					 	// loop through the rows of data
				    while ( have_rows('key_area_block') ) : the_row();

				    $classname = get_sub_field('class_name');
				    $title = get_sub_field('key_area_title');
						$content = get_sub_field('key_area_text');
						$link = get_sub_field('page_link_2');

					?>

					<div class="small-12 five-col-col">
	          <div class="key-block <?php echo $classname; ?>" data-equalizer-watch>
	          	<h6>Key Area</h6>

	          	<h5><?php echo $title; ?></h5>

							<p><?php echo $content; ?></p>

	            <a href="<?php echo $link; ?>" class="gen-link read-more">Read More <span class="icon-right-arrow"></span></a>

	          </div>
	        </div>

	        <?php endwhile;

						endif;

					?>

	        <?php /* endfor; */ ?>
	    </div>
	</div>
</div>