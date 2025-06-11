
<link href="<?=base_url()?>/assets/css/jquery_ui_style.css" rel="stylesheet">



<?php echo view('event/common/menu'); ?>
<?php //print_r($abstract_details); exit;?>
<main>
    <div class="container-fluid">

 <?php echo view('event/common/shortcut_link'); ?>


        <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1">
            <h6 class="fw-bold"> Unlabeled and Unapproved Uses  <span class="show_error1 text-danger" hidden> * Required</span></h6>
            <hr style="height: 5px; color:red" class="m-0">
            <p>Presentations that provide information in whole or in part related to non FDA approved uses of drugs and/or devices must clearly disclose the unlabeled indications or the investigational nature of their proposed uses to the audience. Please document in the abstract FDA disclosure.</p>
            <p>* In my "work" for this educational program or publication:</p>

            <table>
                <tbody>
                    <tr>
                        <td><input type="radio" name="unapproved_publication_radio" class="unapproved_publication_radio" value="0" <?=(isset($abstract_details) && $abstract_details->fda_unapproved_uses !== null)? ($abstract_details->fda_unapproved_uses == 0)? "checked":'':'' ?> ></td>
                        <td>I do not plan to discuss non-FDA approved products or non-FDA approved use of any products. </td>
                    </tr>
                    <tr>
                        <td><input type="radio" name="unapproved_publication_radio" class="unapproved_publication_radio" value="1" <?=(isset($abstract_details) && $abstract_details->fda_unapproved_uses !== null)? ($abstract_details->fda_unapproved_uses == 1)? "checked":'':'' ?>></td>
                        <td>I do not plan to discuss non-FDA approved products or non-FDA approved use of any products. </td>
                    </tr>
                </tbody>
            </table>

            <h6 class="fw-bold mt-5">Use of Product name  <span class="show_error2 text-danger" hidden> * Required</span></h6>
            <hr style="height: 5px; color:red" class="m-0">
            <p>Presentations which utilize product names will receive additional scrutiny during the CME review process and presenters may be asked to remove the product name at the discretion of the CME Committee.</p>

            <table>
                <tbody>
                <tr>
                    <td><input type="radio" name="discuss_product_name_radio" class="discuss_product_name_radio" value="0" required <?=($abstract_details->discuss_product_name !== null)? ($abstract_details->discuss_product_name == 0)? "checked":'':'' ?>></td>
                    <td>I plan to discuss a commercial product by name in my presentation.</td>
                </tr>
                <tr>
                    <td><input type="radio" name="discuss_product_name_radio" class="discuss_product_name_radio" value="1" required <?=($abstract_details->discuss_product_name !== null)? ($abstract_details->discuss_product_name == 1)? "checked":'':'' ?>></td>
                    <td>I do not plan to discuss a commercial product by name in my presentation. </td>
                </tr>
                </tbody>
            </table>

            <hr style="height: 5px; color:red" class="m-0 mt-5">
            <div class="mt-3">
                <input type="checkbox" name="accept_fda_disclosure" class="acceptFdaBtn" id="acceptFdaBtn" required <?=($abstract_details->is_fda_accepted !== null)? ($abstract_details->is_fda_accepted == 1)? "checked":'':'' ?>>
                <label for="acceptFdaBtn"> I accept the terms of participation in this CME activity as noted in the author disclosure statement. <span class="show_error3 text-danger" hidden> * Required</span></label>
                <br>
                <button class="btn btn-success saveFdaBtn mt-3" id="saveFdaBtn"> Save and Continue </button>
            </div>

        </div>


    </div>
</main>

<script>
    $(function(){
        $('#saveFdaBtn').on('click', function(){
            // console.log('rd'+$('.acceptFdaBtn').is(":checked") == false);
            if($('.unapproved_publication_radio').is(":checked") == false){
                toastr.warning('Please fill up all required fields.')
                $('.show_error1').attr('hidden', false);
                return false;
            }
            else if($('.discuss_product_name_radio').is(":checked") == false){
                toastr.warning('Please fill up all required fields.')
                $('.show_error2').attr('hidden', false);
                $('.show_error1').attr('hidden', true);
                return false;
            }
            else if($('.acceptFdaBtn').is(":checked") == false){
                toastr.warning('Please fill up all required fields.')
                $('.show_error3').attr('hidden', false);
                $('.show_error2').attr('hidden', true);
                return false;
            }
            else{
                let unapproved_publication = $('.unapproved_publication_radio:checked').val();
                let discuss_product_name = $('.discuss_product_name_radio:checked').val();
                $.ajax({
                    url: base_url+'/'+event_uri+'/fda_disclosure/save_fda_disclosure',
                    data: {
                        "abstract_id": abstract_id,
                        "unapproved_publication":unapproved_publication,
                        "discuss_product_name":discuss_product_name
                    },
                    type:"POST",
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