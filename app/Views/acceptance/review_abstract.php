    

<?php echo view('reviewer/common/menu'); ?>
<style>
 required{
    color:red;
 }

 /* tr > td:first-child{
    line-height:3.0;
 } */
 table{
    width: 100%;
 }
 table tr td{
    padding-top:20px !important;
    /* border: 1px solid black; */
    vertical-align:top;
 }
 .card-body{
    padding-left:20px !important;
 }
 input{
    border: 2px solid ;
 }
</style>
<?php //echo '<pre>';print_r($abstracts);exit;?>
<main>
    <div class="container-fluid">
         <h4> Review Abstract </h4>    
         <hr/>
        <div class="card">
            <div class="card-header">
                <h5> General Information </h4>   
            </div>
            <div class="card-body">
                <table>
                    <tr>
                        <td style="width:250px">Abstract ID: </td>
                        <td><?=$abstracts->id?></td>
                    </tr>
                     <tr>
                        <td>Abstract Title: </td>
                        <td><?=$abstracts->title?></td>
                    </tr>
                </table>
                
            </div>
        </div>
        <div class="card mt-5">
            <div class="card-header">
                <h5> Abstract Information </h4>   
            </div>
            <div class="card-body">
                <table id="abstractInformationTable">
                    <tr>
                        <td style="width:250px">Session Type:</td>
                        <td>
                            <?php if($abstracts->session_type == '0'){
                                echo 'Program/Research "Showcase" Session (30 minutes)';
                            }
                                else if($abstracts->session_type == '1'){
                                    echo 'Program/Research "Showcase" Session (1 hour)';
                                }
                                else if($abstracts->session_type == '2'){
                                    echo 'Skill Building Session (2 hours)';
                                }
                                else {
                                    echo 'Poster Session';
                                }
                            
                            ?>
                        </td>
                    </tr>
                    <!-- <tr>
                        <td>Foundational Capabilities:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Foundational Services:</td>
                        <td></td>
                    </tr> -->
                    <tr>
                        <td style="width:250px; vertical-align:top">Abstract Title:</td>
                        <td><?=$abstracts->title?></td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top">Abstract Brief Summary:</td>
                        <td><?=$abstracts->abstract_brief_summary?> <br></td>
                    </tr>
                    <tr>
                        <td  style="vertical-align:top">Abstract Text:</td>
                        <td><?=$abstracts->abstract_text?></td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top">Substance Area:</td>
                         <td><?php foreach(($abstracts->substance_area) as $substance_area){
                            echo $substance_area['value']. '<br>';
                        }?></td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top">Population:</td>
                        <td><?php foreach(($abstracts->populations) as $population){
                            echo $population['value']. '<br>';
                        }?></td>
                    </tr>
                     <tr>
                        <td>Consider Poster ? </td>
                        <td><?=isset($abstracts->abstract_yes_no_1)?$abstracts->abstract_yes_no_1:''?></td>
                    </tr>
                    <tr>
                        <td>Learning Objective 1:</td>
                        <td><?=isset($abstracts->learning_objectives[0]->objective_1)?$abstracts->learning_objectives[0]->objective_1:''?></td>
                    </tr>
                    <tr>
                        <td>Learning Objective 2: 	</td>
                        <td><?=isset($abstracts->learning_objectives[0]->objective_2)?$abstracts->learning_objectives[0]->objective_2:''?></td>
                    </tr>
                    <tr>
                        <td>Learning Objective 3:</td>
                        <td><?=isset($abstracts->learning_objectives[0]->objective_3)?$abstracts->learning_objectives[0]->objective_3:''?></td>
                    </tr>
                    
                </table>
                
            </div>
        </div>
        <div class="card  mt-5">
            <div class="card-header">
                <h5> General Information </h4>   
            </div>
            <div class="card-body">
<form id="formReviewData" name="formReviewData" onsubmit="return false;">


