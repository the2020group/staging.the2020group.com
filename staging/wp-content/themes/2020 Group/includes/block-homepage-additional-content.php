<div class="row">
  
  <div class="small-12 medium-6 columns">
  	<div class="addi-content-block">
	    
	    <?php 

			$leftimage = get_field('additional_content_image_left');
			
			if( !empty($leftimage) ): 
			
				// vars
				$url = $leftimage['url'];
				$title = $leftimage['title'];
				$alt = $leftimage['alt'];
				
				// thumbnail
				$size = 'home-panels';
				$thumb = $leftimage['sizes'][ $size ];
				$width = $leftimage['sizes'][ $size . '-width' ];
				$height = $leftimage['sizes'][ $size . '-height' ]; ?>
			
				<img src="<?php echo $thumb; ?>" alt="<?php echo $alt; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" />
			
			<?php endif; ?>
	    
	    <div class="addi-content-copy">
		    <h3><?php the_field('additional_content_title_left');?></h3>
		    <?php the_field('additional_content_text_left');?>
	    </div>
    </div>
  </div>
  <div class="small-12 medium-6 columns">
	  <div class="addi-content-block">
	    <?php 

			$rightimage = get_field('additional_content_image_right');
			
			if( !empty($rightimage) ): 
			
				// $image = wp_get_attachment_image_src(get_sub_field('gallery_item'), 'home-panels');
				
				// vars
				$url = $rightimage['url'];
				$title = $rightimage['title'];
				$alt = $rightimage['alt'];
			
				// thumbnail
				$size = 'home-panels';
				$thumb = $rightimage['sizes'][ $size ];
				$width = $rightimage['sizes'][ $size . '-width' ];
				$height = $rightimage['sizes'][ $size . '-height' ];
			
				?>
			
				<img src="<?php echo $thumb; ?>" alt="<?php echo $alt; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>" />
			
			<?php endif; ?>
	    
	    <div class="addi-content-copy">
		    <h3><?php the_field('additional_content_title_right');?></h3>
		    <?php the_field('additional_content_text_right');?>
	  	</div>
	  </div>
	</div>
</div>

