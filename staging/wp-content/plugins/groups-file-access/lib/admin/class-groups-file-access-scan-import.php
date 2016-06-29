<?php
/**
 * class-groups-file-access-scan-import.php
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
 * @since groups-file-access 1.2.0
 */

/**
 * Scan and import data facility.
 */
class Groups_File_Access_Scan_Import {

	const MAX_LINE_LENGTH   = 10384; // increased but not used since 1.3.1

	const FILENAME_INDEX    = 0;
	const FILE_ID_INDEX     = 1;
	const NAME_INDEX        = 2;
	const DESCRIPTION_INDEX = 3;
	const MAX_COUNT_INDEX   = 4;
	const GROUP_NAMES_INDEX = 5;

	const MAX_INVALID_LINES_SHOWN = 10;

	private static $admin_messages = array();

	/**
	 * Init hook to catch import file generation request.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'wp_init' ) );
		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
	}

	/**
	 * Prints admin notices.
	 */
	public static function admin_notices() {
		if ( !empty( self::$admin_messages ) ) {
			echo '<div style="padding:1em;margin:1em;border:1px solid #aa0;border-radius:4px;background-color:#ffe;color:#333;">';
			foreach ( self::$admin_messages as $msg ) {
				echo '<p>';
				echo $msg;
				echo '</p>';
			}
			echo '</div>';
		}
	}

	/**
	 * Catch and act on valid file action requests.
	 */
	public static function wp_init() {
		if ( isset( $_REQUEST['action'] ) ) {
			switch( $_REQUEST['action'] ) {
				case 'import_files' :
					if ( isset( $_REQUEST['gfa-import'] ) && wp_verify_nonce( $_REQUEST['gfa-import'], 'import' ) ) {
						self::import_files();
					}
					break;
				case 'export_files' :
					if ( isset( $_REQUEST['gfa-export'] ) && wp_verify_nonce( $_REQUEST['gfa-export'], 'export' ) ) {
						self::export_files();
					}
					break;
				case 'scan_files' :
					if ( isset( $_REQUEST['gfa-scan'] ) && wp_verify_nonce( $_REQUEST['gfa-scan'], 'scan' ) ) {
						self::scan_files();
					}
					break;
			}
		}
	}

