
<link href="<?=base_url()?>/assets/css/jquery_ui_style.css" rel="stylesheet">



<?php echo view('event/common/menu'); ?>
<?php //print_r($abstract_id); exit;?>
<main>
    <div class="container-fluid">

        <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1">
            <h5 class="fw-bold"> Authors </h5>
            <hr style="height: 5px; color:red" class="m-0">
            <div class="disclosureContent">
                <!--        This will be filled from database  -->
                <?=$event->disclosure_content?>
            </div>

            <div class="row mt-5">
                <div class="col-lg-5 col-md-12 col-sm-12">
                    <div class="mb-5 justify-content-end">
                        <button class="btn btn-primary btn-sm showSearchAuthorModal" style="min-width:100px; width:200px; max-width:200px; height:38px">Search for Name</button>
                    </div>
                </div>


            </div>


            <div class="">
                <div class="card" style="min-height:120px">
                    <div class="card-header " style="background-color:#FFCC99">
                        Author List
                    </div>
                    <div class="card-body ">
                        <table class="table table-resonsive">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Author Name</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <!-- <th>Status</th>
                                    <th>Email Status</th>
                                    <th>Action</th> -->
                                     <th></th>
                                </tr>
                            </thead>
                            <tbody class="authorList">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-8">
                    <div class="presentingAuthorSubInfo">
                     <p> Is <span class="presentingAuthorName"></span> a Student ?</p>
                    <input type="radio" class="form-check-input" name="isPresentingStudent" id="isPresentingStudentYes" value="1" <?=(isset($abstract_details) && $abstract_details->isPresentingStudent == '1')?'checked':'' ?>> <label><span class="text-danger">*</span>Yes</label>
                    <input type="radio" class="form-check-input ms-5" name="isPresentingStudent" id="isPresentingStudentNo" value="0" <?=(isset($abstract_details) && $abstract_details->isPresentingStudent == '0')?'checked':'' ?>> <label><span class="text-danger">*</span>No</label>
                    <br><br>
                    <p>
                        PRISM Annual Meeting Trainee Travel Grant seeks applicants who are PRISM student trainee members or are applicants for membership
                        to apply for this Travel Grant. You will need to be a member or a member applicant before you are able to access the award application. 
                        Click here  to view the <a href="<?=base_url()?>public/assets/documents/2024_Travel_Grant_Instructions.pdf" target="_blank"> Travel Grant Instructions </a>. 
                    </p>
                    <div id="eligibilityDiv"><br>
                    <p> Is <span class="presentingAuthorName"></span></span> eligible for Trainee Travel Grant ?</p>
                    <input type="radio" class="form-check-input" name="isEligibleGrant" id="isEligibleGrantYes" value="1" <?=(isset($abstract_details) && $abstract_details->isEligibleGrant == '1')?'checked':'' ?>> <label><span class="text-danger">*</span>Yes</label>
                    <input type="radio" class="form-check-input ms-5" name="isEligibleGrant" id="isEligibleGrantNo" value="0" <?=(isset($abstract_details) && $abstract_details->isEligibleGrant == '0')?'checked':'' ?>> <label><span class="text-danger">*</span>No</label>
                    
                    <br>
                    </div>
                    </div>
                    <!-- <input type="checkbox" class="form-check-input" name="markAgreeToRegister" id="markAgreeToRegister" <?=(isset($abstract_details) && $abstract_details->agree_to_register == 1)?'checked':'' ?>> <label><span class="text-danger">*</span> All presenters agree to register for the 2023 Public Health in the Rockies Conference if accepted to present.</label> -->
                    <br>
                    <div id="grantAddedInfoDiv">
                        <form id="grantAddedInfo" method="post" action="" enctype="multipart/form-data">
                            <div>
                                <p>PRISM Annual Meeting Trainee Grant Application for <span class="presentingAuthorName"></span></p>
                                <p><span class="text-danger">*</span><strong>Letter of Intent</strong> (max. 400 words)</p>
                                <p>Briefly explain your need for a travel grant and how you would benefit from receiving a travel grant</p>
                                <textarea  class="form-control required" name="letter_of_intent" title="Letter of intent" rows="10"><?=(isset($abstract_details) && $abstract_details->letter_of_intent !== '')?$abstract_details->letter_of_intent:'' ?></textarea>
                                <div class="letterOfIntentWordCount"></div>
                            </div>
                            <div>
                                <p><span class="text-danger">*</span><strong>Explanation of contribution (max. 200 words)</strong></p>
                                <p>Briefly explain how your attendance or the presentation of your research abstract will specifically contribute
                                    to the research and education initiatives upheld by PRiSM at the annual meeting. </p>
                                <textarea  class="form-control required" name="explanation_of_contribution" title="Explanation of contribution" rows="5"><?=(isset($abstract_details) && $abstract_details->explanation_of_contribution !== '')?$abstract_details->explanation_of_contribution:'' ?></textarea>
                                <div class="contributionWordCount"></div>
                            </div>
                            <br>
                            <div>
                                <div class="form-group  form-control">
                                <p><span class="text-danger">*</span><strong>CV Upload</strong> (PDF and no more than 2 pages)</p>
                            
                                    <input type="file" class="input-group-inline" name="cv_upload">
                                    <button class="btn btn-primary cvUploadBtn input-group-inline " title="CV" abstract_id="<?=(isset($abstract_details)?$abstract_details->id:'')?>">Upload </button>
                                    
                                    <br>
                                    <div><strong>Current cv uploaded:</strong> <span id="cv_preview"><a href="<?=base_url()?>/uploads/cv/<?=((isset($abstract_details) && $abstract_details->cv_rand_name) ? $abstract_details->cv_rand_name :'')?>" target="_blank"><?=((isset($abstract_details) && $abstract_details->cv_name) ? $abstract_details->cv_name :'')?></a></span></div>
                                </div>
                               
                            </div>
                            <br>
                            <div>
                                <div class="form-group form-control">
                                    <div class="form-input-inline"><span class="text-danger">*</span><strong>Please indicate the degree or specialty that you are working toward:</strong></d>
                                    <input type="text" class="form-input-inline required" name="grant_specialty" title="Specialty" value="<?=(isset($abstract_details) && $abstract_details->specialty_working_toward !== '')?$abstract_details->specialty_working_toward:'' ?>" >
                                        <div>
                                            <br>
                                            <div class="form-group">
                                                <label class="text-danger form-input-inline">*</label><strong>Years in training at the current level:</strong>
                                                <select class="form-input-inline required" name="years_of_training" title="Years of training" >
                                                    <option value="">Select </option>
                                                    <option value="1" <?=(isset($abstract_details) && $abstract_details->years_of_training == '1')?'selected':'' ?>>1 year </option>
                                                    <option value="2" <?=(isset($abstract_details) && $abstract_details->years_of_training == '2')?'selected':'' ?>>2 years </option>
                                                    <option value="3" <?=(isset($abstract_details) && $abstract_details->years_of_training == '3')?'selected':'' ?>>3 years </option>
                                                    <option value="4" <?=(isset($abstract_details) && $abstract_details->years_of_training == '4')?'selected':'' ?>>4 years </option>
                                                    <option value="5" <?=(isset($abstract_details) && $abstract_details->years_of_training == '5')?'selected':'' ?>>5 years </option>
                                                    <option value="6" <?=(isset($abstract_details) && $abstract_details->years_of_training == '6')?'selected':'' ?>>6 years </option>
                                                    <option value="7" <?=(isset($abstract_details) && $abstract_details->years_of_training == '7')?'selected':'' ?>>7 years </option>
                                                    <option value="8" <?=(isset($abstract_details) && $abstract_details->years_of_training == '8')?'selected':'' ?>>8 years </option>
                                                    <option value="9" <?=(isset($abstract_details) && $abstract_details->years_of_training == '9')?'selected':'' ?>>9 years </option>
                                                    <option value="10" <?=(isset($abstract_details) && $abstract_details->years_of_training == '19')?'selected':'' ?>>10 years </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                                <br>
                        <p><span class="text-danger">*</span><strong>Type of Training (check all that apply)</strong></p>
                            <div class="row row-cols-2">
                                <div class="form-group">
                                    <input type="checkbox" name="type_of_training[]" id="Athletic" class="form-check-input" value="Athletic-Training"  <?=(isset($abstract_details) && in_array('Athletic-Training', explode(',', $abstract_details->type_of_training)) )?'checked':'' ?>>
                                    <label class="form-check-label" for="Athletic">Athletic Training</label>
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" name="type_of_training[]" id="Biomechanics" class="form-check-input" value="Biomechanics" <?=(isset($abstract_details) && in_array('Biomechanics', explode(',', $abstract_details->type_of_training)) )?'checked':'' ?>>
                                    <label class="form-check-label" for="Biomechanics">Biomechanics</label>
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" name="type_of_training[]" id="Kinesiology" class="form-check-input" value="Kinesiology" <?=(isset($abstract_details) && in_array('Kinesiology', explode(',', $abstract_details->type_of_training)) )?'checked':'' ?>>
                                    <label class="form-check-label" for="Kinesiology">Kinesiology</label>
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" name="type_of_training[]" id="Medicine-Surgery" class="form-check-input" value="Medicine-Surgery" <?=(isset($abstract_details) && in_array('Medicine-Surgery', explode(',', $abstract_details->type_of_training)) )?'checked':'' ?>>
                                    <label class="form-check-label" for="Medicine-Surgery">Medicine-Surgery</label>
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" name="type_of_training[]" id="Medicine-Primary" class="form-check-input" value="Medicine-Primary" <?=(isset($abstract_details) && in_array('Medicine-Primary', explode(',', $abstract_details->type_of_training)) )?'checked':'' ?>>
                                    <label class="form-check-label" for="Medicine-Primary">Medicine-Primary</label>
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" name="type_of_training[]" id="Medicine-Radiology" class="form-check-input" value="Medicine-Radiology" <?=(isset($abstract_details) && in_array('Medicine-Radiology', explode(',', $abstract_details->type_of_training)) )?'checked':'' ?>>
                                    <label class="form-check-label" for="Medicine-Radiology">Medicine-Radiology</label>
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" name="type_of_training[]" id="Medicine-Other" class="form-check-input" value="Medicine-Other" <?=(isset($abstract_details) && in_array('Medicine-Other', explode(',', $abstract_details->type_of_training)) )?'checked':'' ?>>
                                    <label class="form-check-label" for="Medicine-Other">Medicine-Other</label>
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" name="type_of_training[]" id="Nursing" class="form-check-input" value="Nursing" <?=(isset($abstract_details) && in_array('Nursing', explode(',', $abstract_details->type_of_training)) )?'checked':'' ?>>
                                    <label class="form-check-label" for="Nursing">Nursing</label>
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" name="type_of_training[]" id="Nutrition" class="form-check-input" value="Nutrition" <?=(isset($abstract_details) && in_array('Nutrition', explode(',', $abstract_details->type_of_training)) )?'checked':'' ?>>
                                    <label class="form-check-label" for="Nutrition">Nutrition</label>
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" name="type_of_training[]" id="Physical-Therapy" class="form-check-input" value="Physical-Therapy" <?=(isset($abstract_details) && in_array('Physical-Therapy', explode(',', $abstract_details->type_of_training)) )?'checked':'' ?>>
                                    <label class="form-check-label" for="Physical-Therapy">Physical Therapy</label>
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" name="type_of_training[]" id="Psychology"  class="form-check-input" value="Psychology" <?=(isset($abstract_details) && in_array('Psychology', explode(',', $abstract_details->type_of_training)) )?'checked':'' ?>>
                                    <label class="form-check-label" for="Psychology">Psychology</label>
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" name="type_of_training[]" id="Radiology" class="form-check-input"  value="Radiology" <?=(isset($abstract_details) && in_array('Radiology', explode(',', $abstract_details->type_of_training)) )?'checked':'' ?>>
                                    <label class="form-check-label" for="Radiology">Radiology</label>
                                </div>
                                <div class="form-group">
                                    <input type="checkbox" name="type_of_training[]" id="Research" class="form-check-input" value="Research" <?=(isset($abstract_details) && in_array('Research', explode(',', $abstract_details->type_of_training)) )?'checked':'' ?>>
                                    <label class="form-check-label" for="Research">Research</label>
                                </div>
                                <div class="form-group">
                                    
                                    <label class="form-check-label" for="Other">Other</label>
                                    <input type="text" name="other_training" id="Other" class="form-control-inline "  value="">
                                </div>
                            </div>
                    

                            <div>
                                <div class="form-group ">
                                    <div class="form-input-inline"><span class="text-danger">*</span>Grant City:</div>
                                    <input type="text" class="form-input-inline form-control required" name="grantCity" title="City" value="<?=(isset($abstract_details) &&  $abstract_details->grant_city !== '' )?  $abstract_details->grant_city :'' ?>">
                                </div>
                                <div class="form-group ">
                                    <div class="form-input-inline"><span class="text-danger">*</span>Grant State:</div>
                                    <input type="text" class="form-input-inline form-control required" name="grantState" title="State"  value="<?=(isset($abstract_details) &&  $abstract_details->grant_state !== '' )?  $abstract_details->grant_state :'' ?>">
                                </div>
                                <div class="form-group ">
                                    <div class="form-input-inline"><span class="text-danger">*</span>Grant Country:</div>
                                    <input type="text" class="form-input-inline form-control required" name="grantCountry" title="Country"  value="<?=(isset($abstract_details) &&  $abstract_details->grant_country !== '' )?  $abstract_details->grant_country :'' ?>">
                                </div>
                            </div>


                            <div class="form-group">
                                <label for="race">Race</label>
                                <select class="form-control" id="race" name="grant_race">
                                    <option value="">-- Select Race --</option>
                                    <option value="asian" <?=(isset($abstract_details) && $abstract_details->grant_race == 'asian')?'selected':'' ?>>Asian</option>
                                    <option value="black" <?=(isset($abstract_details) && $abstract_details->grant_race == 'black')?'selected':'' ?>>Black or African American</option>
                                    <option value="latino" <?=(isset($abstract_details) && $abstract_details->grant_race == 'latino')?'selected':'' ?>>Hispanic or Latino</option>
                                    <option value="white" <?=(isset($abstract_details) && $abstract_details->grant_race == 'white')?'selected':'' ?>>White</option>
                                    <option value="native" <?=(isset($abstract_details) && $abstract_details->grant_race == 'native')?'selected':'' ?>>Native American or Alaska Native</option>
                                    <option value="hawaiian" <?=(isset($abstract_details) && $abstract_details->grant_race == 'hawaiian')?'selected':'' ?>>Native Hawaiian or Pacific Islander</option>
                                    <option value="other" <?=(isset($abstract_details) && $abstract_details->grant_race == 'other')?'selected':'' ?>>Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select class="form-control" id="gender" name="grant_gender">
                                    <option value="">-- Select Gender --</option>
                                    <option value="male" <?=(isset($abstract_details) && $abstract_details->grant_gender == 'male')?'selected':'' ?>>Male</option>
                                    <option value="female" <?=(isset($abstract_details) && $abstract_details->grant_gender == 'female')?'selected':'' ?>>Female</option>
                                    <option value="nonbinary" <?=(isset($abstract_details) && $abstract_details->grant_gender == 'nonbinary')?'selected':'' ?>>Non-binary</option>
                                    <option value="other" <?=(isset($abstract_details) && $abstract_details->grant_gender == 'other')?'selected':'' ?>>Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="sexual-orientation">Sexual Orientation</label>
                                <select class="form-control" id="sexual-orientation" name="grant_sexual_orientation">
                                    <option value="">-- Select Sexual Orientation --</option>
                                    <option value="heterosexual" <?=(isset($abstract_details) && $abstract_details->grant_sexual_orientation == 'heterosexual')?'selected':'' ?>>Heterosexual</option>
                                    <option value="homosexual" <?=(isset($abstract_details) && $abstract_details->grant_sexual_orientation == 'homosexual')?'selected':'' ?>>Homosexual</option>
                                    <option value="bisexual" <?=(isset($abstract_details) && $abstract_details->grant_sexual_orientation == 'bisexual')?'selected':'' ?>>Bisexual</option>
                                    <option value="asexual" <?=(isset($abstract_details) && $abstract_details->grant_sexual_orientation == 'asexual')?'selected':'' ?>>Asexual</option>
                                    <option value="other" <?=(isset($abstract_details) && $abstract_details->grant_sexual_orientation == 'other')?'selected':'' ?>>Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="other-details">Other Relevant Applicant-Specific Details</label>
                                <textarea class="form-control" id="other-details" name="other-details" rows="3" title="Other relevant info" ><?=(isset($abstract_details) && $abstract_details->grant_other_relevant_details !== '')?$abstract_details->grant_other_relevant_details:'' ?></textarea>
                            </div>

                        </form>
                    </div>
                    <br><br><br><br>
                    <button class="btn btn-success" id="saveDisclosureBtn" style="max-width:200px"> Save and Continue</button>
                </div>
                <div class="col-4">
                    <fieldset class="card p-3">
                        <legend>My Recent Author</legend>
                        <p> Quick add author to Submissions </p>
                        <table class="table table-striped table-responsive table-bordered table-border-success table-responsive quickAddAuthorTable " >
                            <tbody class="">
                        <?php if(isset($disclosure_data) && !empty($disclosure_data)) :
                       
                            foreach($disclosure_data as $data):
                                // print_r($data);exit;
                                
                        ?>
                            <tr class="">
                                <td class="shadow-sm"><?=$data->author_name.' '.$data->author_surname?></td>
                                <td class="shadow-sm" style="width: 30px;"><a class="btn btn-success btn-sm quickAddAuthorBtn" href="" author_id="<?=$data->author_id?>"><i class="fa fa-plus"></i></a></td>
                            </tr>
                        <?php endforeach; endif;?>
                            </tbody>
                        </table>
                    </fieldset>
                </div>
            </div>
            <!--            #########################           -->
        </div>
    </div>
