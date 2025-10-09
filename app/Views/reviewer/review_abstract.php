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

    .parent {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        grid-template-rows: repeat(5, 1fr);
        grid-column-gap: 0px;
        grid-row-gap: 0px;
    }

    .div1 { grid-area: 1 / 1 / 2 / 3; }
    .div2 { grid-area: 2 / 1 / 3 / 3; }
    .div3 { grid-area: 3 / 1 / 4 / 3; }
    .div4 { grid-area: 1 / 3 / 2 / 4; }
    .div5 { grid-area: 2 / 3 / 4 / 4; }
    .div6 { grid-area: 4 / 1 / 5 / 3; }
    .div7 { grid-area: 5 / 1 / 6 / 3; }
    .div8 { grid-area: 4 / 3 / 6 / 4; }

    .customTd{
        width: 30%;
        vertical-align: top;
        text-align: right;
        padding-right: 30px;
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
                            <td class="customTd">Abstract ID : </td>
                            <td>
                                <?= $abstracts->id ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="customTd">Abstract Title : </td>
                            <td>
                                <?= strip_tags($abstracts->title) ?>
                            </td>
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
                    <?php if(isset($abstracts->file_uploads) && !empty($abstracts->file_uploads)) : ?>
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
                            <td class="customTd">Paper Title:</td>
                            <td>
                                <?= strip_tags($abstracts->title) ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="customTd">Session Type:</td>
                            <td>
                                <?= ($abstracts->type_name) ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="customTd">Basic Science Format:</td>
                            <td>
                                <?= ($abstracts->basic_science_format) ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="customTd">Category:</td>
                            <td>
                                <?= $abstract_categories[($abstracts->abstract_category)]; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="customTd">Sub Categories:</td>
                            <td>
                                <?php if(!empty($abstract_sub_categories) && !empty($abstracts->abstract_subcategories)): ?>
                                <?php $subCategories = json_decode($abstracts->abstract_subcategories);
                                    foreach ($subCategories as $index => $subCategory){
                                        echo $abstract_sub_categories[$subCategory]. ($index < count($subCategories) - 1 ? ', ' : '');
                                    }
                                ?>
                                <?php endif ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="customTd">Hypothesis:</td>
                            <td>
                                <?= ($abstracts->hypothesis) ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="customTd">Study Design:</td>
                            <td>
                                <?= ($abstracts->study_design) ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="customTd">Introduction:</td>
                            <td>
                                <?= ($abstracts->introduction) ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="customTd">Methods:</td>
                            <td>
                                <?= ($abstracts->methods) ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="customTd">Results:</td>
                            <td>
                                <?= ($abstracts->results) ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="customTd">Conclusions:</td>
                            <td>
                                <?= ($abstracts->conclusions) ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="customTd">Additional Notes:</td>
                            <td>
                                <?= ($abstracts->additional_notes) ?>
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
                            <h6 class="mt-2"><span class="text-danger">*</span> Please rate the abstract from 1-5 (A Score of 1 is the best score, 5 is the worst score) in each of the three categories below. Full instructions can be found <a href="<?=base_url('assets/documents/reviewers/IMAST26_Abstract_Reviewer_Instructions_OneWorld.pdf')?>">here</a>.</h6>
                            <div class="card shadow-sm mt-3">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0 text-center fw-bolder">Rating Scale</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col">
                                            <div class="border rounded p-3 bg-success bg-opacity-10">
                                                <h4 class="text-success fw-bold">1</h4>
                                                <p class="mb-0 text-muted">Excellent</p>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="border rounded p-3 bg-warning bg-opacity-10">
                                                <h4 class="text-warning fw-bold">2 - 3</h4>
                                                <p class="mb-0 text-muted">Average</p>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="border rounded p-3 bg-danger bg-opacity-10">
                                                <h4 class="text-danger fw-bold">4 - 5</h4>
                                                <p class="mb-0 text-muted">Poor</p>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="border rounded p-3 bg-primary bg-opacity-10">
                                                <h4 class="text-primary fw-bold">N/A</h4>
                                                <p class="mb-0 text-muted">Not Applicable</p>
                                            </div>
                                        </div>

                                        <div class="col">
                                            <div class="border rounded p-3 bg-danger bg-opacity-10">
                                                <h4 class="text-danger fw-bold">COI</h4>
                                                <p class="mb-0 text-muted">Conflict of Interest</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                                function populateOptions($selected = null){
                                    $options = '<option value=""> -- Select --</option>';
                                    $options .= '<option value="1" '.($selected == 1 ? 'selected' : '').' > 1 </option>';
                                    $options .= '<option value="2" '.($selected == 2 ? 'selected' : '').' > 2 </option>';
                                    $options .= '<option value="3" '.($selected == 3 ? 'selected' : '').' > 3 </option>';
                                    $options .= '<option value="4" '.($selected == 4 ? 'selected' : '').' > 4 </option>';
                                    $options .= '<option value="5" '.($selected == 5 ? 'selected' : '').' > 5 </option>';
                                    $options .= '<option value="n/a" '.(strtolower($selected) == 'n/a' ? 'selected' : '').' > N/A </option>';
                                    $options .= '<option value="coi" '.(strtolower($selected) == 'coi' ? 'selected' : '').' > COI </option>';
                                    return $options;
                                }
                            ?>
                            <table>
                                <tbody>
                                <tr>
                                    <td>
                                        <div style="margin-left:5px"><strong>1.	Quality of Content</strong></div>
                                        <div style="margin-left:20px;margin-bottom:5px"><strong><font color="red">*</font></strong>Is the paper free of commercialism?</div>
                                        <div style="margin-left:160px;">
                                            <select name="review_question_1" id="review_question_1" class="requiredSelect form-control border border-primary abstractReviewsScores">
                                                <?= !empty($abstract_reviews) ? populateOptions($abstract_reviews['review_question_1']) : populateOptions() ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="margin-left:5px"><strong>2.	Study Design</strong></div>
                                        <div style="margin-left:20px;margin-bottom:5px"><strong><font color="red">*</font></strong>Is the paper free of commercialism?</div>
                                        <div style="margin-left:160px;">
                                            <select name="review_question_2" id="review_question_2" class="requiredSelect form-control border border-primary abstractReviewsScores">
                                                <?= !empty($abstract_reviews) ? populateOptions($abstract_reviews['review_question_2']) : populateOptions() ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="margin-left:5px"><strong>3.	Innovation </strong></div>
                                        <div style="margin-left:20px;margin-bottom:5px"><strong><font color="red">*</font></strong>Is the paper free of commercialism?</div>
                                        <div style="margin-left:160px;">
                                            <select name="review_question_3" id="review_question_3" class="requiredSelect form-control border border-primary abstractReviewsScores">
                                                <?= !empty($abstract_reviews) ? populateOptions($abstract_reviews['review_question_3']) : populateOptions() ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3">
                            <label for="averageScore" class="fw-bolder">Total Score: </label>
                            <div class="ms-5" style="max-width: 200px">
                                <input type="text" name="total_score" value="" id="total_score" class="form-control">
                            </div>
                            <div class="fw-bolder text-danger m-3 p-3" style="border: 2px dotted red; text-align: center; ">
                                Note: We recommend that you save your work intermittently, by clicking on the 'Save' button at the bottom
                                of the page.
                            </div>
                        </div>

                        <div class="p-3">
                            <div class="card">
                                <div class="card-header">
                                    <b>Reviewer Comments</b>
                                </div>
                                <div class="card-body" style="padding: 0 !important;">
                                    <textarea class="form-control requiredText" name="reviewer_comment" cols="115" rows="3" id="reviewer_comment" placeholder="Start typing here..."><?=(!empty($abstract_reviews)?$abstract_reviews['reviewer_comment'] !== "" ? $abstract_reviews['reviewer_comment']:'':'')?></textarea>
                                </div>
                            </div>
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
    let reviewer_id = "<?= $reviewer_id ?>"
    $(function () {
        // checkShowHide();

        $('#commercialism').on('change', function(){
            if($(this).val() == "No"){
                $('#freeCommercial').css('display', 'block')
            }else
                $('#freeCommercial').css('display', 'none')
        })

        $('.abstractReviewsScores').on('click change input', function () {
            totalScores();
        })

        $('select').on('change', function(){
            $(this).removeClass('border-danger');
        });

        $('textarea').on('input', function(){
            $(this).removeClass('border-danger');
        })
    })

    window.onload = function(){
        totalScores();
    }

    function totalScores(){
        let sum = 0;
        $('.abstractReviewsScores').each(function () {
            var value = parseFloat($(this).val());
            if (!isNaN(value)) {
                sum += value;
            }
        });
        $('input[name="total_score"]').val(parseInt(sum));
    }

    function submitFormReview() {
        let vote_error = 0;
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