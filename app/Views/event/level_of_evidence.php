
<!--<script  type="text/javascript" src="--><?php //=base_url('assets/js/submissionFunction.js?v=4')?><!--"></script>-->


<?php echo view('event/common/menu'); ?>
<?php echo view('event/common/event_details'); ?>
<?php //=print_r(session('user_type'));exit;?>
<main>
    <div class="container pb-5">
        <?php echo view('event/common/shortcut_link'); ?>
        <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1 shadow">
            <form id="level_of_evidence_form"  action="" method="post">
                <label for="min_follow_up_period" class="fw-bolder h6 mt-2"><span class="text-danger">*</span> What was the minimum time period of follow-up? <br></label> <p class="">Follow up is now one year instead of two.</p>
                <input type="text" name="min_follow_up_period" id="min_follow_up_period" class="form-control" required value="<?=!empty($paper) && $paper['min_follow_up_period'] ? $paper['min_follow_up_period'] : ''?>">

                <label for="is_srs_funded_yes"  class="fw-bolder h6 mt-2"> <span class="text-danger">*</span> Was this study funded by a SRS Grant? </label> <br>
                <input type="radio" name="is_srs_funded" id="is_srs_funded_yes" value="Yes" class="" required <?=!empty($paper) && $paper['is_srs_funded'] == 'Yes' ? 'checked' : '' ?>>
                <label for="is_srs_funded_yes"  class="fw-bolder h6 mt-2"> Yes </label> <br>

                <input type="radio" name="is_srs_funded" id="is_srs_funded_no" value="No" class="" required <?=!empty($paper) && $paper['is_srs_funded'] == 'No' ? 'checked' : '' ?>>
                <label for="is_srs_funded_no"  class="fw-bolder h6 mt-2"> No </label> <br>

                <div id="is_srs_funded_yes_extended_info">
                <label for="primary_investigator"  class="fw-bolder h6 mt-2"> <span class="text-danger">*</span> Who is the Primary Investigator?</label>
                <input type="text" name="primary_investigator" id="primary_investigator" class="form-control" required value="<?=!empty($paper) && $paper['primary_investigator'] ? $paper['primary_investigator'] : ''?>">

                <label for="grant_year"  class="fw-bolder h6 mt-2"> <span class="text-danger">*</span> What is the grant year? </label>
                <input type="text" name="grant_year" id="grant_year" class="form-control" required value="<?=!empty($paper) && $paper['grant_year'] ? $paper['grant_year'] : ''?>">
                </div>

                <input type="submit" class="btn btn-success btn-sm mt-4" value="Submit">
            </form>
        </div>
    </div>
</main>

<script>
    let previousUrl = "<?= $previous_url ?? ''?>"
    let previousPage = "<?= $previous_page ?? ''?>"
    $(function() {
        $('#level_of_evidence_form').on('submit', function(e){
            e.preventDefault();

            if (!validateRequired()) {
                return false; // Stop submission if validation fails
            }

            let formData = new FormData(document.getElementById('level_of_evidence_form'));
            formData.append('paper_id', `<?=$paper_id?>`)


            $.ajax({
                url: "<?= base_url().'user/update_paper_ajax' ?>",
                type: "POST", // Change to "GET" if needed
                data: formData,
                processData: false, // Expecting JSON response
                contentType: false, // Expecting JSON response
                dataType: "json", // Expecting JSON response
                success: function(response) {
                    if (response.status === 200) {

                        if((previousPage === 'finalize_paper' || previousPage === 'submission_menu') && previousUrl) {
                            return window.location.href = previousUrl
                        }

                        window.location.href = base_url + `/user/presentation_upload/<?=$paper_id?>`;
                    } else {
                        alert("Error updating paper: " + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                }
            });

            return false;
        })






        $("input[name='is_srs_funded']").on("change", toggleFields);

        // Initial check on page load
        toggleFields();
    })


    function validateRequired() {
        let isValid = true;
        let errorMessage = "";

        // Check text inputs
        $('#level_of_evidence_form input[type="text"]').each(function() {
            if ($(this).prop('required') && $(this).val().trim() === "") {
                isValid = false;
                errorMessage += `- ${$(this).prev('label').text().trim()} is required.\n`;
            }
        });

        // Check radio buttons
        let radioGroups = ['is_srs_funded']; // List of required radio name attributes
        radioGroups.forEach(function(name) {
            if (!$(`input[name="${name}"]:checked`).length) {
                isValid = false;
                errorMessage += `- Please select an option for ${name.replace('_', ' ')}.\n`;
            }
        });

        if (!isValid) {
            alert("Please fix the following errors:\n" + errorMessage);
        }

        return isValid;
    }

    function toggleFields() {

        const srsGrantYes = $("#is_srs_funded_yes");
        const srsGrantNo = $("#is_srs_funded_no");
        // const primaryInvestigator = $("#primary_investigator").closest("label");
        // const grantYear = $("#grant_year").closest("label");
        const is_srs_funded_yes_extended_info = $('#is_srs_funded_yes_extended_info')

        if (srsGrantYes.is(":checked")) {
            is_srs_funded_yes_extended_info.show();
            $('#primary_investigator').attr('required', true)
            $('#grant_year').attr('required', false)
        } else {
            is_srs_funded_yes_extended_info.hide();
            $('#primary_investigator').attr('required', false)
            $('#grant_year').attr('required', false)
        }
    }

</script>

