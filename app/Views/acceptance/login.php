<link href="<?=base_url()?>/assets/css/event/login.css" rel="stylesheet">

<?php echo view('acceptance/common/menu'); ?>

<main>
    <div class="container-fluid">

        <!-- <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success text-center" role="alert">
                    Acceptance Login
                </div>
            </div>
        </div> -->
    <?php if (1==1):?>
        <div class="form-signin w-100 m-auto text-center">
            <form id="formLogin" action="<?=base_url()?>/login/validateLogin" method="post" >
                <h4 class="mb-3 fw-normal">Acceptance Login</h4>

                <div class="form-floating">
                    <input type="email" class="form-control text-center" id="floatingInput" placeholder="name@example.com" autocomplete="username" required>
                    <label for="floatingInput">Email address <small class="text-danger">*</small></label>
                </div>
                <input type="submit" class="SignInBtn btn btn-success mt-3" value="Login">
            </form>
        </div>
    </div>
    <?php endif ?>
</main>

<script>
    $(function(){

        $('.SignInBtn').on('click', function (e) {
            e.preventDefault(); // Prevent form submission

            let email = $('#floatingInput').val(); // Get the email input value

            // Validate the email field
            if (!email) {
                Swal.fire({
                    title: 'Error',
                    text: 'Please enter your email address.',
                    icon: 'error'
                });
                return;
            }

            // Send the POST request
            $.post(base_url + '/acceptance/authenticate', {
                'email': email,
                'login_type': "acceptance"
            }, function (response) {
                // Check for a successful response
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'Login Success',
                        html: 'Redirecting to acceptance page...',
                        timer: 1000,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        willClose: () => {
                            // Clear timer interval if necessary
                        }
                    }).then((result) => {
                        // Handle redirection after the timer
                        if (result.dismiss === Swal.DismissReason.timer) {
                            window.location.href = base_url + '/acceptance/abstract_list';
                        }
                    });
                } else {
                    // Show error message from the response
                    Swal.fire({
                        title: 'Error',
                        text: response.msg || 'An unexpected error occurred.',
                        icon: 'error'
                    });
                }
            }, 'json').fail(function (jqXHR, textStatus, errorThrown) {
                // Handle server or network errors
                Swal.fire({
                    title: 'Error',
                    text: jqXHR.responseJSON ? jqXHR.responseJSON.msg : 'Failed to authenticate. Please try again later.',
                    icon: 'error'
                });
            });
        });

        // ############### forgot password ##################


    })
</script>