<?php
define( 'WP_CACHE', true ); // Added by WP Rocket


 // Added by WP Rocket

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
define('FS_METHOD', 'direct');

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'demoeduma_db' );

/** Database username */
define( 'DB_USER', 'demoeduma_db' );

/** Database password */
define( 'DB_PASSWORD', 'tuan@140902' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );


/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'gnasi9wupyhz6wftxjbdcny7btdxpcmkvlakmig7melj9mwj0io3zbus1yq1rw3d' );
define( 'SECURE_AUTH_KEY',  'vglgx98b6hhzupjwzaycsii9vum7lqrytju2sputgxqo4gfupdcdls3dl5dfnfbu' );
define( 'LOGGED_IN_KEY',    'nyavoxkanxpukm4oknn3wrm6gozr90zcndosij3suypr4j33tkamav0cgrileucc' );
define( 'NONCE_KEY',        'b71hu14itpxmfesuu79sjsqfrsjauvu7gtlsf9k26xreqql60ugsfhijxhccjjab' );
define( 'AUTH_SALT',        'gp2u14qounw0r23ly3hq1tqailfpkluwed90yodsg8o8foeprcze42fvckelhegp' );
define( 'SECURE_AUTH_SALT', 'c4bczakeedip0f5sxuk1cfbn2nvycf1exl9ewglhzkrh9dhigh6prbkuhko9kvnn' );
define( 'LOGGED_IN_SALT',   'q6wo5nniuty1aexvtppkinqyhaiu7fzxfolhr6v7trnaxtyvongld9crul2xtc3e' );
define( 'NONCE_SALT',       'rugu0ekrtthn7tr14rhisqq5qq1c8ev6w6kf6ki9tnksjqyowkrxysiww4swjgsv' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wpuq_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
