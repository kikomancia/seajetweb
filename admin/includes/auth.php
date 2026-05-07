<?php
require __DIR__ . '/session.php'; // starts session with consistent config

if (empty($_SESSION['logged'])) {
    header('Location: index.php');
    exit;
}