<div class="groupDiv">

    <table class="divTitle" border="0">

        <tbody><tr>
            <td colspan="3" style="width:250px"><required>*</required>
                A conflict of interest is present when the reviewer is (a) directly involved
                as a co-author and/or direct contributor; (b) currently works with or supervises/mentors
                one of the presenters; (c) has any other concerns about conflict of interest.
            </td>
        </tr>

        <tr>
            <td colspan="3"><br>
                <input type="radio" value="No" name="conflict_of_interest" id="q1_no" class="required checkShowHideCoi" showId="withCOI" <?=(isset($abstract_review_data['conflict_of_interest']) && $abstract_review_data['conflict_of_interest'] == 'No')? 'checked':''?>>
                <label> No, I do not have a conflict of interest.</label>
            </td>
        </tr>

        <tr>
            <td colspan="3">
               
                <input type="radio" value="Yes" name="conflict_of_interest" id="q1_yes" class="required checkShowHideCoi" showId="withCOI" <?=(isset($abstract_review_data['conflict_of_interest']) && $abstract_review_data['conflict_of_interest'] == 'Yes')? 'checked':''?>>

                Yes, I have a conflict of interest (Please describe the conflict in the
                comment box below. Do not proceed further in the review.)
                <br>

                <label for="question_item1" class="error" hidden>Required</label>
                <br>
            </td>
        </tr>

    </tbody></table>

    <div class="withCOI" id="withCOI" style="display: none;">

        <div class="divTitle" style=""><required>*</required>
            <strong>Session Type and Length:</strong>
            The submission content matches the session type and length. Consider the following:
        </div>

        <div class="divQuestion" style="">
            1. Is the submission type appropriate? If 'No', recommend  which session type
            (Research/Program Showcase, Poster, Skill-building, Roundtable) is more
            appropriate in the comments.
            <br>

            2. Is this content appropriate for the session length? If 'No', please recommend which
            session length is more appropriate in the comments.
            <br><br>

            <input type="radio" name="session_type_and_length" showId="stl_explanation" class="required checkShowHide" value="Yes" <?=(isset($abstract_review_data['session_type_and_length']) && $abstract_review_data['session_type_and_length'] == 'Yes')? 'checked':''?>> Yes
            <input type="radio" name="session_type_and_length" showId="stl_explanation" class="required checkShowHide" value="No" <?=(isset($abstract_review_data['session_type_and_length']) && $abstract_review_data['session_type_and_length'] == 'No')? 'checked':''?>> No

            <label for="question_item2" class="error" hidden><br>Required</label>
            <br><br>

            <div id="stl_explanation" style="display: none;">
                Please Explain: <br><br>

                <textarea name="stl_explanation" cols="60" class="" rows="7"><?=(isset($abstract_review_data['stl_explanation']) && $abstract_review_data['stl_explanation'] !== '')?$abstract_review_data['stl_explanation']:''?></textarea>
                <label for="stl_explanation" class="error" style="display:none"><br>Required</label>
            </div>
        </div>

        <div class="divTitle" style=""><required>*</required>
            <strong>Topic Track:</strong>
            The submission content matches the topic track selected. Consider the following:
        </div>

        <div class="divQuestion" style="">
            1. Is the primary topic track selected appropriate? If 'No', recommend  which
            topic track is more appropriate in the comments.
            <br><br>

            <input type="radio" name="topic_track" showId="tr_explanation" class="required checkShowHide" value="Yes" <?=(isset($abstract_review_data['topic_track']) && $abstract_review_data['topic_track'] == 'Yes')? 'checked':''?>> Yes
            <input type="radio" name="topic_track" showId="tr_explanation" class="required checkShowHide" value="No" <?=(isset($abstract_review_data['topic_track']) && $abstract_review_data['topic_track'] == 'No')? 'checked':''?>> No

            <label for="question_item3" class="error" hidden><br>Required</label>
            <br><br>

            <div id="tr_explanation" style="display: none;">
                Please Explain:
                <br><br>

                <textarea name="tr_explanation" cols="60" class="" rows="7"><?=(isset($abstract_review_data['tr_explanation']) && $abstract_review_data['tr_explanation'] !== '')?$abstract_review_data['tr_explanation']:''?></textarea>
                <label for="tr_explanation" class="error" style="display:none"><br>Required</label>
            </div>
        </div>

        
        <hr>

        <div class="divTitle" style="">
            The next section in the voting system consists of six factors to rate on a scale from
            1 to 5. The bullet points under each question are to help you with this rating.
        </div>

        <div class="divQuestion" style="">
            For the following items, please provide a rating from 1 to 5.
            <br><br>

            <strong>5</strong> = I strongly agree with this statement.
            <br>

            <strong>4</strong> = I agree with this statement.
            <br>

            <strong>3</strong> = I neither agree nor disagree with this statement.
            <br>

            <strong>2</strong> = I disagree with this statement.
            <br>

            <strong>1</strong> = I strongly disagree with this statement.
            <br>
        </div>
        <br><br>
