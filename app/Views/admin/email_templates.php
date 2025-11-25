
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
                        <input type="text" name="template_name" class="form-control" id="templateName" aria-describedby="emailHelp" placeholder="Template Name" required>
<!--                        <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>-->
                    </div>
                    <div class="mb-3">
                        <label for="emailSubject" class="form-label">Email Subject</label>
                        <input type="text" name="email_subject" class="form-control" id="emailSubject" placeholder="Subject" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-check-label" for="emailDescription">Description</label>
                        <input type="text" name="email_description" class="form-control" id="emailDescription" placeholder="Description" required>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="email_category" id="emailCategory1" value="1" placeholder="Category" required >
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

                    <?= view('admin/renders/email_templates_field_merge'); ?>
                    
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

        $('.saveTemplateBtn').on('click', async function(e) {
            e.preventDefault();
            const $form = $('#formTemplate');
            const formData = new FormData($form[0]);
            const message = newEditor.getData();
            const template_id = $(this).attr("template_id");

            // Flag to track validation errors
            let hasErrors = false;

            // Validate required non-radio inputs
            $('input[required]').not('[type="radio"]').each(function() {
                if ($(this).val().trim() === '') {
                    const placeholder = $(this).attr('placeholder') || 'This field is required';
                    toastr.error(placeholder);
                    hasErrors = true;
                    return false; // Stop after first error
                }
            });

            // Validate required radio buttons
            $('input[type="radio"][required]').each(function() {
                const name = $(this).attr('name');
                if ($(`input[name="${name}"]:checked`).length === 0) {
                    toastr.error('Please select an option');
                    hasErrors = true;
                    return false; // Stop after first error
                }
            });

            // Stop submission if validation fails
            if (hasErrors) {
                return;
            }

            // Append additional data
            formData.append('template_id', template_id);
            formData.append('message', message);

            try {
                const response = await $.ajax({
                    url: base_url_admin + 'save_email_template',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                });

                // Success handling
                toastr.success('Template saved successfully!');
                getTemplates(); // Refresh templates list
                $('#templateModal').modal('hide');
                $form[0].reset(); // Reset form

                console.log(response);
            } catch (error) {
                // Error handling
                toastr.error('Failed to save template. Please try again.');
                console.error(error);
            }
        });

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
            const template_id = $(this).attr("template_id");

            $.ajax({
                url: base_url_admin + 'email_templates/' + template_id,
                type: 'delete',
                success: function(response) {
                    toastr.success('Template deleted successfully!');
                    getTemplates(); // Refresh templates list
                },
                error: function(xhr, status, error) {
                    toastr.error('Failed to delete template. Please try again.');
                    console.error(error);
                }
            });
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