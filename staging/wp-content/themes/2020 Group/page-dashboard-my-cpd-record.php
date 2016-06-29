<?php

/*
 * Template Name: Dashboard - My CPD Record
 */

get_header(); ?>

		<div class="dash-wrap">

    <div class="row collapse">

        <div class="small-1 medium-1 columns" role="main" style="background: #000; color: #fff">
            <?php get_sidebar('dashboard'); ?>
        </div>

        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <div class="small-11 medium-11 columns" role="main">

	        	<div id="dash-main">

            <h2><?php the_title(); ?></h2>
            <?php
                // determine  which year to display

                if (isset($_GET['y']) && is_numeric($_GET['y'])) {
                   // $display_year = $_GET['y'];
                    $selected_year = $_GET['y'];
                }

                $types = array('ticket','webinar','article','conference','workshop','focus-group');
                if (isset($_GET['t']) && in_array($_GET['t'], $types) ) {
                    $selected_type = $_GET['t'];
                }

                $current_year = date('Y');

                // now set the year of the first CPD entry
                $args = array(
                    'post_type'   => 'cpd_log',
                    'author' => get_current_user_id(),
                    'posts_per_page' => 1,
                    'order' => 'ASC',
                    'post_status' => 'any'
                );
                $post = new WP_Query($args);

                $current_user =  wp_get_current_user();

                $first_year = date('Y',strtotime($current_user->data->user_registered));

                // reset the query data
                wp_reset_postdata();

                // if no y provided set it to the current year
                if (!isset($_GET['y'])) {
                    //$display_year = $current_year;
                    $selected_year = $current_year;

                }
                else {

                    // if trying to access a year that is too far back set it to the first year
                    if ($selected_year < $first_year) {
                        $selected_year = $first_year;
                    }
                    // future year will be redirected to current year
                    elseif ($selected_year > $current_year) {
                        $selected_year = $current_year;
                    }
                    else {
                        $selected_year = $_GET['y'];
                    }

                }

            ?>
            <div class="dash-btns">
                <?php for ($i=$current_year; $i>=$first_year; $i--) : ?>

                    <?php

                        $button = 'lsilver';

                        if ( isset($selected_year) && $i==$selected_year ) {
                            $button = 'silver';
                        }

                    ?>

                    <a href="#my-cpd-record" data-uri="/dashboard/my-cpd-record?y=<?php echo $i;?><?php if (isset($selected_type)) {echo '&t='.$selected_type;}?>" class="dataload <?php echo $button;?> gen-btn"><?php echo $i;?></a>
                <?php endfor; ?>

                <a href="/dashboard/my-cpd-record/?cpd_log_export&uid=<?php echo get_current_user_id(); ?>" class="gen-btn">Export</a>
                <a href="/dashboard/create-cpd-log/" class="gen-btn fancybox fancybox.iframe">Add Entry</a>

            </div>
              <div class="dash-btns">
                <a href="#my-cpd-record" data-uri="/dashboard/my-cpd-record?<?php if (isset($selected_year)) {echo 'y='.$selected_year.'&';}?>" class="dataload gen-btn <?php if (!in_array($_GET['t'],$types)) { echo ' silver '; }?>">All</a>
                <a href="#my-cpd-record" data-uri="/dashboard/my-cpd-record?<?php if (isset($selected_year)) {echo 'y='.$selected_year.'&';}?>t=article" class="dataload gen-btn <?php if ($_GET['t'] =='article') { echo ' silver ';} ?>">Articles</a>
                <a href="#my-cpd-record" data-uri="/dashboard/my-cpd-record?<?php if (isset($selected_year)) {echo 'y='.$selected_year.'&';}?>t=webinar" class="dataload gen-btn <?php if ($_GET['t'] =='webinar') { echo ' silver ';} ?>">Webinars</a>
                <a href="#my-cpd-record" data-uri="/dashboard/my-cpd-record?<?php if (isset($selected_year)) {echo 'y='.$selected_year.'&';}?>t=conference" class="dataload gen-btn <?php if ($_GET['t'] =='conference') { echo ' silver ';} ?>">Conferences</a>
                <a href="#my-cpd-record" data-uri="/dashboard/my-cpd-record?<?php if (isset($selected_year)) {echo 'y='.$selected_year.'&';}?>t=workshop" class="dataload gen-btn <?php if ($_GET['t'] =='workshop') { echo ' silver ';} ?>">Workshops</a>
                <a href="#my-cpd-record" data-uri="/dashboard/my-cpd-record?<?php if (isset($selected_year)) {echo 'y='.$selected_year.'&';}?>t=focus-group" class="dataload gen-btn <?php if ($_GET['t'] =='focus-group') { echo ' silver ';} ?>">Focus groups</a>
            </div>
            <?php




                $args = array(
                    'post_type'   => 'cpd_log',
                    'author' => get_current_user_id(),
                    'post_status' => 'any',
                    'date_query'  => array(
                                            array(
                                                'year' => $selected_year
                                            )
                                     ),

                );

                // if (isset($_GET['t']) && in_array($_GET['t'], $types) ) {


                //     $args['tax_query'] = array(
                //                             array(  //'taxonomy' => 'cpd_log',
                //                                     'taxonomy' => 'product_cat',
                //                                     'field'    => 'slug',
                //                                     'terms'    => $_GET['t']
                //                                 )
                //                       );
                // }



                $the_query = new WP_Query($args); 





            ?>
            <?php if ($the_query->have_posts()) : while ($the_query->have_posts()) : $the_query->the_post(); ?>
                <?php $parents = get_post_ancestors( get_the_ID() ); ?>
                <?php $parent_post = get_post($parents[0]);?>

                <?php                    
                    $terms = get_the_terms( $parent_post->ID, 'product_cat' );
                    if (!$terms) {$terms = get_the_terms( $post->ID, 'product_cat' );}

                    $checkstring='';
                    foreach ($terms as $term) {
                        $checkstring .= $term->slug;
                    }
                    
