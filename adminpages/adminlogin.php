<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login Form</title>
   <link rel="stylesheet" href="../formstyle.css">
</head>
<body>
   <div class="wrapper">
      <div class="title">
         <span id="formTitle">Login Form</span>
      </div>
      
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
                 <label for="remember-me">Remember me</label>
             </div>
         </div>
         <!-- Submit Button -->
         <div class="field">
             <input type="submit" value="Login">
         </div>
     </form>
 
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
