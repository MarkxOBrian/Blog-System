<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
}

// Timezone
date_default_timezone_set('UTC');

// Constants
define('SITE_NAME', 'Blog System');
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST']); 