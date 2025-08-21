<?php
$page_title = "Login";
include 'includes/header.php';
include 'includes/db.php';

$error = '';

if ($_POST) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if ($username && $password) {
        $sql = "SELECT * FROM users WHERE username = ? AND active = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Check password (raw format only)
            if ($user['password'] === $password) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['coordinator_status'] = $user['coordinator_status'];
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header('Location: admin/index.php');
                } elseif ($user['role'] === 'coordinator') {
                    header('Location: coordinator/dashboard.php');
                } else {
                    header('Location: index.php');
                }
                exit();
            } else {
                $error = 'Invalid username or password';
            }
        } else {
            $error = 'Invalid username or password';
        }
    } else {
        $error = 'Please enter both username and password';
    }
}
?>

<div class="container">
    <div class="form-container">
        <h2 class="text-center mb-3">Sign In</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Sign In</button>
        </form>
        
        <div class="text-center mt-3">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
        
        <div class="text-center mt-3">
            <small>
                <strong>Demo Accounts:</strong><br>
                Admin: admin / admin123<br>
                User: uoc / uoc<br>
                Checker: checker / checker
            </small>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
