<?php

session_start();
define('ROOT_DIR', __DIR__ );
require_once ROOT_DIR . '/../config/database.php';
require_once ROOT_DIR . '/libs/helpers.php';
require_once ROOT_DIR . '/libs/flash.php';
require_once ROOT_DIR . '/libs/sanitization.php';
require_once ROOT_DIR . '/libs/validation.php';
require_once ROOT_DIR . '/libs/filter.php';
require_once ROOT_DIR . '/libs/connection.php';
require_once ROOT_DIR . '/libs/mail.php';
require_once ROOT_DIR . '../inc/auth.php';

?>