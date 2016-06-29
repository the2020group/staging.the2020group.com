<?php
/**
 * groups-file-access.php
 * 
 * Copyright (c) 2012-2014 "kento" Karim Rahimpur www.itthinx.com
 * 
 * =============================================================================
 * 
 *                             LICENSE RESTRICTIONS
 * 
 *           This plugin is provided subject to the license granted.
 *              Unauthorized use and distribution is prohibited.
 *                     See COPYRIGHT.txt and LICENSE.txt.
 * 
 * This plugin relies on code that is NOT licensed under the GNU General
 * Public License. Files licensed under the GNU General Public License state
 * so explicitly in their header. 
 * 
 * =============================================================================
 * 
 * You MUST be granted a license by the copyright holder for those parts that
 * are not provided under the GPLv3 license.
 * 
 * If you have not been granted a license DO NOT USE this plugin until you have
 * BEEN GRANTED A LICENSE.
 * 
 * Use of this plugin without a granted license constitutes an act of COPYRIGHT
 * INFRINGEMENT and LICENSE VIOLATION and may result in legal action taken
 * against the offending party.
 * 
 * Being granted a license is GOOD because you will get support and contribute
 * to the development of useful free and premium themes and plugins that you
 * will be able to enjoy.
 * 
 * Thank you!
 * 
 * Visit www.itthinx.com for more information.
 * 
 * =============================================================================
 * 
 * This code is released under the GNU General Public License.
 * 
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * 
 * This header and all notices must be kept intact.
 * 
 * @author Karim Rahimpur
 * @package groups-file-access
 * @since groups-file-access 1.0.0
 *
 * Plugin Name: Groups File Access
 * Plugin URI: http://www.itthinx.com/plugins/groups-file-access
 * Description: Groups File Access allows to restrict user access to files by group membership.
 * Author: itthinx
 * Author URI: http://www.itthinx.com
 * Version: 1.5.0
 */
define( 'GFA_PLUGIN_VERSION', '1.5.0' );
define( 'GFA_PLUGIN_DOMAIN', 'groups-file-access' );
define( 'GFA_PLUGIN_FILE', __FILE__ );
define( 'GFA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
if ( defined( 'UPLOADS' ) ) {
	global $wpdb;
	define( 'GFA_UPLOADS_DIR', untrailingslashit( WP_CONTENT_DIR ) . "/blogs.dir/{$wpdb->blogid}/files/groups-file-access" );
} else {
	define( 'GFA_UPLOADS_DIR', untrailingslashit( WP_CONTENT_DIR ) . '/uploads/groups-file-access' );
}
define( 'GFA_FILE', __FILE__ );
define( 'GFA_DIR', WP_PLUGIN_DIR . '/groups-file-access' );
define( 'GFA_CORE_LIB', GFA_DIR . '/lib/core' );
require_once( GFA_CORE_LIB . '/boot.php' );
