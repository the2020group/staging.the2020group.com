<?php
/**
 * file-add.php
 * 
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 * 
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 * 
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * This header and all notices must be kept intact.
 * 
 * @author Karim Rahimpur
 * @package groups-file-access
 * @since groups-file-access 1.0.0
 */

/**
 * Show add group form.
 */
function gfa_admin_files_add() {

	global $wpdb;

	if ( !current_user_can( GROUPS_ADMINISTER_GROUPS ) ) {
		wp_die( __( 'Access denied.', GFA_PLUGIN_DOMAIN ) );
	}

	$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$current_url = remove_query_arg( 'paged', $current_url );
	$current_url = remove_query_arg( 'action', $current_url );
	$current_url = remove_query_arg( 'file_id', $current_url );
	
	$name		 = isset( $_POST['name-field'] ) ? wp_filter_nohtml_kses( $_POST['name-field'] ) : '';
	$description = isset( $_POST['description-field'] ) ? $_POST['description-field'] : '';
	$max_count   = isset( $_POST['max-count-field'] ) ? intval( $_POST['max-count-field'] ) : 0;
	if ( $max_count < 0 ) {
		$max_count = 0;
	}
	$group_ids   = isset( $_POST['group_id'] ) ? $_POST['group_id'] : array();

	require_once( GFA_FILE_LIB . '/class-gfa-file-upload.php' );
	$max_bytes = GFA_File_Upload::get_upload_limit();
	$output =
		'<div class="manage-files">' .

		'<h2>' . __( 'Add a new file', GFA_PLUGIN_DOMAIN ) . '</h2>' .

		'<p>' . sprintf( __( 'You can upload files up to <strong>%s</strong>.', GFA_PLUGIN_DOMAIN ), GFA_File_Upload::human_bytes( $max_bytes ) ) . '</p>' .

		'<form enctype="multipart/form-data" id="add-file" method="post" action="' . $current_url . '">' .

		'<div class="file new">' .

		'<div class="field">' .
		'<label for="file" class="field-label first required">' .__( 'File', GFA_PLUGIN_DOMAIN ) . '</label>' .
		'<input id="file-field" name="file" class="filefield" type="file" />' .
		'</div>' .

		'<div class="field">' .
		'<label for="name-field" class="field-label">' .__( 'Name', GFA_PLUGIN_DOMAIN ) . '</label>' .
		'<input id="name-field" name="name-field" class="namefield" type="text" value="' . esc_attr( $name ) . '"/>' .
		'<span class="description">' . __( 'A descriptive name for the file. If left empty, the filename will be used.', GFA_PLUGIN_DOMAIN ) . '</span>' .
		'</div>' .

		'<div class="field">' .
		'<label for="description-field" class="field-label description-field">' .__( 'Description', GFA_PLUGIN_DOMAIN ) . '</label>' .
		'<textarea id="description-field" name="description-field" class="descriptionfield" rows="5" cols="45">' . htmlentities( stripslashes( $description ), ENT_COMPAT, get_bloginfo( 'charset' ) ) . '</textarea>' .
		'<span class="description">' . __( 'A detailed description of the file.', GFA_PLUGIN_DOMAIN ) . '</span>' .
		'</div>' .

		'<div class="field">' .
		'<label for="max-count-field" class="field-label">' .__( 'Max #', GFA_PLUGIN_DOMAIN ) . '</label>' .
		'<input id="max-count-field" name="max-count-field" class="maxcountfield" type="text" value="' . esc_attr( $max_count ) . '"/>' .
		'<span class="description">' . __( 'The maximum number of allowed accesses to the file per user. Use 0 for unlimited accesses.', GFA_PLUGIN_DOMAIN ) . '</span>' .
		'</div>';

	$group_table = _groups_get_tablename( "group" );
	$groups = $wpdb->get_results( "SELECT * FROM $group_table ORDER BY name" );

	$output .= '<div class="field">';
	$output .= '<fieldset name="groups">';
	$output .= '<legend>';
	$output .= __( 'Groups', GFA_PLUGIN_DOMAIN );
	$output .= '</legend>';
	if ( count( $groups ) > 0 ) {
		$output .= '<ul>';
		foreach( $groups as $group ) {
			$output .= '<li>';
			$output .= '<label>';
			$output .= sprintf( '<input type="checkbox" name="group_id[]" value="%d" %s />', $group->group_id, in_array( $group->group_id, $group_ids ) ? ' checked="checked" ' : '' );
			$output .= ' ';
			$output .= wp_filter_nohtml_kses( $group->name );
			$output .= '</label>';
			$output .= '</li>';
		}
		$output .= '</ul>';
	} else {
		$output .= __( 'There are no groups. At least one group must exist.', GFA_PLUGIN_DOMAIN );
	}
	$output .= '</fieldset>';
	$output .= '<span class="description">' . __( 'Access to the file is restricted to members of the selected groups.', GFA_PLUGIN_DOMAIN ) . '</span>';
	$output .= '</div>';

	$output .=
		'<div class="field">' .
		wp_nonce_field( 'files-add', GROUPS_ADMIN_GROUPS_NONCE, true, false ) .
		'<input class="button" type="submit" value="' . __( 'Add', GFA_PLUGIN_DOMAIN ) . '"/>' .
		'<input type="hidden" value="add" name="action"/>' .
		'<a class="cancel" href="' . $current_url . '">' . __( 'Cancel', GFA_PLUGIN_DOMAIN ) . '</a>' .
		'</div>' .
		'</div>' . // .group.new
		'</form>' .
		'</div>'; // .manage-files

	require_once( GFA_VIEWS_LIB . '/class-gfa-help.php' );
	$output .= GFA_Help::footer();

	echo $output;
} // function gfa_admin_files_add

