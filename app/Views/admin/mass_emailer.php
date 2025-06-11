
<link href="<?=base_url()?>/assets/css/jquery_ui_style.css" rel="stylesheet">

<?php echo view('admin/common/menu'); ?>
<?php //print_r($email_recipients); exit;?>

<style>
    .table td {
        vertical-align: middle;
    }
    .table .text-end {
        width: 250px;
    }
    .ck.ck-content:not(.ck-style-grid__button__preview):not(.ck-editor__nested-editable) {
        /* Make sure all content containers have some min height to make them easier to locate. */
        min-height: 300px;
        padding: 1em 1.5em;
    }

    /* Make sure all content containers are distinguishable on a web page even of not focused. */
    .ck.ck-content:not(:focus) {
        border: 1px solid var(--ck-color-base-border);
    }

    /* Fix for editor styles overflowing into comment reply fields */
    .ck-comment__input .ck.ck-content {
        min-height: unset;
        border: 0;
        padding: 0;
    }


</style>
<main style="padding-bottom:100px">
    <div class="container pb-5">
        <?php echo view('event/common/shortcut_link'); ?>

        <div class="card shadow">
            <div class="card-header fw-bolder">
                Step 1. Recipient Type
            </div>
            <div class="card-body">
                <select name="recipientType" id="recipientType">
                    <option value="">--Select--</option>
                    <option value="paper"> Papers </option>
                    <option value="panel"> Panels </option>
                    <option value="regular"> Regular Reviewers </option>
                    <option value="deputy"> Deputies / Program Chairs </option>
                    <option value="moderator"> Session Chairs </option>
                </select>
            </div>

        </div>

        <div class="card shadow mt-2">
            <div class="card-header fw-bolder">
              Step 2. Recipient Group
            </div>
            <div class="card-body">
                <select name="recipient" id="recipientGroup">
                    <option value="">--Select--</option>
                    <?php if(isset($email_recipients)): ?>
                    <?php foreach ($email_recipients as $email_recipient) : ?>
                    <option value="<?=$email_recipient['value'] ?>" data-type="<?=$email_recipient['type']?>"><?= $email_recipient['name'] ?></option>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

        </div>

        <div class="card shadow  mt-2">
            <div class="card-header fw-bolder">
                Step 3. Recipient Designation
            </div>
            <div class="card-body ">
                <div class="recipientDesignationDiv" style="display: none">
                    <div data-for="paper">
                        <input type="checkbox" class="recipientDesignation" id="all_submitters" name="recipientDesignation" value="all_submitters">
                        <label for="all_submitters">Paper Submissions – Submitters</label>
                    </div>
                    <div data-for="paper">
                        <input type="checkbox" class="recipientDesignation" id="presenter_author_only" name="recipientDesignation" value="presenter_author_only">
                        <label for="presenter_author_only">Paper Submissions - Presenting Authors</label>
                    </div>
                    <div data-for="paper">
                        <input type="checkbox" class="recipientDesignation" id="co_author_only" name="recipientDesignation" value="co_author_only">
                        <label for="co_author_only">Paper Submissions – Co-Authors</label>
                    </div>
                    <div data-for="paper">
                        <input type="checkbox" class="recipientDesignation" id="all_correspondents" name="recipientDesignation" value="all_correspondents">
                        <label for="all_correspondents">Paper Submissions – Correspondents Only</label>
                    </div>
                    <div data-for="paper">
                        <input type="checkbox" class="recipientDesignation" id="presenting_author_and_correspondents" name="recipientDesignation" value="presenting_author_and_correspondents">
                        <label for="presenting_author_and_correspondents">Paper Submissions – Presenting Authors with incomplete participation</label>
                    </div>

                    <!--       PANEL Start             -->
                    <div data-for="panel">
                        <input type="checkbox" class="recipientDesignation" id="all_panel_coordinators" name="recipientDesignation" value="all_panel_coordinators">
                        <label for="all_panel_coordinators">All Panel Coordinators</label>
                    </div>
                    <div data-for="panel">
                        <input type="checkbox" class="recipientDesignation" id="all_panelists" name="recipientDesignation" value="all_panelists">
                        <label for="all_panelists">All Panelists</label>
                    </div>

                    <!--       Moderator Start             -->
                    <div data-for="moderator">
                        <input type="checkbox" class="recipientDesignation" id="all_moderators" name="recipientDesignation" value="all_moderators">
                        <label for="all_moderators">All Moderators</label>
                    </div>


                </div>
                <div id="total" class="mt-3 d-none"> Total: <span id="TotalRecipientText"></span> Recipient(s)
                    <a href="" id="showDetailsButton">Show Recipients Detail</a>
                    <span id="notes"></span>
                </div>
                <div class="recipientsDetailsDiv" style="display: none" id="recipientsDetailsDiv">
                    <!--Filled with AJAX-->
                </div>
            </div>
        </div>


        <div class="card shadow  mt-2">
            <div class="card-header fw-bolder">
                Step 4. Testing Option
            </div>
            <div class="card-body">
                Is this a testing environment:  Yes/ No
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="testingSwitch">
                    <label class="form-check-label" for="testingSwitch"> Yes </label>
                </div>


                <div class="mt-2" id="testEmailDiv" style="display: none">
                    <input type="checkbox" id="testEmailOnly"><label for="testEmailOnly">Check here if you want to send just one email to the designated recipient below</label><br>
                    <label for="testEmailTo">Test Recipient Email address :</label> <input type="email" id="testEmailTo" value="" class="form-control" >
