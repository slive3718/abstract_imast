
<!-- Support Request Modal -->
<div class="modal fade" id="supportemail" tabindex="-1" aria-labelledby="supportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="supportForm" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="supportModalLabel">Support Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?= csrf_field() ?>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="fname" id="fnameInput" placeholder="First name" autocomplete="given-name" required>
                                <label for="fnameInput">First Name<small class="text-danger">*</small></label>
                                <div class="invalid-feedback" id="fnameError"></div>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="lname" id="lnameInput" placeholder="Last Name" autocomplete="family-name" required>
                                <label for="lnameInput">Last Name<small class="text-danger">*</small></label>
                                <div class="invalid-feedback" id="lnameError"></div>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="email" class="form-control" name="email" id="semailInput" placeholder="Email" autocomplete="email" required>
                                <label for="semailInput">Email<small class="text-danger">*</small></label>
                                <div class="invalid-feedback" id="emailError"></div>
                            </div>

                            <div class="form-floating mt-2">
                                <input type="text" class="form-control" name="abstract_id" id="abstractIDInput" placeholder="Abstract ID" autocomplete="off">
                                <label for="abstractIDInput">Abstract ID</label>
                            </div>

                            <div class="form-floating mt-2">
                                <textarea class="form-control" style="height: 104px!important;" placeholder="Support Request"
                                          name="message" id="support_messageInput" required></textarea>
                                <label for="support_messageInput">Support Request<small class="text-danger">*</small></label>
                                <div class="invalid-feedback" id="messageError"></div>
                            </div>
                            <?= recaptcha('support_form', 'support_recaptcha' ) ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Send Email</button>
                </div>
            </form>
        </div>
    </div>
</div>