if ( !isset($_GET['t']) ) {

  ?>

            <div class="dash-block" id="cpd-<?php echo get_the_ID();?>">
                <?php
                    $iscustom = get_post_meta(get_the_ID(),'cpd_log_type', true);
                    $customlink = get_post_meta(get_the_ID(),'cpd_log_link', true);
                ?>

                <div class="block-copy">

                  <div class="copy-content">
                    <?php if($iscustom) : ?>
                        <h4><?php the_title(); ?></h4>
                        <p><?php the_content(); ?> </p>
                        <?php if($customlink) : ?>
                            <p><a href="<?php echo $customlink; ?>" target="_blank" class="gen-btn btn-color orange">Visit <?php echo $customlink; ?></a>
                        <?php endif; ?>

                    <?php else : 
                    //if (strpos($checkstring,$_GET['t'])>0 ) {
                    ?>

                        <h4><?php echo $parent_post->post_title;?></h4>
                        <p><?php echo substr($parent_post->post_content,0,330);?>...</p>
                        <a href="<?php echo get_permalink($parent_post->ID);?>" class="gen-btn btn-color orange">View</a>

                    <?php 
                    //}
                endif; ?>

                    </div>

                    <div class="block-edit">
                        <?php if($iscustom && $iscustom != '') : ?>
                            <a href="/dashboard/edit-cpd-log/?id=<?php echo get_the_ID();?>" class="add-reflection fancybox fancybox.iframe"><span class="icon-edit-pen"></span></a>
                            <a href="/dashboard/edit-cpd-log/?delete=1&id=<?php echo get_the_ID();?>" data-cpd-id="<?php echo get_the_ID();?>" class="delete-cpd fancybox fancybox.iframe"><span class="icon-remove"></span></a>
                        <?php else : ?>
                            <a href="<?php echo get_permalink($parent_post->ID);?>"><span class="icon-eye"></span></a>
                            <a href="/dashboard/edit-reflection/?id=<?php echo get_the_ID();?>" class="add-reflection fancybox fancybox.iframe"><span class="icon-edit-pen"></span></a>
                            <a href="/dashboard/edit-cpd-log/?delete=1&id=<?php echo get_the_ID();?>" data-cpd-id="<?php echo get_the_ID();?>" class="delete-cpd fancybox fancybox.iframe"><span class="icon-remove"></span></a>
                        <?php endif; ?>
                  </div>

                </div>
                <?php //} ?>

            </div>

            <div>&nbsp;</div>
            <?

}


if (strpos($checkstring,$_GET['t'])>0 && isset($_GET['t'])) {

                ?>

            <div class="dash-block" id="cpd-<?php echo get_the_ID();?>">
                <?php
                    $iscustom = get_post_meta(get_the_ID(),'cpd_log_type', true);
                    $customlink = get_post_meta(get_the_ID(),'cpd_log_link', true);
                ?>

				<div class="block-copy">

                  <div class="copy-content">
                    <?php if($iscustom) : ?>
                        <h4><?php the_title(); ?></h4>
                        <p><?php the_content(); ?> </p>
                        <?php if($customlink) : ?>
                            <p><a href="<?php echo $customlink; ?>" target="_blank" class="gen-btn btn-color orange">Visit <?php echo $customlink; ?></a>
                        <?php endif; ?>

                    <?php else : 
                    //if (strpos($checkstring,$_GET['t'])>0 ) {
                    ?>

                        <h4><?php echo $parent_post->post_title;?></h4>
                        <p><?php echo substr($parent_post->post_content,0,330);?>...</p>
                        <a href="<?php echo get_permalink($parent_post->ID);?>" class="gen-btn btn-color orange">View</a>

                    <?php 
                    //}
                endif; ?>

                	</div>

                	<div class="block-edit">
                        <?php if($iscustom && $iscustom != '') : ?>
                            <a href="/dashboard/edit-cpd-log/?id=<?php echo get_the_ID();?>" class="add-reflection fancybox fancybox.iframe"><span class="icon-edit-pen"></span></a>
                            <a href="/dashboard/edit-cpd-log/?delete=1&id=<?php echo get_the_ID();?>" data-cpd-id="<?php echo get_the_ID();?>" class="delete-cpd fancybox fancybox.iframe"><span class="icon-remove"></span></a>
                        <?php else : ?>
                            <a href="<?php echo get_permalink($parent_post->ID);?>"><span class="icon-eye"></span></a>
                            <a href="/dashboard/edit-reflection/?id=<?php echo get_the_ID();?>" class="add-reflection fancybox fancybox.iframe"><span class="icon-edit-pen"></span></a>
                            <a href="/dashboard/edit-cpd-log/?delete=1&id=<?php echo get_the_ID();?>" data-cpd-id="<?php echo get_the_ID();?>" class="delete-cpd fancybox fancybox.iframe"><span class="icon-remove"></span></a>
                        <?php endif; ?>
                  </div>

				</div>
                <?php //} ?>

            </div>

            <div>&nbsp;</div>
            <?php } ?>
            <?php endwhile; ?>
            <?php else : ?>
            <p>No <?php if ($_GET['t']) : echo $_GET['t'] . ' '; endif; ?>records found.</p>
            <?php endif; ?>

            <?php

                wp_reset_query();
            ?>

        </div>

        </div>
        <?php endwhile; ?>
        <?php endif; ?>

    </div>
<?php /*
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery(".fancybox").fancybox();
    });
</script>
*/ ?>
<?php get_footer();