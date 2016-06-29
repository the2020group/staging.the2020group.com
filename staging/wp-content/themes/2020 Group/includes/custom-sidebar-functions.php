<?php
  
/*************
  CONTENTS
  1 - Benefits list widget
  2 - Generic text based widget with optional link
  3 - Industry Experts Block
  4 - Testimonials Block
**************/
  
// 1 - BENEFITS LIST WIDGET
// Creating the widget 
class benefits_widget_2020 extends WP_Widget {

  function __construct() {
    parent::__construct(
      // Base ID of your widget
      'benefits_widget_2020', 
      
      // Widget name will appear in UI
      __('2020 Tick List Block', 'benefits_widget_domain'), 
    
      // Widget description
      array(
        'description' => __( 'A widget to display a list of benefits', 'benefits_widget_domain' ),
      )
    );
  }
  
  // Creating widget front-end
  // This is where the action happens
  public function widget( $args, $instance ) {
    
    $widgettitle = $instance['widgettitle'];
    $widgetlistitems = $instance['widget_benefits_list_item'];
   
    $args = array(
      'before_widget' => '<div class="side-block checklist-block">',
      'after_widget' => '</div>',
      'before_title' => '<h4>',
      'after_title' => '</h4>'        
    );
   
    if (count($widgetlistitems) > 0) {
        
      echo $args['before_widget'];
      echo $args['before_title'] . $widgettitle . $args['after_title'];
      // print some HTML for the widget to display here
      
      echo '<ul class="checklist">';
      foreach ($widgetlistitems as $key => $widgetlistitem) :
      echo '<li>' . $widgetlistitem . '</li>';
      endforeach;
      echo '</ul>';
      
      echo $args['after_widget'];
    }

  }
  		
  // Widget Backend 
  public function form( $instance ) {
    
    $widgettitle = $instance['widgettitle'];
    $widgetlistitems = $instance['widget_benefits_list_item'];
  
    ?>
  
    Panel Title:<br />
  	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'widgettitle' ); ?>" name="<?php echo $this->get_field_name( 'widgettitle' ); ?>" value="<?php echo stripslashes($widgettitle); ?>" />
  	<br /><br />

    <?php
    if (count($widgetlistitems)>0) :
      foreach ($widgetlistitems as $key => $widgetlistitem) : 
        if ($widgetlistitem !='') :?>
          List Item <?php echo $key+1; ?><br />
          <input type="text" class="widefat" name="<?php echo $this->get_field_name( 'widget_benefits_list_item' ); ?>[]" value="<?php echo stripslashes($widgetlistitem); ?>" />
          <br /><br />
        <?php endif; ?>
      <?php endforeach; ?>
    <?php endif; ?>
   
    Add a new List Item<br />
    <input type="text" class="widefat" name="<?php echo $this->get_field_name( 'widget_benefits_list_item' ); ?>[]" value="" />
    <br /><br />

    <input type="hidden" name="submitted" value="1" />
  
  <?php 
  }
	
  // Updating widget replacing old instances with new
  public function update( $new_instance, $old_instance ) {
    return $new_instance;
  }
}

// End Custom Benefits Widget


/********************************************************************
********************************************************************/



// 2 - Generic text-based widget with optional link
// Creating the widget 
class text_block_widget_2020 extends WP_Widget {

  function __construct() {
    parent::__construct(
      // Base ID of your widget
      'text_block_widget_2020', 
      
      // Widget name will appear in UI
      __('2020 Simple Text Block', 'text_block_widget_domain'), 
    
      // Widget description
      array(
        'description' => __( 'A widget to display a simple one liner with the option of a link', 'text_block_widget_domain' ),
      )
    );
  }
  
