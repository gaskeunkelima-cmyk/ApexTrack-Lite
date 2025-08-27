<?php
$requestUri = trim($_SERVER['REQUEST_URI'], '/');

if (empty($requestUri) || $requestUri === 'login.php') {
    include 'login.php';
} elseif ($requestUri === 'dashboard.php') {
    include 'dashboard.php';
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Halaman tidak ditemukan.";
}
?>