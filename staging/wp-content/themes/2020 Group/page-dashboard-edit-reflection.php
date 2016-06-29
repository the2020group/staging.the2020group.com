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
 * Template Name: Dashboard - Edit Reflection
 */


$current_user = wp_get_current_user();

if (isset($_POST['save'])) {

    $cpd_content    = trim(strip_tags($_POST['cpd-reflection']));
    $cpd_id         = $_POST['log_id'];



    $my_post = array(
      'ID'           => $cpd_id,
      'post_content' => $cpd_content
    );



    // Update the post into the database
    wp_update_post( $my_post );

    ?>

    <script>
    jQuery(document).ready(function() {
        parent.jQuery.fancybox.close();
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

        <?php

        $args = array(
            'post_type'     => 'cpd_log',
            'author'        => get_current_user_id(),
            'post_status'   => 'any',
            'p'             => $_GET['id']

        );

        query_posts($args);

        ?>

        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

        <div class="small-12 columns">

                <h3>Add/Edit</h3>

                <div class="dash-wrap">

                <div class="dash-block">

                <div class="row">

                    <div class="small-12 columns" role="main">


                        <form action="" method="post" id="edit_reflection">
                            <div class="row collapse">

                                <div class="small-12 medium-12 columns">
                                    <div>
										<p>What Did I Learn?</p>

                                        <textarea name="cpd-reflection"><?php echo $post->post_content; ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row collapse">

                                <div class="small-12 medium-2 columns">
                                    <input type="hidden" name="log_id" value="<?php echo $_GET['id']; ?>"  />
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