<link href="<?=base_url()?>/assets/css/event/menu.css" rel="stylesheet">
<style>
    .dropdown-item:hover {
        background-color: #1a877c !important; /* Darker shade for better contrast */
        color: #ffffff !important; /* Ensure text is white and readable */
    }
</style>
<nav class="navbar navbar-expand-md fixed-top navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
<!--                <li class="nav-item">-->
<!--                    <a class="nav-link active" aria-current="page" href="#">Home</a>-->
<!--                </li>-->
<!--                <li class="nav-item">-->
<!--                    <a class="nav-link" href="#">Link</a>-->
<!--                </li>-->
<!--                <li class="nav-item">-->
<!--                    <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>-->
<!--                </li>-->
            </ul>

            <ul class="navbar-nav mb-2 mb-md-0">
                <?php if((session('user_id'))):?>
                <li class="nav-item">
                    <ul class="nav-link active" aria-current="page"">
                        <a type="button" class="btn btn-outline-light"  href="<?=base_url()?>/user/papers_submission/">Add New Submission <i class="fa-solid fa-file-lines"></i></a>
                        <a type="button" class="btn btn-outline-light" href="<?=base_url()?>/home" >My Submissions <i class="fa-solid fa-briefcase"></i></a>
                    </ul>
                </li>
                <?php endif;?>

                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">
                        <button type="button" id="opensupport" class="btn btn-outline-light">SUPPORT <i class="fa-solid fa-headset"></i></button>
                    </a>
                </li>

                <li class="nav-item mt-2 me-5">
                    <div class="dropdown ">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Submitter Instructions <i class="fa-solid fa-document"></i></button>
                        <ul class="btn btn-outline-light dropdown-menu dropdown-menu-end mt-3 text-white" aria-labelledby="dropdownMenuButton1" style="background-color:#2aa69c">
                            <li><a class="dropdown-item text-white" href="<?=base_url()?>submission_guidelines" target="_blank">Submitter Guidelines</a></li>
                            <li><a class="dropdown-item text-white" href="<?=base_url()?>/public/assets/documents/submission_references/Abstract_Submission_Directions.pdf" target="_blank">Abstract Directions</a></li>
                            <?php if(session('user_id')): ?>

<!--                            <li><a class="dropdown-item text-white" href="--><?php //=base_url().'public/assets/documents/submission_references/2025_Authors_Template.docx'?><!--" target="_blank">AFS Author Template</a></li>-->
<!--                            <li><a class="dropdown-item text-white" href="--><?php //=base_url().'public/assets/documents/submission_references/2025_Authors_Sample_Paper.docx'?><!--" target="_blank">AFS Sample Paper</a></li>-->
<!--                            <li><a class="dropdown-item text-white" href="--><?php //=base_url().'public/assets/documents/submission_references/2025_Authors_Style_Palette.docx'?><!--" target="_blank">AFS Author Style Palette</a></li>-->
<!--                            <li><a class="dropdown-item text-white" href="--><?php //=base_url().'public/assets/documents/submission_references/2025_Author-Speaker_Copyright_Publication_Agreement.pdf'?><!--" target="_blank">AFS Author/ Speaker Copyright & Publication Agreement</a></li>-->
<!--                            <li><a class="dropdown-item text-white" href="--><?php //=base_url().'public/assets/documents/submission_references/2025_Author_Copyright_Permission_Form_for_Rightsholders.pdf'?><!--" target="_blank">AFS Author Copyright Permission Form for Rightsholder</a></li>-->
<!--                            <li><a class="dropdown-item text-white" href="--><?php //=base_url().'public/assets/documents/submission_references/AFS_Policy_on_Authorship_Criteria_&_Use_of_AI_Technology.pdf'?><!--" target="_blank">AFS Policy of Authorship Criteria & Use of AI Technology</a></li>-->
<!--                            <li><a class="dropdown-item text-white" href="--><?php //=base_url().'public/assets/documents/submission_references/Commercialism_Guidelines.pdf'?><!--" target="_blank">Commercialism Guidelines</a></li>-->
<!--                            <li><a class="dropdown-item text-white" href="--><?php //=base_url().'public/assets/documents/submission_references/CastExpo_2025_PPT_Template.pptx'?><!--"target="_blank">PowerPoint Template</a></li>-->
<!--                            <li><a class="dropdown-item text-white" href="--><?php //=base_url().'public/assets/documents/submission_references/-SAMPLE-_Author_s_Response_to_Reviewers.pdf'?><!--" target="_blank">Sample - Authors Response to Reviewers</a></li>-->
                            <?php endif ?>
                        </ul>
                    </div>
                </li>


                <?php if(session('user_id')): ?>
                <li class="nav-item mt-2 me-5">
                    <div class="dropdown ">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            SETTINGS <i class="fa-solid fa-cog"></i></button>
                        <ul class="btn btn-outline-light dropdown-menu dropdown-menu-end mt-3 text-white" aria-labelledby="dropdownMenuButton1" style="background-color:#2aa69c">
                            <li><button class="dropdown-item text-white" id="userInfoBtn" href="#">My Information</button></li>
                            <li><button class="dropdown-item text-white" id="changePasswordBtn" href="#">Password Setting</button></li>
                            <li><button class="dropdown-item text-white" id="logoutBtn" href="#">Logout</button></li>
                        </ul>
                    </div>
                </li>
                <?php endif ?>
            </ul>
        </div>
    </div>
