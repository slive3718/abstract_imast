

<?php echo view('deputy/common/menu'); ?>
<?php //print_r($paper->declined); exit ?>
<main>
    <div class="container-fluid" style="padding-bottom:200px">
        <?php echo view('admin/common/shortcut_link'); ?>
        <div class="card">
            <div class="card-header"> Paper Details</div>
            <div class="card-body">
                <a class="btn btn-success" id="abstractDetails" >Paper Details <i class="fas fa-magnifying-glass"></i></a>
            </div>
        </div>
        <div class="card">
            <div class="card-header"> Uploaded File(s)</div>
            <div class="card-body">
                <?php if(!empty($paper->uploads)):
                    foreach ($paper->uploads as $uploads):
                ?>
                    <a href="<?=base_url($uploads['file_path'].$uploads['file_name'])?>" download="<?=($uploads['file_preview_name'])?>"><?=$uploads['file_preview_name']?></a><br>
                <?php endforeach; endif ?>
            </div>
        </div>
        <div class="card">
            <div class="card-header"> Review Information</div>
            <div class="card-body" style="overflow-x:auto">
                <table id="abstractTable" class="table table-bordered" style="border:2px solid black;">
                    <thead>
                    <tr>
                        <th style="width:250px" id="abstractTableID"><?=$paper->custom_id?></th>
                        <th colspan="13">Ratings and Comments</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>Free from commercialism</th>
                        <th>Can be edited?</th>
                        <th>Operations</th>
                        <th>Management/Marketing</th>
                        <th>Technology/Research</th>
                        <th>Professional Level</th>
                        <th>1. Originality</th>
                        <th>2. Sufficiency of Data/Information</th>
                        <th>3. Readability</th>
                        <th>4. ArtWork</th>
                        <th>5. Composite Score</th>
                        <th>Average Score(1-4)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($paper->reviews)):
                        foreach ($paper->reviews as $review):?>
                            <tr class="bg-primary bg-opacity-10">
                                <th><?=UcFirst($review['user_name']). ' '.UcFirst($review['user_surname'])?></th>
                                <td><?=$review['commercialism']?></td>
                                <td><?=$review['commercialism_editable']?></td>
                                <td><?=$review['operations']?></td>
                                <td><?=$review['marketing_score']?></td>
                                <td><?=$review['research_score']?></td>
                                <td><?=$review['professional_level']?></td>
                                <td><?=$review['originality_score']?></td>
                                <td><?=$review['sufficiency_score']?></td>
                                <td><?=$review['readability_score']?></td>
                                <td><?=$review['artwork_score']?></td>
                                <td class="bg-primary text-white"><?=$review['composite_score']?></td>
                                <td class="bg-danger text-white"><?=$review['average_score']?></td>
                            </tr>
                            <tr>
                                <td>Suggested Revision Comments:</td>
                                <td colspan="9"><textarea class="w-100 border-0" readonly id="suggestedRevisionComment_<?=$review['id']?>"><?=$review['suggested_revision_comment']?></textarea></td>
                                <td colspan="1">
                                    <a href="#" class="editSuggestedBtn editBtn" review_id = "<?=$review['id']?>">Edit</a> <br>
                                    <a href="#" class="saveSuggestedBtn saveBtn" review_id = "<?=$review['id']?>">Save</a>
                                </td>

                                <td colspan="2"><input type="checkbox" <?=(isset($review['display_suggestion']) && $review['display_suggestion'] == 1)?'checked':''?> class="displayCheckbox displaySuggested" name="displayToSubmitter" review_id = "<?=$review['id']?>" paper_id="<?=$review['abstract_id']?>" id="displaySuggested_<?=$review['id']?>" submitter_id="<?=$paper->user_id?>"> <label for="displaySuggested_<?=$review['id']?>"> Display to Submitter</label></td>
                            </tr>
                            <tr>
                                <td>Required Revision Comments:</td>
                                <td colspan="9"><textarea class="w-100 border-0" readonly id="requiredRevisionComment_<?=$review['id']?>"><?=$review['required_revision_comment']?></textarea></td>
                                <td colspan="1">
                                    <a href="#" class="editRequiredBtn editBtn" review_id = "<?=$review['id']?>">Edit</a> <br>
                                    <a href="#" class="saveRequiredBtn saveBtn" review_id = "<?=$review['id']?>">Save</a>
                                </td>
                                <td colspan="2"><input type="checkbox" <?=(isset($review['display_required']) && $review['display_required'] == 1)?'checked':''?> class="displayCheckbox displayRequired" name="displayToSubmitter" review_id = "<?=$review['id']?>" paper_id="<?=$review['abstract_id']?>" id="displayRequired_<?=$review['id']?>" submitter_id="<?=$paper->user_id?>"> <label for="displayRequired_<?=$review['id']?>"> Display to Submitter</label></td>
                            </tr>
                            <tr class="<?=!empty($review['submitter_comment_on_upload']) ? '':'d-none' ?>">
                                <td>Submitter Comment :</td>
                                <td colspan="16">
                                    <textarea class="w-100 border-0" ><?=$review['submitter_comment_on_upload']?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>Uploaded Files:</td>
                                <td colspan="12">
                                    <?php if(isset($review['paper_uploads'])):
                                        foreach ($review['paper_uploads'] as $uploads): ?>
                                                <a href="<?=base_url($uploads['file_path'].$uploads['file_name'])?>"><?=$uploads['file_preview_name']?></a><br>
                                            <?php endforeach;
                                            endif
                                        ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Submitter Deadline:</td>
                                <td colspan="12">
                                    <div class="input-group" style="width: 700px;">
                                        <?php // Convert date format from MM/DD/YYYY to YYYY-MM-DD
                                        $submitter_deadline = '';
                                        if (!empty($review['submitter_deadline'])) {
                                            $submitter_deadline = date("Y-m-d", strtotime($review['submitter_deadline']));
                                        }
                                        ?>
                                        <input type="date" class="form-control" id="submitterDeadline_<?=$review['id']?>" value="<?=$submitter_deadline?>">
                                        <div class="input-group-append">
                                            <button class="btn btn-info saveDeadlineBtn px-4 text-white border-dark" review_id="<?=$review['id']?>">Save Deadline</button>
                                        </div>

                                        <span class="float-end"><a class="ms-5 btn btn-success btn-sm emailToSubmitterBtn" href="" paper_id="<?=$review['abstract_id']?>" submitter_id="<?=$paper->user_id?>" review_id="<?=$review['id']?>" >Email to Submitter Now</a></span>
                                    </div>
                                    <small class="text-muted"><i>This date will be available to the submitter.</i></small>

                                </td>
                            </tr>
                        <tr><td colspan="13" class="bg-secondary bg-opacity-50"></td></tr>
                        <?php endforeach; endif ?>


