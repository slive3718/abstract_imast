
<script  type="text/javascript" src="<?=base_url('assets/js/submissionFunction.js?v=1')?>"></script>


<?php //echo'<pre>'; print_r($abstract_details);exit;?>
<?php echo view('event/common/menu'); ?>
<?php 
// print_r($abstract_disclosure); 

// exit ;
?>
<main>
    <div class="container" style="margin-bottom:200px">

        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning text-center" role="alert">
                    Submission deadline is now: Thursday, August 15, 2024 at 5 pm ET.
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <div class="alert alert-success text-center" role="alert">
                        Note: Submissions are now open.
                    </div>
                </div>
            </div>
        </div>
        <h5  class="fw-bold"> Main Menu</h5>
        <hr />

        <div class="row mt-5">
            <div class="col-md-12">
                <div id="landing-page-contents" class="container-fluid p-4">
                    <div class="submission-menu" style="font-family: inherit;">
                        <a href="<?=base_url()?>user/edit_papers_submission/<?=$panel_id?>" class="btn btn-white btn-sm round-0 text-start ps-0 fw-bold" style="width:80%; border-bottom:1px solid red">
                            <num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">1 </num> Paper / Presentation
                            <?php if(!empty($incompleteStatus['paper'])):?>
                            <span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>
                            <?php else: ?>
                                <span class="float-end text-success"><i class="fw-bold fas fa-exclamation-circle"></i> Complete </span>
                            <?php endif?>
                        </a>
                        <a href="<?=base_url()?>user/authors_and_copyright/<?=$panel_id?>" class="btn btn-white btn-sm round-0 text-start mt-2 ps-0 fw-bold" style="width:80%; border-bottom:1px solid red">
                            <num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">2 </num> Authors / Presenters Copyright
                            <?php if(!empty($incompleteStatus['author'])):?>
                                <span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>
                            <?php else: ?>
                                <span class="float-end text-success"><i class="fw-bold fas fa-exclamation-circle"></i> Complete </span>
                            <?php endif?>
                        </a>
                        <a href="<?=base_url()?>user/presentation_upload/<?=$panel_id?>" class="btn btn-white btn-sm round-0 text-start mt-2 ps-0 fw-bold" style="width:80%; border-bottom:1px solid red">
                            <num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">3 </num> Paper or Prensentation Upload
                            <?php if(!empty($incompleteStatus['paperUpload'])):?>
                                <span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>
                            <?php else: ?>
                                <span class="float-end text-success"><i class="fw-bold fas fa-exclamation-circle"></i> Complete </span>
                            <?php endif?>
                        </a>
<!--                        <a href="YOUR_BASE_URL/YOUR_EVENT_URI/permissions/YOUR_ABSTRACT_ID" class="btn btn-white btn-sm round-0 text-start mt-2 ps-0 fw-bold" style="width:80%; border-bottom:1px solid red">-->
<!--                            <num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">4 </num> Permissions-->
<!--                            <span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>-->
<!--                        </a>-->
                        <a href="<?=base_url()?>user/finalize_paper/<?=$panel_id?>" class="btn btn-white btn-sm round-0 text-start mt-2 ps-0 fw-bold previewFinalizeBtn" style="width:80%; border-bottom:1px solid red">
                            <num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">5 </num> Print/Preview/Finalize
                            <?php if(!empty($incompleteStatus['finalized'])):?>
                                <span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>
                            <?php else: ?>
                                <span class="float-end text-success"><i class="fw-bold fas fa-exclamation-circle"></i> Complete </span>
                            <?php endif?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
