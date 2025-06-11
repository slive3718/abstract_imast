<link href="<?=base_url()?>/assets/css/event/login.css" rel="stylesheet">

<?php echo view('event/common/menu'); ?>

<main>
    <div class="container-fluid">

    
        <div class="form-signin w-100 m-auto text-center">
            <form id="formLogin" action="<?=base_url()?>/login/validateLogin" method="post" >
                <h4 class="mb-3 fw-normal">Please sign in</h4>

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
            <div class="col-md-12 mt-2">
                <span>New Submitter? <a href="<?=base_url()?>/account">Register</a></span>
            </div>
        </div>

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

        $('.SignInBtn').on('click', function(e){
            e.preventDefault();
            let email = $('#floatingInput').val();
            let password = $('#floatingPassword').val();
            $.post(base_url+'/login/validateLogin',
                {
                    'email': email,
                    'password': password,
                    'event_uri': 'afs',
                    'login_type': "user"

                }, function(response){

                console.log(response)
                    if(response['token'] !== ''){
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
                            /* Read more about handling dismissals below */
                            if (result.dismiss === Swal.DismissReason.timer) {
                                window.location.href="<?=base_url()?>/home";
                            }
                        })
                    }else{
                        Swal.fire(
                            '',
                            "Invalid Username or Password",
                            'warning'
                        )
                    }
                },'json')
        })

       // ############### forgot password ##################

        $('.forgotPasswordBtn').on('click', function(){
            $('#forgotPasswordModal').modal('show')
        })

        $('.submitForgotPWBtn').on('click', function(){

             Swal.showLoading()
            $.post(base_url+"/account/reset_password",
                {
                    'email': $('#resetPasswordEmail').val()
                },
                function(response){
                    console.log(response);
                    if(response.status == '200'){
                        swal.fire(
                            'success', 'Password sent to email', 'success'
                        )
                        $('#forgotPasswordModal').modal('hide')
                    }
                    if(response.status == 400){
                        swal.fire(
                            'info', 'Email not registered', 'info'
                        )
                    }
                     if(response.status == 200 && response.data.message == 'user not found'){
                        swal.fire(
                            'info', 'Email not found', 'info'
                        )
                    }
                }, 'json')
        })

    })
</script>