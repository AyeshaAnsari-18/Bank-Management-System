// Handle success and error messages
window.onload = function () {
    const urlParams = new URLSearchParams(window.location.search);
    const successMessage = document.getElementById("successMessage");
    const errorMessage = document.getElementById("errorMessage");
 
    if (urlParams.get("success") === "1") {
       successMessage.style.display = "block";
    }
 
    const error = urlParams.get("error");
    if (error) {
       errorMessage.style.display = "block";
       const errorMessages = {
          incorrect_password: "Incorrect password. Please try again.",
          invalid_customer_id: "Invalid Customer ID. Please try again.",
          missing_fields: "Please fill in all required fields."
       };
       errorMessage.textContent = errorMessages[error] || "An unknown error occurred.";
    }
 };
 
 // Toggle between forms
 function toggleForm(form) {
    const loginForm = document.getElementById("loginForm");
    const signupForm = document.getElementById("signupForm");
    const formTitle = document.getElementById("formTitle");
 
    if (form === "signup") {
       loginForm.style.display = "none";
       signupForm.style.display = "block";
       formTitle.textContent = "Signup Form";
    } else {
       loginForm.style.display = "block";
       signupForm.style.display = "none";
       formTitle.textContent = "Login Form";
    }
 }
 
 // Hide messages
 function hideMessage(messageId) {
    const message = document.getElementById(messageId);
    if (message) {
       message.style.display = "none";
    }
 }
 
 // Toggle password visibility
 function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    if (field.type === "password") {
       field.type = "text";
    } else {
       field.type = "password";
    }
 }
 
 // Validate forms
 function validateLogin() {
    const customerId = document.getElementById("customer_id").value;
    const password = document.getElementById("password").value;
 
    if (customerId.trim() === "" || password.trim() === "") {
       alert("Please fill in all fields.");
       return false;
    }
    return true;
 }
 
 function validateSignup() {
    const password = document.getElementById("signup_password").value;
    const confirmPassword = document.getElementById("confirm_password").value;
 
    if (password !== confirmPassword) {
       alert("Passwords do not match.");
       return false;
    }
    return true;
 }
 