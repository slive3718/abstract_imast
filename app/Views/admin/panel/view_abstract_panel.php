
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
</style>
<main style="padding-bottom:100px">
    <div class="container pb-5">
        <div class="card shadow">
            <?php echo view('admin/common/shortcut_link_detail_back'); ?>
            <div class="card-header fw-bold"> General Information  <a href="<?=base_url()?>/admin/edit_panel_submission/<?=$paper_id?>" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a></div>
            <div class="card-body">
                <table class="table">
                    <tbody>
                    <tr>
                        <td class="text-end fw-bolder">Paper ID : </td>
                        <td ><?=$papers->custom_id?></td>
                    </tr>
                    <tr>
                        <td style="width:250px" class="text-end fw-bolder">Paper Title : </td>
                        <td><?=$papers->title?></td>
                    </tr>
                    <?php if($papers):
                        ?>
                        <tr>
                            <td class="text-end fw-bolder">Division : </td>
                            <td><?=$papers->division_name?></td>
                        </tr>
                        <tr>
                            <td class="text-end fw-bolder">Submission Type : </td>
                            <td><?=$papers->submission_type?></td>
                        </tr>
                        <tr>
                            <td class="text-end fw-bolder">Title : </td>
                            <td><?=$papers->title?></td>
                        </tr>
                        <tr>
                            <td class="text-end fw-bolder">Brief Summary : </td>
                            <td><?=$papers->summary?></td>
                        </tr>
                        <tr>
                            <td class="text-end fw-bolder">Are you interested in submitting this paper to IJMC as well ? </td>
                            <td><?=$papers->is_ijmc_interested == '0' ? 'I am NOT interested in submitting this paper to IJMC' ? $papers->is_ijmc_interested == '1':'I am interested in submitting this paper to IJMC':'I have already submitted this paper to IJMC' ?></td>
                        </tr>
                    <?php endif;?>
                    </tbody>
                </table>
            </div>
        </div>


            <!-- Coordinator(s) Information Section -->
            <div class="card shadow">
                <div class="card-header fw-bold" id="coordinators-info-header" data-bs-toggle="collapse" data-bs-target="#coordinators-info-body" aria-expanded="true" aria-controls="author-info-body">
                    Coordinators Information <?=(!isset($authorInfo)? '<span class="text-danger text-sm badge " title="Please complete requirements before finalizing abstract."><i class="fas fa-exclamation-circle" > INCOMPLETE </i></span>' :'')?>
                </div>
                <div id="coordinators-info-body" class="collapse show" aria-labelledby="coordinators-info-header" >
                    <div class="card-body">
                        <a href="<?=base_url()?>/admin/panel_coordinators/<?=$paper_id?>" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a>
                        <table class="table" style="margin-bottom:0px !important">
                            <tbody>

                            <?php if($coordinators):
                                foreach ($coordinators as $index=>$author):
                                    ?>
                                    <tr >
                                        <td class="text-end">Coordinator </td>
                                        <td><strong><?= UcFirst($author['name']) . ' view_abstract_panel.php' .UcFirst($author['surname']) ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">Coordinator Info: </td>
                                        <td >
                                            Email: <?=$author['email']?><br>
                                            Institution: <?=$author['institution']?><br>
                                            Work Phone: <?=$author['phone']?><br>
                                            <!--                                Fax: --><?php //=$author['fax']?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><br></td>
                                    </tr>

                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <!-- Author Information Section -->
            <div class="card shadow">
                <div class="card-header fw-bold" id="panelist-info-header" data-bs-toggle="collapse" data-bs-target="#panelist-info-body" aria-expanded="true" aria-controls="author-info-body">
                    Panelist Information <?=(!isset($authorInfo)? '<span class="text-danger text-sm badge " title="Please complete requirements before finalizing abstract."><i class="fas fa-exclamation-circle" > INCOMPLETE </i></span>' :'')?>
                </div>
                <div id="panelist-info-body" class="collapse show" aria-labelledby="panelist-info-header" >
                    <div class="card-body">
                        <a href="<?=base_url()?>/admin/panelist/<?=$paper_id?>" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a>
                        <table class="table" style="margin-bottom:0px !important">
                            <tbody>
                            <tr>
                                <td class="text-end" style="width:250px">
                                     Panelist :
                                </td>
                                <td>
                                    <?php if($authorInfo):
                                        foreach ($authorInfo as $author):?>

                                            <?=$author['name'].' '.$author['surname'] ?><br>
                                        <?php endforeach; endif; ?>
                                </td>
                            </tr>
                            <?php if($authorInfo):
                                foreach ($authorInfo as $index=>$author):
                                    ?>
                                    <tr >
                                        <td class="text-end">Panelist </td>
                                        <td><strong><?= UcFirst($author['name']) . ' view_abstract_panel.php' .UcFirst($author['surname']) ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">Panelist Info: </td>
                                        <td >
                                            Address: <?=$author['address']?>
                                            <?=$author['city'], $author['province'], $author['zipcode'], $author['country'] ?><br>
                                            Professional Degree(s): <?=$author['deg']?><br>
                                            Email: <?=$author['email']?><br>
                                            Institution: <?=$author['institution']?><br>
                                            Work Phone: <?=$author['phone']?><br>
                                            <!--                                Fax: --><?php //=$author['fax']?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">Correspondence :</td>
                                        <td><?=($author['is_correspondent'])?'Yes':'No'?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><br></td>
                                    </tr>

                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>

                        <div class="tracksDiv">
                            <form name="formTracks">
                                <table class="table" style="border-bottom-width:4px !important">
                                    <tbody>
                                    <tr>
                                        <td class="text-end">Tracks : </td>
                                        <td>
                                            <?php $tracks = json_decode($papers->tracks); ?>
                                            <input type="checkbox" name="tracks[]" id="track1" value="1" <?= !empty($tracks) && (in_array('1', $tracks)) ? 'checked' : '' ?>> <label for="track1" > Casting Designers & Buyers</label> <br>
                                            <input type="checkbox" name="tracks[]" id="track2" value="2" <?= !empty($tracks) && (in_array('2', $tracks)) ? 'checked' : '' ?>> <label for="track2" > Management</label> <br>
                                            <input type="checkbox" name="tracks[]" id="track3" value="3" <?= !empty($tracks) && (in_array('3', $tracks)) ? 'checked' : '' ?>> <label for="track3" > Student</label> <br>
                                            <input type="checkbox" name="tracks[]" id="track4" value="4" <?= !empty($tracks) && (in_array('4', $tracks)) ? 'checked' : '' ?>> <label for="track4" > Technical</label> <Br>

                                            <a class="btn btn-primary mt-2 btn-sm saveTrackBtn" paper_id = "<?=$paper_id?>">Save</a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

        <div class="card">
            <div class="card-header" id="review-info-header" data-bs-toggle="collapse" data-bs-target="#review-info-body" aria-expanded="true" aria-controls="review-info-body">
                <div class="card-title fw-bold">Review Information</div>
            </div>
            <div class="card-body" style="overflow-x:auto">
                <div id="review-info-body" class="collapse show" aria-labelledby="review-info-header" data-bs-parent=".card">
                    <table id="abstractTable" class="table table-bordered" style="border:2px solid black;">
                        <thead>
                        <tr>
                            <th style="width:250px">ID</th>
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
                        <?php if(!empty($review_details)):
                            foreach ($review_details->reviews as $review):?>
                                <tr class="bg-primary bg-opacity-10">
                                    <th><?= UcFirst($review['user_name']) . ' view_abstract_panel.php' .UcFirst($review['user_surname'])?></th>
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

                                    <td colspan="2"><input type="checkbox" <?=(isset($review['display_suggestion']) && $review['display_suggestion'] == 1)?'checked':''?> class="displayCheckbox displaySuggested" name="displayToSubmitter" review_id = "<?=$review['id']?>" id="displaySuggested_<?=$review['id']?>"> <label for="displaySuggested_<?=$review['id']?>"> Display to Submitter</label></td>
                                </tr>
                                <tr>
                                    <td>Required Revision Comments:</td>
                                    <td colspan="9"><textarea class="w-100 border-0" readonly id="requiredRevisionComment_<?=$review['id']?>"><?=$review['required_revision_comment']?></textarea></td>
                                    <td colspan="1">
                                        <a href="#" class="editRequiredBtn editBtn" review_id = "<?=$review['id']?>">Edit</a> <br>
                                        <a href="#" class="saveRequiredBtn saveBtn" review_id = "<?=$review['id']?>">Save</a>
                                    </td>
                                    <td colspan="2"><input type="checkbox" <?=(isset($review['display_required']) && $review['display_required'] == 1)?'checked':''?> class="displayCheckbox displayRequired" name="displayToSubmitter" review_id = "<?=$review['id']?>" id="displayRequired_<?=$review['id']?>"> <label for="displayRequired_<?=$review['id']?>"> Display to Submitter</label></td>
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

                                            <span class="float-end emailToSubmitterBtn"><a class="ms-5 btn btn-success btn-sm" href=""  review_id="<?=$review['id']?>" >Email to Submitter Now</a></span>
                                        </div>
                                        <small class="text-muted"><i>This date will be available to the submitter.</i></small>

                                    </td>
                                </tr>
                                <tr><td colspan="13" class="bg-secondary bg-opacity-50"></td></tr>
                            <?php endforeach; endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
