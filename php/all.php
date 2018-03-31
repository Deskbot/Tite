<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Europe/London');

define('ROOT', '/var/www/html');

require_once ROOT . 'php/constants.php';
require_once ROOT . 'php/cookie.php';
require_once ROOT . 'php/database.php';
require_once ROOT . 'php/entity.php';
require_once ROOT . 'php/get.php';
require_once ROOT . 'php/holeFiller.php';
require_once ROOT . 'php/lib.php';
require_once ROOT . 'php/mail.php';
require_once ROOT . 'php/post.php';
require_once ROOT . 'php/responses.php';
require_once ROOT . 'php/session.php';
require_once ROOT . 'php/template.php';