/**
 * Handle add file form submission.
 * @return int new file's id or false if unsuccessful
 */
function gfa_admin_files_add_submit() {

	require_once( GFA_FILE_LIB . '/class-gfa-file-upload.php' );

	global $wpdb;
	$file_id = false;
	
	if ( !current_user_can( GROUPS_ADMINISTER_GROUPS ) ) {
		wp_die( __( 'Access denied.', GFA_PLUGIN_DOMAIN ) );
	}
	
	if ( !wp_verify_nonce( $_POST[GROUPS_ADMIN_GROUPS_NONCE], 'files-add' ) ) {
		wp_die( __( 'Access denied.', GFA_PLUGIN_DOMAIN ) );
	}

	if ( file_exists( GFA_UPLOADS_DIR ) ) {
		if ( isset( $_FILES['file'] ) ) {
			if ( $_FILES['file']['error'] == UPLOAD_ERR_OK ) {
				$tmp_name = $_FILES['file']['tmp_name'];
				$filename = GFA_File_Upload::filename_filter( $_FILES['file']['name'] );
				if ( strlen( $filename ) > 0 ) {
					$path = GFA_File_Upload::path_filter( GFA_UPLOADS_DIR . '/' . $filename );
					if ( file_exists( $path ) ) {
						echo "<div class='error'>" . sprintf( __( 'The file %s already exists.', GFA_PLUGIN_DOMAIN ), $path ) . "</div>";
					} else {
						if ( !@move_uploaded_file( $tmp_name, $path ) ) { 
							echo "<div class='error'>" . __( 'Could not upload the file.', GFA_PLUGIN_DOMAIN ) . "</div>";
						} else {
							$name		 = !empty( $_POST['name-field'] ) ? wp_filter_nohtml_kses( $_POST['name-field'] ) : $filename;
							$description = isset( $_POST['description-field'] ) ? $_POST['description-field'] : '';
							$max_count   = isset( $_POST['max-count-field'] ) ? intval( $_POST['max-count-field'] ) : 0;
							if ( $max_count < 0 ) {
								$max_count = 0;
							}
							$file_table = _groups_get_tablename( 'file' );
							$inserted = $wpdb->query( $wpdb->prepare(
								"INSERT INTO $file_table (name,description,path,max_count) VALUES (%s,%s,%s,%d)",
								$name,
								$description,
								$path,
								$max_count
							) );
							if ( $inserted !== false ) {
								if ( $file_id = $wpdb->get_var( "SELECT LAST_INSERT_ID()" ) ) {
									do_action( "groups_created_file", $file_id );
									if ( !empty( $_POST['group_id'] ) ) {
										$file_group_table = _groups_get_tablename( 'file_group' );
										foreach( $_POST['group_id'] as $group_id ) {
											if ( $group = Groups_Group::read( $group_id ) ) {
												if ( $wpdb->query( $wpdb->prepare( "INSERT INTO $file_group_table (file_id,group_id) VALUES (%d,%d)", $file_id, $group_id ) ) ) {
													do_action( "groups_created_file_group", $file_id, $group_id );
												}
											}
										}
									}
								}
							}
						}
					}
				} else { 
					echo "<div class='error'>" . __( 'The filename is not acceptable.', GFA_PLUGIN_DOMAIN ) . "</div>";
				}
			}
		} else {
			echo "<div class='error'>" . __( 'You must upload a file.', GFA_PLUGIN_DOMAIN ) . "</div>";
		}
	} else {
		echo "<div class='error'>" . __( 'The upload directory does not seem to exist. Please review the settings under File Access.', GFA_PLUGIN_DOMAIN ) . "</div>";
	}
	return $file_id;
} // function gfa_admin_files_add_submit
