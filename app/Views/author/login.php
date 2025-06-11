<style>
    .form-signin {
        max-width: 330px;
        padding: 15px;
    }

    .form-signin .form-floating:focus-within {
        z-index: 2;
    }

    .form-signin input[type="email"] {
        margin-bottom: -1px;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }

    .form-signin input[type="password"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
</style>
<main>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 p-0">
                <img id="main-banner" style="width:100%" src="<?=base_url().'main_banner.png'?>" class="img-fluid figure-img" alt="Main Banner"/>
            </div>
            <hr />
        </div>

        <div class="text-center">
            <h4 class="mb-3 fw-normal">Author Disclosures</h4>
        </div>
        <div class="form-signin w-100 m-auto text-center">
            <form id="formLogin" action="<?=base_url()?>/login/validateLogin" method="post" >

                <h4 class="mb-3 fw-normal">Please sign in</h4>

                <div class="form-floating">
                    <input type="email" class="form-control text-center" id="floatingInput" placeholder="name@example.com" autocomplete="username" required>
                    <label for="floatingInput">Email address <small class="text-danger">*</small></label>
                </div>
<!--                <div class="form-floating">-->
<!--                    <input type="password" class="form-control text-center" id="floatingPassword" placeholder="Password" autocomplete="current-password" required>-->
<!--                    <label for="floatingPassword">Password <small class="text-danger">*</small></label>-->
<!--                </div>-->
                <input type="submit" class="SignInBtn mt-4 btn btn-primary" value="Login">
            </form>
        </div>

    </div>
</main>

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
                    'login_type': "author"

                }, function(response){

                    if(response['status'] == "200"){
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
                                window.location.href="<?=base_url()?>/author/view_copyright";
                            }
                        })
                    }else if( response['status'] === 401){
                        Swal.fire(
                            '',
                            response['message'],
                            'error'
                        )
                    }else if(response['status'] === 400){
                        Swal.fire(
                            '',
                            response['message'],
                            'warning'
                        )
                    }else{
                        Swal.fire(
                            '',
                            response['message'],
                            'warning'
                        )
                    }
                },'json')
        })
    })
</script>