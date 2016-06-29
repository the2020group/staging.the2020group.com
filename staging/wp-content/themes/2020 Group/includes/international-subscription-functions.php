<?php

// Register Custom Taxonomy
function directory_location() {

    $labels = array(
        'name'                       => _x( 'location', 'Taxonomy General Name', 'text_domain' ),
        'singular_name'              => _x( 'Location', 'Taxonomy Singular Name', 'text_domain' ),
        'menu_name'                  => __( 'Location', 'text_domain' ),
        'all_items'                  => __( 'All Locations', 'text_domain' ),
        'parent_item'                => __( 'Parent Location', 'text_domain' ),
        'parent_item_colon'          => __( 'Parent Location:', 'text_domain' ),
        'new_item_name'              => __( 'New Location', 'text_domain' ),
        'add_new_item'               => __( 'Add New Location', 'text_domain' ),
        'edit_item'                  => __( 'Edit Location', 'text_domain' ),
        'update_item'                => __( 'Update Location', 'text_domain' ),
        'separate_items_with_commas' => __( 'Separate locations with commas', 'text_domain' ),
        'search_items'               => __( 'Search Locations', 'text_domain' ),
        'add_or_remove_items'        => __( 'Add or remove Locations', 'text_domain' ),
        'choose_from_most_used'      => __( 'Choose from the most used Locations', 'text_domain' ),
        'not_found'                  => __( 'Not Found', 'text_domain' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'rewrite'                    => array(
          'slug'                      => 'directory/location',
          'with_front'                => false
        )
    );
    register_taxonomy( 'location', array( 'directory' ), $args );

}
// Hook into the 'init' action
add_action( 'init', 'directory_location', 0 );


// Register Custom Taxonomy
function directory_category() {

    $labels = array(
        'name'                       => _x( 'directory_cat', 'Taxonomy General Name', 'text_domain' ),
        'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'text_domain' ),
        'menu_name'                  => __( 'Category', 'text_domain' ),
        'all_items'                  => __( 'All Categories', 'text_domain' ),
        'parent_item'                => __( 'Parent Category', 'text_domain' ),
        'parent_item_colon'          => __( 'Parent Category:', 'text_domain' ),
        'new_item_name'              => __( 'New Category', 'text_domain' ),
        'add_new_item'               => __( 'Add New Category', 'text_domain' ),
        'edit_item'                  => __( 'Edit Category', 'text_domain' ),
        'update_item'                => __( 'Update Category', 'text_domain' ),
        'separate_items_with_commas' => __( 'Separate Categories with commas', 'text_domain' ),
        'search_items'               => __( 'Search Categories', 'text_domain' ),
        'add_or_remove_items'        => __( 'Add or remove Categories', 'text_domain' ),
        'choose_from_most_used'      => __( 'Choose from the most used Categories', 'text_domain' ),
        'not_found'                  => __( 'Not Found', 'text_domain' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true
    );
    register_taxonomy( 'directory_cat', array( 'directory' ), $args );

}
// Hook into the 'init' action
add_action( 'init', 'directory_category', 0 );


// Register Custom Post Type
function custom_directory_post_type() {

    $labels = array(
        'name'                => _x( 'Directory', 'Post Type General Name', 'text_domain' ),
        'singular_name'       => _x( 'Directory', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'           => __( 'Directory', 'text_domain' ),
        'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
        'all_items'           => __( 'All Items', 'text_domain' ),
        'view_item'           => __( 'View Item', 'text_domain' ),
        'add_new_item'        => __( 'Add New Item', 'text_domain' ),
        'add_new'             => __( 'Add New', 'text_domain' ),
        'edit_item'           => __( 'Edit Item', 'text_domain' ),
        'update_item'         => __( 'Update Item', 'text_domain' ),
        'search_items'        => __( 'Search Item', 'text_domain' ),
        'not_found'           => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
    );
    $args = array(
        'label'               => __( 'directory', 'text_domain' ),
        'description'         => __( 'directory for 2020 international memberships', 'text_domain' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'author', 'thumbnail', 'revisions', ),
        'taxonomies'          => array( 'location' ),
        'hierarchical'        => true,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
        'rewrite'                    => array(
          'slug'                      => 'directory',
          'with_front'                => false
        )
    );
    register_post_type( 'directory', $args );

}

// Hook into the 'init' action
add_action( 'init', 'custom_directory_post_type', 0 );



function get_countries() {
    return get_terms( 'location' , array ('hide_empty' => 0,'orderby'=>'ID'));
}

function get_dir_categories() {
    return get_terms( 'directory_cat' , array ('hide_empty' => 0,'orderby'=>'ID'));
}

