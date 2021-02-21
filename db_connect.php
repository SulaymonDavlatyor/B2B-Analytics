<?php
require_once ('../db_config.php');
$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, '*****', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);