  // Creating widget front-end
  // This is where the action happens
  public function widget( $args, $instance ) {
    
    $panelcolor = $instance['panelcolor'];
    $widgettitle = $instance['widgettitle'];
    $textarea = $instance['textarea'];
    $panellink = $instance['panellink'];
    $panellinktitle = $instance['panellinktitle'];
    $panellinktarget = $instance['panellinktarget'];
    
    if ($panelcolor == 'blue') {
      $bgcolor = 'dbluebg';
      $btncolor = 'orange';
    }
    elseif ($panelcolor == 'orange') {
      $bgcolor = 'orangebg';
      $btncolor = 'blue';
    }
    elseif ($panelcolor == 'texturedpurple') {
      $bgcolor = 'texturebg';
      $btncolor = 'orange';
    }
    
    $args = array(
      'before_widget' => '<div class="side-block text-block ' . $bgcolor . '">',
      'after_widget' => '</div>',
      'before_title' => '<h4>',
      'after_title' => '</h4>'
    );
   
    if (count($textarea) > 0) {

      echo $args['before_widget'];
      echo $args['before_title'] . $widgettitle . $args['after_title'];
      // print some HTML for the widget to display here
      echo '<p>' . $textarea . '</p>';
      if ($panellink != '') {
        if ($panellink != '') {
          echo '<a href="' . $panellink . '" class="gen-btn ' . $btncolor . ' icon right-arrow" target="' . $panellinktarget . '">' . $panellinktitle . '</a>';
        }
        else {
          echo '<a href="' . $panellink . '" class="gen-btn ' . $btncolor . ' icon right-arrow">' . $panellinktitle . '</a>';  
        }
          
      }
      echo $args['after_widget'];
    }

  }
  		
  // Widget Backend 
  public function form( $instance ) {
    
    if($instance){
      $panelcolor = $instance['panelcolor'];
      $widgettitle = esc_attr($instance['widgettitle']);
      $textarea = esc_textarea($instance['textarea']);
      $panellink = esc_attr($instance['panellink']);
      $panellinktitle = esc_attr($instance['panellinktitle']);
      $panellinktarget = esc_attr($instance['panellinktarget']);
    } else {
      $widgettitle = '';
      $textarea = '';
      $panellink = '';
      $panellinktitle = 'Learn More';
      $panellinktarget = '';
    }
  
    ?>
  
    <br />
    <label for="<?php echo $this->get_field_id( 'panelcolor' ); ?> "><?php _e('Select Panel Colour:', 'panelcolor'); ?></label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'panelcolor' ); ?>" name="<?php echo $this->get_field_name( 'panelcolor' ); ?>">
      <option value="blue"<?php echo ($panelcolor=='blue')?'selected':''; ?>>Blue</option>
      <option value="orange"<?php echo ($panelcolor=='orange')?'selected':''; ?>>Orange</option>
      <option value="texturedpurple"<?php echo ($panelcolor=='texturedpurple')?'selected':''; ?>>Purple (Textured)</option>
      <option value="texturedorange"<?php echo ($panelcolor=='texturedorange')?'selected':''; ?>>Orange (Textured)</option>
    </select>
  	<br /><br />
  
    <label for="<?php echo $this->get_field_id( 'widgettitle' ); ?> "><?php _e('Panel Title:', 'widgettitle'); ?></label>
  	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'widgettitle' ); ?>" name="<?php echo $this->get_field_name( 'widgettitle' ); ?>" value="<?php echo stripslashes($widgettitle); ?>" />
  	<br /><br />

    <label for="<?php echo $this->get_field_id( 'textarea' ); ?> "><?php _e('Text Content:', 'textarea'); ?></label>
    <textarea id="<?php echo $this->get_field_id('textarea'); ?>" class="widefat" name="<?php echo $this->get_field_name('textarea'); ?>"><?php echo stripslashes($textarea); ?></textarea>
    <br /><br />

    <label for="<?php echo $this->get_field_id( 'panellink' ); ?> "><?php _e('Link (url):', 'panellink'); ?></label>
  	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'panellink' ); ?>" name="<?php echo $this->get_field_name( 'panellink' ); ?>" value="<?php echo stripslashes($panellink); ?>" />
  	<br /><br />
    
    <label for="<?php echo $this->get_field_id( 'panellinktarget' ); ?> "><?php _e('Link target (optional):', 'panellinktarget'); ?></label>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'panellinktarget' ); ?>" name="<?php echo $this->get_field_name( 'panellinktarget' ); ?>" value="<?php echo stripslashes($panellinktarget); ?>" />
    <br /><br />
  	
    <label for="<?php echo $this->get_field_id( 'panellinktitle' ); ?> "><?php _e('Button Title:', 'panellinktitle'); ?></label>
  	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'panellinktitle' ); ?>" name="<?php echo $this->get_field_name( 'panellinktitle' ); ?>" value="<?php echo stripslashes($panellinktitle); ?>" />
  	<br /><br />

    <input type="hidden" name="submitted" value="1" />
  
  <?php 
  }
	
  // Updating widget replacing old instances with new
  // Updating widget replacing old instances with new
  public function update( $new_instance, $old_instance ) {
    return $new_instance;
  }

