// Form Validation Functions

function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePhone(phone) {
    const phoneRegex = /^[0-9]{10,15}$/;
    return phoneRegex.test(phone.replace(/\D/g, ''));
}

function validatePassword(password) {
    return password && password.length >= 6;
}

function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    let isValid = true;
    const inputs = form.querySelectorAll('input, textarea, select');

    inputs.forEach(input => {
        const errorElement = input.nextElementSibling;
        if (errorElement && errorElement.classList && errorElement.classList.contains('form-error')) {
            errorElement.remove();
        }

        if (input.hasAttribute('required') && !input.value.trim()) {
            showFieldError(input, 'This field is required');
            isValid = false;
        } else if (input.type === 'email' && input.value && !validateEmail(input.value)) {
            showFieldError(input, 'Please enter a valid email address');
            isValid = false;
        } else if (input.name && input.name.includes('phone') && input.value && !validatePhone(input.value)) {
            showFieldError(input, 'Please enter a valid phone number');
            isValid = false;
        } else if (input.name === 'password' && input.value && !validatePassword(input.value)) {
            showFieldError(input, 'Password must be at least 6 characters');
            isValid = false;
        } else if (input.name === 'confirm_password' && input.value) {
            const passwordInput = form.querySelector('input[name="password"]');
            if (passwordInput && input.value !== passwordInput.value) {
                showFieldError(input, 'Passwords do not match');
                isValid = false;
            }
        }
    });

    return isValid;
}

function showFieldError(input, message) {
    input.style.borderColor = '#dc3545';
    const errorElement = document.createElement('div');
    errorElement.className = 'form-error';
    errorElement.textContent = message;
    input.parentElement.insertBefore(errorElement, input.nextSibling);
}

// Toggle password visibility
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    if (input) {
        input.type = input.type === 'password' ? 'text' : 'password';
    }
}

// Handle form submission with AJAX
function submitFormAjax(event, action) {
    event.preventDefault();
    
    const form = event.target;
    const formId = form.id;

    if (!validateForm(formId)) {
        return;
    }

    const formData = new FormData(form);
    formData.append('action', action);

    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message || 'Operation successful');
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            } else {
                form.reset();
            }
        } else {
            showAlert('danger', data.message || 'Operation failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred. Please try again.');
    });
}

// Handle logout
function logout() {
    if (confirm('Are you sure you want to logout?')) {
        fetch('logout.php')
            .then(() => {
                window.location.href = 'index.php';
            });
    }
}

// Initialize tooltips and popovers
function initializeTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        element.addEventListener('hover', function() {
            const message = this.getAttribute('data-tooltip');
            console.log(message);
        });
    });
}

// Document ready
document.addEventListener('DOMContentLoaded', function() {
    initializeTooltips();

    // Handle dynamic form submissions
    const forms = document.querySelectorAll('form[data-ajax]');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            submitFormAjax(event, this.getAttribute('data-ajax'));
        });
    });
});