<!--                    LIST DECLINED PAPERS FROM REVIEWERS-->
                    <?php if($paper->declined):
                        foreach ($paper->declined as $declined):?>
                            <tr class="bg-danger">
                                <td colspan="1"> <?= ucFirst($declined['name']).' '. ucFirst($declined['surname'])?>  </td>
                                <td colspan="15"> DECLINED REVIEW  </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif ?>

                    </tbody>
                </table>

            </div>
        </div>
        <div class="card">
            <div class="card-header"> Chair Options </div>
            <div class="card-body">
                <form id="formChairAcceptance">
                    <div>
                        <div class="form-group">
                            <label for="comments_program"><?=ucFirst(session('name')).' '.ucFirst(session('surname'))?>, Aluminum Program Chair Comments:</label>
                            <textarea id="comments_program" name="comments_program" rows="7" class="form-control" placeholder="Type here..."><?=(isset($paper->acceptance) && !empty($paper->acceptance['comments'])? $paper->acceptance['comments'] :'')?></textarea>
                        </div>

                        <div id="acceptDiv" class="form-group mt-3">
                            <label><span class="text-danger">*</span><b>Accept/Reject:</b></label>
                            <div>
                                <input type="checkbox" id="accept" name="acceptance[]" value="1" class="form-checkbox"
                                    <?= (isset($paper->acceptance) && $paper->acceptance['acceptance_status'] && in_array(1, json_decode($paper->acceptance['acceptance_status']))) ? 'checked' : '' ?>>
                                <label for="accept">Approved for Proceedings</label><br>

                                <input type="checkbox" id="reject" name="acceptance[]" value="2" class="form-checkbox"
                                    <?= (isset($paper->acceptance) && $paper->acceptance['acceptance_status'] && in_array(2, json_decode($paper->acceptance['acceptance_status']))) ? 'checked' : '' ?>>
                                <label for="reject">Approved for Transactions</label><br>

                                <input type="checkbox" id="suggested" name="acceptance[]" value="3" class="form-checkbox"
                                    <?= (isset($paper->acceptance) && $paper->acceptance['acceptance_status'] && in_array(3, json_decode($paper->acceptance['acceptance_status']))) ? 'checked' : '' ?>>
                                <label for="suggested">Approved for inclusion in the Division’s Program</label><br>

                                <input type="checkbox" id="required" name="acceptance[]" value="4" class="form-checkbox"
                                    <?= (isset($paper->acceptance) && $paper->acceptance['acceptance_status'] && in_array(4, json_decode($paper->acceptance['acceptance_status']))) ? 'checked' : '' ?>>
                                <label for="required">Rejected</label><br>
                            </div>

                        </div>

                        <div class="form-group mt-3">
                            <label for="st_extra1"><span class="text-danger">*</span>Recommend for Publication in Transactions:</label>
                            <select id="st_extra1" name="recommendedPublication" class="form-control">
                                <option value=""> -- Select --</option>
                                <option value="Yes" <?=(isset($paper->acceptance) && $paper->acceptance['is_recommended_for_publications'] == "Yes")? 'selected':''?>>Yes</option>
                                <option value="No" <?=(isset($paper->acceptance) && $paper->acceptance['is_recommended_for_publications'] == "No")? 'checked':''?>>No</option>
                            </select>
                        </div>

                        <div id="buttonDiv" class="form-group mt-5">
                            <button id="acceptanceBtn" type="button" class="btn btn-success">Save and return to main menu</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</main>
