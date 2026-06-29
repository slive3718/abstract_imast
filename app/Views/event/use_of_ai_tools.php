<link href="<?=base_url()?>/assets/css/jquery_ui_style.css" rel="stylesheet">

<?php echo view('event/common/menu'); ?>

<main>
    <div class="container" style="padding-bottom:120px">
        <?php echo view('event/common/shortcut_link'); ?>

        <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1 p-3 shadow">
            <h5 class="fw-bold">Use of Artificial Intelligence (AI) Tools <span class="show_error1 text-danger d-none">* Required</span></h5>
            <p>Utilizing guidance from the Accrediting Council of Continuing Medical Education (ACCME) regarding responsible use of Artificial Intelligence (AI), SRS requires that you disclose whether AI tools have been used to generate, analyze, or edit this abstract and/or its associated materials, such as uploaded images, figures, or charts. Routine spelling or grammar tools do not require disclosure.</p>
            <p>Disclosing AI use will not impact whether your abstract is reviewed or accepted for the meeting. This disclosure is intended to support transparency to learners and confirm that all AI-generated content has been reviewed prior to submission.</p>
            <p>If AI was used, the authors of this abstract remain fully accountable for the submitted content and must attest that all AI-assisted content has been human-verified for clinical accuracy, evidence-based integrity, and the absence of commercial bias.</p>
            <p>Please contact cme@srs.org with any questions regarding this form.</p>

            <p class="fw-bold mt-3">Did you use Artificial Intelligence (AI) tools to generate, modify, or analyze any portion of this abstract or its associated materials?<br>
                <span class="text-muted">Note: Routine spelling or grammar tools do not require disclosure.</span></p>

            <div class="d-flex align-items-start mb-2">
                <div class="pe-2">
                    <input type="radio" name="ai_use_radio" class="ai_use_radio" value="0" <?=(isset($abstract_details['ai_used']) && $abstract_details['ai_used'] == 0) ? 'checked' : ''?>>
                </div>
                <div>
                    No, my co-authors and I did NOT use AI tools while developing this content.
                </div>
            </div>
            <div class="d-flex align-items-start">
                <div class="pe-2">
                    <input type="radio" name="ai_use_radio" class="ai_use_radio" value="1" <?=(isset($abstract_details['ai_used']) && $abstract_details['ai_used'] == 1) ? 'checked' : ''?>>
                </div>
                <div>
                    Yes, my co-authors and I used AI tools while developing this content.
                </div>
            </div>

            <!-- AI Details Section - shown when "Yes" is selected -->
            <div id="ai_details_container" style="<?=($abstract_details['ai_used'] ?? null) == 1 ? '' : 'display:none;'?>">
                <div class="mb-3">
                    <label for="ai_tools" class="form-label fw-bold">Tool(s) &amp; Version:</label>
                    <p class="text-muted small">Examples: ChatGPT-5.5; Claude Sonnet 4.6; Gemini 3</p>
                    <textarea class="form-control" id="ai_tools" name="ai_tools" rows="2"><?=$abstract_details['ai_tools'] ?? ''?></textarea>
                    <span class="show_error4 text-danger d-none">* This field is required when AI tools were used</span>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Purpose of Use:</label>
                    <div class="row">
                        <?php
                        $ai_purposes = ['conception' => 'Conception or design', 'data_collection' => 'Data Collection (e.g. AI XR Measurement)', 'data_analysis' => 'Data Analysis', 'manuscript_prep' => 'Manuscript Preparation/Writing', 'other' => 'Other (please specify):'];
                        $selected_purposes = isset($abstract_details['ai_purposes']) ? explode(',', $abstract_details['ai_purposes']) : [];
                        ?>
                        <?php foreach($ai_purposes as $key => $label): ?>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input ai_purpose_checkbox" type="checkbox" value="<?=$key?>" id="ai_purpose_<?=$key?>" <?=in_array($key, $selected_purposes) ? 'checked' : ''?>>
                                    <label class="form-check-label" for="ai_purpose_<?=$key?>">
                                        <?=$label?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="ai_other_purpose_container" style="<?=in_array('other', $selected_purposes) ? '' : 'display:none;'?>">
                        <input type="text" class="form-control mt-2" id="ai_other_purpose" placeholder="Please specify other purpose" value="<?=$abstract_details['ai_other_purpose'] ?? ''?>">
                    </div>
                    <span class="show_error5 text-danger d-none">* Please select at least one purpose or specify "Other"</span>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input ai_attestation_checkbox" type="checkbox" id="ai_attestation_checkbox" <?=($abstract_details['ai_attestation'] ?? null) == 1 ? 'checked' : ''?>>
                        <label class="form-check-label" for="ai_attestation_checkbox">
                            I attest that all AI-generated content has been personally reviewed to confirm clinical accuracy, lack of commercial bias, security of patient health information (PHI), and evidence-based integrity. I accept full responsibility for the final content.
                            <span class="show_error6 text-danger d-none">* Required</span>
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="ai_attestation_name" class="form-label fw-bold">Name:</label>
                        <input type="text" class="form-control" id="ai_attestation_name" value="<?=$abstract_details['ai_attestation_name'] ?? ''?>">
                        <span class="show_error7 text-danger d-none">* Name is required</span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="ai_attestation_date" class="form-label fw-bold">Date:</label>
                        <input type="date" class="form-control" id="ai_attestation_date" value="<?=$abstract_details['ai_attestation_date'] ?? date('Y-m-d')?>">
                        <span class="show_error8 text-danger d-none">* Date is required</span>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <div class="form-check">
                    <input class="form-check-input acceptTermsBtn" type="checkbox" id="acceptTermsBtn" value="1" required <?=($abstract_details['ai_terms'] ?? null) == 1 ? 'checked' : ''?>>
                    <label class="form-check-label" for="acceptTermsBtn">
                        I accept the terms of participation in this CME activity as noted in the author disclosure statement.
                        <span class="show_error3 text-danger d-none">* Required</span>
                    </label>
                </div>
                <button class="btn btn-success saveAIDisclosureBtn mt-3" id="saveAIDisclosureBtn">Save and Continue</button>
            </div>
        </div>
    </div>
