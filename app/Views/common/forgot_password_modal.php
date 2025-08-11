<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="forgotPasswordForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="forgotPasswordModalLabel">Please type your registered email address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <div class="form-floating">
                        <input type="email" class="form-control text-center" name="email" id="resetPasswordEmail"
                               placeholder="name@example.com" autocomplete="email" required>
                        <label for="resetPasswordEmail">Email address <small class="text-danger">*</small></label>
                        <div class="invalid-feedback" id="emailError"></div>
                    </div>
                    <?= recaptcha('password_reset', 'password_reset_recaptcha'); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submitForgotPWBtn">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>