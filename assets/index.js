

function validateSignInForm() {  
        email = document.getElementById("email").value;
        password = document.getElementById("password").value;
        error = document.getElementById("error"); 

        error.textContent = "";
        if (!email || !password) {
          error.textContent = "Please fill in all fields.";
          error.style.color = "red"; 
          return false;
        }
  
        if(!email_validate()){
          error.textContent = "Invalid email address.";
          error.style.color = "red"; 
          return false;
        }
  
        if (!password_vaildate()) {
          error.textContent = "Password must be at least 8 characters long.";
          error.style.color = "red"; 
          return false;
        }
        error.textContent="";
  
        return true;
}

function validateSignUpForm() {
        const name = document.getElementById("name").value;
        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("repassword").value;
        const error = document.getElementById("error");
  
        error.textContent = "";
  
        if (!name || !email || !password || !confirmPassword) {
          error.textContent = "Please fill in all fields.";
          error.style.color = "red"; 
          return false;
        }

        else if(!email_validate()){
                error.textContent = "Invalid email address.";
                error.style.color = "red"; 
                return false;
        }
  
        else if (!password_vaildate()) {
          error.textContent = "Password must be at least 8 characters long.";
          error.style.color = "red"; 
          return false;
        }
  
        else if (password !== confirmPassword) {
          error.textContent = "Passwords do not match.";
          error.style.color = "red"; 
          return false;
        }
        error.textContent="";
        return true;
}

function validateForgetPassForm(){
        email = document.getElementById("email").value;
        error = document.getElementById("error");
        if(!email){
                error.textContent = "Please fill in all fields.";
                error.style.color = "red"; 
                return false;
        }
        if(!email_validate()){
                error.textContent = "Invalid email address.";
                error.style.color = "red"; 
                return false;
        }
        error.textContent = "";
        window.location.href = "reset_password.html";
        return true;
}

function vaildateResetPass(){
        password = document.getElementById("password").value;
        confirmPassword = document.getElementById("renewpassword").value;
        error =document.getElementById("error");
        if(!password || !confirmPassword){
                error.textContent = "Please fill in all fields.";
                error.style.color = "red"; 
                return false;

        }
        if(!password_vaildate()){
                error.textContent = "Password must be at least 8 characters long.";
                error.style.color=red;
                return false;
        }
        else if(password!==confirmPassword){
                error.innerHTML="Password not Matched";
                error.style.color=red;
                return false;

        }
        error.innerHTML="";
        return true;
}