<!--                    <a href="#" class="btn btn-sm btn-primary mt-2">Set</a>-->
                    <br>
                    <small class="small">All emails will only be sent to the email above.</small>
                </div>
            </div>
        </div>

        <div class="card shadow mt-3">
            <div class="card-header fw-bolder">
                Step 5. Email Templates
            </div>
            <div class="card-body">
                Do you want to use an email template?  Yes/No <br>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="templateSwitch">
                    <label class="form-check-label" for="templateSwitch">Yes</label>
                </div>

                <div id="emailTemplatesDiv" class="mt-2 " style="display: none">
                    <select id="emailTemplateList" class="form-control">
                    </select>
                </div>
            </div>
        </div>

        <div class="card shadow mt-3">
            <div class="card-header fw-bolder">
                Step 6. Email Subject & Message Body
            </div>
            <div class="card-body">
                <form id="formTemplate" action="" method="post">
                    <div class="mb-3">
                        <label for="emailSubject" class="form-label">Email Subject</label>
                        <input type="text" name="email_subject" class="form-control" id="emailSubject">
                    </div>
                    <div class="card">
                        <div class="card-header"> Fields to merge</div>
                        <div class="card-body">

                            <div class="button-group p-0">
                                <input type="button" value="Abstract ID #" onclick="InsertHTML('##ABSTRACT_ID##')" class="btn btn-primary valid" aria-invalid="false">
                                <input type="button" value="Invitation Code #" onclick="InsertHTML('##INVITATION_CODE##')" class="btn btn-primary valid" aria-invalid="false">
                                <input type="button" value="Abstract Title" onclick="InsertHTML('##ABSTRACT_TITLE##')" class="btn btn-primary">
                            </div>

                            <div class="button-group">
                                <input type="button" value="Panel/Workshop ID #" onclick="InsertHTML('##PANEL_ID##')" class="btn btn-primary">
                                <input type="button" value="Panel/Workshop Title" onclick="InsertHTML('##PANEL_TITLE##')" class="btn btn-primary">
                            </div>

                            <div class="button-group">
                                <input type="button" value="Presenting Author Full Name" onclick="InsertHTML('##PRESENTING_FULL_NAME##')" class="btn btn-primary">
                                <input type="button" value="" onclick="InsertHTML('##PRESENTING_LAST_NAME##')" class="btn btn-primary">
                                <input type="button" value="Presenting Author Email" onclick="InsertHTML('##PRESENTING_EMAIL##')" class="btn btn-primary">
                                <input type="button" value="Presenting Author Prefix" onclick="InsertHTML('##PRESENTING_PREFIX##')" class="btn btn-primary">
                            </div>

                            <div class="button-group">
                                <input type="button" value="Reviewer Username" onclick="InsertHTML('##REVIEW_USERNAME##')" class="btn btn-primary">
                                <input type="button" value="Reviewer Password" onclick="InsertHTML('##REVIEW_PASSWORD##')" class="btn btn-primary">
                            </div>

                            <div class="button-group">
                                <input type="button" value="Session Title" onclick="InsertHTML('##SCHEDULER_SESSION_TITLE##')" class="btn btn-primary">
                                <input type="button" value="Session Date" onclick="InsertHTML('##SCHEDULER_SESSION_DATE##')" class="btn btn-primary">
                                <input type="button" value="Session Start Time" onclick="InsertHTML('##SCHEDULER_SESSION_START_TIME##')" class="btn btn-primary">
                                <input type="button" value="Session End Time" onclick="InsertHTML('##SCHEDULER_SESSION_END_TIME##')" class="btn btn-primary">
                                <input type="button" value="Session Room" onclick="InsertHTML('##SCHEDULER_SESSION_ROOM##')" class="btn btn-primary">
                                <input type="button" value="Room Capacity" onclick="InsertHTML('##SCHEDULER_ROOM_CAPACITY##')" class="btn btn-primary">
                            </div>

                            <div class="button-group">
                                <input type="button" value="Recipient Full Name" onclick="InsertHTML('##RECIPIENTS_FULL_NAME##')" class="btn btn-primary valid" aria-invalid="false">
                                <input type="button" value="Recipient First Name" onclick="InsertHTML('##RECIPIENT_FIRST_NAME##')" class="btn btn-primary">
                                <input type="button" value="Recipient Last Name" onclick="InsertHTML('##RECIPIENTS_LAST_NAME##')" class="btn btn-primary">
                                <input type="button" value="Recipient Email Address" onclick="InsertHTML('##RECIPIENT_EMAIL_ADDRESS##')" class="btn btn-primary">
                            </div>

                            <div class="button-group">
                                <input type="button" value="Accepted Presentation Preference" onclick="InsertHTML('##ACCEPTED_PRESPREF##')" class="btn btn-primary">
                                <input type="button" value="Presentation Date" onclick="InsertHTML('##PRESENTATION_DATE##')" class="btn btn-primary">
                                <input type="button" value="Presentation Time" onclick="InsertHTML('##PRESENTATION_TIME##')" class="btn btn-primary">
                                <input type="button" value="Admin Comments" onclick="InsertHTML('##ADMIN_COMMENTS##')" class="btn btn-primary">
                            </div>

                            <div class="button-group">
                                <input type="button" value="Today's Date" onclick="InsertHTML('##TODAY_DATE##')" class="btn btn-primary">
                                <input type="button" value="Admin Comments to Submitter" onclick="InsertHTML('##ADMIN_COMMENTS_TO_SUBMITTER##')" class="btn btn-primary">
                            </div>
                            <hr>
                        </div>
                    </div>
                    <textarea cols="20" rows="20" id="messageEditor" name="email_message"  class="ckeditor-custom-height"></textarea>
            </div>
        </div>

        <div class="card shadow mt-3">
            <div class="card-header fw-bolder">
                Step 7. File Uploads
            </div>
            <div class="card-body">
                <label for="email_subject">Do you want to attach a file? </label>
                <input type="file" name="attachment" id="attachment" class="form-control mt-2" multiple>
            </div>
        </div>

        <div class="card shadow mt-3">
            <div class="card-header fw-bolder">
                Step 8. Preview and/or Send
            </div>
            <div class="card-body">
               <a class="btn btn-success btn-sm sendEmailBtn" style="width:200px"> Send</a>
               <a class="btn btn-primary btn-sm previewEmailBtn" style="width:200px"> Preview</a>
               <a class="btn btn-danger btn-sm " style="width:100px"> Cancel</a>
            </div>
        </div>

    </div>

