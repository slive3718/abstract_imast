
<link href="<?=base_url()?>/assets/css/jquery_ui_style.css" rel="stylesheet">

<?php echo view('admin/common/menu'); ?>
<?php //print_r($learning_objectives); exit;?>

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


    .ck.ck-balloon-panel.ck-balloon-panel_arrow_nw.ck-balloon-panel_visible.ck-balloon-panel_with-arrow {
        z-index: 100009 !important;
    }
    .ck-body-wrapper{
        z-index: 10000;
    }

</style>
<main style="padding-bottom:100px">
    <div class="container pb-5">
        <?php echo view('event/common/shortcut_link'); ?>
        <div class="card shadow">
            <div class="card-header">
              Email Templates
            </div>
            <div class="card-body">
                <a href="#" class="btn btn-primary btn-sm addNewTemplateBtn"> Add New Template</a>

                <div class="mt-3">
                    <div class="" id="templates-content">
<!--                        Filled with Ajax-->
                    </div>
                </div>

            </div>

        </div>

    </div>
</main>

<!-- Template Modal -->
<div class="modal fade" id="templateModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Email Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formTemplate" action="" method="post">
                    <div class="mb-3">
                        <label for="templateName" class="form-label">Template Name</label>
                        <input type="text" name="template_name" class="form-control" id="templateName" aria-describedby="emailHelp">
<!--                        <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>-->
                    </div>
                    <div class="mb-3">
                        <label for="emailSubject" class="form-label">Email Subject</label>
                        <input type="text" name="email_subject" class="form-control" id="emailSubject">
                    </div>
                    <div class="mb-3">
                        <label class="form-check-label" for="emailDescription">Description</label>
                        <input type="text" name="email_description" class="form-control" id="emailDescription">
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="email_category" id="emailCategory1" value="1" >
                        <label class="form-check-label" for="emailCategory1">
                            Subscribe reviewer for the proposal
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="email_category" id="emailCategory2" value="2" >
                        <label class="form-check-label" for="emailCategory2">
                            Program Chair Template
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="email_category" id="emailCategory3" value="3" >
                        <label class="form-check-label" for="emailCategory3">
                            None
                        </label>
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

                            <div class="button-group">
                                <input type="button" value="Submitter Name" onclick="InsertHTML('##SUBMITTER_NAME##')" class="btn btn-primary">
                                <input type="button" value="Submitter Surname" onclick="InsertHTML('##SUBMITTER_SURNAME##')" class="btn btn-primary">
                            </div>

                            <hr>

                        </div>

                    </div>

                    <textarea cols="20" rows="20" id="messageEditor" name="email_message"  class="ckeditor-custom-height"></textarea>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary saveTemplateBtn">Save Template</button>
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
        .create(document.querySelector('#templateModal #messageEditor'), {
            enterMode: 'BR', // Sets Enter to insert <br>
            shiftEnterMode: 'P' // Optional: Shift+Enter to insert <p>. Change to 'BR' for <br>
        })
        .then(editor => {
            newEditor = editor;

            // Apply custom height using JavaScript
            editor.ui.view.editable.element.style.minHeight = '250px'; // Adjust this value to match 10 rows

            // Add event listener for link clicks inside the editor
            editor.ui.view.editable.element.addEventListener('click', function (event) {
                if (event.target.tagName === 'A') {
                    // Scroll the modal to the top
                    var modal = document.querySelector('#templateModal');
                    if (modal) {
                        modal.scrollTop = 0;
                    }
                }
            });
        })
        .catch(error => {
            console.error(error);
        });
</script>


<script>

    $(function(){
        $( '#templateModal' ).modal( {
            focus: false
        } );
        getTemplates();
        $('.addNewTemplateBtn').on('click', function(){
            $('#templateModal').modal('show');
            $('#formTemplate')[0].reset();
            $('.saveTemplateBtn').removeAttr('template_id').html('Save Template')
        })

        $('.saveTemplateBtn').on('click', function(){
            let formData = new FormData(document.getElementById('formTemplate'));
            let message = newEditor.getData();
            let template_id = $(this).attr("template_id");
            formData.append('template_id', template_id)
            formData.append('message', message)
            $.ajax({
                url: base_url_admin+'save_email_template',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    getTemplates();
                    toastr.success('Form submitted successfully!');
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    toastr.error('An error occurred while submitting the form.');
                    console.error(error);
                }
            });

            $('#templateModal').modal('hide');
            $('#templateModal').modal('reset');
        })

        $('#templates-content').on('click','.editTemplateBtn', function(){
            $('#formTemplate')[0].reset();
            let template_id = $(this).attr("template_id");
            console.log(template_id)
            $.get(base_url_admin+'get_email_templates/'+template_id, function(result){
                let emailCategory = result.data.email_category;
                $('#templateName').val(result.data.template_name);
                $('#emailSubject').val(result.data.email_subject);
                $('#emailDescription').val(result.data.email_description);
                $('input[name="email_category"][value="' + emailCategory + '"]').prop('checked', true);
                newEditor.setData(result.data.email_body)
            },'json')

            $('.saveTemplateBtn').attr('template_id', template_id).html('Update Template')
            $('#templateModal').modal('show')
            getTemplates();
        })


        $('#templates-content').on('click','.deleteTemplateBtn', function(){
            alert();
        })
    })

    function InsertHTML(value) {
        newEditor.model.change(writer => {
            const insertPosition = newEditor.model.document.selection.getFirstPosition();
            writer.insertText(value, insertPosition);
        });

        newEditor.focus();
        // Set the selection to the end of the document
        // newEditor.model.change(writer => {
        //     const position = writer.createPositionAt(newEditor.model.document.getRoot(), 'end');
        //     writer.setSelection(position);
        // });

        newEditor.model.change(writer => {
            const root = newEditor.model.document.getRoot();
            const insertPosition = writer.createPositionAt(root, 'end');

            // Get the position right after the inserted text
            const endPosition = writer.createPositionAfter(insertPosition.nodeAfter);

            // Set the selection to the end position of the inserted text
            writer.setSelection(endPosition);
        });
    }

    function getTemplates(){
        $.get(base_url_admin+'get_all_email_templates', function(result){
            $('#templates-content').html('');
            $.each(result.data, function(index, email_template) {
                let editBtn = '<a href="#" class="btn btn-primary btn-sm editTemplateBtn" template_id="' + email_template.id + '">Edit</a>';
                let deleteBtn = '<a href="#" class="btn btn-danger btn-sm deleteTemplateBtn" template_id="' + email_template.id + '">Delete</a>';
                let category;
                switch (email_template.email_category) {
                    case "1":
                        category = 'Subscribe reviewer for the proposal';
                        break;
                    case "2":
                        category = 'Program Chair Template';
                        break;
                    default:
                        category = 'None';
                }
                $('#templates-content').append('<div class="card mt-2">' +
                    '<div class="card-header d-flex justify-content-between">' +
                    '<div><strong>'+(email_template.id)+'. '+email_template.template_name+'</strong></div>' +
                    '<div>'+editBtn+(email_template.is_system !== "1" ? deleteBtn : '')+'</div>' +
                    '</div>' +
                    '<div class="card-body">'+category+'</div>' +
                    '<div class="card-body">'+email_template.email_description+'</div>' +
                    '</div>')
            });

        },'json')

    }

</script>