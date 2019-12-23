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
define( 'DB_NAME', 'sieuwe_wordpress' );

/** MySQL database username */
define( 'DB_USER', 'sieuwe_admin' );

/** MySQL database password */
define( 'DB_PASSWORD', 'Ducati11' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '.lu|ro<4J7o:tq%$~%A9mGx{aOy<F]CC8,JFdn!q:f_G:4lz~qRX|x0KgTG.S` %' );
define( 'SECURE_AUTH_KEY',  '?YDdT6X+zhJxhtbO6`BG*>*KZDT~O%%&7qcSU>ABWU!fr _C_}snt89JGVzF=NC]' );
define( 'LOGGED_IN_KEY',    '?#x$<>>!#wL*|z),{sD;!34tyrR+}tP` E3Zh58{t6aqLA^uUm-LIVdn8@R|XR2>' );
define( 'NONCE_KEY',        'X*Nxm:`/}t1pQR8),h@#8<6a!}8hZmQvp@*Vox%)IAP/jN`C/t:wWYDgf4Z >C.=' );
define( 'AUTH_SALT',        'EcO$8hIV%(FEbc]S+,i-A_PmD:Rb+@`(9-1sZ5DKyjl&uFz(oap~(_X{nlb~sP o' );
define( 'SECURE_AUTH_SALT', '##8BwKk/,XzE`$1Yyr!1S9l#8Tldj89$)o>KaR1j^&`@&pm+c/BM.QNHRl*vWAVp' );
define( 'LOGGED_IN_SALT',   'bcT^CWqKse88i1pZ{Rk_uk<IeopBEw|dr$+(WIBoa.:M_@bmo{Q9.v<~>*d`a-F>' );
define( 'NONCE_SALT',       'G5PQu`dOdUNP=  f?jGJo!yHS1lSAP?zt4jwf7?<MLkUp>65G4)F;^+H{4F#g$G^' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
