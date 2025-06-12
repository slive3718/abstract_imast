
<script  type="text/javascript" src="<?=base_url('assets/js/submissionFunction.js?v=1')?>"></script>


<?php //echo'<pre>'; print_r($abstract_details);exit;?>
<?php echo view('event/common/menu'); ?>
<main>
    <div class="container" style="margin-bottom:200px">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-warning text-center" role="alert">
                    Submission deadline is now: Thursday, August 15, 2024 at 5 pm ET.
                </div>
                <div class="alert alert-success text-center" role="alert">
                    Note: Submissions are now open.
                </div>
            </div>
        </div>
        <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1 p-3 shadow">
            <?php if(isset($paper_id) && !empty($paper_id)): ?>
                <div class="row">
                    <div class="col-md-12" style="margin-left: 10px;">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= base_url().'home' ?>">My Submissions</a></li>
                            <li class="breadcrumb-item active"><?=$title?></li>
                        </ol>
                    </div>
                </div>
                <hr />
                <div class="px-4">
                    <h5 class="fw-bold"> <?=$title?></h5>
                </div>
                <hr />
            <?php endif ?>

            <div class="row ">
                <div class="col-md-12">
                    <div id="landing-page-contents" class="container-fluid p-4">
                        <div class="submission-menu" style="font-family: inherit;">
                            <a href="<?=base_url()?>user/edit_papers_submission/<?=$paper_id?>" class="btn btn-white btn-sm round-0 text-start ps-0 fw-bold" style="width:100%; border-bottom:1px solid red">
                                <num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">1 </num> Paper Details
                                <?php if(!empty($incompleteStatus['paper'])):?>
                                <span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>
                                <?php else: ?>
                                    <span class="float-end text-success"><i class="fw-bold fas fa-exclamation-circle"></i> Complete </span>
                                <?php endif?>
                            </a>
                            <a href="<?=base_url()?>user/authors_and_copyright/<?=$paper_id?>" class="btn btn-white btn-sm round-0 text-start mt-2 ps-0 fw-bold" style="width:100%; border-bottom:1px solid red">
                                <num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">2 </num> Authors / Presenters Copyright
                                <?php if(empty($authors) || !empty($incompleteStatus['author'])):?>
                                    <span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>
                                <?php else: ?>
                                    <span class="float-end text-success"><i class="fw-bold fas fa-exclamation-circle"></i> Complete </span>
                                <?php endif?>
                            </a>
                            <a href="<?=base_url()?>user/presentation_upload/<?=$paper_id?>" class="btn btn-white btn-sm round-0 text-start mt-2 ps-0 fw-bold" style="width:100%; border-bottom:1px solid red">
                                <num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">3 </num> Paper or Presentation Upload
                                <?php if(!empty($incompleteStatus['paperUpload'])):?>
                                    <span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>
                                <?php else: ?>
                                    <span class="float-end text-success"><i class="fw-bold fas fa-exclamation-circle"></i> Complete </span>
                                <?php endif?>
                            </a>
                            <a href="<?=base_url()?>user/finalize_paper/<?=$paper_id?>" class="btn btn-white btn-sm round-0 text-start mt-2 ps-0 fw-bold previewFinalizeBtn" style="width:100%; border-bottom:1px solid red">
                                <num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">4 </num> Print/Preview/Finalize
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
    </div>
</main>
