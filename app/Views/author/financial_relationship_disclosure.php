<?php echo view('author/common/menu'); ?>

<main>
    <div class="container py-5">
        <div class="card shadow-sm">
            <?= view('author/common/shortcut_link') ?>
            <form id="copyrightForm" action="submit.php" method="POST" class="needs-validation" novalidate>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">

                            <div>
                                <h5 class="fw-bolder mb-4"> Disclosure Statement </h5>
                                <p>
                                    SRS must ensure balance, independence, objectivity and scientific rigor in all educational activities. Therefore, as a person who is in position to influence or control the content of a CME activity, you must disclose <strong>all financial relationships</strong> held in the <strong> past 24 months </strong> with <strong> ineligible companies </strong>.* If the financial relationship has occurred in the past 24 months, but has since ended, we still require you to disclose the relationship.
                                </p>
                                <p>
                                    For each financial relationship, include the name of the ineligible company and the nature of the financial relationship(s). There is no minimum financial threshold; we ask that you disclose all financial relationships, regardless of the amount, with ineligible companies. You should disclose all financial relationships regardless of the potential relevance of each relationship to the education you are providing.
                                </p>
                                <p>
                                    <strong>Examples of financial relationships include </strong> employee, researcher, consultant, advisor, speaker, independent contractor (including contracted research), royalties or patent beneficiary, executive role, and ownership interest. Individual stocks and stock options should be disclosed; diversified mutual funds do not need to be disclosed. Research funding from ineligible companies should be disclosed by the principal or named investigator even if that individual’s institution receives the research grant and manages the funds.
                                </p>
                                <p>
                                    The intent of disclosure is not to prevent a speaker with a financial or other relationship from making a presentation, but rather to mitigate any conflicts prior to the CME activity so that learners may participate in a balanced, unbiased CME event.
                                </p>
                                <p>
                                    * An ineligible company is an entity whose primary business is producing, marketing, selling, re-selling, or distributing health care goods or services consumed by or on patients. For specific examples of ineligible companies visit accme.org/standards.
                                </p>
                            </div>
                            <h5 class="mb-4 fw-bold">Financial Relationships</h5>

                                <p>Please select the statement that applies:</p>

                                <!-- Financial Relationship Options -->
                                <div class="form-check mb-2">
                                    <input type="radio" class="form-check-input" name="financial_relationship" id="no_relationship" value="no"  <?= (!empty($author['financial_relationship']) && strtolower($author['financial_relationship']) == 'no') ? 'checked' : '' ?>  required>
                                    <label class="form-check-label" for="no_relationship">
                                        I have held NO financial relationship(s) with an ineligible company within the past 24 months.
                                    </label>
                                </div>

                                <div class="form-check mb-4">
                                    <input type="radio" class="form-check-input" name="financial_relationship" id="yes_relationship" value="yes"  <?= (!empty($author['financial_relationship']) && strtolower($author['financial_relationship']) == 'yes') ? 'checked' : '' ?> required>
                                    <label class="form-check-label" for="yes_relationship">
                                        I have held a financial relationship with an ineligible company within the past 24 months.
                                    </label>
                                </div>

                                <!-- Dynamic Organization Fields -->
                                <div id="organization-list" class="mt-3" style="display: none;">
                                    <!-- Organization fields will be added dynamically here -->
                                </div>

                                <!-- Add Organization Button -->
                                <button type="button" class="btn btn-outline-primary mt-3" id="add-organization-btn" style="display: none;">
                                    + Add another organization
                                </button>



                        </div>
                    </div>

                <!-- Financial Relationship Guidelines -->
                    <div class="mt-5">
                        <h3 class="mb-4 fw-bold">Guidelines Regarding Financial Relationships and Disclosure</h3>

                        <!-- Disclosure Section -->
                        <div class="mb-4">
                            <h5 class="fw-bold">Disclosure</h5>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    All individuals with control of content must complete and submit a financial relationship disclosure prior to planning and/or presenting a CME activity.
                                </li>
                                <li class="list-group-item">
                                    All individuals with control of content are required to disclose all financial relationships they have held with an ineligible company, within the past 24 months, regardless of relevancy.
                                </li>
                            </ul>
                        </div>

                        <!-- Content Validation Section -->
                        <div class="mb-4">
                            <h5 class="fw-bold">Content Validation</h5>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    Speakers/authors are required to prepare fair and balanced presentations that are objective and scientifically rigorous. All clinical recommendations must be based on evidence accepted within the medical profession as adequate justification for their indications and contraindications in the care of patients. All scientific research referred to, reported, or used to support a patient care recommendation must conform to generally accepted standards of experimental design, data collection and analysis.
                                </li>
                            </ul>
                        </div>

                        <!-- Unlabeled and Unapproved Uses Section -->
                        <div class="mb-4">
                            <h5 class="fw-bold">Unlabeled and Unapproved Uses</h5>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    Presentations that provide information in whole or in part related to non FDA approved uses of drugs and/or devices must clearly disclose the unlabeled indications or the investigational nature of their proposed uses to the audience. Please document in the abstract FDA disclosure.
                                </li>
                            </ul>
                        </div>

                        <!-- Use of Generic vs. Trade Names Section -->
                        <div class="mb-4">
                            <h5 class="fw-bold">Use of Generic vs. Trade Names</h5>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    The SRS Board of Directors has determined that the term hardware should not be used in written abstract submissions, oral presentations and E-Poster presentations. The terms instrumentation, implants, or constructs, should be used instead.
                                </li>
                                <li class="list-group-item">
                                    SRS strongly prefers that pharmaceuticals and proprietary software/databases as well as surgical approaches or specific instrumentation such as “Surgimap, MIMICS, EOS Imaging, RHOTEM, Isola, TSRH, CD, XLIF, DLIF, AxiaLIF, Solera, Vertex, Expedium, Mountaineer, Shilla, VEPTR etc.,” are not used in an abstract. These terms should be replaced by a generic term or description of the drug, software/database and/or instrumentation or technique unless the use of the term directly impacts learners’ understanding of the presentation or data. Instrumentation may also be referred to when the device name is a landmark system that is no longer sold (i.e. Harrington, Cotrel-Dubousset, Luque). Company names and logos must never be used in a presentation.
                                </li>
                                <li class="list-group-item">
                                    It is recognized that studies evaluating a product or products or comparing different products or techniques may require the use of product or technique names. If a product trade name or industry developed technique using a trademarked name are used in an abstract or presentation, the author must disclose their intent to use the term at the time of abstract submission and the abstract will be specifically reviewed by the CME Committee for evaluation of any potential conflict of interest. When there is a known potential conflict of interest, an expanded verbal disclosure will be necessary at the time of presentation. Furthermore, if a product name is mentioned, the audience should be informed of why it is necessary to give the name.
                                </li>
                            </ul>
                        </div>

                        <!-- Ineligible Company Influence Section -->
                        <div class="mb-4">
                            <h5 class="fw-bold">Ineligible Company Influence</h5>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    No payments or gifts after the SRS honorary policy may be given by an ineligible company to the director of the activity, planning committee members, teachers or authors, or any others involved with the CME activity receiving commercial support.
                                </li>
                                <li class="list-group-item">
                                    Individuals with control over content cannot receive or accept direct input from an ineligible company regarding the content or preparation of the presentation(s).
                                </li>
                            </ul>
                        </div>

                        <!-- Declaration Section -->
                        <div class="mb-4">
                            <h5 class="fw-bold">Declaration</h5>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    * Check the boxes below indicating that you agree with the statement. Please include your electronic signature and date.
                                </li>
                                <li class="list-group-item">
                                    In the context of the relationships/affiliations that you designated, WE ASK THAT YOU ATTEST THAT:
                                </li>