/*
  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['panelcolor'] = $new_instance['panelcolor'];
    $instance['widgettitle'] = $new_instance['widgettitle'];
    $instance['textarea'] = $new_instance['textarea'];
    $instance['panellink'] = $new_instance['panellink'];
    $instance['panellinktitle'] = $new_instance['panellinktitle'];
    return $instance;
  }
*/
}

// End 2 - Generic text-based widget with optional link



/********************************************************************
********************************************************************/


// 3 - Industry Experts Block
// Creating the widget 
class experts_block_widget_2020 extends WP_Widget {

  function __construct() {
    parent::__construct(
      // Base ID of your widget
      'experts_block_widget_2020', 
      
      // Widget name will appear in UI
      __('2020 Industry Experts Block', 'experts_block_widget_domain'), 
    
      // Widget description
      array(
        'description' => __( 'A widget to display a an "industry experts" panel with some accompanying text and the option of a link', 'experts_block_widget_domain' ),
      )
    );
  }
  
  // Creating widget front-end
  // This is where the action happens
  public function widget( $args, $instance ) {
    
    $panelcolor = $instance['panelcolor'];
    $widgettitle = $instance['widgettitle'];
    $expertimage = $instance['expertimage'];
    $panellink = $instance['panellink'];
    $panellinktitle = $instance['panellinktitle'];
    
    if ($panelcolor == 'blue') {
      $bgcolor = 'dbluebg';
      $btncolor = 'orange';
    }
    elseif ($panelcolor == 'orange') {
      $bgcolor = 'orangebg';
      $btncolor = 'blue';
    }
    elseif ($panelcolor == 'texturedpurple') {
      $bgcolor = 'texturebg';
      $btncolor = 'orange';
    }
    
    if($expertimage == 'expertimage-2') {
      $expertImageClass = ' expertimage-2';
    } elseif ($expertimage == 'expertimage-3') {
      $expertImageClass = ' expertimage-3';
    } else {
      $expertImageClass = '';
    }
    
    $args = array(
      'before_widget' => '<div class="side-block ind-experts ' . $bgcolor . $expertImageClass . '">',
      'after_widget' => '</div>',
      'before_title' => '<h3>',
      'after_title' => '</h3>'
    );
   
    if ($widgettitle != '') {

      echo $args['before_widget'];
      echo $args['before_title'] . $widgettitle . $args['after_title'];
      // print some HTML for the widget to display here
//      echo '<p>' . $textarea . '</p>';
      if ($panellink != '') {
        echo '<a href="' . $panellink . '" class="gen-btn ' . $btncolor . ' icon right-arrow">' . $panellinktitle . '</a>';  
      }
      echo $args['after_widget'];
    }

  }
  		
  // Widget Backend 
  public function form( $instance ) {
    
    if($instance){
      $panelcolor = $instance['panelcolor'];
      $widgettitle = esc_attr($instance['widgettitle']);
      $expertimage = $instance['expertimage'];
      $panellink = esc_attr($instance['panellink']);
      $panellinktitle = esc_attr($instance['panellinktitle']);
    } else {
      $widgettitle = '';
      $panellink = '';
      $panellinktitle = 'Learn More';
    }
      
    ?>
  
    <br />
    <label for="<?php echo $this->get_field_id( 'panelcolor' ); ?> "><?php _e('Select Panel Colour:', 'panelcolor'); ?></label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'panelcolor' ); ?>" name="<?php echo $this->get_field_name( 'panelcolor' ); ?>">
      <option value="blue"<?php echo ($panelcolor=='blue')?'selected':''; ?>>Blue</option>
      <option value="orange"<?php echo ($panelcolor=='orange')?'selected':''; ?>>Orange</option>
      <option value="texturedpurple"<?php echo ($panelcolor=='texturedpurple')?'selected':''; ?>>Purple (Textured)</option>
      <option value="texturedorange"<?php echo ($panelcolor=='texturedorange')?'selected':''; ?>>Orange (Textured)</option>
    </select>
  	<br /><br />
  