</nav>
<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <input type="password" name="current_password" id="current_password" class="form-control" placeholder="Current Password" aria-label="CurrentPassword" aria-describedby="basic-addon1">
                    <span onclick="showPassword('current_password', 'showCurrentPw')" class="input-group-text btn btn-primary text-white showCurrentPw" id="basic-addon1"><i class="fa fa-eye"></i></span>
<!--                    <span onclick="hidePassword('current_password', 'hideCurrentPw')" class="input-group-text btn btn-primary text-white hideCurrentPw" id="basic-addon1" hidden><i class="fa fa-slash"></i></span>-->
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="new_password" id="new_password" class="form-control" placeholder="New Password" aria-label="New Password" aria-describedby="basic-addon1">
                    <span onclick="showPassword('new_password', 'showNewPw')" class="input-group-text btn btn-primary text-white showNewPw" id="basic-addon1"><i class="fa fa-eye"></i></span>
<!--                    <span onclick="hidePassword('new_password', 'hideNewPw')" class="input-group-text btn btn-primary text-white hideNewPw" id="basic-addon1" hidden><i class="fa fa-slash"></i></span>-->
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="confirm_new_password" id="confirm_new_password" class="form-control" placeholder="Confirm Password" aria-label="Confirm Password" aria-describedby="basic-addon1">
                    <span onclick="showPassword('confirm_new_password', 'showConfirmPw')" class="input-group-text btn btn-primary text-white showConfirmPw" id="basic-addon1"><i class="fa fa-eye"></i></span>
<!--                    <span onclick="hidePassword('confirm_change_password', 'hideConfirmPw')" class="input-group-text btn btn-primary text-white hideConfirmPw " id="basic-addon1" hidden><i class="fa fa-slash"></i></span>-->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary updateMyPasswordBtn">Save changes</button>
            </div>
        </div>
    </div>
</div>


<!-- User Info Modal -->
<div class="modal fade" id="userInfoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">My Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 col-lg-6">

                        <div class="form-floating mt-2">
                            <input type="text" class="form-control text-center" id="nameInput" placeholder="Given name" autocomplete="name" >
                            <label for="floatingInput">Given Name<small class="text-danger">*</small></label>
                        </div>
                        <div class="form-floating mt-2">
                            <input type="text" class="form-control text-center" id="middleNameInput" placeholder="Middle Name" autocomplete="middle" >
                            <label for="floatingInput">Middle Name<small class="text-danger">*</small></label>
                        </div>
                        <div class="form-floating mt-2">
                            <input type="text" class="form-control text-center" id="surnameInput" placeholder="Family Name" autocomplete="surname" >
                            <label for="floatingPassword">Family Name <small class="text-danger">*</small></label>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-6">
                        <div class="form-floating mt-2">
                            <input type="email" class="form-control text-center" id="emailInput" placeholder="name@example.com" autocomplete="email" disabled>
                            <label for="floatingInput">Email<small class="text-danger">*</small></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary updateUserInfoBtn">Save changes</button>
            </div>
        </div>
    </div>
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
                            <input type="text" class="form-control" id="abstractIDInput" placeholder="abstract_id" autocomplete="middle" >
                            <label for="floatingInput">Abstract ID</label>
                        </div>
                        <div class="form-floating mt-2">
                             <textarea class="form-control" style="height: 104px!important;" placeholder="Support Request" rows="5" id="support_messageInput"></textarea>
                             <label for="floatingInput">Support Request<small class="text-danger">*</small></label>
                        </div>
                        <!-- <div class="form-floating mt-2">
                         
                          <select class="form-control" id="abstract_uploads">
                             <option value="">Select Abstract</option> 
                            <?php  
                         
                      //  foreach ($abstract_ids as $key => $value) 
                    //    {
                           ?>   
                      
                       //    <?php 


                     //   } ?>
                          </select>
                          <label for="floatingInput">Select Abstract<small class="text-danger">*</small></label>
                        </div> -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary sendsupport">Send Email</button>
            </div>
        </div>
    </div>
</div>




