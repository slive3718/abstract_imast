
<!--<script  type="text/javascript" src="--><?php //=base_url('assets/js/submissionFunction.js?v=4')?><!--"></script>-->


<?php echo view('event/common/menu'); ?>
<?php echo view('event/common/event_details'); ?>
<?php //=print_r(session('user_type'));exit;?>
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

<!--            <form action="--><?php //=(session('is_super_admin') == 1 ? base_url().'/admin/update_paper_ajax' ): (!empty($paper_id)) ? base_url().'/user/update_paper_ajax': base_url().'/user/submit_paper_ajax' ?><!--" method="post">-->
            <input type="hidden" value="<?=(isset($paper_id) && !empty($paper_id))? $paper_id : ''?>" name="paper_id">
                        <div class="bg-warning p-5 text-center align-middle mb-5" >
                            <p class="m-auto"><strong>Important Notice: </strong>  If you do not have an upload yet, still finalize your submission to confirm your entry.</p>
                        </div>
                        <div class="row">
                            <div class="col mt-2">
                                <div id="questionDiv_division">
                                    <div class="title">
                                        <span class="text-danger">*</span>
                                        Division
                                    </div>
                                    <div class="subtitle">Please choose the division that best describes your submission:</div>
                                    <select name="division" id="division" class="form-select required">
                                        <option value=""> -- Select One --</option>

                                        <!--        ########   Options are Fetched from database divisions    ############       -->
                                        <?php if(!empty($divisions)):
                                            foreach ($divisions as $division):
                                        ?>
                                        <option value="<?=$division->division_id?>" <?=(isset($paper) && $paper->division_id == $division->division_id ? 'selected':'')?> ><?=$division->name?></option>
                                        <?php endforeach;
                                        endif?>
                                        <!-- Remaining options -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col">
                                <div id="questionDiv_presPref">
                                    <div class="title">
                                        <span class="text-danger">*</span>
                                        Paper Type
                                    </div>
                                    <div class="subtitle">Please choose one of the following:</div>
                                    <div class="form-check">

                                    <!--        ##########   Options are Fetched from database Paper Type    #############      -->
                                        <?php if(!empty($paper_type)):
                                            foreach ($paper_type as $type):
                                                if($type->id !== '4'):
                                                ?>
                                                <input type="radio" value="<?=$type->id?>" id="paper_type_<?=$type->id?>" name="paper_type" class="form-check-input required" <?=(isset($paper) && $paper->type_id == $type->id? 'checked':'')?> >
                                                <label class="form-check-label" for="paper_type_<?=$type->id?>"><?=$type->name?></label>
                                            <br>
                                            <?php endif; endforeach;
                                        endif?>
                                    </div>
                                    <!-- Remaining radio options -->
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col">
                                <div id="questionDiv_title">
                                    <div class="title">
                                        <span class="text-danger">*</span>
                                        Paper Title
                                    </div>
                                    <div class="subtitle">(Max. 30 Words)</div>
                                    <div class="form-group">
                                        <textarea name="title" id="title" cols="30" rows="3" limit="30" class="summernote countText form-control ckHtmlField required" ><?=(isset($paper) && $paper->title ? $paper->title:'')?></textarea>
                                        <span id="titleWordsCount" ></span> <label for="title" >word(s)</label>
                                        <div id="titleWordsCountExceeded" class="text-danger d-none fw-bolder wordsExceed"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col">
                                <div id="questionDiv_summary">
                                    <div class="title">
                                        <span class="text-danger">*</span>
                                        Paper Summary
                                    </div>
                                    <div class="subtitle">(Maximum 150 words)</div>
                                    <div class="form-group">
                                        <textarea name="summary" id="summary" cols="30" rows="5" limit="150" class="summernote countText form-control ckHtmlField required" ><?=(isset($paper) && $paper->summary ? $paper->summary:'')?></textarea>
                                        <span id="summaryWordsCount" ></span> <label for="title" >word(s)</label>
                                        <div id="summaryWordsCountExceeded" class="text-danger d-none fw-bolder wordsExceed"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col">
                                <div id="questionDiv_paper_ijmc">
                                    <div class="title">
                                        <span class="text-danger">*</span>
                                        Are you interested in submitting this paper to IJMC as well?
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" value="2" name="is_interested" class="form-check-input required" id="is_submitted" <?=(isset($paper) && $paper->is_ijmc_interested == 2? 'checked':'')?>>
                                        <label class="form-check-label" for="is_submitted">I have already submitted this paper to IJMC</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" value="1" name="is_interested" class="form-check-input required" id="is_interested_yes" <?=(isset($paper) && $paper->is_ijmc_interested == 1? 'checked':'')?>>
                                        <label class="form-check-label" for="is_interested_yes">I am interested in submitting this paper to IJMC</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" value="0" name="is_interested" class="form-check-input required" id="is_interested_no" <?=(isset($paper) && $paper->is_ijmc_interested == 0? 'checked':'')?>>
                                        <label class="form-check-label" for="is_interested_no">I am NOT interested in submitting this paper to IJMC</label>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-warning p-2 mt-4">
                                <p class="m-auto">
                                    NEW DISCLAIMER: DO NOT SUBMIT TO IJMC UNTIL YOUR PAPER HAS BEEN FINALIZED THROUGH AFS TRANSACTIONS.
                                </p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col">
                                <?php if(isset($is_edit) && $is_edit == 1): ?>
                                <div><input type="submit" id="updatePapers" value="Update and Continue" class="btn btn-primary"></div>
                                <?php else:?>
                                <div><input type="submit" id="savePapers" value="Save and Continue" class="btn btn-primary"></div>
                                <?php endif ?>
                            </div>
                        </div>
            </form>
<!--            #########################           -->
        </div>
    </div>
</main>

<script>
    let totalWordsCount = 0;
    let userID = `<?=session('user_id')??''?>`;

    $(function(){
        $('.summernote').summernote({
            tabsize: 2,
            height: 120,
            toolbar: [
                ['font', ['bold', 'italic', 'underline', 'clear', 'superscript', 'subscript']],
            ]
            ,callbacks: {
                onKeyup: function(e) {
                    let idProp = $(this).attr('id');
                    let limit =  $(this).attr('limit');
                    if( parseInt(countWords($(this).val())) > parseInt(limit) ){
                        $('#'+idProp+'WordsCountExceeded').removeClass('d-none').html(limit+' words limit exceeded!!')
                    }else{
                        $('#'+idProp+'WordsCountExceeded').addClass('d-none').html('')
                    }
                    $('#'+idProp+'WordsCount').html(countWords($(this).val()))
                    $('#totalWordsCount').html(countTotalWords())
                }
            },
            disableEnter: true,
            enterHtml: '',
        });
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
        let words = str.split(' ');

        // Count the number of words
        let wordCount = words.length;

        return wordCount;
    }
</script>