    <label for="<?php echo $this->get_field_id( 'widgettitle' ); ?> "><?php _e('Panel Title:', 'widgettitle'); ?></label>
  	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'widgettitle' ); ?>" name="<?php echo $this->get_field_name( 'widgettitle' ); ?>" value="<?php echo stripslashes($widgettitle); ?>" />
  	<br /><br />


    <label for="<?php echo $this->get_field_id( 'expertimage' ); ?> "><?php _e('Expert Image:', 'expertimage'); ?></label><br />
    <input type="radio" id="<?php echo $this->get_field_id( 'expertimage' ) . '-1'; ?>" <?php echo ($expertimage=='expertimage-1')?' checked':''; ?> name="<?php echo $this->get_field_name( 'expertimage' ); ?>" value="expertimage-1">Image 1&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="radio" id="<?php echo $this->get_field_id( 'expertimage' ) . '-2'; ?>" <?php echo ($expertimage=='expertimage-2')?' checked':''; ?> name="<?php echo $this->get_field_name( 'expertimage' ); ?>" value="expertimage-2">Image 2&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="radio" id="<?php echo $this->get_field_id( 'expertimage' ) . '-3'; ?>" <?php echo ($expertimage=='expertimage-3')?' checked':''; ?> name="<?php echo $this->get_field_name( 'expertimage' ); ?>" value="expertimage-3">Image 3
    <br /><br />

    <label for="<?php echo $this->get_field_id( 'panellink' ); ?> "><?php _e('Link (url):', 'panellink'); ?></label>
  	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'panellink' ); ?>" name="<?php echo $this->get_field_name( 'panellink' ); ?>" value="<?php echo stripslashes($panellink); ?>" />
  	<br /><br />
  	
    <label for="<?php echo $this->get_field_id( 'panellinktitle' ); ?> "><?php _e('Button Title:', 'panellinktitle'); ?></label>
  	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'panellinktitle' ); ?>" name="<?php echo $this->get_field_name( 'panellinktitle' ); ?>" value="<?php echo stripslashes($panellinktitle); ?>" />
  	<br /><br />

    <input type="hidden" name="submitted" value="1" />
  
  <?php 
  }
	
  // Updating widget replacing old instances with new
  public function update( $new_instance, $old_instance ) {
    return $new_instance;
  }

}

// End 3 - Industry Experts Block



/********************************************************************
********************************************************************/



// 3 - Testimonials Block
// Creating the widget 
class testimonials_block_widget_2020 extends WP_Widget {

  function __construct() {
    parent::__construct(
      // Base ID of your widget
      'testimonials_block_widget_2020', 
      
      // Widget name will appear in UI
      __('2020 Testimonials Block', 'testimonials_block_widget_domain'), 
    
      // Widget description
      array(
        'description' => __( 'A widget to display a number of testimonials', 'testimonials_block_widget_domain' ),
      )
    );
  }
  
  // Creating widget front-end
  // This is where the action happens
  public function widget( $args, $instance ) {
    
    $panelcolor = $instance['panelcolor'];
    $widgettitle = $instance['widgettitle'];
//    $textarea = $instance['textarea'];
    $quotecount = $instance['quotecount'];
    $panellink = $instance['panellink'];
    $panellinktitle = $instance['panellinktitle'];
    
    if ($panelcolor == 'blue') {
      $bgcolor = 'dbluebg';
      $btncolor = 'orange';

    }
    elseif ($panelcolor == 'orange') {
      $bgcolor = 'orangebg';
      $btncolor = 'blue';
    }
    elseif ($panelcolor == 'texturedpurple') {
      $bgcolor = 'texturebg';
      $btncolor = 'orange';
    }
    
    $args = array(
      'before_widget' => '<div class="side-block quotes-block text-block ' . $bgcolor . '">',
      'after_widget' => '</div>',
      'before_title' => '<h3>',
      'after_title' => '</h3>'
    );
   
    echo $args['before_widget'];

    ?>
    
    <div class="quotes">	  	
    	<div class="quoteCarousel">
        <?php echo $args['before_title'] . $widgettitle . $args['after_title']; ?>
        
        <?php    			  
        $testimonial_args = array(
      		'post_type' => 'testimonials',
      		'posts_per_page' => $quotecount,
      		'post_status' => 'publish',
      		'orderby' => 'rand'
        );
        
        $testimonials = get_posts( $testimonial_args );
        foreach ( $testimonials as $testimonial ) : 
        setup_postdata( $testimonial );
        
        $testimonialContent = get_the_content();
        $affiliation = get_field('testimonial_byline', $testimonial->ID);
        $affiliationLink = get_field('testimonial_link', $testimonial->ID);
        
        ?>
          
        <div>
          <div class="row">                
            <div class="small-10 small-offset-1 columns">
            	<p class="ita"><?php echo $testimonialContent; ?></p>
              <p><strong><?php echo get_the_title($testimonial->ID); ?>
                <?php
                  if($affiliation) : ?>,&nbsp;
                    <?php if($affiliationLink) : ?>
                      <a href="<?php echo $affiliationLink; ?>" target="_blank" rel="nofollow"><?php echo $affiliation; ?></a>
                    <?php else : ?>
                      <?php echo $affiliation; ?>  
                    <?php endif;
                  endif; ?>    
              </strong></p>
            </div>
          </div>
        </div>  
          
        <?php endforeach; 
        wp_reset_postdata(); ?>
          
      </div>
    </div>

    <?php
      
    if ($panellink != '') {
      echo '<a href="' . $panellink . '" class="gen-btn ' . $btncolor . ' arrow">' . $panellinktitle . '</a>';  
    }
    echo $args['after_widget'];

  }
  		