<br>
        <div class="divQuestion" style="">
            <strong>1. RELEVANCE TO THEME:</strong>
             The submission relates to the 2023 PRISM theme, "Public Health: Community, Connection, and Action" Consider the following when scoring:
            <br>

            <ul>
                <li>Does this submission relate to theme?</li>
                <li>
                    Does the topic, question or issue increase awareness or address health in equities
                    in public health?
                </li>
            </ul>

            Score: <input type="number" class="parcialField required" name="relevance_to_theme" id="question_item4" min="1" max="5" maxlength="1" value="<?=(isset($abstract_review_data['relevance_to_theme']) && $abstract_review_data['relevance_to_theme'] !== '')?$abstract_review_data['relevance_to_theme']:''?>">
        </div>
<br>
        <div class="divQuestion" style="">
            <strong>2. IMPLICATIONS FOR PUBLIC HEALTH:</strong>
            The information presented in this abstract has or may have a practical implications
            for public health and PRiSM attendees.
            <br><br>

            Consider the following when scoring:
            <br>

            <ul>
                <li>Is the topic, question or issue relevant to the public health community?</li>
                <li>Is the information presented significant to the field of public health?</li>
                <li>is the abstract topic timely in terms of current issues or interest?</li>
                <li>Is the presentation likely to be of interest to a reasonable number of PRiSM attendees?</li>
            </ul>

            Score: <input type="number" class="parcialField required" name="implication_for_public_health" id="question_item5" min="1" max="5" maxlength="1" value="<?=(isset($abstract_review_data['implication_for_public_health']) && $abstract_review_data['implication_for_public_health'] !== '')?$abstract_review_data['implication_for_public_health']:''?>">
        </div>
<br>
        <div class="divQuestion" style="">
            <strong>3. ORIGINALITY:</strong>
            The abstract submission shows a high degree of originality.

            Consider the following when scoring:
            <br>

            <ul>
                <li>Is the work original ?</li>
                <li>Does the work provide new insights for public health or its application?</li>
            </ul>

            Score: <input type="number" class="parcialField required" name="originality" id="question_item6" min="1" max="5" maxlength="1" value="<?=(isset($abstract_review_data['originality']) && $abstract_review_data['originality'] !== '')?$abstract_review_data['originality']:''?>">
        </div>
<br>
        <div class="divQuestion" style="">
            <strong>4. RIGOR &amp; QUALITY:</strong>
            The abstract content is accurate and supported by evidence-based research and / or
            quality data.

            Consider the following when scoring:
            <br>

            <ul>
                <li>Are the conclusions justified?</li>
                <li>
                    If applicable, does the abstract describe the appropriate methodology/approach
                    based on the purpose?
                </li>
                <li>
                    If applicable, does the abstract present appropriate results based on the
                    methodology/approach?
                </li>
                <li>Does the abstract clearly state the source of supporting evidence presented?</li>
            </ul>

            Score: <input type="number" class="parcialField required" name="region_quality" id="question_item7" min="1" max="5" maxlength="1" value="<?=(isset($abstract_review_data['region_quality']) && $abstract_review_data['region_quality'] !== '')?$abstract_review_data['region_quality']:''?>">
        </div>
<br>
        <div class="divQuestion" style="">
            <strong>5. LEARNING OBJECTIVES / INSTRUCTIONAL STRATEGY:</strong>
            The abstract offers measurable learning objectives and if applicable, an appropriate
            instructional strategy.

            Consider the following when scoring:
            <br>

            <ul>
                <li>
                    Are specific, measurable, achievable, realistic and time-phased (SMART) learning objectives,
                    using behavioral verbs, included?
                </li>
                <li>
                    Does the abstract clearly state what participants will be able to do or what they will
                    learn from the session?
                </li>
                <li>
                    If applicable, does the abstract describe an appropriate instructional method to engage
                    adult learners?
                </li>
            </ul>

            Score: <input type="number" class="parcialField required" name="learning_objectives" id="question_item8" min="1" max="5" maxlength="1" value="<?=(isset($abstract_review_data['learning_objectives']) && $abstract_review_data['learning_objectives'] !== '')?$abstract_review_data['learning_objectives']:''?>">
        </div>
