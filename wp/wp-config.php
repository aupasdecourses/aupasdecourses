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
define('DB_NAME', 'apdcdev');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'password');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1');

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
define('AUTH_KEY',         'Kk-puhn|5oj@1kYoWOJoLheCY!=u|oKKY)MF?HPzZ%Ioe<{-P] ?I476?aY=+x,f');
define('SECURE_AUTH_KEY',  '{7U}YtunQ|2u}qmmg?U~%EkRw=%-,{}W2l %ABIaz,Zkcc8&**TlZ32m3Cqw2UmH');
define('LOGGED_IN_KEY',    'K+J8t[II+p7[_y Id4pYnI[z;w#qU@=25GIM9AYcBJhT[hC}r4]}wCy5$&/xuE+2');
define('NONCE_KEY',        'O-(ey^j[fXYfK%D@M7Ht`~DaO`tK$5](Z-fV<X=&qgO2V)hCFi:S2T%<Yk1/5`%8');
define('AUTH_SALT',        'dvhYs]8PO=x%K~_k1Go^me0Q>+@A=Xwgd~:C5G=!E,_eeNzu4 *4b&/<MPnT#N`T');
define('SECURE_AUTH_SALT', '(s}HO!B,q~{CGg]D=WpEttP&28EN`{H`kh%D@u&N52C^5t]MhN0}O2f`sysPSrAd');
define('LOGGED_IN_SALT',   'ze9<CF$&fF1l!7zEA>[jVKtz,^K55,N^l WG.Y3,2-@i&Kx+<lQ%[Uy:n4o+M?,)');
define('NONCE_SALT',       'X1&;om3bBQ6$4c?Gi*9wpC]Aea?S=+ar+fl{3.AOqhCZDcO;9m@&.3~1twqBhcsL');

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

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
