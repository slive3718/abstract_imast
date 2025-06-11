<link href="<?=base_url()?>/assets/css/event/register.css" rel="stylesheet">

<?php echo view('event/common/menu'); ?>

<main>
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12 p-0">
                <img id="main-banner" src="<?=$event->main_banner?>" class="img-fluid figure-img" alt="Main Banner"/>
            </div>
            <hr />
        </div>


        <div class="form-signin m-auto text-center">
            <form>
                <h4 class="mb-3 fw-normal">Register</h4>

                <div class="row mb-2">
                    <div class="col-md-6 pe-1">
                        <div class="form-floating">
                            <input type="text" class="form-control text-center" id="floatingFirstname" placeholder="John" autocomplete="given-name" required>
                            <label for="floatingFirstname">Firstname <small class="text-danger">*</small></label>
                        </div>
                    </div>
                    <div class="col-md-6 ps-1">
                        <div class="form-floating">
                            <input type="text" class="form-control text-center" id="floatingLastname" placeholder="Doe" autocomplete="family-name" required>
                            <label for="floatingLastname">Lastname <small class="text-danger">*</small></label>
                        </div>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-6 pe-1">
                        <div class="form-floating">
                            <input type="email" class="form-control text-center" id="floatingEmail" placeholder="name@example.com" autocomplete="username" required>
                            <label for="floatingEmail">Email address <small class="text-danger">*</small></label>
                        </div>
                    </div>
                    <div class="col-md-6 ps-1">
                        <div class="form-floating">
                            <input type="password" class="form-control text-center" id="floatingPassword" placeholder="Password" autocomplete="new-password" required>
                            <label for="floatingPassword">Password <small class="text-danger">*</small></label>
                        </div>
                    </div>
                </div>

                <button class="w-100 btn btn-lg btn-primary" type="submit">Register <i class="fa-solid fa-paper-plane"></i></button>
            </form>
        </div>

        <div class="row text-center">
            <div class="col-md-12 mt-2">
                <span>Already a member? <a href="<?=base_url()?>/login">Sign in</a></span>
            </div>
        </div>

    </div>
</main>