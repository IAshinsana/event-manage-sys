<?php
$page_title = "Login";
include 'includes/header.php';



if(isset($_SESSION["user_id"])){
    header('Location: index.php');
}


?>

<div class="container">
    <div class="form-container">
        <h2 class="text-center mb-3">Sign In</h2>
        

            <div id="error"></div>
<?php
if (isset($_GET["msg"]) ) {
    ?>
    <div id="regOk" class="alert alert-success">✅ Registration successful! Please log in to continue.</div>
    <?php
}
?>


        <div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" onclick="login();" class="btn btn-primary" style="width: 100%;">Sign In</button>
        </div>
        
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

