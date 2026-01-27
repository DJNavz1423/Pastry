//functions to show and close modals
function showModal() {
  document.getElementById("registration-modal").style.display = "flex";
}

function closeModal() {
  const registrationModal = document.getElementById("registration-modal");
  const loginModal = document.getElementById("login-modal");
  const passwordHint = document.getElementById("#password-hint");

  resetModal("registration-modal");
  resetModal("login-modal");

  registrationModal.style.display = "none";
  loginModal.style.display = "none";
  
}

// Event listeners for toggling between registration and login forms
document.getElementById("register-toggle").addEventListener("click", function(){
  toggleForms("register");
});

document.getElementById("login-toggle").addEventListener("click", function(){
  toggleForms("login");
});

// Function to toggle between registration and login forms
function toggleForms(form){
  const registrationForm = document.getElementById("registration-modal");
  const loginForm = document.getElementById("login-modal");

  if(form === "register"){
    registrationForm.style.display = "flex";
    loginForm.style.display = "none";
  } else if(form === "login"){
    registrationForm.style.display = "none";
    loginForm.style.display = "flex";
  }
}

// Password visibility toggle
function togglePasswordVisibility(button){
  const passwordInput = button.previousElementSibling;
  const icon = button.querySelector("i");

  if(passwordInput.type === "password"){
    passwordInput.type = "text";
    icon.classList.remove("bx-eye");
    icon.classList.add("bx-eye-slash");
    button.setAttribute("aria-label", "Hide password");
  }else{
    passwordInput.type = "password";
    icon.classList.remove("bx-eye-slash");
    icon.classList.add("bx-eye");
    button.setAttribute("aria-label", "Show password");
  }
}
document.querySelectorAll(".toggle-password").forEach(button => {
  button.addEventListener("click", function(){
    togglePasswordVisibility(button);
  });
});