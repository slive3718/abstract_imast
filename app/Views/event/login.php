<link href="<?=base_url()?>/assets/css/event/login.css" rel="stylesheet">

<?php echo view('event/common/menu'); ?>

<main style="margin: unset; padding-bottom:200px">
    <div class="container-fluid">
        <div class="m-auto text-center mb-2 fw-bolder text-primary">
            LOGIN
        </div>
        <div class="text-center m-auto shadow-sm" style="width: 600px">
            <div class="card">
                <div class="card-header text-primary fw-bold"> NOTE FOR SUBMITTERS </div>
                <div class="card-body text-start">
                    <p>
                        If you have previously submitted an abstract to a SRS Annual Meeting, please use those credentials to submit for IMAST 2026.
                    </p>
                    <p>
                        If you have previously been an author on an abstract for a SRS Annual Meeting, please
                        use that email address and password: SRS. You can change your password once you are
                        logged in under 'Settings'.
                    </p>
                </div>
            </div>
        </div>

        <?php if(1==1) :?>
        <div class="form-signin w-100 m-auto text-center">

            <div class="text-start my-3" style="width: 600px">
                <span class="h5"> New Submitter? </span>
                <br><a href="<?=base_url()?>/account"> Click here</a>
                 to create new account
            </div>
            <div class="text-start mb-5" style="width: 600px">
                <span class="h5"> Returning to the site ? </span>
                <br>Enter your email and password and click on 'Login'
            </div>


            <form id="formLogin" action="<?=base_url()?>/login/validateLogin" method="post" >
                <div class="form-floating">
                    <input type="email" class="form-control text-center" id="floatingInput" placeholder="name@example.com" autocomplete="username" required>
                    <label for="floatingInput">Email address <small class="text-danger">*</small></label>
                </div>
                <div class="form-floating">
                    <input type="password" class="form-control text-center" id="floatingPassword" placeholder="Password " autocomplete="current-password" required>
                    <label for="floatingPassword">Password <small class="text-danger"> (Password is case sensitive) * </small></label>
                </div>
                <input type="submit" class="SignInBtn btn btn-primary" value="Login ">

                <div class="col-md-12 mt-3 text-start">
                    <span class="h5">Forgot your password?</span> <br>
                    <span> <a href="#" class="forgotPasswordBtn mt">Click here </a> to reset your password.</span>
                </div>
            </form>
        </div>

        <?php else :?>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-danger text-center" role="alert">
                     The submission site is now closed. Thank you for your interest!
                </div>
            </div>
        </div>
        <?php endif ?>

    </div>
</main>

<?= view('common/forgot_password_modal'); ?>
<script src="<?=base_url('assets/js/forgotPassword.js') ?>"></script>
<script>
    $(function(){

        $('.SignInBtn').on('click', function(e) {
            e.preventDefault();
            let email = $('#floatingInput').val();
            let password = $('#floatingPassword').val();

            $.post(base_url + 'login/validateLogin', {
                'email': email,
                'password': password,
                'login_type': "user"
            }, function(response) {
                if (response.status == "200") {
                    let timerInterval
                    Swal.fire({
                        title: 'Login Success',
                        html: 'Redirecting to homepage...',
                        timer: 1000,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading()
                        },
                        willClose: () => {
                            clearInterval(timerInterval)
                        }
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.timer) {
                            window.location.href = "<?=base_url()?>/home";
                        }
                    })
                } else {
                    Swal.fire(
                        '',
                        response.message || "Invalid Username or Password",
                        'warning'
                    )
                }
            }, 'json').fail(function(xhr, status, error) {
                // Handle AJAX errors
                let errorMessage = "An error occurred during login. Please try again.";

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.statusText) {
                    errorMessage = xhr.statusText;
                }

                Swal.fire(
                    'Error',
                    errorMessage,
                    'error'
                );

                // Optional: Log the error for debugging
                console.error("Login Error:", status, error, xhr.responseText);
            });
        });

    })
</script>