  // Widget Backend 
  public function form( $instance ) {
    
    if($instance){
      $panelcolor = $instance['panelcolor'];
      $widgettitle = esc_attr($instance['widgettitle']);
      $quotecount = $instance['quotecount'];
      $panellink = esc_attr($instance['panellink']);
      $panellinktitle = esc_attr($instance['panellinktitle']);
    } else {
      $widgettitle = '';
      $panellink = '';
      $panellinktitle = 'Learn More';
    }
  
    ?>
  
    <br />
    <label for="<?php echo $this->get_field_id( 'panelcolor' ); ?> "><?php _e('Select Panel Colour:', 'panelcolor'); ?></label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'panelcolor' ); ?>" name="<?php echo $this->get_field_name( 'panelcolor' ); ?>">
      <option value="blue"<?php echo ($panelcolor=='blue')?'selected':''; ?>>Blue</option>
      <option value="orange"<?php echo ($panelcolor=='orange')?'selected':''; ?>>Orange</option>
      <option value="texturedpurple"<?php echo ($panelcolor=='texturedpurple')?'selected':''; ?>>Purple (Textured)</option>
      <option value="texturedorange"<?php echo ($panelcolor=='texturedorange')?'selected':''; ?>>Orange (Textured)</option>
    </select>
  	<br /><br />
  
    <label for="<?php echo $this->get_field_id( 'widgettitle' ); ?> "><?php _e('Panel Title:', 'widgettitle'); ?></label>
  	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'widgettitle' ); ?>" name="<?php echo $this->get_field_name( 'widgettitle' ); ?>" value="<?php echo stripslashes($widgettitle); ?>" />
  	<br /><br />
  	
    <label for="<?php echo $this->get_field_id( 'quotecount' ); ?> "><?php _e('Select Quantity to show:', 'quotecount'); ?></label>
    <select class="widefat" id="<?php echo $this->get_field_id( 'quotecount' ); ?>" name="<?php echo $this->get_field_name( 'quotecount' ); ?>">
      <option value="1"<?php echo ($quotecount=='1')?'selected':''; ?>>1</option>
      <option value="2"<?php echo ($quotecount=='2')?'selected':''; ?>>2</option>
      <option value="3"<?php echo ($quotecount=='3')?'selected':''; ?>>3</option>
      <option value="4"<?php echo ($quotecount=='4')?'selected':''; ?>>4</option>
      <option value="5"<?php echo ($quotecount=='5')?'selected':''; ?>>5</option>
    </select>
  	<br /><br />

    <label for="<?php echo $this->get_field_id( 'panellink' ); ?> "><?php _e('Link (url):', 'panellink'); ?></label>
  	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'panellink' ); ?>" name="<?php echo $this->get_field_name( 'panellink' ); ?>" value="<?php echo stripslashes($panellink); ?>" />
  	<br /><br />
  	
    <label for="<?php echo $this->get_field_id( 'panellinktitle' ); ?> "><?php _e('Button Title:', 'panellinktitle'); ?></label>
  	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'panellinktitle' ); ?>" name="<?php echo $this->get_field_name( 'panellinktitle' ); ?>" value="<?php echo stripslashes($panellinktitle); ?>" />
  	<br /><br />

    <input type="hidden" name="submitted" value="1" />
  
  <?php 
  }
	
  // Updating widget replacing old instances with new
  public function update( $new_instance, $old_instance ) {
    return $new_instance;
  }

}

// End 4 - Testimonials



/********************************************************************
********************************************************************/



// Register and load all the widgets
function wpb_load_widget() {  
	register_widget( 'benefits_widget_2020' );
	register_widget( 'text_block_widget_2020' );
	register_widget( 'experts_block_widget_2020' );
	register_widget( 'testimonials_block_widget_2020' );
}

add_action( 'widgets_init', 'wpb_load_widget' );
