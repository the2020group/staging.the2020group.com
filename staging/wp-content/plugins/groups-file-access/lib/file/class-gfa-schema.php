<?php
/**
 * class-gfa-schema.php
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
 * GFA schema definition.
 */
class GFA_Schema {

	private static $version = 1;

	// Previously description and path had DEFAULT '' but this gives
	// inconsistent results depending on platform; warning on *nix, error
	// on Windows.
	// See http://bugs.mysql.com/bug.php?id=21532
	// Even today 2013-07-04, quoting from the latest
	// http://dev.mysql.com/doc/refman/5.7/en/blob.html
	// "BLOB and TEXT columns cannot have DEFAULT values."
	private static $schema =
		array(
			'file' =>
				"
				file_id       BIGINT(20) UNSIGNED NOT NULL auto_increment,
				name          VARCHAR(255) NULL DEFAULT '',
				description   TEXT NULL,
				path          TEXT NOT NULL,
				max_count     INT NOT NULL DEFAULT 0,
				PRIMARY KEY   (file_id),
				INDEX         gfa_f_n (name(20)),
				INDEX         gfa_f_d (description(20)),
				INDEX         gfa_f_p (path(20))
				",
			'file_group' =>
				"
				file_id       BIGINT(20) UNSIGNED NOT NULL,
				group_id      BIGINT(20) UNSIGNED NOT NULL,
				PRIMARY KEY   (file_id,group_id)
				",
			'file_access' =>
				"
				file_id       BIGINT(20) UNSIGNED NOT NULL,
				user_id       BIGINT(20) UNSIGNED NOT NULL,
				count         INT NOT NULL DEFAULT 0,
				PRIMARY KEY   (file_id,user_id)
				"
		);

	public static function get_schema() {
		return self::$schema;
	}

	public static function get_version() {
		return self::$version;
	}
}