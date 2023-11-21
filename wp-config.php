<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

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
define( 'AUTH_KEY',          '$9L86/|?R|!3j)XytbmpXns5Y&A-aobq0nb)[zwchAVq&Y<`L]3H!1/d8n+R+u5/' );
define( 'SECURE_AUTH_KEY',   ' |@:a|2>&_`0pfF$t3F_&_Q4^Z<2Y{X@$TSnZRZ}g =Qt7)&2<-Yk,MV|Xn|IrCv' );
define( 'LOGGED_IN_KEY',     ',t2VO9e0C _^3twvQ|e`lp7<()pTqV[{n6VAM^vPx]8!X4E I&ac.n:GYwL_6eBt' );
define( 'NONCE_KEY',         '0G[^Sh2s8jDM#u-8~ih@GduL/coTy9OmLt/du{mVu^+nDu22jS;>s>Orl4?poC#*' );
define( 'AUTH_SALT',         '%PtSb)m&0vd4R&`5hgU}}6J2ii|^uJu`fwH(rR^?r>#U51!Adt(~(8!.@L;aVuM,' );
define( 'SECURE_AUTH_SALT',  'D;tFR (EA=U4:5rJ/Zf.{5*v_=vznAljdl(GIR@$g@y~Be lM48D#%Zkd|8b=]X_' );
define( 'LOGGED_IN_SALT',    'I0#> -:`+bZ^rg|3lpc*{EhO>~w1+A9>x#cFK0,U_MABd/Uni(@>j)f0n6;,w7m0' );
define( 'NONCE_SALT',        'D*ON?<46m9j L5N5Cibp71fYv^mARpX)EK#0G.Yb}<`,X@VOFO/(UBo6<]:tz(4]' );
define( 'WP_CACHE_KEY_SALT', '7_=g~Ii7392Pn1>}D?jLd$QpeNCIVCin6S(43y+*7xRqTUB{C[!v)T$t. Y|q(uU' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
