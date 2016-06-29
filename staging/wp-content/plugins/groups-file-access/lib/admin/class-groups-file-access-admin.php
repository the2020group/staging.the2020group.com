<?php
/**
 * class-groups-file-access-admin.php
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
 * Handles admin settings.
 */
class Groups_File_Access_Admin extends Groups_File_Access {

	/**
	 * Plugin settings - additional Groups admin section.
	 */
	public static function groups_admin_file_access() {
	
		$output = '';
	
		if ( !current_user_can( GROUPS_ADMINISTER_OPTIONS ) ) {
			wp_die( __( 'Access denied.', GFA_PLUGIN_DOMAIN ) );
		}
		
		$is_sitewide_plugin = false;
		if ( is_multisite() ) {
			$active_sitewide_plugins = get_site_option( 'active_sitewide_plugins', array() );
			$active_sitewide_plugins = array_keys( $active_sitewide_plugins );
			$is_sitewide_plugin = in_array( 'groups-file-access/groups-file-access.php', $active_sitewide_plugins );
		}

		if ( isset( $_GET[self::DISMISS_HELP] ) ) {
			if ( $_GET[self::DISMISS_HELP] ) {
				update_user_meta( get_current_user_id(), self::PLUGIN_OPTIONS . '-' . self::DISMISS_HELP, true );
			} else {
				delete_user_meta( get_current_user_id(), self::PLUGIN_OPTIONS . '-' . self::DISMISS_HELP );
			}
		}
	
		$options = get_option( self::PLUGIN_OPTIONS , array() );
		if ( !isset( $options[self::SCHEMA_UPDATED] ) || !$options[self::SCHEMA_UPDATED] ) {
			self::schema_update();
		}
		if ( !file_exists( GFA_UPLOADS_DIR ) ) {
			self::folders_update();
		}
	
		if ( isset( $_POST['submit'] ) ) {
			if ( wp_verify_nonce( $_POST[self::NONCE], self::SET_ADMIN_OPTIONS ) ) {
				$options[self::APPLY_MIME_TYPES] = !empty( $_POST[self::APPLY_MIME_TYPES] );
				$content_disposition = $_POST[self::CONTENT_DISPOSITION];
				switch( $content_disposition ) {
					case self::CONTENT_DISPOSITION_ATTACHMENT :
					case self::CONTENT_DISPOSITION_INLINE :
						$options[self::CONTENT_DISPOSITION] = $content_disposition;
						break;
					default :
						$options[self::CONTENT_DISPOSITION] = self::CONTENT_DISPOSITION_DEFAULT;
				}
				$options[self::SESSION_ACCESS] = !empty( $_POST[self::SESSION_ACCESS] );
				$t = !empty( $_POST[self::SESSION_ACCESS_TIMEOUT] ) ? intval( $_POST[self::SESSION_ACCESS_TIMEOUT] ) : self::SESSION_ACCESS_TIMEOUT_DEFAULT;
				if ( $t <= 0 ) {
					$t = self::SESSION_ACCESS_TIMEOUT_DEFAULT;
				}
				$options[self::SESSION_ACCESS_TIMEOUT] = $t;
				$options[self::NOTIFY_ADMIN]  = !empty( $_POST[self::NOTIFY_ADMIN] );
				$options[self::ADMIN_SUBJECT] = $_POST[self::ADMIN_SUBJECT];
				$options[self::ADMIN_MESSAGE] = $_POST[self::ADMIN_MESSAGE];
				$options[self::LOGIN_REDIRECT]= !empty( $_POST[self::LOGIN_REDIRECT] );
				if ( !$is_sitewide_plugin ) {
					$options[self::DELETE_DATA]               = !empty( $_POST[self::DELETE_DATA] );
					$options[self::DELETE_DATA_ON_DEACTIVATE] = !empty( $_POST[self::DELETE_DATA_ON_DEACTIVATE] );
				}
			}
			update_option( self::PLUGIN_OPTIONS, $options );
		}

		$apply_mime_types    = isset( $options[self::APPLY_MIME_TYPES] ) ? $options[self::APPLY_MIME_TYPES] : self::APPLY_MIME_TYPES_DEFAULT;
		$content_disposition = isset( $options[self::CONTENT_DISPOSITION] ) ? $options[self::CONTENT_DISPOSITION] : self::CONTENT_DISPOSITION_DEFAULT;
		$session_access      = isset( $options[self::SESSION_ACCESS] ) ? $options[self::SESSION_ACCESS] : self::SESSION_ACCESS_DEFAULT;
		$session_access_timeout = isset( $options[self::SESSION_ACCESS_TIMEOUT] ) ? $options[self::SESSION_ACCESS_TIMEOUT] : self::SESSION_ACCESS_TIMEOUT_DEFAULT;

		$notify_admin      = isset( $options[self::NOTIFY_ADMIN] ) ? $options[self::NOTIFY_ADMIN] : self::NOTIFY_ADMIN_DEFAULT;
		$admin_subject     = isset( $options[self::ADMIN_SUBJECT] ) ? esc_attr( wp_filter_nohtml_kses( $options[self::ADMIN_SUBJECT] ) ) : self::ADMIN_DEFAULT_SUBJECT;
		$admin_message     = isset( $options[self::ADMIN_MESSAGE] ) ? $options[self::ADMIN_MESSAGE] : self::ADMIN_DEFAULT_MESSAGE;

		$login_redirect    = isset( $options[self::LOGIN_REDIRECT] ) ? $options[self::LOGIN_REDIRECT] : false;

		$delete_data = isset( $options[self::DELETE_DATA] ) ? $options[self::DELETE_DATA] : false;
		$delete_data_on_deactivate = isset( $options[self::DELETE_DATA_ON_DEACTIVATE] ) ? $options[self::DELETE_DATA_ON_DEACTIVATE] : false;
	
		if ( $delete_data ) {
			register_uninstall_hook( GFA_FILE, array( 'Groups_File_Access', 'uninstall' ) );
		} else {
			self::remove_uninstall_hook( GFA_FILE, array( 'Groups_File_Access', 'uninstall' ) );
		}
	
		$output .= '<h2>' . __( 'Groups File Access', GFA_PLUGIN_DOMAIN ) . '</h2>';
	
		$output .= '<form action="" name="options" method="post">';
		$output .= '<div style="margin-right:1em">';
	
		if ( !get_user_meta( get_current_user_id(), self::PLUGIN_OPTIONS . '-' . self::DISMISS_HELP, true ) ) {
			$output .= __( 'The following information is also available on the Help tab.', GFA_PLUGIN_DOMAIN );
			$output .= ' ';
			$dismiss_url = admin_url( 'admin.php?page=groups-admin-file-access' ) . '&' . self::DISMISS_HELP . '=1';
			$output .= '<a href="' . esc_url( $dismiss_url ) . '">' . __( 'Ok I got it, remove it from here.', GFA_PLUGIN_DOMAIN  ) . '</a>';
			require_once( GFA_VIEWS_LIB . '/class-gfa-help.php' );
			$output .= GFA_Help::get_help();
		}

		$output .= '<div class="manage">';
		$output .= '<h4>' . __( 'Uploads', GFA_PLUGIN_DOMAIN ) . '</h4>';

		if ( file_exists( GFA_UPLOADS_DIR ) ) {
			$output .= '<p>';
			$output .= sprintf( __( 'Files are uploaded to the <code>%s</code> directory.', GFA_PLUGIN_DOMAIN ), GFA_UPLOADS_DIR );
			$output .= '<p>';
		} else {
			$output .= "<div class='error'>" . sprintf( __( 'I could not create the <code>%s</code> directory. Your web server must have write permissions on its parent directory.', GFA_PLUGIN_DOMAIN ), GFA_UPLOADS_DIR ) . "</div>";
		}
		if ( file_exists( GFA_UPLOADS_DIR . '/.htaccess' ) ) {
			$output .= '<p>';
			$output .= sprintf( __( 'Access to files in <code>%s</code> is protected by an <code>.htaccess</code> file in that directory.', GFA_PLUGIN_DOMAIN ), GFA_UPLOADS_DIR );
			$output .= '</p>';
		} else {
			$output .= "<div class='error'>" . sprintf( __( 'I could not create the <code>.htaccess</code> file in the <code>%s</code> directory. This file is required to assure that unauthorized access to files is avoided.', GFA_PLUGIN_DOMAIN ), GFA_UPLOADS_DIR ) . "</div>";
		}
		if ( file_exists( GFA_UPLOADS_DIR . '/index.html' ) ) {
			$output .= '<p>';
			$output .= sprintf( __( 'The directory listing is hidden through an <code>index.html</code> in <code>%s</code>.', GFA_PLUGIN_DOMAIN ), GFA_UPLOADS_DIR );
			$output .= '</p>';
		} else {
			$output .= "<div class='error'>" . sprintf( __( 'I could not create the <code>index.html</code> file in the <code>%s</code> directory. This file is used to hide the directory contents from prying eyes.', GFA_PLUGIN_DOMAIN ), GFA_UPLOADS_DIR ) . "</div>";
		}
		$output .= '</div>'; // .manage

		$output .= '<div class="manage">';

		$output .= '<h3>' . __( 'Serving Files', GFA_PLUGIN_DOMAIN ) . '</h3>';

		$output .= '<h4>' . __( 'MIME Types', GFA_PLUGIN_DOMAIN ) . '</h4>';

		$output .= '<p>';
		$output .= '<label>';
		$output .= '<input name="' . self::APPLY_MIME_TYPES. '" type="checkbox" ' . ( $apply_mime_types ? ' checked="checked" ' : '' ) . ' />';
		$output .= ' ';
		$output .= __( 'Use specific MIME types', GFA_PLUGIN_DOMAIN );
		$output .=  '</label>';
		$output .= '</p>';

		$output .= '<p class="description">';
		$output .= __( 'If enabled, the determined MIME type is used as the content type for files served. Otherwise, <code>application/octet-stream</code> is used.', GFA_PLUGIN_DOMAIN );
		$output .= '</p>';

		$output .= '<h4>' . __( 'Content Disposition', GFA_PLUGIN_DOMAIN ) . '</h4>';

		$output .= '<p>';
		$output .= '<label>';
		$output .= '<select name="' . self::CONTENT_DISPOSITION . '">';
		$output .= __( 'Content Disposition Value', GFA_PLUGIN_DOMAIN );
		$output .= ' ';
		$output .= sprintf(
			'<option value="%s" %s>%s</option>',
			self::CONTENT_DISPOSITION_ATTACHMENT,
			$content_disposition == self::CONTENT_DISPOSITION_ATTACHMENT ? ' selected="selected" ' : '',
			self::CONTENT_DISPOSITION_ATTACHMENT . ' - ' . __( 'user controlled display', GFA_PLUGIN_DOMAIN )
		);
		$output .= sprintf(
			'<option value="%s" %s>%s</option>',
			self::CONTENT_DISPOSITION_INLINE,
			$content_disposition == self::CONTENT_DISPOSITION_INLINE ? ' selected="selected" ' : '',
			self::CONTENT_DISPOSITION_INLINE . ' - ' . __( 'displayed automatically', GFA_PLUGIN_DOMAIN )
		);
		$output .=  '</select>';
		$output .=  '</label>';
		$output .= '<p class="description">';
		$output .= __( 'An <code>inline</code> content-disposition means that the file should be automatically displayed.', GFA_PLUGIN_DOMAIN );
		$output .= ' ' ;
		$output .= __( 'An <code>attachment</code> content-disposition, is not displayed automatically and requires some form of action from the user to open it.', GFA_PLUGIN_DOMAIN );
		$output .= '</p>';
		$output .= '</p>';

		$output .= '<h4>' . __( 'Session Access', GFA_PLUGIN_DOMAIN ) . '</h4>';

		$output .= '<p>';
		$output .= '<label>';
		$output .= '<input name="' . self::SESSION_ACCESS . '" type="checkbox" ' . ( $session_access ? ' checked="checked" ' : '' ) . ' />';
		$output .= ' ';
		$output .= __( 'Enable temporary access URLs', GFA_PLUGIN_DOMAIN );
		$output .=  '</label>';
		$output .= '</p>';

		$output .= '<p class="description">';
		$output .= __( 'If enabled, all file URLs and links that are rendered using the <code>[groups_file_url]</code> or the <code>[groups_file_link]</code> shortcode will have a session access identifier appended automatically for authorized users.', GFA_PLUGIN_DOMAIN );
		$output .= ' ';
		$output .= __( 'These URLs grant access to files without the need to be logged in.', GFA_PLUGIN_DOMAIN );
		$output .= ' ';
		$output .= __( 'Session access can be granted for specific files without the need for this option to be enabled, by specifying the <code>session_access="yes"</code> shortcode attribute.', GFA_PLUGIN_DOMAIN );
		$output .= '</p>';

		$output .= '<p>';
		$output .= '<label>';
		$output .= __( 'Temporary access timeout', GFA_PLUGIN_DOMAIN );
		$output .= ' ';
		$output .= sprintf( '<input name="' . self::SESSION_ACCESS_TIMEOUT . '" type="text" value="%d" style="width:5em;text-align:right;"/>', esc_attr( $session_access_timeout ) );
		$output .= ' ';
		$output .= __( '<em>seconds</em>', GFA_PLUGIN_DOMAIN );
		$output .=  '</label>';
		$output .= '</p>';

		$output .= '<p class="description">';
		$output .= __( 'Temporary access is valid during the period of time established through the timeout.', GFA_PLUGIN_DOMAIN );
		$output .= ' ';
		$output .= __( 'If the temporary URL has not been accessed during that period of time, the link is invalid and access is refused.', GFA_PLUGIN_DOMAIN );
		$output .= ' ';
		$output .= __( 'The time period is extended for the duration of the timeout while the URL is accessed.', GFA_PLUGIN_DOMAIN );
		$output .= '</p>';

		$output .= '</div>'; // .manage

		$output .= '<div class="manage">';
	
		$output .= '<h3>' . __( 'Notifications', GFA_PLUGIN_DOMAIN ) . '</h3>';
	
		$output .= '<h4>' . __( 'Notify the admin', GFA_PLUGIN_DOMAIN ) . '</h4>';
		$output .= '<p>';
		$output .= '<input name="' . self::NOTIFY_ADMIN . '" type="checkbox" ' . ( $notify_admin ? ' checked="checked" ' : '' ) . ' />';
		$output .= '&nbsp;';
		$output .= '<label for="' . self::NOTIFY_ADMIN . '">' . __( 'Notify the site administrator', GFA_PLUGIN_DOMAIN) . '</label>';
		$output .= '</p>';
		$output .= '<p class="description">' . __( 'Sends a notification email to the site administrator when a file has been accessed.', GFA_PLUGIN_DOMAIN ) . '</p>';
	
		$output .= '<h4>' . __( 'Admin notification', GFA_PLUGIN_DOMAIN ) . '</h4>';
		$output .= '<p>';
		$output .= '<label style="display:block" for="' . self::ADMIN_SUBJECT . '">' . __( 'Notification email subject', GFA_PLUGIN_DOMAIN) . '</label>';
		$output .= '<input style="width:40em" name="' . self::ADMIN_SUBJECT . '" type="text" value="' . $admin_subject . '" />';
		$output .= '</p>';
		$output .= '<p>';
		$output .= __( 'The default subject is:', GFA_PLUGIN_DOMAIN );
		$output .= '<pre>';
		$output .= htmlentities( self::ADMIN_DEFAULT_SUBJECT );
		$output .= '</pre>';
		$output .= '</p>';
		$output .= '<p>';
		$output .= '<label style="display:block" for="' . self::ADMIN_MESSAGE . '">' . __( 'Notification email message', GFA_PLUGIN_DOMAIN) . '</label>';
		$output .= '<textarea style="width:40em;height:10em;" name="' . self::ADMIN_MESSAGE . '">' . stripslashes( $admin_message ) . '</textarea>';
		$output .= '</p>';
		$output .= '<p>';
		$output .= __( 'The default message is:', GFA_PLUGIN_DOMAIN );
		$output .= '<pre>';
		$output .= htmlentities( self::ADMIN_DEFAULT_MESSAGE );
		$output .= '</pre>';
		$output .= '</p>';
		$output .= '<p class="description">' . __( 'The message format is HTML. Use &lt;br/&gt; for line breaks.', GFA_PLUGIN_DOMAIN ) . '</p>';
		$output .= '<p class="description">' . __( 'These default tokens can be used in the subject and message: [file_path] [file_url] [site_title] [site_url] [username].', GFA_PLUGIN_DOMAIN ) . '</p>';
	
		$output .= '</div>';

		$output .= '<div class="manage">';
		$output .= '<h3>' . __( 'Redirect', GFA_PLUGIN_DOMAIN ) . '</h3>';
		$output .= '<p>';
		$output .= '<input name="' . self::LOGIN_REDIRECT . '" type="checkbox" ' . ( $login_redirect ? ' checked="checked" ' : '' ) . ' />';
		$output .= '&nbsp;';
		$output .= '<label for="' . self::LOGIN_REDIRECT . '">' . __( 'Redirect to the WordPress login when a user who is not logged in tries to access a file?', GFA_PLUGIN_DOMAIN) . '</label>';
		$output .= '</p>';
		$output .= '</div>';

		if ( !$is_sitewide_plugin ) {
			$output .= '<div class="manage">';
			$output .= '<h3>' . __( 'Delete data', GFA_PLUGIN_DOMAIN ) . '</h3>';
			$output .= '<p>';
			$output .= '<input name="' . self::DELETE_DATA . '" type="checkbox" ' . ( $delete_data ? ' checked="checked" ' : '' ) . ' />';
			$output .= '&nbsp;';
			$output .= '<label for="' . self::DELETE_DATA . '">' . __( 'Delete plugin data when the plugin is <strong>deleted</strong>?', GFA_PLUGIN_DOMAIN) . '</label>';
			$output .= '</p>';
			$output .= '<p>';
			$output .= '<input name="' . self::DELETE_DATA_ON_DEACTIVATE . '" type="checkbox" ' . ( $delete_data_on_deactivate ? ' checked="checked" ' : '' ) . ' />';
			$output .= '&nbsp;';
			$output .= '<label for="' . self::DELETE_DATA_ON_DEACTIVATE . '">' . __( 'Delete all <em>Groups File Access</em> plugin data when the plugin is <strong>deactivated</strong>? This option is useful to clean up after testing.', GFA_PLUGIN_DOMAIN) . '</label>';
			$output .= '</p>';
			$output .= '<p class="description warning">' . __( 'CAUTION: These options will delete all plugin data and settings when the plugin is <strong>deactivated</strong> or <strong>deleted</strong>. By enabling any of these options, you agree to be solely responsible for any loss of data or any other consequences thereof.', GFA_PLUGIN_DOMAIN ) . '</p>';
			$output .= '<p class="description">' . sprintf( __( 'They will not delete any files in the <code>%s</code> directory.', GFA_PLUGIN_DOMAIN ), GFA_UPLOADS_DIR ) . '</p>';
			$output .= '<p class="description">' . __( 'Deletion will only take effect if the <strong>Groups</strong> plugin is activated.', GFA_PLUGIN_DOMAIN ) . '</p>';
			$output .= '</div>';
		}

		$output .= '<p>';
		$output .= wp_nonce_field( self::SET_ADMIN_OPTIONS, self::NONCE, true, false );
		$output .= '<input class="button button-primary" type="submit" name="submit" value="' . __( 'Save', GFA_PLUGIN_DOMAIN ) . '"/>';
		$output .= '</p>';
		$output .= '</div>';
	
		$output .= '</div>';
		$output .= '</form>';
		
		require_once( GFA_VIEWS_LIB . '/class-gfa-help.php' );
		$output .= GFA_Help::footer( true );
	
		echo $output;
	}
	
