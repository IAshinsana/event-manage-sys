<?php
// Base URL detection for portable URLs
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script_name = $_SERVER['SCRIPT_NAME'];

// Find the event directory path
$path = dirname($script_name);

// If we're in a subdirectory (like admin, coordinator), go up to the event root
if (strpos($path, '/admin') !== false) {
    $path = str_replace('/admin', '', $path);
} elseif (strpos($path, '/coordinator') !== false) {
    $path = str_replace('/coordinator', '', $path);
} elseif (strpos($path, '/includes') !== false) {
    $path = str_replace('/includes', '', $path);
}

$BASE_URL = $protocol . '://' . $host . $path;
if (substr($BASE_URL, -1) !== '/') {
    $BASE_URL .= '/';
}
?>
