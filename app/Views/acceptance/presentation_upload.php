<?php echo view('acceptance/common/menu'); ?>
<body>
    <div class="container">
        <?= view('acceptance/common/menu_shortcut'); ?>
        <?=$presentation_data_view ?? ''?>
        <div class="card mt-2">
            <div class="card-header bg-primary text-white p-3">
                Manuscript
            </div>
            <div class="card-body">

                <div>
                    <h5>Manuscript</h5>
                    <p>The manuscript should be an accurate reflection of the material you will present at the Annual. Although some additional material can be included, it is best to stay close to the information you will give from the podium.</p>

                    <h6>Length And Format</h6>
                    <p>The manuscript must follow the format of the SRS journal Spine Deformity, the full author guidelines can be accessed at <a href="https://link.springer.com/journal/43390/submission-guidelines?IFA" target="_blank">https://link.springer.com/journal/43390/submission-guidelines?IFA</a> at the guide for authors tab. A brief summary is below.</p>
                    <p>The manuscript should be double-spaced, with generous margins (1 to 1 ½ inches) to allow room for notes by the reviewers. Length can vary, but should probably be 10-15 double-spaced pages (not including illustrations or references).</p>
                    <p>Paragraphs may either be flush left, with an extra return before them, or may be indented with a tab. Main headings should be centered, main subheadings should be typed in bold at the left margin.</p>
                    <p>Tables, charts, and black and white illustrations may be included in the body of the text or submitted on separate pages. Each table or illustration should include a legend (such as Figure 1) to connect the illustration to the text. Tables should have clearly marked headings, and abbreviations should be spelled out and explained at the end of the table. Any arrows, letters, or other indicators that appear in the artwork should be clearly explained in the legend.</p>
                    <p>If abbreviated terms will be used in the body of the abstract, these should be spelled out at their first use. For example, “AIS” should first be shown as “Adolescent Idiopathic Scoliosis (AIS)”.</p>

                    <h6>Title Page</h6>
                    <p>The title page should include the following information:</p>
                    <ul>
                        <li>Title of the paper</li>
                        <li>Each author’s name as it appears in the abstract which will be printed in the Final Program</li>
                        <li>Institution name or practice setting of the primary author</li>
                        <li>Mailing address, telephone number, fax number and email address of the primary author</li>
                    </ul>

                    <h6>References</h6>
                    <p>References are not required, but may be included. If used, they should be numbered in the order in which they are mentioned within the text. The reference list should be typed or printed double-spaced and should follow directly after the text. A good format for references is:</p>
                    <ol>
                        <li>Jones J: Growth plate abnormalities. J Bone Joint Surg 1982;64A:691-703.</li>
                        <li>Marks W: Fractures of the lower extremities, in Smith P (ed): Fundamentals of Orthopaedics, ed 3. New York, NY, Academic Press, 1986, pp 197-228.</li>
                    </ol>

                    <h6>Upload</h6>
                    <p>The system will only allow MS Word (.docx or .doc) files and will automatically add the assigned ID and the presenter last name. For example, a file name such as ‘file.docx’ will be uploaded as ‘101_Smith_file.docx’.</p>
                </div>

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
                                <input type="checkbox" class="mb-4" name="manuscript_agreement" id="manuscript_agreement" <?=!empty($acceptanceDetails['manuscript_agreement'] ) && $acceptanceDetails['manuscript_agreement'] == '1' ? 'checked' : ''?>> <label for="manuscript_agreement"> I understand that I must submit my manuscript to the SRS by March 18, 2026. </label>
                                <br>
                                <button class="btn btn-success continueBtn">Save Continue</button>
                            </div>
                            <span class="text-danger"> * </span>

                        </li>
<!--                        <li class="list-group-item py-3">-->
<!--                            </li>-->
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
                        url: baseUrlAcceptance+'presentation_upload_delete/'+abstract_id,
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
            const manuscript_agreement = $('#manuscript_agreement').is(':checked') ? 1 : 0;
            const uploadedFile = $('.uploadedFile');

            console.log(manuscript_agreement)
            if (manuscript_agreement == 0) {
                swal.fire({
                    title: 'Warning',
                    html: 'Please check the manuscript agreement to continue',
                    icon: 'info'
                });
                return false;
            }

            $.ajax({
                url: baseUrlAcceptance+'update_acceptance',
                type: 'POST',
                data: {
                    manuscript_agreement : manuscript_agreement,
                    abstract_id : abstract_id

                },
                success: function(response) {
                    window.location.href = baseUrlAcceptance + "impact_statement/" + abstract_id;
                },
                error: function(xhr, status, error) {
                    toastr.error('Something went wrong please use the support button for assistance.')
                }
            });


        });
    })
</script>
