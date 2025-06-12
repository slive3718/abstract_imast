<link href="<?=base_url()?>/assets/css/event/menu.css" rel="stylesheet">

<nav class="navbar navbar-expand-md fixed-top navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
            </ul>

            <ul class="navbar-nav mb-2 mb-md-0">

                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">
                        <button type="button" class="btn btn-outline-light">LANGUAGE <i class="fa-solid fa-language"></i></button>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="<?= base_url()."/user/support"?>">
                        <button type="button" class="btn btn-outline-light">SUPPORT <i class="fa-solid fa-headset"></i></button>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>


<div class="row mt-5">
    <div class="col-md-12 text-center mt-md-4" style="width: 60% !important; margin:auto">
        <img id="main-banner" src="<?=$event->main_banner?>" class=" figure-img" alt="Main Banner" style="width: 100% !important;object-fit: cover; mix-blend-mode: multiply;" />
    </div>
    <hr />
</div>