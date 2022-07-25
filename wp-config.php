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
define( 'DB_NAME', 'kasim' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         ';~,@~97b  |~dk_sH}Q|r)W56|I6#U>q=ZjpoiB!c;&rkXahY=/U[*K8)EY_XEqq' );
define( 'SECURE_AUTH_KEY',  'QLBFbhl+;E4:L/dne(|BfYGL%@S7/jX1Eh:L2]-8dKtx}JjdN.P_0& k(4tQujjq' );
define( 'LOGGED_IN_KEY',    'rouqqg]]ra&YDhVIvb6Q+lGbaUngbkLop=ixxP3c,N[QGR78o^!]Al6v+n__DJod' );
define( 'NONCE_KEY',        '2>ipfU4[U&x?%U@*6T{`h`2_V5sx4Q)J_T{!e~xvP1`&q$~{wmfY3`{U#?F|RrmG' );
define( 'AUTH_SALT',        '3n];V)F}JPN%%_Y_yN%XI[*[#ZTpn4$:4/G_J@+NAXu<yIpImSs/yWO!0,H lZhO' );
define( 'SECURE_AUTH_SALT', 'g_$SvI,vu2w>*-b2PzVLUK8 oD-UWKG0uj5maGdM*-qJ`/#Z.rqAF(KcMdcMC;]S' );
define( 'LOGGED_IN_SALT',   'Nm^#oF(Q=spJf4|H?dgU7cfkj}R=U<niYrB6>!PM}O{ mGC/t9(=Wx=5,BBx){(/' );
define( 'NONCE_SALT',       'ywnm9xsnV<AV!J<4v}Q:VN?FEAcwbHTjGYvH!aWw+l+~2] h_TU02.SxNhLmZX@,' );

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

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