</main>

<script>
    $(function() {
        const abstract_id = '<?=$paper_id?>';

        // Show/hide AI details based on radio selection
        $('.ai_use_radio').on('change', function() {
            if ($(this).val() == '1') {
                $('#ai_details_container').slideDown();
            } else {
                $('#ai_details_container').slideUp();
            }
        });

        // Show/hide "Other" purpose input
        $('#ai_purpose_other').on('change', function() {
            if ($(this).is(':checked')) {
                $('#ai_other_purpose_container').slideDown();
            } else {
                $('#ai_other_purpose_container').slideUp();
            }
        });

        // Set default date if not set
        if (!$('#ai_attestation_date').val()) {
            $('#ai_attestation_date').val(new Date().toISOString().split('T')[0]);
        }

        $('#saveAIDisclosureBtn').on('click', function() {
            // Reset error messages
            $('[class^="show_error"]').addClass('d-none');

            // Validate form
            let isValid = true;
            const aiUsed = $('.ai_use_radio:checked').val();

            // AI Use radio validation
            if (!aiUsed) {
                $('.show_error1').removeClass('d-none');
                isValid = false;
            }

            // If AI was used, validate additional fields
            if (aiUsed == '1') {
                // Tools & Version
                if (!$.trim($('#ai_tools').val())) {
                    $('.show_error4').removeClass('d-none');
                    isValid = false;
                }

                // Purpose checkboxes - at least one selected
                if (!$('.ai_purpose_checkbox:checked').length) {
                    $('.show_error5').removeClass('d-none');
                    isValid = false;
                }

                // If "Other" is selected but field is empty
                if ($('#ai_purpose_other').is(':checked') && !$.trim($('#ai_other_purpose').val())) {
                    $('.show_error5').removeClass('d-none');
                    isValid = false;
                }

                // Attestation checkbox
                if (!$('#ai_attestation_checkbox').is(':checked')) {
                    $('.show_error6').removeClass('d-none');
                    isValid = false;
                }

                // Name
                if (!$.trim($('#ai_attestation_name').val())) {
                    $('.show_error7').removeClass('d-none');
                    isValid = false;
                }

                // Date
                if (!$.trim($('#ai_attestation_date').val())) {
                    $('.show_error8').removeClass('d-none');
                    isValid = false;
                }
            }

            // Terms checkbox
            if (!$('#acceptTermsBtn').is(':checked')) {
                $('.show_error3').removeClass('d-none');
                isValid = false;
            }

            if (!isValid) {
                toastr.warning('Please fill up all required fields.');
                return false;
            }

            // Prepare data
            const selectedPurposes = [];
            $('.ai_purpose_checkbox:checked').each(function() {
                selectedPurposes.push($(this).val());
            });

            const isAiUsed = aiUsed == '1';
            const formData = {
                abstract_id: abstract_id,
                ai_used: aiUsed || '',
                ai_terms: $('#acceptTermsBtn').is(':checked') ? 1 : 0
            };

            // Only add AI fields if AI was used
            if (isAiUsed) {
                formData.ai_tools = $('#ai_tools').val() || '';
                formData.ai_purposes = selectedPurposes.join(',');
                formData.ai_other_purpose = $('#ai_purpose_other').is(':checked') ? $('#ai_other_purpose').val() || '' : '';
                formData.ai_attestation = $('#ai_attestation_checkbox').is(':checked') ? 1 : 0;
                formData.ai_attestation_name = $('#ai_attestation_name').val() || '';
                formData.ai_attestation_date = $('#ai_attestation_date').val() || '';
            }

            // Submit data
            $.ajax({
                url: base_url + '/use_of_ai/save',
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
                            window.location.href = `${base_url}/user/submission_menu/${abstract_id}`;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: response.message || 'Something went wrong'
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