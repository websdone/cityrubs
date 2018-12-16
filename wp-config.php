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
define('DB_NAME', 'mariana');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost:8089');

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
define('AUTH_KEY',         '4a-;RR@4I;G3f_{oKIgaWU01I_[]jCme[+3~u:,wNMK<#ek0w >yaN[J<L6EB2sq');
define('SECURE_AUTH_KEY',  'L4[OKO%Sv0!ccOq$:i-{:J}9efw}I?Q8?WmGWs(iEV/_yfzZemNOV]eF/UzVg{CG');
define('LOGGED_IN_KEY',    '2Pdg4dgj`*AYzNBP,x(=,}ra@zzf@|vn.!C(L>sqD#o~Yxv$hw^UU2DQ>4{34BF2');
define('NONCE_KEY',        'L(fQqTU#-4y?{lS@Jdn]syt2SgV=)OrGlw~X ahha<hnNJ`FvCmqeA,9u(~{*MPp');
define('AUTH_SALT',        's)d5#9`/@W,^k+SAU<S0EVnG9Akg|O3H]8In+VbdoTKMvBOCy0tD=f.!9J,y,{,!');
define('SECURE_AUTH_SALT', ':#FDUO=s}23[27q|D,Q[;c%xOOitlPDp|U]#AsY)Cu6w?ntCVNCa$mXYva/cSy4X');
define('LOGGED_IN_SALT',   ' ,0zN~rrc/rKyD/nf.e^1:S+rArPL+BwzM;5:7Hd1!G8j}4|kdFO~K]aNkWvh}c)');
define('NONCE_SALT',       'b^%(ZE}t$&Ow)$Z1eM4]*-Vlv<uhmi~b zw7L9D!wx]}[cXZ{xl,$gr/XaBYo?gs');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'mg_';

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
