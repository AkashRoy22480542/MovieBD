// ===== LOGIN VALIDATION FUNCTION =====
function validateLogin() {
  let email = document.getElementById("loginEmail").value.trim();
  let password = document.getElementById("loginPassword").value.trim();

  if (email === "" || password === "") {
    alert("All fields are required!");
    return false; // stop form submission
  }

  let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
  if (!email.match(emailPattern)) {
    alert("Enter a valid email address.");
    return false;
  }

  if (password.length < 6) {
    alert("Password must be at least 6 characters.");
    return false;
  }

  // If everything is fine, redirect (or submit form)
    return true;
}

// ===== SIGNUP VALIDATION FUNCTION =====
function validateSignup() {
  let name = document.getElementById("fullName").value.trim();
  let email = document.getElementById("signupEmail").value.trim();
  let password = document.getElementById("signupPassword").value.trim();
  let confirmPassword = document.getElementById("confirmPassword").value.trim();

  if (name === "" || email === "" || password === "" || confirmPassword === "") {
    alert("All fields are required!");
    return false;
  }

  let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
  if (!email.match(emailPattern)) {
    alert("Enter a valid email address.");
    return false;
  }

  if (password.length < 6) {
    alert("Password must be at least 6 characters.");
    return false;
  }

  if (password !== confirmPassword) {
    alert("Passwords do not match!");
    return false;
  }

  alert("Sign up successful 🎉");
  return true;
}
