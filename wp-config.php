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
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'wordpress');

/** MySQL database password */
define('DB_PASSWORD', '96189f8017fff8f36aee257e80b19971e810912b77654605');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         'LN2n9++x.4BJY~:-sS`tp1DEK5cqWW$?f%aRQAx^EOz?:d1gjX#Ax&Ot]rN6(W.o');
define('SECURE_AUTH_KEY',  'aG:~?&|alNLqKrQ8hQIWv_Xqs4%f2wZk%H%Mo0K5KvJuIs<V&(oq2[$s;;%[z^Db');
define('LOGGED_IN_KEY',    'RSW)ulbuv8TL^C)U~tRt!>&R{b$(p>VIj~FJ]w8WV$9cb_bm@^i,og=N,pOMkp]!');
define('NONCE_KEY',        'DHpuZL=2=p{4c{!JyqmEnpeH=*V*~aAjE<+qx2.R41y3r[;RYSk/?8d)FK3Ehp|V');
define('AUTH_SALT',        '9_L#?oy.ocD#=quOMo-g+py[7[Mcdg:S2R>8B77;^nQAb-d)_HM;/Y9mIzEEaFVD');
define('SECURE_AUTH_SALT', 'iND} Q%/W2 }@7}5(@?;Ok2|#9w+en$`yYBN=8EizlBfn9,u?e}n{a`_!#LUoBGY');
define('LOGGED_IN_SALT',   'GXk:sl!jdLh^tPI/aV u}Wk4uW~22*^3*g kIzZtH??.^R*0Lq$y5-uu$Qw4~@~%');
define('NONCE_SALT',       'V-#u]AQ#9ISIHuF1Lr:l.0XB&T(X&Y 3&?oezsfi,s:-*mD$DtTHkGRLJh1uTyuN');

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
