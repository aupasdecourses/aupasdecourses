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
define('DB_NAME', 'aupasdecourses_faq');
/** MySQL database username */
define('DB_USER', 'aupasdecourses_user');
/** MySQL database password */
define('DB_PASSWORD', 'aupasdecourses_db_pw2017');
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
define('AUTH_KEY',         '!FlO?c+E2H5fi:TXM8d-rHrkD1-3~Hw1:h;Yg/p&_vE20}<qY=A8*ru-O^GjX[hg');
define('SECURE_AUTH_KEY',  '3m(K ^3M}Z@wd~,s+@+VWDN{a%bN+zlq#^6%9v8W!-da|&=Q@v<*,3WW6o(X+tt<');
define('LOGGED_IN_KEY',    '<PMR_o@7nC=48/h|,?3bd@|P[ ;})*f(:V@MeP3J6U6-`26Nf87rs(ZrC^i Y;>)');
define('NONCE_KEY',        'Cwx_PL}]~-Ww/Lv=_.qX=pcL7,.fgKT8^,==)9O^b1HS-o@qRCj|?E&#aF1JJj|[');
define('AUTH_SALT',        'p)KKc.Q$a.Rw`/h1%c|oI~9yYvLws![$N36pDa&fVn_yX#-RV?(a_avIQW-^NB!k');
define('SECURE_AUTH_SALT', '92<Gc/=3xwVdm|$iT!tv4sM:NG+$NI!cmMuv;DBc)vIR;zt,H|_XP||}XBXhSyV.');
define('LOGGED_IN_SALT',   '-0N^fWj-u^C)F5b!JSv$m%4js uk$psS/L%@-V&ZJf-xG))1~&:OQW8[>-H)-NqX');
define('NONCE_SALT',       '*rFzH7,Z:wQ4B5L|vDD:.8?{8RqUa?qJTEiEGuAzltHcYr2EOr^q.N5y1Rwo}=+Y');
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

