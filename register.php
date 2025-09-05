<?php
$page_title = "Register";
include 'includes/header.php';
?>

<div class="container">
    <div class="form-container">
        <h2 class="text-center mb-3">Register</h2>
        
  
        <div id="reg_error" class=""></div>

        

    <div id="reg_ok" class=""></div>

        
        <div ">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            
            <button onclick="signUp()" type="submit" class="btn btn-success" style="width: 100%;">Register</button>
        </div>
        
        <div class="text-center mt-3">
            <p>Already have an account? <a href="login.php">Sign in here</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
