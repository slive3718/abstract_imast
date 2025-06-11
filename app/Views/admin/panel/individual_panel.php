
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
        <?php echo view('admin/common/shortcut_link'); ?>
        <div class="card shadow">
            <div class="card-header fw-bold"> General Information  </div>
            <div class="card-body">
                <a href="<?=base_url()?>/admin/edit_individual_panel_submission/<?=$panel_details['id']?>" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a>
                <table class="table">
                    <tbody>
                    <tr>
                        <td class="text-end">Panel ID : </td>
                        <td ><?=$panel_details['custom_id']?></td>
                    </tr>
                    <tr>
                        <td style="width:250px" class="text-end">Panel : </td>
                        <td>Panelist</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>


            <!-- Uploaded File(s) Section -->
            <div class="card shadow">
                <div class="card-header fw-bold" id="file-upload-header" data-bs-toggle="collapse" data-bs-target="#file-upload-body" aria-expanded="true" aria-controls="file-upload-body">
                    Uploaded File(s)<?=(!isset($authorInfo)? '<span class="text-danger text-sm badge " title="Please complete requirements before finalizing abstract."><i class="fas fa-exclamation-circle" > INCOMPLETE </i></span>' :'')?>
                </div>
                <div id="file-upload-body" class="collapse show" aria-labelledby="file-upload-header" data-bs-parent="#accordionExample">
                    <div class="card-body">
                        <a href="<?=base_url()?>/admin/view_presentation_upload/<?=$panel_details['id']?>" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a>
                        <p> (The most recent uploaded file will appear at the top of the list) </p>
                        <p class="fw-bolder"> Submitter Uploads: </p>
                        <table class="table" style="margin-bottom:0px !important">
                            <?php if(!empty($paper_uploads)):
                                foreach ($paper_uploads as $index => $uploads): ?>
                                    <a href="<?=base_url($uploads['file_path'].$uploads['file_name'])?>" > <?=$uploads['file_preview_name']?></a><br>
                                <?php endforeach; endif ?>
                        </table>

                        <p class="mt-4 fw-bolder"> Reviewer Uploads: </p>
                        <table class="table" style="margin-bottom:0px !important">
                            <?php if(!empty($paper_reviewer_uploads)):
                                foreach ($paper_reviewer_uploads as $index => $uploads): ?>
                                    <a href="<?=base_url($uploads['file_path'].$uploads['file_name'])?>" > <?=$uploads['file_preview_name']?></a><br>
                                <?php endforeach; endif ?>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Author Information Section -->
            <div class="card shadow">
                <div class="card-header fw-bold" id="author-info-header" data-bs-toggle="collapse" data-bs-target="#author-info-body" aria-expanded="true" aria-controls="author-info-body">
                    Panelist Information <?=(!isset($authorInfo)? '<span class="text-danger text-sm badge " title="Please complete requirements before finalizing abstract."><i class="fas fa-exclamation-circle" > INCOMPLETE </i></span>' :'')?>
                </div>
                <div id="author-info-body" class="collapse show" aria-labelledby="author-info-header" data-bs-parent="#accordionExample">
                    <div class="card-body">
                        <a href="<?=base_url()?>/admin/panelist/<?=$paper_id?>" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a>
                        <table class="table" style="margin-bottom:0px !important">
                            <tbody>
                            <tr>
                                <td class="text-end" style="width:250px">
                                    Author List:
                                </td>
                                <td>
                                    <?php if($authorInfo):
                                        foreach ($authorInfo as $index=> $author):?>
                                            <?=($index + 1).'. <strong>Panelist: </strong>'?>
                                            <?=$author['name'].' '.$author['surname'] ?><br>
                                        <?php endforeach; endif; ?>
                                </td>
                            </tr>
                            <?php if($authorInfo):
                                foreach ($authorInfo as $index=>$author):
                                    ?>
                                    <tr >
                                        <td class="text-end">(<?=($index+1)?>) <?=($author['is_presenting_author'] == "Yes")? 'Presenting Author :':'Co-Author :'?></td>
                                        <td><strong><?= UcFirst($author['name']) . ' ' .UcFirst($author['surname']) ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">Author Info: </td>
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
                                        <td>No</td>
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

            <!-- Paper Information Section -->
            <div class="card shadow">
                <div class="card-header fw-bold" id="paper-info-header" data-bs-toggle="collapse" data-bs-target="#paper-info-body" aria-expanded="true" aria-controls="paper-info-body">
                    Panel Information <?=(!isset($papers)? '<span class="text-danger text-sm badge " title="Please complete requirements before finalizing abstract."><i class="fas fa-exclamation-circle" > INCOMPLETE </i></span>' :'')?>
                </div>
                <div id="paper-info-body" class="collapse show" aria-labelledby="paper-info-header" data-bs-parent="#accordionExample">
                    <div class="card-body">
                        <a href="<?=base_url()?>/admin/edit_individual_panel_submission/<?=$panel_details['id']?>" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a>
                        <table class="table" style="border-bottom-width:4px !important">
                            <tbody>
                            <?php if($papers):
                                ?>
                                <tr>
                                    <td class="text-end">Division : </td>
                                    <td><?=$papers->division_name?></td>
                                </tr>

                                <tr>
                                    <td class="text-end">Panel Overview Title : </td>
                                    <td><?=$papers->title?></td>
                                </tr>
                                <tr>
                                    <td class="text-end">Panel Overview Brief Summary: </td>
                                    <td><?=$papers->summary?></td>
                                </tr>
                                <tr>
                                    <td class="text-end">Panel Overview Are you interested in submitting this paper to IJMC as well?: </td>
                                    <td><?=$papers->is_ijmc_interested == '0' ? 'I am NOT interested in submitting this paper to IJMC' ? $papers->is_ijmc_interested == '1':'I am interested in submitting this paper to IJMC':'I have already submitted this paper to IJMC' ?></td>
                                </tr>

                                <tr>
                                    <td  class="text-end">Individual Panel Title: </td>
                                    <td><?=strip_tags($panel_details ? $panel_details['individual_panel_title'] : '');?></td>
                                </tr>
                            <?php endif;?>
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
                            </form>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


        <div class="card">
            <div class="card-header" id="program-chair-info-header"  data-bs-toggle="collapse" data-bs-target="#program-chair-info-body" aria-expanded="true" aria-controls="program-chair-info-body">
                <div class="card-title fw-bolder">
                    Program Chair Information
                </div>
            </div>
            <div class="card-body" style="overflow-x:auto">
                <div id="program-chair-info-body" class="collapse show" aria-labelledby="program-chair-info-header" data-bs-parent=".card">
                    <table id="abstractTable" class="table table-bordered" style="border:2px solid black;">
                        <thead>
                        <!-- Table headings -->
                        </thead>
                        <tbody>
                        <tr>
                            <td>Cooper Programm Chair Comments: </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Acceptance Status: </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Recommended for Publications: </td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="card">
            <div class="card-header" id="admin-option-header"  data-bs-toggle="collapse" data-bs-target="#admin-option-body" aria-expanded="true" aria-controls="admin-option-body">
                <div class="card-title fw-bold">
                    Administration Option
                    <span class="more-less fas fa-minus float-end"></span> <!-- Icon placeholder -->
                </div>
            </div>
            <div class="card-body">
                <div id="admin-option-body" class="collapse show" aria-labelledby="admin-option-header" data-bs-parent=".card">
                    <div class="container">
                        <form id="adminAcceptanceForm" onsubmit="return false;">
                            <div class="row">
                                <div class="col-md-12">
                                    <p>To give a submission status, click on the status and then click save. If you accepted this submission you must choose an acceptance type. If you add administration comments, you must click on 'Save'. There comments are only visible to administrators.</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Accept/Reject:</label><br>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="presStatus" value="1" id="accepted" <?=!empty($panel_admin_individual_acceptance) && $panel_admin_individual_acceptance['acceptance_confirmation'] == 1 ? 'checked':''?>>
                                        <label for="accepted" class="form-check-label">Accept</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="presStatus" value="2" id="rejected" <?=!empty($panel_admin_individual_acceptance) && $panel_admin_individual_acceptance['acceptance_confirmation'] == 2 ? 'checked':''?>>
                                        <label for="rejected" class="form-check-label">Reject</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="presStatus" value="3" id="suggested" <?=!empty($panel_admin_individual_acceptance) && $panel_admin_individual_acceptance['acceptance_confirmation'] == 3 ? 'checked':''?>>
                                        <label for="suggested" class="form-check-label">Suggested Revision</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="presStatus" value="4" id="required" <?=!empty($panel_admin_individual_acceptance) && $panel_admin_individual_acceptance['acceptance_confirmation'] == 4 ? 'checked':''?>>
                                        <label for="required" class="form-check-label">Required Revision</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="presStatus" value="5" id="declined" <?=!empty($panel_admin_individual_acceptance) && $panel_admin_individual_acceptance['acceptance_confirmation'] == 5 ? 'checked':''?>>
                                        <label for="declined" class="form-check-label">Declined/Withdrawn for Participation</label>
                                    </div>

                                    <!-- Remaining radio buttons -->
                                </div>
                                <div class="col-md-6">
                                    <div id="presPref_accepted" style="display:<?=!empty($admin_acceptance) && $admin_acceptance['acceptance_confirmation'] == 1 ? 'block':'none'?>;">
                                        <label class="required"> <span class="text-danger">*</span>Accepted Presentation Preference:</label>
                                        <select class="form-select" name="presType" id="presType">
                                            <option value=""> -- Select -- </option>
                                            <option value="1" <?=!empty($panel_admin_individual_acceptance) && $panel_admin_individual_acceptance['presentation_preference'] == 1 ? 'selected':''?>>Presentation Only</option>
                                            <option value="2" <?=!empty($panel_admin_individual_acceptance) && $panel_admin_individual_acceptance['presentation_preference'] == 2 ? 'selected':''?>>Publication Only</option>
                                            <option value="3" <?=!empty($panel_admin_individual_acceptance) && $panel_admin_individual_acceptance['presentation_preference'] == 3 ? 'selected':''?>>Presentation and Publication</option>
                                        </select>