	/**
	 * Renders the import section.
	 */
	public static function admin_import_files() {

		echo '<div class="manage-files">';

		//
		// Import files
		//

		echo '<div class="manage import">';
		echo '<form enctype="multipart/form-data" name="import-subscribers" method="post" action="">';
		echo '<div>';
		echo '<h2>' . __( 'Import Files', GFA_PLUGIN_DOMAIN ) . '</h2>';

		echo '<p>';
		printf( __( 'Here you can import file data in bulk from a text file, after uploading your files to <code>%s</code> via FTP.', GFA_PLUGIN_DOMAIN ), GFA_UPLOADS_DIR );
		echo '</p>';

		echo '<ul class="info">';
		echo '<li>';
		echo sprintf( __( '<strong>Adding new files in bulk</strong> : After uploading new files via FTP to the <code>%s</code> folder, use the <em>Scan</em> function below to automatically create an import file. Use the file obtained to import your uploaded files here.', GFA_PLUGIN_DOMAIN ), GFA_UPLOADS_DIR );
		echo '</li>';
		echo '<li>';
		echo __( '<strong>Modifying existing file entries in bulk</strong> : Use the <em>Export</em> function below to create an import file based on existing entries.', GFA_PLUGIN_DOMAIN );
		echo '</li>';
		echo '</ul>';

		echo '<p>';
		echo __( 'The accepted file-format is a plain text file with values separated by tabs provided on one line per file and in this order:', GFA_PLUGIN_DOMAIN );
		echo '</p>';
		echo '<p>';
		echo "<code>filename\tfile_id\tname\tdescription\tmax_count\tgroup_names</code>";
		echo '</p>';
		echo '<p>';
		echo __( 'Description of fields:', GFA_PLUGIN_DOMAIN );
		echo '</p>';
		echo '<ul>';
		echo '<li>';
		echo sprintf( __( '<code>filename</code> - <strong>required</strong> - The full filename of the file uploaded to the <code>%s</code> directory. Do not include the full path.', GFA_PLUGIN_DOMAIN ), GFA_UPLOADS_DIR );
		echo ' ';
		echo __( 'A line that refers to the <code>filename</code> of an existing entry will update the information related to the entry.', GFA_PLUGIN_DOMAIN );
		echo '</li>';
		echo '<li>';
		echo __( '<code>file_id</code> - <em>optional</em> - The <em>Id</em> of an existing file entry. If provided, the existing file will be <strong>deleted</strong> and the new file will be related to the entry.', GFA_PLUGIN_DOMAIN );
		echo '</li>';
		echo '<li>';
		echo __( '<code>name</code> - <em>optional</em> - A descriptive name for the file. If left empty, the filename will be used.', GFA_PLUGIN_DOMAIN );
		echo '</li>';
		echo '<li>';
		echo __( '<code>description</code> - <em>optional</em> - A detailed description of the file.', GFA_PLUGIN_DOMAIN );
		echo '</li>';
		echo '<li>';
		echo __( '<code>max_count</code> - <em>optional</em> - The maximum number of allowed accesses to the file per user. Leave empty or use 0 for unlimited accesses.', GFA_PLUGIN_DOMAIN );
		echo '</li>';
		echo '<li>';
		echo __( '<code>group_names</code> - <em>optional</em> - The names of the groups that are allowed to access the file, separated by comma. If empty, the file can not be accessed until a group is assigned.', GFA_PLUGIN_DOMAIN );
		echo '</li>';
		echo '</ul>';
		echo '<p>';
		printf( __( 'The files must have been uploaded to the <code>%s</code> directory before starting to import.', GFA_PLUGIN_DOMAIN ), GFA_UPLOADS_DIR );
		echo '</p>';

		echo '<p>';
		echo '<label>';
		echo sprintf( '<input type="checkbox" name="test_import" %s />', !empty( $_POST['test_import'] ) ? ' checked="checked" ' : '' );
		echo ' ';
		echo __( 'Test only', GFA_PLUGIN_DOMAIN );
		echo ' ';
		echo '<span class="description">';
		echo __( 'If checked, no changes will be made.', GFA_PLUGIN_DOMAIN );
		echo '</span>';
		echo '</label>';
		echo '</p>';

		echo '<p>';
		echo '<label>';
		echo sprintf( '<input type="checkbox" name="delete_files" %s />', !empty( $_POST['delete_files'] ) ? ' checked="checked" ' : '' );
		echo ' ';
		echo __( 'Delete replaced files', GFA_PLUGIN_DOMAIN );
		echo ' ';
		echo '<span class="description">';
		echo __( 'If checked, existing files that are replaced by new ones are deleted.', GFA_PLUGIN_DOMAIN );
		echo '</span>';
		echo '</label>';
		echo '</p>';

		wp_nonce_field( 'import', 'gfa-import', true, true );
		echo '<div class="buttons">';
		printf( '<input type="file" name="file" /> <input class="import button" type="submit" name="submit" value="%s" />', __( 'Import', GFA_PLUGIN_DOMAIN ) );
		echo '<input type="hidden" name="action" value="import_files" />';

		echo '</div>';
		echo '</div>';
		echo '</form>';
		echo '</div>';

		//
		// Export files
		//

		echo '<div class="manage">';
		echo '<form name="export-files" method="post" action="">';
		echo '<div>';
		echo '<h2>' . __( 'Export Files', GFA_PLUGIN_DOMAIN ) . '</h2>';
		echo '<p>';
		echo __( 'This will create a text file (in the supported import file-format) with current data for all files managed in the <strong>Groups > Files</strong> section.', GFA_PLUGIN_DOMAIN );
		echo '</p>';
		wp_nonce_field( 'export', 'gfa-export', true, true );
		echo '<div class="buttons">';
		printf( '<input class="export button" type="submit" name="submit" value="%s" />', __( 'Export', GFA_PLUGIN_DOMAIN ) );
		echo '<input type="hidden" name="action" value="export_files" />';
		echo '</div>';
		echo '</div>';
		echo '</form>';
		echo '</div>';

		//
		// Scan for files
		//

		echo '<div class="manage">';
		echo '<form name="scan-files" method="post" action="">';
		echo '<div>';
		echo '<h2>' . __( 'Scan for Files', GFA_PLUGIN_DOMAIN ) . '</h2>';
		echo '<p>';
		printf( __( 'This will create a text file (in the supported import file-format) with current data for all files in the <code>%s</code> folder.', GFA_PLUGIN_DOMAIN ), GFA_UPLOADS_DIR );
		echo '</p>';

		echo '<p>';
		echo '<label>';
		printf( '<input type="checkbox" name="exclude_existing" %s />', !empty( $_POST['exclude_existing'] ) || empty( $_POST['action'] ) || ( $_POST['action'] != 'scan_files' ) ? ' checked="checked" ' : '' );
		echo ' ';
		echo __( 'Exclude existing file entries', GFA_PLUGIN_DOMAIN );
		echo ' ';
		echo '<span class="description">';
		echo __( 'If checked, only new files that do not already have an existing entry are included.', GFA_PLUGIN_DOMAIN );
		echo '</span>';
		echo '</label>';
		echo '</p>';

		echo '<p>';
		echo __( 'Predefined fields: These can be left empty, otherwise the value will be used in common for all scanned files.', GFA_PLUGIN_DOMAIN );
		echo '</p>';

		echo '<p>';
		echo __( 'The names for file entries are automatically derived from their filename.', GFA_PLUGIN_DOMAIN );
		echo '</p>';

		echo '<p>';
		echo '<label>';
		echo __( 'Description', GFA_PLUGIN_DOMAIN );
		echo ' ';
		printf( '<input type="text" name="description" value="%s" />', !empty( $_POST['description'] ) ? esc_attr( trim( $_POST['description'] ) ) : '' );
		echo ' ';
		echo '<span class="description">';
		echo __( 'If indicated, the same description is used for all new files that are found.', GFA_PLUGIN_DOMAIN );
		echo '</span>';
		echo '</label>';
		echo '</p>';

		echo '<p>';
		echo '<label>';
		echo __( 'Max #', GFA_PLUGIN_DOMAIN );
		echo ' ';
		printf( '<input type="text" name="max_count" value="%s" />', !empty( $_POST['max_count'] ) ? max( array( 0, intval( trim( $_POST['max_count'] ) ) ) ) : '' );
		echo ' ';
		echo '<span class="description">';
		echo __( 'Maximum number of accesses per user. Unlimited when empty or 0.', GFA_PLUGIN_DOMAIN );
		echo '</span>';
		echo '</label>';
		echo '</p>';

		echo '<p>';
		echo '<label>';
		echo __( 'Groups', GFA_PLUGIN_DOMAIN );
		echo ' ';
		printf( '<input type="text" name="group_names" value="%s" />', !empty( $_POST['group_names'] ) ? esc_attr( trim( $_POST['group_names'] ) ) : '' );
		echo ' ';
		echo '<span class="description">';
		echo __( 'Indicate one or more names of groups that should have access to the files. Separate multiple names by comma. The groups do not need to exist now, but they must exist when the files are imported.', GFA_PLUGIN_DOMAIN );
		echo '</span>';
		echo '</label>';
		echo '</p>';

		wp_nonce_field( 'scan', 'gfa-scan', true, true );

		echo '<div class="buttons">';
		printf( '<input class="scan button" type="submit" name="submit" value="%s" />', __( 'Scan', GFA_PLUGIN_DOMAIN ) );
		echo '<input type="hidden" name="action" value="scan_files" />';
		echo '</div>';

		echo '</div>';
		echo '</form>';
		echo '</div>';

		echo '</div>';
	}

