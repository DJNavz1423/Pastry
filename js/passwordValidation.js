document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('registration-form');
  const passwordInput = document.getElementById('password');
  const confirmPasswordInput = document.getElementById('confirm_password');
  const passwordError = document.getElementById('password-error');
  const passwordHint = document.getElementById('password-hint');

  // Real-time password length validation
  passwordInput.addEventListener('input', validatePasswordLength);
  
  // Real-time password match validation
  confirmPasswordInput.addEventListener('input', validatePasswordMatch);
  passwordInput.addEventListener('input', validatePasswordMatch);

  // Validate password length (at least 8 characters)
  function validatePasswordLength() {
    const password = passwordInput.value;
    
    // Show/hide hint based on length
    if (password.length > 0 && password.length < 8) {
      passwordHint.style.display = 'block';
      passwordHint.style.color = '#E22028B5';
      passwordHint.textContent = `Password too short (${password.length}/8 characters)`;
      return false;
    } else if (password.length >= 8) {
      passwordHint.style.display = 'block';
      passwordHint.style.color = '#0a9233';
      passwordHint.textContent = '✓ Password length is good';
      return true;
    } else {
      passwordHint.style.display = 'block';
      passwordHint.textContent = 'Password must be at least 8 characters';
      passwordHint.style.color = "#6C646E";
      return false;
    }
  }

  // Validate password match
  function validatePasswordMatch() {
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;
    
    // Clear previous error
    passwordError.textContent = '';
    confirmPasswordInput.setCustomValidity('');
    
    // Only validate match if both fields have values
    if (confirmPassword === '' || password === '') {
      passwordError.style.display = "none";
      return true;
    }
    
    if (password !== confirmPassword) {
      const errorMsg = 'Passwords do not match';
      passwordError.textContent = errorMsg;
      passwordError.style.display = "block";
      confirmPasswordInput.setCustomValidity(errorMsg);
      return false;
    } else {
      confirmPasswordInput.setCustomValidity('');
      passwordError.style.display = "none";
      return true;
    }
  }

  // Form submission validation
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Reset all errors
    passwordError.textContent = '';
    passwordInput.setCustomValidity('');
    confirmPasswordInput.setCustomValidity('');
    
    // Validate password length
    const isPasswordValid = validatePasswordLength();
    
    // Validate password match
    const isMatchValid = validatePasswordMatch();
    
    // Check if both validations pass
    if (!isPasswordValid) {
      const errorMsg = 'Password must be at least 8 characters long';
      passwordError.textContent = errorMsg;
      passwordInput.setCustomValidity(errorMsg);
      passwordInput.focus();
      return false;
    }
    
    if (!isMatchValid) {
      const errorMsg = 'Passwords do not match';
      passwordError.textContent = errorMsg;
      confirmPasswordInput.setCustomValidity(errorMsg);
      confirmPasswordInput.focus();
      return false;
    }

    
    
    // If everything is valid
    console.log('Form is valid. Submitting...');
    
    // Here you would typically submit the form
    // For example: form.submit(); or send via fetch()
    
    // Optional: Show success message
    passwordError.textContent = '';
    passwordError.style.color = '#0a9233';
    passwordError.textContent = '✓ All good! Form can be submitted.';
    
    // Simulate form submission (remove in production)
    setTimeout(() => {
      
      alert('Registration successful!');
      closeModal();
    }, 1500);
    

    form.submit();
    return true;
  });

});