
<!-- Add Author Modal -->
<div class="modal fade" id="addAuthorModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"  data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add an Author</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSaveAuthor" action="" method="post" enctype="multipart/form-data" role="form">
                    <!-- Nav tabs -->
                    <input type="hidden" name="author_id" id="author_id" value="">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="true">Personal Information</button>
                        </li>
                        <?php if (1 == 2): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="other-details-tab" data-bs-toggle="tab" data-bs-target="#other-details" type="button" role="tab" aria-controls="other-details" aria-selected="false">Address Information</button>
                        </li>
                        <?php endif ?>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                            <!-- Personal Information Fields -->
                            <div class="row mb-3">
                                <!-- First, Middle, and Last Name -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" for="authorFName">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="authorFName" id="authorFName" class="form-control required" placeholder="Enter your first name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" for="authorMName">Middle Name</label>
                                    <input type="text" name="authorMName" id="authorMName" class="form-control" placeholder="Enter your middle name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" for="authorLName">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="authorLName" id="authorLName" class="form-control required" placeholder="Enter your last name" required>
                                </div>

                                <!-- Primary & Confirm Email -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" for="authorEmail">Primary Email <span class="text-danger">*</span></label>
                                    <input type="email" name="authorEmail" id="authorEmail" class="form-control required" placeholder="Enter your email" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" for="authorConfirmEmail">Retype Email <span class="text-danger">*</span></label>
                                    <input type="email" name="authorConfirmEmail" id="authorConfirmEmail" class="form-control required" placeholder="Retype your email" required>
                                </div>

                                <!-- Work Phone & Fax -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" for="authorPhone">Work Phone </label>
                                    <input type="tel" name="authorPhone" id="authorPhone" class="form-control shadow-none" placeholder="Enter your work phone">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" for="authorFax">Cell Phone</label>
                                    <input type="text" name="cellphone" id="cellphone" class="form-control shadow-none" placeholder="Enter cellphone number">
                                </div>

                                <!-- Designations -->
                                <div class="col-md-12 mb-4">
                                    <label class="form-label fw-bold">Designations <span class="text-danger">*</span></label>
                                    <div class="designationDiv d-flex flex-wrap gap-3"></div>

                                    <!-- "Other" Designation Input (Hidden Initially) -->
                                    <div id="otherDesignationContainer" class="mt-2" style="display: none;">
                                        <input type="text" name="other_designation" id="other_designation"
                                               class="form-control shadow-none" placeholder="Enter other designation">
                                    </div>
                                </div>

                                <!-- IF checkbox is study group  hide the dropdown of study groups -->
                                <div class="col-md-12 mb-3">
                                    <input class="form-check-input" name="is_study_group" type="checkbox" role="switch" id="studyGroupSwitch">
                                    <label class="form-check-label fw-bolder" for="studyGroupSwitch">Save as Study Group <small class="text-danger"> Check this only when the type of author is a group. </small></label>
                                </div>

                                <!-- Institution  -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold" for="authorInstitution">Institution <span class="text-danger">*</span></label>
                                    <!--                                    <input type="text" name="authorInstitution" id="authorInstitution" class="form-control required" placeholder="Enter your institution" required>-->

                                    <p>Please start typing the name of this presenter's affiliation. This field will auto complete after four letters have been entered. If your institution is not in the database, 'Add New", will appear.  Choose that option and follow the prompts.</p>
                                    <div class="input-group ">
                                        <label class="input-group-text text-white" style="background-color:#2AA69C" for="authorInstitution">Find Institution <font color="red">* </font></label>
                                        <input name="authorInstitution" title="Institution" id="authorInstitution" class="form-control shadow-none required">
                                        <input name="authorInstitutionId" title="Institution" type="hidden" id="searchId" class="required">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <?php if(1 == 2): ?>
                        <div class="tab-pane fade" id="other-details" role="tabpanel" aria-labelledby="other-details-tab">
                            <!-- Other Details Fields -->
                            <div class="row my-3">
                                <!-- Your other details fields here -->
                                <div class="col">

                                    <div class="mb-3">
                                        <label class="form-label" for="authorAddress">Address</label>
                                        <input type="text" name="authorAddress" title="Address" class="form-control shadow-none" id="authorAddress" style="max-width:400px" placeholder="">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="authorCountry">Country <span class="text-danger">*</span></label>
                                        <input type="text" name="authorCountry" title="Country" class="form-control shadow-none"  id="authorCountry" style="max-width:400px" placeholder="" required>
                                        <input type="text" name="authorCountryId" title="Country" class="form-control shadow-none d-none" id="authorCountryId" style="max-width:400px" placeholder="">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="authorProvince">Province/State <span class="text-danger">*</span></label>
                                        <input type="text" name="authorProvince" title="Province" class="form-control shadow-none"  id="authorProvince" style="max-width:400px" placeholder="" required>
                                        <input type="text" name="authorProvinceId" title="Province" class="form-control shadow-none d-none" id="authorProvinceId" style="max-width:400px" placeholder="">
                                    </div>

                                </div>
                                <div class="col">

                                    <div class="mb-3">
                                        <label class="form-label" for="authorCity">City <span class="text-danger">*</span></label>
                                        <input type="text" name="authorCity" title="City" class="form-control shadow-none" id="authorCity" style="max-width:400px" placeholder="" required>
                                        <input type="text" name="authorCityId" title="City" class="form-control shadow-none d-none" id="authorCityId" style="max-width:400px" placeholder="">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="authorZipcode">Postal Code/Zip Code</label>
                                        <input type="text" name="authorZipcode" title="Postal Code" class="form-control shadow-none"  id="authorZipcode" style="max-width:400px" placeholder="">
                                        <input type="text" name="authorZipcodeId" title="Postal Code" class="form-control shadow-none d-none" id="authorZipcodeId" style="max-width:400px" placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif ?>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary closeBtn" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
            </form>
        </div>
    </div>
