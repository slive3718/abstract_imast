<link href="<?= base_url() ?>/assets/css/event/landing.css" rel="stylesheet">

<?php echo view('cme/common/menu'); ?>
<?php //print_r($abstract['paper']_reviews); exit;?>
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
<?php //print_r($abstract['paper']_reviewer_uploads);exit;?>
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
                                <?= $abstract['paper']['id'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="customTd">Abstract Title : </td>
                            <td>
                                <?= strip_tags($abstract['paper']['title']) ?>
                            </td>
                        </tr>
                    </table>

                </div>
            </div>

            <!--<div class="card mt-2">
                <div class="card-header">
                    Uploaded Files
                </div>
                <div class="card-body">
                    <p>(The most recent uploaded file will appear at the top of the list) </p>
                    <?php /*if(isset($abstract['paper']->file_uploads) && !empty($abstract['paper']->file_uploads)) : */?>
                        <?php /*foreach($abstract['paper']->file_uploads as $file):
                            */?>
                            <a href="<?php /*=base_url().$file['file_path'].$file['file_name'] */?>"  ><?php /*=$file['file_preview_name']*/?></a><br>
                        <?php /*endforeach; */?>
                    <?php /*else: */?>
                        <p> None </p> <br>
                    <?php /*endif */?>
                </div>
            </div>-->
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
                                <?php echo strip_tags($abstract['paper']['title']); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="customTd">Session Type:</td>
                            <td>
                                <?php echo $abstract['type_name']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="customTd">Basic Science Format:</td>
                            <td>
                                <?php echo $abstract['paper']['basic_science_format']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="customTd">Category:</td>
                            <td>
                                <?php echo $abstract['category']; ?>
                            </td>
                        </tr>

                        <?php if (!empty($abstract['sub_categories'])): ?>
                            <tr>
                                <td class="customTd">Sub Categories:</td>
                                <td>
                                   <?php if(!empty($abstract)): ?>
                                        <?php echo implode(', ', $abstract['sub_categories']); ?>
                                    <?php endif ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <tr>
                            <td class="customTd">Hypothesis:</td>
                            <td>
                                <?php echo $abstract['paper']['hypothesis']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="customTd">Study Design:</td>
                            <td>
                                <?php echo $abstract['paper']['study_design']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="customTd">Introduction:</td>
                            <td>
                                <?php echo $abstract['paper']['introduction']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="customTd">Methods:</td>
                            <td>
                                <?php echo $abstract['paper']['methods']; ?>
                            </td>
                        </tr>

                        <?php if ($abstract['paper']['results']): ?>
                            <tr>
                                <td class="customTd">Results:</td>
                                <td>
                                    <?php echo $abstract['paper']['results']; ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php if ($abstract['paper']['conclusions']): ?>
                            <tr>
                                <td class="customTd">Conclusions:</td>
                                <td>
                                    <?php echo $abstract['paper']['conclusions']; ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php if(!empty($paper_uploads)): ?>
                            <tr>
                                <td class="customTd" style="vertical-align: bottom">Image Caption :</td>
                                <td>
                                    <div>

                                        <?php if (!empty($paper_uploads)): ?>
                                            <?php foreach ($paper_uploads as $index => $uploads): ?>
                                                <div class="mb-3 text-center" style="width: 100px;">
                                                    <a href="<?= base_url($uploads['file_path'] . $uploads['file_name']) ?>" data-lightbox="image-<?= $abstract_id ?>">
                                                        <img src="<?= base_url($uploads['file_path'] . $uploads['file_name']) ?>" class="img-fluid d-block mx-auto" style="width: 100px;">
                                                    </a>
                                                </div>
                                                <a class="d-block small mt-1" href="<?= base_url($uploads['file_path'] . $uploads['file_name']) ?>" download="<?=$uploads['file_preview_name'] ?>">
                                                    <?= htmlspecialchars($uploads['file_preview_name']) ?>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <div>
                                            <?= htmlspecialchars($abstract['paper']['image_caption']) ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endif ?>

                        <?php if ($abstract['paper']['min_follow_up_period']): ?>
                            <tr>
                                <td class="customTd">Minimum time period to follow up:</td>
                                <td>
                                    <?php echo $abstract['paper']['min_follow_up_period']; ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php if ($abstract['paper']['is_srs_funded']): ?>
                            <tr>
                                <td class="customTd">Funded by SRS grant:</td>
                                <td>
                                    <?php echo $abstract['paper']['is_srs_funded']; ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div id="author-info-body" class="collapse show" aria-labelledby="author-info-header" data-bs-parent="#accordionExample">
                    <div class="card-body">
                        <table class="table" style="margin-bottom:0px !important">
                            <tbody>
                            <tr>
                                <td class="text-end" style="width:250px">
                                    Author List:
                                </td>
                                <td>
                                    <?php if($authors):
                                        foreach ($authors  as $index => $author):
                                            $mapDesignations = implode(', ',  $author['designations']);
                                            echo getAuthorTypeBadge($author['assigned_paper'], $index);
                                            ?>

                                            <?=$author['name'].' '.$author['surname'].' '. $mapDesignations?> <br>
                                        <?php endforeach; endif; ?>
                                </td>
                            </tr>
                            <?php if($authors):
                                foreach ($authors as $index => $author):
                                    $mapDesignations = implode(', ',  $author['designations']);
                                    ?>
                                    <tr >
                                        <td class="text-end">(<?=($index+1)?>) <?=($author['assigned_paper']['is_presenting_author'] == "Yes")? 'Presenting Author :':'Co-Author :'?></td>
                                        <td><strong><?=UcFirst($author['name']).' '.UcFirst($author['surname']) ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">Author Info: </td>
                                        <td >
                                            Email: <?=$author['email']?><br>
                                            Institution: <?= !empty($author['institution']) ? implode(', ', $author['institution']) : ''?><br>
                                            Work Phone: <?=$author['profile_data']['phone']?><br>
                                            Cell Phone: <?=$author['profile_data']['cellphone']?><br>
                                            <!--                                Fax: --><?php //=$author['fax']?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">Correspondence :</td>
                                        <td><?=($author['assigned_paper']['is_correspondent']  == 'Yes')?'Yes':'No'?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end">Disclosure Status :</td>
                                        <td>
                                            <?php if (!empty($author['signature_signed_date'])): ?>
                                                <?= (!empty($current_disclosure_date) && $author['signature_signed_date'] > $current_disclosure_date)
                                                    ? '<span class="badge bg-success">Current: '.$author['signature_signed_date'].'</span>'
                                                    : '<span class="badge bg-danger">Expired: '.$author['signature_signed_date'].'</span>' ?>
                                                <br>
                                                Financial Disclosure:
                                                <?= match($author['financial_relationship']) {
                                                    'Yes' => 'I have held a financial relationship with an ineligible company within the past 24 months.',
                                                    'No' => 'I have held NO financial relationship(s) with an ineligible company within the past 24 months.',
                                                    default => ''
                                                } ?>
                                            <?php else: ?>
                                                <span class="badge bg-warning">None</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><br></td>
                                    </tr>

                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card  mt-2 table-responsive">
                <div class="card-header">
                    <h6> Rating </h6>
                </div>
                    <div class="card-body">
                        <p class="mb-4">
                            According to the guidelines set forth by the Accreditation Council for Continuing Medical Education (ACCME), financial relationships are relevant if the educational content an individual can control is related to the business lines of an ineligible company.* All relevant financial relationships must be mitigated prior to an education activity.
                        </p>
                        <p class="text-muted fst-italic">
                            *An ineligible company is one whose primary business is producing, marketing, selling, re-selling, or distributing healthcare products used by or on patients.
                        </p>

                        <form id="formCMRating">
                        <div class="mt-4">
                            <h3 class="h6 fw-bold border-bottom pb-2 mb-3">Identifying Relevant Financial Relationships</h3>
                            <div class="form-group mb-3">
                                <label class="form-label d-block">
                                    Is the content of this abstract related to the products or business lines of a disclosed ineligible company?
                                </label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="relevantRelation" id="relevantYes" value="yes">
                                    <label class="form-check-label" for="relevantYes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="relevantRelation" id="relevantNo" value="no">
                                    <label class="form-check-label" for="relevantNo">No</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="relevantRelation" id="relevantNA" value="na">
                                    <label class="form-check-label" for="relevantNA">N/A</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h3 class="h6 fw-bold border-bottom pb-2 mb-3">Commercial Bias</h3>

                            <div class="form-group mb-3">
                                <label class="form-label d-block">
                                    Does the content promote improvements or quality in healthcare and not a specific proprietary business interest of an ineligible company?
                                </label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="commercialBias1" id="bias1Yes" value="yes">
                                    <label class="form-check-label" for="bias1Yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="commercialBias1" id="bias1No" value="no">
                                    <label class="form-check-label" for="bias1No">No</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="commercialBias1" id="bias1NA" value="na">
                                    <label class="form-check-label" for="bias1NA">N/A</label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label d-block">
                                    Does the content use generic names of products and/or comparable trade names from several companies rather than one ineligible company?
                                </label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="commercialBias2" id="bias2Yes" value="yes">
                                    <label class="form-check-label" for="bias2Yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="commercialBias2" id="bias2No" value="no">
                                    <label class="form-check-label" for="bias2No">No</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="commercialBias2" id="bias2NA" value="na">
                                    <label class="form-check-label" for="bias2NA">N/A</label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label d-block">
                                    Is the content balanced on its discussion of therapeutic options and products?
                                </label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="commercialBias3" id="bias3Yes" value="yes">
                                    <label class="form-check-label" for="bias3Yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="commercialBias3" id="bias3No" value="no">
                                    <label class="form-check-label" for="bias3No">No</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="commercialBias3" id="bias3NA" value="na">
                                    <label class="form-check-label" for="bias3NA">N/A</label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label d-block">
                                    Is the content free from commercial bias?
                                </label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="commercialBias4" id="bias4Yes" value="yes">
                                    <label class="form-check-label" for="bias4Yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="commercialBias4" id="bias4No" value="no">
                                    <label class="form-check-label" for="bias4No">No</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="commercialBias4" id="bias4NA" value="na">
                                    <label class="form-check-label" for="bias4NA">N/A</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h3 class="h6 fw-bold border-bottom pb-2 mb-3">Content Validity</h3>

                            <div class="form-group mb-3">
                                <label class="form-label d-block">
                                    Are recommendations for patient care based on current science, evidence, and clinical reasoning?
                                </label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="contentValidity1" id="validity1Yes" value="yes">
                                    <label class="form-check-label" for="validity1Yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="contentValidity1" id="validity1No" value="no">
                                    <label class="form-check-label" for="validity1No">No</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="contentValidity1" id="validity1NA" value="na">
                                    <label class="form-check-label" for="validity1NA">N/A</label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label d-block">
                                    Does the content have a fair and balanced view of diagnostic and therapeutic options?
                                </label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="contentValidity2" id="validity2Yes" value="yes">
                                    <label class="form-check-label" for="validity2Yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="contentValidity2" id="validity2No" value="no">
                                    <label class="form-check-label" for="validity2No">No</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="contentValidity2" id="validity2NA" value="na">
                                    <label class="form-check-label" for="validity2NA">N/A</label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label d-block">
                                    Does the scientific research conform to the generally accepted standards of experimental design, data collection, analysis, and interpretation?
                                </label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="contentValidity3" id="validity3Yes" value="yes">
                                    <label class="form-check-label" for="validity3Yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="contentValidity3" id="validity3No" value="no">
                                    <label class="form-check-label" for="validity3No">No</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="contentValidity3" id="validity3NA" value="na">
                                    <label class="form-check-label" for="validity3NA">N/A</label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label d-block">
                                    Are new and evolving topics for which there is a lower (or absent) evidence base, clearly identified as such within the education and individual presentations?
                                </label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="contentValidity4" id="validity4Yes" value="yes">
                                    <label class="form-check-label" for="validity4Yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="contentValidity4" id="validity4No" value="no">
                                    <label class="form-check-label" for="validity4No">No</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="contentValidity4" id="validity4NA" value="na">
                                    <label class="form-check-label" for="validity4NA">N/A</label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label d-block">
                                    Does the content avoid advocating for, or promoting, practices that are not, or not yet, adequately based on current science, evidence, and clinical reasoning?
                                </label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="contentValidity5" id="validity5Yes" value="yes">
                                    <label class="form-check-label" for="validity5Yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="contentValidity5" id="validity5No" value="no">
                                    <label class="form-check-label" for="validity5No">No</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="contentValidity5" id="validity5NA" value="na">
                                    <label class="form-check-label" for="validity5NA">N/A</label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label d-block">
                                    Does the content exclude any advocacy for, or promotion of, unscientific approaches to diagnosis or therapy, or recommendations, treatment, or manners of practicing healthcare that are determined to have risks or dangers that outweigh the benefits or are known to be ineffective in the treatment of patients?
                                </label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="contentValidity6" id="validity6Yes" value="yes">
                                    <label class="form-check-label" for="validity6Yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="contentValidity6" id="validity6No" value="no">
                                    <label class="form-check-label" for="validity6No">No</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="contentValidity6" id="validity6NA" value="na">
                                    <label class="form-check-label" for="validity6NA">N/A</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h3 class="h6 fw-bold border-bottom pb-2 mb-3">Relevant Financial Relationship Mitigation</h3>
                            <div class="form-group">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="mitigation" id="mitigation1" value="conflict">
                                    <label class="form-check-label" for="mitigation1">
                                        I, the reviewer, have a financial relationship that is relevant to the content of this abstract and/or a conflict of interest with this content, and it should be reviewed by someone who is not conflicted.
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="mitigation" id="mitigation2" value="noAction">
                                    <label class="form-check-label" for="mitigation2">
                                        No further action is recommended – the financial relationships disclosed are not relevant to the content submitted.
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="mitigation" id="mitigation3" value="revisions">
                                    <label class="form-check-label" for="mitigation3">
                                        Request the speaker to make the following revisions to the content.
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mitigation" id="mitigation4" value="review">
                                    <label class="form-check-label" for="mitigation4">
                                        Request the speaker to submit presentation materials for further review.
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="form-group">
                                <label for="comments" class="form-label fw-bold">Comments*:</label>
                                <textarea class="form-control" id="comments" rows="4" placeholder="Enter any additional comments..."></textarea>
                            </div>
                        </div>

                        </form>
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
    let baseUrlReviewer = "<?= base_url() . '/cme/' ?>";
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
        let formData = new FormData(document.getElementById('formCMRating'));
        formData.append('abstract_id', abstract_id)
        formData.append('reviewer_id', reviewer_id)
        // console.log(formData)

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

        console.log(formData)


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