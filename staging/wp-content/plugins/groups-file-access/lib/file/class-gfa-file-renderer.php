<?php
/**
 * class-gfa-file-renderer.php
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

/**
 * File renderer.
 * 
 * Supports (single) byte range requests as of 1.5.0.
 * @see http://tools.ietf.org/id/draft-ietf-http-range-retrieval-00.txt
 */
class GFA_File_Renderer {
	
	const PARAMETER = 'gfid';
	public static $bsize = 524288;

	public static function probe( $file, $base_path ) {
		$result = false;
		$protocol = $_SERVER["SERVER_PROTOCOL"];
		if ( 'HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol ) {
			$protocol = 'HTTP/1.0';
		}
		require_once( GFA_FILE_LIB . '/class-gfa-file-upload.php' );
		if ( isset( $file->path) &&
			file_exists( $file->path ) &&
			( strpos( $file->path, GFA_File_Upload::path_filter( $base_path ) ) === 0 )
		) { 
			header( "$protocol 200 OK" );
			$result = true;
		} else {
			header( "$protocol 404 Not Found" );
		}
		return $result;
	}

	public static function serve( $file, $base_path ) {

		$result = false;

		$protocol = $_SERVER["SERVER_PROTOCOL"];
		if ( 'HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol ) {
			$protocol = 'HTTP/1.0';
		}
		require_once( GFA_FILE_LIB . '/class-gfa-file-upload.php' );
		require_once( GFA_UTY_LIB . '/class-gfa-utility.php' );
		if ( isset( $file->path) &&
			file_exists( $file->path ) &&
			( strpos( $file->path, GFA_File_Upload::path_filter( $base_path ) ) === 0 )
		) { 
			@ini_set( 'zlib.output_compression', 'Off' );
			if ( !ini_get( 'safe_mode' ) ) {
				set_time_limit( 0 );
			}

			$filesize = @filesize( $file->path );
			$start    = 0;
			$end      = $filesize - 1;

			if ( isset( $_SERVER['HTTP_RANGE'] ) ) {
				$http_range = explode( '=', $_SERVER['HTTP_RANGE'] );
				if ( count( $http_range ) > 1 ) {
					list( $uom, $range_specification ) = $http_range;
					if ( $uom == 'bytes' ) {
						$range = array_shift( explode( ',', $range_specification, 2 ) );
						$r = explode( '-', $range, 2 );
						$start = isset( $r[0] ) ? $r[0] : null;
						$end   = isset( $r[1] ) ? $r[1] : null; 
						$start = !empty( $start ) ? max( 0, intval( $start ) ) : 0;
						$end   = !empty( $end ) ? min( intval( $end ), $filesize - 1 ) : $filesize - 1;
					}
				}
			}

			$is_range = false;
			if ( ( $start > 0 ) || ( $end < ( $filesize - 1 ) ) ) {
				header( "$protocol 206 Partial Content" );
				header( 'Content-Length: ' . ( $end - $start + 1 ) );
				header( sprintf( 'Content-Range: bytes %d-%d/%d', $start, $end, $filesize ) );
				$is_range = true;
			} else {
				header( "$protocol 200 OK" );
				if ( $filesize ) {
					header( 'Content-Length: ' . $filesize );
				}
			}

			header( 'Accept-Ranges: bytes' );
			header( 'Pragma: no-cache');
			header( 'Cache-Control: no-cache, no-store' );
			header( 'Expires: 0');
			header( 'X-Robots-Tag: noindex, nofollow' );
			require_once GFA_CORE_LIB . '/i-groups-file-access.php';
			$options = get_option( I_Groups_File_Access::PLUGIN_OPTIONS , array() );
			$apply_mime_types  = isset( $options[I_Groups_File_Access::APPLY_MIME_TYPES] ) ? $options[I_Groups_File_Access::APPLY_MIME_TYPES] : I_Groups_File_Access::APPLY_MIME_TYPES_DEFAULT;
			if ( $apply_mime_types && ( $mime_type = GFA_Utility::get_mime_type( $file->path ) ) ) {
				header( sprintf( 'Content-Type: %s', $mime_type ) );
			} else {
				header( 'Content-Type: application/octet-stream' );
			}

			// indicate that no encoding is applied @see http://tools.ietf.org/html/rfc2045
			header( 'Content-Transfer-Encoding: binary' );

			$filename = gfa_basename( $file->path );
			$content_disposition = isset( $options[I_Groups_File_Access::CONTENT_DISPOSITION] ) ? $options[I_Groups_File_Access::CONTENT_DISPOSITION] : I_Groups_File_Access::CONTENT_DISPOSITION_DEFAULT;

			header( sprintf( 'Content-Disposition: %s; filename="%s"', $content_disposition, $filename ) );

			if ( $is_range ) {
				$result = self::buffered_read_serve( $file->path, $start, $end );
			} else {
				$result = self::buffered_read_serve_all( $file->path );
			}

		} else {
			header( "$protocol 404 Not Found" );
		}
		return $result;
	}