</main>


<!-- Preview Email Modal -->
<div class="modal fade" id="previewEmailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header">

                    </div>
                    <div class="card-body">
                        <div class="" id="emailBodyDiv">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Preview Email Modal -->
<div class="modal fade massMailerConfirmationModal" id="massMailerConfirmationModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Mass Mailer Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header">
                        Email Details
                    </div>
                    <div class="card-body">
                        <div class="" id="emailDetails">
                            <div class="">
                                <div class="row mb-2">
                                    <div class="col-12">
                                        <strong>Recipients Type:</strong>
                                        <span id="modal-recipients-type"></span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-12">
                                        <strong>Recipients Filter:</strong>
                                        <span id="modal-recipients-filter"></span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-12">
                                        <strong>Recipients Number:</strong>
                                        <span id="modal-recipients-number"></span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-12">
                                        <strong>Session Test Mode:</strong>
                                        <span id="modal-session-test-mode"></span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-12">
                                        <strong>Recipient email for test:</strong>
                                        <span id="modal-recipient-email"></span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-12">
                                        <strong>Send just one email in test:</strong>
                                        <span id="modal-send-one-email"></span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-12">
                                        <strong>Email Template:</strong>
                                        <span id="modal-email-template"></span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-12">
                                        <strong>Email Subject:</strong>
                                        <span id="modal-email-subject"></span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-12">
                                        <strong>Attached File:</strong>
                                        <span id="modal-attached-file"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="card-footer">
                    <div>Are you sure you want to send this email now ?</div>
                    <div class="d-flex mt-2">
                        <button type="button" class="btn btn-success btn-sm" onclick="confirmSendEmail()">Send Email</button>
                        <button type="button" class="btn btn-secondary btn-sm ms-auto" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
