
<?php echo view('event/common/menu'); ?>

<main>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="title"><?=$title?></div>
            </div>
            <div class="card-body">
                <div class="px-2 mt-3">
                    <strong>Welcome <?= ucFirst(session('name')).' '.ucFirst(session('surname'))?></strong>
                    <p>Please choose to add a new submission or click on View/Edit to access a previous submission.</p>
                </div>
                <div id="" class="my-3">
                    <div class="submission-page">
                        <a href="<?=base_url().'user/papers_submission/'?>" class="btn btn-success">Add a new submission</a>
                    </div>
                </div>
<!--                <p>or</p>-->
<!--                <p>To edit an existing abstract, click on the submission number below:</p>-->
            </div>
        <div class="paper-content">
            <?php if(isset($papers) && !empty($papers)):
                foreach ($papers as $index=>$paper):
                    $count = 0;
                    if($paper->submission_type == 'paper'): ?>
                        <div class="card bg-white">
                            <div class="card-header"> <strong>Submission # <?=$paper->custom_id?></strong> - <?= strip_tags($paper->title)?>
                                <span class="float-end"><a href="<?=base_url().'user/submission_menu/'.$paper->id?>" class="btn btn-success btn-sm" paper_id="<?=$paper->id?>">View/Edit</a></span>
                            </div>
                            <div class="card-body" >
                                <?php
                                $presenters = '';
                                if(!empty($paper->authors)) {
                                    foreach ($paper->authors as $authIndex => $author){
                                        $presenters .= $author['name'] . ' ' . $author['surname'].(($authIndex + 1 ) < count($paper->authors) ? ', ':'');
                                    }
                                }
                                ?>
                                <?=$paper->acronym ?? ''?> Presenter(s) : <?= $presenters ?>
                                <span class="float-end">Submission Status: <?=isset($paper)?($paper->is_finalized == "1")? '<span class="text-success">Finalized</span>':'<span class="text-danger">Incomplete</span>':''?></span>

                                <div class="mt-2">
<!--                                    <a class="btn btn-primary btn-sm" data-bs-toggle="collapse" href="#collapse_comments_--><?php //=$paper->id?><!--" role="button" aria-expanded="false" aria-controls="collapse_comments_--><?php //=$paper->id?><!--">-->
<!--                                        + Reviewer Comments-->
<!--                                    </a>-->
                                    <div class="collapse mt-2" id="collapse_comments_<?=$paper->id?>">
                                        <?php if($paper->reviewers):
                                            foreach($paper->reviewers as $reviewer):
                                                ?>

                                                <div class="mt-3">
                                                    <?php if (isset($reviewer['display_suggestion']) && $reviewer['display_suggestion'] == 1): ?>
                                                    <span><strong>Reviewer Comments (Suggested Revisions):</strong></span> <?=$reviewer['suggested_revision_comment']?> <br>
                                                    <?php endif; ?>
                                                    <?php if (isset($reviewer['display_required']) && $reviewer['display_required'] == 1): ?>
                                                    <span><strong>Reviewer Comments (Required Revisions):</strong></span> <?=$reviewer['required_revision_comment']?><br>
                                                    <?php endif; ?>
                                                    <span><strong>Reviewer Upload:</strong></span> <a href="<?=($reviewer['uploads'])?$reviewer['uploads']['file_path'].$reviewer['uploads']['file_name']:''?>"><?=($reviewer['uploads'])?$reviewer['uploads']['file_preview_name']:''?></a>
                                                    <br>
                                                    <span><strong>Submitter Comment:</strong>
                                                    <span id="submitterSpanComment_<?=$reviewer['id']?>"><?=!empty($reviewer['submitter_comment_on_upload'])?$reviewer['submitter_comment_on_upload']:''?></span>
                                                        <br>
                                                    </span><a class="pointer-cursor" data-bs-toggle="collapse" href="#collapseExample_<?=$reviewer['id']?>" role="button" aria-expanded="false" aria-controls="collapseExample_<?=$reviewer['id']?>"> Add/Update Comment </a>
                                                    <div class="collapse" id="collapseExample_<?=$reviewer['id']?>">
                                                        <div class="card card-body">
                                                            <textarea class="form-control p-0" id="submitterComment_<?=$reviewer['id']?>"><?=!empty($reviewer['submitter_comment_on_upload'])?$reviewer['submitter_comment_on_upload'] :''?></textarea>
                                                            <button type="button" class="btn btn-success btn-sm saveCommentToUploadBtn mt-2 mx-2" review_id="<?=$reviewer['id']?>" paper_id="<?=$paper->id?>" style="width:150px">Save</button>
                                                        </div>
                                                    </div>
                                                    <br>

                                                    <span><strong>Date:</strong></span> <?=$reviewer['date_time']?><br>
                                                    <?php if (!empty($reviewer['submitter_deadline'] )): ?>
                                                    <span><strong class="text-danger">Submitter Respond Deadline:</strong></span> <?=$reviewer['submitter_deadline']?><br>
                                                    <?php endif; ?>
                                                    <span><strong>Re-review Comments:</strong></span> <?=$reviewer['re_review_comment']?><br>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span> No Data.</span>
                                        <?php endif ?>
                                    </div>
                                </div>

                                <div class="mt-2">
                                    <a class="btn btn-primary btn-sm" data-bs-toggle="collapse" href="#collapse_copyright_<?=$paper->id?>" role="button" aria-expanded="false" aria-controls="collapse_copyright_<?=$paper->id?>">
                                        + View Disclosures
                                    </a>
                                    <?php foreach ($paper->authors as $author) {
                                        // Check if the value of 'is_copyright_agreement_accepted' is 1
                                        if (!empty($author['signature_signed_date'])) {
                                            // Increment counter
                                            $count++;
                                        }
                                    } ?>
                                    <span class="float-end">Disclosure Status:  <?=($count == count($paper->authors))?'<span class="text-success">Complete</span>':'<span class="text-danger">Incomplete</span>'?>  <?=$count?>/ <?=count($paper->authors)?></span>

                                    <div class="collapse mt-2" id="collapse_copyright_<?=$paper->id?>">
                                        <?php if($paper->authors):
                                            foreach($paper->authors as $author):?>
                                                <?=Ucfirst($author['name']).' '.Ucfirst($author['surname'])?> : <?= (!empty($author['signature_signed_date']))?'<span class="text-success">Completed</span>':'<span class="text-danger">Incomplete</span>'?> <br>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span> No Data.</span>
                                        <?php endif ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif;
                endforeach; endif?>
        </div>


