<?php
$page_title = "Page Not Found";
include '../includes/header.php';
?>

<div class="container" style="margin-top: 3rem; text-align: center;">
    <div style="background: white; padding: 3rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">
        <div style="font-size: 6rem; margin-bottom: 1rem;">🎫</div>
        <h1 style="font-size: 3rem; margin-bottom: 1rem; color: #333;">404</h1>
        <h2 style="margin-bottom: 1rem; color: #666;">Page Not Found</h2>
        <p style="color: #666; margin-bottom: 2rem;">
            Sorry, the page you're looking for doesn't exist or has been moved.
        </p>
        
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="../index.php" class="btn btn-primary">🏠 Go Home</a>
            <a href="../events.php" class="btn btn-outline">🎉 Browse Events</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
