<?php
include_once 'base_url.php';
include_once 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>EventGate</title>
    <link rel="stylesheet" href="<?php echo $BASE_URL; ?>assets/css/style.css">
    <link rel="shortcut icon" href="/assets/icons/fav-icon.png" type="image/x-icon">
    <script src="<?php echo $BASE_URL; ?>assets/js/event-manager.js" defer></script>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="<?php echo $BASE_URL; ?>index.php" class="brand">
                <img src="<?php echo $BASE_URL; ?>assets/img/logo.png" alt="EventGateLogo"/>
            </a>
            
            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                <svg viewBox="0 0 24 24">
                    <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
                </svg>
            </button>
            
            <ul class="nav-links" id="navLinks">
                <li><a href="<?php echo $BASE_URL; ?>index.php">Home</a></li>
                <li><a href="<?php echo $BASE_URL; ?>events.php">Events</a></li>
                <?php if (is_logged_in()): ?>
                    <li><a href="<?php echo $BASE_URL; ?>orders_my.php">My Orders</a></li>
                    <li><a href="<?php echo $BASE_URL; ?>tickets_my.php">My Tickets</a></li>
                    <?php if (is_admin()): ?>
                        <li><a href="<?php echo $BASE_URL; ?>admin/index.php">Admin</a></li>
                    <?php endif; ?>
                    <?php if (is_coordinator()): ?>
                        <li><a href="<?php echo $BASE_URL; ?>coordinator/dashboard.php">Coordinator</a></li>
                    <?php endif; ?>
                    <?php if (is_checker()): ?>
                        <li><a href="<?php echo $BASE_URL; ?>checkin.php">Check Tickets</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li><a href="<?php echo $BASE_URL; ?>coordinator_register.php">Become Coordinator</a></li>
                <?php endif; ?>
            </ul>
            
            <div class="nav-buttons" id="navButtons">
                <?php if (is_logged_in()): ?>
                    <span style="color: var(--gray-600); margin-right: 1rem; font-weight: 500;">Hello, <?php echo $_SESSION['name']; ?></span>
                    <a href="<?php echo $BASE_URL; ?>logout.php" class="btn btn-outline-light">Logout</a>
                <?php else: ?>
                    <a href="<?php echo $BASE_URL; ?>register.php" class="btn btn-outline-light">Register</a>
                    <a href="<?php echo $BASE_URL; ?>login.php" class="btn btn-primary">Sign In</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <main>
