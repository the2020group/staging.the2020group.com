<div class="row">

	<div class="small-12 medium-8 medium-offset-2 columns">
  
    <h3><?php the_field('learn_from_experts_title');?></h3>
    
    <div class="row expert-logos">
	    <div class="small-8 small-offset-2 medium-4 medium-offset-0 columns">
		    <img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/homepage/2020Innovation.png"  alt="" class="logo">
	    </div>
	    <div class="small-8 small-offset-2 medium-4 medium-offset-0 columns">
		    <img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/homepage/2020PracticeExchange.png"  alt="" class="logo">
	    </div>
	    <div class="small-8 small-offset-2 medium-4 medium-offset-0 end columns">
		    <img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/homepage/2020TaxProtection.png"  alt="" class="logo">
	    </div>
    </div>
    
    <?php the_field('learn_from_experts_text');?>
    <a href="<?php the_field('learn_from_experts_link');?>" class="gen-btn orange icon lightbulb"><?php the_field('learn_from_experts_link_text');?></a>
  
  </div>
</div>