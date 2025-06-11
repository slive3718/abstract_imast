
<script  type="text/javascript" src="<?=base_url('assets/js/admin/submissionFunction.js?v=3')?>"></script>


<?php echo view('admin/common/menu'); ?>


<main>
    <div class="container-fluid">

 <?php echo view('admin/common/shortcut_link'); ?>
        

        <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1">
            <form id="abstractSubmissionForm" action="<?=(isset($abstract_details[0]->id)) ? base_url().'/'.$event->uri.'/admin/update_abstract_ajax': base_url().'/'.$event->uri.'/admin/submit_abstract_ajax' ?>" method="post">
            <input type="hidden" value="<?=(isset($abstract_id) && !empty($abstract_id))? $abstract_id : ''?>" name="abstract_id">
            <input type="hidden" value="<?=(isset($abstract_details) && !empty($abstract_details[0]->user_id))? $abstract_details[0]->user_id : ''?>" name="user_id">
              <h5>All submissions are considered for all session types</h5>
              <hr/>
                <div class="mt-4 primary_topic">
                   <h5>Primary Topic (please select 1)</h5>
                    <?php if (isset($abstract_topics)): ?>
                        <?php foreach($abstract_topics as $topics): ?>
                            <div class="form-check">
                                <label class="form-check-label" for="check1">
                                    <input type="checkbox" class="form-check-input" name="primary_topic[]"  value="<?=$topics->value?>" <?=isset($abstract_details) && in_array($topics->value, json_decode($abstract_details[0]->primary_topic, true)) ? 'checked' : '';?> ><?=$topics->name?>   
                                </label>
                                </div>
                        <?php endforeach; ?>
                    <?php endif ?>
                </div>

                <div class="mt-4 secondary_topic">
                   <h5>Secondary Topic (please select 1)</h5>
                    <?php if (isset($abstract_topics)): ?>
                        <?php foreach($abstract_topics as $topics): ?>
                            <div class="form-check">
                                <label class="form-check-label" for="check1">
                                    <input type="checkbox" class="form-check-input"  name="secondary_topic[]"  value="<?=$topics->value?>" <?=isset($abstract_details) && in_array($topics->value, json_decode($abstract_details[0]->secondary_topic, true)) ? 'checked' : '';?>><?=$topics->name?>   
                                </label>
                                </div>
                        <?php endforeach; ?>
                    <?php endif ?>
                </div>


            
            <!--            Abstract Title -->
            <hr/>
               <div class="mt-4">
                  <h5 class="fw-bold"><required class="text-danger">*</required> Abstract Title</h5>
                  <div class="form-group">
                    <textarea name="abstractTitle" id="abstractTitle" class="form-control shadow-none summernote" placeholder="Abstract Title" rows="5"><?=(isset($abstract_details)? ($abstract_details[0]->title !== '')? $abstract_details[0]->title :'':'' )?></textarea>
                  </div>
                  <!-- <input type="hidden" class="total_sum_check" id="abstractTitle_count" value="0"> -->
                </div>

                <hr/>
                <!-- Abstract Body -->
                <h5 class="fw-bold"> Abstract Body</h5>         
                <p> Abstract body is limited to 400 words which includes a Background, Hypothesis, Methods, Results and Conclusion.</p>
                <p>Total Word count (for all textboxes below): <span id="totalWordsCount"> 0 </span><strong> word(s)</strong></p>
                <div class="mt-4">
                  <h5 class="fw-bold"><required class="text-danger">*</required> Background</h5>
                  <div class="form-group">
                    <textarea name="background" id="background" class="form-control shadow-none countText summernote" placeholder="Background" rows="5"><?=(isset($abstract_details)? ($abstract_details[0]->background !== '')? $abstract_details[0]->background :'':'' )?></textarea>
                  </div>
                  <input type="hidden" class="total_sum_check" id="background_count" value="0">
                </div>

                <div class="mt-4">
                  <h5 class="fw-bold"><required class="text-danger">*</required> Hypothesis</h5>
                  <div class="form-group">
                    <textarea name="hypothesis" id="hypothesis" class="form-control shadow-none countText summernote" placeholder="Hypothesis" rows="5"><?=(isset($abstract_details)? ($abstract_details[0]->hypothesis !== '')? $abstract_details[0]->hypothesis :'':'' )?></textarea>
                  </div>
                  <input type="hidden" class="total_sum_check" id="hypothesis_count" value="0">
                </div>

                <div class="mt-4">
                  <h5 class="fw-bold"><required class="text-danger">*</required> Methods</h5>
                  <div class="form-group">
                    <textarea name="methods" id="methods" class="form-control shadow-none countText summernote" placeholder="Methods" rows="5"><?=(isset($abstract_details)? ($abstract_details[0]->methods !== '')? $abstract_details[0]->methods :'':'' )?></textarea>
                  </div>
                  <input type="hidden" class="total_sum_check" id="methods_count" value="0">
                </div>

                <div class="mt-4">
                  <h5 class="fw-bold"><required class="text-danger">*</required> Results</h5>
                  <div class="form-group">
                    <textarea name="results" id="results" class="form-control shadow-none countText summernote" placeholder="Results" rows="5"><?=(isset($abstract_details)? ($abstract_details[0]->results !== '')? $abstract_details[0]->results :'':'' )?></textarea>
                  </div>
                  <input type="hidden" class="total_sum_check" id="results_count" value="0">
                </div>

                <div class="mt-4">
                  <h5 class="fw-bold"><required class="text-danger">*</required> Conclusion</h5>
                  <div class="form-group">
                    <textarea name="conclusion" id="conclusion" class="form-control shadow-none countText summernote" placeholder="Conclusion" rows="5"><?=(isset($abstract_details)? ($abstract_details[0]->conclusion !== '')? $abstract_details[0]->conclusion :'':'' )?></textarea>
                  </div>
                  <input type="hidden" class="total_sum_check" id="conclusion_count" value="0">
                </div>

                <div class="mt-4">
                  <h5 class="fw-bold"><required class="text-danger">*</required> Reference</h5>
                  <div class="form-group">
                    <textarea name="references" id="references" class="form-control shadow-none summernote" placeholder="References" rows="5"><?=(isset($abstract_details)? ($abstract_details[0]->reference !== '')? $abstract_details[0]->reference :'':'' )?></textarea>
                  </div>
                  <!-- <input type="hidden" class="total_sum_check" id="references_count" value="0"> -->
                </div>

                <div class="mt-4">
                  <h5 class="fw-bold"><required class="text-danger">*</required> Diversity</h5>
                    <p>Does this abstract meet any of the following diversity award requirements?</p>
                    <ul style="list-style-type: lower-alpha">
                        <li>Identify, analyze or seek solution for healthcare disparities</li>
                        <li>Focus on underserved, underrepresented or understudied populations; or</li>
                        <li>Promote the values of diversity, inclusion and equity as prioritize by the PRiSM Board of Directors and Diversity Committee</li>
                    </ul>
                    <div class="form-group">
                        <input type="radio" class="form-check-input" name="diversity"  value="yes" id="diversity_yes" <?=(isset($abstract_details)? ($abstract_details[0]->diversity == 'yes')? 'checked':'':'' )?>><label for="diversity_yes">Yes</label>
                        <input type="radio" class="form-check-input ms-5" name="diversity" value="no" id="diversity_no" <?=(isset($abstract_details)? ($abstract_details[0]->diversity == 'no')? 'checked':'':'' )?>><label for="diversity_no">No</label>
                    </div>
                </div>

                <div class="mt-4 ">
                  <h5 class="fw-bold"><required class="text-danger">*</required> Award Information</h5>
                    <p>This abstract focuses on:</p>

                    <div class="form-check"> 
                        <input type="radio" class="form-check-input form-inline" name="award_information"  value="msk" id="MSK" <?=(isset($abstract_details)? ($abstract_details[0]->award_information == 'msk')? 'checked':'':'' )?>>
                        <label for="msk" class="form-check-label">
                        <strong>Diagnosis, Treatment, Management of Medical Conditions (MSK rad/US)</strong>
                        Abstracts related to diagnosis of a condition (including radiologic studies), medical treatments, non-surgical management of injuries, outcomes. 
                        Includes concussion (as it relates to diagnosis, treatment, management), bone health, rehab (from a non-surgical treatment)
                        </label>
                    </div>
                    
                    <div class="form-check"> 
                        <input type="radio" class="form-check-input " name="award_information" value="surgical" id="surgical" <?=(isset($abstract_details)? ($abstract_details[0]->award_information == 'surgical')? 'checked':'':'' )?>>
                        <label for="surgical" class="form-check-label">
                        <strong>Surgical Treatments and Outcomes (pain mgmt, rehab, technique/cadaver)</strong>
                        Surgical treatments and any topic relating to surgical management, such as pain management, rehabilitation after surgery, technique papers, and cadaver studies
                    </label>
                    </div>
                    <div class="form-check"> 
                        <input type="radio" class="form-check-input " name="award_information" value="injury" id="injury" <?=(isset($abstract_details)? ($abstract_details[0]->award_information == 'injury')? 'checked':'':'' )?>>
                        <label for="injury" class="form-check-label">
                        <strong>Injury Prevention and Motion Analysis</strong>
                        Abstracts related to injury prevention strategies, sports specialization, motion analysis
                    </label>
                    </div>
                    <div class="form-check"> 
                        <input type="radio" class="form-check-input " name="award_information" value="mental" id="mental" <?=(isset($abstract_details)? ($abstract_details[0]->award_information == 'mental')? 'checked':'':'' )?>>
                        <label for="mental" class="form-check-label">
                        <strong>Mental Health and Wellness</strong>
                        Abstracts related to patient and athlete mental health, sleep, identity, wellness
                    </label>
                    </div>
                      </div>
                    <div class="form-check"> 
                        <input type="radio" class="form-check-input " name="award_information" value="n/a" id="not_applicable" <?=(isset($abstract_details)? ($abstract_details[0]->award_information == 'n/a')? 'checked':'':'' )?>>
                        <label for="not_applicable" class="form-check-label">
                        <strong>Not Applicable</strong>
                    </label>
                    </div>
                </div>
      

           
                <!-- <input type="button" class="btn btn-primary mt-5" id="saveAbstractBtn" style="max-width:200px" value="Save"><br> -->
                <div class="mt-5">
                    <input type="submit" class="btn btn-success mt-2" style="max-width:200px" value="Save and Continue">
                </div>
            </form>
