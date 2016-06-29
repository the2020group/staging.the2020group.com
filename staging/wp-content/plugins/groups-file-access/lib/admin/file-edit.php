<?php
/**
 * file-edit.php
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
 * Show edit file form.
 * @param int $file_id file id
 */
function gfa_admin_files_edit( $file_id ) {
	
	global $wpdb;
	
	if ( !current_user_can( GROUPS_ADMINISTER_GROUPS ) ) {
		wp_die( __( 'Access denied.', GFA_PLUGIN_DOMAIN ) );
	}
	
	$file_table = _groups_get_tablename( 'file' );
	$file_group_table = _groups_get_tablename( 'file_group' );
	$file_id = intval( $file_id );
	$file = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $file_table WHERE file_id = %d", $file_id ) );
	
	if ( empty( $file ) ) {
		wp_die( __( 'No such file.', GFA_PLUGIN_DOMAIN ) );
	}
	
	$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$current_url = remove_query_arg( 'action', $current_url );
	$current_url = remove_query_arg( 'file_id', $current_url );
	
	$name        = isset( $_POST['name-field'] ) ? wp_filter_nohtml_kses( $_POST['name-field'] ) : $file->name;
	$description = isset( $_POST['description-field'] ) ? $_POST['description-field'] : $file->description;
	$path        = $file->path;
	$max_count   = isset( $_POST['max-count-field'] ) ? intval( $_POST['max-count-field'] ) : $file->max_count;
	if ( $max_count < 0 ) {
		$max_count = 0;
	}
	$group_ids = array();
	if ( $_group_ids  = $wpdb->get_results( $wpdb->prepare( "SELECT group_id FROM $file_group_table WHERE file_id = %d", $file_id ) ) ) {
		foreach( $_group_ids as $group_id ) {
			$group_ids[] = $group_id->group_id;
		}
	}
	
	$base_url = get_bloginfo( 'url' );
	
	$output =
		'<div class="manage-files">' .
		'<div>' .
		'<h2>' .
		__( 'Edit a file', GFA_PLUGIN_DOMAIN ) .
		'</h2>' .
		'</div>' .
		
		'<p>' .
		sprintf( __( 'File Id: %d', GFA_PLUGIN_DOMAIN ), $file_id ) .
		'<p>' .
		'<p>' .
		sprintf( __( 'Path: %s', GFA_PLUGIN_DOMAIN ), $path ) .
		'<br/>' .
		sprintf( __( 'URL: %s', GFA_PLUGIN_DOMAIN ), GFA_File_Renderer::render_url( $file, $base_url ) ) .
		'<br/>' .
		sprintf( __( 'Link: %s', GFA_PLUGIN_DOMAIN ), GFA_File_Renderer::render_link( $file, $base_url ) ) .
		'</p>' .

		'<form enctype="multipart/form-data" id="add-file" method="post" action="' . $current_url . '">' .
		
		'<div class="file edit">' .
		'<input id="file-id-field" name="file-id-field" type="hidden" value="' . esc_attr( intval( $file_id ) ) . '"/>';

	$output .=
		'<p>' .
		__( 'The current file can be replaced by a new file. You can select a new file below.', GFA_PLUGIN_DOMAIN ) .
		'</p>' .
		
		// allow to upload a new file
		'<div class="field">' .
		'<label for="file" class="field-label">' .__( 'File', GFA_PLUGIN_DOMAIN ) . '</label>' .
		'<input id="file-field" name="file" class="filefield" type="file" />' .
		'<span class="description">' . __( 'If a new file is chosen here, the current file will be <strong>deleted</strong>.', GFA_PLUGIN_DOMAIN ) . '</span>' .
		'</div>';

	$output .=
		'<div class="field">' .
		'<label for="name-field" class="field-label first required">' .__( 'Name', GFA_PLUGIN_DOMAIN ) . '</label>' .
		'<input id="name-field" name="name-field" class="namefield" type="text" value="' . esc_attr( stripslashes( $name ) ) . '"/>' .
		'</div>' .

		'<div class="field">' .
		'<label for="description-field" class="field-label description-field">' .__( 'Description', GFA_PLUGIN_DOMAIN ) . '</label>' .
		'<textarea id="description-field" name="description-field" class="descriptionfield" rows="5" cols="45">' . htmlentities( stripslashes( $description ), ENT_COMPAT, get_bloginfo( 'charset' ) ) . '</textarea>' .
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
		wp_nonce_field( 'files-edit', GROUPS_ADMIN_GROUPS_NONCE, true, false ) .
		'<input class="button" type="submit" value="' . __( 'Save', GFA_PLUGIN_DOMAIN ) . '"/>' .
		'<input type="hidden" value="edit" name="action"/>' .
		'<a class="cancel" href="' . $current_url . '">' . __( 'Cancel', GFA_PLUGIN_DOMAIN ) . '</a>' .
		'</div>' .
		'</div>' . // .file.edit
		'</form>' .
		'</div>'; // .manage-files

	require_once( GFA_VIEWS_LIB . '/class-gfa-help.php' );
	$output .= GFA_Help::footer();

	echo $output;
} // function

