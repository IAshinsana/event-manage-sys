<?php

$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';

$domain = $_SERVER['HTTP_HOST'];

$folder_path = dirname($_SERVER['SCRIPT_NAME']);

$folder_path = str_replace(['/admin', '/coordinator', '/includes'], '', $folder_path);

$BASE_URL = $protocol . '://' . $domain . $folder_path . '/';

?>
