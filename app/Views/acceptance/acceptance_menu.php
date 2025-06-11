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
                                <a id="speakerAcceptance" href="<?=base_url()?>/acceptance/speaker_acceptance/<?=$abstract_id?>" class="btn btn-white btn-sm round-0 text-start ps-0 fw-bold" style="width:80%; border-bottom:1px solid red"><num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">1 </num> Speaker Acceptance
                                    <?=isset($author_acceptance) && (!empty($author_acceptance->acceptance_confirmation_date)|| $author_acceptance->acceptance_confirmation_date !== Null )? '<span class="float-end text-success"><i class="fw-bold  fas fa-check-circle"> </i> Completed </span>' :'<span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>' ?>
                                </a>
                                <a id="curriculumVitaeUpload" href="<?=base_url()?>/acceptance/breakfast_attendance/<?=$abstract_id?>" class="btn btn-white btn-sm round-0 text-start ps-0 fw-bold" style="width:80%; border-bottom:1px solid red"><num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">1 </num> Breakfast Attendance
                                    <?=isset($author_acceptance) && (!empty($author_acceptance->breakfast_attendace)|| $author_acceptance->breakfast_attendance !== '' )? '<span class="float-end text-success"><i class="fw-bold  fas fa-check-circle"> </i> Completed </span>' :'<span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>' ?>
                                </a>
                                <a id="acceptance_disclosure" href="<?=base_url()?>/acceptance/biography/<?=$abstract_id?>" class="btn btn-white btn-sm round-0 text-start ps-0 fw-bold" style="width:80%; border-bottom:1px solid red"><num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">1 </num> Biography
                                    <?=isset($author_acceptance) && (!empty($author_acceptance->author_bio)|| $author_acceptance->author_bio !== '' )? '<span class="float-end text-success"><i class="fw-bold  fas fa-check-circle"> </i> Completed </span>' :'<span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>' ?>
                                </a>
                                <a id="presentationUpload" href="<?=base_url()?>/acceptance/presentation_upload/<?=$abstract_id?>" class="btn btn-white btn-sm round-0 text-start ps-0 fw-bold" style="width:80%; border-bottom:1px solid red"><num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">1 </num> Presentation Uploads
                                    <?=isset($author_acceptance) && (!empty($author_acceptance->presentation_original_name)|| $author_acceptance->presentation_original_name !== '' )? '<span class="float-end text-success"><i class="fw-bold  fas fa-check-circle"> </i> Completed </span>' :'<span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>' ?>
                                </a>
                                <a href="<?=base_url()?>/acceptance/speaker_acceptance_finalize/<?=$abstract_id?>" class="btn btn-white btn-sm round-0 text-start ps-0 fw-bold" style="width:80%; border-bottom:1px solid red"><num class="btn-sm me-2 text-white " style="background-color:#FF6600; padding:5px 10px 5px 10px">1 </num> Print/Preview/Finalize
                                    <?=isset($author_acceptance) && (!empty($author_acceptance->is_finalized)|| $author_acceptance->is_finalized == 1 )? '<span class="float-end text-success"><i class="fw-bold  fas fa-check-circle"> </i> Completed </span>' :'<span class="float-end text-danger"><i class="fw-bold fas fa-exclamation-circle"></i> Incomplete </span>' ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</body>
