<?php
/**
 * i-groups-file-access.php
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
 * GFA base interface.
 */
interface I_Groups_File_Access {
	
	// general
	const PLUGIN_OPTIONS        = 'groups_file_access';
	const NONCE                 = 'gfa_nonce';
	const SET_ADMIN_OPTIONS     = 'set_admin_options';
	const FOLDERS               = 'folders';
	const LOGIN_REDIRECT        = 'login_redirect';
	const DELETE_DATA           = 'delete_data';
	const DELETE_DATA_ON_DEACTIVATE         = 'delete_data_on_deactivate';
	const NETWORK_DELETE_DATA_ON_DEACTIVATE = 'network_delete_data_on_deactivate';
	const DISMISS_HELP          = 'dismiss_help';
	const KEY                   = 'key';
	// sanity
	const SCHEMA_UPDATED        = 'schema_updated';
	const SCHEMA_VERSION        = 'schema_version';
	// email notifications
	const NOTIFY_ADMIN          = 'notify_admin';
	const NOTIFY_ADMIN_DEFAULT  = true;
	const ADMIN_SUBJECT         = 'admin_subject';
	const ADMIN_DEFAULT_SUBJECT = "File accessed at [site_title]";
	const ADMIN_MESSAGE         = 'admin_message';
	const ADMIN_DEFAULT_MESSAGE =
"The file [file_path] has been accessed through [file_url] at <a href='[site_url]'>[site_title]</a> by the user [username].<br/>
<br/>
[site_title]<br/>
[site_url]<br/>
";
	const SERVICE_ACTION_PROBE = 0;
	const SERVICE_ACTION_SERVE = 1;

	const APPLY_MIME_TYPES         = 'apply_mime_types';
	const APPLY_MIME_TYPES_DEFAULT = true;

	const SESSION_ACCESS         = 'session_access';
	const SESSION_ACCESS_DEFAULT = false;
	const SESSION_ACCESS_TIMEOUT = 'session_access_timeout';
	const SESSION_ACCESS_TIMEOUT_DEFAULT = 60;

	const CONTENT_DISPOSITION            = 'content_disposition';
	const CONTENT_DISPOSITION_ATTACHMENT = 'attachment';
	const CONTENT_DISPOSITION_INLINE     = 'inline';
	const CONTENT_DISPOSITION_DEFAULT    = self::CONTENT_DISPOSITION_ATTACHMENT;
}
