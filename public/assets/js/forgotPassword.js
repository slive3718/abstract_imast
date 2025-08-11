$(function(){
    $('.forgotPasswordBtn').on('click', function(){
        $('#forgotPasswordModal').modal('show')
    })

    $('#forgotPasswordModal').on('shown.bs.modal', function() {
        refreshRecaptchaToken();
    });

    // Function to refresh the reCAPTCHA token
    function refreshRecaptchaToken() {
        if (typeof grecaptcha !== 'undefined' && grecaptcha.ready) {
            grecaptcha.ready(function() {
                grecaptcha.execute(recaptchaSiteKey, {action: 'password_reset'})
                    .then(function(token) {
                        document.getElementById('password_reset_recaptcha').value = token;
                    });
            });
        }
    }
})

document.addEventListener('DOMContentLoaded', function() {
    const resetPasswordForm = document.getElementById('forgotPasswordForm');

    resetPasswordForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

        // Show loading state
        const submitBtn = resetPasswordForm.querySelector('[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
        submitBtn.disabled = true;

        // Prepare form data
        const formData = new FormData(resetPasswordForm);

        fetch(base_url+'account/reset_password', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 200) {
                    // Success
                    const modal = bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'));
                    modal.hide();

                    // Show success message
                    toastr.success('Your password and instruction successfully sent to your email!');

                    // Reset form
                    resetPasswordForm.reset();
                } else {
                    // Show validation errors
                    if (data.errors) {
                        for (const [field, message] of Object.entries(data.errors)) {
                            const input = document.querySelector(`[name="${field}"]`);
                            const errorElement = document.getElementById(`${field}Error`) || input.nextElementSibling;

                            if (input) {
                                input.classList.add('is-invalid');
                            }
                            if (errorElement) {
                                errorElement.textContent = message;
                            }
                        }
                    } else if (data.message) {
                        toastr.error(data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('An error occurred while submitting your request. Please try again.');
            })
            .finally(() => {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            });
    });
});