</main>


<!--  Search Author Modal -->
<div class="modal fade" id="searchAuthorModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Search an Author</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12 col-md-12  col-sm-12">
                                <p class="text-danger">Search author name</p>
                                <div class="input-group mb-3 ">
                                    <label class="input-group-text text-dark" style="background-color:lightgray" for="authorName">Search Name</label>
                                    <input type="text" name="authorName" id="authorName" class="form-control shadow-none">
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="mb-5">
                                        <button class="btn btn-success btn-sm searchAuthorBtn" style="min-width:100px; width:200px; max-width:200px; height:38px"><i class="fas fa-search"></i> Search Author </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Author Search Result Modal -->
<div class="modal fade" id="authorResultModal" tabindex="" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="background-color: lightgray">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Searched Result</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-3">
                    <div class="card-body">
                       <div class="table">
                           <table class="authorResultTable table table-striped table-bordered">
                               <thead>
                                   <tr>
                                       <th></th>
                                       <th>First Name</th>
                                       <th>Middle Name</th>
                                       <th>Last Name</th>
                                       <th>Institution</th>
                                   </tr>
                               </thead>
                               <tbody class="authorResultTableBody" id="authorResultTableBody">

                               </tbody>
                           </table>

                       </div>
                    </div>

                </div>
                <button class="btn btn-primary btn-sm addAuthorBtn float-left" style="min-width:100px; width:331px; max-width:400px; height:38px"><i class="fas fa-plus"></i> Add an author who is not found in database</button>

                <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success addMarkedAuthor">Add marked author</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!--  Add Author Modal -->
