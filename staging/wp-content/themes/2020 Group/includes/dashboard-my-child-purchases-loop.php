<div class="dash-block" data-purchase-type="child-loop">
    <div class="block-copy">
        <?php

          $product = get_post($product['product_id']);

            $terms = get_the_terms( $product->ID, 'product_cat' );
            //print_r($terms);
            $cat  = '';

            foreach ($terms as $term) {
                if(in_array($term->term_id,array(10,11,27,28,92,97))) {
                    $cat = $term->name;
                    break;
                }
            }
        ?>
        <h4><?php echo $product->post_title;?></h4>
        <div><?php echo get_webinar_date ($product->ID); ?></div>
        <div><?php echo strip_tags(substr($product->post_content,0,330));?>...
            <a href="<?php echo get_permalink($product->ID); ?>" class="more">View More</a>
        </div>

          <?php if (get_field('dashboard_pmd_webinar', $product->ID)) : ?>
            <a href="<?php echo get_field('dashboard_pmd_webinar', $product->ID); ?>" target="_blank" class="gen-btn btn-color orange">Stream</a>
          <?php endif; ?>

          <?php if (get_field('dashboard_pmd_webinar_mp4_link', $product->ID)) : ?>
            <a href="<?php echo get_field('dashboard_pmd_webinar_mp4_link', $product->ID); ?>" target="_blank" class="gen-btn btn-color orange">Download</a>
          <?php endif; ?>

          <?php if (get_field('dashboard_cpd_webinar', $product->ID)) : ?>
            <a href="<?php echo get_field('dashboard_cpd_webinar', $product->ID); ?>" target="_blank" class="gen-btn btn-color orange">Stream Webinar</a>
          <?php endif; ?>

          <?php if (get_field('dashboard_webinar_mp4_link', $product->ID)) : ?>
            <a href="<?php echo get_field('dashboard_webinar_mp4_link', $product->ID); ?>" target="_blank" class="gen-btn btn-color orange">Download Webinar</a>
          <?php endif; ?>

          <?php if (get_field('dashboard_webinar_notes_link', $product->ID)) :?>
            <a href="<?php echo get_field('dashboard_webinar_notes_link', $product->ID); ?>" target="_blank" class="gen-btn btn-color orange">Notes</a>
          <?php endif; ?>

          <?php if (get_field('dashboard_webinar_slides_link', $product->ID)) :?>
            <a href="<?php echo get_field('dashboard_webinar_slides_link', $product->ID); ?>" target="_blank" class="gen-btn btn-color orange">Slides</a>
          <?php endif; ?>

          <?php if (get_field('product_download', $product->ID)) :?>
            <a href="<?php echo get_field('product_download', $product->ID); ?>" target="_blank" class="gen-btn btn-color orange">Download</a>
          <?php endif; ?>

          <?php 
          /*
          $assigned = get_assigned_users($product->ID, $year, $parent_user_id);

          if (count($assigned)>0) {

            echo '<div class="seats"><h4>Current attendees</h4><ul>';

              foreach ($assigned as $a) {

              echo '<li>'.$a.'</li>';

              }
              echo '</ul></div>';
          } */?>

    </div>
</div>