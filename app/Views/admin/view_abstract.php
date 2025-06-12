
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
    <div class="container-fluid pb-5">
        <?php echo view('admin/common/shortcut_link'); ?>
        <div class="card shadow">
            <div class="card-header">
                <span class="collapse-toggle link-primary btn border-0 text-dark fw-bolder" data-bs-toggle="collapse" data-bs-target="#general-body">
                General Information
                </span>
                <a href="<?=base_url()?>/admin/edit_papers_submission/<?=$paper_id?>" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a>
            </div>
            <div id="general-body" class="collapse show" aria-labelledby="general-header" data-bs-parent="#accordionExample">
                <div class="card-body">
                    <table class="table">
                        <tbody>
                        <tr>
                            <td class="text-end">Paper ID : </td>
                            <td ><?=$papers->id?></td>
                        </tr>
                        <tr>
                            <td style="width:250px" class="text-end">Paper Title : </td>
                            <td><?=$papers->title?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

            <!-- Uploaded File(s) Section -->
            <div class="card shadow">
                <div class="card-header" id="file-upload-header">
                    <span class="collapse-toggle link-primary btn border-0 text-dark fw-bolder" data-bs-toggle="collapse" data-bs-target="#file-upload-body" aria-expanded="true" aria-controls="file-upload-body" >
                        Uploaded File(s)<?=(!isset($authorInfo)? '<span class="text-danger text-sm badge" title="Please complete requirements before finalizing abstract.">
                        <i class="fas fa-exclamation-circle"> INCOMPLETE </i></span>' :'')?>
                    </span>
                    <a href="<?=base_url()?>/admin/view_paper_presentation_upload/<?=$paper_id?>" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a>
                </div>

                <div id="file-upload-body" class="collapse show" aria-labelledby="file-upload-header" data-bs-parent="#accordionExample">
                    <div class="card-body">
                        <p> (The most recent uploaded file will appear at the top of the list) </p>
                        <p class="fw-bolder"> Submitter Uploads: </p>
                        <table class="table" style="margin-bottom:0px !important">
                            <?php if(!empty($paper_uploads)):
                                foreach ($paper_uploads as $index => $uploads): ?>
                                    <div id="uploadDiv_<?=$uploads['id']?>" style="display: block"><a href="<?=base_url($uploads['file_path'].$uploads['file_name'])?>" > <?=$uploads['file_preview_name']?> </a> <span><a href="" class="btn btn-danger btn-sm deleteUploadBtn ms-2" upload_id="<?=$uploads['id']?>"> <i class="fas fa-times text-white me-2"></i>Delete</a></span></div>
                                <?php endforeach; endif ?>
                        </table>

                        <p class="mt-4 fw-bolder"> Reviewer Uploads: </p>
                        <table class="table" style="margin-bottom:0px !important">
                            <?php if(!empty($paper_reviewer_uploads)):
                                foreach ($paper_reviewer_uploads as $index => $uploads): ?>
                                    <a href="<?=base_url($uploads['file_path'].$uploads['file_name'])?>" > <?=$uploads['file_preview_name']?></a>  <br>
                                <?php endforeach; endif ?>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Author Information Section -->
            <div class="card shadow">
                <div class="card-header" id="author-info-header">
                    <span class="collapse-toggle link-primary btn border-0 text-dark fw-bolder"  data-bs-toggle="collapse" data-bs-target="#author-info-body" aria-expanded="true" aria-controls="author-info-body" style="cursor: pointer;">
                        Author Information <?=(!isset($authorInfo)? '<span class="text-danger text-sm badge " title="Please complete requirements before finalizing abstract.">
                        <i class="fas fa-exclamation-circle" > INCOMPLETE </i></span>' :'')?>
                    </span>
                    <a href="<?=base_url()?>/admin/authors_and_copyright/<?=$paper_id?>" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a>
                </div>
                <div id="author-info-body" class="collapse show" aria-labelledby="author-info-header" data-bs-parent="#accordionExample">
                    <div class="card-body">
                        <table class="table" style="margin-bottom:0px !important">
                            <tbody>
                            <tr>
                                <td class="text-end" style="width:250px">
                                    Author List:
                                </td>
                                <td>
                                    <?php if($authorInfo):
                                        foreach ($authorInfo as $author):?>
                                            <?=($author['is_presenting_author'] == "Yes")? '<strong> Presenting Author </strong>: ':'<strong> Co-Author </strong>: '?>
                                            (<?=$author['author_order']?>)

                                            <?=$author['name'].' '.$author['surname'] ?><br>
                                        <?php endforeach; endif; ?>
                                </td>
                            </tr>
                            <?php if($authorInfo):
                                foreach ($authorInfo as $index=>$author):
                                    ?>
                                    <tr >
                                        <td class="text-end">(<?=($index+1)?>) <?=($author['is_presenting_author'] == "Yes")? 'Presenting Author :':'Co-Author :'?></td>
                                        <td><strong><?=UcFirst($author['name']).' '.UcFirst($author['surname']) ?></strong></td>
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
                                        <td><?=($author['is_correspondent'])?'Yes':'No'?></td>
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
                <div class="card-header" id="paper-info-header" >
                     <span class="collapse-toggle link-primary btn border-0 text-dark fw-bolder"  data-bs-toggle="collapse" data-bs-target="#paper-info-body" aria-expanded="true" aria-controls="paper-info-body">
                    Paper Information <?=(!isset($papers)? '<span class="text-danger text-sm badge " title="Please complete requirements before finalizing abstract."><i class="fas fa-exclamation-circle" > INCOMPLETE </i></span>' :'')?>
                    </span>
                    <a href="<?=base_url()?>/admin/edit_papers_submission/<?=$paper_id?>" class="btn btn-sm btn-primary float-end"><i class="fas fa-edit"></i> Edit</a>
                </div>
                <div id="paper-info-body" class="collapse show" aria-labelledby="paper-info-header" data-bs-parent="#accordionExample">
                    <div class="card-body">
                        <table class="table" style="border-bottom-width:4px !important">
                            <tbody>
                            <?php if($papers):
                                ?>
                                <tr>
                                    <td class="text-end">Division : </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="text-end">Paper Type : </td>
                                    <td><?=$papers->paper_type_name?></td>
                                </tr>
                                <tr>
                                    <td class="text-end">Paper Title : </td>
                                    <td><?=$papers->title?></td>
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
            <div class="card-header" id="review-info-header">
                <span class="collapse-toggle link-primary btn border-0 text-dark fw-bolder"  data-bs-toggle="collapse" data-bs-target="#review-info-body" aria-expanded="true" aria-controls="review-info-body">
                    Review Information
                </span>
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
                                    <td colspan="12`"><textarea class="w-100 border-0" readonly id="suggestedRevisionComment_<?=$review['id']?>"><?=$review['suggested_revision_comment']?></textarea></td>


<!--                                    <td colspan="2"><input type="checkbox" --><?php //=(isset($review['display_suggestion']) && $review['display_suggestion'] == 1)?'checked':''?><!-- class="displayCheckbox displaySuggested" name="displayToSubmitter" review_id = "--><?php //=$review['id']?><!--" id="displaySuggested_--><?php //=$review['id']?><!--"> <label for="displaySuggested_--><?php //=$review['id']?><!--"> Display to Submitter</label></td>-->
                                </tr>
                                <tr>
                                    <td>Required Revision Comments:</td>
                                    <td colspan="12"><textarea class="w-100 border-0" readonly id="requiredRevisionComment_<?=$review['id']?>"><?=$review['required_revision_comment']?></textarea></td>

<!--                                    <td colspan="2"><input type="checkbox" --><?php //=(isset($review['display_required']) && $review['display_required'] == 1)?'checked':''?><!-- class="displayCheckbox displayRequired" name="displayToSubmitter" review_id = "--><?php //=$review['id']?><!--" id="displayRequired_--><?php //=$review['id']?><!--"> <label for="displayRequired_--><?php //=$review['id']?><!--"> Display to Submitter</label></td>-->
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
<!--                                            <div class="input-group-append">-->
<!--                                                <button class="btn btn-info saveDeadlineBtn px-4 text-white border-dark" review_id="--><?php //=$review['id']?><!--">Save Deadline</button>-->
<!--                                            </div>-->
<!---->
<!--                                            <span class="float-end emailToSubmitterBtn"><a class="ms-5 btn btn-success btn-sm" href=""  review_id="--><?php //=$review['id']?><!--" >Email to Submitter Now</a></span>-->
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
                                        <input class="form-check-input" type="radio" name="presStatus" value="1" id="accepted" <?=!empty($admin_acceptance) && $admin_acceptance['acceptance_confirmation'] == 1 ? 'checked':''?>>
                                        <label for="accepted" class="form-check-label">Accept</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="presStatus" value="2" id="rejected" <?=!empty($admin_acceptance) && $admin_acceptance['acceptance_confirmation'] == 2 ? 'checked':''?>>
                                        <label for="rejected" class="form-check-label">Reject</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="presStatus" value="3" id="suggested" <?=!empty($admin_acceptance) && $admin_acceptance['acceptance_confirmation'] == 3 ? 'checked':''?>>
                                        <label for="suggested" class="form-check-label">Suggested Revision</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="presStatus" value="4" id="required" <?=!empty($admin_acceptance) && $admin_acceptance['acceptance_confirmation'] == 4 ? 'checked':''?>>
                                        <label for="required" class="form-check-label">Required Revision</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="presStatus" value="5" id="declined" <?=!empty($admin_acceptance) && $admin_acceptance['acceptance_confirmation'] == 5 ? 'checked':''?>>
                                        <label for="declined" class="form-check-label">Declined/Withdrawn for Participation</label>
                                    </div>

                                    <!-- Remaining radio buttons -->
                                </div>
                                <div class="col-md-6">
                                    <div id="presPref_accepted" style="display:<?=!empty($admin_acceptance) && $admin_acceptance['acceptance_confirmation'] == 1 ? 'block':'none'?>;">
                                        <label class="required"> <span class="text-danger">*</span>Accepted Presentation Preference:</label>
                                        <select class="form-select" name="presType" id="presType">
                                            <option value=""> -- Select -- </option>
                                            <?php if(!empty($paper_types)): ?>
                                            <?php foreach ($paper_types as $paper_type) : ?>
                                                <option value="<?=$paper_type['type']?>" <?= (!empty($admin_acceptance) && $admin_acceptance['presentation_preference'] == $paper_type['type'] ? 'selected':'')?>><?=$paper_type['name']?></option>
                                            <?php endforeach; ?>
                                            <?php endif ?>
                                        </select>
<!--                                        <div id="panel_no" style="display:none; padding-top: 3px;"><br> Poster No.: <input type="text" name="PosterNo" id="PosterNo" class="form-control" size="20" value=""></div>-->
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Comments to Submitter:</label>
                                    <textarea id="st_comments_to_submitter" name="st_comments_to_submitter" class="form-control" style="height: 200px"><?=!empty($admin_acceptance) && $admin_acceptance['comment'] !== '' ? trim($admin_acceptance['comment']):''?></textarea>
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
                                                <option value="<?=$template['id']?>" <?=!empty($admin_acceptance) && $admin_acceptance['email_template_used'] == $template['id'] ? 'selected':''?>> <?=$template['template_name']?>  </option>
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
                                    <input type="button" class="btn btn-primary" id="publishBtn" value="Save" onclick="" paper_id="<?=$papers->id?>">
                                    <input type="button" class="btn btn-info text-white" id="publishAndSendBtn" value="Save and Send Mail" onclick="" paper_id="<?=$papers->id?>">
                                </div>
                            </div>
                        </form>

                        <hr>

                        <form id="formAdminComment" name="formAdminComment" onsubmit="return false;">
                            <div>
                                <input type="hidden" name="abstract_id" value="<?=$papers->id?>">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="adminComment">Administration Comments:</label>
                                    <textarea class="form-control" name="adminComment" id="adminComment" cols="60" rows="7"><?=!empty($adminComment) && $adminComment['comment'] !== '' ? trim($adminComment['comment']):''?></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Flag this submission:</label><br>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="adminFlag" id="adminFlagYes" value="1" <?=!empty($adminComment) && $adminComment['is_flag'] == 1 ? 'checked':''?>>
                                        <label class="form-check-label" for="adminFlagYes">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="adminFlag" id="adminFlagNo" value="0" <?=!empty($adminComment) && $adminComment['is_flag'] == 0 ? 'checked':''?>>
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
                    Deputy Acceptance Information
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
                        <?php if(isset($authorInfo)): foreach ($authorInfo as $author): if($author['is_presenting_author'] == 'Yes'): ?>
                        <tr>
                            <td style="width:220px">Author:</td>
                            <td class="fw-bolder">
                                <?= ($author) ? $author['name'].' '.$author['surname'] : '' ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Acceptance Status:</td>
                            <td>
                                <?= ($admin_acceptance && $admin_acceptance['acceptance_confirmation'] !== '2' && $author['acceptance'] && in_array($author['acceptance']->acceptance_confirmation, ['1'])) ? "I plan to present at the 129th AFS Metalcasting Congress held in Atlanta, Georgia April 12-15, 2025. " : ''; ?>
                                <?= ($admin_acceptance && $admin_acceptance['acceptance_confirmation'] !== '2' && $author['acceptance'] && in_array($author['acceptance']->acceptance_confirmation, ['2'])) ? "I am unable to participate in the 129th AFS Metalcasting Congress held in Atlanta, Georgia April 12-15, 2025. " : ''; ?>
                                <?= ($admin_acceptance && $admin_acceptance['acceptance_confirmation'] !== '2' && !$author['acceptance']) ? "Incomplete" : ''; ?>
                                <?= ($admin_acceptance && $admin_acceptance['acceptance_confirmation'] == '2') ? "N/A" : ''; ?>
                            </td>
                        </tr>
                            <tr>
                                <td>Uploaded Presentation:</td>
                                <td>
                                    <a href="<?= $author['acceptance'] ? base_url().$author['acceptance']->presentation_file_path.'/'.$author['acceptance']->presentation_saved_name : '' ?>"> <?= ($author['acceptance']->presentation_saved_name  ?? '')?></a>
                                </td>
                            </tr>
                        <?php endif; endforeach; endif ?>
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

        $('.deleteUploadBtn').on('click',function(e) {
            let uploadID = $(this).attr('upload_id');
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: base_url + 'admin/delete_presentation_upload',
                        type: 'POST',
                        data: {
                            'upload_id': $(this).attr('upload_id')
                        },
                        dataType: 'json',
                        success: function (response) {
                            console.log(response);
                            if (response.status == 200) {
                                Swal.fire(
                                    'Deleted!',
                                    'Your file has been deleted.',
                                    'success'
                                )

                                $('#uploadDiv_'+uploadID).css('display', 'none');
                            }

                        }, error: function (e) {
                            Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong: ' + e.message,});
                        }
                    })

                }
            })

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
        formData.append('acceptance_type', 'paper');
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