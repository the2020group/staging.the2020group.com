<?php
/**
* boot.php
*
* Copyright (c) "kento" Karim Rahimpur www.itthinx.com
*
* This code is provided subject to the license granted.
* Unauthorized use and distribution is prohibited.
* See COPYRIGHT.txt and LICENSE.txt
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
*/
define( 'GFA_ADMIN_LIB', GFA_DIR . '/lib/admin' );
define( 'GFA_FILE_LIB', GFA_DIR . '/lib/file' );
define( 'GFA_UTY_LIB', GFA_DIR . '/lib/uty' );
define( 'GFA_VIEWS_LIB', GFA_DIR . '/lib/views' );

/**
 * basename() alternative - PHP's basename() will remove multibyte
 * characters that prefix the $path.
 *
 * @param string $path
 * @return string
 */
function gfa_basename( $path ) {
	// unify
	$path = str_replace( "\\", "/", $path );
	// last /
	if ( function_exists( 'mb_strrpos' ) ) {
		$k = mb_strrpos( $path, "/" );
		if ( $k !== false ) {
			$path = mb_substr( $path, $k + 1 );
		}
	} else {
		$k = strrpos( $path, "/" );
		if ( $k !== false ) {
			$path = substr( $path, $k + 1 );
		}
	}
	return $path;
}

require_once( GFA_CORE_LIB . '/class-groups-file-access.php' );
