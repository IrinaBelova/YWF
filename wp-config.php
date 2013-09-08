<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpressYWF');

/** MySQL database username */
define('DB_USER', 'monkey');

/** MySQL database password */
define('DB_PASSWORD', 'password');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/*horrible hack to fix ftp */
define( 'FS_METHOD', 'direct' );
define( 'FS_CHMOD_DIR', 0777 );
define( 'FS_CHMOD_FILE', 0777 );


/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'A!gHvHF+-!,a4*c3%fR,mkI59Z7r#Z-Io{|P+{m^K>iq8E*bG_2r8R]&I^KB-~Vw');
define('SECURE_AUTH_KEY',  '-|(u+1=>K=G/60V~R 8,g2|Knk3_ato`7i|M5_Avq,+:P5=o*]2LW5X{)KX{-@+1');
define('LOGGED_IN_KEY',    'KA7S48<yrLjevN$c@K>Df:b}Z9=g(z O{;V6f=^|<C/w5hq(MDydcB#>U!&3JxWo');
define('NONCE_KEY',        '$*dVmm}{]*(:+cQ3rP63EID9UK%rxb} &Ye?45aPSp]GI=2$8-tOOYK^CJq%[!.@');
define('AUTH_SALT',        'Fcrg:<|. *e[aPD|L&N*&Fp6DAc/-fp$+O--rwClHo_7=hfTXVI/@/CZd#C`SlYH');
define('SECURE_AUTH_SALT', ']yC*>V<nJ]xM|pjj5kDi[jJHG*qZSUrV!}M WpW6KYXiLj7F##a<L8#cYY:|t4)[');
define('LOGGED_IN_SALT',   '^*UL}+Y9c/p4Jx:M@xlp!i*S^mQ*!K-:{,vPXl{ZWNI]zK3ZK{==(1a{G=w*-vO|');
define('NONCE_SALT',       'Lb+A8ERju.K/jyU)eO1RY3`h #i.JIAh(KstkZJ8ztF+f,AD>5F%V]v<VY+$h0@T');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

