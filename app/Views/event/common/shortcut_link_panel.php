<?php if(isset($paper_id) && !empty($paper_id)): ?>
    <div class="row">
        <div class="col-md-12" style="margin-left: 10px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url().'home' ?>">My Submissions</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url().'user/panel_submission_menu/'.$paper_id; ?>">Submission Menu</a></li>
                <li class="breadcrumb-item active"><?=$title?></li>
            </ol>
        </div>
    </div>
    <hr />
    <div>
        <h5 class="fw-bold"> <?=$title?></h5>
    </div>
    <hr />
<?php endif ?>