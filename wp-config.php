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
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'bd_test' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'p=0b03ZI@Ag1zLpNvFnCa}(i]ri+}oT]@$;!,FEK6A9Tc8/OHCWxKl7Mv&WsHko8' );
define( 'SECURE_AUTH_KEY',  'Iu>(!}kN-HJE@IXpp.9V-_.%aGehof)Vu^6U$C4EyV.lN-!cTB84cpJ[%<]yj?-_' );
define( 'LOGGED_IN_KEY',    'Q=)B@a-wy<*@ju!K<%&t]$eZ=>?t!39C&:8tF 80Bk{m*pG3%fEbd&Rgk8d},Y.r' );
define( 'NONCE_KEY',        ';oDH14HI7B1O<!W-LAM,?hmcbFso#]2qNTdFPm]#PPZixDlj]2!C=Sr(F+-eT~3R' );
define( 'AUTH_SALT',        '>1!_d0`MrV[=?gavz.<,LX]JZ@a{U}:6=Y?sWvS {=L2M&F,9A?fKv%i@IEhjlm%' );
define( 'SECURE_AUTH_SALT', '>^C/ /9>G]p}aaBZob?;3HBo1;<DD9wfoq BI`bryP Cmc%U (.[nda[ubnI@=VE' );
define( 'LOGGED_IN_SALT',   'T;0H?B(W3YLc/:Bo(y;APz!LB9EI+,UO=RIFPkSlz(KI!!y9lcp.HwT{Km~Rt];M' );
define( 'NONCE_SALT',       'Z2`$Eqsl^iU*bQxtB>:W)Y6*To$pRE4srz29_Dkx2MQLt{X N9@<(zM!*:1OAw_l' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', false );

set_time_limit(300);

/* Add any custom values between this line and the "stop editing" line. */


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
