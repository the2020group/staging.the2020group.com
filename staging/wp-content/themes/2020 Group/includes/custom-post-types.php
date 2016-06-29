<?php
	
register_taxonomy('persona',array('post','product','pmdwebinars','cpdwebinars','page'),array( 'hierarchical' => true, 'label' => 'Personas','show_ui' => true,'query_var' => true,'rewrite' => array('slug' => 'interested-in', 'hierarchical' => true),'singular_label' => 'Persona', 'show_admin_column' => true) );

/**
 * Register `PDM Webinars` post type
 */
function pmdweb_post_type() {
   
   // Labels
	$labels = array(
		'name' => _x("PMD Webinars", "post type general name"),
		'singular_name' => _x("PMD Webinar", "post type singular name"),
		'menu_name' => 'PMD Webinars',
		'add_new' => _x("Add New", "PMD Webinar"),
		'add_new_item' => __("Add New PMD Webinar"),
		'edit_item' => __("Edit Profile"),
		'new_item' => __("New Profile"),
		'view_item' => __("View Profile"),
		'search_items' => __("Search Profiles"),
		'not_found' =>  __("No Profiles Found"),
		'not_found_in_trash' => __("No Profiles Found in Trash"),
		'parent_item_colon' => ''
	);
	
	// Register post type
	register_post_type('pmdwebinars' , array(
		'labels' => $labels,
		'exclude_from_search' => true,
		'public' => true,
		'has_archive' => false,
		'menu_icon' => get_stylesheet_directory_uri() . '/library/images/admin-icons/webinars.png',
		'rewrite' => false,
		'supports' => array('title', 'editor', 'thumbnail', 'page-attributes'),
		'taxonomies' => array('category')
	) );
}
add_action( 'init', 'pmdweb_post_type', 0 );



/**
 * Register `CPD Webinar` post type
 */
function cpdweb_post_type() {
   
   // Labels
	$labels = array(
		'name' => _x("CPD Webinars", "post type general name"),
		'singular_name' => _x("CPD Webinar", "post type singular name"),
		'menu_name' => 'CPD Webinars',
		'add_new' => _x("Add New", "CPD Webinar"),
		'add_new_item' => __("Add New CPD Webinar"),
		'edit_item' => __("Edit Profile"),
		'new_item' => __("New Profile"),
		'view_item' => __("View Profile"),
		'search_items' => __("Search Profiles"),
		'not_found' =>  __("No Profiles Found"),
		'not_found_in_trash' => __("No Profiles Found in Trash"),
		'parent_item_colon' => ''
	);
	
	// Register post type
	register_post_type('cpdwebinars' , array(
		'labels' => $labels,
		'exclude_from_search' => true,
		'public' => true,
		'has_archive' => false,
		'menu_icon' => get_stylesheet_directory_uri() . '/library/images/admin-icons/webinars.png',
		'rewrite' => false,
		'supports' => array('title', 'editor', 'thumbnail', 'page-attributes'),
		'taxonomies' => array('category')
	) );
}
add_action( 'init', 'cpdweb_post_type', 0 );


/**
 * Register `audio files` post type
 */
function audio_post_type() {
   
   // Labels
	$labels = array(
		'name' => _x("Audio Files", "post type general name"),
		'singular_name' => _x("Audio File", "post type singular name"),
		'menu_name' => 'Audio Files',
		'add_new' => _x("Add New", "Audio File"),
		'add_new_item' => __("Add New File"),
		'edit_item' => __("Edit Profile"),
		'new_item' => __("New Profile"),
		'view_item' => __("View Profile"),
		'search_items' => __("Search Profiles"),
		'not_found' =>  __("No Profiles Found"),
		'not_found_in_trash' => __("No Profiles Found in Trash"),
		'parent_item_colon' => ''
	);
	
	// Register post type
	register_post_type('audio' , array(
		'labels' => $labels,
				'exclude_from_search' => true,
		'public' => true,
		'has_archive' => false,
		'menu_icon' => get_stylesheet_directory_uri() . '/library/images/admin-icons/audio.png',
		'rewrite' => false,
		'supports' => array('title', 'editor', 'thumbnail', 'page-attributes')
	) );
}
add_action( 'init', 'audio_post_type', 0 );




/**
 * Register `team` post type
 */
function team_post_type() {
   
   // Labels
	$labels = array(
		'name' => _x("People", "post type general name"),
		'singular_name' => _x("Team", "post type singular name"),
		'menu_name' => 'People',
		'add_new' => _x("Add New", "team item"),
		'add_new_item' => __("Add New Profile"),
		'edit_item' => __("Edit Profile"),
		'new_item' => __("New Profile"),
		'view_item' => __("View Profile"),
		'search_items' => __("Search Profiles"),
		'not_found' =>  __("No Profiles Found"),
		'not_found_in_trash' => __("No Profiles Found in Trash"),
		'parent_item_colon' => ''
	);
	
	// Register post type
	register_post_type('team' , array(
		'labels' => $labels,
		'public' => true,
		'exclude_from_search' => true,
		'has_archive' => false,
		'menu_icon' => get_stylesheet_directory_uri() . '/library/images/admin-icons/team.png',
		'rewrite' => false,
		'supports' => array('title', 'editor', 'thumbnail', 'page-attributes')
	) );
}
add_action( 'init', 'team_post_type', 0 );

