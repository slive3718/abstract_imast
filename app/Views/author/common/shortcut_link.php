<?php if(session('user_id')): ?>
    <div class="card">
        <div class="card-header">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url().'author/view_copyright'; ?>">Disclosure Menu</a></li>
                <li class="breadcrumb-item active"> <a href="javascript:void(0)" onclick="location.reload();"><?=$title?></a></li>
            </ol>
            <h5 class="fw-bold"> <?=$title?></h5>
        </div>

    </div>
<?php endif ?>