	/**
	 * Import data from uploaded file.
	 * 
	 * @return int number of records created
	 */
	public static function import_files() {

		global $wpdb;

		$charset = get_bloginfo( 'charset' );
		$now     = date( 'Y-m-d H:i:s', time() );

		$test_import = !empty( $_POST['test_import'] );
		$delete_files = !empty( $_POST['delete_files'] );

		if ( isset( $_FILES['file'] ) ) {
			if ( $_FILES['file']['error'] == UPLOAD_ERR_OK ) {
				$tmp_name = $_FILES['file']['tmp_name'];
				if ( file_exists( $tmp_name ) ) {
					if ( $h = @fopen( $tmp_name, 'r' ) ) {

						$imported           = 0;
						$entries_added      = 0;
						$entries_updated    = 0;
						$empty              = 0; // also comment lines (starting with ; or # )
						$invalid            = 0;
						$invalid_lines      = array();
						$invalid_line_messages = array();
						$line_number        = 0;
						$skipped_file       = 0;

						$group_table      = _groups_get_tablename( 'group' );
						$file_table       = _groups_get_tablename( 'file' );
						$file_group_table = _groups_get_tablename( 'file_group' );

						while ( $line = fgets( $h ) ) {

							$line_number++;
							$line = preg_replace( '/\r|\n/', '', $line );
							$data = explode( "\t", $line );
							
							$filename     = !empty( $data[self::FILENAME_INDEX] ) ? $data[self::FILENAME_INDEX] : null;
							$file_id      = !empty( $data[self::FILE_ID_INDEX] ) ? intval( $data[self::FILE_ID_INDEX] ) : null;
							$name         = !empty( $data[self::NAME_INDEX] ) ? wp_filter_nohtml_kses( $data[self::NAME_INDEX] ) : $filename;
							$description  = !empty( $data[self::DESCRIPTION_INDEX] ) ? $data[self::DESCRIPTION_INDEX] : '';
							$max_count    = !empty( $data[self::MAX_COUNT_INDEX] ) ? max( array( 0, intval( $data[self::MAX_COUNT_INDEX] ) ) ) : 0;
							$_group_names = !empty( $data[self::GROUP_NAMES_INDEX] ) ? explode( ',', $data[self::GROUP_NAMES_INDEX] ) : array(); 

							if ( ( strlen( $line ) > 0 ) && $line[0] !== ';' && $line[0] !== '#' ) {
								if ( !empty( $filename ) && file_exists( GFA_UPLOADS_DIR . '/' . $filename ) && is_file( GFA_UPLOADS_DIR . '/' . $filename ) ) {

									$path = GFA_UPLOADS_DIR . '/' . $filename;

									// file id
									if ( $file_id !== null ) {
										if ( $file_id !== intval( $wpdb->get_var( $wpdb->prepare( "SELECT file_id FROM $file_table WHERE file_id = %d", $file_id ) ) ) ) {
											$invalid++;
											$invalid_lines[] = $line_number;
											$invalid_line_messages[$line_number] = sprintf( __( 'Invalid file_id %d', GFA_PLUGIN_DOMAIN ), $file_id );
											continue;
										}
									}

									// group names
									$group_names = array();
									$invalid_group = false;
									foreach( $_group_names as $group_name ) {
										$group_name = wp_strip_all_tags( trim ( $group_name ) );
										if ( Groups_Group::read_by_name( $group_name ) ) {
											$group_names[] = $group_name;
										} else {
											$invalid++;
											$invalid_lines[] = $line_number;
											$invalid_line_messages[$line_number] = sprintf( __( 'Invalid group name %s', GFA_PLUGIN_DOMAIN ), $group_name );
											$invalid_group = true;
											break;
										}
									}
									if ( $invalid_group ) {
										continue;
									}

									$inserted = false;
									$updated  = false;

									// If no file id is given but an existing file entry is referenced, assign it, this file must not be deleted, only info updated. [*]
									$existing_file_id = null;
									if ( $file_id === null ) {
										if ( $existing_file = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $file_table WHERE path = %s", $path ) ) ) {
											if ( file_exists( $existing_file->path ) ) {
												$file_id = $existing_file->file_id;
												$existing_file_id = $file_id;
											}
										}
									} else { // also if the file id is given and the file does not change
										if ( $existing_file = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $file_table WHERE file_id = %d", $file_id ) ) ) {
											if ( file_exists( $existing_file->path ) && ( $existing_file->path == $path ) ) {
												$existing_file_id = $file_id;
											}
										}
									}

									if ( $file_id === null ) {
										if ( !$test_import ) {
											$inserted = $wpdb->query( $wpdb->prepare(
												"INSERT INTO $file_table (name,description,path,max_count) VALUES (%s,%s,%s,%d)",
												$name,
												$description,
												$path,
												$max_count
											) );
											if ( $inserted !== false ) {
												$entries_added++;
												if ( $file_id = $wpdb->get_var( "SELECT LAST_INSERT_ID()" ) ) {
													do_action( "groups_created_file", $file_id );
												}
											}
										}
									} else {
										if ( !$test_import ) {
											// See [*] above: don't delete an existing file entry which only needs to be updated.
											if ( $existing_file_id !== $file_id ) {
												$file = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $file_table WHERE file_id = %d", $file_id ) );
												if ( $delete_files && file_exists( $file->path ) ) {
													@unlink( $file->path );
												}
											}
											$updated = $wpdb->query( $wpdb->prepare(
												"UPDATE $file_table SET name = %s, description = %s, path = %s, max_count = %d WHERE file_id = %d",
												$name,
												$description,
												$path,
												$max_count,
												$file_id
											) );
											if ( $updated !== false ) {
												$entries_updated++;
												do_action( "groups_updated_file", $file_id );
											}
										}
									}

									// must use strict comparison, e.g. $updated can be 0 when no changes where made but we need to know if we should enter here
									if ( $inserted !== false || $updated !== false ) {

										if ( !$test_import ) {
											// remove group assignments that should no longer exist
											$current_file_groups = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $file_group_table fg LEFT JOIN $group_table g ON fg.group_id = g.group_id WHERE fg.file_id = %d", intval( $file_id ) ) );
											foreach ( $current_file_groups as $current_file_group ) {
												if ( !in_array( $current_file_group->name, $group_names ) ) {
													if ( $wpdb->query( $wpdb->prepare( "DELETE FROM $file_group_table WHERE file_id = %d AND group_id = %d", intval( $file_id ), intval( $current_file_group->group_id ) ) ) > 0 ) {
														do_action( "groups_deleted_file_group", intval( $file_id ), intval( $current_file_group->group_id ) );
													}
												}
											}

											// add file-group relations
											foreach( $group_names as $group_name ) {
												if ( $group = Groups_Group::read_by_name( $group_name ) ) {
													// we need to IGNORE for duplicate keys here (quick & dirty)
													if ( $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO $file_group_table (file_id,group_id) VALUES (%d,%d)", intval( $file_id ), intval( $group->group_id ) ) ) ) {
														do_action( "groups_created_file_group", intval( $file_id ), intval( $group->group_id ) );
													}
												}
											}
										}
									}

									$imported++;

								} else {
									$invalid++;
									$invalid_lines[] = $line_number;
									$invalid_line_messages[$line_number] = sprintf( __( 'Invalid file %s', GFA_PLUGIN_DOMAIN ), $filename );
									continue;
								}
							} else {
								$empty++;
							}
						}

						@fclose( $h );

						if ( !$test_import ) {
							self::$admin_messages[] = sprintf( __( 'Results after importing from <code>%s</code> :', GFA_PLUGIN_DOMAIN ), wp_strip_all_tags( $_FILES['file']['name'] ) );
							self::$admin_messages[] = sprintf( _n( '1 file has been imported', '%d files have been imported', $imported, GFA_PLUGIN_DOMAIN ), $imported );
							self::$admin_messages[] = sprintf( _n( '1 entry has been added', '%d entries have been added', $entries_added, GFA_PLUGIN_DOMAIN ), $entries_added );
							self::$admin_messages[] = sprintf( _n( '1 entry has been updated', '%d entries have been updated', $entries_updated, GFA_PLUGIN_DOMAIN ), $entries_updated );
							if ( $invalid > 0 ) {
								self::$admin_messages[] = sprintf( _n( '1 invalid line was skipped', '%d invalid lines were skipped', $invalid, GFA_PLUGIN_DOMAIN ), $invalid );
							}
							if ( $skipped_file > 0 ) {
								self::$admin_messages[] = sprintf( _n( '1 existing file was skipped', '%d existing files were skipped', $skipped_file, GFA_PLUGIN_DOMAIN ), $skipped_file );
							}
						} else {
							self::$admin_messages[] = sprintf( __( 'Results after importing (test only) from <code>%s</code> :', GFA_PLUGIN_DOMAIN ), wp_strip_all_tags( $_FILES['file']['name'] ) );
							self::$admin_messages[] = sprintf( _n( '1 file would have been imported', '%d files would have been imported', $imported, GFA_PLUGIN_DOMAIN ), $imported );
							if ( $invalid > 0 ) {
								self::$admin_messages[] = sprintf( _n( '1 invalid line was detected', '%d invalid lines were detected', $invalid, GFA_PLUGIN_DOMAIN ), $invalid );
							}
							if ( $skipped_file > 0 ) {
								self::$admin_messages[] = sprintf( _n( '1 existing file would have been skipped', '%d existing files would have been skipped', $skipped_file, GFA_PLUGIN_DOMAIN ), $skipped_file );
							}
						}

						if ( count( $invalid_lines ) > self::MAX_INVALID_LINES_SHOWN ) {
							array_splice( $invalid_lines, self::MAX_INVALID_LINES_SHOWN );
							$show_invalid_lines = implode( ', ', $invalid_lines ) . '&hellip;';
						} else {
							$show_invalid_lines = implode( ', ', $invalid_lines );
						}
						if ( count( $invalid_lines ) > 0 ) {
							self::$admin_messages[] = sprintf( __( 'Invalid lines: %s', GFA_PLUGIN_DOMAIN ), $show_invalid_lines );
							if ( count( $invalid_line_messages ) > 0 ) {
								foreach ( $invalid_lines as $line ) {
									if ( isset( $invalid_line_messages[$line] ) ) {
										self::$admin_messages[] = sprintf( '%d : %s', $line, $invalid_line_messages[$line] );
									}
								}
							}
						}
					} else {
						self::$admin_messages[] = __( 'Import failed (error opening temporary file).', GFA_PLUGIN_DOMAIN );
					}
				}
			}
		}

	}

