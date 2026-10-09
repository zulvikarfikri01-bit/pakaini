<?php
    require_once "E:\\aplikasi pakaini\\tests/../config.php";
    $_SESSION["user_id"] = 1;
    $_SERVER["REQUEST_METHOD"] = "POST";
    $_POST = array (
  'platform' => 'instagram',
  'task_type' => 'followers',
  'task_url' => 'https://instagram.com/user',
  'cpc' => '25',
  'target_clicks' => '20',
);
    require "E:\\aplikasi pakaini\\tests/../add_task.php";
    