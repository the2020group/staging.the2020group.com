<?php

    // Register Custom Post Type
function register_cpt_newletter() {

    $labels = array(
        'name'                => _x( 'Newsletters', 'Post Type General Name', 'text_domain' ),
        'singular_name'       => _x( 'Newsletter', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'           => __( 'Newsletter', 'text_domain' ),
        'parent_item_colon'   => __( 'Parent Newsletters:', 'text_domain' ),
        'all_items'           => __( 'All Newsletters', 'text_domain' ),
        'view_item'           => __( 'View Newsletter', 'text_domain' ),
        'add_new_item'        => __( 'Add New Newsletter', 'text_domain' ),
        'add_new'             => __( 'Add New', 'text_domain' ),
        'edit_item'           => __( 'Edit Newsletter', 'text_domain' ),
        'update_item'         => __( 'Update Newsletter', 'text_domain' ),
        'search_items'        => __( 'Search Newsletter', 'text_domain' ),
        'not_found'           => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
    );
    $args = array(
        'label'               => __( 'newsletter', 'text_domain' ),
        'description'         => __( 'Newsletters', 'text_domain' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', ),
        'taxonomies'          => array( 'newscat' ),
        'hierarchical'        => false,
        'exclude_from_search' => true,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'menu_icon' 					=> get_stylesheet_directory_uri() . '/library/images/admin-icons/newsletters.png',
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    register_post_type( 'newsletter', $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_cpt_newletter', 0 );


// Register Custom Taxonomy
function register_cptax_newscat() {

    $labels = array(
        'name'                       => _x( 'Categories', 'Taxonomy General Name', 'text_domain' ),
        'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'text_domain' ),
        'menu_name'                  => __( 'Categories', 'text_domain' ),
        'all_items'                  => __( 'All Categories', 'text_domain' ),
        'parent_item'                => __( 'Parent Category', 'text_domain' ),
        'parent_item_colon'          => __( 'Parent Category:', 'text_domain' ),
        'new_item_name'              => __( 'New Category', 'text_domain' ),
        'add_new_item'               => __( 'Add New Category', 'text_domain' ),
        'edit_item'                  => __( 'Edit Category', 'text_domain' ),
        'update_item'                => __( 'Update Category', 'text_domain' ),
        'separate_items_with_commas' => __( 'Separate categories with commas', 'text_domain' ),
        'search_items'               => __( 'Search Categories', 'text_domain' ),
        'add_or_remove_items'        => __( 'Add or remove categories', 'text_domain' ),
        'choose_from_most_used'      => __( 'Choose from the most used Categories', 'text_domain' ),
        'not_found'                  => __( 'Not Found', 'text_domain' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'exclude_from_search'        => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy( 'newscat', array( 'newsletter' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_cptax_newscat', 0 );