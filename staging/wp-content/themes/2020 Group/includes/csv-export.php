<?php

function twenty_csv_export() {
	if ( ! is_super_admin() ) {
		return;
	}

	if ( ! isset( $_GET['twenty_csv_export'] ) ) {
		return;
	}

	$filename = 'twenty-members-' . time() . '.csv';

	$header_row = array(
		0 => 'First Name',
		1 => 'Last Name',
		2 => 'Email',
		3 => 'Company',
		4 => 'Account Type',
		5 => 'Registration Date',
	);

	$data_rows = array();

	global $wpdb, $bp;
	$users = $wpdb->get_results( "SELECT ID, user_email, user_registered FROM {$wpdb->users} WHERE user_status = 0" );

	foreach ( $users as $u ) {

		//var_dump($u);
		$user_info = get_userdata($u);
		$row = array();
		$row[0] = get_user_meta($u->ID, 'first_name', true);
		$row[1] = get_user_meta($u->ID, 'last_name', true);
		$row[2] = $u->user_email;
		$row[3] = get_user_meta($u->ID, 'billing_company', true);
		$row[4] = get_user_meta($u->ID, '2020_account_type', true);
		$row[5] = $u->user_registered;

		$data_rows[] = $row;
	}

	$fh = @fopen( 'php://output', 'w' );

	fprintf( $fh, chr(0xEF) . chr(0xBB) . chr(0xBF) );

	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Content-Description: File Transfer' );
	header( 'Content-type: text/csv' );
	header( "Content-Disposition: attachment; filename={$filename}" );
	header( 'Expires: 0' );
	header( 'Pragma: public' );

	fputcsv( $fh, $header_row );

	foreach ( $data_rows as $data_row ) {
		fputcsv( $fh, $data_row );
	}

	fclose( $fh );
	die();
}

function attendees_csv_export() {
	if ( ! is_super_admin() ) {
		return;
	}

	if ( ! isset( $_GET['attendees_csv_export'] ) ) {
		return;
	}

	if ( ! isset( $_GET['event'] ) ) {
		return;
	}

	$event = $_GET['event'];



	$filename = 'attendees-' . time() . '.csv';

	$header_row = array(
		0 => 'First Name',
		1 => 'Last Name',
		2 => 'Email',
		3 => 'Company',
		4 => 'Account Type',
		5 => 'Infusionsoft Company ID',
		6 => 'Infusionsoft User ID',
	);

	$data_rows = array();

	global $wpdb, $bp;
	//$users = $wpdb->get_results( "SELECT ID, user_email, user_registered FROM {$wpdb->users} WHERE user_status = 0" );
	$users = $wpdb->get_results( $wpdb->prepare("SELECT u.ID, u.user_email AS user_email, m1.meta_value AS first_name, m2.meta_value AS last_name FROM wp_users u INNER JOIN wp_usermeta m ON m.user_id = u.ID INNER JOIN wp_usermeta m1 ON m1.user_id = u.ID AND m1.meta_key = 'first_name' INNER JOIN wp_usermeta m2 ON m2.user_id = u.ID AND m2.meta_key = 'last_name' WHERE m.meta_key = 'event_%d'", $event ));

	foreach ( $users as $u ) {

		//var_dump($u);
		$user_info = get_userdata($u);
		$row = array();
		$row[0] = get_user_meta($u->ID, 'first_name', true);
		$row[1] = get_user_meta($u->ID, 'last_name', true);
		$row[2] = $u->user_email;
		$row[3] = get_user_meta($u->ID, 'billing_company', true);
		$row[4] = get_user_meta($u->ID, '2020_account_type', true);
		$row[5] = get_user_meta($u->ID, 'is_company_id', true);
		$row[5] = get_user_meta($u->ID, 'is_user_id', true);

		$data_rows[] = $row;
	}

	$fh = @fopen( 'php://output', 'w' );

	fprintf( $fh, chr(0xEF) . chr(0xBB) . chr(0xBF) );

	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Content-Description: File Transfer' );
	header( 'Content-type: text/csv' );
	header( "Content-Disposition: attachment; filename={$filename}" );
	header( 'Expires: 0' );
	header( 'Pragma: public' );

	fputcsv( $fh, $header_row );

	foreach ( $data_rows as $data_row ) {
		fputcsv( $fh, $data_row );
	}

	fclose( $fh );
	die();
}



