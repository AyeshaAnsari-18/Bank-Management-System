<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Login</title>
   <link rel="stylesheet" href="../formstyle.css">
   <style>
      /* General Reset */
   * {
   margin: 0;
   padding: 0;
   box-sizing: border-box;
   }

   html, body {
   font-family: Arial, sans-serif;
   height: 100%;
   width: 100%;
   background-color:whitesmoke;
   }

   .container {
   display: flex;
   width: 48%;
   height: 60%;
   background:#003b78;
   border-radius: 10px;
   overflow: hidden;
   box-shadow: 6px 8px 12px rgba(0, 0, 0, 0.4);
   }

   .image-section {
   flex: 1;
   background-color: white;
   display: flex;
   justify-content: center;
   align-items: center;
   }

   .image-section img {
   height: 90%;
   }

   .form-section {
   flex: 1;
   padding: 40px;
   background-color: #1b3f99;
   color: white;
   display: flex;
   flex-direction: column;
   justify-content: center;
   }

   .title {
   font-size: 28px;
   font-family: sans-serif ;
   margin-bottom: 20px;
   text-align: center;
   }

   .success-message,
   .error-message {
   margin-bottom: 20px;
   font-size: 14px;
   text-align: center;
   }

   .field {
   margin-bottom: 20px;
   position: relative;
   }

   .field input {
   width: 100%;
   padding: 10px 10px;
   font-size: 16px;
   border: 1px solid #ccc;
   border-radius: 5px;
   margin-top: 10px;
   }

   .field label {
   position: absolute;
   top: 10px;
   left: 10px;
   font-size: 14px;
   color: black;
   transition: 0.3s;
   pointer-events: none;
   justify-content: center;
   }

   .field input:focus + label,
   .field input:not(:placeholder-shown) + label {
   top: -15px;
   left: 5px;
   font-size: 12px;
   color: white;
   }

   .field input:focus {
   outline: none;
   border-color: #00509e;
   box-shadow: 0 0 5px rgba(0, 80, 158, 0.2);
   }

   button,
   input[type="submit"] {
   width: 100%;
   padding: 10px;
   font-size: 16px;
   color: white;
   background-color: white;
   border: none;
   border-radius: 5px;
   }

   button:hover,
   input[type="submit"]:hover {
   background-color: rgb(28, 120, 211);
   }

   .signup-link {
   text-align: center;
   margin-top: 20px;
   font-size: 14px;
   color: white;
   }

   .signup-link a {
   color: #ffd700;
   text-decoration: none;
   }

   .signup-link a:hover {
   text-decoration: underline;
   }

   .admin-link {
      font-family: sans-serif;
      color: white;
      text-align: center;
      margin-top: 15px;
   }

   .admin-link a{
      color:rgb(28, 120, 211);
      text-decoration: none;
   }

   .admin-link a:hover{
      text-decoration: underline;

   }

   </style>
</head>
<body>
   <div class="container">
        <!-- Image Section -->
        <div class="image-section">
            <img src="../pictures/login.png" alt="Illustration" style="width: 80%;"/>
        </div>

        <div class="form-section">
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
