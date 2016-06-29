<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'the2020g_wp409');

/** MySQL database username */
define('DB_USER', 'the2020g_wp409');

/** MySQL database password */
define('DB_PASSWORD', '!P3Wn!9S07');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'wk98ece0tku7qjblsakptnrgtgjgrhq0s5amzdo7oglx2qdjhemiu4gwynmzcn9h');
define('SECURE_AUTH_KEY',  '4xsjk2zos2yriwgvz6oncihyjtfhprjaacv5v3xkgfmpmze3x9yqgeksu2rpug4p');
define('LOGGED_IN_KEY',    '0bnqddba1agn1lnviili8d9smbgbvgfdarhsjqppnjtg36dsgtaxvds9wpie29ss');
define('NONCE_KEY',        'nvzb2s9ev3yl8meicyoevrtboojdlyb8gpzvzbqd4c36ic4s9l3pw4da589k9hsj');
define('AUTH_SALT',        '860ue11qm7jbeikpbivpi0gsyi8bgyox3jzzmhvqrqicuvfapfhievrzqogtpr3n');
define('SECURE_AUTH_SALT', 'aisxxfrv47obsopb4awi4xmziegpjw4j6pmav9rzfdocd88debz93k4qkhoft6mg');
define('LOGGED_IN_SALT',   'po7w29l3yc5p6jh5wueulvphau3fljrpkduyyhpfb0i1fzhsu2d4wqdrcblaggqh');
define('NONCE_SALT',       'b38csz2fbcq6bqtgeqecdhjrrytfho3o9wp7dv99xaxetgrkaumk8p9k4bm7rhun');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);
define( 'WP_MEMORY_LIMIT', '128M' );
define( 'WP_AUTO_UPDATE_CORE', false );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
