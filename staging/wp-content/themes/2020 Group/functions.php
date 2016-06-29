<?php

// add_filter('template_redirect','email_testing');

function email_testing(){
  if(isset($_GET['email_testing'])) {
    require_once(get_stylesheet_directory().'/woocommerce/emails/email-header.php');
    $user_login = 'the2020group';
    global $order;

		$order = new WC_Order( 16303 );
        //require_once(get_stylesheet_directory().'/woocommerce/emails/customer-processing-order.php');
        require_once(get_stylesheet_directory().'/woocommerce/emails/admin-new-order.php');
        require_once(get_stylesheet_directory().'/woocommerce/emails/email-footer.php');
    exit;
  }
}

add_action( 'admin_init', 'redirect_non_admin_users' );
function redirect_non_admin_users() {
    if (( ! current_user_can( 'assign_product_terms' ) && '/wp-admin/admin-ajax.php' != $_SERVER['PHP_SELF'] )) {
       wp_redirect(  home_url('/dashboard/') );
        exit;
    }
}


// Add custom functions
require_once( 'includes/custom-functions.php' );
require_once( 'includes/custom-post-types.php' );
require_once( 'includes/custom-taxonomies.php' );
require_once( 'includes/international-subscription-functions.php' );
require_once( 'includes/export-customise.php' );
require_once( 'includes/infusedwoopro.php' );


// Add theme support
add_theme_support('post-thumbnails');


// Add theme support woocommerce
add_theme_support( 'woocommerce' );

// Add Excerpts to pages
add_action( 'init', 'my_add_excerpts_to_pages' );
function my_add_excerpts_to_pages() {
     add_post_type_support( 'page', 'excerpt' );
}


// WP custom background (thx to @bransonwerner for update)
add_theme_support( 'custom-background',
    array(
    'default-image' => '',    // background image default
    'default-color' => '',    // background color default (dont add the #)
    'wp-head-callback' => '_custom_background_cb',
    'admin-head-callback' => '',
    'admin-preview-callback' => ''
    )
);


// RSS thingy
add_theme_support('automatic-feed-links');


// adding post format support
add_theme_support( 'post-formats',
	array(
		'aside',             // title less blurb
		'gallery',           // gallery of images
		'link',              // quick link to other site
		'image',             // an image
		'quote',             // a quick quote
		'status',            // a Facebook like status update
		'video',             // video
		'audio',             // audio
		'chat'               // chat transcript
	)
);


// WP menus
add_theme_support( 'menus' );


// Add image sizes
add_image_size( 'thumbnail', 200, 200, true );
add_image_size( 'image', 700, 350, true );
add_image_size( 'teamimage', 400, 400, true );
add_image_size( 'exhibitor-logo', 200, 9999, false );
add_image_size( 'exhibitor-logo-r', 151, 9999, false );
add_image_size( 'services-logo', 9999, 75, false );

//add_image_size( 'home-herosm', 400, 303, true );
//add_image_size( 'home-heromd', 800, 605, true );
add_image_size( 'home-hero', 1275, 965, true );
add_image_size( 'home-panels', 700, 400, true );

add_image_size( 'sidepanel-image', 500, 9999, false );
add_image_size( 'conf-imagesm', 300, 200, true );
add_image_size( 'conf-image', 800, 556, true );


// Register our sidebars and widgetized areas.
function arphabet_widgets_init() {

	register_sidebar( array(
		'name' => 'Main Sidebar',
		'id' => 'main-sidebar',
		'before_widget' => '<div class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h2>',
		'after_title' => '</h2>',
	) );

}
add_action( 'widgets_init', 'arphabet_widgets_init' );


// Change default excerpt
function new_excerpt_more( $more ) {
	return ' <a class="read-more" href="'. get_permalink( get_the_ID() ) . '">' . __('Read More', 'your-text-domain') . '</a>';
}
add_filter( 'excerpt_more', 'new_excerpt_more' );
