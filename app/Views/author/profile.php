<link href="<?=base_url()?>/assets/css/event/login.css" rel="stylesheet">

<?php echo view('author/common/menu'); ?>

<main>
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12 p-0 text-center">
<!--                <img id="main-banner" src="--><?//=$event->main_banner?><!--" class="img-fluid figure-img" alt="Main Banner" style="height:200px"/>-->
            </div>
            <hr />
        </div>


        <div class="w-100 m-auto text-left">
            <form>
                <div class="card " style="">
                    <div class="card-header">
                       Profile Information
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <h4 class=" fw-bold">Profile Information</h4>
                            <hr class="">
                            <div class="col-lg-6 col-md-12">
                                <div class="form-floating mt-2">
                                    <input type="text" class="form-control text-center" id="floatingFirstname" placeholder="John" autocomplete="given-name" value="<?=($user_details)?$user_details->name:''?>" required>
                                    <label for="floatingFirstname">Firstname <small class="text-danger">*</small></label>
                                </div>
                                <div class="form-floating mt-2">
                                    <input type="text" class="form-control text-center" id="floatingFirstname" placeholder="John" autocomplete="given-name" value="<?=($user_details)?$user_details->middle_name:''?>" required>
                                    <label for="floatingFirstname">Middlename <small class="text-danger">*</small></label>
                                </div>
                                <div class="form-floating mt-2">
                                    <input type="text" class="form-control text-center" id="floatingFirstname" placeholder="John" autocomplete="given-name" value="<?=($user_details)?$user_details->surname:''?>" required>
                                    <label for="floatingFirstname">Lastname <small class="text-danger">*</small></label>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-12 ">
                                <div class="form-floating mt-2">
                                    <input type="text" class="form-control text-center shadow-none" id="floatingFirstname" placeholder="John" autocomplete="given-name" value="<?=($user_details)?$user_details->email:''?>" required readonly>
                                    <label for="floatingFirstname">Email <small class="text-danger">*</small></label>
                                </div>
                            </div>


                            <h4 class="mt-4 fw-bold">Institution </h4>
                            <hr class="">
                            <div class="col-lg-6 col-md-12 ">

                                <div class="form-floating mt-2">
                                    <input type="text" class="form-control text-center shadow-none" id="floatingFirstname" placeholder="John" autocomplete="given-name" value="<?=($user_details)?$user_details->email:''?>" required readonly>
                                    <label for="floatingFirstname">Email <small class="text-danger">*</small></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button class="btn btn-danger">Cancel</button>
                        <button class="btn btn-success">Save Changes</button>
                    </div>

                </div>
            </form>
        </div>

    </div>
</main>

<script>
    $(function(){

    })
</script>