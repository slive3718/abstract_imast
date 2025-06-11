<link href="<?= base_url() ?>/assets/css/event/landing.css" rel="stylesheet">

<?php echo view('deputy/common/menu'); ?>
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
<?php //echo '<pre>';print_r($abstract_review_data['methodology_score']);exit;
?>
<main>
    <div class="container-fluid">
        <h4> Review Abstract </h4>
        <hr/>
        <div class="card">
            <div class="card-header">
                <h5> General Information </h5>
            </div>
            <div class="card-body">
                <table>
                    <div class="row">
                        <td style=" width:30%; vertical-align:top; text-align: right; padding-right: 30px;">Abstract ID : </td>
                        <td>
                            <?= $abstracts->id ?>
                        </td>
                        </tr>
                        <div class="row">
                            <td style=" width:30%; vertical-align:top; text-align: right; padding-right: 30px;">Abstract Title : </td>
                            <td>
                                <?= $abstracts->title ?>
                            </td>
                            </tr>
                </table>

            </div>
        </div>
        <div class="card mt-5">
            <div class="card-header">
                <h5> Abstract Information </h5>
            </div>
            <div class="card-body">
                <table id="abstractInformationTable">
                    <div class="row">

                        </tr>
                        <div class="row">
                            <td style=" width:30%; vertical-align:top; text-align: right; padding-right: 30px;">Abstract Title:</td>
                            <td>
                                <?= $abstracts->title ?>
                            </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:top; text-align: right; padding-right: 30px">Primary Topic :</td>
                                <td>
                                    <?= isset($abstracts->primary_topic) ? $abstracts->primary_topic[0]['name'] : '' ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:top; text-align: right; padding-right: 30px">Secondary Topic :</td>
                                <td>
                                    <?= isset($abstracts->primary_topic) ? $abstracts->secondary_topic[0]['name'] : '' ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:top; text-align: right; padding-right: 30px">Background :</td>
                                <td>
                                    <?= isset($abstracts->primary_topic) ? $abstracts->background : '' ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:top; text-align: right; padding-right: 30px">Hypothesis :</td>
                                <td>
                                    <?= isset($abstracts->primary_topic) ? $abstracts->hypothesis : '' ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:top; text-align: right; padding-right: 30px">Method :</td>
                                <td>
                                    <?= isset($abstracts->primary_topic) ? $abstracts->methods : '' ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:top; text-align: right; padding-right: 30px">Results :</td>
                                <td>
                                    <?= isset($abstracts->primary_topic) ? $abstracts->results : '' ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:top; text-align: right; padding-right: 30px">Conclusion :</td>
                                <td>
                                    <?= isset($abstracts->primary_topic) ? $abstracts->conclusion : '' ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:top; text-align: right; padding-right: 30px">Image Uploads :</td>
                                <td>

                                    <?php if(isset($abstracts->image_uploads) && !empty($abstracts->image_uploads)) : ?>
                                        <p > Click image to zoom.</p> <br>
                                        <?php foreach($abstracts->image_uploads as $image):
                                            ?>
                                            <a href="<?=base_url().$image['file_path'].$image['file_name'] ?>" data-lightbox="image" class="" imageSrc="<?=base_url().$image['file_path'].$image['file_name'] ?>" ><img src="<?=base_url().$image['file_path'].$image['file_name'] ?>" style="width:200px; height:200px"></a>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p> None </p> <br>
                                    <?php endif ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:top; text-align: right; padding-right: 30px">Reference :</td>
                                <td>
                                    <?= isset($abstracts->primary_topic) ? $abstracts->reference : '' ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:top; text-align: right; padding-right: 30px;">Does this abstract meet the following diversity award requirements?  Identify, analyze or seek solution for healthcare disparities, focus on underserved, underrepresented or understudied populations; or promote the values of diversity, inclusion and equity as prioritize by the PRiSM Board of Directors and Diversity Committee: </td>
                                <td style="vertical-al">
                                    <?= isset($abstracts->primary_topic) ? $abstracts->diversity : '' ?>
                                </td>
                            </tr>

                </table>
            </div>
        </div>
        <div class="card  mt-5 table-responsive">
            <div class="card-header">
                <h5> General Information </h4>
            </div>
            <div class="card-body">
                <form id="formReviewData" name="formReviewData" onsubmit="return false;">

                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <required class="text-danger">*</required> 1. Methodology/Hypothesis: Is the research method well planned and thoroughly thought out?
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div>
                                <select name="methodology_score" class="parcialField form-control inputScore required" id="pw_parcial1">
                                    <option value=""> -- Select -- </option>
                                    <option value="5" <?=(isset($abstract_review_data['methodology_score']) && ($abstract_review_data['methodology_score']) == "5") ? "selected":""?> >5 = Excellent</option>
                                    <option value="4" <?=(isset($abstract_review_data['methodology_score']) && ($abstract_review_data['methodology_score']) == "4") ? "selected":""?> >4 = Good</option>
                                    <option value="3" <?=(isset($abstract_review_data['methodology_score']) && ($abstract_review_data['methodology_score']) == "3") ? "selected":""?> >3 = Average</option>
                                    <option value="2" <?=(isset($abstract_review_data['methodology_score']) && ($abstract_review_data['methodology_score']) == "2") ? "selected":""?> >2 = Below Average</option>
                                    <option value="1" <?=(isset($abstract_review_data['methodology_score']) && ($abstract_review_data['methodology_score']) == "1") ? "selected":""?> >1 = Inferior, but accepted</option>
                                    <option value="0" <?=(isset($abstract_review_data['methodology_score']) && ($abstract_review_data['methodology_score']) == "0") ? "selected":""?> >0 = Not accepted</option>
                                    <option value="COI" <?=(isset($abstract_review_data['methodology_score']) && ($abstract_review_data['methodology_score']) == "COI") ? "selected":""?> >COI (Conflict of interest)</option>
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="row mt-2">
                        <div class="col-md-6 col-sm-12">
                            <required class="text-danger">*</required> 2. Data Analysis: Is there sufficient data and does the author make evidence-based conclusions?
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div>
                                <select name="data_analysis_score" class="parcialField form-control inputScore required" id="pw_parcial1">
                                    <option value=""> -- Select -- </option>
                                    <option value="5" <?=(isset($abstract_review_data['data_analysis_score']) && ($abstract_review_data['data_analysis_score']) == "5") ? "selected":""?> >5 = Excellent</option>
                                    <option value="4" <?=(isset($abstract_review_data['data_analysis_score']) && ($abstract_review_data['data_analysis_score']) == "4") ? "selected":""?> >4 = Good</option>
                                    <option value="3" <?=(isset($abstract_review_data['data_analysis_score']) && ($abstract_review_data['data_analysis_score']) == "3") ? "selected":""?> >3 = Average</option>
                                    <option value="2" <?=(isset($abstract_review_data['data_analysis_score']) && ($abstract_review_data['data_analysis_score']) == "2") ? "selected":""?> >2 = Below Average</option>
                                    <option value="1" <?=(isset($abstract_review_data['data_analysis_score']) && ($abstract_review_data['data_analysis_score']) == "1") ? "selected":""?> >1 = Inferior, but accepted</option>
                                    <option value="0" <?=(isset($abstract_review_data['data_analysis_score']) && ($abstract_review_data['data_analysis_score']) == "0") ? "selected":""?> >0 = Not accepted</option>
                                    <option value="COI" <?=(isset($abstract_review_data['data_analysis_score']) && ($abstract_review_data['data_analysis_score']) == "COI") ? "selected":""?> >COI (Conflict of interest)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6 col-sm-12">
                            <required class="text-danger">*</required> 3. Discovery/Interpretation: How well does the author utilize the data and use it to support the hypothesis? Were the methods appropriate for the research?
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div>
                                <select name="interpretation_score" class="parcialField form-control inputScore required" id="pw_parcial1">
                                    <option value=""> -- Select -- </option>
                                    <option value="5" <?=(isset($abstract_review_data['interpretation_score']) && ($abstract_review_data['interpretation_score']) == "5") ? "selected":""?> >5 = Excellent</option>
                                    <option value="4" <?=(isset($abstract_review_data['interpretation_score']) && ($abstract_review_data['interpretation_score']) == "4") ? "selected":""?> >4 = Good</option>
                                    <option value="3" <?=(isset($abstract_review_data['interpretation_score']) && ($abstract_review_data['interpretation_score']) == "3") ? "selected":""?> >3 = Average</option>
                                    <option value="2" <?=(isset($abstract_review_data['interpretation_score']) && ($abstract_review_data['interpretation_score']) == "2") ? "selected":""?> >2 = Below Average</option>
                                    <option value="1" <?=(isset($abstract_review_data['interpretation_score']) && ($abstract_review_data['interpretation_score']) == "1") ? "selected":""?> >1 = Inferior, but accepted</option>
                                    <option value="0" <?=(isset($abstract_review_data['interpretation_score']) && ($abstract_review_data['interpretation_score']) == "0") ? "selected":""?> >0 = Not accepted</option>
                                    <option value="COI" <?=(isset($abstract_review_data['interpretation_score']) && ($abstract_review_data['interpretation_score']) == "COI") ? "selected":""?> >COI (Conflict of interest)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6 col-sm-12">
                            <required class="text-danger">*</required> 4. Clarity of Writing/Presentation: Is the information clear to the reader and presented in a concise manner? Do the facts and figures appropriately highlight the data?
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div>
                                <select name="clarity_score" class="parcialField form-control inputScore required" id="pw_parcial1">
                                    <option value=""> -- Select -- </option>
                                    <option value="5" <?=(isset($abstract_review_data['clarity_score']) && ($abstract_review_data['clarity_score']) == "5") ? "selected":""?> >5 = Excellent</option>
                                    <option value="4" <?=(isset($abstract_review_data['clarity_score']) && ($abstract_review_data['clarity_score']) == "4") ? "selected":""?> >4 = Good</option>
                                    <option value="3" <?=(isset($abstract_review_data['clarity_score']) && ($abstract_review_data['clarity_score']) == "3") ? "selected":""?> >3 = Average</option>
                                    <option value="2" <?=(isset($abstract_review_data['clarity_score']) && ($abstract_review_data['clarity_score']) == "2") ? "selected":""?> >2 = Below Average</option>
                                    <option value="1" <?=(isset($abstract_review_data['clarity_score']) && ($abstract_review_data['clarity_score']) == "1") ? "selected":""?> >1 = Inferior, but accepted</option>
                                    <option value="0" <?=(isset($abstract_review_data['clarity_score']) && ($abstract_review_data['clarity_score']) == "0") ? "selected":""?> >0 = Not accepted</option>
                                    <option value="COI" <?=(isset($abstract_review_data['clarity_score']) && ($abstract_review_data['clarity_score']) == "COI") ? "selected":""?> >COI (Conflict of interest)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6 col-sm-12">
                            <required class="text-danger">*</required> 5. Relevance/Significance: Will this have an impact on the PRiSM membership and other sports professionals? Does it align with the mission of PRiSM?
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div>
                                <select name="significance_score" class="parcialField form-control inputScore required" id="pw_parcial1">
                                    <option value=""> -- Select -- </option>
                                    <option value="5" <?=(isset($abstract_review_data['significance_score']) && ($abstract_review_data['significance_score']) == "5") ? "selected":""?> >5 = Excellent</option>
                                    <option value="4" <?=(isset($abstract_review_data['significance_score']) && ($abstract_review_data['significance_score']) == "4") ? "selected":""?> >4 = Good</option>
                                    <option value="3" <?=(isset($abstract_review_data['significance_score']) && ($abstract_review_data['significance_score']) == "3") ? "selected":""?> >3 = Average</option>
                                    <option value="2" <?=(isset($abstract_review_data['significance_score']) && ($abstract_review_data['significance_score']) == "2") ? "selected":""?> >2 = Below Average</option>
                                    <option value="1" <?=(isset($abstract_review_data['significance_score']) && ($abstract_review_data['significance_score']) == "1") ? "selected":""?> >1 = Inferior, but accepted</option>
                                    <option value="0" <?=(isset($abstract_review_data['significance_score']) && ($abstract_review_data['significance_score']) == "0") ? "selected":""?> >0 = Not accepted</option>
                                    <option value="COI" <?=(isset($abstract_review_data['significance_score']) && ($abstract_review_data['significance_score']) == "COI") ? "selected":""?> >COI (Conflict of interest)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6 col-sm-12">
                            <required class="text-danger">*</required> 6. Originality: How innovative is the research? Have there already been similar studies?
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div>
                                <select name="originality_score" class="parcialField form-control inputScore required" id="pw_parcial1">
                                    <option value=""> -- Select -- </option>
                                    <option value="5" <?=(isset($abstract_review_data['originality_score']) && ($abstract_review_data['originality_score']) == "5") ? "selected":""?> >5 = Excellent</option>
                                    <option value="4" <?=(isset($abstract_review_data['originality_score']) && ($abstract_review_data['originality_score']) == "4") ? "selected":""?> >4 = Good</option>
                                    <option value="3" <?=(isset($abstract_review_data['originality_score']) && ($abstract_review_data['originality_score']) == "3") ? "selected":""?> >3 = Average</option>
                                    <option value="2" <?=(isset($abstract_review_data['originality_score']) && ($abstract_review_data['originality_score']) == "2") ? "selected":""?> >2 = Below Average</option>
                                    <option value="1" <?=(isset($abstract_review_data['originality_score']) && ($abstract_review_data['originality_score']) == "1") ? "selected":""?> >1 = Inferior, but accepted</option>
                                    <option value="0" <?=(isset($abstract_review_data['originality_score']) && ($abstract_review_data['originality_score']) == "0") ? "selected":""?> >0 = Not accepted</option>
                                    <option value="COI" <?=(isset($abstract_review_data['originality_score']) && ($abstract_review_data['originality_score']) == "COI") ? "selected":""?> >COI (Conflict of interest)</option>
                                </select>
                            </div>
                        </div>
                    </div>





                    <div class="row mt-2">
                        <div class="col-md-6 col-sm-12"></div>
                        <div class="col-md-6 col-sm-12">
                            <div class="input-group mt-3 " style="width:100">
                                <label class="me-2"> Total Score: </label><input type="text" class="form-control inputScore" name="total_score" readonly>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6 col-sm-12"><required class="text-danger">*</required> 7. Topic 1: In your opinion, the topic of this abstract is:</div>
                            <div class="col-md-6 col-sm-12">
                                <div>
                                    <select class="form-control requiredSelect" name="opinion_topic_selected">
                                        <option value=""> -- Select One --</option>
                                        <?php
                                        if(!empty($abstract_topics)):
                                            foreach($abstract_topics as $abstract_topic):
                                                ?>
                                                <option value="<?=$abstract_topic['value']?>" <?=isset($abstract_review_data['opinion_topic_selected']) && $abstract_review_data['opinion_topic_selected']  == $abstract_topic['value']? 'selected':''?>><?=$abstract_topic['name']?></option>
                                            <?php
                                            endforeach;
                                        endif
                                        ?>

                                    </select>
                                    <div class="otherTopicSection mt-2"  style="display:hidden">
                                    <span  class="text-info" id="msg_other">
                                        <em>( Please indicate your "other" topic in the Comments for the Committee box
                                            below. )</em>
                                    </span>
                                        <input name="other_topic" type="text" class="form-control" value="<?=isset($abstract_review_data['other_topic'])?$abstract_review_data['other_topic']:''?>" placeholder="Other topic comment">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6 col-sm-12"> 8. Topic 2: In your opinion, a secondary topic of this abstract is:</div>
                            <div class="col-md-6 col-sm-12">
                                <div>
                                    <select class="form-control" name="opinion_topic_selected2">
                                        <option value=""> -- Select One --</option>
                                        <?php
                                        if(!empty($abstract_topics)):
                                            foreach($abstract_topics as $abstract_topic):
                                                ?>
                                                <option value="<?=$abstract_topic['value']?>" <?=isset($abstract_review_data['opinion_topic_selected2']) && $abstract_review_data['opinion_topic_selected2']  == $abstract_topic['value']? 'selected':''?>><?=$abstract_topic['name']?></option>
                                            <?php
                                            endforeach;
                                        endif
                                        ?>

                                    </select>
                                    <div class="otherTopicSection2 mt-2"  style="display:hidden">
                                    <span  class="text-info" id="msg_other">
                                        <em>( Please indicate your "other" topic in the Comments for the Committee box
                                            below. )</em>
                                    </span>
                                        <input name="other_topic2" type="text" class="form-control" value="<?=isset($abstract_review_data['other_topic2'])?$abstract_review_data['other_topic2']:''?>" placeholder="Other topic comment">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6 col-sm-12"><required class="text-danger">*</required> 9. Case : Is this a case report ?</div>
                            <div class="col-md-6 col-sm-12">
                                <div>
                                    <input type="radio" name="caseReportRadio" class="form-radio requiredOption" value="1" id="reportYes" <?=(isset($abstract_review_data['is_case_report']) && $abstract_review_data['is_case_report'] == "1")? "checked":''?>> <label for="reportYes" class="form-control-label">Yes</label> <br>
                                    <input type="radio" name="caseReportRadio" class="form-radio requiredOption" value="0" id="reportNo" <?=(isset($abstract_review_data['is_case_report']) && $abstract_review_data['is_case_report'] == "0")? "checked":''?>> <label for="reportNo" class="form-control-label">No</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6 col-sm-12"><required class="text-danger">*</required> 10. COI : Do you have a conflict of interest ?</div>
                            <div class="col-md-6 col-sm-12">
                                <div>
                                    <input type="radio" name="coiRadio" class="form-radio requiredOption" value="1" id="coiYes" <?=(isset($abstract_review_data['with_conflict_of_interest']) && $abstract_review_data['with_conflict_of_interest'] == "1")? "checked":''?>> <label for="coiYes" class="form-control-label">Yes</label><br>
                                    <input type="radio" name="coiRadio" class="form-radio requiredOption" value="0" id="coiNo" <?=(isset($abstract_review_data['with_conflict_of_interest']) && $abstract_review_data['with_conflict_of_interest'] == "0")? "checked":''?>> <label for="coiNo" class="form-control-label">No</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6 col-sm-12">Comments for the author</div>
                            <div class="col-md-6 col-sm-12">
                                <textarea name="commentsForAuthor" rows="4" type="text" class="form-control"  placeholder="Type here..."><?=isset($abstract_review_data['comments_for_author'])?$abstract_review_data['comments_for_author']:''?></textarea>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6 col-sm-12">Comments for the committee</div>
                            <div class="col-md-6 col-sm-12">
                                <textarea name="commentsForCommittee" rows="4" type="text" class="form-control" placeholder="Type here..."><?=isset($abstract_review_data['comments_for_committee'])?$abstract_review_data['comments_for_committee']:''?></textarea>
                            </div>
                        </div>

                        <hr class="mt-2"/>

                        <div class="row mt-3 text-start">
                            <h6 class="fw-bolder">Relevance</h6>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6 col-sm-12"><required class="text-danger">*</required> Abstracts must be relevant to pediatric sports medicine. To be considered for presentation, it
                                must include a pediatric/adolescent population (explicitly or primarily) and one of the
                                following should be met:
                                <ul>
                                    <li>The population includes athletes</li>
                                    <li>Outcome measures includes RTS</li>
                                    <li>Is a sports-related injury/surgery/condition</li>
                                </ul>
                            </div>
                            <div class="col-md-6 col-sm-12">

                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6 col-sm-12"><required class="text-danger">*</required> Does the abstract meet the requirements?
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div>
                                    <input type="radio" name="requirementMeetRadio" class="form-radio requiredOption" value="1" id="meetYes" <?=(isset($abstract_review_data['is_requirements_meet']) && $abstract_review_data['is_requirements_meet'] == "1")? "checked":''?>> <label for="meetYes" class="form-control-label">Yes</label><br>
                                    <input type="radio" name="requirementMeetRadio" class="form-radio requiredOption" value="0" id="meetNo" <?=(isset($abstract_review_data['is_requirements_meet']) && $abstract_review_data['is_requirements_meet'] == "0")? "checked":''?>> <label for="meetNo" class="form-control-label">No</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3 text-start">
                            <h6 class="fw-bolder">PRISM Diversity Committee Inclusion and Equity Abstract Award</h6>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6 col-sm-12">Does this abstract meet the following diversity award requirements?
                                Identify, analyze or seek solution for healthcare disparities, focus on underserved,
                                underrepresented or understudied populations; or promote the values of diversity,
                                inclusion and equity as prioritize by the PRiSM Board of Directors and Diversity Committee
                            </div>
                            <div class="col-md-6 col-sm-12">
                            </div>
                        </div>


                        <div class="row mt-3">
                            <div class="col-md-6 col-sm-12">
                                <required class="text-danger">*</required> In your opinion, would this abstract qualify for the diversity award ?
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div>
                                    <input type="radio" name="qualifiedDiversityRadio" class="form-radio requiredOption" value="1" id="qualifiedYes" <?=(isset($abstract_review_data['is_abstract_qualified']) && $abstract_review_data['is_abstract_qualified'] == "1")? "checked":''?>> <label for="qualifiedYes" class="form-control-label">Yes</label><br>
                                    <input type="radio" name="qualifiedDiversityRadio" class="form-radio requiredOption" value="0" id="qualifiedNo" <?=(isset($abstract_review_data['is_abstract_qualified']) && $abstract_review_data['is_abstract_qualified'] == "0")? "checked":''?>> <label for="qualifiedNo" class="form-control-label">No</label>
                                </div>
                            </div>
                        </div>


                </form>  <!-- End of the form -->
            </div>
        </div>
    </div>
    <div class="mt-4">
        <input type="button" value="Submit Review" style="width:150px" class="btn btn-success mt-" onclick="submitFormReview();">
    </div>