<!--                                <li class="list-group-item">-->
<!--                                    Practice recommendations that are relevant to the ineligible companies with whom you have relationships/affiliations will be supported by the best available evidence or absent evidence will be consistent with generally accepted medical practice.-->
<!--                                </li>-->
<!--                                <li class="list-group-item">-->
<!--                                    All reasonable clinical alternatives will be discussed when making practice recommendations.-->
<!--                                </li>-->
<!--                                <li class="list-group-item">-->
<!--                                    Relationships with ineligible companies will not bias or otherwise influence your involvement in the CME activity.-->
<!--                                </li>-->
                            </ul>

                            <div class="form-check my-2">
                                <input type="checkbox" class="form-check-input" id="disclosure_support" name="disclosure_support" value="1" <?= ( !empty($author['disclosure_support']) && $author['disclosure_support'] == '1' ? 'checked' : '')?> required>
                                <label class="form-check-label" for="disclosure_support">
                                    Practice recommendations will be supported by the best available evidence.
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="disclosure_discussed" name="disclosure_discussed" value="1"  <?= ( !empty($author['disclosure_support']) && $author['disclosure_support'] == '1' ? 'checked' : '')?> required>
                                <label class="form-check-label" for="disclosure_discussed">
                                    All reasonable clinical alternatives will be discussed.
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="disclosure_relationship" name="disclosure_relationship" value="1"  <?= ( !empty($author['disclosure_relationship']) && $author['disclosure_relationship'] == '1' ? 'checked' : '')?> required>
                                <label class="form-check-label" for="disclosure_relationship">
                                    Relationships with ineligible companies will not bias or otherwise influence your involvement in the CME activity.
                                </label>
                            </div>
                        </div>

                        <ul class="list-group mb-3">
                            <li class="list-group-item">
                                Additional information may be requested to mitigate a relevant financial relationship. All identified relevant financial relationships will be mitigated and disclosure made available to activity participants prior to the start of the CME activity.
                            </li>
                        </ul>

                        <!-- Signature Section -->
                        <div class="mb-4">
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-2 text-md-end">
                                    <strong>Electronic Signature:</strong>
                                </div>
                                <div class="col-md-8">
                                    <input id="eSignature" type="text" class="form-control" name="disclosure_signature" value="<?= ( !empty($author['disclosure_signature']) ? $author['disclosure_signature'] : '')?>" required>
                                </div>
                            </div>

                            <div class="row align-items-center">
                                <div class="col-md-2 text-md-end">
                                    <strong>Date:</strong>
                                </div>
                                <div class="col-md-8">
                                    <?= date('m/d/Y') ?>
                                </div>
                            </div>

                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="frd_save_btn" class="btn btn-success mt-4">Save and Continue</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<!-- JS Section -->
