<?php echo view('acceptance/common/menu'); ?>
<body>
    <div class="container">
        <div aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?=base_url()?>/acceptance/abstract_list">My Activity</a></li>
                <li class="breadcrumb-item"><a href="javascript:location.reload()">Refresh</a></li>
            </ol>
        </div>

        <?=$presentation_data_view ?? ''?>
            <div class="card mt-2">
                <div class="card-header bg-primary text-white p-3">
                    Acceptance Menu
                </div>
                <div class="card-body">
                    <div class="col-md-12">
                        <div id="landing-page-contents" class="container-fluid p-4">
                            <div class="submission-menu" style="font-family: inherit;">
                                <?php $stepNumber = 1; ?>
                                <a id="" href="<?=base_url()?>/acceptance/moderator/acceptance/<?=$scheduler_id?>" class="btn btn-white btn-sm round-0 text-start ps-0 fw-bold" style="width:80%; border-bottom:1px solid red"><num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px"><?= $stepNumber++ ?> </num> <Moderator></Moderator> Acceptance
                                    <?=isset($moderator_acceptance) && (!empty($moderator_acceptance->acceptance_confirmation_date)|| $moderator_acceptance->acceptance_confirmation_date !== Null )? '<span class="float-end text-success"><i class="fw-bold  fas fa-check-circle"> </i> Completed </span>' :'<span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>' ?>
                                </a>
<!--                                <a id="" href="--><?php //=base_url()?><!--/acceptance/moderator/breakfast_attendance/--><?php //=$scheduler_id?><!--" class="btn btn-white btn-sm round-0 text-start ps-0 fw-bold" style="width:80%; border-bottom:1px solid red"><num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">1 </num> Breakfast Attendance-->
<!--                                    --><?php //=(!empty($moderator_acceptance)) && (!empty($moderator_acceptance->breakfast_attendance))? '<span class="float-end text-success"><i class="fw-bold  fas fa-check-circle"> </i> Completed </span>' :'<span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>' ?>
<!--                                </a>-->
<!--                                <a id="" href="--><?php //=base_url()?><!--/acceptance/moderator/session_details/--><?php //=$scheduler_id?><!--" class="btn btn-white btn-sm round-0 text-start ps-0 fw-bold" style="width:80%; border-bottom:1px solid red"><num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">1 </num> Session Details-->
<!--                                    --><?php //=isset($moderator_acceptance) && (!empty($moderator_acceptance->is_session_previewed))? '<span class="float-end text-success"><i class="fw-bold  fas fa-check-circle"> </i> Completed </span>' :'<span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>' ?>
<!--                                </a>-->
                                <a href="<?=base_url()?>/acceptance/moderator/finalize/<?=$scheduler_id?>" class="btn btn-white btn-sm round-0 text-start ps-0 fw-bold" style="width:80%; border-bottom:1px solid red"><num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px"><?= $stepNumber++ ?>  </num> Finalize
                                    <?=isset($moderator_acceptance) && (!empty($moderator_acceptance->is_finalized))? '<span class="float-end text-success"><i class="fw-bold  fas fa-check-circle"> </i> Completed </span>' :'<span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>' ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</body>
