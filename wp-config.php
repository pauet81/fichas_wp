<?php
// Local, untracked overrides for production secrets.
$local_config = __DIR__ . '/wp-config.local.php';
if (file_exists($local_config)) {
	require $local_config;
}

if (!defined('DB_NAME')) {
	define('DB_NAME', getenv('WP_DB_NAME') ?: 'database_name_here');
}
if (!defined('DB_USER')) {
	define('DB_USER', getenv('WP_DB_USER') ?: 'username_here');
}
if (!defined('DB_PASSWORD')) {
	define('DB_PASSWORD', getenv('WP_DB_PASSWORD') ?: 'password_here');
}
if (!defined('DB_HOST')) {
	define('DB_HOST', getenv('WP_DB_HOST') ?: 'localhost');
}

define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

$table_prefix = 'mzvw_';

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('FS_METHOD', 'direct');
define('FS_CHMOD_DIR', (0755 & ~ umask()));


if (!defined('WP_HOME')) {
	define('WP_HOME', getenv('WP_HOME') ?: 'http://localhost:8080');
}
if (!defined('WP_SITEURL')) {
	define('WP_SITEURL', getenv('WP_SITEURL') ?: 'http://localhost:8080');
}

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';

