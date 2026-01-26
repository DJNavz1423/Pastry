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

//event listeners for buttons
document.querySelectorAll(".cta-button.register").forEach(button => {
  button.addEventListener("click", function(){
    showModal();
    toggleForms("register");
  });
});

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

// Event listener to close modal when clicking outside the modal content
document.addEventListener("click", function(event) {
  const registrationModal = document.getElementById("registration-modal");
  const loginModal = document.getElementById("login-modal");

  function isClickOutsideModal(event, modal) {
    if(modal.style.display === "flex") {
      const modalContent = modal.querySelector(".modal-content");
      return modalContent && !modalContent.contains(event.target) && modal.contains(event.target);
    }
    return false;
  }

  if (isClickOutsideModal(event, registrationModal)) {
    closeModal();
  }

  if (isClickOutsideModal(event, loginModal)) {
    closeModal();
  }
});

document.querySelectorAll(".modal-content").forEach(content => {
  content.addEventListener("click", function(event){
    event.stopPropagation();
  });
});

// Event listener to close modal on pressing the Escape key
document.addEventListener("keydown", function(event) {
  if (event.key === "Escape" || event.key === 27) {
    closeModal();
  }
});


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


// reset modal function
function resetModal(modalId){
  const modal = document.getElementById(modalId);

  const form = modal.querySelector("form");

  if(form){
    form.reset();

    const passwordInputs = form.querySelectorAll('input[type="text"], input[type="password"]');
    passwordInputs.forEach(input => {
      if(input.name === "password" || input.name === "confirm_password" || input.id === "password" || input.id === "confirm_password" || input.id === "password_login"){
        input.type = "password";
      }
    });

    const toggleButtons = form.querySelectorAll(".toggle-password");
    toggleButtons.forEach(button => {
      const icon = button.querySelector("i");

      if(icon){
        icon.classList.remove("bx-eye-slash");
        icon.classList.add("bx-eye");
        button.setAttribute("aria-label", "Show password");
      }
    });

    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
      checkbox.checked = false;
    });

    if(modalId === "registration-modal"){
      const passwordHint = document.getElementById("password-hint");

      if(passwordHint){
        passwordHint.textContent = 'Password must be at least 8 characters';
        passwordHint.style.color = "#6C646E"
      }
    }
  }
}