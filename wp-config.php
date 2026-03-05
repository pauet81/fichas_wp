<?php
define('DB_NAME', 'favnzzb_bk3c1');
define('DB_USER', 'wp');
define('DB_PASSWORD', 'wp');
define('DB_HOST', 'localhost');

define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

$table_prefix = 'mzvw_';

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('FS_METHOD', 'direct');
define('FS_CHMOD_DIR', (0755 & ~ umask()));


define('WP_HOME', 'http://localhost:8080');
define('WP_SITEURL', 'http://localhost:8080');

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';