<div class="modal fade" id="addAuthorModal" tabindex="" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add an Author</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSaveAuthor" action="" method="post" enctype="multipart/form-data" role="form">
                <div class="card mb-3">
                    <div class="card-header">
                        Personal Information
                    </div>
                    <div class="card-body">

                    
                         <div class="input-group mb-2">
                            <label class="input-group-text text-white" style="background-color:#2AA69C" for="authorPronoun">Pronoun</label>
                            <select name="authorTitle" id="authorTitle" class="form-control">
                                 <option class="form-control" value="">>>>> Select Title <<<<<</option>
                                <option class="form-control" value="Mr.">Mr.</option>
                                <option class="form-control" value="Mrs.">Mrs.</option>
                                <option class="form-control" value="Ms.">Ms.</option>
                                <option class="form-control" value="Miss.">Miss.</option>3
                                <option class="form-control" value="Mx">Mx</option>
                                <option class="form-control" value="Dr.">Dr.</option>
                                <option class="form-control" value="Other">Other Title</option>
                            </select>
                        </div>

                        <div class="input-group mb-2">
                            <input type="hidden"  name="author_id"  class="form-control shadow-none" id="author_id" style="max-width:400px" placeholder="">
                        </div>
                        <div class="input-group mb-2">
                            <label class="input-group-text text-white" style="background-color:#2AA69C" for="authorFName">First Name<font color="red">* </font></label>
                            <input type="text" name="authorFName" title="First Name" class="form-control shadow-none required" id="authorFName" style="max-width:400px" placeholder="">
                        </div>

                        <div class="input-group mb-2">
                            <label class="input-group-text text-white" style="background-color:#2AA69C" for="authorMName">Middle Name</label>
                            <input type="text"  name="authorMName" title="Middle Name" class="form-control shadow-none" id="authorMName" style="max-width:400px" placeholder="">
                        </div>

                        <div class="input-group mb-2">
                            <label class="input-group-text text-white" style="background-color:#2AA69C" for="authorLName">Last Name<font color="red">* </font></label>
                            <input type="text"  name="authorLName" title="Last Name" class="form-control shadow-none required" id="authorLName" style="max-width:400px" placeholder="">
                        </div>


                        <div class="input-group mb-2">
                            <label class="input-group-text text-white" style="background-color:#2AA69C" for="authorDeg">Credentials/Degree</label>
                            <input type="text"  name="authorDeg" title="Degree" class="form-control shadow-none" id="authorDeg" style="max-width:400px" placeholder="">
                        </div>

                        <!-- <div class="input-group mb-2">
                            <label class="input-group-text text-white" style="background-color:#2AA69C" for="authorPosition">Position<font color="red">* </font></label>
                            <input type="text"  name="authorPosition" title="Position" class="form-control shadow-none required" id="authorPosition" style="max-width:400px" placeholder="">
                        </div> -->

                        <!-- <div class="input-group mb-2">
                            <label class="input-group-text text-white" style="background-color:#2AA69C" for="authorPronoun">Pronoun</label>
                            <select name="authorPronoun" id="authorPronoun" class="form-control">
                                 <option class="form-control" value="">>>>> Select Pronoun <<<<<</option>
                                <option class="form-control" value="He/Him/His">He/Him/His</option>
                                <option class="form-control" value="She/Her/Hers">She/Her/Hers</option>
                                <option class="form-control" value="They/Them/Theirs">They/Them/Theirs</option>
                                <option class="form-control" value="Ze/Hir/Hirs">Ze/Hir/Hirs</option>
                                <option class="form-control" value="Other">Other Pronouns</option>
                            </select>
                        </div> -->




                        <div class="input-group mb-2">
                            <label class="input-group-text text-white" style="background-color:#2AA69C" for="authorEmail">Primary Email<font color="red">* </font></label>
                            <input type="email"  name="authorEmail" title="Email"  class="form-control shadow-none required" id="authorEmail" style="max-width:400px" placeholder="">
                        </div>

                        <div class="input-group mb-2">
                            <label class="input-group-text text-white" style="background-color:#2AA69C" for="authorConfirmEmail">Retype Email<font color="red">* </font></label>
                            <input type="email"  name="authorConfirmEmail" title="Confirm Email" class="form-control shadow-none required" id="authorConfirmEmail" style="max-width:400px" placeholder="">
                        </div>

                        <!-- <div class="input-group mb-2">
                            <label class="input-group-text text-white" style="background-color:#2AA69C" for="authorPhone">Phone</label>
                            <input type="text"  name="authorPhone" title="Phone" class="form-control shadow-none " id="authorPhone" style="max-width:400px" placeholder="">
                        </div> -->

                        <!-- <div class="input-group mb-2">
                            <label class="input-group-text text-white" style="background-color:#2AA69C" for="authorStudentContact">If student, summer contact phone number</label>
                            <input type="text"  name="authorStudentContact" title="Student/Summer Contact" class="form-control shadow-none" id="authorStudentContact" style="max-width:400px" placeholder="">
                        </div> -->

                        <!-- <div class="input-group mb-2">
                            <label class="input-group-text text-white" style="background-color:#2AA69C" for="authorAddress">Address<font color="red">* </font></label>
                            <input type="text"  name="authorAddress" title="Address" class="form-control shadow-none required" id="authorAddress" style="max-width:400px" placeholder="">
                        </div>

                        <div class="input-group mb-2">
                            <label class="input-group-text text-white" style="background-color:#2AA69C" for="authorCity">City<font color="red">* </font></label>
                            <input type="text"  name="authorCity" title="City" class="form-control shadow-none required" id="authorCity" style="max-width:400px" placeholder="">
                        </div>

                         <div class="input-group mb-2">
                            <label class="input-group-text text-white" style="background-color:#2AA69C" for="authorCountry">Country<font color="red">* </font></label>
                            <input type="text"  name="authorCountry" title="Country" class="form-control shadow-none required" id="authorCountry" style="max-width:400px" placeholder="">
                        </div>

                         <div class="input-group mb-2">
                            <label class="input-group-text text-white" style="background-color:#2AA69C" for="authorProvince">Province/State<font color="red">* </font></label>
                            <input type="text"  name="authorProvince" title="Province" class="form-control shadow-none required" id="authorProvince" style="max-width:400px" placeholder="">
                        </div>

                        <div class="input-group mb-2">
                            <label class="input-group-text text-white" style="background-color:#2AA69C" for="authorZipcode">Postal Code/Zip Code<font color="red">* </font></label>
                            <input type="text"  name="authorZipcode" title="Postal Code" class="form-control shadow-none required" id="authorZipcode" style="max-width:400px" placeholder="">
                        </div> -->

                        <!-- <div class=" mb-2">
                            <label class="input-group-text text-white" style="background-color:#2AA69C" for="addressType">Is the above address home or institution?<font color="red">* </font></label>
                            <ul style="list-style:none">
                                <li> <input type="radio"  name="addressType"  value="institution" class="form-radio shadow-none" id="addressInstitution" style="max-width:400px" placeholder=""> Institution</li>
                                <li> <input type="radio"  name="addressType" value="home" class="form-radio shadow-none" id="addressHome" style="max-width:400px" placeholder=""> Home </li>
                            </ul>
                        </div> -->

                        <!-- <div class=" mb-2">
                            <label class="input-group-text text-white" style="background-color:#2AA69C" for="currentStatus">Are you currently (check only one):<font color="red">* </font></label>
                            <ul style="list-style:none">
                                <li ><input type="radio"  name="currentStat" value="student"  class="form-radio shadow-none" id="statusStudent" style="max-width:400px" placeholder=""> A student </li>
                                <li ><input type="radio"  name="currentStat" value="degree" class="form-radio shadow-none" id="statusDeg" style="max-width:400px" placeholder=""> Received your degree within the past 5 years </li>
                                 <li ><input type="radio"  name="currentStat" value="none"  class="form-radio shadow-none" id="statusNone" style="max-width:400px" placeholder=""> Neither of these </li>
                            </ul>
                        </div> -->

                        <!-- <div class="mb-2">
                            <label class="input-group-text text-white" style="background-color:#2AA69C" for="cphaComittee"><strong>
                                    <font color="red">* </font>
                                    Are you a part of any <a href="https://www.coloradopublichealth.org/committees" target="_blank">CPHA Committees</a>? (select all that apply)
                                </strong></label>
                            <ul style="list-style:none">
                                <li > <input type="checkbox" name="cphaComittee[]" value="Professional Education" id="professionalEducation"> Professional Education (COSPHE, Culture of Data and/or Public Health in the Rockies conference planning committees) </li>
                                <li > <input type="checkbox" name="cphaComittee[]" value="Public Health Policy" id="PHP"> Public Health Policy </li>
                                <li > <input type="checkbox" name="cphaComittee[]" value="Membership" id="membership"> Membership </li>
                                <li > <input type="checkbox" name="cphaComittee[]" value="Emerging Leaders" id="emergingLeaders"> Emerging Leaders </li>
                                <li > <input type="checkbox" name="cphaComittee[]" value="Communications" id="communications"> Communications </li>
                                <li > <input type="checkbox" name="cphaComittee[]" value="Health Equity" id="healthEquity"> Health Equity </li>
                                <li > <input type="checkbox" name="cphaComittee[]" value="Not part of a committee" id="notCommittee"> Not part of a committee </li>
                            </ul>
                        </div> -->

                    </div>

                </div>
                <div class="card">
                    <div class="card-header">
                    Affiliations
                    </div>
                    <div class="card-body">
                        <div class="input-group ">
                            <label class="input-group-text text-white" style="background-color:#2AA69C" for="authorInstitution">Find Institution <font color="red">* </font></label>
                            <input name="authorInstitution" title="Institution" id="authorInstitution" class="form-control shadow-none required">
                            <input name="authorInstitutionId" title="Institution" type="hidden" id="searchId" class="required">

                        </div>
                    </div>
                </div>

                <!-- <div class="card mt-3">
                    <div class="card-header">
                    Other Information
                    </div>
                    <div class="card-body">
                        <div class="input-group flex-column">
                            <div>
                                <label class="input-group-text text-white" style="background-color:#2AA69C" for="authorInstitution">Biography (minimum 50 words)<font color="red">* </font></label>
                                <textarea name="biography" title="Biography" id="biography" class="form-control shadow-none required"></textarea>
                            </div>
                            <div class="mt-3">
                                <label class="input-group-text text-white" style="background-color:#2AA69C" for="imgUpload">HeadShots (Max 2MB, JPG, PNG, or GIF)<font color="red">* </font></label>
                             
                                <div class="form-control">
                                <input class="form-inline " title="Head Shot" type="file" name="headShot" id="headShot">
                                <button class="btn btn-primary form-inline" name="uploadHeadShot" id="uploadHeadShotBtn" value>Upload</button>
                                </div>
                                <div class="mt-2 currentHeadShotDiv">
                                <label >Current Head Shot: <img src=""  id="currentHeadShot" style="max-width:200px; max-height:200px"></img><span id="currentHeadShotName" st></span></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->


            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary saveAuthorBtn" id="saveAuthorBtn">Save changes</button>
            </div>
                </form>
        </div>
    </div>
