<?php

    // Register Custom Post Type
function register_cpt_devtools() {

    $labels = array(
        'name'                => _x( 'Tools', 'Post Type General Name', 'text_domain' ),
        'singular_name'       => _x( 'Tool', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'           => __( 'Tools', 'text_domain' ),
        'parent_item_colon'   => __( 'Parent Tool:', 'text_domain' ),
        'all_items'           => __( 'All Tools', 'text_domain' ),
        'view_item'           => __( 'View Tool', 'text_domain' ),
        'add_new_item'        => __( 'Add New Tool', 'text_domain' ),
        'add_new'             => __( 'Add New', 'text_domain' ),
        'edit_item'           => __( 'Edit Tool', 'text_domain' ),
        'update_item'         => __( 'Update Tool', 'text_domain' ),
        'search_items'        => __( 'Search Tools', 'text_domain' ),
        'not_found'           => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
    );
    $args = array(
        'label'               => __( 'devtools', 'text_domain' ),
        'description'         => __( 'Development Tools', 'text_domain' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', ),
        'taxonomies'          => array( 'tools_cat' ),
        'hierarchical'        => false,
        'public'              => true,
        'exclude_from_search' => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    register_post_type( 'devtools', $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_cpt_devtools', 0 );

// Register Custom Taxonomy
function register_cptax_devtools() {

    $labels = array(
        'name'                       => _x( 'Tool Categories', 'Taxonomy General Name', 'text_domain' ),
        'singular_name'              => _x( 'Tool Category', 'Taxonomy Singular Name', 'text_domain' ),
        'menu_name'                  => __( 'Tool Categories', 'text_domain' ),
        'all_items'                  => __( 'All Tool Categories', 'text_domain' ),
        'parent_item'                => __( 'Parent Tool Category', 'text_domain' ),
        'parent_item_colon'          => __( 'Parent Tool Category:', 'text_domain' ),
        'new_item_name'              => __( 'New Tool Category', 'text_domain' ),
        'add_new_item'               => __( 'Add New Tool Category', 'text_domain' ),
        'edit_item'                  => __( 'Edit Tool Category', 'text_domain' ),
        'update_item'                => __( 'Update Tool Category', 'text_domain' ),
        'separate_items_with_commas' => __( 'Separate Tool Categories with commas', 'text_domain' ),
        'search_items'               => __( 'Search Tool Categories', 'text_domain' ),
        'add_or_remove_items'        => __( 'Add or remove Tool Categories', 'text_domain' ),
        'choose_from_most_used'      => __( 'Choose from the most used Tool Categories', 'text_domain' ),
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
    );
    register_taxonomy( 'tools_cat', array( 'devtools' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'register_cptax_devtools', 0 );



