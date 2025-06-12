<link href="<?= base_url() ?>/assets/css/event/landing.css" rel="stylesheet">

<?php echo view('reviewer/common/menu'); ?>
<?php //print_r($abstract_reviews); exit;?>
<style>
    required {
        color: red;
    }

    /* tr > td:first-child{
    line-height:3.0;
 } */
    table {
        width: 100%;
    }

    table tr td {
        padding-top: 20px !important;
        /* border: 1px solid black; */
        vertical-align: top;
    }

    .card-body {
        padding-left: 20px !important;
    }

    input {
        border: 2px solid;
    }

    .inputScore{
        max-width:300px;
    }
</style>
<?php //print_r($abstract_reviewer_uploads);exit;?>
<main>
    <div class="container">
        <?php echo view('admin/common/shortcut_link'); ?>
        <div class="card p-3">
            <div class="card">
                <div class="card-header">
                    <h6> General Information </h6>
                </div>
                <div class="card-body">
                    <table>
                        <tr>
                        <td style=" width:30%; vertical-align:top; text-align: right; padding-right: 30px;">Abstract ID : </td>
                        <td><?= $abstracts->id ?? '' ?></td>
                        <td style=" width:30%; vertical-align:top; text-align: right; padding-right: 30px;">Abstract Title : </td>
                        <td><?= strip_tags($abstracts->title ?? '') ?></td>
                        </tr>
                    </table>

                </div>
            </div>

            <div class="card mt-2">
                <div class="card-header">
                    Uploaded Files
                </div>
                <div class="card-body">
                    <p>(The most recent uploaded file will appear at the top of the list) </p>
                    <?php if(isset($abstracts) && !empty($abstracts->file_uploads)) : ?>
                        <?php foreach($abstracts->file_uploads as $file):
                            ?>
                            <a href="<?=base_url().$file['file_path'].$file['file_name'] ?>"  ><?=$file['file_preview_name']?></a><br>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p> None </p> <br>
                    <?php endif ?>
                </div>
            </div>
            <div class="card mt-2">
                <div class="card-header">
                    <h6> Paper Information </h6>
                </div>
                <div class="card-body">
                    <table id="abstractInformationTable">
                        <tbody>
                        <tr>
                            <td style=" width:30%; vertical-align:top; text-align: right; padding-right: 30px;">Division:</td>
                            <td>
                                <?= ($abstracts->division_name) ?>
                            </td>
                        </tr>
                        <tr>
                            <td style=" width:30%; vertical-align:top; text-align: right; padding-right: 30px;">Paper Type:</td>
                            <td>
                                <?= ($abstracts->type_name) ?>
                            </td>
                        </tr>
                        <tr>
                            <td style=" width:30%; vertical-align:top; text-align: right; padding-right: 30px;">Paper Title:</td>
                            <td>
                                <?= strip_tags($abstracts->title) ?>
                            </td>
                        </tr>
                        <tr>
                            <td style=" width:30%; vertical-align:top; text-align: right; padding-right: 30px;">Paper Summary:</td>
                            <td>
                                <?= ($abstracts->summary) ?>
                            </td>
                        </tr>
                        <tr>
                            <td style=" width:30%; vertical-align:top; text-align: right; padding-right: 30px;">Are you interested in submitting this paper to IJMC as well?::</td>
                            <td>
                                <?= ($abstracts->is_ijmc_interested == 1)? 'Yes' : 'No' ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card  mt-2 table-responsive">
                <div class="card-header">
                    <h6> General Information </h6>
                </div>
                    <form id="formReviewData">
                        <div class="p-2">
                            <table>
                                <tbody><tr>
                                    <td>
                                        <div style="margin-left:5px"><strong>Commercialism?</strong></div>
                                        <div style="margin-left:20px;margin-bottom:5px"><strong><font color="red">*</font></strong>Is the paper free of commercialism?</div>
                                        <div style="margin-left:160px;">
                                            <select name="commercialism" id="commercialism" class="requiredSelect form-control border border-primary">
                                                <option value=""> -- Select --</option>
                                                <option value="Yes" <?=(!empty($abstract_reviews)?$abstract_reviews['commercialism'] == 'Yes' ? 'selected':'':'')?>>Yes</option>
                                                <option value="No" <?=(!empty($abstract_reviews)?$abstract_reviews['commercialism'] == 'No' ? 'selected':'':'')?>>No</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="freeCommercial" id="freeCommercial" style="display:<?=(!empty($abstract_reviews)?$abstract_reviews['commercialism'] == 'No' ? 'block':'none':'none')?>">
                                    <td>
                                        <div style="margin-left:20px;margin-bottom:5px"> <font color="red">*</font>If the paper does have some commercialism, can it be edited out without destroying the content of the paper?</div>
                                        <div style="margin-left:160px;">
                                            <select name="commercialismEdit" id="commercialismEdit" class="form-control border border-primary <?=(!empty($abstract_reviews)?$abstract_reviews['commercialism'] == 'No' ? 'requiredSelect':'':'')?>">
                                                <option value=""> -- Select --</option>
                                                <option value="Yes" <?=(!empty($abstract_reviews)?$abstract_reviews['commercialism_editable'] == 'Yes' ? 'selected':'':'')?>>Yes</option>
                                                <option value="No" <?=(!empty($abstract_reviews)?$abstract_reviews['commercialism_editable'] == 'No' ? 'selected':'':'')?>>No</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div style="margin-left:5px"><strong>Operations</strong></div>
                                        <div style="margin-left:20px;margin-bottom:5px"><strong><font color="red">*</font></strong>Is this paper most relevent for foundry operations or marketing and management audience operations.</div>
                                        <div style="margin-left:160px;">
                                            <select name="operations" id="operations" class="requiredSelect form-control border border-primary">
                                                <option value=""> -- Select -- </option>
                                                <option value="Foundry Operation" <?=(!empty($abstract_reviews)?$abstract_reviews['operations'] == 'Foundry Operation' ? 'selected':'':'')?>  >Foundry Operation</option>
                                                <option value="Marketing and Management"  <?=(!empty($abstract_reviews)?$abstract_reviews['operations'] == 'Marketing and Management' ? 'selected':'':'')?> >Marketing and Management</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div style="margin-left:5px"><strong>Management/Marketing</strong></div>
                                        <div style="margin-left:20px;margin-bottom:5px"><strong><font color="red">*</font></strong>Rate the paper for its value in the following area of the foundry?
                                            <br>( 1 = of the little interest/value and 5 = high level of interest/value )</div>
                                        <div style="margin-left:160px;">
                                            <select name="marketingScore" id="marketingScore" class="requiredSelect form-control border border-primary">
                                                <option value=""> -- Select --</option>
                                                <?php for($i = 1; $i < 6; $i++): ?>
                                                    <option value="<?=$i?>"  <?=(!empty($abstract_reviews)?$abstract_reviews['marketing_score'] == $i ? 'selected':'':'')?> ><?=$i?></option>
                                                <?php endfor ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div style="margin-left:5px"><strong>Technology/Research</strong></div>
                                        <div style="margin-left:20px;margin-bottom:5px"><strong><font color="red">*</font></strong>Rate the paper for its value in the following area of the foundry?
                                            <br>( 1 = of the little interest/value and 5 = high level of interest/value )</div>
                                        <div style="margin-left:160px;">
                                            <select name="researchScore" id="researchScore" class="requiredSelect form-control border border-primary">
                                                <option value=""> -- Select --</option>
                                                <?php for($i = 1; $i < 6; $i++): ?>
                                                    <option value="<?=$i?>"  <?=(!empty($abstract_reviews)?$abstract_reviews['research_score'] == $i ? 'selected':'':'')?> ><?=$i?></option>
                                                <?php endfor ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="margin-left:5px"><strong>Professional Level</strong></div>
                                        <div style="margin-left:20px;margin-bottom:5px"><strong><font color="red">*</font></strong>What is the professional technical level of this paper? Is it aimed at a higher technical and research level or more shop floor operational level?</div>
                                        <div style="margin-left:160px;">
                                            <select name="professionalLevel" id="professionalLevel" class="requiredSelect form-control border border-primary">
                                                <option value=""> -- Select --</option>
                                                <option value="Technical and Research" <?=(!empty($abstract_reviews)?$abstract_reviews['professional_level'] == "Technical and Research" ? 'selected':'':'')?>>Technical and Research</option>
                                                <option value="Shop Floor" <?=(!empty($abstract_reviews)?$abstract_reviews['professional_level'] == "Shop Floor" ? 'selected':'':'')?>>Shop Floor</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="margin-left:5px"><strong>1. Originality</strong></div>
                                        <div style="margin-left:20px;margin-bottom:5px"><strong><font color="red">*</font></strong>Rate the paper on the originality of its content
                                            <br> ( 5 = new and original work,theory or intervention 1= paper that repeats or uses the work of others )</div>
                                        <div style="margin-left:160px;">
                                            <select name="originalityScore" id="originalityScore" class="requiredSelect form-control border border-primary selectScore" >
                                                <option value=""> -- Select --</option>
                                                <?php for($i = 1; $i < 6; $i++): ?>
                                                    <option value="<?=$i?>"  <?=(!empty($abstract_reviews)?$abstract_reviews['originality_score'] == $i ? 'selected':'':'')?> ><?=$i?></option>
                                                <?php endfor ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="margin-left:5px"><strong>2. Sufficiency of Data/Information</strong></div>
                                        <div style="margin-left:20px;margin-bottom:5px"><strong><font color="red">*</font></strong>Is there sufficient data and information given to justify the conclusions?
                                            <br>( 5 = more than enough data and information to justify the conclusions 1 = insufficient data to justify the conclusions made )</div>
                                        <div style="margin-left:160px;">
                                            <select name="sufficiencyScore" id="sufficiencyScore" class="requiredSelect form-control border border-primary selectScore" >
                                                <option value=""> -- Select --</option>
                                                <?php for($i = 1; $i < 6; $i++): ?>
                                                    <option value="<?=$i?>"  <?=(!empty($abstract_reviews)?$abstract_reviews['sufficiency_score'] == $i ? 'selected':'':'')?> ><?=$i?></option>
                                                <?php endfor ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="margin-left:5px"><strong>3. Readability</strong></div>
                                        <div style="margin-left:20px;margin-bottom:5px"><strong><font color="red">*</font></strong>Rate the paper on the readability of the text
                                            <br>( 5 =very understandable,easy to read, logical presentation.  1= Poorly written, difficult to understand the concepts presented )</div>
                                        <div style="margin-left:160px;">
                                            <select name="readabilityScore" id="readabilityScore" class="requiredSelect form-control border border-primary selectScore" >
                                                <option value=""> -- Select --</option>
                                                <?php for($i = 1; $i < 6; $i++): ?>
                                                    <option value="<?=$i?>"  <?=(!empty($abstract_reviews)?$abstract_reviews['readability_score'] == $i ? 'selected':'':'')?> ><?=$i?></option>
                                                <?php endfor ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="margin-left:5px"><strong>4. Artwork</strong></div>
                                        <div style="margin-left:20px;margin-bottom:5px"><strong><font color="red">*</font></strong>Rate the paper for the quality and quantity of the charts,graphs and pictures.
                                            <br>( 5 = graphs and artwork are of excellent quality and greatly illustrate and enhance the text. 1= Graphs and artwork are of very poor quality and /or do little to help explain the text. )</div>
                                        <div style="margin-left:160px;">
                                            <select name="artworkScore" id="artworkScore" class="requiredSelect form-control border border-primary selectScore" >
                                                <option value=""> -- Select --</option>
                                                <?php for($i = 1; $i < 6; $i++): ?>
                                                    <option value="<?=$i?>"  <?=(!empty($abstract_reviews)?$abstract_reviews['artwork_score'] == $i ? 'selected':'':'')?> ><?=$i?></option>
                                                <?php endfor ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="margin-left:5px"><strong>5. Composite score</strong></div>
                                        <div style="margin-left:20px;margin-bottom:5px"><strong><font color="red">*</font></strong>Give your recommendation on whether this paper should be published and presented. The composite score is not an average of the categories above. This should reflect your best evaluation of the quality of this paper and value for publication.
                                            <br>( 5 = highest quality paper. 1 = paper should not be published. )</div>
                                        <div style="margin-left:160px;">
                                            <select name="compositeScore" id="compositeScore" class="requiredSelect form-control border border-primary">
                                                <option value=""> -- Select --</option>
                                                <?php for($i = 1; $i < 6; $i++): ?>
                                                    <option value="<?=$i?>"  <?=(!empty($abstract_reviews)?$abstract_reviews['composite_score'] == $i ? 'selected':'':'')?> ><?=$i?></option>
                                                <?php endfor ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>

                                </tbody>
                            </table>
                        </div>

                        <div class="p-3">
                            <label for="averageScore">Average Score: </label>
                            <div class="ms-5" style="width: 400px">
                                <input type="text" name="averageScore" value="" id="averageScore" class="form-control">
                            </div>

                            <small class="text-danger">(Average score is based on items #1~4 above and does not include the score for #5)</small>
                        </div>

                        <div class="fw-bolder text-danger m-3 p-3" style="border: 2px dotted red; text-align: center; ">
                            Note: We recommend that you save your work intermittently, by clicking on the 'Save' button at the bottom
                            of the page.
                        </div>

                        <div class="p-3">
                            <div class="card">
                                <div class="card-header">
                                    <b>Reviewer Comments (Suggested Revisions):</b>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        Please enter into this box any comments regarding your evaluation of this paper.
                                        Also provide any comments, concerns, and recommendations to improve the paper or help clarify particular issues within the paper.
                                    </p>
                                    <textarea class="form-control requiredText" name="suggested_revision_comment" cols="115" rows="3" id="suggested_revision_comment" placeholder="Start typing here..."><?=(!empty($abstract_reviews)?$abstract_reviews['suggested_revision_comment'] !== "" ? $abstract_reviews['suggested_revision_comment']:'':'')?></textarea>

                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <b>Reviewer Comments (Required Revisions):</b>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        Enter into this box any concerns recommendations or suggestions that are serious enough to prohibit the publication of the paper,
                                        and must be revised before it is published.
                                    </p>
                                    <textarea class="form-control requiredText mt-3" name="required_revision_comment" cols="115" rows="3" id="required_revision_comment" placeholder="Start typing here..."><?=(!empty($abstract_reviews)?$abstract_reviews['required_revision_comment'] !== "" ? $abstract_reviews['required_revision_comment']:'':'')?></textarea>
                                </div>

                                <div class="m-3 ms-5 card <?=(!empty($abstract_reviews)?$abstract_reviews['submitter_comment_on_upload'] !== "" ? 'd-block':'d-none':'d-none')?>"  >
                                    <div class="card-header"><label for="re_review_comment" class="fw-bolder">Submitter Comment:</label></div>
                                    <div class="card-body">
                                        <label class=""><?=(!empty($abstract_reviews)?$abstract_reviews['submitter_comment_on_upload'] !== "" ? $abstract_reviews['submitter_comment_on_upload']:'':'')?></label>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title fw-bolder">Re-Review Comments:</h5>
                                </div>
                                <div class="card-body p-0" style="padding:0 !important">
                                    <textarea class="form-control requiredText" name="re_review_comment" cols="115" rows="3" id="re_review_comment" placeholder="Start typing here..."><?=(!empty($abstract_reviews)?$abstract_reviews['re_review_comment'] !== "" ? $abstract_reviews['re_review_comment']:'':'')?></textarea>
                                </div>
                            </div>


                        </div>


                        <div class="mt3 p-3 uploadDiv" style="display:<?=!empty($abstract_reviewer_uploads)?'none':'block'?>">
                            <label>Upload File: </label>
                            <input type="file" name="uploadFile" id="uploadFile" accept="" class="form-control">

                            <a class="btn uploadReviewBtn btn-info mt-2" style="width:200px" id="uploadReviewBtn">Upload</a>

                        </div>
                        <div class="filePreviewDiv p-3" style="display:<?=!empty($abstract_reviewer_uploads)?'block':'none'?>">
                            Uploaded File : <a id="filePreviewLink" href="<?=!empty($abstract_reviewer_uploads)?base_url().$abstract_reviewer_uploads['file_path'].$abstract_reviewer_uploads['file_name']:'#'?>"><?=!empty($abstract_reviewer_uploads)?$abstract_reviewer_uploads['file_preview_name']:''?></a>
                        </div>

                        <div class="card mt-2">
                            <div class="card-header"> Final Approval: </div>
                            <div class="card-body">
                                <input type="radio" name="final_approval" id="finalApprovalYes" value="1" <?=(!empty($abstract_reviews)? ($abstract_reviews['is_approved'] !== "" && $abstract_reviews['is_approved'] == 1) ? 'checked':'':'')?>> <label for="finalApprovalYes"> I approve</label>
                                <input type="radio" name="final_approval" id="finalApprovalNo" value="2" <?=(!empty($abstract_reviews)? ($abstract_reviews['is_approved'] !== "" && $abstract_reviews['is_approved'] == 2) ? 'checked':'':'')?> class="ms-5"> <label for="finalApprovalNo"> I still have concerns</label>
<!--                                <div class="" id="concernCommentDiv" style="display: --><?php //=(!empty($abstract_reviews)? ($abstract_reviews['is_approved'] !== "" && $abstract_reviews['is_approved'] == 1) ? 'block':'none':'none')?><!--">-->
<!--                                    <textarea id="concernComment" class="form-control" name="concernComment" placeholder="Type here..."></textarea>-->
<!--                                </div>-->
                            </div>
                        </div>
                    </form>  <!-- End of the form -->

                        <div class="p-3 small text-danger bg-warning bg-opacity-50 m-2">
                            ANY DOCUMENTS UPLOADED SHOULD NOT INCLUDE YOUR NAME DIGITALLY UNLESS YOU WANT TO FORGO ANONYMITY. PEN MARK UPS ARE PREFERRED WHEN UTILIZING THIS FEATURE.
                        </div>


                </div>
            <div class="mt-4">
                <input type="button" value="Save" style="width:150px" class="btn btn-success mt-" onclick="submitFormReview();">
            </div>
        </div>
</main>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css"/>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
<script>
    let baseUrlReviewer = "<?= base_url() . '/reviewer/' ?>";
    let reviewer_id = "<?= $reviewer_id ?? '' ?>"
    $(function () {
        // checkShowHide();

        $('#commercialism').on('change', function(){
            if($(this).val() == "No"){
                $('#freeCommercial').css('display', 'block')
            }else
                $('#freeCommercial').css('display', 'none')
        })

        // $("#finalApprovalNo").on('click', function(){
        //     $('#concernCommentDiv').css('display', 'none');
        // })
        // $('#finalApprovalYes').on('click', function(){
        //     $('#concernCommentDiv').css('display', 'block');
        // })

        $('.selectScore').on('click change input', function () {
            let sum = 0;
            let average = 0;
            $('.selectScore').each(function () {
                var value = parseFloat($(this).val());
                if (!isNaN(value)) {
                    sum += value;
                }
            });
            average = sum/4;
            $('input[name="averageScore"]').val(average.toFixed(2))

        })

        $('select').on('change', function(){
            $(this).removeClass('border-danger');
        });

        $('textarea').on('input', function(){
            $(this).removeClass('border-danger');
        })

        $('#uploadReviewBtn').on('click', function(){
            let formData = new FormData();
            let paper_id = `<?=$abstracts->id?>`
            formData.append('uploadFile', $('#uploadFile')[0].files[0])
            formData.append('paper_id', paper_id)
            // console.log(formData);

            $.ajax({
                url: baseUrlReviewer + "uploadReviewerFile",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    data = JSON.parse(data)
                    if(data.status == '200') {
                        $('.uploadDiv').css('display', 'none')
                        $('#filePreviewLink').html('File Uploaded: '+data.data.file_preview_name)
                        $('.filePreviewDiv').css('display', 'block')
                    }
                }
            })
        })

    })

    window.onload = function(){
        $('.selectScore').each(function() {
            let sum = 0;
            let average = 0;
            $('.selectScore').each(function () {
                var value = parseFloat($(this).val());
                if (!isNaN(value)) {
                    sum += value;
                }
            });
            average = sum/4;
            $('input[name="averageScore"]').val(average.toFixed(2))

        })

    }

    function submitFormReview() {
        let vote_error = 0;
        let totalScore = $('input[name="averageScore"]').val()
        let missing_fields = 0;
        let requiredFieldsArray = [];
        let formData = new FormData(document.getElementById('formReviewData'));
        formData.append('abstract_id', abstract_id)
        formData.append('reviewer_id', reviewer_id)
        // console.log(formData)

        $('.selectScore').each(function () {
            if (parseFloat($(this).val()) > 5 || parseFloat($(this).val()) < 0 || $(this).val() == '') {
                vote_error = 1;
            }
        })

        $(".requiredSelect").each(function() {
            $this = $(this)
            if (!$(this).val()) {
                requiredFieldsArray.push($(this).attr('id'))
                missing_fields = 1;
                $(this).addClass(['border-danger'])
            }
        });


        $(".requiredText").each(function() {
            if($(this).val().trim() == ''){
                $(this).addClass('border-danger')
                return false;
            }
        });
        // return false;


        if (missing_fields > 0) {
            toastr.error('Please fill all required fields');
            console.log(requiredFieldsArray[0])
            window.location.href = "#" + requiredFieldsArray[0];
            $('html, body').animate({
                scrollTop: $('#' + requiredFieldsArray[0]).offset().top - 200
            }, 'fast');
            return false;
        }

        if (vote_error > 0) {
            toastr.warning('Rating/score can only be 1 to 5')
            return false;
        }



        $.ajax({
            url: baseUrlReviewer + "addReviewData",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                data = JSON.parse(data)
                if (data.status == 200) {
                    Swal.fire({
                        title: data.message,
                        text: 'Do you want to proceed to next paper?',
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, continue!',
                        cancelButtonText: 'No, Stay on Page!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.get(baseUrlReviewer + '/getNextReviewAbstract/' + abstract_id, function (nextAbstract) {
                                if (nextAbstract && (nextAbstract.length > 0 || nextAbstract !== null)) {
                                    let timerInterval
                                    Swal.fire({
                                        title: 'Saved',
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
                                        window.location.href = baseUrlReviewer + '/reviewAbstract/' + nextAbstract
                                    })

                                } else {
                                    let timerInterval
                                    Swal.fire({
                                        title: 'Saved',
                                        html: 'All abstracts have been reviewed, Thank you for your participation! <br> You will be automatically redirected to submission menu.',
                                        timer: 10000,
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
                                       
                                        window.location.href = baseUrlReviewer + '/abstract_list'
                                    })
                                }
                            }).done(function (response) {
                                // Handle a successful response

                            })
                                .fail(function (jqXHR, textStatus, errorThrown) {
                                    // Handle a failed response
                                    console.error(textStatus, errorThrown);
                                });
                        }
                    })
                }else if(data.status == 201){
                    Swal.fire({
                        title: 'Info',
                        text: data.message,
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, continue!'
                    }).then((result) => {
                        if (result.isConfirmed) {

                        }
                    })
                }
            }

        }, 'json')

    }
</script>