function exhibitor_post_type() {
   
   // Labels
	$labels = array(
		'name' => _x("Exhibitors", "post type general name"),
		'singular_name' => _x("Exhibitor", "post type singular name"),
		'menu_name' => 'Exhibitors',
		'add_new' => _x("Add New", "exhibitor"),
		'add_new_item' => __("Add New Exhibitor Profile"),
		'edit_item' => __("Edit Exhibitor Profile"),
		'new_item' => __("New Exhibitor Profile"),
		'view_item' => __("View Exhibitor Profile"),
		'search_items' => __("Search Exhibitor Profiles"),
		'not_found' =>  __("No Exhibitor Profiles Found"),
		'not_found_in_trash' => __("No Exhibitor Profiles Found in Trash"),
		'parent_item_colon' => ''
	);
	
	// Register post type
	register_post_type('exhibitors' , array(
		'labels' => $labels,
		'exclude_from_search' => true,
		'public' => true,
		'has_archive' => false,
		'menu_icon' => get_stylesheet_directory_uri() . '/library/images/admin-icons/exhibitors.png',
		'rewrite' => false,
		'supports' => array('title', 'editor', 'thumbnail', 'page-attributes')
	) );
}
add_action( 'init', 'exhibitor_post_type', 0 );


/**
 * Register `Offices` post type
 */
function office_post_type() {
   
   // Labels
	$labels = array(
		'name' => _x("Offices", "post type general name"),
		'singular_name' => _x("Office", "post type singular name"),
		'menu_name' => 'Offices',
		'add_new' => _x("Add New", "Office"),
		'add_new_item' => __("Add New Office"),
		'edit_item' => __("Edit Office"),
		'new_item' => __("New Office"),
		'view_item' => __("View Office Details"),
		'search_items' => __("Search Offices"),
		'not_found' =>  __("No Offices Found"),
		'not_found_in_trash' => __("No Offices Found in Trash"),
		'parent_item_colon' => ''
	);
	
	// Register post type
	register_post_type('offices' , array(
		'labels' => $labels,
		'public' => true,
		'exclude_from_search' => true,
		'has_archive' => false,
		'menu_icon' => get_stylesheet_directory_uri() . '/library/images/admin-icons/office.png',
		'rewrite' => true,
		'supports' => array('title', 'editor', 'thumbnail')
	) );
}
add_action( 'init', 'office_post_type', 0 );


// Register Custom Post Type
function testimonials_post_type() {

	$labels = array(
		'name'                => _x( 'Testimonials', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'Testimonial', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Testimonials', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Testimonial:', 'text_domain' ),
		'all_items'           => __( 'All Testimonials', 'text_domain' ),
		'view_item'           => __( 'View Testimonial', 'text_domain' ),
		'add_new_item'        => __( 'Add New Testimonial', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'edit_item'           => __( 'Edit Testimonial', 'text_domain' ),
		'update_item'         => __( 'Update Testimonial', 'text_domain' ),
		'search_items'        => __( 'Search Testimonials', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$rewrite = array(
		'slug'                => 'testimonials',
		'with_front'          => true,
		'pages'               => true,
		'feeds'               => true,
	);
	$args = array(
		'label'               => __( 'testimonials', 'text_domain' ),
		'description'         => __( 'Testimonials from past customers', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'page-attributes', ),
		'taxonomies'          => array( 'division', 'post_tag' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => false,
		'menu_position'       => 5,
		'menu_icon'           => get_stylesheet_directory_uri() . '/library/images/admin-icons/testimonials.png',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'page',
	);
	register_post_type( 'testimonials', $args );

}

// Hook into the 'init' action
add_action( 'init', 'testimonials_post_type', 0 );

// Remove the wysiwyg on certain post types to discourage text styling
add_filter('user_can_richedit', 'disable_wyswyg_for_custom_post_type');
function disable_wyswyg_for_custom_post_type( $default ){
  if( get_post_type() === 'testimonials') return false;
  return $default;
}


// Redirect single testimonials to home as we don't want single pages for these
add_action( 'template_redirect', 'wpse_128636_redirect_post' );

function wpse_128636_redirect_post() {
  $queried_post_type = get_query_var('post_type');
  if ( is_single() && 'testimonials' ==  $queried_post_type ) {
    wp_redirect( home_url(), 301 );
    exit;
  }
}
