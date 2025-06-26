<?php if(isset($paper_id) && !empty($paper_id)): ?>
    <div class="card">
        <div class="card-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url().'home' ?>">My Submissions</a></li>

                <li class="breadcrumb-item"><a href="<?= base_url().'user/submission_menu/'.$paper_id; ?>">Submission Menu</a></li>
                <li class="breadcrumb-item active"><?=$title?></li>
            </ol>
        </div>
        <div class="card-body">
            <h5 class="fw-bold"> <?=$title?></h5>
        </div>
    </div>
<?php else : ?>
    <div class="card">
        <div class="card-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url().'home' ?>">My Submissions</a></li>
                <li class="breadcrumb-item active"><?=$title?></li>
            </ol>
        </div>
        <div class="card-body">
            <h5 class="fw-bold"> <?=$title?></h5>
        </div>
    </div>
<?php endif ?>