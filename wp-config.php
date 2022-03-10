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

/**
 * Database connection information is automatically provided.
 * There is no need to set or change the following database configuration
 * values:
 *   DB_HOST
 *   DB_NAME
 *   DB_USER
 *   DB_PASSWORD
 *   DB_CHARSET
 *   DB_COLLATE
 */

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */

define('AUTH_KEY',         'zR3$Kgf4F$ZNpdbV04<VemakVLsO[)MeaRWbSR0XdSj%eVGkoTCNr}kOG5?GN$8]');
define('SECURE_AUTH_KEY',  'fDx#8+}T0Eb0kjJ2V3.j)Zf.uE>)m,M>mZka^-9MD=qHZYOs9{IN<}$^Y_!cX3x<');
define('LOGGED_IN_KEY',    '<U!|oe$M#N;5xcoO.J{hqC},t{29+A5B>#E<HKUcQkfl74MdX_zNt?<k>G]pErgY');
define('NONCE_KEY',        'gzRU%qVVPeb<3CRl(kDv]cp_C(,A*<KtbY8<*tiuE.~hjT_CE2pkH}Fn%h!KNh:e');
define('AUTH_SALT',        'l(9F|U@<xjgn<rajN_{VdZLi$^Nw8Pa0z{Z?v6%~?p#dC6?fT%|GXZw*Y1:|^s::');
define('SECURE_AUTH_SALT', 'hpHRE1Ui#P{W+2<XP@J:B]Jnj(O;BGgYlz<0CS}lq(^6d)L*sA~ypzyH||O!=8d4');
define('LOGGED_IN_SALT',   'KWH:sYze2hO[WHA-s7G77WN_UlJ-X0<VFf<ze^#q%{y;QIrYH}:<.gNOv~ig#Cei');
define('NONCE_SALT',       ']Cr[ISR[v<e,SUj59u=+fr{h5?}(E0wKf;VsZ_D=xn;zFM#A:w?hDrb3JvHb<@A2');

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
define('WP_DEBUG', true);
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
  define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
