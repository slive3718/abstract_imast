<?php echo view('acceptance/common/menu'); ?>
<body>
    <div class="container">
        <?= view('acceptance/common/menu_shortcut'); ?>
        <?=$presentation_data_view ?? ''?>
        <div class="card mt-2">
            <div class="card-header bg-primary text-white p-3">
                Impact Statement
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <p class="mb-3"><strong>SRS has added a new feature for the discussion section in each Podium Session.</strong> This will include a brief summary of your abstract's conclusion that will be referred to as an <strong>Impact Statement</strong>. These Impact Statements will be displayed on a slide while discussion takes place.</p>
                    <p class="mb-0">If you have any questions, please email the SRS Education team: <a href="mailto:education@srs.org" class="alert-link">education@srs.org</a></p>
                </div>

                <h5 class="mt-4 mb-3">Examples:</h5>

                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Example 1</h6>
                        <ul class="list-unstyled mb-0">
                            <li><strong>Abstract ID:</strong> 659</li>
                            <li><strong>Abstract Title:</strong> No Improvement in Longitudinal Pulmonary Function After Surgery for Early Onset Scoliosis</li>
                            <li class="mt-2"><strong>Impact</strong> - Growth friendly treatment for EOS for average of 3 years' time did not improve pulmonary function, and at best maintained it in all but congenital.</li>
                        </ul>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Example 2</h6>
                        <ul class="list-unstyled mb-0">
                            <li><strong>Abstract ID:</strong> 670</li>
                            <li><strong>Abstract Title:</strong> Pulmonary Function During Growth Friendly Treatment of Early Onset Scoliosis: Importance of Apical Translation</li>
                            <li class="mt-2"><strong>Impact</strong> - Change in thoracic AVT was the strongest radiographic parameter correlating to PFT change in EOS during GF treatment, not Cobb nor T1-12 height.</li>
                        </ul>
                    </div>
                </div>

                <div class="alert alert-warning">
                    <p class="mb-2"><strong>Note:</strong> You do not need to include your Abstract ID number or title in the box below; only include your impact statement.</p>
                </div>

                <form id="uploadForm">
                    <div class="mb-3">
                        <label for="impact_statement" class="form-label">Your Impact Statement:</label>
                        <textarea name="impact_statement" class="form-control" id="impact_statement" rows="10" placeholder="Enter your impact statement here..." required><?=!empty($acceptanceDetails) ? $acceptanceDetails['impact_statement'] : ''?></textarea>
                    </div>

                    <div class="alert alert-secondary py-2 uploadedFile" role="alert">
                        <?php if (!empty($acceptanceDetails) && $acceptanceDetails['impact_statement_saved_name'] !== '') : ?>
                            <div class="uploadedFile pb-2"><strong> Uploaded File:
                                    <a href="<?= base_url().$acceptanceDetails['impact_statement_file_path'].'/'.$acceptanceDetails['impact_statement_saved_name'] ?>" download="<?=$acceptanceDetails['impact_statement_saved_name']?>">
                                        <?= !empty($acceptanceDetails) && $acceptanceDetails['presentation_saved_name'] ? $acceptanceDetails['impact_statement_saved_name'] : ''?>
                                    </a><a  class="btn btn-danger btn-sm float-end deleteUploadBtn"> Delete</a>
                                </strong>
                            </div>
                        <?php else: ?>
                            <div class="noUpload"><strong>No upload</strong></div>
                        <?php endif ?>
                    </div>

                    <ol class="list-group list-group-numbered">
                        <li class="list-group-item">
                            <strong>Step 1:</strong> Click on <strong>"Choose File"</strong> and navigate to the file you want to upload.
                            <div class="mt-3">
                                <input type="file" name="presentation_file" accept=".doc,.docx" class="form-control" id="fileUpload">
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
                                <button class="btn btn-success continueBtn">Save Continue</button>
                            </div>
                            <span class="text-danger"> * </span>
                            <input type="checkbox" name="impact_statement_agreement" id="impact_statement_agreement" <?=!empty($acceptanceDetails['impact_statement_agreement'] ) && $acceptanceDetails['impact_statement_agreement'] == '1' ? 'checked' : ''?>> <label for="impact_statement_agreement"> I understand that I must upload my Impact Statement to the SRS by January 6, 2026. </label>
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
                url: baseUrlAcceptance+'impact_statement_do_upload',
                type: 'POST',
                processData: false,
                contentType: false,
                data: formData,
                success: function(response) {
                    console.log(response);
                    if(response.status === 'success') {
                        let uploadedFileName = response.data.new_name;
                        let filePath = baseUrlAcceptance + response.data.file_path +'/'+ uploadedFileName;
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
                        url: baseUrlAcceptance+'impact_statement_upload_delete/'+abstract_id,
                        type: 'DELETE',
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
            const impact_statement_agreement = $('#impact_statement_agreement').is(':checked') ? 1 : 0;
            const impact_statement = $('#impact_statement').val().trim();
            if (impact_statement_agreement == '0') {
                swal.fire({
                    title: 'Warning',
                    html: 'Please check the manuscript agreement to continue',
                    icon: 'info'
                });
                return false;
            }

            if (impact_statement === '') {
                swal.fire({
                    title: 'Warning',
                    html: 'Impact statement is required.',
                    icon: 'info'
                });
                return false;
            }

            $.ajax({
                url: baseUrlAcceptance+'update_acceptance',
                type: 'POST',
                data: {
                    impact_statement : impact_statement,
                    impact_statement_agreement : impact_statement_agreement,
                    abstract_id : abstract_id
                },
                success: function(response) {
                    window.location.href = baseUrlAcceptance + "speaker_acceptance_finalize/" + abstract_id;
                },
                error: function(xhr, status, error) {
                    toastr.error('Something went wrong please use the support button for assistance.')
                }
            });


        });
    })
</script>