</div>
<?php

?>

<!-- Add Institution Modal -->
<div class="modal fade" id="addInstitutionModal" tabindex="" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="background-color:gainsboro">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add institution</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5 pt-0">
                <div class="pb-3"><small style="color:red">Tip: you can directly search the city, just type the city and select its right location from dropdown.</small></div>
                <div>
                    <div class="input-group ">
                        <label class="input-group-text text-white" style="background-color:#2AA69C" for="institutionName">Institution Name</label>
                        <input type="text"  name="institutionName"  class="form-control shadow-none" id="institutionName" style="max-width:400px" placeholder="">
                    </div>

                    <small style="color:red">Please type in the first four letters of your city.  Once the cities appear, select from the list.</small>
                    <div class="input-group ">
                        <label class="input-group-text text-white" style="background-color:#2AA69C" for="institutionCity">City</label>
                        <input type="text"  name="institutionCity"  class="form-control shadow-none" id="institutionCity" style="max-width:400px" placeholder="">
                        <input type="text"  name="institutionCityId"  class="form-control shadow-none" id="institutionCityId" style="max-width:400px" placeholder="">
                    </div>

                    <div class="input-group ">
                        <label class="input-group-text text-white" style="background-color:#2AA69C" for="institutionProvince">State/Province</label>
                        <input type="text"  name="institutionState"  class="form-control shadow-none" id="institutionState" style="max-width:400px" placeholder="" readonly>
                        <input type="text"  name="institutionStateId"  class="form-control shadow-none" id="institutionStateId" style="max-width:400px" placeholder="" readonly>
                    </div>

                    <div class="input-group ">
                        <label class="input-group-text text-white" style="background-color:#2AA69C" for="institutionCountry">Country</label>
                        <input type="text"  name="institutionCountry"  class="form-control shadow-none" id="institutionCountry" style="max-width:400px" placeholder="" readonly>
                        <input type="text"  name="institutionCountryId"  class="form-control shadow-none" id="institutionCountryId" style="max-width:400px" placeholder="" readonly>
                    </div>
                    

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary submitNewInstitutionBtn">Submit Institution</button>
            </div>
        </div>
    </div>
