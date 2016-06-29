<?php
/**
 * class-gfa-shortcodes.php
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
class GFA_Shortcodes {
	
	/**
	 * @var string show if user has file access
	 */
	const CAN_ACCESS = 'can_access';
	
	/** 
	 * @var string always show
	 */
	const ALWAYS = 'always';
	
	/**
	 * @var string current default visibility to apply
	 */
	private static $visibility = self::CAN_ACCESS;
	
	/**
	 * Adds shortcodes.
	 */
	public static function init() {
		// content by file access
		add_shortcode( 'groups_can_access_file', array( __CLASS__, 'groups_can_access_file' ) );
		add_shortcode( 'groups_can_not_access_file', array( __CLASS__, 'groups_can_not_access_file' ) );
		// file information
		add_shortcode( 'groups_file_info', array( __CLASS__, 'groups_file_info' ) );
		// Link
		add_shortcode( 'groups_file_link', array( __CLASS__, 'groups_file_link' ) );
		// URL
		add_shortcode( 'groups_file_url', array( __CLASS__, 'groups_file_url' ) );
		// determine default visibility
		add_shortcode( 'groups_file_visibility', array( __CLASS__, 'groups_file_visibility' ) );
		// render the service key
		add_shortcode( 'groups_file_access_service_key', array( __CLASS__, 'groups_file_access_service_key' ) );
	}
	
	/**
	 * Renders a file URL.
	 * Attributes:
	 * - "file_id" : id of the file
	 * - "visibility" : "can_access" (default) renders only if current user is authorized to access the file, "always" renders in any case
	 * 
	 * @param array $atts attributes
	 * @param string $content not used
	 * @return rendered URL
	 */
	public static function groups_file_url( $atts, $content = null ) {
		global $wpdb;
		$output = "";
		$options = shortcode_atts(
			array(
				'file_id'        => null,
				'visibility'     => self::$visibility,
				'session_access' => Groups_File_Access_Session::enabled() ? 'yes' : 'no'
			),
			$atts
		);
		if ( $options['file_id'] !== null ) {
			$file_id = intval( $options['file_id'] );
			$can_see = false;
			switch( $options['visibility'] ) {
				case self::ALWAYS :
					$can_see = true;
					break;
				default :
					$user_id = get_current_user_id();
					$can_see = Groups_File_Access::can_access( $user_id, $file_id );
			}
			if ( $can_see ) {
				$file_table = _groups_get_tablename( 'file' );
				$file = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $file_table WHERE file_id = %d", $file_id ) );
				$base_url = get_bloginfo( 'url' );
				$output = GFA_File_Renderer::render_url( $file, $base_url, array( 'session_access' => $options['session_access'] ) );
			}
		}
		return $output;
	}
	
	/**
	 * Renders a link to a file.
	 * 
	 * Required attributes are either "file_id" or "group".
	 * 
	 * Basic attributes:
	 * - "file_id" : id of the file
	 * - "visibility" : "can_access" or "always" see GFA_Shortcodes::groups_file_url()
	 * - "group" : group name or ID - will list files for the given group sorted by name
	 * - "description" : defaults to "no", "yes" shows description for each entry (only "group")
	 * - "order" : ASC or DESC sort order (only for "group")
	 * - "list_prefix" : defaults to "<ul>"
	 * - "list_suffix" : defaults to "</ul>"
	 * - "item_prefix" : defaults to "<li>"
	 * - "item_suffix" : defaults to "</li>"
	 * 
	 * Note that the prefixes and suffixes are very limited due to filters applied.
	 * 
	 * Allowed link attributes: accesskey, alt, charset, coords, class, dir, hreflang, id, lang, name, rel, rev, shape, style, tabindex, target 
	 *
	 * @see GFA_Shortcodes::groups_file_url()
	 *
	 * @param array $atts attributes
	 * @param string $content not used
	 * @return rendered link
	 */
	public static function groups_file_link( $atts, $content = null ) {
		global $wpdb;
		$output = "";
		$options = shortcode_atts(
			array(
				// attributes
				'file_id'     => null,
				'visibility'  => self::$visibility,
				'group'       => null,
				'description' => 'no',
				'description_filter' => 'wp_filter_kses',
				'order'       => 'ASC',
				'orderby'     => 'name',
				'list_prefix' => '<ul>',
				'list_suffix' => '</ul>',
				'item_prefix' => '<li>',
				'item_suffix' => '</li>',
				// link attributes
				'accesskey' => null,
				'alt'       => null,
				'charset'   => null,
				'coords'    => null,
				'class'     => null,
				'dir'       => null,
				'hreflang'  => null,
				'id'        => null,
				'lang'      => null,
				'name'      => null,
				'rel'       => null,
				'rev'       => null,
				'shape'     => null,
				'style'     => null,
				'tabindex'  => null,
				'target'    => null,
				'session_access' => Groups_File_Access_Session::enabled() ? 'yes' : 'no'
			),
			$atts
		);
		foreach( $options as $key => $value ) {
			if ( $value === null ) {
				unset( $options[$key] );
			} else {
				$options[$key] = esc_attr( $value );
			}
		}
		if ( isset( $options['file_id'] ) && ( $options['file_id'] !== null ) ) {
			$file_id = intval( $options['file_id'] );
			switch( $options['visibility'] ) {
				case self::ALWAYS :
					$can_see = true;
					break;
				default :
					$user_id = get_current_user_id();
					$can_see = Groups_File_Access::can_access( $user_id, $file_id );
			}
			if ( $can_see ) {
				$file_table = _groups_get_tablename( 'file' );
				$file = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $file_table WHERE file_id = %d", $file_id ) );
				$base_url = get_bloginfo( 'url' );
				unset( $options['file_id'] );
				unset( $options['group'] );
				unset( $options['order'] );
				unset( $options['orderby'] );
				unset( $options['visibility'] );
				unset( $options['list_prefix'] );
				unset( $options['list_suffix'] );
				unset( $options['item_prefix'] );
				unset( $options['item_suffix'] );
				$output = GFA_File_Renderer::render_link( $file, $base_url, $options );
			}
		} else if ( isset( $options['group'] ) && ( $options['group'] !== null ) ) {
			
			$file_group_where = '';
			$group_ids        = array();
			$groups           = array_map( 'trim', explode( ',', trim( $options['group'] ) ) );
			if ( in_array( '*', $groups ) ) {
				// files for all groups
				$group_table = _groups_get_tablename( 'group' );
				$_groups = $wpdb->get_results( "SELECT group_id FROM $group_table" );
				foreach( $_groups as $_group ) {
					$group_ids[] = $_group->group_id;
				}
			} else {
				foreach( $groups as $group ) {
					$the_group = Groups_Group::read_by_name( $group );
					if ( !$the_group ) {
						if ( is_numeric( $group ) ) {
							$the_group = Groups_Group::read( $group );
						}
					}
					if ( $the_group ) {
						$group_ids[] = $the_group->group_id;
					}
				}
			}
			if ( count( $group_ids )  > 0 ) {
				$file_group_where = ' WHERE group_id IN ( ' . implode( ',', $group_ids ) . ' ) ';
				$file_table       = _groups_get_tablename( 'file' );
				$file_group_table = _groups_get_tablename( 'file_group' );
				$order = strtoupper( isset( $options['order'] ) ? trim( $options['order'] ) : 'ASC' );
				switch ( $order ) {
					case 'ASC' :
					case 'DESC' :
						break;
					default :
						$order = 'ASC';
				}
				$orderby = isset( $options['orderby'] ) ? trim( $options['orderby'] ) : 'name';
				switch( $orderby ) {
					case 'file_id' :
					case 'name' :
					case 'description' :
					case 'path' :
					case 'max_count' :
						break;
					default :
						$orderby = 'name';
				}
				$description = isset( $options['description'] ) ? strtolower( trim( $options['description'] ) ) : 'no';
				switch ( $description ) { 
					case 'yes' :
					case 'true' :
					case '1' :
						$show_description = true;
						break;
					default :
						$show_description = false;
				}
				if ( $file_ids = $wpdb->get_results(
					"SELECT * FROM $file_table WHERE file_id IN ( SELECT file_id FROM $file_group_table $file_group_where ) ORDER BY $orderby $order"
				) ) {
					$visibility = $options['visibility'];
					$list_prefix = html_entity_decode( !empty( $options['list_prefix'] ) ? $options['list_prefix'] : '' );
					$list_suffix = html_entity_decode( !empty( $options['list_suffix'] ) ? $options['list_suffix'] : '' );
					$item_prefix = html_entity_decode( !empty( $options['item_prefix'] ) ? $options['item_prefix'] : '' );
					$item_suffix = html_entity_decode( !empty( $options['item_suffix'] ) ? $options['item_suffix'] : '' );
					unset( $options['file_id'] );
					unset( $options['group'] );
					unset( $options['order'] );
					unset( $options['visibility'] );
					unset( $options['list_prefix'] );
					unset( $options['list_suffix'] );
					unset( $options['item_prefix'] );
					unset( $options['item_suffix'] );
					$base_url = get_bloginfo( 'url' );
					$output .= $list_prefix;
					foreach ( $file_ids as $file_id ) {
						$file_id = $file_id->file_id;
						switch( $visibility ) {
							case self::ALWAYS :
								$can_see = true;
								break;
							default :
								$user_id = get_current_user_id();
								$can_see = Groups_File_Access::can_access( $user_id, $file_id );
						}
						if ( $can_see ) {
							$file = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $file_table WHERE file_id = %d", $file_id ) );
							$output .= $item_prefix;
							$output .= $show_description ? '<div class="name">' : '';
							$output .= GFA_File_Renderer::render_link( $file, $base_url, $options );
							$output .= $show_description ? '</div>' : '';
							if ( $show_description ) {
								$output .= '<div class="description">';
								$output .= self::groups_file_info( array(
									'file_id'    => $file_id,
									'visibility' => $visibility,
									'show'       => 'description',
									'filter'     => $options['description_filter']
								) );
								$output .= '</div>';
							}
							$output .= $item_suffix;
						}
					}
					$output .= $list_suffix;
				}
				
			}
		}
		return $output;
	}
	
	/** 
	 * Shows enclosed content if the current user can access the file.
	 *
	 * @param array $atts attributes - must provide the "file_id"
	 * @param string $content content to render
	 */
	public static function groups_can_access_file( $atts, $content = null ) {
		$output = "";
		$options = shortcode_atts( array( "file_id" => null ), $atts );
		if ( $content !== null ) {
			if ( $options['file_id'] !== null ) {
				$file_id = intval( $options['file_id'] );
				$user_id = get_current_user_id();
				if ( Groups_File_Access::can_access( $user_id, $file_id ) ) {
					remove_shortcode( 'groups_can_access_file' );
					$content = do_shortcode( $content );
					add_shortcode( 'groups_can_access_file', array( __CLASS__, 'groups_can_access_file' ) );
					$output = $content;
				}
			}
		}
		return $output;
	}
	
	/**
	 * Shows enclosed content if the current user can not access the file.
	 *
	 * @param array $atts attributes - must provide the "file_id"
	 * @param string $content content to render
	 */
	public static function groups_can_not_access_file( $atts, $content = null ) {
		$output = "";
		$options = shortcode_atts( array( "file_id" => null ), $atts );
		if ( $content !== null ) {
			if ( $options['file_id'] !== null ) {
				$file_id = intval( $options['file_id'] );
				$user_id = get_current_user_id();
				if ( !Groups_File_Access::can_access( $user_id, $file_id ) ) {
					remove_shortcode( 'groups_can_not_access_file' );
					$content = do_shortcode( $content );
					add_shortcode( 'groups_can_not_access_file', array( __CLASS__, 'groups_can_not_access_file' ) );
					$output = $content;
				}
			}
		}
		return $output;
	}
	
	/**
	 * Renders file information.
	 * 
	 * Attributes:
	 * - "file_id" : id of the file
	 * - "visibility" : "can_access" (default) or "always"
	 * - "show" : "name", "description", "count", "max_count", "remaining", "file_id", "size", "sizeb"
	 *
	 * @param array $atts attributes
	 * @param string $content not used
	 * @return rendered file information
	 */
	public static function groups_file_info( $atts, $content = null ) {
		global $wpdb;
		$output = "";
		$options = shortcode_atts(
			array(
				'file_id'    => null,
				'visibility' => self::$visibility,
				'show'       => 'name',
				'filter'     => 'wp_filter_kses'
			),
			$atts
		);
		if ( $options['file_id'] !== null ) {
			$file_id = intval( $options['file_id'] );
			$user_id = get_current_user_id();
			$can_see = false;
			switch( $options['visibility'] ) {
				case self::ALWAYS :
					$can_see = true;
					break;
				default :
					$can_see = Groups_File_Access::can_access( $user_id, $file_id );
			}
			if ( $can_see ) {
				$file_table = _groups_get_tablename( 'file' );
				$file = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $file_table WHERE file_id = %d", $file_id ) );
				switch ( $options['show'] ) {
					case 'description' :
						$output = $file->description;
						break;
					case 'count' :
						$output = Groups_File_Access::get_count( $user_id, $file_id );
						break;
					case 'max_count' :
						$output = Groups_File_Access::get_max_count( $file_id );
						break;
					case 'remaining' :
						$remaining = Groups_File_Access::get_remaining( $user_id, $file_id );
						if ( $remaining !== INF ) {
							$output = $remaining;
						} else {
							$output = '&infin;';
						}
						break;
					case 'file_id' :
						$output = intval( $file_id );
						break;
					case 'size' :
					case 'sizeb' :
						if ( $size = @filesize( $file->path ) ) {
							if ( $options['show'] !== 'sizeb' ) {
								$units = 'BKMGTP';
								$power = floor( ( strlen( $size ) - 1 ) / 3 );
								if ( $power > strlen( $units ) - 1 ) {
									$power = strlen( $units ) - 1;
								}
								$output = sprintf(
									"<span class='size'>%.2f</span> <span class='unit'>%s</span>",
									$size / pow( 1024, $power ),
									@$units[$power] . 'B'
								);
							} else {
								$output = sprintf(
									"<span class='size'>%d</span> <span class='unit'>%s</span>",
									$size,
									'B'
								);
							}
						}
						break;
					default :
						$output = $file->name;
				}
				switch ( $options['filter'] ) {
					case '' :
					case 'none' :
						$output = stripslashes( $output );
						break;
					default :
						if ( function_exists( $options['filter'] ) ) {
							$output = call_user_func( $options['filter'], $output );
							$output = stripslashes( $output );
						}
				}
				
			}
		}
		return $output;
	}
	
	/**
	 * Allows to switch the default visibility setting for shortcodes handled by GFA_Shortcodes.
	 * 
	 * @param array $atts attributes must specify the "visibility" with allowed values "always", "can_access"
	 * @param string $content not used
	 */
	public static function groups_file_visibility( $atts, $content = null ) {
		$options = shortcode_atts( array( "visibility" => self::$visibility ), $atts );
		switch( $options['visibility'] ) {
			case self::ALWAYS :
			case self::CAN_ACCESS :
				self::$visibility = $options['visibility'];
				break;
		}
	}
	
	/**
	 * Render the service key for the current user.
	 * 
	 * @param array $atts
	 * @param string  $content not used
	 * @return string
	 */
	public static function groups_file_access_service_key( $atts, $content = null ) {
		$output = '';
		$user_id = get_current_user_id();
		if ( $user_id ) {
			$service_key = get_user_meta( $user_id, 'gfa_service_key', true );
			if ( !$service_key ) {
				$service_key = md5( time() );
				add_user_meta( $user_id, 'gfa_service_key', $service_key );
			}
			$output = $service_key;
		}
		return $output;
	}
}
GFA_Shortcodes::init();
