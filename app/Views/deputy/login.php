<link href="<?=base_url()?>/assets/css/event/login.css" rel="stylesheet">

<?php echo view('deputy/common/menu'); ?>

<main>
    <div class="container-fluid">

        <!-- <div class="row">
            <div class="col-md-12">
                <div class="alert alert-danger text-center" role="alert">
                    Review is now closed
                </div>
            </div>
        </div> -->
    <?php if (1==1):?>
        <div class="form-signin w-100 m-auto text-center">
            <form id="formLogin" action="<?=base_url()?>/login/validateLogin" method="post" >
                <h4 class="mb-3 fw-normal">Reviewer Login</h4>

                <div class="form-floating">
                    <input type="email" class="form-control text-center" id="floatingInput" placeholder="name@example.com" autocomplete="username" required>
                    <label for="floatingInput">Email address <small class="text-danger">*</small></label>
                </div>
                <div class="form-floating">
                    <input type="password" class="form-control text-center" id="floatingPassword" placeholder="Password" autocomplete="current-password" required>
                    <label for="floatingPassword">Password <small class="text-danger">*</small></label>
                </div>
                <input type="submit" class="SignInBtn">
            </form>
        </div>

        <div class="row text-center">
            <div class="col-md-12">
                <span><a href="#" class="forgotPasswordBtn">Forgot password?</a></span>
            </div>
        </div>

    </div>
    <?php endif ?>
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

        $('.SignInBtn').on('click', function(e){
            e.preventDefault();
            let email = $('#floatingInput').val();
            let password = $('#floatingPassword').val();
            $.post(base_url+'/deputy/authenticate',
                {
                    'email': email,
                    'password': password,
                    'event_uri': "afs",
                    'login_type': "reviewer"

                }, function(response){
                    // console.log(response.data.data.credentials.is_super_admin)
                    // console.log(response.status);return false;
                    
                   if(response.status == '200'){
                    console.log('ghere')
                    let timerInterval
                            Swal.fire({
                                title: 'Login Success',
                                html: 'Redirecting to Program Chair page...',
                                timer: 1000,
                                timerProgressBar: true,
                                didOpen: () => {
                                    Swal.showLoading()
                                },
                                willClose: () => {
                                    clearInterval(timerInterval)
                                }
                            }).then((result) => {
                                /* Read more about handling dismissals below */
                                if (result.dismiss === Swal.DismissReason.timer) {
                                     window.location.href="<?=base_url()?>/deputy/menu";
                                }
                            })
                   }else if(response.status == '403'){
                         swal.fire({
                            title: 'Password incorrect',
                            text: response.msg,
                            icon: 'error'
                         })
                   }else if(response.status == '500'){
                       swal.fire({
                           title: 'Password incorrect',
                           text: response.msg,
                           icon: 'error'
                       })
                   }
                },'json')
        })

       // ############### forgot password ##################

        $('.forgotPasswordBtn').on('click', function(){
            $('#forgotPasswordModal').modal('show')
        })

        $('.submitForgotPWBtn').on('click', function() {
            $.ajax({
                url: base_url + "/account/reset_password",  // URL to send the request
                type: 'POST',  // Method type
                data: {
                    'email': $('#resetPasswordEmail').val(),  // Data to be sent
                    'from': 'deputy_reviewer'
                },
                dataType: 'json',  // Expect JSON response
                beforeSend: function () {  // No comma before this
                    Swal.showLoading();  // Show loading animation
                },
                success: function (response) {
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
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html:  error
                    });
                }
            });
        });

    })
</script>