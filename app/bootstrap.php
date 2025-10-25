<?php

session_start();

date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/helpers.php';

$pdo = get_connection();
ensure_schema($pdo);
