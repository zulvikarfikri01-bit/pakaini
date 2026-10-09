<?php 
require_once 'E:\\aplikasi pakaini\\tests/../config.php';
$_SESSION['user_id'] = 3;
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['PHP_SELF'] = 'load_tasks.php';
$_GET['offset'] = '0';
$_GET['platform'] = '';
require 'E:\\aplikasi pakaini\\tests/../load_tasks.php';