<br>
        <div class="divQuestion" style="">
            <strong>6. CLARITY &amp; COMPLETENESS:</strong>
            The abstract is complete, clearly written  and easy to understand.

            Consider the following when scoring:
            <br>

            <ul>
                <li>
                    Is the work complete? (Note: we do accept work with preliminary, in progress or
                    anticipated results for the Poster format)
                </li>
                <li>Does the abstract clearly summarize the session?</li>
                <li>Is the writing clear and free of jargon? If jargon is used, is it defined?</li>
                <li>Is there a logical flow to how the information is presented?</li>
                <li>Are the key points clear?</li>
                <li>Is the abstract topic clearly stated and understood?</li>
            </ul>

            Score: <input type="number" class="parcialField required" name="clarity_and_completeness" id="review_question4" min="1" max="5" maxlength="1" pattern="([0-5])" value="<?=(isset($abstract_review_data['clarity_and_completeness']) && $abstract_review_data['clarity_and_completeness'] !== '')?$abstract_review_data['clarity_and_completeness']:''?>">
        </div>
<br>
        <div class="divTitle" style="">
            <strong>Total Score from #1 to #6 above:</strong>
            <input type="number" name="total_score" id="review_question3" min="1" max="5" maxlength="1" value="<?=(isset($abstract_review_data['total_score']) && $abstract_review_data['total_score'] !== '')?$abstract_review_data['total_score']:''?>" >
        </div>
<div class="hidden" style="display: block;">
            <hr>
        </div>
        <div class="divQuestion">

        <div class="hidden" style="display: block;">

            <strong>
                OVERALL VOTE: Please cast your final vote on this abstract here, using the
                following abstract rating scale.
            </strong>
            <br><br>

            Abstract Rating Scale
            <br><br>

            <strong>5</strong>
            = Total Recommendation (I vote to recommend this abstract for acceptance in the conference program.)
            <br>

            <strong>4</strong>
            = Weak Recommendation (I can support a vote to accept, and I won’t argue against it.)
            <br>

            <strong>3</strong>
            = Neutral (I’m not impressed but I don’t feel strongly for or against it.)
            <br>

            <strong>2</strong>
            = Weak Decline (I don’t like it, but I won’t vote to completely decline it.)
            <br>

            <strong>1</strong>
            = Total Decline (I vote to decline this abstract for acceptance in the conference program.)
            <br><br><br>

            Score: <input type="number" name="overall_vote" id="review_rate" min="1" max="5" class="required" maxlength="1" value="<?=(isset($abstract_review_data['overall_vote']) && $abstract_review_data['overall_vote'] !== '')?$abstract_review_data['overall_vote']:''?>">
            <br><br>

        </div>
        </div>
        
    </div>
        
        Comments for the planning committee:
        <br>

        <textarea name="comments_for_committee" id="comments_for_committee" cols="60" rows="7"><?=(isset($abstract_review_data['comments_for_committee']) && $abstract_review_data['comments_for_committee'] !== '')?$abstract_review_data['comments_for_committee']:''?></textarea>

        <br><br>
        <input type="button" value="Save" class="btn btn-success" onclick="submitFormReview();">
   
</div>
<br><br>

</form>
                
            </div>
        </div>
    </div>
