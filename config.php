<?php
// Sesuaikan konfigurasi ini dengan MySQL lokal Anda.
define('DB_HOST', 'localhost');
define('DB_NAME', 'uts_perpustakaan');
define('DB_USER', 'root');
define('DB_PASS', '');

function db(): mysqli
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');
    return $conn;
}
