<!doctype html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

<head>
<meta charset="utf-8">

<title><?php bloginfo('name'); ?> | <?php is_home() ? bloginfo('description') : wp_title(''); ?></title>

<meta name="HandheldFriendly" content="True">
<meta name="MobileOptimized" content="320">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/library/images/apple-icon-touch.png">
<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
<!--[if IE]>
	<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
<![endif]-->
<?php // set /favicon.ico for IE10 win ?>
<meta name="msapplication-TileColor" content="#d3492f">
<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/library/images/win8-tile-icon.png">

<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

<script src="//use.typekit.net/nab6gby.js"></script>
<script>try{Typekit.load();}catch(e){}</script>

<?php wp_head(); ?>
<?php

	// get id of current user
    $user_id = get_current_user_id();

    // if the user is logged in
    if ($user_id <= 0) :
    	// get all active subscriptions
        exit;

	endif;
?>
<?php

/*
 * Template Name: Dashboard - Create CPD Log
 */


$current_user = wp_get_current_user();

if (isset($_POST['save'])) {

    $cpd_title      = trim(strip_tags($_POST['title']));
    $cpd_content    = trim(strip_tags($_POST['content']));
    $cpd_link       = trim(strip_tags($_POST['link']));

    /*$cpd_link       = '<a href="'.$cpd_link.'">'.$cpd_link.'</a>';
    $cpd_content    = $cpd_content . '<br/><br/>' . $cpd_link;*/

    $post_type      = 'cpd_log';




    $my_post = array(
        'post_title'    => $cpd_title,
        'post_content'  => $cpd_content,
        'post_type'     => $post_type
    );



    // Update the post into the database
    $post_id = wp_insert_post( $my_post );
    update_post_meta($post_id, 'cpd_log_link', $cpd_link);
    update_post_meta($post_id, 'cpd_log_type', 'non-2020');

    ?>

    <script>
    jQuery(document).ready(function() {
        parent.jQuery.fancybox.close();
        parent.location.reload(true);
    });
    </script>
    <?php
    exit;
}

/* get_header(); */ ?>

</head>
<body <?php body_class(); ?>>

	<div id="outer-wrap" class="">



    <div class="row">


        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

        <div class="small-12 columns">

                <h3>Add a CPD Log</h3>

                <div class="dash-wrap">

                <div class="dash-block">

                <div class="row">

                    <div class="small-12 columns" role="main">


                        <form action="" method="post" id="edit_reflection">
                            <div class="row collapse">

                                <div class="small-12 medium-12 columns">
                                    <div>
										<p>Title</p>

                                        <input name="title" type="text" />
                                    </div>
                                </div>
                            </div>

                            <div class="row collapse">

                                <div class="small-12 medium-12 columns">
                                    <div>
                                        <p>Content</p>

                                        <textarea name="content" rows="6"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row collapse">

                                <div class="small-12 medium-12 columns">
                                    <div>
                                        <p>Enter Website URL (including http://www)</p>

                                        <input name="link" type="url" value="http://www." />
                                    </div>
                                </div>
                            </div>

                            <div class="row collapse">

                                <div class="small-12 medium-2 columns">
                                    <button class="gen-btn orange" type="submit" name="save">Save</button>
                                </div>

                            </div>

                        </form>
                    </div>
                </div>

                </div>

                </div>

        </div>
        <?php endwhile; ?>
        <?php endif; ?>
    </div>

<?php wp_footer();