<script>
    $(document).ready(function () {
        let organizationCount = 0;
        let selectedOrganizations = `<?= json_encode($selectedOrganizations) ?>`;
        let financialRelationshipStatus = `<?= ($author['financial_relationship']) ?>`;
        selectedOrganizations = JSON.parse(selectedOrganizations);
        console.log(selectedOrganizations)
        // Show fields based on radio button selection
        $('input[name="financial_relationship"]').change(function () {
            if ($(this).val() === 'yes') {
                $('#organization-list').show();
                $('#add-organization-btn').show();

                //  Populate existing data if available (preserve order)
                if (organizationCount === 0 && selectedOrganizations.length !== 0) {
                    populateOrganizations(selectedOrganizations);
                } else if (organizationCount === 0) {
                    addOrganization();
                }
            } else {
                $('#organization-list').empty().hide();
                $('#add-organization-btn').hide();
                organizationCount = 0;
            }
        });

        //  Trigger the population if data exists
        if (financialRelationshipStatus && financialRelationshipStatus.toLowerCase() === 'yes') {
            if (selectedOrganizations.length !== 0 && Object.keys(selectedOrganizations).length !== 0) {
                $('input[name="financial_relationship"][value="yes"]').prop('checked', true).trigger('change');
            }
        }

        // Add Organization Field (without data)
        $('#add-organization-btn').click(addOrganization);

        //  Populate Existing Organizations (Preserve Order)
        function populateOrganizations(data) {
            for (const key in data) {
                if (data.hasOwnProperty(key)) {
                    addOrganization(data[key], key);
                }
            }
        }

        //  Add Organization Field (with or without data)
        function addOrganization(data = {}, orgId = null) {
            organizationCount++;

            // Directly use the PHP template for rendering
            let html = `<?= view('author/common/organization_template', ['organizationCount' => '${organizationCount}']) ?>`;
            $('#organization-list').append(html);

            // Pre-fill data if provided
            if (orgId) {
                $(`select[name="organization[${organizationCount}][name]"]`).val(data.organization_id);
                $(`input[name="organization[${organizationCount}][id]"]`).val(data.organization_id);

                if (data.affiliations?.length) {
                    data.affiliations.forEach(function (affiliationId) {
                        $(`input[name="organization[${organizationCount}][affiliation][]"][value="${affiliationId}"]`).prop('checked', true);
                    });
                }

                //  If "Other" is selected, show the textbox and pre-fill value
                if (data.organization_id === '29') {
                    $(`.other-organization-input[data-org="${organizationCount}"]`)
                        .val(data.custom_organization || '')
                        .prop('required', true)
                        .attr('data-existing-value', data.custom_organization || '')
                        .closest('div').show();
                }else{
                    $(`.other-organization-input[data-org="${organizationCount}"]`)
                        .val('')
                        .prop('required', false)
                        .closest('div').hide()
                }

            }

            // Handle "Other" Selection (Fixed)
            $(document).on('change', 'select[name^="organization"]', function () {
                let selectedValue = $(this).val();
                let container = $(this).closest('.organization-item'); // Target the specific organization
                let otherInput = container.find('input[name^="organization"][name$="[other_name]"]');

                if (selectedValue == '29') { // Assuming '29' is the ID for "Other"
                    otherInput.show().prop('required', true);
                    otherInput.closest('div').show();

                    //  Populate the input if it exists in the data
                    let existingValue = otherInput.attr('data-existing-value');
                    if (existingValue) {
                        otherInput.val(existingValue);
                    }
                } else {
                    otherInput.hide().val('').prop('required', false);
                    otherInput.closest('div').hide();
                }
            });
        }

        // Remove Organization
        $(document).on('click', '.remove-organization', function () {
            $(this).closest('.organization-item').remove();
            organizationCount--;
            if (organizationCount === 0) $('#add-organization-btn').hide();
        });

        // Save Data
        $('#frd_save_btn').on('click', function (e) {
            e.preventDefault();

            let isValid = true;
            let missingFields = [];
            let form = document.getElementById("copyrightForm");

            if (!form.checkValidity()) {
                form.reportValidity(); // Show validation messages
                return;
            }

            // Check all visible other-organization-input fields
            $('.other-organization-input:visible').each(function () {
                if (!$(this).val().trim()) {
                    isValid = false;
                    let organizationName = $(this).closest('.organization-item').find('label').first().text().trim();
                    missingFields.push(organizationName);
                }
            });

            // Check if at least one affiliation is selected for each organization
            $('.organization-item').each(function () {
                let organizationName = $(this).find('select[name^="organization"]').find(":selected").text().trim();
                let checkedAffiliations = $(this).find('input[name^="organization"][name$="[affiliation][]"]:checked');

                if (checkedAffiliations.length === 0) {
                    isValid = false;
                    missingFields.push(`Affiliation for ${organizationName}`);
                }
            });

            // Check if #yes_relationship is checked but no organization is added
            if ($("#yes_relationship").is(":checked") && $(".organization-item").length === 0) {
                isValid = false;
                missingFields.push("At least one organization is required if you have a relationship.");
            }



            if (!isValid) {
                toastr.error('Please fill in the "Specify Other" field for the following organizations: ' + missingFields.join('<br>'));
                return;
            }


            let formData = new FormData($('form')[0]);

            $.ajax({
                url: '<?= base_url('author/save_financial_relationship') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Thank you for submitting your Financial Relationships.",
                            text: "Click Here to return to the disclosure form",
                            footer: '<a href="<?= base_url() ?>">Return to submission.</a>'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '<?= base_url('author/view_copyright') ?>';
                            }
                        });
                        // alert('Thank you for submitting your Financial Relationships.');

                    } else {
                        alert('Failed to save data.');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('An error occurred while saving the data.');
                }
            });
        });


    });





</script>