/**
 * Handle edit form submission.
 */
function gfa_admin_files_edit_submit() {
	
	require_once( GFA_FILE_LIB . '/class-gfa-file-upload.php' );
	
	global $wpdb;
	
	if ( !current_user_can( GROUPS_ADMINISTER_GROUPS ) ) {
		wp_die( __( 'Access denied.', GFA_PLUGIN_DOMAIN ) );
	}
	if ( !wp_verify_nonce( $_POST[GROUPS_ADMIN_GROUPS_NONCE],  'files-edit' ) ) {
		wp_die( __( 'Access denied.', GFA_PLUGIN_DOMAIN ) );
	}
	
	$file_id = isset( $_POST['file-id-field'] ) ? $_POST['file-id-field'] : null;
	$file_id = intval( $file_id );
	$file_table = _groups_get_tablename( 'file' );
	$file = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $file_table WHERE file_id = %d", $file_id ) );
	if ( empty( $file ) ) {
		wp_die( __( 'No such file.', GFA_PLUGIN_DOMAIN ) );
	}
	$errors = 0;
	$new_path = null;
	if ( !empty( $_FILES['file']['tmp_name'] ) ) {
		if ( file_exists( GFA_UPLOADS_DIR ) ) {
			if ( $_FILES['file']['error'] == UPLOAD_ERR_OK ) {
				$tmp_name = $_FILES['file']['tmp_name'];
				$filename = GFA_File_Upload::filename_filter( $_FILES['file']['name'] );
				if ( strlen( $filename ) > 0 ) {
					$path = GFA_File_Upload::path_filter( GFA_UPLOADS_DIR . '/' . $filename );
					if ( ( $path !== $file->path ) && file_exists( $path ) ) {
						echo "<div class='error'>" . sprintf( __( 'The file %s already exists but it is not related to this entry. The existing file is not replaced and the current file for this entry is maintained.', GFA_PLUGIN_DOMAIN ), $path ) . "</div>";
						$errors++;
					} else {
						if ( file_exists( $file->path ) ) {
							@unlink( $file->path );
						}
						if ( !@move_uploaded_file( $tmp_name, $path ) ) {
							echo "<div class='error'>" . __( 'Could not upload the file.', GFA_PLUGIN_DOMAIN ) . "</div>";
							$errors++;
						} else {
							$new_path = $path;
						}
					}
				} else {
					echo "<div class='error'>" . __( 'The filename is not acceptable.', GFA_PLUGIN_DOMAIN ) . "</div>";
					$errors++;
				}
			}
		} else {
			echo "<div class='error'>" . __( 'The upload directory does not seem to exist. Please review the settings under File Access.', GFA_PLUGIN_DOMAIN ) . "</div>";
			$errors++;
		}
	}
	$path        = $file->path;
	$name        = isset( $_POST['name-field'] ) ? wp_filter_nohtml_kses( $_POST['name-field'] ) : '';
	$description = isset( $_POST['description-field'] ) ? $_POST['description-field'] : '';
	$max_count   = isset( $_POST['max-count-field'] ) ? intval( $_POST['max-count-field'] ) : 0;
	if ( $max_count < 0 ) {
		$max_count = 0;
	}
	if ( $new_path !== null ) {
		$path = $new_path;
	}
	$updated = $wpdb->query( $wpdb->prepare(
		"UPDATE $file_table SET name=%s, description=%s, path=%s, max_count=%d WHERE file_id=%d",
		$name,
		$description,
		$path,
		$max_count,
		$file_id
	) );
	if ( $updated !== false ) {
		do_action( "groups_updated_file", $file_id );

		$file_group_table  = _groups_get_tablename( 'file_group' );
		$new_group_ids     = !empty( $_POST['group_id'] ) ? $_POST['group_id'] : array();
		$current_group_ids = array();
		if ( $_group_ids  = $wpdb->get_results( $wpdb->prepare( "SELECT group_id FROM $file_group_table WHERE file_id = %d", $file_id ) ) ) {
			foreach( $_group_ids as $group_id ) {
				$current_group_ids[] = $group_id->group_id;
			}
		}
		foreach( $current_group_ids as $current_group_id ) {
			if ( !in_array( $current_group_id, $new_group_ids ) ) {
				if ( $wpdb->query( $wpdb->prepare( "DELETE FROM $file_group_table WHERE file_id = %d AND group_id = %d", $file_id, $current_group_id ) ) > 0 ) {
					do_action( "groups_deleted_file_group", $file_id, $current_group_id );
				}
			}
		}
		foreach( $new_group_ids as $new_group_id ) {
			if ( !in_array( $new_group_id, $current_group_ids ) ) {
				if ( $group = Groups_Group::read( $new_group_id ) ) {
					if ( $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO $file_group_table (file_id,group_id) VALUES (%d,%d)", $file_id, $new_group_id ) ) ) {
						do_action( "groups_created_file_group", $file_id, $new_group_id );
					}
				}
			}
		}
	} else {
		$errors++;
	}
	if ( $errors > 0 ) {
		return false;
	} else {
		return $file_id;
	}
} // function