<?=view('deputy/common/abstractDetailsModal')?>
<script>
    let baseUrlDeputy = "<?=base_url().'deputy/'?>";
    $(function(){

        $('input[type="checkbox"]').change(function() {
            if ($(this).is(':checked')) {
                // If the "Rejected" checkbox is checked
                if ($(this).val() == '4') {
                    // Uncheck all other checkboxes
                    $('input[type="checkbox"]').not(this).prop('checked', false);
                } else {
                    // If any other checkbox is checked, uncheck the "Rejected" checkbox
                    $('#required').prop('checked', false);
                }
                console.log("Checked value: " + $(this).val());
            } else {
                console.log("Unchecked value: " + $(this).val());
            }
        });

        $('#acceptanceBtn').on('click', function(){
            let formData = new FormData(document.getElementById('formChairAcceptance'));
            let paperID = "<?=$paper->id?>"
            formData.append('paper_id', paperID)
            $.ajax({
                url: baseUrlDeputy + 'acceptance',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    response = JSON.parse(response)
                    console.log(response)
                    if(response.status == "200") {
                        Swal.fire({
                            icon: "success",
                            title: "Your review has been saved",
                            showConfirmButton: true,
                            timer: 1500
                        });
                    }
                    else {
                        Swal.fire({
                            icon: "error",
                            title: "Please contact administrator!",
                            showConfirmButton: true,
                            timer: 1500
                        });
                    }
                }
            });
        });

        $('#abstractDetails').on('click', function(){
            let  paper_id = $(this).attr('paper_id');
            let paperID = "<?=$paper->id?>"
            $.ajax({
                url: baseUrlDeputy+'allPaperDetails/'+paperID,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.status == 200 && response.data) {
                        var paper = response.data;
                        var uploads = paper.uploads;
                        var authors = paper.authors;

                        // Populate General Information
                        $('.general-info #paper-id').text(paper.id);
                        $('.general-info #paper-title').html(stripTags(paper.title));

                        // Populate Uploaded Files
                        var filesHtml = '';
                        if(uploads.length > 0) {
                            uploads.forEach(function(upload) {
                                filesHtml += '<a href="' + upload.file_path + upload.file_name + '">' + upload.file_preview_name + '</a><br>';
                            });
                        } else {
                            filesHtml = 'No files uploaded';
                        }
                        $('#uploaded-files').html(filesHtml);

                        // Populate Author Information
                        var authorsHtml = '';
                        if (authors.length > 0) {
                            authorsHtml += '<tr><td>Author List: </td>';
                            authorsHtml += '<td>';
                            authors.forEach(function(author) {
                                var capitalizedFirstName = author.name.charAt(0).toUpperCase() + author.name.slice(1);
                                var capitalizedLastName = author.surname.charAt(0).toUpperCase() + author.surname.slice(1);
                                if (author.is_presenting_author == "Yes") {
                                    authorsHtml += 'Presenting Author: ' + '<span style="font-weight: bold;">' + capitalizedFirstName + ' ' + capitalizedLastName + '</span><br>';
                                } else {
                                    authorsHtml += 'Co-Author: ' + '<span style="font-weight: bold;">' + capitalizedFirstName + ' ' + capitalizedLastName + '</span><br>';
                                }
                            });
                            authorsHtml += '</td></tr>';

                            authors.forEach(function(author) {
                                var capitalizedFirstName = author.name.charAt(0).toUpperCase() + author.name.slice(1);
                                var capitalizedLastName = author.surname.charAt(0).toUpperCase() + author.surname.slice(1);
                                if (author.is_presenting_author == "Yes") {
                                    authorsHtml += '<tr><td style="width:200px">Presenting Author : </td>';
                                    authorsHtml += '<td>';
                                    authorsHtml += '<strong>' + capitalizedFirstName + ' ' + capitalizedLastName + '</strong><br>';
                                    authorsHtml += 'Address: ' + author.address + '<br>' + author.country + '<br>Email: ' + author.email + '<br> Institution: ' + author.institution + '<br> Work Phone: ' + author.phone + '<br> Fax: ' + author.fax;
                                    authorsHtml += '</td></tr>';
                                }
                            });

                            authors.forEach(function(author) {
                                var capitalizedFirstName = author.name.charAt(0).toUpperCase() + author.name.slice(1);
                                var capitalizedLastName = author.surname.charAt(0).toUpperCase() + author.surname.slice(1);
                                if (author.is_presenting_author !== "Yes") {
                                    authorsHtml += '<tr><td style="width:200px">Co-Author : </td>';
                                    authorsHtml += '<td>';
                                    authorsHtml += '<strong>' + capitalizedFirstName + ' ' + capitalizedLastName + '</strong><br>';
                                    authorsHtml += 'Address: ' + author.address + '<br>' + author.country + '<br>Email: ' + author.email + '<br> Institution: ' + author.institution + '<br> Work Phone: ' + author.phone + '<br> Fax: ' + author.fax;
                                    authorsHtml += '</td></tr>';
                                }
                            });
                        } else {
                            authorsHtml = '<tr><td colspan="2">No authors available</td></tr>';
                        }




                        $('.author-info tbody').html(authorsHtml);

                        // Populate Paper Information
                        $('.paper-info #division').text(paper.division_name);
                        $('.paper-info #paper-type').text(paper.paper_type_name);
                        $('.paper-info #paper-summary').text(stripTags(paper.summary));
                        $('.paper-info #ijmc-interest').text((paper.is_ijmc_interested == "1")?"Yes": "No");
                        // $('#displaySuggested_'+paper)

                        // Populate User Information
                        $('.user-info #user-name').text(paper.submitter.name + ' ' + paper.submitter.surname);
                        $('.user-info #user-email').text(paper.submitter.email);
                    } else {
                        console.error('Error fetching data: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data: ' + error);
                }
            });

            $('#abstractDetailsModal').modal('show')
        })


        $('#abstractTable').on('click', '.editBtn', function(e){
            e.preventDefault();
            let review_id = $(this).attr('review_id');
            let textarea;
            if ($(this).hasClass('editSuggestedBtn')) {
                textarea = $('#suggestedRevisionComment_' + review_id);
            } else if ($(this).hasClass('editRequiredBtn')) {
                textarea = $('#requiredRevisionComment_' + review_id);
            }
            editComment(review_id, textarea);
        });


        $('#abstractTable').on('click', '.saveBtn', function(e){
            e.preventDefault();
            let review_id = $(this).attr('review_id');
            let textarea ;
            let type ;
            if ($(this).hasClass('saveSuggestedBtn')) {
                textarea = $('#suggestedRevisionComment_' + review_id);
                type = 'suggestedComment'
            } else if ($(this).hasClass('saveRequiredBtn')) {
                textarea = $('#requiredRevisionComment_' + review_id);
                type = 'requiredComment'
            }
           saveComment(review_id,textarea, type);
        });

        $('#abstractTable').on('click', '.displayCheckbox', function(e){ // Added parameter e for event
            e.preventDefault();

            let status = $(this).prop('checked'); // Getting the status of the checkbox
            let review_id = $(this).attr('review_id');
            let type; // Declaring type variable
            let paper_id = $(this).attr('paper_id');
            let submitter_id = $(this).attr('submitter_id');

            if ($(this).hasClass('displaySuggested')) {
                type = 'suggestedComment';
            } else if ($(this).hasClass('displayRequired')) {
                type = 'requiredComment';
            }

            let checkBox = $(this)
            if(status == true) {
                Swal.fire({
                    title: "Display to Submitter?",
                    text: "This will also send an automatic email notification to the submitter that the review is done and comment is available to their submission!",
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, show and notify submitter!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: "Please Wait!",
                            html: "Sending Email...",
                            timerProgressBar: true,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                        })
                        display(submitter_id, review_id, type, status, paper_id, function(response){
                            if (response.status == 200) {
                                checkBox.prop('checked', true); // Ensure the checkbox stays checked
                                    Swal.fire({
                                        title: "success!",
                                        text: "Your comment will be displayed the the submitter and a notification is sent to their email.",
                                        icon: "success"
                                    });
                            } else {
                                checkBox.prop('checked', false); // Uncheck if the server response is not successful
                                Swal.fire('Error', response.message, 'error');
                            }
                        })
                    }
                });
            }else{
                display(submitter_id, review_id, type, status, paper_id, function(response){
                    if (response.status == 200) {
                        checkBox.prop('checked', false); // Ensure the checkbox stays checked
                        Swal.fire('Success', response.message, 'success');
                    } else {
                        checkBox.prop('checked', true); // Uncheck if the server response is not successful
                        Swal.fire('Error', response.message, 'error');
                    }
                });
            }

        });

        // if(response.status == "200"){
        //     Swal.fire({
        //         title: "success!",
        //         text: "Your comment will be displayed the the submitter and a notification is sent to their email.",
        //         icon: "success"
        //     });
        // }else{
        //     Swal.fire({
        //         title: "info!",
        //         text: "Your comment will be hidden to the submitter.",
        //         icon: "success"
        //     });
        // }

        $('#abstractTable').on('click', '.saveDeadlineBtn', function() {
            let review_id = $(this).attr('review_id');
            let date = $('#submitterDeadline_' + review_id).val();

            $.post(baseUrlDeputy+'saveDeadline', {
                "date":date,
                "review_id":review_id
            }, function(response){
                if(response.status == "200"){
                    toastr.success(response.message)
                }else{
                    toastr.error(response.message)
                }
            },'json')
        });


        $('#abstractTable').on('click', '.emailToSubmitterBtn', function(e) {
            e.preventDefault();

            let paper_id = $(this).attr('paper_id');
            let submitter_id = $(this).attr('submitter_id');
            let review_id = $(this).attr('review_id');
            let date = $("#submitterDeadline_" + review_id).val();

            if(!date) {
                toastr.error('Submitter Deadline is required.');
                return false;
            }

            Swal.fire({
                title: "Send Email?",
                text: "This will send an automatic email notification to the submitter that the deadline is posted!",
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, email submitter!"
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Please Wait!",
                        html: "Sending email to submitter...",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.post(baseUrlDeputy + 'emailDeadlineToSubmitter', {
                        "date": date,
                        "paper_id": paper_id,
                        "submitter_id": submitter_id,
                        "review_id": review_id
                    }, function(response) {
                        Swal.close(); // Close the loading spinner

                        if (response.status == "200") {
                            Swal.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success"
                            });
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: response.message,
                                icon: "error"
                            });
                        }
                    }, 'json');
                }
            });
        });


    })

    function editComment(review_id, textarea){
        // Set readonly attribute to false
        textarea.prop('readonly', false);

        textarea.focus();
        let contentLength = textarea.val().length;
        textarea[0].setSelectionRange(contentLength, contentLength);
    }

    function saveComment(review_id, textarea, type){
        // console.log(textarea.val()
        let comment = textarea.val();
        $.post(baseUrlDeputy+'updateReviewerComments', {
            "comment":comment,
            "type":type,
            "review_id":review_id
        }, function(response){
           if(response.status == "200"){
               toastr.success(response.message)
           }else{
               toastr.error(response.message)
           }
        },'json')

        textarea.prop('readonly', true);
    }

    function display(submitter_id, review_id, type, status, paper_id, callback){
            $.post(baseUrlDeputy+'displayReview', {
                "review_id":review_id,
                "submitter_id":submitter_id,
                "type":type,
                "status": ((status == true)?1:0),
                "paper_id":paper_id
            }, function(response){
                callback(response)
            },'json')

    }

    function stripTags(html) {
        // Create a new div element
        var div = document.createElement("div");
        // Set the HTML content of the div to the provided HTML string
        div.innerHTML = html;
        // Return the text content of the div, which will not include any HTML tags
        return div.textContent || div.innerText || "";
    }

</script>