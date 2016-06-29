<?php
add_action( 'init', 'register_cpt_cpd_log' );

function register_cpt_cpd_log() {

    $labels = array(
        'name' => _x( 'CPD Logs', 'cpd_log' ),
        'singular_name' => _x( 'CPD Log', 'cpd_log' ),
        'add_new' => _x( 'Add New', 'cpd_log' ),
        'add_new_item' => _x( 'Add New CPD Log', 'cpd_log' ),
        'edit_item' => _x( 'Edit CPD Log', 'cpd_log' ),
        'new_item' => _x( 'New CPD Log', 'cpd_log' ),
        'view_item' => _x( 'View CPD Log', 'cpd_log' ),
        'search_items' => _x( 'Search CPD Logs', 'cpd_log' ),
        'not_found' => _x( 'No cpd logs found', 'cpd_log' ),
        'not_found_in_trash' => _x( 'No cpd logs found in Trash', 'cpd_log' ),
        'parent_item_colon' => _x( 'Parent CPD Log:', 'cpd_log' ),
        'menu_name' => _x( 'CPD Logs', 'cpd_log' ),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'supports' => array( 'title', 'author','editor', 'page-attributes','tags', 'excerpt' ),
        'taxonomies'          => array( 'cpd_log', ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => true,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'cpd_log', $args );



  $labels = array(
    'name'                       => _x( 'CPD Log Type', 'Taxonomy General Name', 'text_domain' ),
    'singular_name'              => _x( 'CPD Log Type', 'Taxonomy Singular Name', 'text_domain' ),
    'menu_name'                  => __( 'CPD Log Type', 'text_domain' ),
    'all_items'                  => __( 'All Items', 'text_domain' ),
    'parent_item'                => __( 'Parent Item', 'text_domain' ),
    'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
    'new_item_name'              => __( 'New Item Name', 'text_domain' ),
    'add_new_item'               => __( 'Add New Item', 'text_domain' ),
    'edit_item'                  => __( 'Edit Item', 'text_domain' ),
    'update_item'                => __( 'Update Item', 'text_domain' ),
    'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
    'search_items'               => __( 'Search Items', 'text_domain' ),
    'add_or_remove_items'        => __( 'Add or remove items', 'text_domain' ),
    'choose_from_most_used'      => __( 'Choose from the most used items', 'text_domain' ),
    'not_found'                  => __( 'Not Found', 'text_domain' ),
  );
  $args = array(
    'labels'                     => $labels,
    'hierarchical'               => false,
    'public'                     => true,
    'show_ui'                    => true,
    'show_admin_column'          => true,
    'show_in_nav_menus'          => true,
    'show_tagcloud'              => true,
  );
  register_taxonomy( 'cpd_log', array( 'cpd_log' ), $args );


}

//
// CPD Log functions
//

function add_cpd_log($data,$user_id=0) {

  // if user id hasn't been provided get current user
  if ($user_id == 0) {
    $user_id = get_current_user_id();
  }

  // if date hasn't been provided use current date
  if (!isset($data['date'])) {
    $data['date'] = date('Y-m-d H:i:s');
  }

  $data['reflection'] = '';

  $args = array (
    'author' => $user_id,
    'post_parent' => $data['ID'],
    'post_type'   => 'cpd_log'
  );

  query_posts($args);

  //
  if (!have_posts()) {

    // add cpd entry
    $post_id = wp_insert_post(
      array(
        'comment_status' => 'closed',
        'ping_status'    => 'closed',
        'post_author'    => $user_id,
        'post_title'     => 'CPD Entry',
        'post_status'    => 'publish',
        'post_type'      => 'cpd_log',
        'post_parent'    => $data['ID'],
        'tags_input'     => $data['type'],
        'post_date'      => $data['date'],
      )
    );

      $my_post = array(
      'ID'           => $post_id,
      'post_status' => 'publish'
    );

    // Update the post into the database
    wp_update_post( $my_post );

    return $post_id;

  }

  return 0;

}

function update_cpd_log($data) {

}

add_action('wp_ajax_delete_cpd_log','delete_cpd_log');
function delete_cpd_log() {
  $userid    =  get_current_user_id();
  $cpd_entry =  (int)$_POST['cpd-log-id'];
  if ($userid > 0 && $cpd_entry > 0) {
    $org_post = get_post($cpd_entry,'ARRAY_A');

    if ($org_post['post_author']==$userid && $org_post['post_type']=='cpd_log') {

      if (!is_array(wp_trash_post( $cpd_entry ))) {
        echo 'false';
      }
      else {
        echo 'true';
      }

      exit;
    }
    else {
      //echo 'dd';
    }
  }
  echo 'false';
  exit;

}








