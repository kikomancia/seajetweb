<?php
// Central session configuration — include this before session_start() in every file.

$_sess_dir = dirname(__DIR__) . '/sessions';
if (!is_dir($_sess_dir)) {
    @mkdir($_sess_dir, 0700, true);
}

session_save_path($_sess_dir);
session_name('SJADMIN');

ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_path', '/');

// Detect HTTPS even behind a reverse proxy / load balancer
$_is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
          || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
          || (!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https');

if ($_is_https) {
    ini_set('session.cookie_secure', 1);
}

session_start();

unset($_sess_dir, $_is_https);