</div>

    <script  type="text/javascript" src="<?=base_url('assets/js/admin/submissionFunction.js?v=5')?>"></script>
    <script  type="text/javascript" src="<?=base_url('assets/js/locationAutocomplete.js')?>"></script>

    <script>
        let abstractID = "<?=$abstract_id?>"
    </script>
<script>

$(function(){
    $('.presentingAuthorSubInfo').hide();

    $('.authorList').on('change','input[name="presentingAuthor"]', function(){
        $('.presentingAuthorSubInfo').show();
        let presentingAuthorFullName = $(this).attr('author_name')+' '+ $(this).attr('author_surname')
        $('.presentingAuthorName').html('<span class="text-primary">'+presentingAuthorFullName+'</span>')
    })

    $('input[name="isEligibleGrant"]').on('change', function(){
       if($('input[name="isEligibleGrant"]:checked').val() == '1' && $('input[name="isPresentingStudent"]:checked').val() == '1'){
        $('#grantAddedInfoDiv').show();
    }else{
        $('#grantAddedInfoDiv').hide();
    }
    })

    $('input[name="isPresentingStudent"]').on('change', function(){
       if($('input[name="isEligibleGrant"]:checked').val() == '1' && $('input[name="isPresentingStudent"]:checked').val() == '1'){
        $('#grantAddedInfoDiv').show();
    }else{
        $('#grantAddedInfoDiv').hide();
    }
    })

    $('input[name="isPresentingStudent"]').on('change', function(){
       if($('input[name="isPresentingStudent"]:checked').val() == '1'){
        $('#eligibilityDiv').show();
    }else{
        $('#eligibilityDiv').hide();
    }
    })
    
})

window.onload = function(){
    if( $('input[name="isEligibleGrant"]:checked').val() == '1' && $('input[name="isPresentingStudent"]:checked').val() == '1'){
        $('#grantAddedInfoDiv').show();
    }else{
        $('#grantAddedInfoDiv').hide();
    }

     
    if($('input[name="isPresentingStudent"]:checked').val() == '1'){
        $('#eligibilityDiv').show();
    }else{
        $('#eligibilityDiv').hide();
    }
}
</script>