<div class="row mt-5">
    <div class="col-md-12 text-center mt-md-4" style="width: 80% !important; margin:auto">
        <img id="main-banner" src="<?=base_url()?>main_banner.png" class=" figure-img" alt="Main Banner" style="width: 100% !important;object-fit:" />
    </div>
    <hr />
</div>
<script>
    $(function(){
        let user = `<?=session('user_id')?>`

        $('#changePasswordBtn').on('click', function(){
            $('#changePasswordModal').modal('show')
            $('#current_password').val('');
            $('#new_password').val('');
            $('#confirm_new_password').val('');
        })
        $('#userInfoBtn').on('click', function(){
            $('#userInfoModal').modal('show')
            fillUserInfo();
        })

        $('#opensupport').on('click', function(){
            $('#supportemail').modal('show')
            // if(user){
            //
            // }
            //
            // fillUserInfo();
        })

        $('.updateMyPasswordBtn').on('click', function(){

            let newPw =  $('#new_password').val();

            if (newPw !== $('#confirm_new_password').val()){
                toastr.warning('New Password did not match');
                return false;
            }
            $.ajax({
                url:base_url+'/account/update_password',
                data: {
                    'current_password': $('#current_password').val(),
                    'new_password': newPw
                },
                method: "POST",
                dataType: "json",
                success: function(response){
                    console.log(response);
                    if(response){
                        if(response.status == 200){
                            swal.fire(
                                'success',
                                'Password updated successfully',
                                'success'
                            )
                            $('#changePasswordModal').modal('hide')
                        }else{
                            swal.fire(
                                'error',
                                response.reason,
                                'error'
                            )
                        }
                    }

                }
            })
        })

        $('.updateUserInfoBtn').on('click', function(){
            let name = $('#nameInput').val()
            let surname = $('#surnameInput').val()
            let middle_name = $('#middleNameInput').val()

            if(name == ''){
                toastr.warning('Given Name cannot be empty')
                return false;
            }

            if(surname == ''){
                toastr.warning('Family Name cannot be empty')
                return false;
            }
            $.ajax({
                url:base_url+'/user/update_user_info',
                method: "POST",
                dataType: "json",
                data:{
                  'name': name,
                  'surname': surname,
                  'middle_name': middle_name,
                },
                success: function(response){
                    console.log(response);
                    if(response.reason == "update success"){
                        swal.fire(
                            'success',
                            'Account updated',
                            'success'
                        )
                        $('#userInfoModal').modal('hide')
                    }else if(response.reason == "no changes"){
                       toastr.info('no changes')
                    }else{
                        swal.fire(
                            'error',
                            'Something went wrong updating account',
                            'error'
                        )
                    }
                }
            })
        })

        $('.sendsupport').on('click', function(){
            let fname = $('#fnameInput').val()
            let lname = $('#lnameInput').val()
            let email = $('#semailInput').val()
            let abstract_id = $('#abstractIDInput').val()
            let message = $('#support_messageInput').val()

            if(fname == ''){
                toastr.warning('First Name cannot be empty')
                return false;
            }

            if(lname == ''){
                toastr.warning('Last Name cannot be empty')
                return false;
            }

            if(email == ''){
                toastr.warning('Email cannot be empty')
                return false;
            }

            if(message == ''){
                toastr.warning('Message Name cannot be empty')
                return false;
            }
            $.ajax({
                url:base_url+'/user/send_support_mail',
                method: "POST",
                dataType: "json",
                data:{
                  'abstract_id':abstract_id,
                  'fname': fname,
                  'lname': lname,
                  'email': email,
                  'message':message
                },
                success: function(response){
                    console.log(response);
                    if(response.status == 200){
                        swal.fire(
                            'success',
                            'Support message successfully sent',
                            'success'
                        )
                        $('#supportemail').modal('hide')
                    }else{
                        swal.fire(
                            'error',
                            'Something went wrong',
                            'error'
                        )
                    }
                }
            })
        })

        $('#logoutBtn').on('click', function(e){

            e.preventDefault();
            window.location.href = base_url+'/logout';
        })
    })
    function showPassword(input_id, span_class){
        if($('#'+input_id).attr('type')=='text'){
            $('#'+input_id).attr('type', 'password')
        }else{
            $('#'+input_id).attr('type', 'text')
        }
    }

    function fillUserInfo(){
        $('#nameInput').val('')
        $('#middleNameInput').val()
        $('#surnameInput').val()
        $.ajax({
            url:base_url+'/user/get_user_info',
            method: "POST",
            dataType: "json",
            success: function(response){
                if(response.data){
                    $('#nameInput').val(response.data.name)
                    $('#middleNameInput').val(response.data.middle_name)
                    $('#surnameInput').val(response.data.surname)
                    $('#emailInput').val(response.data.email)
                }
            }
        })
    }
</script>