	/**
	 * Export files.
	 */
	public static function export_files() {
		global $wpdb;
		if ( !headers_sent() ) {
			$charset = get_bloginfo( 'charset' );
			$now     = date( 'Y-m-d-H-i-s', time() );
			header( 'Content-Description: File Transfer' );
			if ( !empty( $charset ) ) {
				header( 'Content-Type: text/plain; charset=' . $charset );
			} else {
				header( 'Content-Type: text/plain' );
			}
			header( "Content-Disposition: attachment; filename=\"groups-file-access-export-$now.txt\"" );
			$group_table      = _groups_get_tablename( 'group' );
			$file_table       = _groups_get_tablename( 'file' );
			$file_group_table = _groups_get_tablename( 'file_group' );
			$separator        = "\t";
			if ( $results = $wpdb->get_results( "SELECT * FROM $file_table ORDER BY file_id" ) ) {
				foreach( $results as $result ) {
					$group_names = array();
					if ( $groups = $wpdb->get_results( $wpdb->prepare( "SELECT g.* FROM $file_group_table fg LEFT JOIN $group_table g ON fg.group_id = g.group_id WHERE file_id = %d ORDER BY g.name", $result->file_id ) ) ) {
						foreach ( $groups as $group ) {
							$group_names[] = $group->name;
						}
					}
					if ( count( $group_names ) > 0 ) {
						$group_names = implode( ',', $group_names );
					} else {
						$group_names = '';
					}

					echo sprintf(
						"%s%s%d%s%s%s%s%s%d%s%s\n",
						gfa_basename( $result->path ),
						$separator,
						intval( $result->file_id ),
						$separator,
						stripslashes( $result->name ),
						$separator,
						stripslashes( preg_replace( '/(\n|\r|\t)+/', ' ', $result->description ) ),
						$separator,
						intval( $result->max_count ),
						$separator,
						$group_names
					);
				}
				echo "\n";
			}
			die;
		} else {
			wp_die( 'ERROR: headers already sent' );
		}
	}
	