</div>

<script>

    $(document).ready(function() {
        function toggleOtherDesignation() {
            let isChecked = $('input[name="designations[]"][value="13"]').is(':checked');
            $('#otherDesignationContainer').toggle(isChecked);

            // Check if "None" (value = 12) is selected
            let isNoneChecked = $('input[name="designations[]"][value="12"]').is(':checked');

            if (isNoneChecked) {
                // Uncheck and disable all other checkboxes except "None"
                $('input[name="designations[]"]').not('[value="12"]').prop('checked', false).prop('disabled', true);
            } else {
                // Enable all checkboxes when "None" is unchecked
                $('input[name="designations[]"]').prop('disabled', false);
            }
        }
        // Run on modal load
        $('#addAuthorModal').on('shown.bs.modal', function() {
            toggleOtherDesignation(); // Check status on modal load
        });

        // Run when a checkbox is clicked
        $(document).on('change', 'input[name="designations[]"]', function() {
            toggleOtherDesignation();
        });

        $('#designations').prepend('<option value=""> -- Select Designation -- </option>')

        fetchDesignations().then(designations => {
            let designationHTML = `<div class="d-flex flex-wrap gap-3">`;

            $.each(designations, function(i, designation) {
                designationHTML += `
                                <div class="form-check">
                                    <input class="form-check-input required" type="checkbox" name="designations[]"
                                        id="designation_${designation.designation_id}" value="${designation.designation_id}">
                                    <label class="form-check-label fw-bold" for="designation_${designation.designation_id}">
                                        ${designation.name}
                                    </label>
                                </div>
                            `;
            });

            designationHTML += `</div>`;
            $('.designationDiv').html(designationHTML);
        });

        $('#studyGroupSwitch').on('change', function(){
            if($('#studyGroupAffiliatedSwitch').is(':checked')){
                $(this).prop('checked', false)
            }
        })

    });


    async function fetchDesignations() {
        try {
            return await $.ajax({
                url: base_url + '/user/get_designations',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                method: "POST",
                dataType: "json"
            });

        } catch (error) {
            console.error("Error fetching designations:", error);
            return []; // Return an empty array if an error occurs
        }
    }


</script>