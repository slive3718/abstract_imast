<?php echo view('author/common/menu'); ?>

<main>
    <div class="container py-5">
        <div class="card p-4 shadow-sm">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="mb-4 fw-bold">Financial Relationships</h4>

                    <form action="submit.php" method="POST" class="needs-validation" novalidate>
                        <p>Please select the statement that applies:</p>

                        <!-- Financial Relationship Options -->
                        <div class="form-check mb-2">
                            <input type="radio" class="form-check-input" name="financial_relationship" id="no_relationship" value="no" required>
                            <label class="form-check-label" for="no_relationship">
                                I have held NO financial relationship(s) with an ineligible company within the past 24 months.
                            </label>
                        </div>

                        <div class="form-check mb-4">
                            <input type="radio" class="form-check-input" name="financial_relationship" id="yes_relationship" value="yes">
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


                    </form>
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
                            All individuals with control of content are required to disclose all financial relationships they have held with an ineligible company within the past 24 months, regardless of relevancy.
                        </li>
                    </ul>
                </div>

                <!-- Content Validation Section -->
                <div class="mb-4">
                    <h5 class="fw-bold">Content Validation</h5>
                    <ul class="list-group">
                        <li class="list-group-item">
                            Speakers/authors are required to prepare fair and balanced presentations that are objective and scientifically rigorous.
                            All clinical recommendations must be based on evidence accepted within the medical profession as adequate justification for their indications and contraindications.
                        </li>
                    </ul>
                </div>

                <!-- Unlabeled and Unapproved Uses Section -->
                <div class="mb-4">
                    <h5 class="fw-bold">Unlabeled and Unapproved Uses</h5>
                    <ul class="list-group">
                        <li class="list-group-item">
                            Presentations that provide information in whole or in part related to non-FDA approved uses of drugs and/or devices must clearly disclose the unlabeled indications or the investigational nature of their proposed uses to the audience.
                        </li>
                    </ul>
                </div>

                <!-- Use of Generic vs. Trade Names Section -->
                <div class="mb-4">
                    <h5 class="fw-bold">Use of Generic vs. Trade Names</h5>
                    <ul class="list-group">
                        <li class="list-group-item">
                            The term "hardware" should not be used in written abstract submissions, oral presentations, and E-Poster presentations. The terms "instrumentation," "implants," or "constructs" should be used instead.
                        </li>
                        <li class="list-group-item">
                            SRS prefers that pharmaceuticals and proprietary software/databases be replaced by a generic term or description of the drug, software/database, or instrumentation unless it impacts learners’ understanding.
                        </li>
                    </ul>
                </div>

                <!-- Ineligible Company Influence Section -->
                <div class="mb-4">
                    <h5 class="fw-bold">Ineligible Company Influence</h5>
                    <ul class="list-group">
                        <li class="list-group-item">
                            No payments or gifts after the SRS honorary policy may be given by an ineligible company to the director of the activity, planning committee members, teachers or authors.
                        </li>
                        <li class="list-group-item">
                            Individuals with control over content cannot receive or accept direct input from an ineligible company regarding the content or preparation of the presentation(s).
                        </li>
                    </ul>
                </div>

                <!-- Declaration Section -->
                <div class="mb-4">
                    <h5 class="fw-bold">Declaration</h5>
                    <p>
                        <span class="text-danger">*</span> Check the boxes below indicating that you agree with the statement.
                    </p>

                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="disclosure_support" name="disclosure_support" checked>
                        <label class="form-check-label" for="disclosure_support">
                            Practice recommendations will be supported by the best available evidence.
                        </label>
                    </div>

                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="disclosure_discussed" name="disclosure_discussed" checked>
                        <label class="form-check-label" for="disclosure_discussed">
                            All reasonable clinical alternatives will be discussed.
                        </label>
                    </div>
                </div>

                <!-- Signature Section -->
                <div class="mb-4">
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-2 text-md-end">
                            <strong>Electronic Signature:</strong>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="disclosure_signature" value="sdm" required>
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
                <button type="submit" class="btn btn-success mt-4">Save and Continue</button>
            </div>

        </div>
    </div>
</main>

<!-- JS Section -->
<script>
    $(document).ready(function () {
        let organizationCount = 0;

        // Show fields based on radio button selection
        $('input[name="financial_relationship"]').change(function () {
            if ($(this).val() === 'yes') {
                $('#organization-list').show();
                $('#add-organization-btn').show();
                if (organizationCount === 0) addOrganization();
            } else {
                $('#organization-list').empty().hide();
                $('#add-organization-btn').hide();
                organizationCount = 0;
            }
        });

        // Add Organization Field
        $('#add-organization-btn').click(addOrganization);

        function addOrganization() {
            organizationCount++;
            let html = `<?=view('author/common/organization_template')?>`;
            $('#organization-list').append(html);
        }

        // Remove Organization
        $(document).on('click', '.remove-organization', function () {
            $(this).closest('.organization-item').remove();
            organizationCount--;
            if (organizationCount === 0) $('#add-organization-btn').hide();
        });
    });
</script>