<!--<script src=" https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-essentials@41.3.1/src/essentials.min.js "></script>-->
<script>
    var base_url_admin = "<?=base_url().'admin/'?>"
    var newEditor;
    ClassicEditor
        .create( document.querySelector( '#messageEditor' ),
            {
                enterMode: 'BR', // This sets Enter to insert <br>
                shiftEnterMode: 'P' // Optional: Shift+Enter to insert <p>. Change to 'BR' for <br>
            }).then(editor => {
        newEditor = editor;
        // Apply custom height using JavaScript
        editor.ui.view.editable.element.style.minHeight = '250px'; // Adjust this value to match 10 rows
    })
        .catch( error => {
            console.error( error );
        } );

</script>

<script>
    var base_url_admin = "<?=base_url().'admin/'?>"
    $(function(){
        const showRecipientDesignation = (type) => {
            $('.recipientDesignationDiv div').each(function() {
                const dataFor = $(this).data('for');
                if (dataFor === type) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            $('.recipientDesignationDiv').css('display', type ? 'block' : 'none');
        };

        const showRecipientGroupOptions = (type) => {
            $('#recipientGroup option').each(function() {
                const optionType = $(this).data('type');
                if (optionType === type) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        };

        const resetFields = () => {
            $('#recipientDesignation').val('');
            $('#recipientDesignation').change();
            $('#recipientGroup').val('');
        };

        const uncheckAllCheckboxes = () => {
            $('.recipientsDetailsDiv input[type="checkbox"]').prop('checked', false);
        };

        $('#recipientType').on('change', function() {
            const recipientType = $(this).val();
            resetFields();

            // Hide all designation and recipient details
            $('.recipientDesignationDiv').css('display', 'none');
            $('.recipientsDetailsDiv').css('display', 'none');

            // Uncheck all checkboxes whenever the recipientType changes
            uncheckAllCheckboxes();

            switch (recipientType) {
                case 'paper':
                    showRecipientDesignation('paper');
                    showRecipientGroupOptions('paper');
                    break;
                case 'deputy':
                    showRecipientGroupOptions('deputy');
                    break;
                case 'regular':
                    showRecipientGroupOptions('regular');
                    break;
                case 'panel':
                    showRecipientDesignation('panel');
                    showRecipientGroupOptions('panel');
                    break;
                case 'moderator':
                    showRecipientDesignation('moderator');
                    showRecipientGroupOptions('moderator');
                    break;
                default:
                    // No matching type, everything is hidden
                    break;
            }

            if (recipientType === 'deputy' || recipientType === 'regular') {
                $('.recipientsDetailsDiv').css('display', 'block');
            } else if (recipientType === 'panel' || recipientType === 'moderator') {
                $('.recipientsDetailsDiv').css('display', 'block');
            }
        });

        $('#recipientGroup').on('change', function(){
            clearSelectedOptions();
            let recipientGroup = $('select[name="recipient"]').val();
            let recipientType = $('select[name="recipientType"]').val();
            if($(this).val() === 'all_dpc'){
                clearSelectedOptions();
                getFilteredDesignation('all_dpc', recipientType, recipientGroup);
                $('#total').removeClass('d-none').addClass('d-block');
            }else if($(this).val() === 'all_regular'){
                clearSelectedOptions();
                getFilteredDesignation('all_regular', recipientType, recipientGroup);
                $('#total').removeClass('d-none').addClass('d-block');
            }else if($(this).val() === 'all_regular_incomplete'){
                clearSelectedOptions();
                getFilteredDesignation('all_regular_incomplete', recipientType, recipientGroup);
                $('#total').removeClass('d-none').addClass('d-block');
            }

            if(recipientType === 'panel' && ($(this).val() === 'all_accepted' || $(this).val() === 'all_incomplete')){
                $('#all_panel_coordinators').closest('div').hide();
            }else if(recipientType === 'panel'){
                $('#all_panel_coordinators').closest('div').show();
            }


        })

        $('.recipientDesignation').on('change', function(){
            $('#recipientsTable').html('');
            let selectedOptions = [];

            $('.recipientDesignation:checked').each(function() {
                selectedOptions.push($(this).val());
            });

            let recipientGroup = $('select[name="recipient"]').val();
            let recipientType = $('select[name="recipientType"]').val();

            if($(this).is(':checked') && (recipientGroup === 'incomplete_participation' || recipientGroup === 'all_papers')){
                if($(this).val()  === "presenter_author_only"){
                    $('#notes').html("NOTE: Some incomplete abstracts may not have any presenting authors or co-authors")
                }
            }else{
                $('#notes').html('');
            }

            if(selectedOptions.length === 0){
                $('#total').removeClass('d-block').addClass('d-none');
                clearSelectedOptions();
            } else {
                $('#total').removeClass('d-none').addClass('d-block');
                getFilteredDesignation(selectedOptions.join(','), recipientType, recipientGroup);
            }
        });

        $('#showDetailsButton').on('click', function(e){
            e.preventDefault();
            $('#recipientsDetailsDiv').toggle()
        })

        $('#testingSwitch').on('change', function() {
            if ($(this).is(':checked')) {
                $('#testEmailDiv').show();
            } else {
                $('#testEmailDiv').hide();
            }
        });

        $('#templateSwitch').on('change', function() {
            if ($(this).is(':checked')) {
                $('#emailTemplatesDiv').show();
                getTemplates(function(result) {
                    $('#emailTemplateList').html('<option value="">-- Select --</option>')
                    $.each(result.data, function (index, email_template) {
                        $('#emailTemplateList').append('<option value="' + email_template.id + '">' + email_template.template_name + '</option>')
                    });
                })
            } else {
                $('#emailTemplatesDiv').hide();
            }
        });

        $('#emailTemplateList').on('change', function(){
            let selected = $(this).val()
            // getTemplate(selected);
            getTemplate(selected, function(result) {
                let emailCategory = result.data.email_category;
                $('#emailSubject').val(result.data.email_subject);
                $('input[name="email_category"][value="' + emailCategory + '"]').prop('checked', true);
                newEditor.setData(result.data.email_body)
            });
        })

        $('.previewEmailBtn').on('click', function(){
            previewEmail('preview', function(response){
                Swal.close();
                $('#emailBodyDiv').html('');
                if (response.status == 200) {
                    $.each(response.data, function(i, val) {
                        $('#emailBodyDiv').append(`
                    <div class="card mt-2">
                    <div class="card-header"> Sending To: ${val.name}  ${val.surname} </div>
                    <div class="card-body">${val.email_template}</div>
                    </div>`);
                    });
                }
            });

            $('#previewEmailModal').modal('show')
        })

        $('.sendEmailBtn').on('click', function(){
            $('#massMailerConfirmationModal').modal('show')

            // Get the selected Recipient Type from Step 1
            let recipientType = $('#recipientType option:selected').val();
            $('#modal-recipients-type').text(recipientType ? recipientType : "None");

            // Get the selected Recipient Group from Step 2
            let recipientGroup = $('#recipientGroup option:selected').val();
            $('#modal-recipients-group').text(recipientGroup ? recipientGroup : "None");

            // Get selected checkboxes from Step 3: Recipient Designation
            let selectedDesignations = [];
            $('.recipientDesignation:checked').each(function() {
                selectedDesignations.push($(this).next('label').text());
            });
            $('#modal-recipients-filter').text(selectedDesignations.length ? selectedDesignations.join(', ') : "None");

            // Show the Total Recipients number (you can fetch this dynamically based on selections if needed)
            $('#modal-recipients-number').text($('#TotalRecipientText').text() || "0");

            // Check if testing mode is on and populate the related fields from Step 4
            let isTesting = $('#testingSwitch').is(':checked');
            $('#modal-session-test-mode').text(isTesting ? "Yes, the email will be sent to the test recipient only." : "No, emails will be sent to all recipients.");
            $('#modal-recipient-email').text(isTesting ? $('#testEmailTo').val() : "N/A");

            // Check if only one email should be sent in test mode
            let sendOneEmail = $('#testEmailOnly').is(':checked');
            $('#modal-send-one-email').text(sendOneEmail ? "Yes" : "No");

            // Populate email template details from Step 5
            let isTemplateUsed = $('#templateSwitch').is(':checked');
            $('#modal-email-template').text(isTemplateUsed ? $('#emailTemplateList option:selected').text() : "None");

            // Populate email subject from Step 6
            let emailSubject = $('#emailSubject').val();
            $('#modal-email-subject').text(emailSubject ? emailSubject : "No Subject");

            // Handle file attachment from Step 7
            let files = $('#attachment')[0].files;
            if (files.length > 0) {
                let fileNames = Array.from(files).map(file => file.name).join(', ');
                $('#modal-attached-file').text(fileNames);
            } else {
                $('#modal-attached-file').text("None");
            }


            // $('#previewEmailModal').modal('show')
        })

    })

    function confirmSendEmail(){
        $('#massMailerConfirmationModal').modal('hide');

        previewEmail('send', function(response){
            $('#emailBodyDiv').html('');
            if (response.status == 200) {
                swal.fire({
                    'title':'success',
                    'icon': 'success',
                    'text': 'Email sent successfully!'
                });
            }else{
                swal.fire({
                    'title':'error',
                    'icon': 'error',
                    'text': 'Failed to send Email. Please contact administrator.'
                });
            }
        });
    }

    function getFilteredDesignation(selectedOption, recipientType, recipientGroup){
        console.log(selectedOption, recipientType, recipientGroup)

        $.post(base_url_admin + 'get_all_users_filtered',{
            'selectedOption': selectedOption,
            'recipientType':recipientType,
            'recipientGroup':recipientGroup
        }, function(response) {
            console.log(response)
            $('#TotalRecipientText').html(response.data.length);
            var tableHTML = '<table class="table table-striped table-bordered table-hover" id="recipientsTable">';
            tableHTML += '<thead> ' +
                '<th><input type="checkbox" name="selectAllRecipient" id="selectAllRecipient" onclick="checkAllRecipients(this)" ><label for="selectAllRecipient"> Select All</label>' +
                '</th><th>Recipients Name</th> <th>Abstract ID</th> <th>Email</th>   </thead>'

            response.data.forEach(function(user) {
                // console.log(user)
                tableHTML += '<tr>';
                tableHTML += '<td><input type="checkbox" class="recipientsCheckbox" checked author_id="'+user.details.id+'" name="'+user.details.name+'"  surname="'+user.details.surname+'" email="'+ user.details.email +'" paper_id="'+user.paper_id+'" filter="'+user.filter+'"></td>';
                tableHTML += '<td>' + user.details.name + ' ' + user.details.surname + '</td>';
                tableHTML += '<td class="abstractID">' + (user.paper_id !== undefined ? user.paper_id : '') + '</td>';
                tableHTML += '<td>' + user.details.email +'</td>';
                tableHTML += '<td class="designation d-none">' + user.filter + '</td>';
                tableHTML += '</tr>';
            });

            tableHTML += '</table>';

            $('#recipientsDetailsDiv').html(tableHTML);

            // Attach a single event handler to the table for both checkbox changes and "select all" clicks
            $('#recipientsTable').on('change', '.recipientsCheckbox, #selectAllRecipient', function() {
                updateCheckedCount();
            });

            // Function to update the total checked count
            function updateCheckedCount() {
                let checkedCount = $('#recipientsTable .recipientsCheckbox:checked').length;
                $('#TotalRecipientText').html(checkedCount);
            }

        }, 'json');
    }

    function clearSelectedOptions(){
            $('#TotalRecipientText').html('');
            var tableHTML = '<table class="table table-striped table-bordered table-hover" id="recipientsTable">';
            tableHTML += '</table>';
            $('#recipientsDetailsDiv').html(tableHTML);

        $('.recipientDesignationDiv input[type="checkbox"]').each(function(){
            $(this).prop('checked', false); // Uncheck the checkbox
        });
    }

    function getTemplates(callback) {
        $.get(base_url_admin + 'get_all_email_templates', function(result) {
            callback(result);
        }, 'json');
    }

    function getTemplate(template_id, callback){
        $.get(base_url_admin+'get_email_templates/'+template_id, function(result){
            callback(result);
        },'json')

    }


    function InsertHTML(value) {
        newEditor.model.change(writer => {
            const insertPosition = newEditor.model.document.selection.getFirstPosition();
            writer.insertText(value, insertPosition);
        });

        newEditor.focus();
        // Set the selection to the end of the document
        newEditor.model.change(writer => {
            const position = writer.createPositionAt(newEditor.model.document.getRoot(), 'end');
            writer.setSelection(position);
        });
    }

    function previewEmail(action, callback) {
        let recipients = [];
        let recipientDesignations = $("input[name='recipientDesignation']:checked").map(function(){
            return $(this).val();
        }).get();
        let message_body = newEditor.getData();
        let testingSwitch = $('#testingSwitch').is(':checked') ? 'on' : 'off';
        let testEmailOnly = $('#testEmailOnly').is(':checked') ? 'on' : 'off';
        let emailTemplateList = $('#emailTemplateList').val();

        $('.recipientsCheckbox').each(function() {
            if($(this).is(":checked")){
                recipients.push({
                    author_id: $(this).attr('author_id'),
                    abstract_id: $(this).attr('paper_id'),
                    filter: $(this).attr('filter')
                });
            }
        });

        // console.log(recipients);return false;
        let emailSubject = $('#emailSubject').val();

        $('#previewEmailModal .card-header').html('').append(
            '<p>Recipients Total: '+ recipients.length +'</p>' +
            '<p>Session Test Mode: '+ testingSwitch +'</p>' +
            '<p>Email Subject: '+ emailSubject +'</p>'
        );

        // Prepare FormData to send along with attachments
        let formData = new FormData();
        formData.append('template_id', emailTemplateList);
        formData.append('message_body', message_body);
        formData.append('email_subject', emailSubject);
        formData.append('test_mode', testingSwitch);
        formData.append('test_email_to', $('#testEmailTo').val());
        formData.append('test_email_only', testEmailOnly);
        formData.append('action', action);
        formData.append('recipientType', $('#recipientType').val());
        formData.append('recipientGroup', $('#recipientGroup').val());

        // Append recipientDesignations array
        recipientDesignations.forEach(function(designation) {
            formData.append('recipientDesignation[]', designation);
        });

        // Append recipients array
        recipients.forEach(function(recipient) {
            formData.append('recipients[]', JSON.stringify(recipient));
        });

        // Attach files
        let emailAttachments = $('#attachment')[0].files;
        for (let i = 0; i < emailAttachments.length; i++) {
            formData.append('attachments[]', emailAttachments[i]);
        }

        $.ajax({
            url: base_url_admin + 'get_preview_email',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            beforeSend: function() {
                if(action !== 'preview') {
                    Swal.fire({
                        title: 'Sending...',
                        text: 'Please wait.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        onBeforeOpen: () => {
                            Swal.showLoading();
                        }
                    });
                }
            },
            success: function(response) {
                callback(response);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }
    

    function replaceFieldsKeyWords(message_body, email, callback){
        let result = message_body
            .replace('##PRESENTING_FULL_NAME##', recipient.fullName)
            .replace('##test##', recipient.testValue);

        callback(result)

    }

    function getAbstractID(){

    }

    function checkAllRecipients(e) {
        var isChecked =$(e).prop('checked');
        if(isChecked){
            $('.recipientsCheckbox').each(function(){
               $(this).prop('checked', true)
            })
        }else{
            $('.recipientsCheckbox').each(function(){
                $(this).prop('checked', false)
            })
        }
    }
</script>
