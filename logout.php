<?php
session_start();
session_destroy();
include 'includes/base_url.php';
header('Location: ' . $BASE_URL . 'index.php');
exit();
?>
