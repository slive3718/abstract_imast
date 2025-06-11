<link href="<?=base_url()?>/assets/css/event/register.css" rel="stylesheet">

<?php echo view('event/common/menu'); ?>

<main>
    <div class="container-fluid">


        <div class="form-signin m-auto text-center">
            <form id="registrationForm" action="<?=base_url()?>/register" method="POST">
                <h4 class="mb-3 fw-normal">Register</h4>

                <div class="row ">
                    <div class="col-md-6 pe-1 mb-2">
                        <div class="form-floating">
                            <input type="text" name="name" class="form-control text-center" id="floatingFirstname" placeholder="John" autocomplete="given-name" required>
                            <label for="floatingFirstname">Firstname <small class="text-danger">*</small></label>
                        </div>
                    </div>
                    <div class="col-md-6 pe-1 mb-2">
                        <div class="form-floating">
                            <input type="text" name="surname" class="form-control text-center" id="floatingLastname" placeholder="Doe" autocomplete="family-name" required>
                            <label for="floatingLastname">Lastname <small class="text-danger">*</small></label>
                        </div>
                    </div>
                </div>

                <div class="row ">
                    <div class="col-md-6 pe-1 mb-2">
                        <div class="form-floating">
                            <input type="email" name="email" class="form-control text-center" id="email" placeholder="name@example.com" autocomplete="username" required>
                            <label for="email">Email address <small class="text-danger">*</small></label>
                        </div>
                    </div>
                    <div class="col-md-6 pe-1 mb-2">
                        <div class="form-floating">
                            <input type="email" name="confirmEmail" class="form-control text-center" id="confirmEmail" placeholder="name@example.com" autocomplete="" required>
                            <label for="confirmEmail">Confirm Email address <small class="text-danger">*</small></label>
                        </div>
                    </div>
                    <div class="col-md-6 pe-1 mb-2">
                        <div class="form-floating">
                            <input type="password" name="password" class="form-control text-center" id="password" placeholder="Password" autocomplete="new-password" required>
                            <label for="password">Password <small class="text-danger">*</small></label>
                        </div>
                    </div>
                    <div class="col-md-6 pe-1 mb-2">
                        <div class="form-floating">
                            <input type="password" name="password" class="form-control text-center" id="confirmPassword" placeholder="Password" autocomplete="new-password" required>
                            <label for="confirmPassword">Confirm Password <small class="text-danger">*</small></label>
                        </div>
                    </div>
                </div>

                <button class="w-100 btn btn-lg btn-primary registerBtn" type="submit">Register <i class="fa-solid fa-paper-plane"></i></button>
            </form>
        </div>

        <div class="row text-center">
            <div class="col-md-12 mt-2">
                <span>Already a member? <a href="<?=base_url()?>/login">Sign in</a></span>
            </div>
        </div>

    </div>
</main>

<script>
    $(function(){

        $('#registrationForm').submit(function(event) {
            if($('#password').val() !== $('#confirmPassword').val()){
                swal.fire(
                    'info',
                    'Password did not match',
                    'info'
                )
                return false;
            }
            if($('#email').val() !== $('#confirmEmail').val()){
                swal.fire(
                    'info',
                    'Email address did not match',
                    'info'
                )
                return false;
            }
            event.preventDefault(); // Prevent the form from submitting via the browser
            var form = $(this);
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                success:function(response){
                    response = JSON.parse(response);
                    if(response.status == 200){
                        Swal.fire({
                            title: 'Account created',
                            confirmButtonText: 'Ok, continue',
                            icon: 'success'
                        }).then((result) => {
                            /* Read more about isConfirmed, isDenied below */
                           window.location.href="<?=base_url()?>/home";
                        })
                    }else{
                        swal.fire(
                            'info',
                            'Email already exists',
                            'info'
                        )
                    }
                }
            }).done(function(response) {

            }).fail(function(response) {
                // console.log(response)
                swal.fire(
                    'error',
                    'something went wrong',
                    'error'
                )
            });
        });
    })
</script>