
<!--<script  type="text/javascript" src="--><?php //=base_url('assets/js/submissionFunction.js?v=4')?><!--"></script>-->


<?php echo view('event/common/menu'); ?>
<?php echo view('event/common/event_details'); ?>
<main>
    <div class="container pb-5">
        <?php echo view('event/common/shortcut_link'); ?>
        <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1 shadow">
            <?php
            if (session('user_type') == 'admin' && !empty($paper_id)) {
                $actionUrl = base_url('admin/update_abstract_ajax');
            } elseif (!empty($paper_id)) {
                $actionUrl = base_url('user/update_paper_ajax');
            } else {
                $actionUrl = base_url('user/submit_paper_ajax');
            }
            ?>


            <form id="abstractSubmissionForm"  action="<?= $actionUrl ?>" method="post">
                <input type="hidden" value="<?=(isset($paper_id) && !empty($paper_id))? $paper_id : ''?>" name="paper_id">
                <div class="row">
                    <div class="col mt-4">
                        <div id="questionDiv_division">
                            <h5 class="title">
                                <span class="text-danger">*</span>
                                Session Types
                            </h5>
                            <strong class="fw-bolder">All completed abstract submissions will be reviewed and considered for a podium presentation.</strong>
                        </div>
                    </div>
                </div>

                <!-- ##########   Question 1: Previous Presentation ############### -->
                <div class="row previous-presentation">
                    <div class="col mt-4">
                        <div id="previous-presentation-container">
                            <h5 class="title">
                                <span class="text-danger">*</span>
                                Previous Presentation
                            </h5>
                            <p>Was this paper previously presented at an SRS IMAST or Annual Meeting?</p>
                            <input type="radio" name="previous_presentation" id="previous_presentation_yes" value="Yes" class="form-input" <?=(!empty($paper) && $paper['previous_presentation'] == "Yes" ? 'checked' : '')?>>
                            <label for="previous_presentation_yes"> Yes, this paper has previously been presented at SRS IMAST or Annual Meeting </label> <br>
                            <input type="radio" name="previous_presentation" id="previous_presentation_no" value="No" class="form-input" <?=(!empty($paper) && $paper['previous_presentation'] == "No" ? 'checked' : '')?>>
                            <label for="previous_presentation_no"> No, this paper has not been previously presented at SRS IMAST or Annual Meeting </label>
                        </div>
                    </div>
                </div>

                <!-- ##########   Question 2: Basic Science Proposal Format ############### -->
                <div class="row basic-science-format">
                    <div class="col mt-4">
                        <div id="basic-science-format-container">
                            <h5 class="title">
                                <span class="text-danger">*</span>
                                Basic Science Proposal Format
                            </h5>
                            <p>Is your proposal in a Basic Science format?</p>
                            <input type="radio" name="basic_science_format" id="basic_science_format_yes" value="Yes" <?=(!empty($paper) && $paper['basic_science_format'] == "Yes" ? 'checked' : '')?>>
                            <label for="basic_science_format_yes"> Yes</label> <br>
                            <input type="radio" name="basic_science_format" id="basic_science_format_no" value="No" <?=(!empty($paper) && $paper['basic_science_format'] == "No" ? 'checked' : '')?>>
                            <label for="basic_science_format_no"> No</label>
                        </div>
                    </div>
                </div>

                <!-- ##########   Question 3: Abstract Category ############### -->
                <div class="row abstract-category">
                    <div class="col mt-4">
                        <div id="abstract-category-container">
                            <h5 class="title">
                                <span class="text-danger">*</span>
                                Abstract Category
                            </h5>
                            <label for="abstract_category">Please choose a category:</label>
                            <select name="abstract_category" id="abstract_category" class="form-control">
                                <option id="abstract_category_default" value=""> -- Select Category --</option>
                                <?php if(!empty($categories)): ?>
                                    <?php foreach ($categories as $category) : ?>
                                        <option id="abstract_category_<?=$category['category_id']?>" value="<?=$category['category_id']?>" <?=(!empty($paper) && $paper['abstract_category'] == $category['category_id'] ? 'selected' : '')?>  ><?=$category['name']?></option>
                                    <?php endforeach; ?>
                                <?php endif ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ##########   Abstract Body Section ############### -->
                <div class="row abstract-body">
                    <div class="col mt-4">
                        <div id="abstract-body-container">
                            <h5 class="title">
                                <span class="text-danger">*</span>
                                Abstract Body
                            </h5>
                            <p>Abstract body is limited to 2500 characters which includes: Title, Hypothesis, Study Design, Introduction, Methods, Results, and Conclusions. The character count includes titles, spaces, abstract body, and table/image captions. It does not include authors or institutions. Please save your work intermittently by using the 'save' option at the bottom of the page.</p>
                            <p>To maintain an unbiased, blinded review of all abstracts, please do NOT include any identifying information such as researcher, institution, or study group names in your abstract. Inclusion of any identifying information will disqualify your abstract from review.</p>
                            <p>Please note: this text is what will be printed in the Final Program if it is accepted. You WILL NOT BE ABLE TO EDIT IT AFTER THE SUBMISSION DEADLINE.</p>
                            <div class="text-center m-auto p-4" style="width: 600px; border:4px dotted black">
                                Total Abstract Body Count: <span id="abstract_body_count">0 characters</span> <br>
                                <?php if(!empty($paper) && trim($paper['image_caption']) !== ''): ?>
                                Image Caption Body Count: <span id="image_caption_body_count" >0 characters</span><br>
                                <?php endif ?>
                                Limit: 2500
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ##########   Question 4: Abstract Title ############### -->
                <div class="row abstract-title">
                    <div class="col mt-4">
                        <div id="abstract-title-container">
                            <h5 class="title">
                                <span class="text-danger">*</span>
                                Abstract Title
                            </h5>
                            <p>PLEASE enter this title in mixed title case. Do NOT use capital letters and do NOT put the title in quotation marks.</p>
                            <label for="abstract_title"> <span class="text-success">CORRECT </span> - This is the Title Of My Abstract <span class="text-danger">INCORRECT </span> = THIS IS THE TITLE OF MY ABSTRACT <span class="text-danger">INCORRECT </span> = This is the title of my abstract</label>
                            <textarea name="abstract_title" id="abstract_title" class="form-control countWords mt-2" rows="1" placeholder="Abstract Title" ><?=(!empty($paper) ? $paper['title'] : '')?></textarea>
                            <label class="counted_words fw-bolder"></label>
                        </div>
                    </div>
                </div>

                <!-- ##########   Question 5: Hypothesis ############### -->
                <div class="row hypothesis">
                    <div class="col mt-4">
                        <div id="hypothesis-container">
                            <label for="hypothesis" class="title">
                                <span class="text-danger">*</span>
                                Hypothesis
                            </label>
                            <textarea name="hypothesis" id="hypothesis" class="form-control countWords" rows="4" placeholder="Enter your hypothesis..."><?=(!empty($paper) ? $paper['hypothesis'] : '')?></textarea>
                            <label class="counted_words fw-bolder"></label>
                        </div>
                    </div>
                </div>

                <!-- ##########   Question 6: Study Design ############### -->
                <div class="row study-design">
                    <div class="col mt-4">
                        <div id="study-design-container">
                            <label for="study_design" class="title">
                                <span class="text-danger">*</span>
                                Study Design
                            </label>
                            <textarea name="study_design" id="study_design" class="form-control countWords" rows="4" placeholder="Describe the study design..."><?=(!empty($paper) ? $paper['study_design'] : '')?></textarea>
                            <label class="counted_words fw-bolder"></label>
                        </div>
                    </div>
                </div>

                <!-- ##########   Question 7: Introduction ############### -->
                <div class="row introduction">
                    <div class="col mt-4">
                        <div id="introduction-container">
                            <label for="introduction" class="title">
                                <span class="text-danger">*</span>
                                Introduction
                            </label>
                            <textarea name="introduction" id="introduction" class="form-control countWords" rows="4" placeholder="Write the introduction..."><?=(!empty($paper) ? $paper['introduction'] : '')?></textarea>
                            <label class="counted_words fw-bolder"></label>
                        </div>
                    </div>
                </div>

                <!-- ##########   Question 8: Methods ############### -->
                <div class="row methods">
                    <div class="col mt-4">
                        <div id="methods-container">
                            <label for="methods" class="title">
                                <span class="text-danger">*</span>
                                Methods
                            </label>
                            <textarea name="methods" id="methods" class="form-control countWords" rows="4" placeholder="Explain the methods used..."><?=(!empty($paper) ? $paper['methods'] : '')?></textarea>
                            <label class="counted_words fw-bolder"></label>
                        </div>
                    </div>
                </div>

                <!-- ##########   Question 9: Results ############### -->
                <div class="row results">
                    <div class="col mt-4">
                        <div id="results-container">
                            <label for="results" class="title">
                                <span class="text-danger">*</span>
                                Results
                            </label>
                            <textarea name="results" id="results" class="form-control countWords" rows="4" placeholder="Summarize the results..."><?=(!empty($paper) ? $paper['results'] : '')?></textarea>
                            <label class="counted_words fw-bolder"></label>
                        </div>
                    </div>
                </div>

                <!-- ##########   Question 10: Conclusions ############### -->
                <div class="row conclusions">
                    <div class="col mt-4">
                        <div id="conclusions-container">
                            <label for="conclusions" class="title">
                                <span class="text-danger">*</span>
                                Conclusions
                            </label>
                            <textarea name="conclusions" id="conclusions" class="form-control countWords" rows="4" placeholder="Provide the conclusions..."><?=(!empty($paper) ? $paper['conclusions'] : '')?></textarea>
                            <label class="counted_words fw-bolder"></label>
                        </div>
                    </div>
                </div>

                <!-- ##########   Question 11: Additional Notes ############### -->
                <div class="row additional-notes">
                    <div class="col mt-4">
                        <div id="additional-notes-container">
                            <label for="additional_notes" class="title">
                                Additional Notes
                            </label>
                            <textarea name="additional_notes" id="additional_notes" class="form-control" rows="4" placeholder="Enter any additional notes..."><?=(!empty($paper) ? $paper['additional_notes'] : '')?></textarea>
                            <label class="counted_words fw-bolder"></label>
                        </div>
                    </div>
                </div>

                <!-- ##########   Question 11: Image Caption : for counting purposes only ############### -->
                <div class="row image_caption" style="display: none">
                    <div class="col mt-4">
                        <div id="image-caption-container">
                            <textarea name="image_caption" id="image_caption" class="form-control countWordsCaption" rows="4" placeholder="Enter any additional notes..."><?=(!empty($paper) ? $paper['image_caption'] : '')?></textarea>
                            <label class="counted_words_caption fw-bolder"></label>
                        </div>
                    </div>
                </div>


                <div>
                    <label class="title mt-4">Image/Table Caption</label>
                    <p>If you are adding an image Or table to your abstract submission, the caption for this will count to your total 2500 characters. Please add your caption on the upload page you have <span id="remaining_caption_count" class="fw-bolder">0</span>/2500 characters left for your caption.</p>
                </div>


                <hr>
                <hr>
                <div class="row">
                    <div class="col">
                        <?php if(isset($is_edit) && $is_edit == 1): ?>
                            <div>
                                <input type="submit" id="updatePapers" value="Save" class="btn btn-primary">
                                <input type="submit" id="updateContinuePapers" value="Save and Continue" class="btn btn-primary">
                            </div>
                        <?php else:?>
                            <div>
                                <input type="submit" id="savePapers" value="Save" class="btn btn-primary">
                                <input type="submit" id="saveContinuePapers" value="Save and Continue" class="btn btn-primary">
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </form>
<!--            #########################           -->
        </div>
    </div>