<!--        <div class="card">-->
<!--            <div class="card-header" id="program-chair-info-header"  data-bs-toggle="collapse" data-bs-target="#program-chair-info-body" aria-expanded="true" aria-controls="program-chair-info-body">-->
<!--                <div class="card-title fw-bolder">-->
<!--                    Program Chair Information-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="card-body" style="overflow-x:auto">-->
<!--                <div id="program-chair-info-body" class="collapse show" aria-labelledby="program-chair-info-header" data-bs-parent=".card">-->
<!--                    <table id="abstractTable" class="table table-bordered" style="border:2px solid black;">-->
<!--                        <thead>-->

<!--                        </thead>-->
<!--                        <tbody>-->
<!--                        <tr>-->
<!--                            <td>Cooper Programm Chair Comments: </td>-->
<!--                            <td></td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>Acceptance Status: </td>-->
<!--                            <td></td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>Recommended for Publications: </td>-->
<!--                            <td></td>-->
<!--                        </tr>-->
<!--                        </tbody>-->
<!--                    </table>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->

        
<!---->
<!--        <div class="card">-->
<!--            <div class="card-header" id="deputy-acceptance-header" data-bs-toggle="collapse" data-bs-target="#deputy-acceptance-body" aria-expanded="true" aria-controls="deputy-acceptance-body">-->
<!--                <div class="card-title fw-bold">-->
<!--                    Deputy Acceptance Information-->
<!--                    <span class="more-less fas fa-minus float-end"></span> -->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="card-body">-->
<!--                <div id="deputy-acceptance-body" class="collapse show" aria-labelledby="deputy-acceptance-header" data-bs-parent=".card">-->
<!--                    <table id="acceptanceTable" class="table table-bordered" style="border:2px solid black;">-->
<!--                        <thead>-->
<!---->
<!--                        </thead>-->
<!--                        <tbody>-->
<!--                        <tr>-->
<!--                            <td>Cooper Programm Chair Comments:</td>-->
<!--                            <td></td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>Acceptance Status:</td>-->
<!--                            <td></td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>Recommended for Publications:</td>-->
<!--                            <td></td>-->
<!--                        </tr>-->
<!--                        </tbody>-->
<!--                    </table>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->


        <div class="card shadow">
            <div class="card-header fw-bold" id="user-info-header" type="button" data-bs-toggle="collapse" data-bs-target="#user-info-body" aria-expanded="true" aria-controls="user-info-body">
                <div class="card-title fw-bold" >
                    User Information
                    <span class="more-less fas fa-minus float-end" ></span> <!-- Icon placeholder -->
                </div>
            </div>
            <div id="user-info-body" class="collapse show" aria-labelledby="user-info-header">
                <div class="card-body">
                    <table class="table">
                        <tbody>
                        <tr>
                            <td class="text-end"><strong>User/Submitter Name:</strong></td>
                            <td class="text-start"><?=$userInfo['name']. ' ' . $userInfo['surname']?></td>
                        </tr>
                        <tr>
                            <td class="text-end"><strong>User/Submitter Email:</strong></td>
                            <td class="text-start"><?=$userInfo['email'] ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>