</main>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
<script>
    let baseUrlReviewer = "<?=base_url().'/'.$event->uri.'/reviewer/'?>";
    let reviewer_id = "<?=$reviewer_id?>"
    $(function(){
        // checkShowHide();
        $('.checkShowHide').on('click change', function(){
             if($(this).val() == "No" && $(this).is(':checked')){
                $('#'+$(this).attr('showId')).show();
             }else{
                 $('#'+$(this).attr('showId')).hide();
             }
        })

        $('.checkShowHide').each(function(){
            if($(this).val() == "No" && $(this).is(':checked')){
                $('#'+$(this).attr('showId')).show();
             }
        })

        $('.checkShowHideCoi').on('click change', function(){
             if($(this).val() == "No" && $(this).is(':checked')){
                $('#'+$(this).attr('showId')).show();
             }else{
                 $('#'+$(this).attr('showId')).hide();
             }
        })


        $('.checkShowHideCoi').each(function(){
            if($(this).val() == "No" && $(this).is(':checked')){
                $('#'+$(this).attr('showId')).show();
             }
        })

         $('.parcialField').on('change click',function() {
            var sum = 0;
            $('.parcialField').each(function() {
                var value = parseFloat($(this).val());
                if (!isNaN(value)) {
                    sum += value;
                }
            });
            $('input[name="total_score"]').val(sum)
        });
    })

    function submitFormReview(){
        let vote_error = 0;
        let totalScore = 0;
        let missing_fields = 0;
        let formData = new FormData(document.getElementById('formReviewData'));
        formData.append('abstract_id', abstract_id)
        formData.append('reviewer_id', reviewer_id)
        // console.log(formData)

        if( $('input[name="conflict_of_interest"]:checked').val() == "No"){
            console.log($('input[name="conflict_of_interest"]:checked').val() )
            $('.required').each(function(){
            if($(this).val() == ''){
                console.log($(this).val())
                    missing_fields = 1;
                }
            })

            $('.parcialField').each(function(){
                if( parseFloat($(this).val()) > 5 || parseFloat($(this).val()) < 1){
                    // console.log('test')
                    // console.log($(this).attr('id'))
                    vote_error = 1;
                }
             })

            $('.parcialField').each(function() {
            var value = parseFloat($(this).val());
            if (!isNaN(value)) {
                totalScore += value;
            }
        });

        if($('input[name="overall_vote"]').val() < 1 || $('input[name="overall_vote"]').val() > 5 ){
                toastr.error('Overall score is incorrect')
                return false
            }

        }else{
            if($(this).val == ''){
                toastr.error('Please select conflict of interest.')
            }
        }

        if(vote_error > 0){
            toastr.warning('Rating/score can only be 1 to 5')
            return false;
        }

        if(missing_fields > 0){
                toastr.error('Please fill all required fields');
                return false;
            }
       
        $.ajax({
            url: baseUrlReviewer+"addReviewData",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(data){
                console.log(data)
                data = JSON.parse(data)
                if(data.status == 200){
                    Swal.fire({
                    title: 'Are you sure?',
                    text: "Continue to next abstract",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, continue!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.get(baseUrlReviewer+'/getNextReviewAbstract/'+abstract_id, function(data){
                               if (data && (data.length > 0 || data !== null)) 
                                {
                                    let timerInterval
                                    Swal.fire({
                                    title: 'Success',
                                    html: 'Loading next abstract',
                                    timer: 2000,
                                    icon: 'success',
                                    timerProgressBar: true,
                                    didOpen: () => {
                                        // Swal.showLoading()
                                        timerInterval = setInterval(() => {
                                        }, 20)
                                    },
                                    willClose: () => {
                                        clearInterval(timerInterval)
                                    }
                                    }).then((result) => {
                                    /* Read more about handling dismissals below */
                                        window.location.href= baseUrlReviewer+'/reviewAbstract/'+data
                                    })
                                
                                }
                               else{
                                    let timerInterval
                                    Swal.fire({
                                    title: 'Success',
                                    html: 'Thank you for reviewing abstracts',
                                    timer: 2000,
                                    icon: 'success',
                                    timerProgressBar: true,
                                    didOpen: () => {
                                        // Swal.showLoading()
                                        timerInterval = setInterval(() => {
                                        }, 20)
                                    },
                                    willClose: () => {
                                        clearInterval(timerInterval)
                                    }
                                    }).then((result) => {
                                    /* Read more about handling dismissals below */
                                        window.location.href= baseUrlReviewer+'/abstract_list'
                                    })
                               }
                            }).done(function(response) {
                                // Handle a successful response
                       
                            })
                            .fail(function(jqXHR, textStatus, errorThrown) {
                                // Handle a failed response
                                console.error(textStatus, errorThrown);
                            });
                        }
                    })
                }
            }
                            
        },'json')

    }

    //Todo: fix still can save after session expired
   
</script>