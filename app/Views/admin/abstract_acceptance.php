    

<?php echo view('admin/common/menu'); ?>
<?php // echo '<pre>';  print_R($acceptanceRooms);?>
<main>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
            <h4> Acceptance for Abstract # <?=isset($abstract_id)? $abstract_id:'' ?></h4>    
            </div>
            <div class="card-body">
                <form id="adminAcceptanceForm">
                    <input type="hidden" name="abstract_id" value="<?=(isset($abstract_id))?$abstract_id:''?>">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <label>Accept/Reject: </label>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div>
                        <input type="radio" name="adminAcceptanceOption" id="acceptSession" value="accepted" <?=(isset($abstracts[0]->admin_acceptance_status) && $abstracts[0]->admin_acceptance_status == 'accepted')? "checked":''?>><label for="acceptSession"> Accepted</label>
                        </div><div>
                        <input type="radio" name="adminAcceptanceOption" id="declineSession" value="declined" <?=(isset($abstracts[0]->admin_acceptance_status) && $abstracts[0]->admin_acceptance_status == 'declined')? "checked":''?>><label for="declineSession"> Declined</label>
                        </div><div>
                        <input type="radio" name="adminAcceptanceOption" id="reservedSession" value="reserved" <?=(isset($abstracts[0]->admin_acceptance_status) && $abstracts[0]->admin_acceptance_status == 'reserved')? "checked":''?>><label for="reservedSession"> Reserved</label>
                        </div><div>
                        <input type="radio" name="adminAcceptanceOption" id="declineWithdrawSession" value="declined_withdraw" <?=(isset($abstracts[0]->admin_acceptance_status) && $abstracts[0]->admin_acceptance_status == 'declined_withdraw')? "checked":''?>><label for="declineWithdrawSession"> Declined/Withdraw</label>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-6 col-sm-12">
                        <label>Accepted Presentation Preference: </label>
                    </div>
                    <div class="col-md-6 col-sm-12">
                       <div>
                            <select id="" name="adminAcceptancePreference" class="required form-control">
                                <option value="" <?=(!isset($abstracts[0]->admin_acceptance_preference))? "selected":''?>> -- Select --</option>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                                <option value="1" <?=(isset($abstracts[0]->admin_acceptance_preference) && $abstracts[0]->admin_acceptance_preference == '1')? "selected":''?>>Podium Presentation</option>
                                <option value="2" <?=(isset($abstracts[0]->admin_acceptance_preference) && $abstracts[0]->admin_acceptance_preference == '2')? "selected":''?>>Poster Presentation</option>
                                <option value="3" <?=(isset($abstracts[0]->admin_acceptance_preference) && $abstracts[0]->admin_acceptance_preference == '3')? "selected":''?>>ePoster Presentation</option>
                                <option value="4" <?=(isset($abstracts[0]->admin_acceptance_preference) && $abstracts[0]->admin_acceptance_preference == '4')? "selected":''?>>Invited Speaker</option>
                            </select>
                       </div>
                    </div>
                </div>

                  <div class="row mt-4">
                    <div class="col-md-6 col-sm-12">
                        <label>Acceptance Date: </label>
                    </div>
                    <div class="col-md-6 col-sm-12">
                       <div class="">
                            <div class="row">
                                <div class="">
                                    <input type="date" name="presentationDate" class="form-control" placeholder="Session Speaking Date" value="<?=(isset($abstracts[0]->presentation_date) && $abstracts[0]->presentation_date !== null)?(date("Y-m-d", strtotime($abstracts[0]->presentation_date))):''?>"/>
                                </div>
                                <div  class="mt-2">
                                    <input type="time" name="presentationStartTime" id="timepicker2" class="form-control" placeholder="Session Start Time" value="<?=(isset($abstracts[0]->presentation_start_time) && $abstracts[0]->presentation_start_time !== null)?(date("H:i", strtotime($abstracts[0]->presentation_start_time))):''?>"/>
                                </div>
                                 <div class="mt-2">
                                    <input type="time" name="presentationEndTime" id="timepicker2" class="form-control" placeholder="Session End Time" value="<?=(isset($abstracts[0]->presentation_end_time) && $abstracts[0]->presentation_end_time !== null)?(date("H:i", strtotime($abstracts[0]->presentation_end_time))):''?>"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6 col-sm-12">
                        <label>Acceptance Room: </label>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="">
                               <select id="" name="presentationRoom" class="required form-control">
                                <option value="" <?=(!isset($abstracts[0]->presentation_room_id))? "selected":''?>> -- Select --</option>
                                <?php if (isset($acceptanceRooms)): 
                                    foreach ($acceptanceRooms as $rooms):
                                    ?>
                                <option value="<?=$rooms['room_id']?>" <?= (isset($abstracts[0]->presentation_room_id) ? (($abstracts[0]->presentation_room_id) == $rooms['room_id']) ? "selected" : "" : "") ?> > <?= $rooms['name'] ?></option>                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
                                <?php endforeach; 
                                    endif;
                                    ?>
                            </select>
                        </div>
                    </div>
                </div>

                 <div class="row mt-4">
                    <div class="col-md-6 col-sm-12">
                        <label>Administration Comments: </label>
                    </div>
                    <div class="col-md-6 col-sm-12">
                       <div>
                          <textarea name="adminAcceptanceComments" class="form-control" rows="5"><?=(isset($abstracts[0]->admin_acceptance_comments) && $abstracts[0]->admin_acceptance_comments !== '')? $abstracts[0]->admin_acceptance_comments:''?></textarea>
                       </div>
                    </div>
                </div>
                <button class="btn btn-success" id="submitAcceptanceForm" abstract_id="<?=$abstract_id?>">Submit</button>
                </form>
            </div>
        </div>
    </div>
</main>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    let baseUrlAdmin = "<?=base_url().'/'.$event->uri.'/admin/'?>";

    $(function(){
        $("#submitAcceptanceForm").on('click', function(e){ 
            e.preventDefault();
            let formData = new FormData(document.getElementById('adminAcceptanceForm'));
        $.ajax({
            url: baseUrlAdmin+"save_admin_acceptance",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                response = JSON.parse(response);
                // Request successful, do something with the response
                console.log(response);
                if(response.status == "success"){
                    swal.fire({
                        'icon':'success',
                        'text': response.msg,
                        'title':'Success'
                    });
                }else if(response.status == "noChanges"){
                    swal.fire({
                        'icon':'info',
                        'text': response.msg,
                        'title':'Info'
                    })
                }
            },
            error: function(xhr, status, error) {
                // Request failed
                console.log('Request failed. Error: ' + error);
            }
            });
            })


          flatpickr("#timepicker1, #timepicker2", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true
        });
    })
</script>