<!--            #########################           -->
        </div>
    </div>
</main>

<script>
    let attrId_array = {};

    let topic1_limit = 1;
    let topic2_limit = 1;
    let population_limit = 2;

    let totalWordsCount = 0;
 
$(function(){

      $('.summernote').summernote({
         tabsize: 2,
          height: 120,
          toolbar: [
          ['font', ['bold', 'italic', 'underline', 'clear', 'superscript', 'subscript']],
        ]
      });

    $('.primary_topic input[type=checkbox]').on('change', function (e) {
        if ($('.primary_topic input[type=checkbox]:checked').length > topic1_limit) {
            $(this).prop('checked', false);
            swal.fire(
                'Please note',
                'Please choose one item per topic',
                'warning'
            )
        }
    });

    $('.secondary_topic input[type=checkbox]').on('change', function (e) {
        if ($('.secondary_topic input[type=checkbox]:checked').length > topic2_limit) {
            $(this).prop('checked', false);
            swal.fire(
                'Please note',  
                'Please choose one item per topic',
                'warning'
            )
        }
    });

    $('.countText').each(function(){
        let idProp = $(this).attr('id');
         $(this).on('summernote.keypress summernote.click summernote.load summernote.blur ', function(){
          // console.log('tst')
           $('#'+idProp+'_count').val(countWords($(this).val()))

           $('#totalWordsCount').html(countTotalWords())
        })
    })
})
    
function countTotalWords(){
     let totalWordsSum = 0;
  $('.total_sum_check').each(function() {
      totalWordsSum += parseInt($(this).val());
  });
  return totalWordsSum;
}

 $(window).on('load', function(){
        $('.countText').each(function(){
            let idProp = $(this).attr('id');
            $('#'+idProp+'_count').val(countWords($(this).val()))
        })
         $('#totalWordsCount').html(countTotalWords())
})

    
function countWords(str) {
  // Remove leading and trailing white spaces
  str = str.trim();

  // Replace multiple spaces with a single space
  str = str.replace(/\s+/g, ' ');

  if (str === '') {
    return 0;
  }

  // Split the string by space
  var words = str.split(' ');

  // Count the number of words
  var wordCount = words.length;

  return wordCount;
}
</script>
