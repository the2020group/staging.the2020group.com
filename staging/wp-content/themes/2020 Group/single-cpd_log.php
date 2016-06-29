
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

<?php wp_head(); ?>


</head>
<body <?php body_class(); ?>>

    <div class="row">

        <div class="small-12 medium-8 columns" role="main">

            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">

                <header class="article-header">

                    <h1>

                        <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>

                    </h1>

                </header>

                <section class="entry-content">

                    <?php if ( has_post_thumbnail()) : ?>

                        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" >

                            <?php the_post_thumbnail(); ?>

                        </a>

                    <?php endif; ?>

                    <?php the_content(); ?>

                </section>



            </article>

            <?php endwhile; ?>

            <?php endif; ?>



        </div>



    </div>



<?php wp_footer(); ?>

</body>
</html>