	public static function groups_network_admin_file_access() {
	
		$output = '';
	
		if ( !current_user_can( GROUPS_ADMINISTER_OPTIONS ) ) {
			wp_die( __( 'Access denied.', GFA_PLUGIN_DOMAIN ) );
		}

		$options = get_option( self::PLUGIN_OPTIONS , array() );	
		if ( isset( $_POST['submit'] ) ) {
			if ( wp_verify_nonce( $_POST[self::NONCE], self::SET_ADMIN_OPTIONS ) ) {
				$options[self::NETWORK_DELETE_DATA_ON_DEACTIVATE] = !empty( $_POST[self::NETWORK_DELETE_DATA_ON_DEACTIVATE] );
				update_option( self::PLUGIN_OPTIONS, $options );
			}
		}

		$delete_data_on_deactivate = isset( $options[self::NETWORK_DELETE_DATA_ON_DEACTIVATE] ) ? $options[self::NETWORK_DELETE_DATA_ON_DEACTIVATE] : false;

		$output .=
			'<div>' .
			'<h2>' .
			__( 'Groups File Access', GFA_PLUGIN_DOMAIN ) .
			'</h2>' .
			'</div>';
	
		$output .= '<form action="" name="options" method="post">';
		$output .= '<div style="margin-right:1em">';
	
		$output .= '<div class="manage">';
		$output .= '<h3>' . __( 'Network delete data', GFA_PLUGIN_DOMAIN ) . '</h3>';
		$output .= '<p>';
		$output .= '<input name="' . self::NETWORK_DELETE_DATA_ON_DEACTIVATE . '" type="checkbox" ' . ( $delete_data_on_deactivate ? ' checked="checked" ' : '' ) . ' />';
		$output .= '&nbsp;';
		$output .= '<label for="' . self::NETWORK_DELETE_DATA_ON_DEACTIVATE . '">' . __( 'Delete all <em>Groups File Access</em> plugin data on <strong>all sites</strong> when the plugin is <strong>network deactivated</strong>.', GFA_PLUGIN_DOMAIN) . '</label>';
		$output .= '</p>';
		$output .= '<p>';
		$output .= '<ul>';
		$output .= '<li class="description warning">' . __( 'CAUTION: This option will delete all plugin data and settings <strong>on all sites</strong> when the plugin is <strong>network deactivated</strong>. By enabling the option, you agree to be solely responsible for any loss of data or any other consequences thereof.', GFA_PLUGIN_DOMAIN ) . '</li>';
		$output .= '<li class="description">' . sprintf( __( 'This option will not delete any files that have been uploaded to the sites.', GFA_PLUGIN_DOMAIN ), GFA_UPLOADS_DIR ) . '</li>';
		$output .= '<li class="description">' . __( 'This option will only take effect if the <strong>Groups</strong> plugin is activated.', GFA_PLUGIN_DOMAIN ) . '</li>';
		$output .= '</ul>';
		$output .= '</p>';

		$output .= '<p>';
		$output .= wp_nonce_field( self::SET_ADMIN_OPTIONS, self::NONCE, true, false );
		$output .= '<input class="button" type="submit" name="submit" value="' . __( 'Save', GFA_PLUGIN_DOMAIN ) . '"/>';
		$output .= '</p>';
		$output .= '</div>';

		$output .= '</div>';
		$output .= '</form>';

		require_once( GFA_VIEWS_LIB . '/class-gfa-help.php' );
		$output .= GFA_Help::footer( true );
	
		echo $output;
	}
}