function attendees_conf_csv_export() {
	if ( ! is_super_admin() ) {
		return;
	}

	if ( ! isset( $_GET['attendees_conf_csv_export'] ) ) {
		return;
	}

	if ( ! isset( $_GET['event'] ) ) {
		return;
	}

	$event = $_GET['event'];



	$filename = 'conference-attendees' . time() . '.csv';

	$header_row = array(
		0 => 'First Name',
		1 => 'Last Name',
		2 => 'Email',
		3 => 'Company',
		4 => 'Conference Package',
		5 => 'Account Type',
		6 => 'Infusionsoft Company ID',
		7 => 'Infusionsoft User ID',
	);

	$data_rows = array();

	global $wpdb, $bp;
	//$users = $wpdb->get_results( "SELECT ID, user_email, user_registered FROM {$wpdb->users} WHERE user_status = 0" );
	$users = $wpdb->get_results( $wpdb->prepare("SELECT u.ID, u.user_email AS user_email, m1.meta_value AS first_name, m2.meta_value AS last_name FROM wp_users u INNER JOIN wp_usermeta m ON m.user_id = u.ID INNER JOIN wp_usermeta m1 ON m1.user_id = u.ID AND m1.meta_key = 'first_name' INNER JOIN wp_usermeta m2 ON m2.user_id = u.ID AND m2.meta_key = 'last_name' WHERE m.meta_key = 'event_%d'", $event ));

	foreach ( $users as $u ) {

		//var_dump($u);
		$user_info = get_userdata($u);
		$row = array();
		$row[0] = get_user_meta($u->ID, 'first_name', true);
		$row[1] = get_user_meta($u->ID, 'last_name', true);
		$row[2] = $u->user_email;
		$row[3] = get_user_meta($u->ID, 'billing_company', true);
		$row[4] = get_user_meta($u->ID, 'event_'.$event.'_meal', true);
		$row[5] = get_user_meta($u->ID, '2020_account_type', true);
		$row[6] = get_user_meta($u->ID, 'is_company_id', true);
		$row[7] = get_user_meta($u->ID, 'is_user_id', true);

		$data_rows[] = $row;
	}

	$fh = @fopen( 'php://output', 'w' );

	fprintf( $fh, chr(0xEF) . chr(0xBB) . chr(0xBF) );

	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Content-Description: File Transfer' );
	header( 'Content-type: text/csv' );
	header( "Content-Disposition: attachment; filename={$filename}" );
	header( 'Expires: 0' );
	header( 'Pragma: public' );

	fputcsv( $fh, $header_row );

	foreach ( $data_rows as $data_row ) {
		fputcsv( $fh, $data_row );
	}

	fclose( $fh );
	die();
}