	private static function buffered_read_serve( $path, $start = null, $end = null ) {
		@ob_start();
		$read = 0;
		$filesize = @filesize( $path );
		if ( $start === null ) {
			$start = 0;
		}
		if ( $end === null ) {
			$end = $filesize;
		}
		$bytes = $end - $start;
		if ( $h = @fopen( $path, 'rb' ) ) {
			@fseek( $h, $start );
			while( !@feof( $h ) && ( $read < $bytes ) ) {
				$s = @fread( $h, self::$bsize );
				$read += strlen( $s );
				echo $s;
				@ob_flush();
				@flush();
			}
			@fclose( $h );
		}
		@ob_end_clean();
		if ( $filesize === $read ) {
			return $read;
		} else {
			return false;
		}
	}

	private static function buffered_read_serve_all( $path ) {
		@ob_start();
		$read = 0;
		$filesize = @filesize( $path );
		if ( $h = @fopen( $path, 'rb' ) ) {
			while( !@feof( $h ) ) {
				$s = @fread( $h, self::$bsize );
				$read += strlen( $s );
				echo $s;
				@ob_flush();
				@flush();
			}
			@fclose( $h );
		}
		@ob_end_clean();
		if ( $filesize === $read ) {
			return $read;
		} else {
			return false;
		}
	}

	/**
	 * File URL renderer.
	 * 
	 * Session Access : Unless session_access is explicitly enabled by
	 * indicating it in $options (true, 'yes' or 'true'), no gfsid will be
	 * generated.
	 * 
	 * @param string $file
	 * @param string $base_url
	 * @param array $options
	 * @return string file URL
	 */
	public static function render_url( $file, $base_url, $options = array() ) {
		$separator = '?';
		$url_query = parse_url( $base_url, PHP_URL_QUERY );
		if ( !empty( $url_query ) ) {
			$separator = '&';
		}
		$parameter      = isset( $options['parameter'] ) ? $options['parameter'] : self::PARAMETER;
		$session_access = isset( $options['session_access'] ) ? $options['session_access'] : 'no';
		if ( $session_access === true ) {
			$session_access = 'true';
		}
		switch( $session_access ) {
			case 'yes' :
			case 'true' :
				$session_access = true;
				break;
			default :
				$session_access = false;
		}
		if ( $session_access ) {
			$s = new Groups_File_Access_Session( $base_url . $separator . $parameter . '=' . $file->file_id );
			return $s->get_url();
		} else {
			return $base_url . $separator . $parameter . '=' . $file->file_id;
		}
	}

	public static function render_link( $file, $base_url, $options = array(), $url_options = array() ) {
		$defaults = array(
			'accesskey' => null,
			'alt'       => $file->name,
			'charset'   => null,
			'coords'    => null,
			'class'     => 'groups-file-access',
			'dir'       => null,
			'href'      => self::render_url( $file, $base_url, $url_options ),
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
			'title'     => $file->name
		);
		$link = '<a ';
		foreach ( $defaults as $key => $value ) {
			if ( $key != 'href' && isset( $options[$key] ) ) {
				$value = $options[$key];
			}
			if ( $value !== null ) {
				$link .= $key . '="' . $value . '" ';
			}
		}
		$link .= '>';
		$link .= stripslashes( $file->name );
		$link .= '</a>';
		return $link;
	}
}
