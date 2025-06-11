
<script  type="text/javascript" src="<?=base_url('assets/js/submissionFunction.js?v=1')?>"></script>


<?php //echo'<pre>'; print_r($abstract_details);exit;?>
<?php echo view('event/common/menu'); ?>

<main>
    <div class="container">
        <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1">
                <div class="container">
                <div id="landing-page-contents" class="container-fluid p-4">
                    <h5  class="fw-bold"> Main Menu</h5>
                    <hr />
                    <div>
                        Please fill out the following pages. Once you have added your panel title and summary, you will be required to add the panel authors. Once your authors are added, you are required to preview and finalize your summary. Once finalized, emails are automatically sent to the authors so that they can access this panel submission. If you wish to add an author at a later date, you may log back in and do so.<br><br>
                        If you want to view the status of the panelists submissions, please click on "My Submissions" icon at the top of the page.
                    </div>
                    <div class="submission-menu mt-5" style="font-family: inherit;">

                        <a href="<?=base_url()?>user/panel_coordinators/<?=$paper_id?>" class="btn btn-white btn-sm round-0 text-start ps-0 fw-bold" style="width:80%; border-bottom:1px solid red">
                            <num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">1 </num> Panel Coordinator
                            <?php if(!empty($incompleteStatus['coordinators'])):?>
                            <span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>
                            <?php else: ?>
                                <span class="float-end text-success"><i class="fw-bold fas fa-exclamation-circle"></i> Complete </span>
                            <?php endif?>
                        </a>
                        <a href="<?=base_url()?>user/panel_overview/<?=$paper_id?>" class="btn btn-white btn-sm round-0 text-start mt-2 ps-0 fw-bold" style="width:80%; border-bottom:1px solid red">
                            <num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">2 </num> Panel Overview
                            <?php if(!empty($incompleteStatus['paper'])):?>
                                <span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>
                            <?php else: ?>
                                <span class="float-end text-success"><i class="fw-bold fas fa-exclamation-circle"></i> Complete </span>
                            <?php endif?>
                        </a>
                        <a href="<?=base_url()?>user/panelist/<?=$paper_id?>" class="btn btn-white btn-sm round-0 text-start mt-2 ps-0 fw-bold" style="width:80%; border-bottom:1px solid red">
                            <num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">3 </num> Panelist
                            <?php if(!empty($incompleteStatus['panelist'])):?>
                                <span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>
                            <?php else: ?>
                                <span class="float-end text-success"><i class="fw-bold fas fa-exclamation-circle"></i> Complete </span>
                            <?php endif?>
                        </a>
                        <a href="<?=base_url()?>user/finalize_panel/<?=$paper_id?>" class="btn btn-white btn-sm round-0 text-start mt-2 ps-0 fw-bold previewFinalizeBtn" style="width:80%; border-bottom:1px solid red">
                            <num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">5 </num> Preview
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
