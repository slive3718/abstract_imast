<div class="card">
    <div class="card-header">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"  onclick="window.history.go(-1);">Abstract List/Search</a></li>
            <li class="breadcrumb-item"><a href="#"  onclick="window.history.go(-0);">Detail</a></li>
        </ol>
    </div>
    <div class="card-body">
        <h5 class="fw-bold"> <?=$title?> #<?= $paper->id ?? '' ?>: <?= strip_tags($paper->title ?? '') ?></h5>
    </div>
</div>
