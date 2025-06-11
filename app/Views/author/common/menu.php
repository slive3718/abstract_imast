<link href="<?=base_url()?>/assets/css/event/menu.css" rel="stylesheet">

<nav class="navbar navbar-expand-md fixed-top navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav me-auto mb-2 mb-md-0 float-end text-end">
                <li class="nav-item">
                </li>
            </ul>

            <a class="nav-link active" aria-current="page" href="#">
                <button type="button" onclick="openSupport()" class="btn btn-outline-light">SUPPORT <i class="fa-solid fa-headset"></i></button>
            </a>

            <a class="nav-link active ms-5" aria-current="page" href="<?=base_url().'author/logout'?>">
                <button type="button" class="btn btn-outline-light">Log Out <i class="fas fa-sign-out-alt"></i></button>
            </a>
        </div>
    </div>
</nav>


<div class="row mt-5">
    <div class="col-md-12 text-center mt-md-4" style="width: 60% !important; margin:auto">
        <img id="main-banner" src="<?=base_url().'main_banner.png'?>" class=" figure-img" alt="Main Banner" style="width: 100% !important;object-fit: cover; mix-blend-mode: multiply;" />
    </div>
    <hr />
</div>

<!-- User Info Modal -->
<div class="modal fade" id="supportemail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Support Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">

                        <div class="form-floating mt-2">
                            <input type="text" class="form-control" id="fnameInput" placeholder="First name" autocomplete="name" >
                            <label for="floatingInput">First Name<small class="text-danger">*</small></label>
                        </div>
                        <div class="form-floating mt-2">
                            <input type="text" class="form-control" id="lnameInput" placeholder="Last Name" autocomplete="middle" >
                            <label for="floatingInput">Last Name<small class="text-danger">*</small></label>
                        </div>
                        <div class="form-floating mt-2">
                            <input type="text" class="form-control" id="semailInput" placeholder="Email" autocomplete="middle" >
                            <label for="floatingInput">Email<small class="text-danger">*</small></label>
                        </div>
                        <div class="form-floating mt-2">
                            <textarea class="form-control" style="height: 104px!important;" placeholder="Support Request" rows="5" id="support_messageInput"></textarea>
                            <label for="floatingInput">Support Request<small class="text-danger">*</small></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="sendSupport()">Send Email</button>
            </div>
        </div>
    </div>
</div>


<script src="<?=base_url().'assets/js/author/support.js'?>"></script>