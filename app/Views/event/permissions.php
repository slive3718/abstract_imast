
<link href="<?=base_url()?>/assets/css/jquery_ui_style.css" rel="stylesheet">



<?php echo view('event/common/menu'); ?>
<?php  //print_r($abstract_details->permission_1);exit;?>
<main>
    <div class="container-fluid">

       <?php echo view('event/common/shortcut_link'); ?>

        <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1">
            <h6 class="fw-bold"> Permissions <span class="show_error1 text-danger" hidden> * Required</span></h6>
            <hr style="height: 5px; color:red" class="m-0">
            <div>
        
            <form id="permissionsForm" method="post">
            <div class="mt-5">
                <div class="row mt-2">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="permission_digital_recording" id="permission_digital_recording" value="yes" <?=(isset($abstract_details->permission_1) && $abstract_details->permission_1) == 'yes' ? 'checked':''?>>
                        <lable class="form-check-label" for="permission"><required class="text-danger">*</required>
                        I agree to allow the audio and digital recording of my presentation in connection with my participation in the PRISM
                        Annual Meeting. | consent to the recording and use of my audio presentation and PowerPoint presentation slides by
                        PRISM as part of the enduring online educational material. My execution of this Permission Form does not transfer my
                        ownership rights of the presentation materials and does not prohibit me from using materials in the future. | agree to
                        obtain permission from the patient in the case that | use photos, video, or other identifiable patient information in my
                        presentation.
                        </label>
                    </div>
            
                     <div class="form-control my-2">
                        <label><required class="text-danger">*</required>Electronic Signature</label> 
                        <input type="text" class="form-control" name="signature_1" id="signature_1" value="<?=(isset($abstract_details->signature_1) && !empty($abstract_details->signature_1))?$abstract_details->signature_1:''?>">
                    </div>
        
                     <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" name="permission_allow_prism" id="permission_allow_prism" value="yes" <?=(isset($abstract_details->permission_2) && $abstract_details->permission_2) == 'yes' ? 'checked':''?>>
                        <lable class="form-check-label" for="permission"><required class="text-danger">*</required>
                       I agree to allow PRISM, in partnership with the Orthopaedic Journal of Sports Medicine, to publish my written abstract,
                        along with all supporting image, tables and files, in an open access, electronic supplement following the PRISM Annual
                        Meeting.
                        </label>
                    </div>
                    <div class="form-control mb-5">
                        <label><required class="text-danger">*</required>Electronic Signature</label> 
                        <input type="text" class="form-control" name="signature_2" id="signature_2" value="<?=(isset($abstract_details->signature_2) && !empty($abstract_details->signature_2))?$abstract_details->signature_2:'' ?>" >
                    </div>
            
                </div>
            </div>
            <input type="submit" class="btn btn-success" value="Save and continue">
            </form>
        </div>



    </div>
</main>

<script>
    $(function(){
        $('#permissionsForm').on('submit', function(e){
            e.preventDefault();
           
            // console.log($('#objective_1').val().trim() == ''); return false;

            // let abstract_details = <?=isset($abstract_details)?json_encode($abstract_details):''?>;
    
            // let frmData= $('#permissionsForm').serializeArray();
            //     frmData.push({'name':"abstract_id", 'value':<?=$abstract_id?>});
            
            if($('#permission_digital_recording').prop('checked')==false){
                Swal.fire({ 
                            icon: 'info', 
                            title: '', 
                            text: 'Please check all required fields.'
                        })
                return false;
            }
            else if($('#permission_allow_prism').prop('checked')==false){
                Swal.fire({ 
                            icon: 'info', 
                            title: '', 
                            text: 'Please check all required fields.'
                        })
                return false;
            }
            else if($('#signature_1').val() == ''){
                  Swal.fire({ 
                            icon: 'info', 
                            title: '', 
                            text: 'Please check all required fields.'
                        })
                return false;
            }
             else if($('#signature_2').val() == ''){
                  Swal.fire({ 
                            icon: 'info', 
                            title: '', 
                            text: 'Please check all required fields.'
                        })
                return false;
            }else{
                let frmData= $('#permissionsForm').serializeArray();
                frmData.push({'name':"abstract_id", 'value':<?=$abstract_id?>});
                $.ajax({
                    url: base_url+'/'+event_uri+'/user/update_abstract_permission',
                    headers: {'X-Requested-With': 'XMLHttpRequest'},
                    data: frmData,
                    method: "POST",
                    dataType: "json",
                    success:
                        function(response){
                        if(response.status == 200){
                            Swal.fire(
                                { icon: 'success', title: 'Changes saved', text: 'success'}
                            ).then(function(){
                                window.location.href = base_url+'/'+event_uri+'/user/submission_menu/'+abstract_id;
                            });
                        }else{
                            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong: ' + e.message, });
                        }

                    }
                })
            }
        })

     
    })

</script>