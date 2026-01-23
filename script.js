function showModal() {
  document.getElementById("registration-modal").style.display = "flex";
}

function closeModal() {
  document.getElementById("registration-modal").style.display = "none";
  document.getElementById("login-modal").style.display = "none";
}

document.querySelectorAll(".cta-button.register").forEach(button => {
  button.addEventListener("click", function(){
    showModal();
    toggleForms("register");
  });
});

document.getElementById("register-toggle").addEventListener("click", function(){
  toggleForms("register");
});

document.getElementById("login-toggle").addEventListener("click", function(){
  toggleForms("login");
});

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

document.addEventListener("keydown", function(event) {
  if (event.key === "Escape" || event.key === 27) {
    closeModal();
  }
});