</main>

<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
<!-- Include the Lightbox2 JavaScript file -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>


<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css"/>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
<script>
    let baseUrlReviewer = "<?= base_url() . '/' . $event->uri . '/deputy/' ?>";
    let reviewer_id = "<?= $reviewer_id ?>"
    $(function () {
        // checkShowHide();
        $("select[name='opinion_topic_selected']").on('click change input', function(){
            if($("select[name='opinion_topic_selected'] :selected").val() == '26'){
                $(".otherTopicSection").show()
            }else{
                $(".otherTopicSection").hide()
            }
        })
        $("select[name='opinion_topic_selected2']").on('click change input', function(){
            if($("select[name='opinion_topic_selected2'] :selected").val() == '26'){
                $(".otherTopicSection2").show()
            }else{
                $(".otherTopicSection2").hide()
            }
        })


        $('.parcialField').on('click change input', function () {
            var sum = 0;
            $('.parcialField').each(function () {
                var value = parseFloat($(this).val());
                if (!isNaN(value)) {
                    sum += value;
                }
            });
            $('input[name="total_score"]').val(sum)
        });
    })

    window.onload = function(){
        $('.parcialField').each(function () {
            var sum = 0;
            $('.parcialField').each(function () {
                var value = parseFloat($(this).val());
                if (!isNaN(value)) {
                    sum += value;
                }
            });
            $('input[name="total_score"]').val(sum)
        })

        if($("select[name='opinion_topic_selected'] :selected").val() == '26'){
            $(".otherTopicSection").show()
        }else{
            $(".otherTopicSection").hide()
        }

        if($("select[name='opinion_topic_selected2'] :selected").val() == '26'){
            $(".otherTopicSection2").show()
        }else{
            $(".otherTopicSection2").hide()
        }



    }

    function submitFormReview() {
        let vote_error = 0;
        let totalScore = 0;
        let missing_fields = 0;
        let formData = new FormData(document.getElementById('formReviewData'));
        formData.append('abstract_id', abstract_id)
        formData.append('reviewer_id', reviewer_id)
        // console.log(formData)

        $('.parcialField').each(function () {
            if (parseFloat($(this).val()) > 5 || parseFloat($(this).val()) < 0 || $(this).val() == '') {
                vote_error = 1;
            }
        })

        $('.parcialField').each(function () {
            var value = parseFloat($(this).val());
            if (!isNaN(value)) {
                totalScore += value;
            }
        });

        if(($("select[name='opinion_topic_selected'] :selected").val() == '26') && $("input[name='other_topic']").val() == ""){
            toastr.error('comment is required if other topic is selected')
            return false
        }

        $(".requiredOption").each(function() {
            var groupName = $(this).attr("name");
            var checked = $("input[name='" + groupName + "']").is(":checked");

            if (!checked) {
                missing_fields = 1;
            }
        });

        // return false;


        if (vote_error > 0) {
            toastr.warning('Rating/score can only be 1 to 5')
            return false;
        }

        if (missing_fields > 0) {
            toastr.error('Please fill all required fields');
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
                        title: 'Saved',
                        text: "Continue to next abstract",
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, continue!'
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
                }
            }

        }, 'json')

    }
</script>