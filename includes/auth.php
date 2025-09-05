<?php
// Simple authentication helpers
session_start();

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../login.php');
        exit();
    }
}

function require_admin() {
    require_login();
    if ($_SESSION['role'] !== 'admin') {
        header('Location: index.php');
        exit();
    }
}

function require_checker() {
    require_login();
    if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'checker') {
        header('Location: index.php');
        exit();
    }
}

function require_coordinator() {
    require_login();
    if ($_SESSION['role'] !== 'coordinator') {
        header('Location: index.php');
        exit();
    }
}

function require_coordinator_or_admin() {
    require_login();
    if ($_SESSION['role'] !== 'coordinator' && $_SESSION['role'] !== 'admin') {
        header('Location: index.php');
        exit();
    }
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function is_checker() {
    return isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'checker');
}

function is_coordinator() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'coordinator';
}

function is_approved_coordinator() {
    return is_coordinator() && isset($_SESSION['coordinator_status']) && $_SESSION['coordinator_status'] === 'approved';
}
?>
