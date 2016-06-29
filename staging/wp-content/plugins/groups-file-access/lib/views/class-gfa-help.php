<?php
/**
 * class-gfa-file-renderer.php
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
 * Context-specific help.
 */
class GFA_Help {
	
	/**
	* Renders the help section.
	*
	* @param string $what help
	* @return string help markup
	*/
	public static function get_help( $context = null ) {
		$output = '';
		switch( $context ) {
			case 'groups-admin-files' :
				$output .= '<div class="manage gfa-help">';
				$output .= '<h3>' . __( 'Groups File Access', GFA_PLUGIN_DOMAIN ) . '</h3>';
				
				$output .= '<p>';
				$output .= __( 'Additional information and examples are available on the <a href="http://www.itthinx.com/plugins/groups-file-access/">plugin page</a> and the <a href="http://www.itthinx.com/documentation/groups-file-access/">documentation pages</a>.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				
				$output .= '<h4>' . __( 'Managing files', GFA_PLUGIN_DOMAIN ) . '</h4>';
				$output .= '<p>';
				$output .= __( 'Files are managed in the <strong>Groups > Files</strong> section. Here you can add, edit and delete files that you want to make accessible to group members.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( 'Access to files is restricted by group membership. To be able to download a file, a user must be a member of a group that is assigned to the file. If an access limit has been set for the file, the user must also have accessed (downloaded) the file fewer times than the file&rsquo;s access limit.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( 'If you want to restrict access to a file to registered users, add the file using the <strong>New File</strong> option at the top of the screen. Once the file is added, tick the checkbox for the desired file in the file list, select the <em>Registered</em> group on top of the list and use the <strong>Add</strong> button to assign the group to the file. Now any registered user who is logged in can access the file (provided the access limit has not been exceeded by the user).', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( 'If you want to restrict access to a file to users that belong to a certain group, create the group, add the desired users to the group and assign the group to the files that the group should be able to access. More than one group can be assigned to a file.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( 'By selecting one or more files, bulk operations can be executed, including adding files to a group, removing files from a group and removing files.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<h5>' . __( 'Filters', GFA_PLUGIN_DOMAIN ) . '</h5>';
				$output .= '<p>';
				$output .= __( 'Use the filters to restrict the files displayed in the file list to those that match the given criteria. Note that the filter settings are persistent, i.e. if you leave the screen or log out and come back, the same settings will be in effect. Click the <strong>Apply</strong> button to use the filters and the <strong>Clear</strong> button to remove all filters.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '</div>';
				break;
			default :
				$output .= '<div class="manage gfa-help">';
				$output .= '<h3>' . __( 'Groups File Access', GFA_PLUGIN_DOMAIN ) . '</h3>';
				
				$output .= '<p>';
				$output .= __( 'Additional information and examples are available on the <a href="http://www.itthinx.com/plugins/groups-file-access/">plugin page</a> and the <a href="http://www.itthinx.com/documentation/groups-file-access/">documentation pages</a>.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
	
				$output .= '<h4>' . __( 'Setup', GFA_PLUGIN_DOMAIN ) . '</h4>';
	
				$output .= '<ol>';
				$output .= '<li>';
				$output .= __( 'If you have not done so already, install and activate the <a href="http://www.itthinx.com/plugins/groups/">Groups</a> plugin. Go to <strong>Plugins > Add New</strong>, search for <em>Groups</em> and click <em>Install Now</em>. You can also download the plugin <a href="http://wordpress.org/extend/plugins/groups/">here</a> and upload it to your site.', GFA_PLUGIN_DOMAIN );
				$output .= '</li>';
				$output .= '<li>';
				$output .= __( 'Check the settings on the <strong>Groups > File Access</strong> page and adjust them if needed.', GFA_PLUGIN_DOMAIN );
				$output .= '</li>';
				$output .= '<li>';
				$output .= __( 'If your setup is adequate, you can manage files controlled by the plugin on the <strong>Groups > Files</strong> page.', GFA_PLUGIN_DOMAIN );
				$output .= '</li>';
				$output .= '<li>';
				$output .= __( 'Use the shortcodes provided by the plugin to embed download links for group members on your pages or posts.', GFA_PLUGIN_DOMAIN );
				$output .= '</li>';
				$output .= '</ol>';
				
				$output .= '<h4>' . __( 'Managing files', GFA_PLUGIN_DOMAIN ) . '</h4>';
				$output .= '<p>';
				$output .= __( 'Files are managed in the <strong>Groups > Files</strong> section. Here you can add, edit and delete files that you want to make accessible to group members.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( 'Access to files is restricted by group membership. To be able to download a file, a user must be a member of a group that is assigned to the file. If an access limit has been set for the file, the user must also have accessed (downloaded) the file fewer times than the file&rsquo;s access limit.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( 'If you want to restrict access to a file to registered users, add the file using the <strong>New File</strong> option at the top of the screen. Once the file is added, tick the checkbox for the desired file in the file list, select the <em>Registered</em> group on top of the list and use the <strong>Add</strong> button to assign the group to the file. Now any registered user who is logged in can access the file (provided the access limit has not been exceeded by the user).', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( 'If you want to restrict access to a file to users that belong to a certain group, create the group, add the desired users to the group and assign the group to the files that the group should be able to access. More than one group can be assigned to a file.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( 'By selecting one or more files, bulk operations can be executed, including adding files to a group, removing files from a group and removing files.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<h5>' . __( 'Filters', GFA_PLUGIN_DOMAIN ) . '</h5>';
				$output .= '<p>';
				$output .= __( 'Use the filters to restrict the files displayed in the file list to those that match the given criteria. Note that the filter settings are persistent, i.e. if you leave the screen or log out and come back, the same settings will be in effect. Click the <strong>Apply</strong> button to use the filters and the <strong>Clear</strong> button to remove all filters.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
	
				$output .= '<h4>' . __( 'Shortcodes', GFA_PLUGIN_DOMAIN ) . '</h4>';
				$output .= '<p>';
				$output .= __( 'Shortcodes are used on posts or pages to render links to files, provide information about files and conditionally show content to users depending on whether they are allowed to access a file.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( 'The <code>[groups_file_link]</code> shortcode described below, renders the actual link to a file that authorized users can click to download the file.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				
				$output .= '<h5>' . __( '[groups_can_access_file]', GFA_PLUGIN_DOMAIN ) .'</h5>';
				$output .= '<p>';
				$output .= __( 'Content enclosed by this shortcode will only be shown if the current user can access the file. The file is identified by the required <code>file_id</code> attribute.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( 'Example: <code>[groups_can_access_file file_id="3"]This is shown if the user can access the file.[/groups_can_access_file]</code>', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( 'Attributes', GFA_PLUGIN_DOMAIN );
				$output .= '<br/>';
				$output .= '<ul>';
				$output .= '<li>';
				$output .= __( '<code>file_id</code> required - identifies the desired file', GFA_PLUGIN_DOMAIN );
				$output .= '</li>';
				$output .= '</ul>';
				$output .= '</p>';
				
				$output .= '<h5>' . __( '[groups_can_not_access_file]', GFA_PLUGIN_DOMAIN ) .'</h5>';
				$output .= '<p>';
				$output .= __( 'Shows content enclosed by the shortcode only when the current user can not access the given file. Attributes and usage are the same as for the <code>[groups_can_access_file]</code> shortcode.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				
				$output .= '<h5>' . __( '[groups_file_info]', GFA_PLUGIN_DOMAIN ) .'</h5>';
				$output .= '<p>';
				$output .= __( 'This shortcode renders information about a file including the name, description, maximum number of allowed accesses per user, consumed and remaining number of accesses for the current user and the file id.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( 'Example: <code>[groups_file_info file_id="7" show="name"]</code>', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( 'Attributes', GFA_PLUGIN_DOMAIN );
				$output .= '<br/>';
				$output .= '<ul>';
				$output .= '<li>';
				$output .= __( '<code>file_id</code> required - identifies the desired file', GFA_PLUGIN_DOMAIN );
				$output .= '</li>';
				$output .= '<li>';
				$output .= __( '<code>show</code> optional - defaults to "name". Acceptable values are any of <code>name</code>, <code>description</code>, <code>count</code>, <code>max_count</code>, <code>remaining</code> and <code>file_id</code>', GFA_PLUGIN_DOMAIN );
				$output .= '</li>';
				$output .= '<li>';
				$output .= __( '<code>visibility</code> optional - defaults to <code>can_access</code> showing information only if the current user can access the file, <code>always</code> will show information unconditionally', GFA_PLUGIN_DOMAIN );
				$output .= '</li>';
				$output .= '<li>';
				$output .= __( '<code>filter</code> optional - defaults to <code>wp_filter_kses</code> determining the filter function that is applied to the information about to be shown. If <code>none</code> or an empty value is provided, no filter function will be applied prior to rendering the information.', GFA_PLUGIN_DOMAIN );
				$output .= '</li>';
				$output .= '</ul>';
				$output .= '</p>';
								
				$output .= '<h5>' . __( '[groups_file_url]', GFA_PLUGIN_DOMAIN ) .'</h5>';
				$output .= '<p>';
				$output .= __( 'This shortcode renders the URL that serves the file. An authorized user can visit the URL to download the file.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( 'Example: <code>[groups_file_url file_id="456"]</code>', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( 'Attributes', GFA_PLUGIN_DOMAIN );
				$output .= '<br/>';
				$output .= '<ul>';
				$output .= '<li>';
				$output .= __( '<code>file_id</code> required - identifies the desired file', GFA_PLUGIN_DOMAIN );
				$output .= '</li>';
				$output .= '<li>';
				$output .= __( '<code>visibility</code> optional - defaults to <code>can_access</code> showing information only if the current user can access the file, <code>always</code> will show information unconditionally', GFA_PLUGIN_DOMAIN );
				$output .= '</li>';
				$output .= '</ul>';
				$output .= '</p>';
				
				$output .= '<h5>' . __( '[groups_file_link]', GFA_PLUGIN_DOMAIN ) .'</h5>';
				$output .= '<p>';
				$output .= __( 'This shortcode renders links to files. An authorized user can click on a link to download the related file.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( 'Example: <code>[groups_file_link file_id="78"]</code>', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( 'Attributes', GFA_PLUGIN_DOMAIN );
				$output .= '<br/>';
				$output .= '<ul>';
				$output .= '<li>';
				$output .= __( '<code>file_id</code> required* - identifies the desired file', GFA_PLUGIN_DOMAIN );
				$output .= '</li>';
				$output .= '<li>';
				$output .= __( '<code>group</code> required* - group name or ID - will list file links for the files that are related to the given group, sorted by name', GFA_PLUGIN_DOMAIN );
				$output .= '</li>';
				$output .= '<li>';
				$output .= __( '<code>description</code> optional - only effective when used with the <code>group</code> attribute; defaults to "no", if set to "yes" will show descriptions for each file', GFA_PLUGIN_DOMAIN );
				$output .= '</li>';
				$output .= '<li>';
				$output .= __( '<code>order</code> optional - only effective when used with the <code>group</code> attribute; files are listed sorted by name which defaults to "asc" for ascending order, allows "desc" for descending order', GFA_PLUGIN_DOMAIN );
				$output .= '</li>';
				$output .= '<li>';
				$output .= __( '<code>visibility</code> optional - defaults to <code>can_access</code> showing information only if the current user can access the file, <code>always</code> will show information unconditionally', GFA_PLUGIN_DOMAIN );
				$output .= '</li>';
				$output .= '</ul>';
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( '* Only one of <code>file_id</code> or <code>group</code> is required, both should not be provided.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( 'Additional attributes are accepted for the link&rsquo;s <code>a</code> tag. These are <code>accesskey</code>, <code>alt</code>, <code>charset</code>, <code>coords</code>, <code>class</code>, <code>dir</code>, <code>hreflang</code>, <code>id</code>, <code>lang</code>, <code>name</code>, <code>rel</code>, <code>rev</code>, <code>shape</code>, <code>style</code>, <code>tabindex</code> and <code>target</code>. Please refer to the <a href="http://www.w3.org/TR/html401/cover.html">HTML 4.01 Specification</a> on the <a href="http://www.w3.org/TR/html401/struct/links.html#h-12.2">The A element</a> for further information about these attributes.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				
				$output .= '<h5>' . __( '[groups_file_visibility]', GFA_PLUGIN_DOMAIN ) .'</h5>';
				$output .= '<p>';
				$output .= __( 'This shortcode allows to switch the default visibility setting for those shortcodes that provide a <code>visibility</code> attribute. The shortcode can be used multiple times on a page or post and will affect the shortcodes below it unless it is used again.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( 'Example: <code>[groups_file_visibility visibility="always"]</code>', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';
				$output .= '<p>';
				$output .= __( 'Attributes', GFA_PLUGIN_DOMAIN );
				$output .= '<br/>';
				$output .= '<ul>';
				$output .= '<li>';
				$output .= __( '<code>visibility</code> required - <code>can_access</code> or <code>always</code>', GFA_PLUGIN_DOMAIN );
				$output .= '</li>';
				$output .= '</ul>';
				$output .= '</p>';
	
				$output .= '<h4>' . __( 'API', GFA_PLUGIN_DOMAIN ) . '</h4>';
				$output .= '<p>';
				$output .= __( 'Please refer to the <a href="http://www.itthinx.com/documentation/groups-file-access/">documentation pages</a> for details on the plugin&rsquo;s API.', GFA_PLUGIN_DOMAIN );
				$output .= '</p>';

				$output .= '</div>';
	
				break;
		}
		return $output;
	}
	
	public static function footer( $show_icon = false ) {
		return
		'<div class="gfa-footer">' .
		( $show_icon ? '<img src="http://www.itthinx.com/img/groups/gfa.png">' : '' ) .
		__( 'Copyright <a href="http://www.itthinx.com/">itthinx</a> - This plugin is provided subject to the license granted. Unauthorized use and distribution is prohibited.', GFA_PLUGIN_DOMAIN ) .
		'</div>';
	}
}