<!--                                        <div id="panel_no" style="display:none; padding-top: 3px;"><br> Poster No.: <input type="text" name="PosterNo" id="PosterNo" class="form-control" size="20" value=""></div>-->
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Comments to Submitter:</label>
                                    <textarea id="st_comments_to_submitter" name="st_comments_to_submitter" class="form-control" style="height: 200px"><?=!empty($panel_admin_individual_acceptance) && $panel_admin_individual_acceptance['comment_to_submitter'] !== '' ? trim($panel_admin_individual_acceptance['comment_to_submitter']):''?></textarea>
                                    <br>
                                    <span>Once published, this text will be automatically displayed on the submission site page, "My Submissions" under "Reviewer Comments" for the submitter.</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Choose Email Template:</label>
                                    <select class="form-select" name="template" id="templateSelect">
                                        <option value=""> None  </option>
                                        <!-- Remaining options -->
                                        <?php if(!empty($email_templates)): ?>
                                        <?php foreach($email_templates as $template): ?>
                                                <option value="<?=$template['id']?>" <?=!empty($panel_admin_individual_acceptance) && $panel_admin_individual_acceptance['email_template_used'] == $template['id'] ? 'selected':''?>> <?=$template['template_name']?>  </option>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
<!--                                    <input type="hidden" name="statusForPoster" id="statusForPoster" value="">-->
<!--                                    <input type="hidden" name="st_id" value="1">-->
<!--                                    <input type="hidden" name="pp_id_c" value="1">-->
                                    <input type="button" class="btn btn-primary" id="publishBtn" value="Save" onclick="" paper_id="<?=$papers->id?>"  individual_panel_id="<?=$panel_details['id']?>>">
                                    <input type="button" class="btn btn-info text-white" id="publishAndSendBtn" value="Save and Send Mail" onclick="" paper_id="<?=$papers->id?>"  individual_panel_id="<?=$panel_details['id']?>">
                                </div>
                            </div>
                        </form>

                        <hr>

                        <form id="formAdminComment" name="formAdminComment" onsubmit="return false;">
                            <div>
                                <input type="hidden" name="individual_panel_id" value="<?=$panel_details['id']?>">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="adminComment">Administration Comments:</label>
                                    <textarea class="form-control" name="adminComment" id="adminComment" cols="60" rows="7"><?=!empty($panel_admin_individual_acceptance) && $panel_admin_individual_acceptance['administration_comment'] !== '' ? trim($panel_admin_individual_acceptance['administration_comment']):''?></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Flag this submission:</label><br>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="adminFlag" id="adminFlagYes" value="1" <?=!empty($panel_admin_individual_acceptance) && $panel_admin_individual_acceptance['is_flag'] == 1 ? 'checked':''?>>
                                        <label class="form-check-label" for="adminFlagYes">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="adminFlag" id="adminFlagNo" value="0" <?=!empty($panel_admin_individual_acceptance) && $panel_admin_individual_acceptance['is_flag'] == 0 ? 'checked':''?>>
                                        <label class="form-check-label" for="adminFlagNo">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="button" class="btn btn-primary saveAdminCommentBtn" value="Save" onclick="">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header" id="deputy-acceptance-header" data-bs-toggle="collapse" data-bs-target="#deputy-acceptance-body" aria-expanded="true" aria-controls="deputy-acceptance-body">
                <div class="card-title fw-bold">
                     Acceptance Information
                    <span class="more-less fas fa-minus float-end"></span> <!-- Icon placeholder -->
                </div>
            </div>
            <div class="card-body">
                <div id="deputy-acceptance-body" class="collapse show" aria-labelledby="deputy-acceptance-header" data-bs-parent=".card">
                    <table id="acceptanceTable" class="table table-bordered" style="border:2px solid black;">
                        <thead>
                        <!-- Table header content -->
                        </thead>
                        <tbody>
                        <tr>
                            <td>Cooper Programm Chair Comments:</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Acceptance Status:</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Recommended for Publications:</td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header" id="author-acceptance-header" data-bs-toggle="collapse" data-bs-target="#author-acceptance-body" aria-expanded="true" aria-controls="author-acceptance-body">
                <div class="card-title fw-bold">
                    Author Acceptance Information
                    <span class="more-less fas fa-minus float-end"></span> <!-- Icon placeholder -->
                </div>
            </div>
            <div class="card-body">
                <div id="author-acceptance-body" class="collapse show" aria-labelledby="author-acceptance-body" data-bs-parent=".card">
                    <table id="acceptanceTable" class="table table-bordered" style="border:2px solid black;">
                        <thead>
                        <!-- Table header content -->
                        </thead>
                        <tbody>
                        <?php if(isset($authorInfo)): foreach ($authorInfo as $author): ?>
                            <tr>
                                <td style="width:220px">Author:</td>
                                <td class="fw-bolder">

                                    <?= ($author) ? $author['name'].''.$author['surname'] : '' ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Acceptance Status:</td>
                                <td>
                                    <?= ($author['acceptance'] && in_array($author['acceptance']->acceptance_confirmation, ['1'])) ? "I plan to present at the 129th AFS Metalcasting Congress held in Atlanta, Georgia April 12-15, 2025. " : ''; ?>
                                    <?= ($author['acceptance'] && in_array($author['acceptance']->acceptance_confirmation, ['2'])) ? "I am unable to participate in the 129th AFS Metalcasting Congress held in Atlanta, Georgia April 12-15, 2025. " : ''; ?>
                                    <?= (!$author['acceptance']) ? "N/A" : ''; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Uploaded Presentation:</td>
                                <td>
                                    <a href="<?= $author['acceptance'] ? base_url().$author['acceptance']->presentation_file_path.'/'.$author['acceptance']->presentation_saved_name : '' ?>"> <?= ($author['acceptance']->presentation_saved_name  ?? '')?></a>
                                </td>
                            </tr>
                        <?php endforeach; endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

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
            let individual_panel_id = $(this).attr('individual_panel_id')
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
                    publish(individual_panel_id, function(response){
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
            let individual_panel_id = $(this).attr('individual_panel_id')
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
                    publish(individual_panel_id, function(response){
                        response = JSON.parse(response)
                        if(response.status == '200'){
                            publishAndSend(individual_panel_id, template, function(emailResponse){
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

        if($('input[name="presStatus"]:checked').val() == 1){
            $('#presPref_accepted').css('display', 'block');
        }else{
            $('#presPref_accepted').css('display', 'none');
        }


        $('.collapse').on('click', function(){
            console.log($(this))
            $(this).on('show.bs.collapse', toggleIcon);
        })

        $('.saveAdminCommentBtn').on('click', function(){
            let formData = new FormData(document.getElementById('formAdminComment'));

            $.ajax({
                url: baseUrlAdmin + 'saveIndividualPanelComment',
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


    function publish(individual_panel_id, callback) {

        if($('input[name="presStatus"]:checked').val() == 1 && $('#presType').val() == ''){
            toastr.error('Please select Presentation Preference')
            return false;
        }

        let formData = new FormData(document.getElementById('adminAcceptanceForm'));
        formData.append('individual_panel_id', individual_panel_id);

        $.ajax({
            url: baseUrlAdmin + 'saveIndividualPanelAdminAcceptance',
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

    function publishAndSend(individual_panel_id, template, callback){
        $.ajax({
            url: baseUrlAdmin + 'emailAdminAcceptance',
            type: 'POST',
            data: {
                'paper_id': paper_id,
                'individual_panel_id': individual_panel_id,
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