<div class="dash-block" data-purchase-type="parent-loop">
    <div class="dash-ajax-overlay">
        <div class="overlay-content">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/library/images/dashboard/default.gif"><br/>
            Saving...
        </div>
    </div>
    <div class="block-copy">
        <?php

            //echo 'Product ID = '. $product->ID;

            $terms = get_the_terms( $product->ID, 'product_cat' );
            //print_r($terms);
            $cat  = '';

            foreach ($terms as $term) {
                if(in_array($term->term_id,array(10,11,14,27,28,97))) {
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

        <a href="<?php echo wp_nonce_url( admin_url( 'admin-ajax.php?action=generate_wpo_wcpdf&template_type=invoice&order_ids=' . $product->order_id . '&my-account'), 'generate_wpo_wcpdf' ); ?>" class="gen-btn btn-color orange">Invoice</a>
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

          <?php //if (get_field('product_download', $product->ID)) :

            $variant_id = get_variant_from_order_product($product->order_id,$product->ID);
            //$variant = new WC_Product_Variable($variant_id);

            if ($variant_id == '') {
                $variant = get_post_meta($product->ID,'_downloadable_files',true);
            } else {
                $variant = get_post_meta($variant_id,'_downloadable_files',true);
            }
            if ($variant) {
                foreach ($variant as $download) {
                    $download_variant_url = $download['file'] ;
                    $download_variant_title = $download['name'] ;
                }
            }

            if (isset($download_variant_url) && $download_variant_url != '') {

                ?>
                    <a href="<?php echo $download_variant_url; ?>" title="<?php echo $download_variant_title; ?>" target="_blank" class="gen-btn btn-color orange">Download</a>
                <?php
                $download_variant_url = '';
            }

            switch ($cat) {
              case 'Webinars':
              case 'ACCA Webinars':
                $product->p_type = 'webinar';
                if (!$is_child_user) {
                    $product->seats = get_webinar_seats($product->ID,$user_id,$year);
                } else {
                    $product->seats = get_webinar_seats($product->ID,$child_user_id,$year);
                }
                break;

              case 'Conferences':
                $product->p_type = 'conference';
                if (!$is_child_user) {
                    $product->seats = get_conference_seats($product->ID,$user_id,$year);
                } else {
                    $product->seats = get_conference_seats($product->ID,$child_user_id,$year);
                }
                break;

              case 'Workshops':
                $product->p_type = 'workshop';
                if (!$is_child_user) {
                    $product->seats = get_workshop_seats($product->ID,$user_id,$year);
                } else {
                    $product->seats = get_workshop_seats($product->ID,$child_user_id,$year);
                }
                break;

              case 'Focus Groups':
                $product->p_type = 'focusgroup';
                if (!$is_child_user) {
                    $product->seats = get_focusgroup_seats($product->ID,$user_id,$year);
                } else {
                    $product->seats = get_focusgroup_seats($product->ID,$child_user_id,$year);
                }
                break;

              case 'Subscriptions':
                $product->p_type = 'subscription';
                break;
            }

            ?>

            <div class="seats">
                <?php


                    //if (!$is_child_user || $is_sibling) {
                       // $assigned = get_assigned_users($product->ID, $year, $user_id);
                    //} else {
                     //   $assigned = get_assigned_users($product->ID, $year, $child_user_id);
                   // }


                    if (/*$product->p_type != 'conference' &&*/ $product->p_type != 'subscription' && count($product->seats) > 0 ) {

                        echo '<form id="purchase-'.$counter.'">';
                            echo '<input type="hidden" name="booking_year" value="'.$year.'" />';
                            echo '<input type="hidden" name="product_id" value="'.$product->ID.'" />';
                            $event = get_field('date',$product->ID);
                            echo '<input type="hidden" name="event_date" value="'.substr($event,0,4).'-'.substr($event,4,2).'-'.substr($event,6,2).'" />';

                            echo '<ol>';

                            $assigned_li = '';
                            $unassigned_li = '';


                            if ($product->seats && count($product->seats) > 0) {

                                foreach ($product->seats as $order_id => $seat) {
                                    $assigned = get_assigned_users($product->ID, $order_id, $year, $user_id);

                                    foreach ($assigned as $assigned_user) {
                                        $assigned_li .= '<li>'.$assigned_user.'</li>';
                                    }

                                    $left_seats = $seat - count($assigned);

                                    if($left_seats === 0) {
                                        $hide_button = true;
                                    }

                                    for ($i = $left_seats; $i > 0; $i--) {

                                        $unassigned_li .= '<li><select name="users['.$order_id.'][]"><option value="">Select Member</option>'.$account_dropdown.'</select></li>';

                                    }
                                }
                                $hide_button = false;
                            }
                            else {

                                $hide_button = true;
                            }
                            echo $assigned_li;
                            echo $unassigned_li;
                            if($unassigned_li == '') {
                                $hide_button = true;
                            }
                            echo '</ol>';

                            if (!$hide_button) {
                                echo '<button data-counter="'.$counter.'" class="save-'.$product->p_type.'s gen-btn btn-color orange" style="float: right;">Save</button>';
                            }

                        echo '</form>';
                    }
                    elseif ( $product->p_type == 'conference' ) {
                        echo '<form id="purchase-'.$counter.'">';
                            echo '<input type="hidden" name="booking_year" value="'.$year.'" />';
                            echo '<input type="hidden" name="product_id" value="'.$product->ID.'" />';
                            $event = get_field('date',$product->ID);
                            echo '<input type="hidden" name="event_date" value="'.substr($event,0,4).'-'.substr($event,4,2).'-'.substr($event,6,2).'" />';

                            foreach ($product->seats as $order_id => $seat_list) {
                                error_log('about to sort seats');
                                error_log($seat_list);
                                if(is_array($seat_list)) {
                                    error_log('seats array found');
                                    foreach ($seat_list as $k=>$v) {

                                        error_log('looping through seats');
                                        $temp_k = str_replace('-', ' ',$k);
                                        echo '<h4>'.ucwords($temp_k).'</h4>';

                                        $variant_id = get_post_id_from_meta( 'attribute_conference-package',$temp_k );

                                        $assigned_variants = get_assigned_users_conf($variant_id, $order_id, $year, $user_id);

                                        $assigned_li   = '';
                                        $unassigned_li = '';

                                        echo '<ol>';
                                        if (count($assigned_variants)>0) {
                                            foreach ($assigned_variants as $a) {
                                                 $assigned_li .= '<li>'.$a.'</li>';
                                            }
                                            $v = $v - count($assigned_variants);
                                        }
                                        if ($v > 0) {
                                            for ($i = $v; $i > 0; $i--) {
                                                $unassigned_li .= '<li><select name="users['.$order_id.']['.$variant_id.'][]"><option value="">Select Member</option>'.$account_dropdown.'</select></li>';
                                            }
                                            $hide_button = false;
                                        }
                                        else {
                                            //temp redo count
                                            //$hide_button = true;
                                        }
                                        echo $assigned_li;
                                        echo $unassigned_li;
                                        echo '</ol>';
                                    }
                                }
                            }
                            if (!$hide_button) {
                                echo '<button data-counter="'.$counter.'" class="save-conferences gen-btn btn-color orange" style="float: right;">Save</button>';
                            }
                        echo '</form>';
                    }
                ?>
            </div>


    </div>
</div>
