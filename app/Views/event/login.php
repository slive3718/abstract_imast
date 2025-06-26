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

<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Please type your registered email address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-floating">
                    <input type="email" class="form-control text-center" id="resetPasswordEmail" placeholder="name@example.com" autocomplete="email" required>
                    <label for="resetPasswordEmail">Email address <small class="text-danger">*</small></label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary submitForgotPWBtn">Submit</button>
            </div>
        </div>
    </div>
</div>
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

        // ############### forgot password ##################

        $('.forgotPasswordBtn').on('click', function(){
            $('#forgotPasswordModal').modal('show')
        })

        $('.submitForgotPWBtn').on('click', function() {

            Swal.showLoading();  // Show loading animation

            $.ajax({
                url: base_url + "/account/reset_password",  // URL to send the request
                type: 'POST',  // Method type
                data: {
                    'email': $('#resetPasswordEmail').val()  // Data to be sent
                },
                dataType: 'json',  // Expect JSON response
                beforeSend: function () {  // No comma before this
                    Swal.showLoading();  // Show loading animation
                },
                success: function(response) {
                    console.log(response);

                    // Check if the response status is 200 (success)
                    if (response.status == '200') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Password reset instructions sent to your email.'
                        });
                        $('#forgotPasswordModal').modal('hide');  // Hide modal on success
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'There was an issue. Please try again.'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr, status, error)
                    // Handle failed request (e.g., network error, server error)
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: error + '<br>Please register here: <a href="'+base_url+'account"> Register Link</a>'
                    });
                    // console.log('Error:', error);  // Log the error for debugging
                }
            });
        });

    })
</script>