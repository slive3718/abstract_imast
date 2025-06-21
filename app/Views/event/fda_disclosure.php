<link href="<?=base_url()?>/assets/css/jquery_ui_style.css" rel="stylesheet">

<?php echo view('event/common/menu'); ?>

<main>
    <div class="container-fluid" style="padding-bottom:120px">
        <?php echo view('event/common/shortcut_link'); ?>

        <div class="card p-lg-5 p-md-2 p-sm-1 ">
            <h6 class="fw-bold">Unlabeled and Unapproved Uses <span class="show_error1 text-danger d-none">* Required</span></h6>
            <hr class="m-0" style="height: 5px; background-color:  #6390be;">
            <p>Presentations that provide information in whole or in part related to non FDA approved uses of drugs and/or devices must clearly disclose the unlabeled indications or the investigational nature of their proposed uses to the audience. Please document in the abstract FDA disclosure.</p>
            <p>* In my "work" for this educational program or publication:</p>

            <table class="mb-4">
                <tbody>
                <tr>
                    <td><input type="radio" name="unapproved_publication_radio" class="unapproved_publication_radio" value="1" <?=($abstract_details['fda_unapproved_uses'] ?? null) == 1 ? 'checked' : ''?>></td>
                    <td>I do not plan to discuss non-FDA approved products or non-FDA approved use of any products.</td>
                </tr>
                <tr>
                    <td><input type="radio" name="unapproved_publication_radio" class="unapproved_publication_radio" value="2" <?=($abstract_details['fda_unapproved_uses'] ?? null) == 2 ? 'checked' : ''?>></td>
                    <td>I plan to discuss non-FDA approved products or non-FDA approved use of any products.</td>
                </tr>
                </tbody>
            </table>

            <h6 class="fw-bold mt-5">Use of Product name <span class="show_error2 text-danger d-none">* Required</span></h6>
            <hr class="m-0" style="height: 5px; background-color:  #6390be;">
            <p>Presentations which utilize product names will receive additional scrutiny during the CME review process and presenters may be asked to remove the product name at the discretion of the CME Committee.</p>

            <table class="mb-4">
                <tbody>
                <tr>
                    <td><input type="radio" name="discuss_product_name_radio" class="discuss_product_name_radio" value="1" required <?=($abstract_details['fda_discuss_product_name'] ?? null) == 1 ? 'checked' : ''?>></td>
                    <td>I plan to discuss a commercial product by name in my presentation.</td>
                </tr>
                <tr>
                    <td><input type="radio" name="discuss_product_name_radio" class="discuss_product_name_radio" value="2" required <?=($abstract_details['fda_discuss_product_name'] ?? null) == 2 ? 'checked' : ''?>></td>
                    <td>I do not plan to discuss a commercial product by name in my presentation.</td>
                </tr>
                </tbody>
            </table>

            <hr class="m-0 mt-5" style="height: 5px; background-color: #6390be;">
            <div class="mt-3">
                <div class="form-check">
                    <input class="form-check-input acceptFdaBtn" type="checkbox" id="acceptFdaBtn" required <?=($abstract_details['is_fda_accepted'] ?? null) == 1 ? 'checked' : ''?>>
                    <label class="form-check-label" for="acceptFdaBtn">
                        I accept the terms of participation in this CME activity as noted in the author disclosure statement.
                        <span class="show_error3 text-danger d-none">* Required</span>
                    </label>
                </div>
                <button class="btn btn-success saveFdaBtn mt-3" id="saveFdaBtn">Save and Continue</button>
            </div>
        </div>
    </div>
</main>

<script>
    $(function() {
        const abstract_id = '<?=$abstract_id?>';

        $('#saveFdaBtn').on('click', function() {
            // Reset error messages
            $('[class^="show_error"]').addClass('d-none');

            // Validate form
            let isValid = true;

            if (!$('.unapproved_publication_radio:checked').length) {
                $('.show_error1').removeClass('d-none');
                isValid = false;
            }

            if (!$('.discuss_product_name_radio:checked').length) {
                $('.show_error2').removeClass('d-none');
                isValid = false;
            }

            if (!$('.acceptFdaBtn').is(':checked')) {
                $('.show_error3').removeClass('d-none');
                isValid = false;
            }

            if (!isValid) {
                toastr.warning('Please fill up all required fields.');
                return false;
            }

            // Prepare data
            const formData = {
                abstract_id: abstract_id,
                unapproved_publication: $('.unapproved_publication_radio:checked').val(),
                discuss_product_name: $('.discuss_product_name_radio:checked').val(),
                is_fda_accepted: $('.acceptFdaBtn').is(':checked') ? 1 : 0
            };

            // Submit data
            $.ajax({
                url: base_url + '/save_fda_disclosure',
                type: 'POST',
                dataType: 'json',
                data: formData,
                success: function(response) {
                    if (response.status == 200) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Changes saved',
                            text: 'Success'
                        }).then(() => {
                            window.location.href = `${base_url}/${event_uri}/user/submission_menu/${abstract_id}`;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Request failed: ' + xhr.statusText
                    });
                }
            });
        });
    });
</script>