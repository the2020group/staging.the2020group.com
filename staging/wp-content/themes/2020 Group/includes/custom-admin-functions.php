<?php
  
add_filter( 'manage_edit-testimonials_columns', 'my_edit_testimonials_columns' ) ;

function my_edit_testimonials_columns( $columns ) {

	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'Name' ),
		'affiliation' => __( 'Affiliation' ),
		'division' => __( 'Divisions' ),
		'date' => __( 'Date' )
	);

	return $columns;
}



add_action( 'manage_testimonials_posts_custom_column', 'my_manage_testimonials_columns', 10, 2 );

function my_manage_testimonials_columns( $column, $post_id ) {
	global $post;

	switch( $column ) {

    /* If displaying the 'company' column. */
		case 'affiliation' :

			/* Get the genres for the post. */
			$terms = get_field( 'testimonial_byline', $post_id );

			/* If terms were found. */
			if ( !empty( $terms ) ) {
        echo $terms;
			}

			/* If no terms were found, output a default message. */
			else {
				_e( 'Not entered' );
			}

		break;
		

		/* If displaying the 'divisions' column. */
		case 'division' :

			/* Get the post meta. */
			$division = wp_get_post_terms($post_id, 'division', array("fields" => "all"));

			/* If no divisions are association, output a default message. */
			if ( empty( $division ) )
				echo __( 'None linked' );

			/* If there are divisions associated, list them */
			else

        foreach( $division as $singleDivision ) {
          echo $singleDivision->name . ', ';
        }

			break;

		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}