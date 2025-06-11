<?php echo view('acceptance/common/menu'); ?>
<body>
    <div class="container">
        <?= view('acceptance/common/menu_shortcut'); ?>
        <?=$presentation_data_view ?? ''?>
        <div class="card mt-2">
            <div class="card-header bg-primary text-white p-3">
                Presentation Upload
            </div>
            <div class="card-body">
                <h5>You may upload your PowerPoint here (PPT, PPTX).</h5>
                <p class="text-muted">
                    Be sure your PowerPoint is on the AFS template before uploading. AFS PowerPoint template can be downloaded
                    <a href="<?=base_url().'public/assets/documents/submission_references/CastExpo_2025_PPT_Template.pptx'?>" target="_blank"> here </a>.
                </p>
                <p class="text-muted">
                    The system will automatically add your name and your submission ID to the file name. For example, the naming protocol will be:
                    <code>lastname_2025-0001_filename.pptx.</code>
                </p>

                <div class="alert alert-secondary py-2 uploadedFile" role="alert">
                    <?php if (!empty($acceptanceDetails) && $acceptanceDetails['presentation_saved_name'] !== '') : ?>
                        <div class="uploadedFile pb-2"><strong> Uploaded File:
                                <a href="<?= base_url().$acceptanceDetails['presentation_file_path'].'/'.$acceptanceDetails['presentation_saved_name'] ?>" download="<?=$acceptanceDetails['presentation_saved_name']?>">
                                    <?= !empty($acceptanceDetails) && $acceptanceDetails['presentation_saved_name'] ? $acceptanceDetails['presentation_saved_name'] : ''?>
                                </a><a  class="btn btn-danger btn-sm float-end deleteUploadBtn"> Delete</a>
                            </strong>
                        </div>
                    <?php else: ?>
                        <div class="noUpload"><strong>No upload</strong></div>
                    <?php endif ?>
                </div>

                <form id="uploadForm">
                    <ol class="list-group list-group-numbered">
                        <li class="list-group-item">
                            <strong>Step 1:</strong> Click on <strong>"Choose File"</strong> and navigate to the file you want to upload.
                            <div class="mt-3">
                                <input type="file" name="presentation_file" accept=".ppt,.pptx" class="form-control" id="fileUpload">
                            </div>
                        </li>
                        <li class="list-group-item">
                            <strong>Step 2:</strong> Click on <strong>"Upload File"</strong> to upload the new file to the system server.
                            <div class="mt-3">
                                <button class="btn btn-primary uploadPresentationBtn">Upload File</button>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <strong>Step 3:</strong> Click <strong>"Continue"</strong> to proceed.
                            <div class="mt-3">
                                <button class="btn btn-success continueBtn">Continue</button>
                            </div>
                        </li>
                    </ol>
                </form>
            </div>
        </div>
    </div>
</body>

<script>
    let baseUrlAcceptance = "<?=base_url().'acceptance/'?>";
    $(function(){

        $('.uploadPresentationBtn').on('click', function(e){
            e.preventDefault();
            let formData = new FormData(document.getElementById('uploadForm'));
            formData.append('abstract_id', abstract_id)
            $.ajax({
                url: baseUrlAcceptance+'presentation_do_upload',
                type: 'POST',
                processData: false,
                contentType: false,
                data: formData,
                success: function(response) {
                    console.log(response);
                    if(response.status === 'success') {
                        let uploadedFileName = response.data.presentation_saved_name;
                        let filePath = baseUrlAcceptance + response.data.presentation_file_path +'/'+ uploadedFileName;
                        $('.uploadedFile').html(
                            `<strong>Uploaded File:</strong>
                            <a href="${filePath}" download="${uploadedFileName}">
                                ${uploadedFileName}
                            </a> <a  class="btn btn-danger btn-sm float-end deleteUploadBtn"> Delete</a>`
                        );
                        $('.noUpload').hide();
                        Swal.fire({
                            title: "Uploaded!",
                            text: "Your file has been uploaded.",
                            icon: "success"
                        });
                    } else {
                        alert('Error uploading the file');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', status, error);
                }
            });
        })

        $('.uploadedFile').on('click', '.deleteUploadBtn', function(){
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: baseUrlAcceptance+'presentation_upload_delete',
                        type: 'POST',
                        data: {
                            abstract_id : abstract_id
                        },
                        success: function(response) {
                            Swal.fire({
                                title: "Deleted!",
                                text: "Your file has been deleted.",
                                icon: "success"
                            });
                            $('.uploadedFile').html('<strong>No upload</strong>');
                        },
                        error: function(xhr, status, error) {
                           toastr.error('Something went wrong please use the support button for assistance.')
                        }
                    });


                }
            });
        })

        $('.continueBtn').on('click', function(e) {
            e.preventDefault();
            const uploadedFile = $('.uploadedFile');
            if (uploadedFile.length === 0 || !uploadedFile.has('a').length) {
                swal.fire({
                    title: 'Warning',
                    html: 'Missing Presentation File, Please upload to continue',
                    icon: 'info'
                });
                return false;
            }
            window.location.href = baseUrlAcceptance + "speaker_acceptance_finalize/" + abstract_id;
        });
    })
</script>
