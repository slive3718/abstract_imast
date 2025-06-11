

<?php echo view('admin/common/menu'); ?>
<?php //=print_r(session('user_type'));exit;?>
<main>
    <div class="container pb-5">
        <?php echo view('admin/common/shortcut_link_detail_back'); ?>
        <div class="card p-lg-5 p-md-2 p-sm-1 p-xs-1 shadow">
            <?php
            $actionUrl = base_url('admin/update_individual_panel_ajax');
            ?>

            <form id="individualPanelForm"  action="<?= $actionUrl ?>" method="post">

                <input type="hidden" value="<?=(isset($paper_id) && !empty($paper_id))? $paper_id : ''?>" name="paper_id">
                <div class="bg-warning p-5 text-center align-middle mb-5" >
                    <p class="m-auto"><strong>Important Notice: </strong>  If you do not have an upload yet, still finalize your submission to confirm your entry.</p>
                </div>
                <div class="row">
                    <div class="col mt-2">
                            <div class="title">
                                Panel Overview Division :
                                <?php if(!empty($divisions)):
                                    foreach ($divisions as $division):
                                        ?>
                                       <?=(isset($paper) && $paper->division_id == $division->division_id ? $division->name:'')?>
                                    <?php endforeach;
                                endif?>
                            </div>
                        <span style="font-size:12px !important">(As submitted by the Panel Coordinator)</span>
                    </div>
                </div>

                <hr>
                <div class="row">
                    <div class="col">
                        <div id="questionDiv_title">
                            <div class="title">
                                Panel Overview Title : <?=(isset($paper) && ($paper->title) ? strip_tags($paper->title):'')?>
                            </div>
                        </div>
                        <span style="font-size:12px !important">(As submitted by the Panel Coordinator)</span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col">
                        <div id="questionDiv_summary">
                            <div class="title">
                                Panel Overview Brief Summary : <?=(isset($paper) && $paper->summary ? strip_tags($paper->summary):'')?>
                            </div>
                            <span style="font-size:12px !important">(As submitted by the Panel Coordinator)</span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col">
                        <div id="questionDiv_paper_ijmc">
                            <div class="title">
                                Panel Overview Are you interested in submitting this paper to IJMC as well?:
                                <?=(isset($paper) && $paper->is_ijmc_interested == 2? 'I have already submitted this paper to IJMC':'')?>
                                <?=(isset($paper) && $paper->is_ijmc_interested == 1? 'I am interested in submitting this paper to IJMC':'')?>
                                <?=(isset($paper) && $paper->is_ijmc_interested == 0? 'I am NOT interested in submitting this paper to IJMC':'')?>
                            </div>

                        </div>
                        <span style="font-size:12px !important">(As submitted by the Panel Coordinator)</span>
                    </div>
<!--                    <div class="bg-warning p-2 mt-4">-->
<!--                        <p class="m-auto">-->
<!--                            NEW DISCLAIMER: DO NOT SUBMIT TO IJMC UNTIL YOUR PAPER HAS BEEN FINALIZED THROUGH AFS TRANSACTIONS.-->
<!--                        </p>-->
<!--                    </div>-->
                </div>
                <hr>
                <div class="row">
                    <div class="col">
                        <div id="">
                            <div class="title">
                                Individual Panel Title
                            </div>

                            <textarea name="individual_panel_title" id="individual_panel_title" cols="30" rows="5" class="summernote countText form-control ckHtmlField required"><?=!empty($panelist)?$panelist['individual_panel_title']:''?></textarea>
                        </div>
                    </div>
                    <span style="font-size:12px !important">(Max. 30 Words)</span>
                    <span id="titleWordsCount" ></span> <label for="title" >word(s)</label>
                    <div id="titleWordsCountExceeded" class="text-danger d-none fw-bolder wordsExceed"></div>
                </div>
                <hr>

                <div class="row">
                    <div class="col">
                        <div><input type="submit" id="saveIndividualPanel" value="Save and Continue" class="btn btn-primary"></div>
                    </div>
                </div>
                <input type="hidden" name="panelist_paper_sub_id" value="<?=$panelist['id']?>">
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


        $('#individualPanelForm').validate({
            rules: {
                individual_panel_title: "required",
            },
            messages: {
                individual_panel_title: "Please fill individual panel title",
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "content") {
                    error.appendTo(element.parent()); // Appends error message after the Summernote field
                } else {
                    error.insertAfter(element); // Appends error message after the input field
                }
            },
            highlight: function(element) {
                $(element).addClass('error'); // Adds the 'error' class to the input field
            },
            unhighlight: function(element) {
                $(element).removeClass('error'); // Removes the 'error' class from the input field
            },
            submitHandler: function(form) {

                let totalWordsError = 0;
                let emptyError = 0;

                $('.wordsExceed').each(function() {
                    if (!$(this).hasClass('d-none')) {
                        totalWordsError = 1;
                    }
                });

                $(".summernote").each(function() {
                    if ($(this).summernote('isEmpty')) {
                        emptyError = 1;
                    }
                });

                if (totalWordsError > 0) {
                    swal.fire({
                        'title': 'Info',
                        'html': 'Total number of words should not exceed limit',
                        'icon': 'info',
                    });
                    return false;
                }

                if (emptyError > 0) {
                    swal.fire({
                        'title': 'Info',
                        'html': 'Required text field is empty',
                        'icon': 'info',
                    });
                    return false;
                }

                let formData = new FormData(form);
                let individual_panel_title = $('#individual_panel_title').val();

                formData.append("user_id", userID);

                Swal.fire({
                    title: "info",
                    showCancelButton: true,
                    confirmButtonText: "Save",
                    icon: 'info',
                    html: 'Can you confirm that your abstract title, '+individual_panel_title+' is in title case?  If not, click on ‘cancel’ to return to the page to edit your title.'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: $(form).attr('action'),
                            data: formData,
                            method: "POST",
                            processData: false, // Prevent jQuery from processing the data
                            contentType: false, // Prevent jQuery from setting contentType
                            success: function(response) {
                                response = JSON.parse(response);
                                // console.log(response.data.insert_id);
                                if (response.status == '200') {
                                    window.location.href = base_url+'/admin/view_individual_panel/'+response.data.individual_panel_id;
                                }else{
                                    toastr.error(response)
                                }
                            }
                        });
                    }
                });
                return false; // Prevent default form submission
            }
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