</main>
<script src="<?=base_url()?>assets/js/helpers.js"></script>
<script>
    let totalWordsCount = 0;
    let userID = `<?=session('user_id')??''?>`;
    let previousUrl = "<?= $previous_url ?? ''?>"
    let previousPage = "<?= $previous_page ?? '' ?>"
    $(function(){
        // $('.summernote').summernote({
        //     tabsize: 2,
        //     height: 120,
        //     toolbar: [
        //         ['font', ['bold', 'italic', 'underline', 'clear', 'superscript', 'subscript']],
        //     ]
        //     ,callbacks: {
        //         onKeyup: function(e) {
        //             let idProp = $(this).attr('id');
        //             let limit =  $(this).attr('limit');
        //             if( parseInt(countWords($(this).val())) > parseInt(limit) ){
        //                 $('#'+idProp+'WordsCountExceeded').removeClass('d-none').html(limit+' words limit exceeded!!')
        //             }else{
        //                 $('#'+idProp+'WordsCountExceeded').addClass('d-none').html('')
        //             }
        //             $('#'+idProp+'WordsCount').html(countWords($(this).val()))
        //             $('#totalWordsCount').html(countTotalWords())
        //         }
        //     },
        //     disableEnter: true,
        //     enterHtml: '',
        // });

        abstract_body_counter();
    })


    function abstract_body_counter(){
        WordCounterHelper.init(
            'textarea.countWords',  // Textarea selector
            '.counted_words',       // Word count display
            '#abstract_body_count' // Total word count display
        );

        WordCounterHelper.init(
            'textarea.countWordsCaption',  // Textarea selector
            '.counted_words_caption',       // Word count display
            '#image_caption_body_count' // Total word count display
        );


        $('textarea.countWords').on('input', function(){
            let total_body_count = parseInt($('#abstract_body_count').text())
            let remaining = 2500 - total_body_count;
            $('#remaining_caption_count').text(remaining)
        })


        $('textarea.countWords').on('input', function(){
            let abstract_body_count = $('#abstract_body_count').text();
            let image_caption_body_count = $('#image_caption_body_count').text();

            if(parseInt(abstract_body_count) + parseInt(image_caption_body_count) > 2500){
                toastr.error('Total of description already exceed 2500 words!')
                $('#abstract_body_count').closest('div').addClass('text-danger')
                $('#abstract_body_count').closest('div').removeClass('text-success')
            }else{
                $('#abstract_body_count').closest('div').addClass('text-success')
                $('#abstract_body_count').closest('div').removeClass('text-danger')
            }
        })

        $('textarea.countWords').trigger('input');
        $('textarea.countWordsCaption').trigger('input');
    }


</script>