<!-- Start of Panel Contents-->


<!--            --><?php //= print_r($paper);exit ?>
        <div class="panel-content">
            <?php if(isset($papers) && !empty($papers)):
            foreach ($papers as $index=>$paper):
            $count = 0;
            if($paper->submission_type == 'panel'): ?>
            <div class=" card bg-white ">
                <div class="card-header">  <strong>Panel #</strong><?=$paper->id?> - <?= strip_tags($paper->title)?>
                    <span class="float-end"><a href="<?=base_url().'user/panel_submission_menu/'.$paper->id?>" class="btn btn-success btn-sm" paper_id="<?=$paper->id?>">View/Edit</a></span>
                </div>
                <div class="card-body" >
                    <ul>
                    <?php foreach ($paper->panelist as $i => $panelist) :?>
                            <li> Panel # <?=$paper->id. (chr($i + 65))?> </li>
<!--                        // Check if the value of 'is_copyright_agreement_accepted' is 1-->
                      <?php  if ($panelist['is_copyright_agreement_accepted'] == "1") {
                            // Increment counter
                            $count++;
                        } ?>
                     <?php endforeach; ?>
                    </ul>
                    <span class="float-end">Submission Status: <?=isset($paper)?($paper->is_finalized == '1')? '<span class="text-success">Finalized</span>':'<span class="text-danger">Incomplete</span>':''?></span>

                    <div class="mt-5">
                        <a class="btn btn-primary btn-sm" data-bs-toggle="collapse" href="#collapse_copyright_<?=$paper->id?>" role="button" aria-expanded="false" aria-controls="collapse_copyright_<?=$paper->id?>">
                            + Copyright
                        </a>
                        <span class="float-end">Copyright Status:  <?=($count == count($paper->panelist))?'<span class="text-success">Complete</span>':'<span class="text-danger">Incomplete</span>'?>  <?=$count?>/ <?=count($paper->panelist)?></span>

                        <div class="collapse mt-2" id="collapse_copyright_<?=$paper->id?>">
                            <?php if($paper->panelist):
                                foreach($paper->panelist as $panelist):?>
                                    <?=Ucfirst($panelist['name']).' '.Ucfirst($panelist['surname'])?> : <?= ($panelist['is_copyright_agreement_accepted'] == 1)?'<span class="text-success">Completed</span>':'<span class="text-danger">Incomplete</span>'?> <br>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span> No Data.</span>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif;
        endforeach; endif?>

        <div class="row mt-2">
            <div class="col-md-12">
                <div class="alert alert-success text-center" role="alert">
                    Note: Submissions are now open.
                </div>
            </div>
        </div>
    </div>
    </div>
</main>

<!-- Modal -->
<div class="modal fade" id="submissionOptionModal" tabindex="-1" aria-labelledby="submissionOptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="submissionOptionModalLabel">Select type of submission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5">
                <h5>Choose one of the following options:</h5>
                <div class="row">
                    <a href="<?=base_url().'/user/papers_submission/'?>" class="btn btn-primary text-center text-white p-3 ">
                        <h5><i class="fas fa-file"></i> Submit a <u>Paper</u> or <u>Presentation</u></h5>
                        <h6 class="abstractSubmissionStatus"></h6>
                    </a>
                    <a href="<?=base_url().'/user/panel_coordinators/'?>" class="btn btn-primary text-center text-white p-3 mt-2">
                        <h5><i class="fas fa-person"></i> Submit a <u>Panel</u> (for Panel Coordinators Only)</h5>
                        <h6 class="educationSubmissionStatus"></h6>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        function intToChar(int){
            return String.fromCharCode(int);
        }
    })
</script>