</main>
<script>
    let baseUrlAdmin = "<?=base_url().'admin/'?>";
    $(function(){

        $('#publishBtn').on('click', function(){
            let paper_id = $(this).attr('paper_id')
            Swal.fire({
                title: "Are you sure?",
                text: 'Once published, this text will be automatically displayed on the submission site page, "My Submissions" under "Reviewer Comments" for the submitter.',
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Publish it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    publish(paper_id, function(response){
                        response = JSON.parse(response)
                        if(response.status == '200'){
                            Swal.fire({
                                title: "Saved!",
                                text:  "Successfully Published!",
                                icon: "success"
                            });
                        }else{
                            Swal.fire({
                                title: "Error",
                                text:  "Something went wrong!",
                                icon: "error"
                            });
                        }
                    });
                }
            });
        })

        $('#publishAndSendBtn').on('click', function(){
            let template = $('#templateSelect').val();
            Swal.fire({
                title: "Are you sure?",
                text: 'Once published, this text will be automatically displayed on the submission site page, "My Submissions" under "Reviewer Comments" and a notification email will be sent to the submitter of this paper.',
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Publish and Send!",
            }).then((result) => {
                if (result.isConfirmed) {
                    publish(paper_id, function(response){
                        response = JSON.parse(response)
                        if(response.status == '200'){
                            publishAndSend(paper_id, template, function(emailResponse){
                                emailResponse = JSON.parse(emailResponse)
                                if(emailResponse.status == 200){
                                    Swal.fire({
                                        title: "Saved!",
                                        text:  "Successfully Emailed and Published!",
                                        icon: "success"
                                    });
                                }else{
                                    Swal.fire({
                                        title: "Error",
                                        text:  "Something went wrong!",
                                        icon: "error"
                                    });
                                }
                            })
                        }else{
                            Swal.fire({
                                title: "Error",
                                text:  "Something went wrong!",
                                icon: "error"
                            });
                        }
                    });
                }
            });
        })

        $('input[name="presStatus"]').on('change', function(){
            if($(this).val() == '1'){
                $('#presPref_accepted').css('display', 'block');

            }else{
                $('#presPref_accepted').css('display', 'none');
            }
        })

        $('.collapse').on('click', function(){
            console.log($(this))
            $(this).on('show.bs.collapse', toggleIcon);
        })

        $('.saveAdminCommentBtn').on('click', function(){
            let formData = new FormData(document.getElementById('formAdminComment'));

            $.ajax({
                url: baseUrlAdmin + 'saveAdminCommentOnPaper',
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from automatically transforming the data into a query string
                contentType: false, // Prevent jQuery from setting the content-type header
                dataType: 'json',
                success: function(response) {
                    if(response.status == 200){
                        Swal.fire({
                            title: "Success",
                            text:  response.message,
                            icon: "success"
                        });
                    }
                   console.log(response)
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        })

        $('.saveTrackBtn').on('click', function(){
            // let formTracks = new FormData(document.getElementById('formTracks'))
            let paper_id = $(this).attr('paper_id');
            let selectedTracks = [];
            $('input[name="tracks[]"]:checked').each(function() {
                selectedTracks.push($(this).val());
            });

            $.ajax({
                url: baseUrlAdmin + 'savePaperTracks',
                type: 'POST',
                data: {
                    'selectedTracks' : selectedTracks,
                    'paper_id':paper_id
                },

                dataType: 'json',
                success: function(response) {
                    if(response.status == 200){
                        Swal.fire({
                            title: "Success",
                            text:  response.message,
                            icon: "success"
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: "error",
                        text:  error,
                        icon: "error"
                    });
                }
            });
        })

    })

    function toggleIcon(event) {
        $(event.target)
            .closest('.card-header') // Find the closest card-header element
            .find('.more-less')      // Find the .more-less element within that card-header
            .toggleClass('fa-plus fa-minus');
    }


    function publish(paper_id, callback) {

        if($('input[name="presStatus"]:checked').val() == 1 && $('#presType').val() == ''){
            toastr.error('Please select Presentation Preference')
            return false;
        }

        let formData = new FormData(document.getElementById('adminAcceptanceForm'));
        formData.append('paper_id', paper_id);
        formData.append('acceptance_type', 'panel');

        $.ajax({
            url: baseUrlAdmin + 'saveAdminAcceptance',
            type: 'POST',
            data: formData,
            processData: false, // Prevent jQuery from automatically transforming the data into a query string
            contentType: false, // Prevent jQuery from setting the content-type header
            success: function(response) {
              return callback(response)
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    function publishAndSend(paper_id, template, callback){
        $.ajax({
            url: baseUrlAdmin + 'emailAdminAcceptance',
            type: 'POST',
            data: {
                'paper_id': paper_id,
                'template': template
            },
            beforeSend: function() {
                // Show a SweetAlert2 loading indicator before sending the AJAX request
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we send email on the submitter.',
                    allowOutsideClick: false,
                    showConfirmButton: false, // Do not show the "OK" button
                    onBeforeOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                return callback(response)
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

</script>