function cpd_log_export() {

	if ( ! isset( $_GET['cpd_log_export'] ) ) {
		return;
	}

	if ( ! isset( $_GET['uid'] ) ) {
		return;
	}

	$uid = $_GET['uid'];

	$filename = '2020-cpdlog-' . time() . '.csv';

	$header_row = array(
		0 => trim(utf8_decode('CPD Title')),
		1 => 'Summary',
		2 => 'Reflection',
		3 => 'Date'
	);

	$data_rows = array();

	global $wpdb, $bp;
	$posts = $wpdb->get_results( "SELECT ID, post_title, post_date, post_content, post_parent FROM {$wpdb->posts} WHERE post_author = $uid AND post_type = 'cpd_log'" );
	/*$users = $wpdb->get_results( $wpdb->prepare("SELECT u.ID, u.user_email AS user_email, m1.meta_value AS first_name, m2.meta_value AS last_name FROM wp_users u INNER JOIN wp_usermeta m ON m.user_id = u.ID INNER JOIN wp_usermeta m1 ON m1.user_id = u.ID AND m1.meta_key = 'first_name' INNER JOIN wp_usermeta m2 ON m2.user_id = u.ID AND m2.meta_key = 'last_name' WHERE m.meta_key = 'event_%d'", $event ));*/

	foreach ( $posts as $post ) {

		//var_dump($u);
		$parent = $post->post_parent;
		if ($parent==0) {
			$title = $post->post_title;
			$excerpt = 'No summary';
		} else {
			$excerpt = $parent.'---'.strip_tags(apply_filters('the_excerpt', get_post_field('post_content', $parent)));
			$title=get_the_title($parent);
		}
		//$user_info = get_userdata($u);
		$row = array();

		$row[0] = iconv("ISO-8859-5", "ISO-8859-5//IGNORE", $title);
		$row[1] = iconv("ISO-8859-5", "ISO-8859-5//IGNORE", $excerpt);

		$temp = str_replace('Ã¢Â€Â™','\'',$row[0]);


		$temp = str_replace('£','GBP',$temp);
		$temp = str_replace('€','EUR',$temp);

		$temp = str_replace('±','+',$temp);

		$temp = preg_replace('/[^\x20-\x7E]/', ' ', $temp);
		//$temp = html_entity_decode($temp);

		$row[0] = $temp;

		$temp = str_replace('Ã¢Â€Â™','\'',$row[1]);

		$temp = str_replace('£','GBP',$temp);
		$temp = str_replace('€','EUR',$temp);

		$temp = str_replace('±','+',$temp);
		$temp = preg_replace('/[^\x20-\x7E]/', ' ', $temp);
		//$temp = html_entity_decode($temp);

		$temp = str_replace('&#8217;','\'',$temp);
		$temp = str_replace('&#8242;','\'',$temp);


		$row[1] = $temp;


		$row[0] = strip_tags($row[0]);
		$row[1] = strip_tags($row[1]);

		if (trim($row[1])=='') {
			$row[0] = ($title);
			$row[1] = ($excerpt);
		}

		$row[0] = htmlspecialchars_decode($row[0]);
		$row[1] = htmlspecialchars_decode($row[1]);

		$row[0] = str_replace('&pound;',utf8_encode('£'),$row[0]);
		$row[0] = str_replace('&euro;',utf8_encode('Û'),$row[0]);

		$row[0] = str_replace('Ã‚Â','',$row[0]);

		$row[1] = str_replace('Ã‚Â','',$row[1]);



		$row[2] = ($post->post_content);

		$temp = str_replace('Ã¢Â€Â™','\'',$row[2]);
		$temp = str_replace('£','GBP',$temp);
		$temp = str_replace('€','EUR',$temp);

		$temp = str_replace('±','+',$temp);

		$temp = preg_replace('/[^\x20-\x7E]/', ' ', $temp);
		//$temp = html_entity_decode($temp);

		$row[2] = $temp;

		$row[3] = ($post->post_date);

		$data_rows[] = $row;
	}

	$fh = @fopen( 'php://output', 'w' );

	//fprintf( $fh, chr(0xEF) . chr(0xBB) . chr(0xBF) );

	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Content-Description: File Transfer' );
	header( 'Content-type: text/csv;charset=utf-8' );
	header( "Content-Disposition: attachment; filename={$filename}" );
	header( 'Expires: 0' );
	header( 'Pragma: public' );

	fputcsv( $fh, $header_row );


	foreach ( $data_rows as $data_row ) {
		$data_row = str_replace('&#8211;', '-',$data_row);
		$data_row = str_replace('&nbsp;', ' ',$data_row);

		fputcsv( $fh, $data_row );
	}


	fclose( $fh );
	die();
}

add_action( 'admin_init', 'twenty_csv_export' );
add_action( 'admin_init', 'attendees_csv_export' );
add_action( 'admin_init', 'attendees_conf_csv_export' );
add_action( 'init', 'cpd_log_export' );

/** Add export meta box to events
 **/
function add_events_metaboxes() {
	add_meta_box('webinars_csv_export', 'Export Attendees', 'twenty_events_html', 'product', 'side', 'default');
}

function twenty_events_html() {
	global $post;
	//$qualifies = get_field('content_qualifies_for_cpd_log_entry');

	if( has_term( array(10,27,28), 'product_cat' ) ) {
		echo '<a href="?attendees_csv_export&event='.$post->ID.'">Download Attendees (CSV)</a>';
	}
	elseif (has_term( 11, 'product_cat' ) ) {
		echo '<a href="?attendees_conf_csv_export&event='.$post->ID.'">Download Attendees (CSV)</a>';
	}
	else {
		echo 'Attendee export is only available on products within the <em>Webinar</em>, <em>Workshop</em>, <em>Conference</em> and <em>Focus Group</em> categories';
	}

}

add_action( 'add_meta_boxes', 'add_events_metaboxes' );