<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Login</title>
   <link rel="stylesheet" href="../css/adminlogin.css">
   <style>
    .message {
        font-weight: bold;
        margin-bottom: 15px;
        padding: 10px;
        border-radius: 5px;
    }
    .message.success {
        color: green;
        background-color: #e6ffee;
        border: 1px solid #c3e6cb;
    }
    .message.error {
        color: red;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
    }
   </style>
</head>
<body>
<div class="container">
        <!-- Image Section -->
        <div class="image-section">
            <img src="../pictures/login.png" alt="Illustration" style="width: 90%;"/>
        </div>

        <div class="form-section">
            <div class="title">
                <span id="formTitle">Login Form</span>
            </div>
            <?php if (isset($_GET['message']) && isset($_GET['type'])): ?>
                <p class="message <?= $_GET['type'] === 'success' ? 'success' : 'error'; ?>">
                    <?= htmlspecialchars($_GET['message']); ?>
                </p>
            <?php endif; ?>
            
            <form action="adminlogin_api.php" method="POST" style="display: block;">
                <!-- Admin ID -->
                <div class="field">
                    <input type="text" id="admin_id" name="admin_id" required placeholder=" " autocomplete="username">
                    <label for="admin_id">Admin ID</label>
                </div>
                <!-- Password -->
                <div class="field">
                    <input type="password" id="password" name="password" required minlength="8" placeholder=" " autocomplete="current-password">
                    <label for="password">Password</label>
                </div>
                <!-- Remember Me -->
                <div class="content">
                    <div class="checkbox">
                        <input type="checkbox" id="remember-me" name="remember_me">
                        <label for="remember-me" style="color: white;">Remember me</label>
                    </div>
                </div>
                <!-- Submit Button -->
                <div class="field">
                    <input type="submit" value="Login">
                </div>  
            </form>
        </div>
    </div>
    
    <script>
         document.getElementById('loginForm').addEventListener('submit', function(e) {
             const adminId = document.getElementById('admin_id').value.trim();
             const password = document.getElementById('password').value.trim();

if (adminId === '' || password === '') {
                 const errorMessage = document.getElementById('error-message');
                 errorMessage.textContent = 'All fields are required.';
                 errorMessage.style.display = 'block';
                 e.preventDefault(); // Prevent form submission
             }
         });
     </script>

     
</body>
</html>