	/**
	 * Scan for files.
	 */
	public static function scan_files() {
		global $wpdb;
		if ( !headers_sent() ) {
			$charset = get_bloginfo( 'charset' );
			$now     = date( 'Y-m-d-H-i-s', time() );
			header( 'Content-Description: File Transfer' );
			if ( !empty( $charset ) ) {
				header( 'Content-Type: text/plain; charset=' . $charset );
			} else {
				header( 'Content-Type: text/plain' );
			}
			header( "Content-Disposition: attachment; filename=\"groups-file-access-scan-$now.txt\"" );
			$group_table      = _groups_get_tablename( 'group' );
			$file_table       = _groups_get_tablename( 'file' );
			$file_group_table = _groups_get_tablename( 'file_group' );
			$separator        = "\t";

			$paths = array();
			if ( $h = @opendir( GFA_UPLOADS_DIR ) ) {
				while ( false !== ( $path = @readdir( $h ) ) ) {
					if ( !is_dir( $path ) ) {
						if ( substr( $path, 0, 1 ) != '.' && $path != 'index.html' && $path != 'index.php' ) { // not hidden, or index file
							$paths[] = $path;
						}
					}
				}
				@closedir( $h );
			}

			$exclude_existing = !empty( $_POST['exclude_existing'] );
			foreach( $paths as $path ) {
				$filename = gfa_basename( $path );
				$full_path = GFA_File_Upload::path_filter( GFA_UPLOADS_DIR . '/' . $filename );
				$results   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $file_table WHERE path = %s", $full_path ) );
				if ( count( $results ) == 0 ) { // doesn't already exist as file entry
					$file_id = '';
					$filetrunk = $filename;
					$extension = pathinfo( $filename, PATHINFO_EXTENSION );
					if ( strlen( $extension ) > 0 ) {
						$k = strrpos( $filename, $extension );
						if ( $k !== false ) {
							if ( --$k > 0 ) {
								$filetrunk = substr( $filename, 0, $k );
							}
						}
					}
					if ( function_exists( 'mb_convert_case' ) ) {
						$name = mb_convert_case( preg_replace( '/\pP+/', ' ', $filetrunk ), MB_CASE_TITLE );
					} else {
						$name = ucwords( preg_replace( '/\pP+/', ' ', $filetrunk ) );
					}
					$description = stripslashes( !empty( $_POST['description'] ) ? trim( $_POST['description'] ) : '' );
					$max_count = !empty( $_POST['max_count'] ) ? intval( $_POST['max_count'] ) : 0;
					if ( $max_count < 0 ) {
						$max_count = 0;
					}
					$group_names = array();
					if ( !empty( $_POST['group_names'] ) ) {
						$_group_names = explode( ',', $_POST['group_names'] );
						foreach( $_group_names as $group_name ) {
							$group_names[] = trim( $group_name );
						}
					}
					if ( count( $group_names ) > 0 ) {
						$group_names = implode( ',', $group_names );
					} else {
						$group_names = '';
					}
					echo sprintf(
						"%s%s%s%s%s%s%s%s%d%s%s\n", // note that the $file_id placeholder must be %s here because it can be empty
						$filename,
						$separator,
						$file_id,
						$separator,
						$name,
						$separator,
						$description,
						$separator,
						$max_count,
						$separator,
						$group_names
					);
				} else if ( !$exclude_existing ) {
					foreach( $results as $result ) {
						$group_names = array();
						if ( $groups = $wpdb->get_results( $wpdb->prepare( "SELECT g.* FROM $file_group_table fg LEFT JOIN $group_table g ON fg.group_id = g.group_id WHERE file_id = %d ORDER BY g.name", $result->file_id ) ) ) {
							foreach ( $groups as $group ) {
								$group_names[] = $group->name;
							}
						}
						if ( count( $group_names ) > 0 ) {
							$group_names = implode( ',', $group_names );
						} else {
							$group_names = '';
						}
						echo sprintf(
							"%s%s%d%s%s%s%s%s%d%s%s\n",
							gfa_basename( $result->path ),
							$separator,
							intval( $result->file_id ),
							$separator,
							stripslashes( $result->name ),
							$separator,
							stripslashes( preg_replace( '/(\n|\r|\t)+/', ' ', $result->description ) ),
							$separator,
							intval( $result->max_count ),
							$separator,
							$group_names
						);
					}
				}
			}
			echo "\n";
			die;
		} else {
			wp_die( 'ERROR: headers already sent' );
		}
	}
}
Groups_File_Access_Scan_Import::init();
